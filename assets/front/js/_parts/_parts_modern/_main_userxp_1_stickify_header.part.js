var czrapp = czrapp || {};

(function($, czrapp) {
  var _methods =  {

        /*-----------------------------------------------------
        * MAIN
        ------------------------------------------------------*/
        stickifyHeader : function() {
              //Do nothing if there's no header => plugins compatibility
              if ( czrapp.$_header.length < 1 )
                return;

              var self = this;
              this.stickyCandidatesMap = {
                    mobile : {
                          mediaRule : 'only screen and (max-width: 991px)',
                          selector : 'mobile-sticky'
                    },
                    desktop : {
                          mediaRule : 'only screen and (min-width: 992px)',
                          selector : 'desktop-sticky'
                    }
              };
              //this.stickyHeaderClass      = 'primary-navbar__wrapper';
              this.navbarsWrapperSelector   = '.tc-header';
              this.$_navbars_wrapper        = $( this.navbarsWrapperSelector );
              this.$_topbar                 = 1 == this.$_navbars_wrapper.length ? this.$_navbars_wrapper.find( '.topbar-navbar__wrapper') : false;
              this.$_primary_navbar         = 1 == this.$_navbars_wrapper.length ? this.$_navbars_wrapper.find( '.primary-navbar__wrapper') : false;

              this.mobileMenuOpenedEvent    = 'show.czr.czrCollapse'; //('show' : start of the uncollapsing animation; 'shown' : end of the uncollapsing animation)
              this.mobileMenuStickySelector = '.mobile-sticky .mobile-nav__nav';

              this.stickyMenuWrapper        = false;
              this.stickyMenuDown           = new czrapp.Value( '_not_set_' );
              this.stickyHeaderThreshold    = 50;
              this.currentStickySelector    = new czrapp.Value( '' );//<= will be set on init and on resize
              this.hasStickyCandidate       = new czrapp.Value( false );
              this.stickyHeaderAnimating    = new czrapp.Value( false );
              this.animationPromise         = $.Deferred( function() { return this.resolve(); });
              this.userStickyOpt            = new czrapp.Value( self._setUserStickyOpt() );//set on init and on resize : stick_always, no_stick, stick_up
              this.isFixedPositionned       = new czrapp.Value( false );//is the candidate fixed ? => toggle the 'fixed-header-on' css class to the header
              //this.isSticky               = new czrapp.Value( false );
              this.stickyStage              = new czrapp.Value( '_not_set_' );
              this.shrinkBrand              = new czrapp.Value( false );//Toggle a class to maybe shrink the title or logo if the option is on


              //// SETUP LISTENERS ////
              //react to current sticky selector
              //will be set on init and reset on resize
              this.currentStickySelector.bind( function( to ) {
                    var _reset = function() {
                          czrapp.$_header.css( { 'height' : '' });
                          self.isFixedPositionned( false );//removes css class 'fixed-header-on' from the czrapp.$_header element
                          self.stickyMenuDown( false );
                          self.stickyMenuWrapper = false;
                          self.hasStickyCandidate( false );
                    };

                    //we have a candidate
                    if ( ! _.isEmpty( to ) ) {
                          self.hasStickyCandidate( 1 == czrapp.$_header.find( to ).length );
                          //Does this selector actually exists ?
                          if ( ! self.hasStickyCandidate() ) {
                                _reset();
                          }
                          else {
                                //cache the menu wrapper now
                                self.stickyMenuWrapper = czrapp.$_header.find( to );
                                //make sure we have the transition class in any cases
                                // + Always set the header height on dom ready
                                //=> will prevent any wrong value being assigned if menu is expanded before scrolling
                                //If the header has an image, defer setting the height when the .site-image is loaded
                                //=> otherwise the header height might be wrong because based on an empty img
                                var $_header_logo = self.stickyMenuWrapper.find('.navbar-brand-sitelogo img');
                                if ( 1 == $_header_logo.length ) {

                                      //We introduce a custom event here
                                      //=> because we need to handle the case when the image is already loaded. This way we don't need to fire the load() function twice.
                                      //=> This fixes : https://wordpress.org/support/topic/navbar-not-full-width-until-scroll/
                                      // reported here : https://github.com/presscustomizr/hueman/issues/508
                                      $_header_logo.bind( 'header-logo-loaded', function() {
                                            czrapp.$_header.css( { 'height' : czrapp.$_header[0].getBoundingClientRect().height });
                                      });

                                      //If the image status is "complete", then trigger the custom event right away, else bind the "load" event
                                      //http://stackoverflow.com/questions/1948672/how-to-tell-if-an-image-is-loaded-or-cached-in-jquery
                                      if ( $_header_logo[0].complete ) {
                                            $_header_logo.trigger('header-logo-loaded');
                                      } else {
                                        $_header_logo.on('load', function() {
                                              $_header_logo.trigger('header-logo-loaded');
                                        } );
                                      }
                                } else {
                                    // + Always set the header height on dom ready
                                    //=> will prevent any wrong value being assigned if menu is expanded before scrolling

                                    //To avoid decimal roundings use element pure js element.getBoundingClientRect().height
                                    //instead of $.height()
                                    //https://developer.mozilla.org/it/docs/Web/API/Element/getBoundingClientRect
                                    czrapp.$_header.css( { 'height' : czrapp.$_header[0].getBoundingClientRect().height });
                                    //Note: actually we should not really set the header height on init, since we fix the sticky candidate
                                    //on fly...
                                }
                          }
                    } else {//we don't have a candidate
                          _reset();
                    }
              });

              //STICKY STATE LISTENER
              // this.isSticky.bind( function( _isSticky ) {
              //       czrapp.$_header.toggleClass( 'is-sticky', _isSticky );
              // });


              //FIXED POSITION LISTENER
              //adding the 'fixed-header-on' class makes the sticky candidate position:fixed
              this.isFixedPositionned.bind( function( isFixed ) {
                    czrapp.$_header.toggleClass( 'fixed-header-on', isFixed ).toggleClass( 'is-sticky', isFixed );
                    self._pushPrimaryNavBarDown( isFixed );
                    //set the skink state
                    self.shrinkBrand( isFixed );
              });

              //SHRINK BRAND LISTENER
              this.shrinkBrand.bind( function( isShrinked ) {
                    //Toggle a class to maybe shrink the title or logo if the option is on
                    czrapp.$_header.toggleClass( 'can-shrink-brand', isShrinked );
                    //let's emulate a resize when unshrinking
                    //=> this fixes a potential issue when
                    //1) user has scrolled
                    //2) changed a tabled layout from portrait to landscape
                    //3) scrolled up to top
                    //=> might lead to a wrong header's height
                    //=> emulating a 'resize' will fire the isResizing callbacks and reset the header's height
                    if ( ! isShrinked ) {
                          _.delay( function() {
                                if ( self.scrollPosition() < self.stickyHeaderThreshold ) {
                                      czrapp.$_header.trigger( 'czr-resize');
                                }
                          }, 400 );//<=400ms gives us enough room to finish the title or logo unshrinking animation
                    }
              });


              //SCROLL POSITION LISTENER
              //Animate based on scroll position.
              //Must have a sticky candidate
              var _setStickynessStatesOnScroll = function( to, from ) {
                    if ( ! self.hasStickyCandidate() )
                      return;

                    to = to || self.scrollPosition();
                    from = from || self.scrollPosition();

                    var reachedTheTop = ( to == from ) && 0 === to;
                    //Set up only when
                    //1) We've reached the top <=> to = from = 0 ( => always fired when scroll stopped after 500 ms )
                    //2) we're not on top and scroll up is significant => avoid revealing the menu for minor scroll up actions on mobile devices
                    if ( ! reachedTheTop ) {
                          if ( Math.abs( to - from ) <= 5 ) {
                            return;
                          }
                    }
                    var $menu_wrapper = czrapp.$_header.find( self.currentStickySelector() ),
                        _h = $menu_wrapper[0].getBoundingClientRect().height;

                    if ( 'down' == self.scrollDirection() && to <= ( self.topStickPoint() + _h ) ) {
                          self.stickyStage( 'down_top' );
                          self.isFixedPositionned( false );
                          //animate up but don't set to position fixed too early
                          self.stickyMenuDown( true );

                    } else if ( 'down' == self.scrollDirection() && to > ( self.topStickPoint() + _h ) && to < ( self.topStickPoint() + ( _h * 2 ) ) ) {
                          self.stickyStage( 'down_middle' );
                          self.isFixedPositionned( false );
                          //animate up but don't set to position fixed too early
                          self.stickyMenuDown( false );

                    } else if ( 'down' == self.scrollDirection() && to >= ( self.topStickPoint() + ( _h * 2 ) ) ) {
                          if ( 'stick_always' == self.userStickyOpt()  ) {
                                var _dodo = function() {
                                      self.stickyMenuDown( false, { fast : true,  } ).done( function() {
                                            self.stickyMenuDown( true, { forceFixed : true } ).done( function() {});
                                            self.stickyStage( 'down_after' );
                                      });
                                };
                                // if ( 'up' == self.stickyStage() ) {
                                //       _dodo();
                                // }
                                //When stick_always, we need to make sure that the sticky candidate is going up and the down.
                                //No need to do this if it's already down
                                if ( ! self.stickyHeaderAnimating() && ( ( 'down_after' != self.stickyStage() && 'up' != self.stickyStage() ) || true !== self.stickyMenuDown() ) ) {
                                     _dodo();
                                }
                          } else {
                                self.stickyMenuDown( false );
                                self.stickyStage( 'down_after' );
                          }

                    } else if ( 'up' == self.scrollDirection() ) {
                          self.stickyStage( 'up' );
                          self.stickyMenuDown( true ).done( function() {});
                          //self.isFixedPositionned( to > self.topStickPoint() );

                          //on scroll up maybe update isFixedPositionned only if already sticky
                          if ( self.isFixedPositionned() ) {
                                self.isFixedPositionned( to > self.topStickPoint() );
                          }
                    }
              };


              //SCROLL POSITION LISTENER
              this.scrollPosition.bind( function( to, from ) {
                    _setStickynessStatesOnScroll( to, from );
                    self.shrinkBrand( self.isFixedPositionned() );
              } );


              //czrapp.bind( 'page-scrolled-top', _mayBeresetTopPosition );
              var _maybeResetTop = function() {
                    if ( 'up' == self.scrollDirection() ) {
                          self._mayBeresetTopPosition();
                    }
              };
              //react on scrolling finished <=> after the timer
              czrapp.bind( 'scrolling-finished', _maybeResetTop );
              //always make sure that we didn't miss a sticky stage while scrolling fast
              czrapp.bind( 'scrolling-finished', function() {
                    _.delay( function() {
                          _setStickynessStatesOnScroll();
                    }, 400);
              });
              //react on topbar collapsed, @see topNavToLife
              czrapp.bind( 'topbar-collapsed', _maybeResetTop );


              //STICKY MENU DOWN LISTENER
              //animate : make sure we don't hide the menu when too close from top
              //only animate when user option is set to stick_up
              self.stickyMenuDown.validate = function( newValue ) {
                    if ( ! self.hasStickyCandidate() )
                      return false;
                    // fixes Mobile menu : Don’t collapse long menus when scrolling down https://github.com/presscustomizr/customizr/issues/1226
                    if ( self._isMobileMenuExpanded() )
                      return this._value;

                    if ( self.scrollPosition() < self.stickyHeaderThreshold && ! newValue ) {
                          if ( ! self.isScrolling() ) {
                                //print a message when attempt to programmatically hide the menu
                                czrapp.errorLog('Menu too close from top to be moved up');
                          }
                          return self.stickyMenuDown();
                    } else {
                          return newValue;
                    }
              };

              //This czrapp.Value() is bound with a callback returning a promise ( => in this case an animation lasting a few ms )
              //This allows to write this type of code :
              //self.stickyMenuDown( false ).done( function() {
              //    $('#header').css( 'padding-top', '' );
              //});
              self.stickyMenuDown.bind( function( to, from, args ){
                    if ( ! _.isBoolean( to ) || ! self.hasStickyCandidate() ) {
                          return $.Deferred( function() { return this.resolve().promise(); } );
                    }
                    args = _.extend(
                          {
                                direction : to ? 'down' : 'up',
                                forceFixed : false,
                                menu_wrapper : self.stickyMenuWrapper,
                                fast : false
                          },
                          args || {}
                    );

                    return self._animate(
                          {
                                direction : args.direction,
                                forceFixed : args.forceFixed,
                                menu_wrapper : args.menu_wrapper,
                                fast : args.fast
                          }
                    );
              }, { deferred : true } );


              /*-----------------------------------------------------
              * (real) RESIZE ( refreshed every 50 ms ) OR REFRESH EVENT
              ------------------------------------------------------*/
              self.isResizing.bind( function() {self._refreshOrResizeReact(); } );//resize();
              czrapp.$_header.on( 'refresh-sticky-header', function() { self._refreshOrResizeReact(); } );


              /*-----------------------------------------------------
              * Adjust mobile menu max-height on menu shown
              ------------------------------------------------------*/
              czrapp.$_body.on( self.mobileMenuOpenedEvent, self.mobileMenuStickySelector, function() {
                    var $_mobileMenu         = $(this),
                        $_mobileMenuNavInner = $_mobileMenu.find( '.mobile-nav__inner' );

                    if ( $_mobileMenu.length > 0 ) {
                          //fallback on jQuery height() if window.innerHeight isn't defined (e.g. ie<9)
                          var _winHeight = 'undefined' !== typeof window.innerHeight ? window.innerHeight : czrapp.$_window.height(),
                              _maxHeight = _winHeight - $_mobileMenu.closest( '.mobile-nav__container' ).offset().top + czrapp.$_window.scrollTop();

                          $_mobileMenuNavInner.css( 'max-height', _maxHeight + 'px'  );
                    }
              });


              /*-----------------------------------------------------
              * INITIAL ACTIONS
              ------------------------------------------------------*/
              //Set initial sticky selector
              self._setStickySelector();

              //distance from the top where we should decide if fixed or not. => function of topbar height + admin bar height
              //=> set on instantiation and reset on resize
              this.topStickPoint          = new czrapp.Value( self._getTopStickPoint() );

              //set fixed-header-on if is desktop because menu is already set to fixed position, we want to have the animation from the start
              // + Adjust padding top if desktop sticky
              if ( ! self._isMobile() && self.hasStickyCandidate() ) {
                    self._adjustDesktopTopNavPaddingTop();
              }

        },//stickify




        /*-----------------------------------------------------
        * ANIMATE
        ------------------------------------------------------*/
        //args = { direction : up / down , forceFixed : false, menu_wrapper : $ element, fast : false }
        _animate : function( args ) {
              // args = _.extend(
              //       {
              //             direction : 'down',
              //             forceFixed : false,
              //             menu_wrapper : {},
              //             fast : false,
              //       },
              //       args || {}
              // );
              var dfd = $.Deferred(),
                  self = this,
                  $menu_wrapper = ! args.menu_wrapper.length ? czrapp.$_header.find( self.currentStickySelector() ) : args.menu_wrapper;


              this.animationPromise = dfd;

              //Bail here if  we don't have a menu element
              if ( ! $menu_wrapper.length )
                return dfd.resolve().promise();

              //Make sure we are position:fixed with the 'fixed-header-on' css class before doing anything
              //If the position is already fixed, don't move,
              //if not, make sure we are currently scrolling up before setting it, otherwise the sticky candidate will appear for a few ms before being animated up
              self.isFixedPositionned( self.isFixedPositionned() ? true : ( 'up' == self.scrollDirection() || args.forceFixed ) );//toggles the css class 'fixed-header-on' from the czrapp.$_header element

              var _do = function() {
                    var translateYUp = $menu_wrapper[0].getBoundingClientRect().height,
                        translateYDown = 0,
                        _translate;

                    if ( args.fast ) {
                          $menu_wrapper.addClass( 'fast' );
                    }
                    // Handled via CSS
                    // //Handle the specific case of user logged in ( wpadmin bar length not false ) and previewing website with a mobile device < 600 px
                    // //=> @media screen and (max-width: 600px)
                    // // admin-bar.css?ver=4.7.3:1097
                    // // #wpadminbar {
                    // //     position: absolute;
                    // // }
                    // if ( _.isFunction( window.matchMedia ) && matchMedia( 'screen and (max-width: 600px)' ).matches && 1 == czrapp.$_wpadminbar.length ) {
                    //       //translateYUp = translateYUp + czrapp.$_wpadminbar.outerHeight();
                    //       translateYDown = translateYDown - $menu_wrapper.outerHeight();
                    // }

                    _translate = 'up' == args.direction ? 'translate(0px, -' + translateYUp + 'px)' : 'translate(0px, -' + translateYDown + 'px)';
                    self.stickyHeaderAnimating( true );
                    self.stickyHeaderAnimationDirection = args.direction;
                    $menu_wrapper.toggleClass( 'sticky-visible', 'down' == args.direction );

                    $menu_wrapper.css({
                          //transform: 'up' == args.direction ? 'translate3d(0px, -' + _height + 'px, 0px)' : 'translate3d(0px, 0px, 0px)'
                          '-webkit-transform': _translate,   /* Safari and Chrome */
                          '-moz-transform': _translate,       /* Firefox */
                          '-ms-transform': _translate,        /* IE 9 */
                          '-o-transform': _translate,         /* Opera */
                          transform: _translate
                    });

                    //this delay is tied to hardcoded css animation delay
                    _.delay( function() {
                          //Say it ain't so
                          self.stickyHeaderAnimating( false );
                          if ( args.fast ) {
                                $menu_wrapper.removeClass('fast');
                          }
                          dfd.resolve();
                    }, args.fast ? 100 : 350 );
                    return dfd;
              };//_do

              _.delay( function() {
                    //TO IMPLEMENT
                    var sticky_menu_id = _.isString( $menu_wrapper.attr('data-menu-id') ) ? $menu_wrapper.attr('data-menu-id') : '';
                    // if ( czrapp.userXP.mobileMenu && czrapp.userXP.mobileMenu.has( sticky_menu_id ) ) {
                    //       czrapp.userXP.mobileMenu( sticky_menu_id )( 'collapsed' ).done( function() {
                    //             _do();
                    //       });
                    // }
                    //Is the mobile menu expanded ?
                    if ( czrapp.userXP.isResponsive() && 1 == $('.mobile-navbar__wrapper').length ) {
                          //var $mobile_menu = $('.mobile-navbar__wrapper');
                          if ( self._isMobileMenuExpanded() ) {
                                self._toggleMobileMenu().done( function() {
                                      _do().done( function() { self._mayBeresetTopPosition(); } );
                                });
                          } else {
                                _do();
                          }
                    } else {
                          _do();
                    }

                    if ( czrapp.userXP.mobileMenu && czrapp.userXP.mobileMenu.has( sticky_menu_id ) ) {
                          czrapp.userXP.mobileMenu( sticky_menu_id )( 'collapsed' ).done( function() {
                                _do();
                          });
                    }
              }, 10 );
              return dfd.promise();
        },




        /*-----------------------------------------------------
        * HELPERS
        ------------------------------------------------------*/
        _isMobileMenuExpanded : function() {
              var $mobile_menu = $('.mobile-navbar__wrapper');
              if ( 1 != $('.mobile-navbar__wrapper').length )
                return false;

              return 1 == $mobile_menu.find('.ham-toggler-menu').length && "true" == $mobile_menu.find('.ham-toggler-menu').attr('aria-expanded');
        },


        //MOBILE MENU STATE LISTENER
        _toggleMobileMenu : function() {
            return $.Deferred( function() {
                  var $mobile_menu = $('.mobile-navbar__wrapper'),
                      _dfd_ = this;
                  if ( 1 == $mobile_menu.length ) {
                        //close the mobile menu
                        $mobile_menu.find('.ham-toggler-menu').trigger('click');
                        _.delay( function() {
                              _dfd_.resolve();
                        }, 350 );
                  } else {
                        _dfd_.resolve();
                  }
            }).promise();
        },


        //@return void()
        //Is fired on first load and on resize
        //set the currentStickySelector observable Value()
        // this.stickyCandidatesMap = {
        //       mobile : {
        //             mediaRule : 'only screen and (max-width: 719px)',
        //             selector : 'mobile-sticky'
        //       },
        //       desktop : {
        //             mediaRule : 'only screen and (min-width: 720px)',
        //             selector : 'desktop-sticky'
        //       }
        // };
        _setStickySelector : function() {
              var self = this,
                  _selector = false;

              // self.currentStickySelector = self.currentStickySelector || new czrapp.Value('');
              _.each( self.stickyCandidatesMap, function( _params ) {
                    if ( _.isFunction( window.matchMedia ) && matchMedia( _params.mediaRule ).matches && 'no_stick' != self.userStickyOpt() ) {
                          _selector = '.' + _params.selector;
                    }
              });
              self.currentStickySelector( _selector );
        },

        //@return string : no_stick, stick_up, stick_always
        //falls back on no_stick
        _setUserStickyOpt : function( device ) {
              var self = this;
              if ( _.isUndefined( device ) ) {
                    // self.currentStickySelector = self.currentStickySelector || new czrapp.Value('');
                    _.each( self.stickyCandidatesMap, function( _params, _device ) {
                          if ( _.isFunction( window.matchMedia ) && matchMedia( _params.mediaRule ).matches ) {
                                device = _device;
                          }
                    });
              }
              device = device || 'desktop';

              return ( czrapp.localized.menuStickyUserSettings && czrapp.localized.menuStickyUserSettings[ device ] ) ? czrapp.localized.menuStickyUserSettings[ device ] : 'no_stick';
        },


        //@return a number
        //used to set the topStickPoint Value
        //the question is, if the current sticky candidate is not the topbar AND that there is a topbar, let's return this topbar's height
        _getTopStickPoint : function() {
              // //Do we have a topbar ?
              // if ( 1 !== czrapp.$_header.find( '[data-czr-model_id="topbar"]' ).length && 1 !== czrapp.$_header.find( '[data-czr-template="header/topbar"]' ).length )
              //   return 0;
              // //if there's a topbar, is this topbar the current sticky candidate ?
              // if ( czrapp.$_header.find( '[data-czr-model_id="topbar"]' ).hasClass( 'desktop-sticky') )
              //   return 0;
              // return czrapp.$_header.find( '[data-czr-model_id="topbar"]' ).height();

              if ( this.$_navbars_wrapper.length < 1 )
                return 0;

              //Do we have a topbar
              //todo: refer to a common jQuery selector (class or id)
              var self = this;

              //if there's a topbar, is this topbar the current sticky candidate ?
              if ( 1 == self.$_topbar.length && ! self.$_topbar.is( $( this.currentStickySelector() ) ) ) {
                    //$_topbar[0].getBoundingClientRect().height => returns 0 in mobiles when the topbar is  hidden in mobiles
                    return self.$_navbars_wrapper.offset().top + self.$_topbar[0].getBoundingClientRect().height;
                  //when the topbar is the sticky candidate we should also add a padding top to the $_navbars_wrapper
                  //top "push down" the primay navbar
              }

              return self.$_navbars_wrapper.offset().top;

        },

        //This is specific to Hueman
        _adjustDesktopTopNavPaddingTop : function() {
              // var self = this;
              // if ( ! self._isMobile() && self.hasStickyCandidate() ) {
              //       $('.full-width.topbar-enabled #header').css( 'padding-top', czrapp.$_header.find( self.currentStickySelector() ).outerHeight() );
              // } else {
              //       $('#header').css( 'padding-top', '' );
              // }
        },

        //RESET MOBILE HEADER TOP POSITION
        //@return void()
        //Make sure the header is visible when close to the top
        //Fired on each 'scrolling-finished' <=> user has not scrolled during 250 ms
        //+ 'up' == self.scrollDirection()
        _mayBeresetTopPosition : function() {

              var  self = this, $menu_wrapper = self.stickyMenuWrapper;
              //Bail if we are scrolling up
              if ( 'up' != self.scrollDirection() )
                return;
              //Bail if no menu wrapper
              //Or if we are already after the threshold
              //Or if we are scrolling down
              if ( ! $menu_wrapper.length )
                return;

              if ( self.scrollPosition() >= self.stickyHeaderThreshold )
                return;


              if ( ! self._isMobile() ) {
                  self._adjustDesktopTopNavPaddingTop();
              }

              //Always add this class => make sure the transition is smooth
              //self.isFixedPositionned( true );//toggles the css class 'fixed-header-on' from the czrapp.$_header element
              self.stickyMenuDown( true, { force : true, fast : true } ).done( function() {
                    self.stickyHeaderAnimating( true );
                    ( function() {
                          return $.Deferred( function() {
                              var dfd = this;
                              _.delay( function() {
                                    if ( 'up' == self.scrollDirection() && self.scrollPosition() < 10) {
                                          $menu_wrapper.css({
                                                '-webkit-transform': '',   /* Safari and Chrome */
                                                '-moz-transform': '',       /* Firefox */
                                                '-ms-transform': '',        /* IE 9 */
                                                '-o-transform': '',         /* Opera */
                                                transform: ''
                                          });
                                    }
                                    self.stickyHeaderAnimating( false );
                                    self.isFixedPositionned( false );
                                    dfd.resolve();
                              }, 10 );
                          }).promise();
                    } )().done( function() {});
              });
        },

        //@return void()
        //@param push = boolean
        _pushPrimaryNavBarDown : function( push ) {
              push = push || this.isFixedPositionned();
              if ( 1 == this.$_primary_navbar.length && 1 == this.$_topbar.length && this.$_topbar.is( $( this.currentStickySelector() ) ) ) {
                    this.$_primary_navbar.css( { 'padding-top' : push ? this.$_topbar[0].getBoundingClientRect().height + 'px' : '' } );
              }
        },

        _refreshOrResizeReact : function() {
              var  self = this;
              //always reset the userStickyOpt ( czrapp.Value() ) when resizing
              //=> desktop and mobile sticky user option can be different
              self.userStickyOpt( self._setUserStickyOpt() );

              //reset the current sticky selector
              self._setStickySelector();

              //reset the topStickPoint Value
              self.topStickPoint( self._getTopStickPoint() );

              //maybe push the primary nav bar if the topbar is the current sticky candidate
              self._pushPrimaryNavBarDown();

              //Always close the mobile menu expanded when resizing
              if ( self._isMobileMenuExpanded() ) {
                    self._toggleMobileMenu();
              }

              if ( self.hasStickyCandidate() ) {
                    self.stickyMenuDown( self.scrollPosition() < self.stickyHeaderThreshold ,  { fast : true } ).done( function() {
                          czrapp.$_header.css( 'height' , '' );
                          self.isFixedPositionned( false );//removes css class 'fixed-header-on' from the czrapp.$_header element
                          if ( self.hasStickyCandidate() ) {
                                czrapp.$_header.css( 'height' , czrapp.$_header[0].getBoundingClientRect().height );
                                //make sure we don't set the position to fixed if not scrolled enough : self.scrollPosition() must be > self.topStickPoint()
                                self.isFixedPositionned( self.scrollPosition() > self.topStickPoint() );//toggles the css class 'fixed-header-on' from the czrapp.$_header element
                          }
                    });
              } else {
                    self.stickyMenuDown( false ).done( function() {
                          $('#header').css( 'padding-top', '' );
                    });
              }

              //Adjust padding top if desktop sticky
              if ( ! self._isMobile() ) {
                    self._adjustDesktopTopNavPaddingTop();
              } else {
                    $('.full-width.topbar-enabled #header').css( 'padding-top', '' );
                    //Make sure the transform property is reset when swithing from desktop to mobile on resize
                    self._mayBeresetTopPosition();
              }
        }

  };//_methods{}

  czrapp.methods.UserXP = czrapp.methods.UserXP || {};
  $.extend( czrapp.methods.UserXP , _methods );

})(jQuery, czrapp);
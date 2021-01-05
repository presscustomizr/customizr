// global CZRParams
var czrapp = czrapp || {};

(function($, czrapp) {
   var _methods =   {

      //outline firefox fix, see https://github.com/presscustomizr/customizr/issues/538
      outline: function() {
            if ( 'function' == typeof( tcOutline ) ) {
                tcOutline();
            }
      },

      //VARIOUS HOVERACTION
      variousHoverActions : function() {
            if ( czrapp.$_body.hasClass( 'czr-is-mobile' ) )
                return;

            /* Grid */
            $( '.grid-container__alternate, .grid-container__square-mini, .grid-container__plain' ).on( 'mouseenter mouseleave', '.entry-media__holder, article.full-image .tc-content', _toggleArticleParentHover );
            $( '.grid-container__masonry, .grid-container__classic').on( 'mouseenter mouseleave', '.grid__item', _toggleArticleParentHover );

            //Featured pages ( including FPU ) + gallery
            czrapp.$_body.on( 'mouseenter mouseleave', '.gallery-item, .widget-front, .fpc-widget-front', _toggleThisHover );

            /* end Grid */

            /* Widget li */
            czrapp.$_body.on( 'mouseenter mouseleave', '.widget li', _toggleThisOn );

            function _toggleArticleParentHover( evt ) {
                  _toggleElementClassOnHover( $(this).closest('article'), 'hover', evt );
            }

            function _toggleThisHover( evt ) {
                  _toggleElementClassOnHover( $(this), 'hover', evt );
            }

            function _toggleThisOn( evt ) {
                  _toggleElementClassOnHover( $(this), 'on', evt );
            }

            function _toggleElementClassOnHover( $_el, _class, _evt ) {
                  if ( 'mouseenter' == _evt.type )
                     $_el.addClass( _class );
                  else if ( 'mouseleave' == _evt.type )
                     $_el.removeClass( _class );
            }
      },




      //FORM FOCUS ACTION
      formFocusAction : function() {
            var _input_types       = [
                      'input[type="url"]',
                      'input[type="email"]',
                      'input[type="text"]',
                      'input[type="password"]',
                      'textarea'
                ],
                _focusable_class        = 'czr-focus',
                _focusable_field_class  = 'czr-focusable',
                _focus_class            = 'in-focus',
                _czr_form_class         = 'czr-form',
                _parent_selector        = '.'+ _czr_form_class + ' .'+_focusable_class,
                _inputs                 = _.map( _input_types, function( _input_type ){ return _parent_selector + ' ' + _input_type ; } ).join(),
                $_focusable_inputs      = $( _input_types.join() );

            if ( $_focusable_inputs.length <= 0 )
              return;


            //This is needed to add a class to the input parent (label parent) so that
            //we can limit absolute positioning + translations only to relevant ones ( defined in _input_types )
            //consider the exclude?!
            $_focusable_inputs.each( function() {
               var $_this = $(this);
               if ( !$_this.attr('placeholder') && ( $_this.closest( '#buddypress' ).length < 1 ) ) {
                  $(this)
                        .addClass(_focusable_field_class)
                        .parent().addClass(_focusable_class);
               }
            });


            var _toggleThisFocusClass = function( evt ) {
                  var $_el       = $(this),
                        $_parent = $_el.closest(_parent_selector);

                  //toggle class with a very little delay (accounting for the focus loss when clicking on the icn-close)
                  setTimeout(
                        function(){
                            if ( $_el.val() || ( evt && ( 'focusin' == evt.type || 'focus' == evt.type ) ) ) {
                                  $_parent.addClass( _focus_class );
                            } else {
                                  $_parent.removeClass( _focus_class );
                            }
                        },
                        50
                  );
            };

            czrapp.$_body.on( 'in-focus-load.czr-focus focusin focusout', _inputs, _toggleThisFocusClass );

            //on ready :   think about search forms in search pages
            $(_inputs).trigger( 'in-focus-load.czr-focus' );

            //search form clean on .icn-close click
            czrapp.$_body.on( 'click', '.' + _focusable_class + ' .icn-close', function(e) {
                  e.preventDefault();
                  e.stopPropagation();

                  var $_search_field = $(this).closest('form').find('.czr-search-field');

                  if ( $_search_field.length ) {
                        //empty search field if needed and keep the focus
                        if ( $_search_field.val() ) {
                              $_search_field.val('').focus();
                        }
                        //otherwise (search field alredy empty) release th focus
                        else {
                              $_search_field.blur();
                        }
                  }

            });
      },

      //React on Esc key pressed
      onEscapeKeyPressed : function() {
            var ESCAPE_KEYCODE                  = 27, // KeyboardEvent.which value for Escape (Esc) key

                Event = {
                      KEYEVENT          : 'keydown', //or keyup, if we want to react to the release event
                      SIDENAV_CLOSE     : 'sn-close',
                      OVERLAY_TOGGLER   : 'click',
                      SIDENAV_TOGGLER   : 'click'
                },

                ClassName = {
                      SEARCH_FIELD      : 'czr-search-field',
                      OLVERLAY_SHOWN    : 'czr-overlay-opened',
                      SIDENAV_SHOWN     : 'tc-sn-visible'
                },

                Selector = {
                      OVERLAY           : '.czr-overlay',
                      SIDENAV           : '#tc-sn',
                      OVERLAY_TOGGLER   : '.czr-overlay-toggle_btn',
                      SIDENAV_TOGGLER   : '[data-toggle="sidenav"]'
                };


            czrapp.$_body.on( Event.KEYEVENT, function(evt) {

                  if ( ESCAPE_KEYCODE == evt.which ) {

                        //search field clean and collapse (release the focus)
                        if ( $(evt.target).hasClass( ClassName.SEARCH_FIELD ) ) {
                              $( evt.target ).val('').blur();
                              return;
                        }

                        //else close the overlay if exists and opened
                        //might be the search full page or the full page menu
                        if ( $( Selector.OVERLAY ).length && czrapp.$_body.hasClass( ClassName.OLVERLAY_SHOWN ) ) {
                              $( Selector.OVERLAY ).find( Selector.OVERLAY_TOGGLER ).trigger( Event.OVERLAY_TOGGLER );
                              return;
                        }

                        //else close the sidenav if exists and opened
                        if ( $( Selector.SIDENAV ).length  && czrapp.$_body.hasClass( ClassName.SIDENAV_SHOWN ) ) {
                              $( Selector.SIDENAV ).find( Selector.SIDENAV_TOGGLER ).trigger( Event.SIDENAV_TOGGLER );
                              return;
                        }
                  }

            });

      },

      variousHeaderActions : function() {
            var //_mobile_viewport = 992,
                self = this;


            //custom scrollbar for woocommerce list
            czrapp.$_body.on( 'shown.czr.czrDropdown', '.nav__woocart', function() {
                  var $_el = $(this);
                  var _do = function() {
                        var $_to_scroll = $_el.find('.product_list_widget');
                        if ( $_to_scroll.length && ! $_to_scroll.hasClass('mCustomScrollbar') ) {
                              $_to_scroll.mCustomScrollbar({
                                    theme: czrapp.$_body.hasClass('header-skin-light') ? 'minimal-dark' : 'minimal',
                              });
                        }
                  };
                  if ( 'function' == typeof $.fn.mCustomScrollbar ) {
                        _do();
                  } else {
                        self.maybeLoadCustomScrollAssets().done( function() {
                            _do();
                       });
                  }
            });

      },

      /*  Toggle header search
      /* ------------------------------------ */
      //@return void()
      headerSearchToLife : function() {
            var self = this,

                _search_toggle_event            = 'click',

                _search_overlay_toggler_sel     = '.search-toggle_btn.czr-overlay-toggle_btn',
                _search_overlay_toggle_class    = 'full-search-opened czr-overlay-opened',

                transitionEnd                   = 'transitionend webkitTransitionEnd otransitionend oTransitionEnd MSTransitionEnd',
                _transitioning_el_sel           = '.czr-overlay .overlay-content',
                _search_input_sel               = '.czr-search-field',
                _search_overlay_open_class      = 'full-search-opened',

                _search_dropdown_wrapper_sel    = '.mobile-utils__wrapper',
                _search_dropdown_button_sel     = '.search-toggle_btn.czr-dropdown',
                _search_dropdown_menu_sel       = '.nav__search .czr-dropdown-menu',
                _search_dropdown_menu_input_sel = '.czr-search-field',
                _search_dropdown_expanded_class = 'show',

                _mobile_menu_to_close_sel       = '.ham-toggler-menu:not(.czr-collapsed)',
                _mobile_menu_close_event        = 'click.czr.czrCollapse',
                _mobile_menu_opened_event       = 'show.czr.czrCollapse', //('show' : start of the uncollapsing animation; 'shown' : end of the uncollapsing animation)
                _mobile_menu_sel                = '.mobile-nav__nav';


            /* header search overlay button */
            czrapp.$_body.on( _search_toggle_event, _search_overlay_toggler_sel, function(evt) {
                  evt.preventDefault();

                  // determine the position of the search icon to add a body class
                  // so that the search field is not partially hidden
                  // @see issue : https://github.com/presscustomizr/customizr/issues/1854
                  var search_icon = $(_search_overlay_toggler_sel),
                      rect = search_icon[0].getBoundingClientRect(),
                      winWidth = $(window).width(),
                      isLeftSide = rect.left < winWidth/2;

                  czrapp.$_body.removeClass( 'search-icon-left').removeClass('search-icon-right');
                  czrapp.$_body.toggleClass( isLeftSide ? 'search-icon-left' : 'search-icon-right' );
                  czrapp.$_body.toggleClass( _search_overlay_toggle_class );
            });

            /* header search overlay button:
            *  automatically focus/blur on overlay open/close
            *  see: https://github.com/presscustomizr/customizr/issues/1374
            */
            czrapp.$_body.on( transitionEnd, _transitioning_el_sel, function( evt ) {
                  //make sure we react only to the relevant element transition and not to nested elements ones
                  if ( $( _transitioning_el_sel ).get()[0]  != evt.target )
                        return;

                  if ( czrapp.$_body.hasClass( _search_overlay_open_class ) ) {
                        $(this).find(  _search_input_sel ).focus();
                  }
                  else {
                        $(this).find(  _search_input_sel ).blur();
                  }
            });

            /* header search dropdown */
            self.headerSearchExpanded = new czrapp.Value( false );
            //listen to app event
            //the callback returns a promise to allow sequential actions
            self.headerSearchExpanded.bind( function( exp ) {
                  return $.Deferred( function() {
                        var _dfd = this;
                        $(  _search_dropdown_button_sel, _search_dropdown_wrapper_sel )
                                  .toggleClass( _search_dropdown_expanded_class, exp )
                                  .attr('aria-expanded', exp );

                        //do this before starting the animation
                        if ( exp ) {
                              //collapse mobile menu if open
                              $( _mobile_menu_to_close_sel ).trigger( _mobile_menu_close_event );
                        }

                        $(  _search_dropdown_menu_sel, _search_dropdown_wrapper_sel )
                            .attr('aria-expanded', exp )
                            .stop()[ ! exp ? 'slideUp' : 'slideDown' ]( {
                                  duration : 250,
                                  complete : function() {
                                    if ( exp ) {
                                          //focus the search input
                                          $( _search_dropdown_menu_input_sel, $(this) ).focus();
                                    }
                                    _dfd.resolve();
                                  }
                            } );
                  }).promise();
            }, { deferred : true } );

            //listen to user actions
            czrapp.setupDOMListeners(
                  [
                        {
                              trigger   : _search_toggle_event,
                              selector  : _search_dropdown_button_sel,
                              actions   : function() {
                                    czrapp.userXP.headerSearchExpanded( ! czrapp.userXP.headerSearchExpanded() );
                              }
                        },
                  ],//actions to execute
                  { dom_el: $( _search_dropdown_wrapper_sel ) },//dom scope
                  czrapp.userXP //instance where to look for the cb methods
            );

            //collapse on resize
            czrapp.userXP.windowWidth.bind( function() {
                  self.headerSearchExpanded( false );
                  // May 2020 for https://github.com/presscustomizr/customizr/issues/1807
                  _.delay( function() {
                     czrapp.$_body.removeClass( _search_overlay_toggle_class );
                  }, 250 );
            });

            //collapse on mobile menu show
            czrapp.$_body.on( _mobile_menu_opened_event, _mobile_menu_sel, function() {
                  self.headerSearchExpanded( false );
                  // May 2020 for https://github.com/presscustomizr/customizr/issues/1807
                  _.delay( function() {
                     czrapp.$_body.removeClass( _search_overlay_toggle_class );
                  }, 250 );
            });

            //collapse on menu animation
            if ( czrapp.userXP.stickyHeaderAnimating ) {
                  czrapp.userXP.stickyHeaderAnimating.bind( function() {
                        self.headerSearchExpanded( false );
                        // May 2020 for https://github.com/presscustomizr/customizr/issues/1807
                        _.delay( function() {
                           czrapp.$_body.removeClass( _search_overlay_toggle_class );
                        }, 250 );
                  });
            }
      },//toggleHeaderSearch

      // @return promise()
      maybeLoadCustomScrollAssets : function() {
            var dfd = $.Deferred();
            if ( 'function' == typeof $.fn.mCustomScrollbar ) {
                  return dfd.resolve().promise();
            } else {

                  // Load the style
                  // Needs to be loaded before because : https://github.com/presscustomizr/customizr/issues/1285
                  $('head').append( $('<link/>' , {
                              rel : 'stylesheet',
                              id : 'czr-custom-scroll-bar',
                              type : 'text/css',
                              href : czrapp.localized.assetsPath + 'css/jquery.mCustomScrollbar.min.css'
                        }) );

                  //Load the js
                  $.ajax( {
                        url : ( czrapp.localized.assetsPath + 'js/libs/jquery-mCustomScrollbar.min.js'),
                        cache : true,
                        dataType: "script"
                  }).done(function() {
                        if ( 'function' != typeof $.fn.mCustomScrollbar )
                          return dfd.rejected();
                        dfd.resolve();
                  }).fail( function() {
                        czrapp.errorLog( 'mCustomScrollbar instantiation failed' );
                  });
            }
            return dfd.promise();
      },

      //SMOOTH SCROLL
      smoothScroll: function() {
            // Don't SmoothScroll for devices under 1024px ( tablet in landscape ). Most likely mobile devices.
            // Nov 2017 : Always smoothScroll when infinite is on => fix bug on chrome => https://github.com/presscustomizr/customizr/commit/50ba5eb495e3504629162cbf079e9888220f42ae#commitcomment-25377040
            if ( $('body').hasClass( 'czr-infinite-scroll-on' ) || ( czrapp.localized.SmoothScroll && czrapp.localized.SmoothScroll.Enabled && ! czrapp.base.matchMedia( 1024 ) ) ) {
                  smoothScroll( czrapp.localized.SmoothScroll.Options );
            }
      },

      magnificPopup : function() {},

      //ATTACHMENT FADE EFFECT
      attachmentsFadeEffect : function() {
            $( '.attachment-image-figure img' ).delay(500).addClass( 'opacity-forced' );
      },

      pluginsCompatibility: function() {
            /*
             * Super socializer
             * it prints the socializer vertical bar filtering the excerpt
             * so as child of .entry-content__holder.
             * In alternate layouts, when centering sections, the use of the translate property
             * changed the fixed behavior (of the aforementioned bar) to an absoluted behavior
             * with the following core we move the bar outside the section
             * ( different but still problems occurr with the masonry )
            */
            var $_ssbar = $( '.the_champ_vertical_sharing, .the_champ_vertical_counter', '.article-container' );
            if ( $_ssbar.length )
              $_ssbar.detach().prependTo('.article-container');
      },


      /* Find a way to make this smaller but still effective */
      //The job of this method is to align horizontally the various elements of the featured pages for a given row of featured pages
      //=>
      featuredPagesAlignment : function() {
          var $_featured_pages   = $('.featured-page .widget-front'),
               _n_featured_pages = $_featured_pages.length,
               doingAnimation      = false,
               _lastWinWidth       = '';


          if ( _n_featured_pages < 2 )
            return;

          var $_fp_elements       = new Array( _n_featured_pages ),
               _n_elements          = new Array( _n_featured_pages );

          //Grab all subelements having class starting with fp-
          //Requires all fps having same html structure...
          $.each( $_featured_pages, function( _fp_index, _fp ) {
                $_fp_elements[_fp_index]   = $(_fp).find( '[class^=fp-]' );
                _n_elements[_fp_index]      = $_fp_elements[_fp_index].length;
          });

          _n_elements = Math.max.apply(Math, _n_elements );

          if ( ! _n_elements )
            return;

          var _offsets      = new Array( _n_elements ),
               _maxs          = new Array( _n_elements );

         /*
         * Build the _offsets matrix
         * Row => element (order given by _elements array)
         * Col => fp
         */
         for (var i = 0; i < _n_elements; i++)
            _offsets[i] = new Array( _n_featured_pages );


          //fire
          maybeSetElementsPosition();
          //bind
          czrapp.$_window.on( 'resize', _.debounce( maybeSetElementsPosition, 20 ) );

         function maybeSetElementsPosition() {

            if ( ! doingAnimation ) {
               var _winWidth = czrapp.$_window.width();
               /*
               * we're not interested in win height resizing
               */
               if ( _winWidth == _lastWinWidth )
                  return;

               _lastWinWidth = _winWidth;

               doingAnimation = true;

               window.requestAnimationFrame(function() {
                  setElementsPosition();
                  doingAnimation = false;
               });

            }
         }

         //All elements are aligned using padding top.
         //This means that the aligned elements are dependant on each others and we can't do it on only one pass
        function setElementsPosition() {
              /*
              * this array will store the
              */
              var _fp_offsets = [], _element_index, _fp_index;
              //Loop on each single feature page block
              for ( _element_index = 0; _element_index < _n_elements; _element_index++ ) {
                  //loop on each featured page blocks
                  for ( _fp_index = 0; _fp_index < _n_featured_pages; _fp_index++ ) {
                    //Reset and grab the the top offset for each element
                    var $_el      = $( $_fp_elements[ _fp_index ][ _element_index ] ),
                          _offset = 0,
                          $_fp      = $($_featured_pages[_fp_index]);

                    if ( $_el.length > 0 ) {
                       //reset maybe added paddingTop
                       $_el.css( 'paddingTop', '' );
                       //retrieve the top position
                       _offset = $_el.offset().top;

                    }
                    _offsets[_element_index][_fp_index] = _offset;

                    /*
                    * Build the array of fp offset once (first loop on elements)
                    */
                    if ( _fp_offsets.length < _n_featured_pages )
                       _fp_offsets[_fp_index] = parseFloat( $_fp.offset().top);
                 }//endfor


                 /*
                 * Break this only loop when featured pages are one on top of each other
                 * featured pages top offset differs
                 * We continue over other elements as we need to reset other marginTop
                 */
                 if ( 1 != _.uniq(_fp_offsets).length )
                    continue;

                 /*
                 * for each type of element store the max offset value
                 */
                 _maxs[_element_index] = Math.max.apply(Math, _offsets[_element_index] );

                 /*
                 * apply the needed offset for each featured page element
                 */
                 for ( _fp_index = 0; _fp_index < _n_featured_pages; _fp_index++ ) {
                    var $__el      = $( $_fp_elements[ _fp_index ][ _element_index ] ),
                          __offset;

                    if ( $__el.length > 0 ) {
                       __offset = +_maxs[_element_index] - _offsets[_element_index][_fp_index];
                       if ( __offset )
                          $__el.css( 'paddingTop', parseFloat($__el.css('paddingTop')) + __offset );
                    }
                 }//endfor
              }//endfor
          }//endfunction
      },//endmethod

      //Btt arrow visibility
      bttArrow : function() {
            var doingAnimation = false,
                $_btt_arrow = $( '.czr-btta' );

            if ( 0 === $_btt_arrow.length )
                return;
            var bttArrowVisibility = function() {
                  if ( ! doingAnimation ) {
                     doingAnimation = true;

                     window.requestAnimationFrame( function() {
                          $_btt_arrow.toggleClass( 'show', czrapp.$_window.scrollTop() > ( czrapp.$_window.height() ) );
                          doingAnimation = false;
                     });
                  }
            };//bttArrowVisibility

            czrapp.$_window.on( 'scroll', _.throttle( bttArrowVisibility, 20 ) );
            bttArrowVisibility();
      },//bttArrow


      //BACK TO TOP
      backToTop : function() {
            var $_html = $("html, body"),
                 _backToTop = function( evt ) {
                      return ( evt.which > 0 || "mousedown" === evt.type || "mousewheel" === evt.type) && $_html.stop().off( "scroll mousedown DOMMouseScroll mousewheel keyup", _backToTop );
                 };

            czrapp.$_body.on( 'click touchstart touchend czr-btt', '.czr-btt', function ( evt ) {
                  evt.preventDefault();
                  evt.stopPropagation();
                  $_html.on( "scroll mousedown DOMMouseScroll mousewheel keyup", _backToTop );
                  $_html.animate({
                        scrollTop: 0
                  }, 1e3, function () {
                        $_html.stop().off( "scroll mousedown DOMMouseScroll mousewheel keyup", _backToTop );
                  });
           });
      },

      //SMOOTH SCROLL FOR AUTHORIZED LINK SELECTORS
      //If the ! isAnchorScrollEnabled, an anchor scroll candidate must validate those conditions :
      //1) having the [data-anchor-scroll="true"] attibute
      //2) the href attribute should start by #
      //3) the link should be inside the #content dom element
      //
      //If isAnchorScrollEnabled => all links passing conditions 2 and 3 will be accepted
      anchorSmoothScroll : function() {
            var _excl_sels = ( czrapp.localized.anchorSmoothScrollExclude && _.isArray( czrapp.localized.anchorSmoothScrollExclude.simple ) ) ? czrapp.localized.anchorSmoothScrollExclude.simple.join(',') : '',
                self = this,
                $_links = $('a[data-anchor-scroll="true"][href^="#"]').not( _excl_sels );

            //Widen the number of candidates if the user option is enabled
            if ( czrapp.localized.isAnchorScrollEnabled ) {
                // Makes sure we include not only the anchor links not only in the content but also in header and footer
                // https://github.com/presscustomizr/customizr/issues/1662
                $_links = $_links.add( '#tc-page-wrap a[href^="#"],#tc-sn a[href^="#"]').not( _excl_sels );
            }
            //Deep exclusion
            //are ids and classes selectors allowed ?
            //all type of selectors (in the array) must pass the filter test
            var   _links,
                  _deep_excl = _.isObject( czrapp.localized.anchorSmoothScrollExclude.deep ) ? czrapp.localized.anchorSmoothScrollExclude.deep : null;

            if ( _deep_excl ) {
                  _links = _.toArray($_links).filter( function ( _el ) {
                    return ( 2 == ( ['ids', 'classes'].filter(
                                  function( sel_type) {
                                      return self.isSelectorAllowed( $(_el), _deep_excl, sel_type);
                                  } ) ).length
                          );
                  });
            }

            $(_links).on('click', function () {
                  var anchor_id = $(this).attr("href");

                  //anchor el exists ?
                  if ( ! $(anchor_id).length )
                    return;

                  if ('#' != anchor_id) {
                      $('html, body').animate({
                          scrollTop: $(anchor_id).offset().top
                      }, 700, czrapp.localized.isAnchorScrollEnabled ? 'easeOutExpo' : 'linear' ); //<= the jquery effect library ( for the easeOutExpo effect ) is only available when czr_fn_is_checked( czr_fn_opt( 'tc_link_scroll' ) ),
                  }
                  return false;
            });//click
      },

      /*  Gutenberg fine alignfull cover image width fine tuning
      /* ------------------------------------ */
      gutenbergAlignfull : function() {
            //check if there's at least an alignfull in a full-width layout with no sidebars
            var _isPage   = czrapp.$_body.hasClass( 'page' ),
                  _isSingle = czrapp.$_body.hasClass( 'single' ),
                  // The "cover image" block of the new WP editor has been renamed "cover". See https://github.com/WordPress/gutenberg/pull/10659, but posts created with the former cover-image block will still use the wp-block-cover-image css class.
                  _coverImageSelector = '.czr-full-layout.czr-no-sidebar .entry-content .alignfull[class*=wp-block-cover]',
                  _alignFullSelector  = '.czr-full-layout.czr-no-sidebar .entry-content .alignfull[class*=wp-block]',
                  _alignTableSelector = [
                                    '.czr-boxed-layout .entry-content .wp-block-table.alignfull',
                                    '.czr-boxed-layout .entry-content .wp-block-table.alignwide',
                                    '.czr-full-layout.czr-no-sidebar .entry-content .wp-block-table.alignwide'
                                    ];


            //allowed only in singular
            if ( ! ( _isPage || _isSingle ) ) {
                  return;
            }

            if ( _isSingle ) {
                  _coverImageSelector = '.single' + _coverImageSelector;
                  _alignFullSelector  = '.single' + _alignFullSelector;
                  _alignTableSelector = '.single' + _alignTableSelector.join(',.single');
            } else {
                  _coverImageSelector = '.page' + _coverImageSelector;
                  _alignFullSelector  = '.page' + _alignFullSelector;
                  _alignTableSelector = '.page' + _alignTableSelector.join(',.page');
            }


            var _coverWParallaxImageSelector   = _coverImageSelector + '.has-parallax',
                  _classParallaxTreatmentApplied = 'czr-alignfull-p',
                  $_refWidthElement              = $('#tc-page-wrap'),
                  $_refContainedWidthElement     = $( '.container[role="main"]', $_refWidthElement );

            if ( $( _alignFullSelector ).length > 0 ) {
                  _add_alignelement_style( $_refWidthElement, _alignFullSelector, 'czr-gb-alignfull' );
                  if ( $(_coverWParallaxImageSelector).length > 0 ) {
                  _add_parallax_treatment_style();
                  }
                  czrapp.userXP.windowWidth.bind( function() {
                        _add_alignelement_style( $_refWidthElement, _alignFullSelector, 'czr-gb-alignfull' );
                        _add_parallax_treatment_style();
                  });
            }
            if ( $( _alignTableSelector ).length > 0 ) {
                  _add_alignelement_style( $_refContainedWidthElement, _alignTableSelector, 'czr-gb-aligntable' );
                  czrapp.userXP.windowWidth.bind( function() {
                        _add_alignelement_style( $_refContainedWidthElement, _alignTableSelector, 'czr-gb-aligntable' );
                  });
            }
            function _add_parallax_treatment_style() {
                  $( _coverWParallaxImageSelector ).each(function() {
                        $(this)
                              .css( 'left', '' )
                              .css( 'left', -1 * $(this).offset().left )
                              .addClass(_classParallaxTreatmentApplied);
                  });
            }
            function _add_alignelement_style( $_refElement, _selector, _styleId ) {
                  var newElementWidth = $_refElement[0].getBoundingClientRect().width,
                        $_style         = $( 'head #' + _styleId );

                  if ( 1 > $_style.length ) {
                        $_style = $('<style />', { 'id' : _styleId });
                        $( 'head' ).append( $_style );
                        $_style = $( 'head #' + _styleId );
                  }
                  $_style.html( _selector + '{width:'+ newElementWidth +'px}' );
            }
      },

      mayBeLoadFontAwesome : function() {
            jQuery( function() {
                  if ( ! CZRParams.deferFontAwesome )
                    return;
                  var $candidates = $('[class*=fa-]');
                  if ( $candidates.length < 1 )
                    return;

                  // January 2021
                  // inject with a delay by default, force injection without delay on first user scroll or mousemove
                  // => Offers better performance results with Google lighthouse
                  var _inject_in_progress = false;
                  var _inject = function(type) {
                      _inject_in_progress = true;
                      if ( $('head').find( '[href*="fontawesome-all.min.css"]' ).length > 0 )
                        return;
                      var link = document.createElement('link');
                      link.setAttribute('href', CZRParams.fontAwesomeUrl ); // assets/shared/fonts/fa/css/fontawesome-all.min.css?
                      link.setAttribute('id', 'czr-font-awesome');
                      link.setAttribute('rel', 'stylesheet' );
                      document.getElementsByTagName('head')[0].appendChild(link);
                  };
                  setTimeout( function() {
                        if ( !_inject_in_progress ) {_inject('timeout'); }
                  }, 3000 );

                  czrapp.$_window
                      .one('scroll', function() {
                          if ( !_inject_in_progress ) {_inject('scroll'); }
                      })
                      .one('mousemove', function() {
                          if ( !_inject_in_progress ) {_inject('mousemove'); }
                      });

            });
      },
      // March 2020 : gfonts can be preloaded since https://github.com/presscustomizr/customizr/issues/1816
      maybePreloadGoogleFonts : function() {
            if ( !window.CZRParams || !CZRParams.preloadGfonts || _.isEmpty(CZRParams.googleFonts) )
              return;
            var _hasPreloadSupport = function( browser ) {
                  var link = document.createElement('link');
                  var relList = link.relList;
                  if (!relList || !relList.supports)
                    return false;
                  return relList.supports('preload');
                },
                headTag = document.getElementsByTagName('head')[0],
                link = document.createElement('link'),
                _injectFinalAsset = function() {
                    var link = this;
                    // this is the link element
                    link.setAttribute('rel', 'stylesheet');
                };

            link.setAttribute('href', '//fonts.googleapis.com/css?family=' + CZRParams.googleFonts + '&display=swap');
            link.setAttribute('rel', _hasPreloadSupport() ? 'preload' : 'stylesheet' );
            link.setAttribute('id', 'czr-gfonts-css-preloaded' );
            link.setAttribute('as', 'style');
            link.onload = function() {
                this.onload=null;
                _injectFinalAsset.call(link);
            };
            link.onerror = function(er) {
                console.log('Customizr preloadAsset error', er );
            };
            headTag.appendChild(link);

      }//maybePreloadGoogleFonts

   };//_methods{}

   czrapp.methods.UserXP = czrapp.methods.UserXP || {};
   $.extend( czrapp.methods.UserXP , _methods );

})(jQuery, czrapp);

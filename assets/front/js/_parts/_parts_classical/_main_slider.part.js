var czrapp = czrapp || {};

/************************************************
* ADD SLIDER METHODS
*************************************************/
(function($, czrapp) {
      var _methods = {

            //INIT
            initOnDomReady : function() {
                  var self = this;

                  // cache jQuery el
                  this.$_sliders = $( 'div[id*="customizr-slider"]' );

                  //@todo EVENT
                  //Recenter the slider arrows on resize
                  czrapp.$_window.on('resize', function(){
                    self.centerSliderArrows();
                  });
            },



            fireSliders : function(name, delay, hover) {
              //Slider with localized script variables
              var self = this,
                  _name   = name || TCParams.SliderName,
                  _delay  = delay || TCParams.SliderDelay,
                  _hover  = hover || TCParams.SliderHover,
                  _cellSelector = '.czr-item',
                  _cssLoaderClass = 'tc-css-loader',
                  _css_loader = '<div class="' + _cssLoaderClass + ' tc-mr-loader" style="display:none"><div></div><div></div><div></div></div>';

              if ( 0 === _name.length || 1 > self.$_sliders.length )
                return;

              //Shall we smartload ?
              if ( czrapp.localized.imgSmartLoadsForSliders ) {
                    //Disable the css transition on img => create a unwanted expansion effect when img is smartloaded
                    self.$_sliders.addClass('disable-transitions-for-smartload');
                    //always smartload the first slide
                    self.$_sliders.find( _cellSelector + '.active').imgSmartLoad().data( 'czr_smartLoaded', true );

                    var _maybeRemoveLoader = function( $_cell ) {
                          $_cell.find('.czr-css-loader').fadeOut( {
                                duration: 'fast',
                                done : function() { $(this).remove();}
                          } );
                    };


                    var _smartLoadCellImg = function( _event_ ) {
                          _event_ = _event_ || 'czr-smartloaded';
                          var $_cell = this;
                          // First check that the image can be a candidate for smartloading
                          // => has it been parsed server side ?
                          // If so, it should have eithe rthe data-src or the data-smartload attribute ( @see php function czr_fn_regex_callback() )
                          if ( 1 > $_cell.find('img[data-src], img[data-smartload]').length )
                            return;

                          // If the image is not yet smartloaded, do it
                          if ( ! $_cell.data( 'czr_smartLoaded' ) ) {
                                //if the loader has not been rendered yet, render it
                                if ( 1 > $_cell.find('.czr-css-loader').length ) {
                                      $_cell.append( _css_loader ).find('.czr-css-loader').fadeIn( 'slow' );
                                }
                                $_cell.imgSmartLoad().data( 'czr_smartLoaded', true ).addClass( _event_ );
                                $_cell.data( 'czr_loader_timer' , $.Deferred( function() {
                                      var self = this;
                                      _.delay( function() {
                                            self.resolve();
                                      }, 2000 );
                                      return this.promise();
                                }) );
                                // Make sure the css loader is always removed after a moment. Even if the smartload event has not been triggered
                                $_cell.data( 'czr_loader_timer' ).done( function() {
                                      _maybeRemoveLoader( $_cell );
                                });
                          }
                    };

                    //SCHEDULE LOADING SCENARIOS
                    //=> The general idea is to first load the lighest possible page
                    //=> we first wait for a user action, click on a slider arrow or scroll.
                    //=> then after a longer delay, if none of the 2 first event was trigger, fire the smartload.
                    // this last delay has to be long enough to not downgrade the pagespeed test. If set too small, it might add Kbytes weight to the page too early.
                    //
                    // + 500 ms SELECT slide event => can be triggered by a user action, or an autoplay
                    // As soon as a slide get selected, smart load all imgs of the slider
                    //
                    //  1) verify that the img is a valid candidate for smartload
                    //  2) append the css loader
                    //  3) smartload

                    //Flag the slider wrapper with a class when any loading scenario is achieved
                    self.$_sliders.data( 'czr_smartload_scheduled', $.Deferred().done( function() {
                          self.$_sliders.addClass('czr-smartload-scheduled');
                    }) );

                    //When infinite is on, hidden elements are cached, for that the dom is emptied, the remove element are stored as a collection of nodes, and re-appended later
                    //during this process, we lose jQuery reference to DOM elements.
                    //That's why we need this check
                    var _isSliderDataSetup = function() {
                          return 1 <= self.$_sliders.length && ! _.isUndefined( self.$_sliders.data( 'czr_smartload_scheduled' ) );
                    };

                    //React one time on 'customizr.slide'
                    self.$_sliders.data( 'czr_schedule_select',
                          $.Deferred( function() {
                                var dfd = this;
                                self.$_sliders.parent().one( 'customizr.slide click' , function() {
                                      dfd.resolve();
                                } );
                          }).done( function() {
                                if ( ! _isSliderDataSetup() || 'resolved' == self.$_sliders.data( 'czr_smartload_scheduled' ).state() )
                                    return;

                                self.$_sliders.find( _cellSelector ).each( function() {
                                      _smartLoadCellImg.call( $(this), 'czr-smartloaded-on-select' );
                                });
                                self.$_sliders.data( 'czr_smartload_scheduled').resolve();
                          })
                    );//data( 'czr_schedule_select' )



                    // + 1000 ms SMARTLOAD ON SCROLL
                    // React one time on scroll
                    self.$_sliders.data( 'czr_schedule_scroll_resize',
                          $.Deferred( function() {
                                var dfd = this;
                                czrapp.$_window.one( 'scroll resize', function() {
                                      _.delay( function() { dfd.resolve(); }, 5000 );
                                });
                          }).done( function() {
                                if ( ! _isSliderDataSetup() || 'resolved' == self.$_sliders.data( 'czr_smartload_scheduled' ).state() )
                                    return;

                                self.$_sliders.find( _cellSelector ).each( function() {
                                      _smartLoadCellImg.call( $(this), 'czr-smartloaded-on-scroll' );
                                });
                                self.$_sliders.data( 'czr_smartload_scheduled').resolve();
                          })
                    );//data( 'czr_schedule_scroll_resize' )



                    // + 10000 ms AUTO SMARTLOAD AFTER A MOMENT
                    //Then schedule the smartloading after a moment.
                    self.$_sliders.data( 'czr_schedule_autoload',
                          $.Deferred( function() {
                                var dfd = this;
                                _.delay( function() { dfd.resolve(); }, 10000 );
                          }).done( function() {
                                if ( ! _isSliderDataSetup() || 'resolved' == self.$_sliders.data( 'czr_smartload_scheduled' ).state() )
                                    return;

                                self.$_sliders.find( _cellSelector ).each( function() {
                                      _smartLoadCellImg.call( $(this), 'czr-auto-smartloaded' );
                                });
                                self.$_sliders.data( 'czr_smartload_scheduled').resolve();
                          })
                    );

                    //Make sure the loader is removed when smarload triggered
                    self.$_sliders.on( 'smartload', _cellSelector , function() {
                          _maybeRemoveLoader( $(this) );
                    });
              }//if czrapp.localized.imgSmartLoadsForSliders

              if ( 0 !== _delay.length && ! _hover ) {
                this.$_sliders.czrCarousel({
                    interval: _delay,
                    pause: "false"
                });
              } else if ( 0 !== _delay.length ) {
                this.$_sliders.czrCarousel({
                    interval: _delay
                });
              } else {
                this.$_sliders.czrCarousel();
              }
            },

            parallaxSliders : function() {
              if ( 'function' == typeof $.fn.czrParallax ) {
                $( '.czr-parallax-slider' ).czrParallax();
              }
            },

            manageHoverClass : function() {
              //add a class to the slider on hover => used to display the navigation arrow
              this.$_sliders.on('mouseenter', function() {
                  $(this).addClass('tc-slid-hover');
                }).on('mouseleave', function() {
                  $(this).removeClass('tc-slid-hover');
                });
            },

            //SLIDER ARROWS
            centerSliderArrows : function() {
              if ( 0 === this.$_sliders.length )
                  return;
              this.$_sliders.each( function() {
                  var _slider_height = $( '.carousel-inner' , $(this) ).height();
                  $('.tc-slider-controls', $(this) ).css("line-height", _slider_height +'px').css("max-height", _slider_height +'px');
              });
            },


            //Slider swipe support with hammer.js
            addSwipeSupport : function() {
              if ( 'function' != typeof($.fn.hammer) || 0 === this.$_sliders.length )
                return;

              //prevent propagation event from sensible children
              this.$_sliders.on('touchstart touchmove', 'input, button, textarea, select, a:not(".tc-slide-link")', function(ev) {
                  ev.stopPropagation();
              });

              var _is_rtl = czrapp.$_body.hasClass('rtl');
              this.$_sliders.each( function() {
                  $(this).hammer().on('swipeleft', function() {
                      $(this).czrCarousel( ! _is_rtl ? 'next' : 'prev' );
                  });
                  $(this).hammer().on('swiperight', function(){
                      $(this).czrCarousel( ! _is_rtl ? 'prev' : 'next' );
                  });
              });
            },

            //Has to be fire on load after all other methods
            //@todo understand why...
            sliderTriggerSimpleLoad : function() {
              this.triggerSimpleLoad( this.$_sliders.find('.carousel-inner img') );
            }
      };//methods {}

      czrapp.methods.Slider = {};
      $.extend( czrapp.methods.Slider , _methods );

})(jQuery, czrapp);
var czrapp = czrapp || {};
/***************************
* ADD JQUERY PLUGINS METHODS
****************************/
(function($, czrapp, Waypoint ) {
      var _methods = {
            centerImagesWithDelay : function( delay ) {
                  var self = this;
                  //fire the center images plugin
                  //setTimeout( function(){ self.emit('centerImages'); }, delay || 300 );
                  setTimeout( function(){ self.emit('centerImages'); }, delay || 50 );
            },


            centerInfinity : function() {

                  var centerInfiniteImagesModernStyle = function ( collection, _container ) {
                      var $_container  = $(_container);

                      if ( 'object' !== typeof collection || 1 > $_container.length )
                        return;

                      //actually this can be avoided if we improve the centerImages to skip already parse imgs
                      //in that case we have to only trigger the simple load that will fire the centering
                      _.each( collection, function( elementSelector ) {

                            var $_imgsToSimpleLoad = $(  elementSelector + ' .js-centering', $_container ).centerImages( {
                                  enableCentering : 1,
                                  enableGoldenRatio : false,
                                  disableGRUnder : 0,//<= don't disable golden ratio when responsive,
                                  zeroTopAdjust: 0,
                                  setOpacityWhenCentered : false,//will set the opacity to 1
                                  oncustom : [ 'simple_load', 'smartload' ]
                            })
                            //images with src which starts with "data" are our smartload placeholders
                            //we don't want to trigger the simple_load on them
                            //the centering, will be done on the smartload event (see onCustom above)
                            .find( 'img:not([src^="data"])' );

                            //trigger the simple load
                            czrapp.methods.Base.triggerSimpleLoad( $_imgsToSimpleLoad );
                      });

                  };

                  //maybe center infinite appended elements
                  czrapp.$_body.on( 'post-load', function( e, response ) {
                        if ( ( 'undefined' !== typeof response ) && 'success' == response.type && response.collection && response.container ) {
                              centerInfiniteImagesModernStyle(
                                  response.collection,
                                  '#'+response.container //_container
                              );
                        }
                  } );
            },

            //IMG SMART LOAD
            //.article-container covers all post / page content : single and list
            //__before_main_wrapper covers the single post thumbnail case
            //.widget-front handles the featured pages
            //.post-related-articles handles the related posts
            imgSmartLoad : function() {
                  var smartLoadEnabled = 1 == czrapp.localized.imgSmartLoadEnabled,
                      //Default selectors for where are : $( '[class*=grid-container], .article-container', '.__before_main_wrapper', '.widget-front', '.post-related-articles' ).find('img');
                      _where = czrapp.localized.imgSmartLoadOpts.parentSelectors.join(),
                      _params = _.size( czrapp.localized.imgSmartLoadOpts.opts ) > 0 ? czrapp.localized.imgSmartLoadOpts.opts : {};

                  //Smart-Load images
                  //imgSmartLoad plugin will trigger the smartload event when the img will be loaded
                  //the centerImages plugin will react to this event centering them
                  // $smart_load_opts       = apply_filters( 'tc_img_smart_load_options' , array(

                  //          'parentSelectors' => array(
                  //              '[class*=grid-container], .article-container',
                  //              '.__before_main_wrapper',
                  //              '.widget-front',
                  //              '.post-related-articles',
                  //              '.tc-singular-thumbnail-wrapper',
                  //          ),
                  //          'opts'     => array(
                  //              'excludeImg' => array( '.tc-holder-img' )
                  //          )

                  //  ));
                  var _doLazyLoad = function() {
                        if ( !smartLoadEnabled )
                          return;

                        $(_where).each( function() {
                            // if the element already has an instance of LazyLoad, simply trigger an event
                              if ( !$(this).data('smartLoadDone') ) {
                                    $(this).imgSmartLoad(_params);
                              } else {
                                    $(this).trigger('trigger-smartload');
                              }
                        });
                      //$(_where).imgSmartLoad(_params);
                  };
                  _doLazyLoad();

                  // Observer Mutations off the DOM to detect images
                  // <=> of previous $(document).bind( 'DOMNodeInserted', fn );
                  // implemented to fix https://github.com/presscustomizr/hueman/issues/880
                  this.observeAddedNodesOnDom('body', 'img', _.debounce( function(element) {
                        _doLazyLoad();
                  }, 50 ));



                  //If the centerAllImg is on we have to ensure imgs will be centered when simple loaded,
                  //for this purpose we have to trigger the simple-load on:
                  //1) imgs which have been excluded from the smartloading if enabled
                  //2) all the images in the default 'where' if the smartloading isn't enabled
                  //simple-load event on holders needs to be triggered with a certain delay otherwise holders will be misplaced (centering)
                  if ( 1 == czrapp.localized.centerAllImg ) {
                        var self                   = this,
                            $_to_center;
                        if ( smartLoadEnabled ) {
                              $_to_center = $( _.filter( $( _where ).find('img'), function( img ) {
                                  return $(img).is(czrapp.localized.imgSmartLoadOpts.opts.excludeImg.join());
                                }) );
                        } else { //filter
                              $_to_center = $( _where ).find('img');
                        }

                        var $_to_center_with_delay = $( _.filter( $_to_center, function( img ) {
                                return $(img).hasClass('tc-holder-img');
                        }) );

                        //imgs to center with delay
                        setTimeout( function(){
                              self.triggerSimpleLoad( $_to_center_with_delay );
                        }, 800 );
                        //all other imgs to center
                        self.triggerSimpleLoad( $_to_center );
                  }
            },


            /**
            * CENTER VARIOUS IMAGES
            * @return {void}
            */
            centerImages : function() {
                  var $wrappersOfCenteredImagesCandidates = $('.widget-front .tc-thumbnail, .js-centering.entry-media__holder, .js-centering.entry-media__wrapper');

                  //Featured pages and classical grid are always centered
                  // $('.tc-grid-figure, .widget-front .tc-thumbnail').centerImages( {
                  //       enableCentering : 1,
                  //       oncustom : ['smartload', 'refresh-height', 'simple_load'],
                  //       zeroTopAdjust: 0,
                  //       enableGoldenRatio : false,
                  // } );
                  var _css_loader = '<div class="czr-css-loader czr-mr-loader" style="display:none"><div></div><div></div><div></div></div>';
                  $wrappersOfCenteredImagesCandidates.each( function() {
                        $( this ).append(  _css_loader ).find('.czr-css-loader').fadeIn( 'slow');
                  });
                  $wrappersOfCenteredImagesCandidates.centerImages({
                        onInit : true,
                        enableCentering : 1,
                        oncustom : ['smartload', 'refresh-height', 'simple_load'],
                        enableGoldenRatio : false, //true
                        zeroTopAdjust: 0,
                        setOpacityWhenCentered : false,//will set the opacity to 1
                        addCenteredClassWithDelay : 50,
                        opacity : 1
                  });
                  _.delay( function() {
                        $wrappersOfCenteredImagesCandidates.find('.czr-css-loader').fadeOut( {
                          duration: 500,
                          done : function() { $(this).remove();}
                        } );
                  }, 300 );


                  // if for any reasons, the centering did not happen, the imgs will not be displayed because opacity will stay at 0
                  // => the opacity is set to 1 as soon as v-centered or h-centered has been added to a img element candidate for centering
                  // @see css
                  var _mayBeForceOpacity = function( params ) {
                        params = _.extend( {
                              el : {},
                              delay : 0
                        }, _.isObject( params ) ? params : {} );

                        if ( 1 !== params.el.length  || ( params.el.hasClass( 'h-centered') || params.el.hasClass( 'v-centered') ) )
                          return;

                        _.delay( function() {
                              params.el.addClass( 'opacity-forced');
                        }, params.delay );

                  };
                  //For smartloaded image, let's wait for the smart load to happen, for the others, let's do it now without delay
                  if ( czrapp.localized.imgSmartLoadEnabled ) {
                        $wrappersOfCenteredImagesCandidates.on( 'smartload', 'img' , function( ev ) {
                              if ( 1 != $( ev.target ).length )
                                return;
                              _mayBeForceOpacity( { el : $( ev.target ), delay : 200 } );
                        });
                  } else {
                        $wrappersOfCenteredImagesCandidates.find('img').each( function() {
                              _mayBeForceOpacity( { el : $(this), delay : 100 } );
                        });
                  }

                  //then last check
                  _.delay( function() {
                        $wrappersOfCenteredImagesCandidates.find('img').each( function() {
                              _mayBeForceOpacity( { el : $(this), delay : 0 } );
                        });
                  }, 1000 );


                  //---------------------------------------/
                  // CENTER FPU
                  //---------------------------------------/
                  var $_fpuEl = $('.fpc-widget-front .fp-thumb-wrapper');
                  if ( 1 < $_fpuEl.length ) {
                        var _isFPUimgCentered = _.isUndefined( czrapp.localized.FPUImgCentered ) ? 1 == czrapp.localized.centerAllImg : 1 == czrapp.localized.FPUImgCentered;
                        $_fpuEl.centerImages( {
                            onInit : false,
                            enableCentering : _isFPUimgCentered,
                            enableGoldenRatio : false,
                            disableGRUnder : 0,//<= don't disable golden ratio when responsive
                            zeroTopAdjust : 0,
                            oncustom : ['smartload', 'simple_load', 'block_resized', 'fpu-recenter']
                        });

                        // Smartload enabled ?
                        // If not, trigger a simple load, because the fpu images are not centered on init
                        if ( 1 != czrapp.localized.imgSmartLoadEnabled ) {
                            czrapp.base.triggerSimpleLoad( $_fpuEl.find("img:not(.tc-holder-img)") );
                        } else {
                            //we don't want to center the holder imgs
                            $_fpuEl.find("img:not(.tc-holder-img)").each( function() {
                                    //if already smartloaded, we are late, let's trigger the simple load
                                    if ( $(this).data( 'czr-smart-loaded') ) {
                                        czrapp.base.triggerSimpleLoad( $(this) );
                                    }
                            });
                        }
                        //simple-load event on holders needs to be needs to be triggered with a certain delay otherwise holders will be misplaced (centering)
                        if ( _isFPUimgCentered && 1 != czrapp.localized.imgSmartLoadEnabled ) {
                              var $_holder_img = $_fpuEl.find("img.tc-holder-img");
                              if ( 0 < $_holder_img.length ) {
                                  czrapp.base.triggerSimpleLoad( $_holder_img );
                                  setTimeout( function(){
                                        czrapp.base.triggerSimpleLoad( $_holder_img );
                                  }, 100 );
                              }
                        }
                  }//if ( 1 < $_fpuEl.length )
            },//center_images

            parallax : function() {
                  $( '.parallax-item' ).czrParallax();
                  /* Refresh waypoints when mobile menu button is toggled as
                  *  the static/relative menu will push the content
                  */
                  $('.ham__navbar-toggler').on('click', function(){
                        setTimeout( function(){
                        Waypoint.refreshAll(); }, 400 ); }
                  );
            },

            // This is a wrapper for $.fn.magnificPopup
            // magnificPopup should be loaded only when a candidate becomes visible
            czrMagnificPopup : function( $lightBoxCandidate, params ) {
                  // Is lightbox enabled ?
                  if ( !CZRParams.isLightBoxEnabled )
                    return;

                  if ( 1 > $lightBoxCandidate.length )
                    return;

                  var _scrollHandle = function() {},//abstract that we can unbind
                      _do = function() {

                        // I've been executed forget about me
                        czrapp.$_window.off( 'scroll', _scrollHandle );

                        if ( 'function' == typeof $.fn.magnificPopup ) {
                              // instantiate if not done yet
                              //if ( ! $lightBoxCandidate.data( 'magnificPopup' ) )
                                $lightBoxCandidate.magnificPopup( params );
                        } else {
                              // Check if the load request has already been made, but not yet finished.
                              if ( czrapp.base.scriptLoadingStatus.czrMagnificPopup && 'pending' == czrapp.base.scriptLoadingStatus.czrMagnificPopup.state() ) {
                                    czrapp.base.scriptLoadingStatus.czrMagnificPopup.done( function() {
                                          $lightBoxCandidate.magnificPopup( params );
                                    });
                                    return;
                              }

                              // set the script loading status now to avoid several calls
                              czrapp.base.scriptLoadingStatus.czrMagnificPopup = czrapp.base.scriptLoadingStatus.czrMagnificPopup || $.Deferred();

                              //Load the style
                              if ( $('head').find( '#czr-magnific-popup' ).length < 1 ) {
                                    $('head').append( $('<link/>' , {
                                          rel : 'stylesheet',
                                          id : 'czr-magnific-popup',
                                          type : 'text/css',
                                          href : czrapp.localized.assetsPath + 'css/magnific-popup.min.css'
                                    }) );
                              }

                              $.ajax( {
                                    url : ( czrapp.localized.assetsPath + 'js/libs/jquery-magnific-popup.min.js'),
                                    cache : true,// use the browser cached version when available
                                    dataType: "script"
                              }).done(function() {
                                    if ( 'function' != typeof( $.fn.magnificPopup ) )
                                      return;
                                    //the script is loaded. Say it globally.
                                    czrapp.base.scriptLoadingStatus.czrMagnificPopup.resolve();

                                    // instantiate if not done yet
                                    //if ( ! $lightBoxCandidate.data( 'magnificPopup' ) )
                                      $lightBoxCandidate.magnificPopup( params );
                              }).fail( function() {
                                    czrapp.errorLog( 'Magnific popup instantiation failed for candidate : '  + $lightBoxCandidate.attr( 'class' ) );
                              });
                        }
                  };//_do()

                  // Fire now or schedule when becoming visible.
                  if ( czrapp.base.isInWindow( $lightBoxCandidate ) ) {
                        _do();
                  } else {
                        _scrollHandle = _.throttle( function() {
                              if ( czrapp.base.isInWindow( $lightBoxCandidate ) ) {
                                    _do();
                              }
                        }, 100 );
                        czrapp.$_window.on( 'scroll', _scrollHandle );
                  }
            },

            lightBox : function() {
                  var self = this,
                      _arrowMarkup = '<span class="czr-carousel-control btn btn-skin-dark-shaded inverted mfp-arrow-%dir% icn-%dir%-open-big"></span>';

                  /* The magnificPopup delegation is very good
                  * it works when clicking on a dynamically added a.expand-img
                  * but also when clicking on an another a.expand-img the image speficified in the
                  * dynamically added a.expang-img href is added to the gallery
                  */
                  if ( $('a.expand-img').length > 0 ) {
                        this.czrMagnificPopup( $( '[class*="grid-container__"]' ), {
                          delegate: 'a.expand-img', // child items selector, by clicking on it popup will open
                          type: 'image'
                          // other options
                        });
                  }


                  /* galleries in singles Create grouped galleries */
                  if ( $('[data-lb-type="grouped-gallery"]').length > 0 ) {
                        $( '.czr-gallery' ).each( function(){
                              self.czrMagnificPopup( $(this), {
                                    delegate: '[data-lb-type="grouped-gallery"]', // child items selector, by clicking on it popup will open
                                    type: 'image',
                                    gallery: {
                                          enabled: true,
                                          arrowMarkup: _arrowMarkup
                                    }
                                    // other options
                              });
                        });
                  }

                  /*
                  * in singular, or in post list plain, when former tc_fancybox enabled
                  */
                  if ( $('[data-lb-type="grouped-post"]').length > 0 ) {
                        this.czrMagnificPopup( $('#content'), {
                              delegate: '[data-lb-type="grouped-post"]',
                              type: 'image',
                              gallery: {
                                   enabled: true,
                                   arrowMarkup: _arrowMarkup
                              }
                        });
                  }

                  //in post lists galleries post formats
                  //only one button for each gallery
                  czrapp.$_body.on( 'click', '[class*="grid-container__"] .expand-img-gallery', function(e) {
                        e.preventDefault();

                        var $_expand_btn    = $( this ),
                            $_gallery_crsl  = $_expand_btn.closest( '.czr-carousel' );


                        if ( $_gallery_crsl.length < 1 )
                          return;

                        var _do = function() {
                              if ( ! $_gallery_crsl.data( 'mfp' ) ) {

                                    self.czrMagnificPopup( $_gallery_crsl, {
                                        delegate: '.carousel-cell img',
                                        type: 'image',
                                        gallery: {
                                          enabled: true,
                                          arrowMarkup: _arrowMarkup
                                        }
                                    });
                                    $_gallery_crsl.data( 'mfp', true );
                              }

                              if ( $_gallery_crsl.data( 'mfp' ) ) {
                                    //open the selected carousel gallery item
                                    $_gallery_crsl.find( '.is-selected img' ).trigger('click');
                              }
                        };

                        //schedule it with a small delay if the flickity not yeat ready
                        if ( 0 < $_gallery_crsl.find( '.flickity-slider').length ) {
                              _do();
                        } else {
                              _.delay( function() {
                                    _do();
                              }, 500 );//<= let the flickity slider be setup, because the slider is setup on click
                        }

                  });
            },

      };//_methods{}

      czrapp.methods.JQPlugins = {};
      $.extend( czrapp.methods.JQPlugins , _methods );


})(jQuery, czrapp, Waypoint);
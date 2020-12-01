var czrapp = czrapp || {};

/************************************************
* ADD SLIDER METHODS
*************************************************/
(function($, czrapp ) {
      var _methods = {

            initOnCzrReady : function() {
                  var self = this;

                  this.slidersSelectorMap = {
                        mainSlider : '[id^="customizr-slider-main"] .carousel-inner',
                        galleries : '.czr-gallery.czr-carousel .carousel-inner',
                        relatedPosts : '.grid-container__square-mini.carousel-inner'
                  };


                  /* Allow parallax */
                  czrapp.$_body.on( 'czr-flickity-ready.flickity', '.czr-parallax-slider', self._parallax );

                  /* Fire fittext */
                  czrapp.$_body.on( 'czr-flickity-ready.flickity', self.slidersSelectorMap.mainSlider, function() {
                    $(this).find( '.carousel-caption .czrs-title' ).czrFitText(
                                1.5,//<=kompressor
                                {
                                      maxFontSize : 65,//the default max font-size
                                      minFontSize : 18,
                                }
                    );
                    $(this).find( '.carousel-caption .czrs-subtitle' ).czrFitText(
                                2,//<=kompressor
                                {
                                      maxFontSize : 35,//the default max font-size
                                      minFontSize : 15,
                                }
                    );
                    $(this).find( '.carousel-caption .czrs-cta-wrapper' ).czrFitText(
                                2,//<=kompressor
                                {
                                      maxFontSize : 18,//the default max font-size
                                      minFontSize : 12,
                                }
                    );
                  });


                  // Disable controllers when the first or the latest slide is in the viewport (for the related posts)
                  czrapp.$_body.on( 'select.flickity', '.czr-carousel .carousel-inner', self._slider_arrows_enable_toggler );

                  // For gallery carousels to preserve the dragging we have to move the possible background gallery link inside the flickity viewport
                  czrapp.$_body.on( 'czr-flickity-ready.flickity', self.slidersSelectorMap.galleries, self._move_background_link_inside );
                  /*Handle custom nav */
                  // previous
                  czrapp.$_body.on( 'click prev.czr-carousel', '.czr-carousel-prev', function(e) { self._slider_arrows.apply( this , [ e, 'previous' ] );} );
                  // next
                  czrapp.$_body.on( 'click next.czr-carousel', '.czr-carousel-next', function(e) { self._slider_arrows.apply( this , [ e, 'next' ] );} );

                  //FIRE CAROUSELS
                  this.fireRelatedPostsCarousel();
                  this.scheduleGalleryCarousels();

                  //If the user has enabled the smartload for slider ( enabled by default )
                  //1) the first (is-selected slide is smartloaded)
                  //2) then we react on 'select.flickity' to smartload the other => append css loader + smartload
                  //3) and on 'smartload' ( triggered by our smartload plugin ) => remove css loader
                  // this.fireMainSlider().centerMainSlider();
                  this.fireMainSlider();


                  // GALLERIES
                  // => REACT ON ENDLESSLY ( INFINITE SCROLL ) EVENT TO INSTANTIATE / DESTROY FLICKITY
                  // @see endlessly.js
                  czrapp.$_body.on( 'post-load', function( e, response ) {
                        if ( ( 'undefined' !== typeof response ) && 'success' == response.type && response.collection && response.container ) {
                              // Are there galleries in the incoming set of posts?
                              if ( ! response.html || -1 === response.html.indexOf( 'czr-gallery' ) || -1 === response.html.indexOf( 'czr-carousel' ) ) {
                                    return;
                              }
                              self.scheduleGalleryCarousels();
                        }
                  } );

                  // The 'before-endlessly-caching' event is fired just before endlessly.js caches the hidden nodes already rendered
                  // When emptying nodes, we lose Jquery references for DOM elements
                  // => when the set of nodes is "uncached", this can lead to several instantiation of the same plugin on the same element.
                  //
                  // Solution : Use this hook to destroy jQuery plugin instances before it's being cached
                  // then we can bind on 'post-load' to re-instantiate the jQuery plugin on the element
                  // @see endlessly.js => Scroller.prototype.determineURL()
                  //
                  // params = { candidates_for_caching : $() }
                  czrapp.$_body.on( 'before-endlessly-caching', function( e, params ) {
                        if ( ! _.isObject( params ) || _.isUndefined( params.candidates_for_caching || ! ( params.candidates_for_caching instanceof $ ) ) )
                          return;

                        params.candidates_for_caching.find( self.slidersSelectorMap.galleries ).each( function() {
                              if ( $(this).data('flickity') ) {
                                    // move background link outside
                                    // will be back inside flickity viewport on 'czr-flickity-ready.flickity'
                                    // => needed For gallery carousels to preserve the dragging we have to move the possible background gallery link inside the flickity viewport
                                    // Typically looks like this :
                                    // <a class="bg-link" rel="bookmark" title="Permalink to:&nbsp;A Short Gallery from Our Long Trip" href="http://customizr-dev.dev/a-short-gallery-from-our-long-trip/"></a>
                                    var $_bg_link = $(this).find('.bg-link');
                                    //@see templates/parts/content/common/media.php
                                    $(this).closest('.entry-media__wrapper').prepend( $_bg_link );

                                    $(this).flickity( 'destroy' );
                                    //make sure we don't have any loader looping somewhere
                                    $(this).find('.czr-css-loader').remove();
                              }
                        });
                  });

                  // cache the loader here
                  self._css_loader = '<div class="czr-css-loader czr-mr-loader" style="display:none"><div></div><div></div><div></div></div>';


                  // Emit an event when a gallery is in the window view port
                  czrapp.$_window.on('scroll', _.throttle( function() {
                        $( self.slidersSelectorMap.galleries ).each( function() {
                              if ( czrapp.base.isInWindow( $(this) ) ){
                                    $(this).trigger( 'czr-is-in-window', { el : $(this) } );
                              }
                        });
                  }, 50 ) );
            },//_init()



            // This is a wrapper for $.fn.flickity
            // Flickity should be loaded only when a flickity slider candidate becomes visible
            czrFlickity : function( $_sliderCandidate, params ) {
                  if ( 1 > $_sliderCandidate.length )
                    return;

                  var _scrollHandle = function() {};//abstract that we can unbind
                  var _do = function() {
                        // I've been executed forget about me
                        czrapp.$_window.off( 'scroll', _scrollHandle );

                        if ( 'function' == typeof $.fn.flickity ) {
                              // instantiate if not done yet
                              if ( ! $_sliderCandidate.data( 'flickity' ) )
                                $_sliderCandidate.flickity( params );
                        } else {
                              // Check if the load request has already been made, but not yet finished.
                              if ( czrapp.base.scriptLoadingStatus.flickity && 'pending' == czrapp.base.scriptLoadingStatus.flickity.state() ) {
                                    czrapp.base.scriptLoadingStatus.flickity.done( function() {
                                          $_sliderCandidate.flickity( params );
                                    });
                                    return;
                              }

                              // set the script loading status now to avoid several calls
                              czrapp.base.scriptLoadingStatus.flickity = czrapp.base.scriptLoadingStatus.flickity || $.Deferred();

                              //Load the style
                              if ( $('head').find( '#czr-flickity' ).length < 1 ) {
                                    $('head').append( $('<link/>' , {
                                          rel : 'stylesheet',
                                          id : 'czr-flickity',
                                          type : 'text/css',
                                          href : czrapp.localized.assetsPath + 'css/flickity.min.css'
                                    }) );
                              }

                              //Load the js
                              $.ajax( {
                                    url : ( czrapp.localized.assetsPath + 'js/libs/flickity-pkgd.min.js'),
                                    cache : true,// use the browser cached version when availabl
                                    dataType: "script"
                              }).done(function() {
                                    if ( 'function' != typeof( $.fn.flickity ) )
                                      return;

                                    // The script is loaded. Say it Globally
                                    czrapp.base.scriptLoadingStatus.flickity.resolve();
                                    /*
                                    * Modify Flickity prototype for our needs
                                    * Flickity ready
                                    * see https://github.com/metafizzy/flickity/issues/493#issuecomment-262658287
                                    */
                                    var activate = Flickity.prototype.activate;
                                    Flickity.prototype.activate = function() {
                                          if ( this.isActive ) {
                                            return;
                                          }
                                          activate.apply( this, arguments );
                                          this.dispatchEvent( 'czr-flickity-ready', null, this );
                                    };
                                    // instantiate if not done yet
                                    if ( ! $_sliderCandidate.data( 'flickity' ) )
                                      $_sliderCandidate.flickity( params );
                              }).fail( function() {
                                    czrapp.errorLog( 'Flickity instantiation failed for slider candidate : '  + $_sliderCandidate.attr( 'class' ) );
                              });
                        }
                  };//_do()

                  // Fire now or schedule when becoming visible.
                  if ( czrapp.base.isInWindow( $_sliderCandidate ) ) {
                        _do();
                  } else {
                        _scrollHandle = _.throttle( function() {
                              if ( czrapp.base.isInWindow( $_sliderCandidate ) ) {
                                    _do();
                              }
                        }, 100 );
                        czrapp.$_window.on( 'scroll', _scrollHandle );
                  }
            },





            // Schedule the flickity instantiation
            // The goal is to instantiate flickity at the right time.
            // This is essential, in particular on mobile devices and when the infinite scroll is on.
            // => in this scenario, many js tasks are running at the same time and can slow down the navigation
            //
            // Solution for a given gallery grid-item
            // on page load => schedule the smartload of the first image if smartload is enabled
            // Then maybe smartload if enabled + instantiate flickity when
            // 1) user click on the grid-item ( the slider arrow for example )
            // 2) an image of the gallery has been smartloaded or the gallery container is visible in the window
            // AND user stopped scrolling for more than 4 seconds <= important for mobile devices
            scheduleGalleryCarousels : function( $_gallery_container ) {
                  var $_galleries,
                      //_cellSelector = '.carousel-cell',
                      self = this;

                  if ( ! _.isUndefined( $_gallery_container ) && 0 < $_gallery_container.length ) {
                        $_galleries = $_gallery_container.find( self.slidersSelectorMap.galleries );
                  } else {
                        $_galleries = $(self.slidersSelectorMap.galleries);
                  }


                  // In each gallery, whatever event or action fires it, before firing the various actions we check if $_gallery.data( 'czr-gallery-setup' )
                  // if true, we are done
                  // each event is listened to once with $.one()
                  $_galleries.each( function() {
                        var $_gal = $(this),
                            $_firstcell = $_gal.find( '.carousel-cell' ).first(),
                            $_parentGridItem = $_gal.closest('.grid-item');

                        if ( 1 > $_firstcell.length )
                          return;

                        // Center
                        // If smartload enabled, we don't want to fire the centering twice
                        // => disable onInit
                        // the img of the cell is a valid smartload candidate if
                        // 1) it exists
                        // 2) it starts with 'data' : 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'
                        var _isSmartLoadCandidateImg = 0 < $_firstcell.find('img').length && 0 === $_firstcell.find('img').attr('src').indexOf('data');

                        $_firstcell.centerImages( {
                              enableCentering : 1 == czrapp.localized.centerSliderImg,
                              onInit : ! czrapp.localized.imgSmartLoadsForSliders || ( czrapp.localized.imgSmartLoadsForSliders && ! _isSmartLoadCandidateImg ),
                              oncustom : ['smartload']
                        } );


                        // Remove the smart-load-skip-class preventing any smarload and do smartload
                        if ( czrapp.localized.imgSmartLoadsForSliders ) {
                              if ( ! $_firstcell.data('czr_smartLoaded') ) {
                                    $_firstcell.find('img').removeClass('tc-smart-load-skip');
                                    $_firstcell.on( 'smartload', function() {
                                          self._maybeRemoveLoader.call( $_firstcell );
                                    });
                                    self._smartLoadCellImg( { el : $_firstcell, ev : 'czr-smartloaded-on-init', delay : 800 } );
                                    //$_firstcell.imgSmartLoad().data( 'czr_smartLoaded', true ).addClass( 'czr-smartloaded-on-init');
                              }
                        }


                        // schedule firing on first click
                        $_parentGridItem.one( 'click', function() {
                              self._fireGalleryCarousel( $_gal );
                        });

                        // schedule only for devices > 1024 ( tablet in landscape )
                        // => if the page includes several galleries, firing them at the same time might be too expensive on a mobile device.
                        // => it's better to rely on the fire on click only for those devices
                        $_parentGridItem.one( 'smartload czr-is-in-window', function() {
                              if ( czrapp.base.matchMedia( 1024 ) )//<= tablets in landscape mode
                                return;

                              if ( czrapp.userXP.isScrolling() ) {
                                    czrapp.$_body.one( 'scrolling-finished', function() {
                                          self.fireMeWhenStoppedScrolling( { delay : 4000, func : self._fireGalleryCarousel, instance : self, args : [ $_gal ] } );
                                    });
                              } else {
                                    self.fireMeWhenStoppedScrolling( { delay : 4000, func : self._fireGalleryCarousel, instance : self, args : [ $_gal ] } );
                              }
                        });
                  });
            },


            //images are centered by czrapp.userXP.centerImages()
            _fireGalleryCarousel : function( $_gallery ) {
                  var _cellSelector = '.carousel-cell',
                      self = this;
                  //is the dom element still valid ? ( in infinite, it might not be )
                  if ( ! ( $_gallery instanceof $ ) || 1 > $_gallery.length ) {
                        czrapp.errorLog( '_fireGalleryCarousel : the passed element is not printed in the DOM');
                        return;
                  }

                  //did we already do ?
                  if ( $_gallery.data( 'czr-gallery-setup' ) )
                    return;

                  if ( czrapp.localized.imgSmartLoadsForSliders ) {
                        self._smartLoadFlickityImg({
                              sliderEl : $_gallery,
                              cellSelector : _cellSelector,
                              scheduleLoading : false
                        });
                  }
                  if ( _.isUndefined( $_gallery.data('flickity') ) ) {
                        //number of slides
                        //check if has one single slide
                        //in this case the slide will be draggable
                        var _is_single_slide = 1 == $_gallery.find( _cellSelector ).length,
                            //we don't allow pageDots when is single slide or when the has-dots attribute is false
                            _hasPageDots    = ! _is_single_slide && $_gallery.data( 'has-dots' );

                        self.czrFlickity( $_gallery, {
                              prevNextButtons: false,
                              wrapAround: true,
                              imagesLoaded: true,
                              setGallerySize: false,
                              cellSelector: _cellSelector,
                              accessibility: false,
                              dragThreshold: 10,
                              lazyLoad: false,
                              freeScroll: false,
                              pageDots: _hasPageDots,
                              draggable: ! _is_single_slide,
                        });
                        $_gallery.find( _cellSelector ).each( function() {
                              $(this).centerImages( {
                                    enableCentering : 1 == czrapp.localized.centerSliderImg,
                                    onInit : ! czrapp.localized.imgSmartLoadsForSliders,
                                    oncustom : ['smartload']
                              } );
                        });
                  }
                  $_gallery.data( 'czr-gallery-setup', true );
            },


            fireRelatedPostsCarousel : function() {
                  var self = this;
                  //TODO BETTER
                  /* Test only RELATED POSTS !!!!!! */
                  self.czrFlickity( $( self.slidersSelectorMap.relatedPosts ), {
                        prevNextButtons: false,
                        pageDots: false,
                        imagesLoaded: true,
                        cellSelector: 'article',
                        groupCells: true,
                        cellAlign: 'left',
                        dragThreshold: 10,
                        accessibility: false,
                        contain: true /* allows to not show a blank "cell" when the number of cells is odd but we display an even number of cells per viewport */
                  });
            },


            fireMainSlider : function() {
                  var self = this,
                      $_main_slider = $(self.slidersSelectorMap.mainSlider),
                      _cellSelector = '.carousel-cell',
                      $_firstcell = $_main_slider.find( _cellSelector ).first();

                  if ( 1 > $_firstcell.length )
                    return;

                  // Schedule Centering
                  // If smartload enabled, we don't want to fire the centering twice because it will be fired on smartload event
                  // => disable onInit, but only if the image src is a valid smartload candidate
                  $_main_slider.find( _cellSelector ).each( function() {
                        // the img of the cell is a valid smartload candidate if
                        // 1) it exists
                        // 2) it starts with 'data' : 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'
                        var _isSmartLoadCandidateImg = 0 < $(this).find('img').length && 0 === $(this).find('img').attr('src').indexOf('data');
                        $(this).centerImages( {
                              enableCentering : 1 == czrapp.localized.centerSliderImg,
                              onInit : ! czrapp.localized.imgSmartLoadsForSliders || ( czrapp.localized.imgSmartLoadsForSliders && ! _isSmartLoadCandidateImg ),
                              oncustom : [ 'simple_load', 'smartload', 'refresh-centering-on-select' ],
                              defaultCSSVal : { width : '100%' , height : 'auto' },
                              useImgAttr : true,
                              zeroTopAdjust: 0
                        } );
                  });

                  // This is an additional centering triggered on select flickity
                  $_main_slider.on( 'czr-flickity-ready.flickity', function() {
                        // the delay is to avoid the select triggered when instantiating flickity
                        _.delay( function() {
                              $(this).on( 'select.flickity', function() {
                                    if ( $_main_slider.data('flickity').selectedElement && 1 == $( $_main_slider.data('flickity').selectedElement ).length ) {
                                          $( $_main_slider.data('flickity').selectedElement ).trigger( 'refresh-centering-on-select');
                                    }
                              });
                        }, 500 );
                  });

                  if ( czrapp.localized.imgSmartLoadsForSliders ) {
                        this._smartLoadFlickityImg( { sliderEl : $_main_slider, cellSelector : _cellSelector });
                  }

                  //fade out the loading icon per carousel with a little delay
                  //mostly for retina devices (the retina image will be downloaded afterwards
                  //and this may cause the re-centering of the image)
                  setTimeout( function() {
                        $_main_slider.prevAll('.czr-slider-loader-wrapper').fadeOut();
                  }, 300 );

                  //FIRE THE SLIDER
                  if ( $_main_slider.length > 0 ) {
                        //number of slides
                        //check if has one single slide
                        //in this case the slide will be draggable
                        var _is_single_slide = 1 == $_main_slider.find( _cellSelector ).length,
                            _autoPlay        = $_main_slider.data('slider-delay'),
                            //we don't allow pageDots when is single slide or when the has-dots attribute is false
                            _hasPageDots    = !_is_single_slide && $_main_slider.data( 'has-dots' );

                        _autoPlay           =  ( _.isNumber( _autoPlay ) && _autoPlay > 0 ) ? _autoPlay : false;

                        self.czrFlickity( $_main_slider, {
                            prevNextButtons: false,
                            pageDots: _hasPageDots,
                            draggable: !_is_single_slide,

                            wrapAround: true,

                            imagesLoaded: true,

                            //lazyLoad: true,

                            setGallerySize: false,
                            cellSelector: _cellSelector,

                            dragThreshold: 10,

                            autoPlay: _autoPlay, // {Number in milliseconds }

                            accessibility: false,
                        });
                  }
                  return this;
            },


            //--------------------------------------------------/
            // <IMG SMARTLOAD>
            //--------------------------------------------------/
            //params = {
            //  sliderEl : object <= jQuery dom element on which the flickity instance is set
            //  cellSelector : string <= the flickity css selector for a cell element
            //  schedule : boolean <= shall we load all images when flickity ready or schedule various loading scenarios ?
            //}
            _smartLoadFlickityImg : function( params ) {
                  var self = this;
                  if ( ! _.isObject( params )  ) {
                        czrapp.errorLog( '_smartLoadFlickityImg params should be an object' );
                        return;
                  }
                  params = _.extend( {
                      sliderEl : {},
                      cellSelector : '.carousel-cell',
                      scheduleLoading : true
                  }, params );

                  if ( ! ( params.sliderEl instanceof $ ) || 1 > params.sliderEl.length )
                    return;

                  params.sliderEl.on( 'czr-flickity-ready.flickity', function() {
                        // var _getSelectedCell = function() {
                        //       return $( params.sliderEl.data('flickity').selectedCell.element );
                        //     };

                        // First loop on the images and remove the '.tc-smart-load-skip'
                        // => .tc-smart-load-skip is the flag used by the smartload jquery plugin to prevent smartloading an image
                        //
                        // => when is this useful ?
                        // Flagging the image with tc-smart-load-skip allows us to prepare the img when parsing the src server side ( @see czr_fn_parse_imgs() )
                        // and then smartload it later, after having removed the flag.
                        // Use case : the slider of the galleries post format in grids. Grids are globally smartloaded when the option is enabled. ( @see the localized parent selector for smartload : '[class*=grid-container], .article-container' ).
                        // But we don't want to smartload all the images of a gallery slider. Only the first one, and then the other when sliding.
                        // => That's why we need to deactivate the front js part with the flag and control it here afterwards
                        params.sliderEl.find( params.cellSelector ).each( function() {
                              if ( ! $(this).data('czr_smartLoaded') ) {
                                    $(this).find('img').removeClass('tc-smart-load-skip');
                              }
                              // Always smartload the first slide
                              if ( $(this).hasClass( 'is-selected') && ! $(this).data('czr_smartLoaded') ) {
                                    $(this).imgSmartLoad().data( 'czr_smartLoaded', true ).addClass( 'czr-smartloaded-on-init');
                              }
                        });

                        if ( ! params.scheduleLoading ) {
                              params.sliderEl.find( params.cellSelector ).each( function() {
                                    self._smartLoadCellImg( { el : $(this), ev : 'czr-smartloaded-on-init' } );
                              });
                        } else {
                              self._scheduleLoadingScenarios( params );
                        }
                  });//on flickity ready


                  // Remove the css loader when smartload completed
                  params.sliderEl.on( 'smartload', params.cellSelector , function() {
                        self._maybeRemoveLoader.call( $(this) );
                  });
            },//_smartLoadFlickityImg


            //Invoked in flickity ready callback
            //SCHEDULE LOADING SCENARIOS
            //=> The general idea is to first load the lighest possible page
            //=> we first wait for a user action, click on a slider arrow or scroll.
            //=> then after a longer delay, if none of the 2 first event was trigger, fire the smartload.
            // this last delay has to be long enough to not downgrade the pagespeed test. If set too small, it might add Kbytes weight to the page too early.
            //
            // + 500 ms SELECT flickity event => can be triggered by a user action, or an autoplay
            // As soon as a slide get selected, smart load all imgs of the slider
            //
            //  1) verify that the img is a valid candidate for smartload
            //  2) append the css loader
            //  3) smartload
            //
            //  params = _.extend( {
            //     sliderEl : {},
            //     cellSelector : '.carousel-cell',
            //     scheduleLoading : true
            // }, params );
            _scheduleLoadingScenarios : function( params ) {
                  var self = this;
                  //Flag the slider wrapper with a class when any loading scenario is achieved
                  params.sliderEl.data( 'czr_smartload_scheduled', $.Deferred().done( function() {
                        params.sliderEl.addClass('czr-smartload-scheduled');
                  }) );

                  //When infinite is on, hidden elements are cached, for that the dom is emptied, the remove element are stored as a collection of nodes, and re-appended later
                  //during this process, we lose jQuery reference to DOM elements.
                  //That's why we need this check
                  var _isSliderDataSetup = function() {
                        return 1 <= params.sliderEl.length && ! _.isUndefined( params.sliderEl.data( 'czr_smartload_scheduled' ) );
                  };

                  //React one time on select.flickity
                  params.sliderEl.data( 'czr_schedule_select',
                        $.Deferred( function() {
                              var dfd = this;
                              //@see https://flickity.metafizzy.co/events.html
                              params.sliderEl.parent().one( 'click staticClick.flickity pointerDown.flickity dragMove.flickity', function() {
                                    dfd.resolve();
                              } );

                              // the delay of before listining to select is needed because flickity triggers select on init, which we don't want to listen to
                              // => could be improved probably, but there's no flickity event like 'ready-and-setup'
                              _.delay( function() {
                                    if ( 'pending' == dfd.state() ) {
                                          params.sliderEl.one( 'select.flickity' , function() {
                                                dfd.resolve();
                                          } );
                                    }
                              }, 2000 );
                        }).done( function() {
                              if ( ! _isSliderDataSetup() || 'resolved' == params.sliderEl.data( 'czr_smartload_scheduled' ).state() )
                                return;

                              params.sliderEl.find( params.cellSelector ).each( function() {
                                    self._smartLoadCellImg( { el : $(this), ev : 'czr-smartloaded-on-select' } );
                              });
                              params.sliderEl.data( 'czr_smartload_scheduled').resolve();
                        })
                  );//data( 'czr_schedule_select' )



                  // + 5000 ms SMARTLOAD ON SCROLL
                  // React one time on scroll
                  // Start listining to scroll after a few seconds. Because the event might be trigger in some browser on page load.
                  params.sliderEl.data( 'czr_schedule_scroll_resize',
                        $.Deferred( function() {
                              var dfd = this;
                              _.delay( function() {
                                    czrapp.$_window.one( 'scroll resize', function() {
                                          dfd.resolve();
                                    });
                              }, 5000 );
                        }).done( function() {
                              if ( ! _isSliderDataSetup() || 'resolved' == params.sliderEl.data( 'czr_smartload_scheduled' ).state() )
                                return;

                              params.sliderEl.find( params.cellSelector ).each( function() {
                                    self._smartLoadCellImg( { el : $(this), ev : 'czr-smartloaded-on-scroll' } );
                              });
                              params.sliderEl.data( 'czr_smartload_scheduled').resolve();
                        })
                  );//data( 'czr_schedule_scroll_resize' )



                  // + 10000 ms AUTO SMARTLOAD AFTER A MOMENT
                  //Then schedule the smartloading after a moment.
                  params.sliderEl.data( 'czr_schedule_autoload',
                        $.Deferred( function() {
                              var dfd = this;
                              _.delay( function() { dfd.resolve(); }, 10000 );
                        }).done( function() {
                              if ( ! _isSliderDataSetup() || 'resolved' == params.sliderEl.data( 'czr_smartload_scheduled' ).state() )
                                return;

                              params.sliderEl.find( params.cellSelector ).each( function() {
                                    self._smartLoadCellImg( { el : $(this), ev : 'czr-auto-smartloaded' } );
                              });
                              params.sliderEl.data( 'czr_smartload_scheduled').resolve();
                        })
                  );
            },


            //@params = { el : $(this), ev : 'string' }
            _smartLoadCellImg : function( params ) {
                  params = _.extend( {
                     el : {},
                     ev : 'czr-smartloaded',
                     delay : 0
                  }, params || {} );

                  var _event_ = params.ev,
                      self = this,
                      $_cell = params.el;

                  // First check that the image can be a candidate for smartloading
                  // => has it been parsed server side ?
                  // If so, it should have eithe rthe data-src or the data-smartload attribute ( @see php function czr_fn_regex_callback() )
                  if ( ! ( $_cell instanceof $ ) || 1 > $_cell.find('img[data-src], img[data-smartload]').length )
                    return;

                  // If the image is not yet smartloaded, do it
                  if ( ! $_cell.data( 'czr_smartLoaded' ) ) {
                        //if the loader has not been rendered yet, render it
                        if ( 1 > $_cell.find('.czr-css-loader').length ) {
                              $_cell.append( self._css_loader ).find('.czr-css-loader').fadeIn( 'slow' );
                        }

                        // A delay might be needed when infinite loaded
                        _.delay( function() {
                              $_cell.imgSmartLoad().data( 'czr_smartLoaded', true ).addClass( _event_ );
                        }, params.delay );

                        $_cell.data( 'czr_loader_timer' , $.Deferred( function() {
                              var self = this;
                              _.delay( function() {
                                    self.resolve();
                              }, 2000 );
                              return this.promise();
                        }) );
                        // Make sure the css loader is always removed after a moment. Even if the smartload event has not been triggered
                        $_cell.data( 'czr_loader_timer' ).done( function() {
                              self._maybeRemoveLoader.call( $_cell );
                        });
                  }
            },

            //@return void()
            //this is a $(this) jQuery obj
            _maybeRemoveLoader : function() {
                  if ( ! ( $(this) instanceof $ ) )
                    return;

                  $(this).find('.czr-css-loader').fadeOut( {
                        duration: 'fast',
                        done : function() { $(this).remove();}
                  } );
            },
            //--------------------------------------------------/
            // </IMG SMARTLOAD>
            //--------------------------------------------------/




            /*
            * carousel parallax on flickity ready
            * we parallax only the flickity-viewport, so that we don't parallax the carouasel-dots
            */
            _parallax : function() {
                  var $_parallax_carousel  = $(this),
                        //extrapolate data from the parallax carousel and pass them to the flickity viewport
                        _parallax_data_map = ['parallaxRatio', 'parallaxDirection', 'parallaxOverflowHidden', 'backgroundClass', 'matchMedia'],
                        _parallax_data     = _.object( _.chain(_parallax_data_map).map( function( key ) {
                                                var _data = $_parallax_carousel.data( key );
                                                return _data ? [ key, _data ] : '';
                                          })
                                          .compact()
                                          .value()
                        );

                  $_parallax_carousel.children('.flickity-viewport').czrParallax(_parallax_data);

            },


            //SLIDER ARROW UTILITY
            //@return void()
            _slider_arrows : function ( evt, side ) {

                  evt.preventDefault();
                  var $_this    = $(this),
                      _flickity = $_this.data( 'controls' );

                  if ( ! $_this.length )
                    return;

                  //if not already done, cache the slider this control controls as data-controls attribute
                  if ( ! _flickity ) {
                        _flickity   = $_this.closest('.czr-carousel').find('.flickity-enabled').data('flickity');
                        $_this.data( 'controls', _flickity );
                  }
                  //bail if we still don't have a flickity instance at this point
                  if ( ! _flickity )
                    return;

                  if ( 'previous' == side ) {
                        _flickity.previous();
                  }
                  else if ( 'next' == side ) {
                        _flickity.next();
                  }

            },


            /* Handle carousels nav */
            /*
            * Disable controllers when the first or the latest slide is in the viewport and no wraparound selected
            * when wrapAround //off
            */
            _slider_arrows_enable_toggler: function() {

                  var $_this             = $(this),
                      flkty              = $_this.data('flickity');

                  if ( ! flkty )//maybe not ready
                        return;

                  if ( flkty.options.wrapAround ) {
                        return;
                  }


                  var $_carousel_wrapper = $_this.closest('.czr-carousel'),
                      $_prev             = $_carousel_wrapper.find('.czr-carousel-prev'),
                      $_next             = $_carousel_wrapper.find('.czr-carousel-next');

                  //Reset
                  $_prev.removeClass('disabled');
                  $_next.removeClass('disabled');

                  //selected index is 0, disable prev or
                  //first slide shown but not selected
                  if ( ( 0 === flkty.selectedIndex ) )
                        $_prev.addClass('disabled');

                  //console.log(Math.abs( flkty.slidesWidth + flkty.x ) );
                  //selected index is latest, disable next or
                  //latest slide shown but not selected
                  if ( ( flkty.slides.length - 1 == flkty.selectedIndex ) )
                        $_next.addClass('disabled');

            },
            // for gallery carousels to preserve the dragging we have to move the possible background gallery link inside the flickity viewport
            _move_background_link_inside : function() {
                  var $_flickity_slider = $(this),
                      $_bg_link = $_flickity_slider.closest('.entry-media__wrapper').children('.bg-link');

                  if ( $_bg_link.length > 0 ) {
                        $(this).find( '.flickity-viewport' ).prepend( $_bg_link );
                  }
            }
      };//methods {}

      czrapp.methods.Slider = {};
      $.extend( czrapp.methods.Slider , _methods );

})(jQuery, czrapp );
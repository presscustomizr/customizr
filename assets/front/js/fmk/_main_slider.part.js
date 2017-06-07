var czrapp = czrapp || {};

/************************************************
* ADD SLIDER METHODS
*************************************************/
(function($, czrapp) {
      var _methods = {

            initOnCzrReady : function() {
                  var self = this;

                  /* Flickity ready
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


                  /* Allow parallax */
                  czrapp.$_body.on( 'czr-flickity-ready.flickity', '.czr-parallax-slider', self._parallax );

                  /* Enable page dots on fly (for the main slider only, for the moment, consider to make it dependend to data-flickity-dots)*/
                  czrapp.$_body.on( 'czr-flickity-ready.flickity', '[id^="customizr-slider-main"] .carousel-inner', self._slider_dots );

                  /* Fire fittext */
                  czrapp.$_body.on( 'czr-flickity-ready.flickity', '[id^="customizr-slider-main"] .carousel-inner', function() {
                    $(this).find( '.carousel-caption .czrs-title' ).czrFitText(
                                1.5,//<=kompressor
                                {
                                      maxFontSize : 65,//the default max font-size
                                      minFontSize : 30,
                                }
                    );
                    $(this).find( '.carousel-caption .czrs-subtitle' ).czrFitText(
                                1.5,//<=kompressor
                                {
                                      maxFontSize : 35,//the default max font-size
                                      minFontSize : 20,
                                }
                    );
                    $(this).find( '.carousel-caption .czrs-cta' ).czrFitText(
                                1.5,//<=kompressor
                                {
                                      maxFontSize : 16,//the default max font-size
                                      minFontSize : 14,
                                }
                    );
                  });


                  /* Disable controllers when the first or the latest slide is in the viewport (for the related posts) */
                  czrapp.$_body.on( 'select.flickity', '.czr-carousel .carousel-inner', self._slider_arrows_enable_toggler );

                  /* for gallery carousels to preserve the dragging we have to move the possible background gallery link inside the flickity viewport */
                  czrapp.$_body.on( 'czr-flickity-ready.flickity', '.czr-gallery.czr-carousel .carousel-inner', self._move_background_link_inside );
                  /*Handle custom nav */
                  // previous
                  czrapp.$_body.on( 'click tap prev.czr-carousel', '.czr-carousel-prev', function(e) { self._slider_arrows.apply( this , [ e, 'previous' ] );} );
                  // next
                  czrapp.$_body.on( 'click tap next.czr-carousel', '.czr-carousel-next', function(e) { self._slider_arrows.apply( this , [ e, 'next' ] );} );

            },//_init()



            fireCarousels : function() {
                  //TODO BETTER

                  /* Test only RELATED POSTS !!!!!! */
                  $('.grid-container__square-mini.carousel-inner').flickity({
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


                  /* Test only GALLERY SLIDER IN POST LISTS !!!!!! */
                  $('.czr-gallery.czr-carousel .carousel-inner').flickity({
                      prevNextButtons: false,
                      pageDots: false,
                      wrapAround: true,
                      imagesLoaded: true,
                      setGallerySize: false,
                      cellSelector: '.carousel-cell',
                      accessibility: false,
                      dragThreshold: 10
                  });

                  $('.carousel-inner', '[id^="customizr-slider-main"]').flickity({
                      prevNextButtons: false,
                      pageDots: false,

                      wrapAround: true,
                      imagesLoaded: true,
                      //lazyLoad ?

                      setGallerySize: false,
                      cellSelector: '.carousel-cell',

                      dragThreshold: 10,

                      autoPlay: true, // {Number in milliseconds }

                      accessibility: false,
                  });
            },

            centerMainSlider : function() {
                  //SLIDER IMG
                  setTimeout( function() {

                        //centering per carousel
                        $.each( $( '.carousel-inner', '[id^="customizr-slider-main"]' ) , function() {

                              $( this ).centerImages( {
                                    enableCentering : 1 == czrapp.localized.centerSliderImg,
                                    imgSel : '.carousel-image img',
                                    /* To check settle.flickity is working, it should according to the docs */
                                    oncustom : ['settle.flickity', 'simple_load'],
                                    defaultCSSVal : { width : '100%' , height : 'auto' },
                                    useImgAttr : true,
                                    zeroTopAdjust: 0
                              });

                              //fade out the loading icon per carousel with a little delay
                              //mostly for retina devices (the retina image will be downloaded afterwards
                              //and this may cause the re-centering of the image)
                              var self = this;
                              setTimeout( function() {

                                    $( self ).prevAll('.czr-slider-loader-wrapper').fadeOut();

                              }, 500 );

                        });

                  } , 50);
            },
            /*
            * carousel parallax on flickity ready
            * we parallax only the flickity-viewport, so that we don't parallax the carouasel-dots
            */
            _parallax : function( evt ) {
                var $_parallax_carousel  = $(this),
                  //extrapolate data from the parallax carousel and pass them to the flickity viewport
                      _parallax_data_map = ['parallaxRatio', 'parallaxDirection', 'parallaxOverflowHidden', 'backgroundClass', 'matchMedia'];
                      _parallax_data     = _.object( _.chain(_parallax_data_map).map( function( key ) {
                                                var _data = $_parallax_carousel.data( key );
                                                return _data ? [ key, _data ] : '';
                                          })
                                          .compact()
                                          .value()
                        );

                  $_parallax_carousel.children('.flickity-viewport').czrParallax(_parallax_data);

            },



            //Enable page dots on fly
            _slider_dots : function( evt, _flickity ) {

                  if ( $(evt.target).find('.carousel-cell').length > 1 ) {
                    _flickity.options.pageDots = true;
                    _flickity._createPageDots();
                    _flickity.activatePageDots();
                  }

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
            _slider_arrows_enable_toggler: function( evt ) {

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

            _move_background_link_inside : function( evt ) {

                  var $_flickity_slider = $(this),
                      $_bg_link = $_flickity_slider.closest('.entry-media__wrapper').children('.bg-link');

                  if ( $_bg_link.length > 0 ) {
                        $(this).find( '.flickity-viewport' ).prepend($_bg_link);
                  }
            }
      };//methods {}

      czrapp.methods.Slider = {};
      $.extend( czrapp.methods.Slider , _methods );

})(jQuery, czrapp);
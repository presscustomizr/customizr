var czrapp = czrapp || {};
/***************************
* ADD JQUERY PLUGINS METHODS
****************************/
(function($, czrapp) {
  var _methods = {
    centerImagesWithDelay : function( delay ) {
      var self = this;
      //fire the center images plugin
      //setTimeout( function(){ self.emit('centerImages'); }, delay || 300 );
      setTimeout( function(){ self.emit('centerImages'); }, delay || 100 );
    },

    /**
    * CENTER VARIOUS IMAGES
    * @return {void}
    */
    centerImages : function() {
      //POST CLASSIC GRID IMAGES
      $('.tc-grid-figure, .widget-front .tc-thumbnail').centerImages( {
        enableCentering : CZRParams.centerAllImg,
        oncustom : ['smartload', 'simple_load'],
        zeroTopAdjust: 0,
        enableGoldenRatio : false, //true
        goldenRatioVal : CZRParams.goldenRatio || 1.618,
        goldenRatioLimitHeightTo : CZRParams.gridGoldenRatioLimit || 350
      } );

      $('.js-media-centering.entry-media__holder').centerImages({
        enableCentering : CZRParams.centerAllImg,
        oncustom : ['smartload', 'simple_load'],
        enableGoldenRatio : false, //true
        zeroTopAdjust: 0,
        goldenRatioVal : CZRParams.goldenRatio || 1.618,
        goldenRatioLimitHeightTo : CZRParams.gridGoldenRatioLimit || 350
      });

      //SLIDER IMG + VARIOUS
      setTimeout( function() {
        //centering per slider
        $.each( $( '.czr-carousel .carousel-inner') , function() {
          $( this ).centerImages( {
            enableCentering : 1 == CZRParams.centerSliderImg,
            imgSel : '.carousel-image img',
            /* To check settle.flickity is working, it should according to the docs */
            oncustom : ['settle.flickity', 'simple_load'],
            defaultCSSVal : { width : '100%' , height : 'auto' },
            useImgAttr : true,
            zeroTopAdjust: 0
          });
          //fade out the loading icon per slider with a little delay
          //mostly for retina devices (the retina image will be downloaded afterwards
          //and this may cause the re-centering of the image)
          var self = this;
          setTimeout( function() {
              $( self ).prevAll('.czr-slider-loader-wrapper').fadeOut();
          }, 500 );
        });
      } , 50);
    },//center_images

    parallax : function() {
      //slider parallax
      $( '.czr-parallax-slider' ).czrParallax();

      $( '.parallax-item' ).czrParallax();
      /* Refresh waypoints when mobile menu button is toggled as
      *  the static/relative menu will push the content
      */
      $('.ham__navbar-toggler').on('click', function(){
        setTimeout( function(){
        Waypoint.refreshAll(); }, 400 ); }
      );
    },

    lightbox : function() {
      /* The magnificPopup delegation is very good
      * not even works when clicking on a dynamically added a.expand-img
      * but clicking on an another a.expand-img the image speficied in the
      * dynamically added a.expang-img href is added to the gallery
      */
      $( '[class*="grid-container__"]' ).magnificPopup({
        delegate: 'a.expand-img', // child items selector, by clicking on it popup will open
        type: 'image'
        // other options
      });
      /* galleries in singles Create grouped galleries */
      $( '.post-gallery' ).each(function(){
        $(this).magnificPopup({
          delegate: 'a.expand-img', // child items selector, by clicking on it popup will open
          type: 'image',
          gallery: {
           enabled: true
          }
          // other options
        });
      });
      //TODO: FIND A BETTER SOLUTION
      //in post lists galleries post formats
      czrapp.$_body.on( 'click', '[class*="grid-container__"] .format-gallery .expand-img-gallery', function(e) {
        e.preventDefault();

        $(this).closest('article').magnificPopup({
            delegate: '.gallery-img', // child items selector, by clicking on it popup will open
            type: 'image',
            gallery: {
              enabled: true
            },
        }).magnificPopup('open');
      });
    },



    /*
    * flickity slider:
    */
    czr_slider : function() {
      /* Test only RELATED POSTS !!!!!! */
      $('.grid-container__square-mini').flickity({
          prevNextButtons: false,
          pageDots: false,
          groupCells: "50%",
          imagesLoaded: true,
          cellSelector: '.post',
          cellAlign: 'left',
          dragThreshold: 10,
          accessibility: false,
          contain: true /* allows to not show a blank "cell" when the number of cells is odd but we display an even number of cells per viewport */
      });

      /*
      * Disable controllers when the first or the latest slide is in the viewport
      */
      $('.grid-container__square-mini', '.czr-carousel').on( 'settle.flickity', function( evt ) {
        var $_this             = $(this),
            flkty              = $_this.data('flickity'),
            $_carousel_wrapper = $_this.closest('.czr-carousel'),
            $_prev             = $_carousel_wrapper.find('.slider-prev'),
            $_next             = $_carousel_wrapper.find('.slider-next');

        //Reset
        $_prev.removeClass('disabled');
        $_next.removeClass('disabled');

        //selected index is 0, disable prev or
        //first slide shown but not selected
        if ( ( 0 == flkty.selectedIndex ) || ( Math.abs(flkty.x ) < .1 ) )
          $_prev.addClass('disabled');

        //console.log(Math.abs( flkty.slidesWidth + flkty.x ) );
        //selected index is latest, disable next or
        //latest slide shown but not selected
        if ( ( flkty.slides.length - 1 == flkty.selectedIndex ) || ( Math.abs( flkty.slidesWidth + flkty.x ) < .1 ) )
          $_next.addClass('disabled');

      })

      /* Test only GALLERY SLIDER IN POST LISTS !!!!!! */
      $('[class*="grid-container__"] .format-gallery .carousel-inner').flickity({
          prevNextButtons: false,
          pageDots: false,
          wrapAround: true,
          imagesLoaded: true,
          setGallerySize: false,
          cellSelector: '.carousel-cell',
          accessibility: false,
          dragThreshold: 10
      });

      /* Test only !!!!!! MAIN SLIDER */
      $('.czr-carousel .carousel-inner').flickity({
          prevNextButtons: false,
          pageDots: false,
          wrapAround: true,
          imagesLoaded: true,
          setGallerySize: false,
          cellSelector: '.carousel-cell',
          dragThreshold: 10,
          autoPlay: true,
          /*
          * Set accessibility to false as it produces the following issue:
          * - flickity, when accessibiity is set to true, sets the "carousel" tabindex property
          * - dragging a slide the carousel is focused with focus(), because of the tabindex the page scrolls to top
          * and flickity re-scrolls to the correct position.
          * The scroll to top (due to the focus) for some reason conflicts with the #customizr-slider-* overflow:hidden property
          * when parallaxing.
          * Basically the parallaxed item, despite the top property is set to Y <> 0, appears as it had Y = 0.
          * Plus absoluted elements referring to the #customizr-slider-* seems to be shifted up of -Y
          * very weird behavior to investigate on :/
          */
          accessibility: false,
          draggable: true
      });

      /* Handle custom nav */
      // previous
      czrapp.$_body.on( 'click', '.slider-prev', function(evt) {
        evt.preventDefault();

        var $_this    = $(this),
            _flickity = $_this.data( 'controls' );

        //if not already done, cache the slider this control controls as data-controls attribute
        if ( ! _flickity ) {
          _flickity   = $_this.closest('.czr-carousel').find('.flickity-enabled').data('flickity');
          $_this.data( 'controls', _flickity )
        }

        _flickity.previous();
      });

      // next
      czrapp.$_body.on( 'click', '.slider-next', function(evt) {
        evt.preventDefault();

        var $_this    = $(this),
            _flickity = $_this.data( 'controls' );

        //if not already done, cache the slider this control controls as data-controls attribute
        if ( ! _flickity ) {
          _flickity   = $_this.closest('.czr-carousel').find('.flickity-enabled').data('flickity');
          $_this.data( 'controls', _flickity )
        }

        _flickity.next();
      });
    }
  };//_methods{}

  $.extend( czrapp.methods.Czr_Plugins = {} , _methods );

})(jQuery, czrapp);
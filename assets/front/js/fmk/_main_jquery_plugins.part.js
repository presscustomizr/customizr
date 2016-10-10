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
      $('.tc-grid-figure, .square-grid__mini .entry-image__container ').centerImages( {
        enableCentering : CZRParams.centerAllImg,
        oncustom : ['smartload', 'simple_load'],
        enableGoldenRatio : false, //true
        goldenRatioVal : CZRParams.goldenRatio || 1.618,
        goldenRatioLimitHeightTo : CZRParams.gridGoldenRatioLimit || 350
      } );

      $('.js-media-centering.entry-image__container').centerImages({
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
            useImgAttr : true
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
      $( '.square-grid__mini' ).magnificPopup({
        delegate: 'a.expand-img', // child items selector, by clicking on it popup will open
        type: 'image'
        // other options
      });
      $( '[class*="grid-container__"]' ).magnificPopup({
        delegate: 'a.expand-img:not(.gallery)', // child items selector, by clicking on it popup will open
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
      czrapp.$_body.on( 'click', '[class*="grid-container__"] .format-gallery .expand-img.gallery', function(e) {
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
      $('.square-grid__mini').flickity({
          prevNextButtons: false,
          pageDots: false,
          groupCells: true,
          imagesLoaded: true,
          cellSelector: '.post',
          cellAlign: 'left'
      });

      /* Test only GALLERY SLIDER IN POST LISTS !!!!!! */
      $('[class*="grid-container__"] .format-gallery .carousel-inner').flickity({
          prevNextButtons: false,
          pageDots: false,
          wrapAround: true,
          imagesLoaded: true,
          setGallerySize: false,
          cellSelector: '.carousel-cell'
      });

      /* Test only !!!!!! MAIN SLIDER */
      $('.czr-carousel .carousel-inner').flickity({
          prevNextButtons: false,
          pageDots: false,
          wrapAround: true,
          imagesLoaded: true,
          setGallerySize: false,
          cellSelector: '.carousel-cell'
      });

      /* Handle custom nav */
      // previous
      czrapp.$_body.on( 'click', '.slider-prev', function(evt) {
        evt.preventDefault();
        var $flickity_instance = $(this).closest('.czr-carousel').find('.flickity-enabled');
        $flickity_instance.flickity('previous');
      });
      // next
      czrapp.$_body.on( 'click', '.slider-next', function(evt) {
        evt.preventDefault();
        var $flickity_instance = $(this).closest('.czr-carousel').find('.flickity-enabled');
        $flickity_instance.flickity('next');
      });
    }
  };//_methods{}

  $.extend( czrapp.methods.Czr_Plugins = {} , _methods );

})(jQuery, czrapp);
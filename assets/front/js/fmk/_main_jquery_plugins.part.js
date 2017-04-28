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

    //IMG SMART LOAD
    //.article-container covers all post / page content : single and list
    //__before_main_wrapper covers the single post thumbnail case
    //.widget-front handles the featured pages
    //.post-related-articles handles the related posts
    imgSmartLoad : function() {
      var smartLoadEnabled = 1 == CZRParams.imgSmartLoadEnabled,
          //Default selectors for where are : $( '[class*=grid-container], .article-container', '.__before_main_wrapper', '.widget-front', '.post-related-articles' ).find('img');
          _where           = CZRParams.imgSmartLoadOpts.parentSelectors.join();

      //Smart-Load images
      //imgSmartLoad plugin will trigger the smartload event when the img will be loaded
      //the centerImages plugin will react to this event centering them
      if (  smartLoadEnabled )
        $( _where ).imgSmartLoad(
          _.size( CZRParams.imgSmartLoadOpts.opts ) > 0 ? CZRParams.imgSmartLoadOpts.opts : {}
        );

      //If the centerAllImg is on we have to ensure imgs will be centered when simple loaded,
      //for this purpose we have to trigger the simple-load on:
      //1) imgs which have been excluded from the smartloading if enabled
      //2) all the images in the default 'where' if the smartloading isn't enaled
      //simple-load event on holders needs to be triggered with a certain delay otherwise holders will be misplaced (centering)
      if ( 1 == CZRParams.centerAllImg ) {
        var self                   = this,
            $_to_center            = smartLoadEnabled ?
               $( _.filter( $( _where ).find('img'), function( img ) {
                  return $(img).is(CZRParams.imgSmartLoadOpts.opts.excludeImg.join());
                }) ): //filter
                $( _where ).find('img');
            $_to_center_with_delay = $( _.filter( $_to_center, function( img ) {
                return $(img).hasClass('tc-holder-img');
            }) );

        //imgs to center with delay
        setTimeout( function(){
          self.triggerSimpleLoad( $_to_center_with_delay );
        }, 300 );
        //all other imgs to center
        self.triggerSimpleLoad( $_to_center );
      }
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

      $('.js-centering.entry-media__holder').centerImages({
        enableCentering : CZRParams.centerAllImg,
        oncustom : ['smartload', 'simple_load'],
        enableGoldenRatio : false, //true
        zeroTopAdjust: 0,
        goldenRatioVal : CZRParams.goldenRatio || 1.618,
        goldenRatioLimitHeightTo : CZRParams.gridGoldenRatioLimit || 350
      });

      //SLIDER IMG + VARIOUS
      setTimeout( function() {
        //centering per carousel
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
          //fade out the loading icon per carousel with a little delay
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
      /*
      * carousel parallax on flickity ready
      * we parallax only the flickity-viewport, so that we don't parallax the carouasel-dots
      */
      czrapp.$_body.on( 'czr-flickity-ready.flickity', '.czr-parallax-slider', function( evt ) {
            var $_parallax_carousel  = $(evt.target),
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
      });

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
      var _arrowMarkup = '<span class="czr-carousel-control btn btn-skin-darkest-shaded mfp-arrow-%dir% icn-%dir%-open-big"></span>';

      /* The magnificPopup delegation is very good
      * it works when clicking on a dynamically added a.expand-img
      * but also when clicking on an another a.expand-img the image speficified in the
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
          delegate: '.expand-img', // child items selector, by clicking on it popup will open
          type: 'image',
          gallery: {
           enabled: true,
           arrowMarkup: _arrowMarkup
          }
          // other options
        });
      });
      /*
      * in singles when former tc_fancybox enabled
      */
      $('#content').magnificPopup({
        delegate: '.expand-img-grouped',
        type: 'image',
        gallery: {
         enabled: true,
         arrowMarkup: _arrowMarkup
        }
      });
      //TODO: FIND A BETTER SOLUTION
      //in post lists galleries post formats
      czrapp.$_body.on( 'click', '[class*="grid-container__"] .expand-img-gallery', function(e) {
        e.preventDefault();

        $(this).closest('article').magnificPopup({
            delegate: '.gallery-img', // child items selector, by clicking on it popup will open
            type: 'image',
            gallery: {
              enabled: true,
              arrowMarkup: _arrowMarkup
            },
        }).magnificPopup('open');
      });
    },



    /*
    * flickity carousel:
    */
    czrCarousels : function() {
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

      /* Disable controllers when the first or the latest slide is in the viewport */
      czrapp.$_body.on( 'select.flickity', '.czr-carousel .carousel-inner', czr_controls_disabling );
      /*Handle custom nav */
      // previous
      czrapp.$_body.on( 'click tap prev.czr-carousel', '.czr-carousel-prev', carousel_previous );
      // next
      czrapp.$_body.on( 'click tap next.czr-carousel', '.czr-carousel-next', carousel_next );


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

      /* Test only !!!!!! MAIN SLIDER */

      //Enable page dots on fly
      czrapp.$_body.on( 'czr-flickity-ready.flickity', '[id^="customizr-slider-main"] .carousel-inner', function( evt, _flickity ) {
        if ( $(this).find('.carousel-cell').length > 1 ) {
          _flickity.options.pageDots = true;
          _flickity._createPageDots();
          _flickity.activatePageDots();
        }
      });


      $('.carousel-inner', '[id^="customizr-slider-main"]').flickity({
          prevNextButtons: false,
          pageDots: false,
          /*
          * From flickity docs
          * At the end of cells, wrap-around to the other end for infinite scrolling.
          */
          wrapAround: true,
          imagesLoaded: true,
          //lazyLoad ?
          /*
          * From flickity docs
          * Sets the height of the carousel to the height of the tallest cell. Enabled by default setGallerySize: true.
          */
          setGallerySize: false,
          cellSelector: '.carousel-cell',
          /*
          * From flickity docs
          * The number of pixels a mouse or touch has to move before dragging begins.
          * Increase dragThreshold to allow for more wiggle room for vertical page scrolling on touch devices.
          * Default dragThreshold: 3.
          */
          dragThreshold: 10,
          /*
          * From flickity docs
          * Auto-playing will pause when mouse is hovered over,
          * and resume when mouse is hovered off. Auto-playing will stop when
          * the carousel is clicked or a cell is selected.
          */
          autoPlay: true, // {Number in milliseconds }
          /*
          * Set accessibility to false as it produces the following issue:
          * - flickity, when accessibiity is set to true, sets the "carousel" tabindex property
          * - dragging a slide the carousel is focused with focus(), because of the tabindex the page scrolls to top
          * and flickity re-scrolls to the correct position.
          * The scroll to top (due to the focus) for some reason conflicts with the #customizr-carousel-* overflow:hidden property
          * when parallaxing.
          * Basically the parallaxed item, despite the top property is set to Y <> 0, appears as it had Y = 0.
          * Plus absoluted elements referring to the #customizr-carousel-* seems to be shifted up of -Y
          * very weird behavior to investigate on :/
          */
          accessibility: false,
      });

      /* Handle carousels nav */
      /*
      * Disable controllers when the first or the latest slide is in the viewport and no wraparound selected
      * when wrapAround //off
      */
      function czr_controls_disabling(evt) {
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
        if ( ( 0 == flkty.selectedIndex ) )
          $_prev.addClass('disabled');

        //console.log(Math.abs( flkty.slidesWidth + flkty.x ) );
        //selected index is latest, disable next or
        //latest slide shown but not selected
        if ( ( flkty.slides.length - 1 == flkty.selectedIndex ) )
          $_next.addClass('disabled');

      };

      /*Handle custom nav */
      // previous
      function carousel_previous(evt) {
        evt.preventDefault();

        var $_this    = $(this),
            _flickity = $_this.data( 'controls' );

        //if not already done, cache the carousel this control controls as data-controls attribute
        if ( ! _flickity ) {
          _flickity   = $_this.closest('.czr-carousel').find('.flickity-enabled').data('flickity');
          $_this.data( 'controls', _flickity )
        }

        _flickity.previous();
      };

      // next
      function carousel_next(evt) {
        //evt.preventDefault();

        var $_this    = $(this),
            _flickity = $_this.data( 'controls' );

        //if not already done, cache the carousel this control controls as data-controls attribute
        if ( ! _flickity ) {
          _flickity   = $_this.closest('.czr-carousel').find('.flickity-enabled').data('flickity');
          $_this.data( 'controls', _flickity )
        }

        _flickity.next();
      };
    }
  };//_methods{}

  $.extend( czrapp.methods.Czr_Plugins = {} , _methods );

})(jQuery, czrapp);
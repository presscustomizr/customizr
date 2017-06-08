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
      var smartLoadEnabled = 1 == czrapp.localized.imgSmartLoadEnabled,
          //Default selectors for where are : $( '[class*=grid-container], .article-container', '.__before_main_wrapper', '.widget-front', '.post-related-articles' ).find('img');
          _where           = czrapp.localized.imgSmartLoadOpts.parentSelectors.join();

      //Smart-Load images
      //imgSmartLoad plugin will trigger the smartload event when the img will be loaded
      //the centerImages plugin will react to this event centering them
      if (  smartLoadEnabled )
        $( _where ).imgSmartLoad(
          _.size( czrapp.localized.imgSmartLoadOpts.opts ) > 0 ? czrapp.localized.imgSmartLoadOpts.opts : {}
        );

      //If the centerAllImg is on we have to ensure imgs will be centered when simple loaded,
      //for this purpose we have to trigger the simple-load on:
      //1) imgs which have been excluded from the smartloading if enabled
      //2) all the images in the default 'where' if the smartloading isn't enaled
      //simple-load event on holders needs to be triggered with a certain delay otherwise holders will be misplaced (centering)
      if ( 1 == czrapp.localized.centerAllImg ) {
        var self                   = this,
            $_to_center            = smartLoadEnabled ?
               $( _.filter( $( _where ).find('img'), function( img ) {
                  return $(img).is(czrapp.localized.imgSmartLoadOpts.opts.excludeImg.join());
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
        enableCentering : czrapp.localized.centerAllImg,
        oncustom : ['smartload', 'refresh-height', 'simple_load'],
        zeroTopAdjust: 0,
        enableGoldenRatio : false,
      } );

      $('.js-centering.entry-media__holder, .js-centering.entry-media__wrapper').centerImages({
        enableCentering : 1,
        oncustom : ['smartload', 'refresh-height', 'simple_load'],
        enableGoldenRatio : false, //true
        zeroTopAdjust: 0,
        setOpacityWhenCentered : true,//will set the opacity to 1
        opacity : 1
      });


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

    lightBox : function() {
      var _arrowMarkup = '<span class="czr-carousel-control btn btn-skin-dark-shaded inverted mfp-arrow-%dir% icn-%dir%-open-big"></span>';

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
      $( '.czr-gallery' ).each(function(){
        $(this).magnificPopup({
          delegate: '[data-lb-type="grouped-gallery"]', // child items selector, by clicking on it popup will open
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
      $('article .tc-content-inner').magnificPopup({
        delegate: '[data-lb-type="grouped-post"]',
        type: 'image',
        gallery: {
         enabled: true,
         arrowMarkup: _arrowMarkup
        }
      });

      //in post lists galleries post formats
      //only one button for each gallery
      czrapp.$_body.on( 'click', '[class*="grid-container__"] .expand-img-gallery', function(e) {
            e.preventDefault();

            var $_expand_btn    = $( this ),
                $_gallery_crsl  = $_expand_btn.closest( '.czr-carousel' );

              if ( $_gallery_crsl.length > 0 ) {

                  if ( ! $_gallery_crsl.data( 'mfp' ) ) {
                        $_gallery_crsl.magnificPopup({
                            delegate: '.gallery-img',
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
                        $_gallery_crsl.find( '.is-selected .gallery-img' ).trigger('click');
                  }

            }//endif
      });
    },

  };//_methods{}

  czrapp.methods.JQPlugins = {};
  $.extend( czrapp.methods.JQPlugins , _methods );


})(jQuery, czrapp);
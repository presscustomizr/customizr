var czrapp = czrapp || {};
/***************************
* ADD JQUERY PLUGINS METHODS
****************************/
(function($, czrapp) {
  var _methods = {
    centerImagesWithDelay : function( delay ) {
      var self = this;
      //fire the center images plugin
      setTimeout( function(){ self.emit('centerImages'); }, delay || 300 );
    },


    //IMG SMART LOAD
    //.article-container covers all post / page content : single and list
    //__before_main_wrapper covers the single post thumbnail case
    //.widget-front handles the featured pages
    imgSmartLoad : function() {
      var smartLoadEnabled = 1 == TCParams.imgSmartLoadEnabled,
          //Default selectors for where are : $( '.article-container, .__before_main_wrapper, .widget-front' ).find('img');
          _where           = TCParams.imgSmartLoadOpts.parentSelectors.join();

      //Smart-Load images
      //imgSmartLoad plugin will trigger the smartload event when the img will be loaded
      //the centerImages plugin will react to this event centering them
      if (  smartLoadEnabled )
        $( _where ).imgSmartLoad(
          _.size( TCParams.imgSmartLoadOpts.opts ) > 0 ? TCParams.imgSmartLoadOpts.opts : {}
        );
    
      //If the centerAllImg is on we have to ensure imgs will be centered when simple loaded,
      //for this purpose we have to trigger the simple-load on:
      //1) imgs which have been excluded from the smartloading if enabled
      //2) all the images in the default 'where' if the smartloading isn't enaled
      //simple-load event on holders needs to be triggered with a certain delay otherwise holders will be misplaced (centering)
      if ( 1 == TCParams.centerAllImg ) {
        var self                   = this,
            $_to_center            = smartLoadEnabled ? 
               $( _.filter( $( _where ).find('img'), function( img ) {
                  return $(img).is(TCParams.imgSmartLoadOpts.opts.excludeImg.join());
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


    //FIRE DROP CAP PLUGIN
    dropCaps : function() {
      if ( ! TCParams.dropcapEnabled || ! _.isObject( TCParams.dropcapWhere ) )
        return;

      $.each( TCParams.dropcapWhere , function( ind, val ) {
        if ( 1 == val ) {
          $( '.entry-content' , 'body.' + ( 'page' == ind ? 'page' : 'single-post' ) ).children().first().addDropCap( {
            minwords : TCParams.dropcapMinWords,//@todo check if number
            skipSelectors : _.isObject(TCParams.dropcapSkipSelectors) ? TCParams.dropcapSkipSelectors : {}
          });
        }
      });//each
    },


    //FIRE EXT LINKS PLUGIN
    //May be add (check if activated by user) external class + target="_blank" to relevant links
    //images are excluded by default
    //links inside post/page content
    extLinks : function() {
      if ( ! TCParams.extLinksStyle && ! TCParams.extLinksTargetExt )
        return;
      $('a' , '.entry-content').extLinks({
        addIcon : TCParams.extLinksStyle,
        newTab : TCParams.extLinksTargetExt,
        skipSelectors : _.isObject(TCParams.extLinksSkipSelectors) ? TCParams.extLinksSkipSelectors : {}
      });
    },

    //FIRE FANCYBOX PLUGIN
    //Fancybox with localized script variables
    fancyBox : function() {
      if ( 1 != TCParams.FancyBoxState || 'function' != typeof($.fn.fancybox) )
        return;

      $("a.grouped_elements").fancybox({
        transitionOut: "elastic",
        transitionIn: "elastic",
        speedIn: 200,
        speedOut: 200,
        overlayShow: !1,
        autoScale: 1 == TCParams.FancyBoxAutoscale ? "true" : "false",
        changeFade: "fast",
        enableEscapeButton: !0
      });

      //replace title by img alt field
      $('a[rel*=tc-fancybox-group]').each( function() {
        var title = $(this).find('img').prop('title');
        var alt = $(this).find('img').prop('alt');
        if (typeof title !== 'undefined' && 0 !== title.length)
          $(this).attr('title',title);
        else if (typeof alt !== 'undefined' &&  0 !== alt.length)
          $(this).attr('title',alt);
      });
    },


    /**
    * CENTER VARIOUS IMAGES
    * @return {void}
    */
    centerImages : function() {
      //SLIDER IMG + VARIOUS
      setTimeout( function() {
        //centering per slider
        $.each( $( '.carousel .carousel-inner') , function() {  
          $( this ).centerImages( {
            enableCentering : 1 == TCParams.centerSliderImg,
            imgSel : '.item .carousel-image img',
            oncustom : ['slid', 'simple_load'],
            defaultCSSVal : { width : '100%' , height : 'auto' },
            useImgAttr : true
          });
          //fade out the loading icon per slider with a little delay
          //mostly for retina devices (the retina image will be downloaded afterwards
          //and this may cause the re-centering of the image)
          var self = this;
          setTimeout( function() {
              $( self ).prevAll('.tc-slider-loader-wrapper').fadeOut();
          }, 500 );
        });  
      } , 50);

      //Featured Pages
      $('.widget-front .thumb-wrapper').centerImages( {
        enableCentering : 1 == TCParams.centerAllImg,
        enableGoldenRatio : false,
        disableGRUnder : 0,//<= don't disable golden ratio when responsive
        zeroTopAdjust : 1,
        leftAdjust : 2.5,
        oncustom : ['smartload', 'simple_load']
      });
      //POST LIST THUMBNAILS + FEATURED PAGES
      //Squared, rounded
      $('.thumb-wrapper', '.hentry' ).centerImages( {
        enableCentering : 1 == TCParams.centerAllImg,
        enableGoldenRatio : false,
        disableGRUnder : 0,//<= don't disable golden ratio when responsive
        oncustom : ['smartload', 'simple_load']
      });

      //rectangulars
      $('.tc-rectangular-thumb').centerImages( {
        enableCentering : 1 == TCParams.centerAllImg,
        enableGoldenRatio : true,
        goldenRatioVal : TCParams.goldenRatio || 1.618,
        disableGRUnder : 0,//<= don't disable golden ratio when responsive
        oncustom : ['smartload', 'refresh-height', 'simple_load'] //bind 'refresh-height' event (triggered to the the customizer preview frame)
      });

      //SINGLE POST THUMBNAILS
      $('.tc-rectangular-thumb' , '.single').centerImages( {
        enableCentering : 1 == TCParams.centerAllImg,
        enableGoldenRatio : false,
        disableGRUnder : 0,//<= don't disable golden ratio when responsive
        oncustom : ['smartload', 'refresh-height', 'simple_load'] //bind 'refresh-height' event (triggered to the the customizer preview frame)
      });

      //POST GRID IMAGES
      $('.tc-grid-figure').centerImages( {
        enableCentering : 1 == TCParams.centerAllImg,
        oncustom : ['smartload', 'simple_load'],
        enableGoldenRatio : true,
        goldenRatioVal : TCParams.goldenRatio || 1.618,
        goldenRatioLimitHeightTo : TCParams.gridGoldenRatioLimit || 350
      } );
    },//center_images

  };//_methods{}

  $.extend( czrapp.methods.Czr_Plugins = {} , _methods );

})(jQuery, czrapp);

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
          _where           = TCParams.imgSmartLoadOpts.parentSelectors.join(),
          $_to_center      = $( _where ).find('img');

      if ( smartLoadEnabled ) {
        $_to_center      = _.filter( $_to_center , function ( img ) {
          return $(img).is(TCParams.imgSmartLoadOpts.opts.excludeImg.join());
        } );//filter
      }//if

      if (  smartLoadEnabled )
        $( _where ).imgSmartLoad(
          _.size( TCParams.imgSmartLoadOpts.opts ) > 0 ? TCParams.imgSmartLoadOpts.opts : {}
        );
    
      //simple-load event on holders needs to be needs to be triggered with a certain delay otherwise holders will be misplaced (centering)
      if ( 1 == TCParams.centerAllImg ) {
        var self = this;
        setTimeout( function(){ 
            self.triggerSimpleLoad( _.filter( $_to_center, function( img ) { 
                return $(img).hasClass('tc-holder-img'); 
            } ) ); }, 
            300 );
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
        $( '.carousel .carousel-inner').centerImages( {
          enableCentering : 1 == TCParams.centerSliderImg,
          imgSel : '.item .carousel-image img',
          oncustom : ['slid', 'simple_load'],
          defaultCSSVal : { width : '100%' , height : 'auto' },
          useImgAttr : true
        });
        $('.tc-slider-loader-wrapper').hide();
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

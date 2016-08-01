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
      //POST GRID IMAGES
      $('.tc-grid-figure').centerImages( {
        enableCentering : TCParams.centerAllImg,
        oncustom : ['smartload', 'simple_load'],
        enableGoldenRatio : false, //true
        goldenRatioVal : TCParams.goldenRatio || 1.618,
        goldenRatioLimitHeightTo : TCParams.gridGoldenRatioLimit || 350
      } );
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

    lightbox : function() {
      /* The magnificPopup delegation is very good
      * not even works when clicking on a dynamically added a.expand-img
      * but clicking on an another a.expand-img the image speficied in the 
      * dynamically added a.expang-img href is added to the gallery
      */
      $( '[class*="grid-container__"]' ).magnificPopup({
        delegate: 'a.expand-img', // child items selector, by clicking on it popup will open
        gallery: {
          enabled: true
        },
        type: 'image'
        // other options
      });
    }
  };//_methods{}

  $.extend( czrapp.methods.Czr_Plugins = {} , _methods );

})(jQuery, czrapp);

var czrapp = czrapp || {};

(function($, czrapp) {
  var _methods =  {

    init : function() {

    },
    //VARIOUS HOVER ACTION
    variousHoverActions : function() {
      /* Temporary */
      $( '.grid-container__alternate' ).on( 'mouseenter mouseleave', '.entry-image__container', _toggleParentHover );
      $( '.grid-container__masonry, grid-container__classic').on( 'mouseenter mouseleave', '.grid-item', _toggleThisHover );
        
      function _toggleParentHover() {
        $(this).closest('article').toggleClass("hover");
      };

      function _toggleThisHover() {
        $(this).toggleClass("hover");
      }

      $(".widget li").hover(function () {
          $(this).addClass("on");
      }, function () {
          $(this).removeClass("on");
      });
    },

    //SMOOTH SCROLL
    smoothScroll: function() {
      if ( TCParams.SmoothScroll && TCParams.SmoothScroll.Enabled )
        smoothScroll( TCParams.SmoothScroll.Options );
    }
  };//_methods{}

  czrapp.methods.Czr_UserExperience = {};
  $.extend( czrapp.methods.Czr_UserExperience , _methods );  
})(jQuery, czrapp);

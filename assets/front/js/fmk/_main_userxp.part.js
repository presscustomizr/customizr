var czrapp = czrapp || {};

(function($, czrapp) {
  var _methods =  {

    init : function() {

    },
    //VARIOUS HOVER ACTION
    variousHoverActions : function() {
      /* Temporary */
      $('.entry-image__container').hover(function () {
          $(this).closest('article').addClass("hover");
      }, function () {
          $(this).closest('article').removeClass("hover");
      });

      $('article.grid-item').hover(function () {
          $(this).addClass("hover");
      }, function () {
          $(this).removeClass("hover");
      });

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
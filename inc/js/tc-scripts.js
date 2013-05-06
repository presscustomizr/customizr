/* ==========================================================
 * 
 * ========================================================== */

jQuery(document).ready(function($) {
!function ($) {

  "use strict"; // jshint ;_;

  $(window).on('load', function () {
    /* Add hover class on front widgets
    * ======
    ======== */
      $(".widget-front, article").hover(
        function () {
          $(this).addClass('hover');
        },
        function () {
          $(this).removeClass('hover');
        });


     /* Detect layout and reorder content divs
      * ============== */
        var $window = $(window);

        function checkWidth() {
        var windowsize = $window.width();
        if (windowsize < 767) {
            //if the window is smaller than 440px wide then turn
            $("#main-wrapper .container .span3").insertAfter("#main-wrapper .container .article-container");
          }
          else {
            $("#main-wrapper .container .span3.left").insertBefore("#main-wrapper .container .article-container");
            $("#main-wrapper .container .span3.right").insertAfter("#main-wrapper .container .article-container");
          }
        }
         // Execute on load
        checkWidth();
         // Bind event listener
        $(window).resize(checkWidth);

        //arrows bullet list effect
        $('.widget li').hover(function() {
          $(this).addClass("on");
        }, function() {
        $(this).removeClass("on");
      });
    })
    

}(window.jQuery);

});
/* ==========================================================
 * 
 * ========================================================== */

jQuery(document).ready(function($) {
!function ($) {

  "use strict"; // jshint ;_;

  $(window).on('load', function () {
      // Fancybox
      $("a[rel^='tc-fancybox']").fancybox({
        padding: 0,

        openEffect : 'elastic',
        openSpeed  : 150,

        closeEffect : 'elastic',
        closeSpeed  : 150,

        closeClick : true,

        helpers : {
          overlay : null
        }
      });
    })

}(window.jQuery);

});
/* ==========================================================
 * 
 * ========================================================== */

jQuery(document).ready(function( $) {
!function ( $) {

  $(window).on( 'load' , function () {
      // Fancybox
      $("a.grouped_elements").fancybox({
        'transitionIn'  : 'elastic' ,
        'transitionOut' : 'elastic' ,
        'speedIn'   : 200, 
        'speedOut'    : 200, 
        'overlayShow' : false,
        'autoScale' : false,
        'changeFade' : 'fast',
        'enableEscapeButton' : true
      });
    })

}(window.jQuery);

});
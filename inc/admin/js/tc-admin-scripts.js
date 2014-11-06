/**
 * Some admin scripts
 *
 * @package Customizr
 * @since Customizr 1.0
 */

jQuery(document).ready(function( $) {
!function ( $) {

 /* Add hover class on front widgets
  * ============== */
  $(window).on( 'load' , function () {
     /*
      * first example - defaults
      */
      $( '.iphonecheck' ).iCheckbox();
      $( '.iphonecheck' ).change(function(e){
        if ( $( '.iphonecheck' ).attr( 'checked' ) == true ) {
          console.log( 'checkbox one checked: '+$(e.target).attr( 'checked' ));
        }
      });
      })
  }(window.jQuery);

});
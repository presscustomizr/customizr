//DOM READY :
//1) FIRE SPECIFIC INPUT PLUGINS
//2) ADD SOME COOL STUFFS
jQuery(function ($) {

  /* RECENTER CURRENT SECTIONS */
  $('.accordion-section').not('.control-panel').click( function () {
    _recenter_current_section($(this));
  });

  function _recenter_current_section( section ) {
    var $siblings               = section.siblings( '.open' );
    //check if clicked element is above or below sibling with offset.top
    if ( 0 !== $siblings.length &&  $siblings.offset().top < 0 ) {
      $('.wp-full-overlay-sidebar-content').animate({
            scrollTop:  - $('#customize-theme-controls').offset().top - $siblings.height() + section.offset().top + $('.wp-full-overlay-sidebar-content').offset().top
      }, 700);
    }
  }//end of fn

  /* ADD GOOGLE IN TITLE */
  $g_logo = $('<img>' , {class : 'tc-title-google-logo' , src : 'http://www.google.com/images/logos/google_logo_41.png' , height : 20 });
  $('#accordion-section-tc_fonts').prepend($g_logo);

});

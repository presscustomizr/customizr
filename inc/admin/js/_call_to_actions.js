/**
 * Call to actions
 * @package Customizr
 * @since Customizr 3.2.9
 */
(function ( _, $) {

  /* CONTRIBUTION TO CUSTOMIZR */
  if (  ! TCControlParams.HideDonate && 'customizr-pro' != TCControlParams.themeName )
    donate_block();

  function donate_block() {
    var html  = '',
        trans = TCControlParams.translations.donate;

    html += '  <div id="tc-donate-customizer">';
    html += '    <span class="tc-close-request button">X</span>';
    html += '    <h3>' + trans.hi + ' <a href="https://twitter.com/nicguillaume" target="_blank">Nicolas</a>' + trans.developer + ' :).</h3>';
    html += '    <span class="tc-notice"> ' + trans.support_message + '</span>';
    html += '      <a class="tc-donate-link" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=8CTH6YFDBQYGU" target="_blank" rel="nofollow">';
    html += '        <img src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" alt="' + trans.donate_img_alt + '">';
    html += '      </a>';
    html += '     <div class="donate-alert">';
    html += '       <p class="tc-notice">' + trans.alert_message + '</p>';
    html += '       <span class="tc-hide-donate button">' + trans.hide_forever + '</span>';
    html += '       <span class="tc-cancel-hide-donate button">' + trans.think_twice + '</span>';
    html += '     </div>';
    html += '  </div>';

    $('#customize-info').append( html );

     //BIND EVENTS
    $('.tc-close-request').click( function(e) {
      $('.donate-alert').slideToggle("fast");
      $(this).hide();
    });

    $('.tc-hide-donate').click( function(e) {
      DoAjaxSave();
      setTimeout(function(){
          $('#tc-donate-customizer').slideToggle("fast");
      }, 200);
    });

    $('.tc-cancel-hide-donate').click( function(e) {
      $('.donate-alert').slideToggle("fast");
      setTimeout(function(){
          $('.tc-close-request').show();
      }, 200);
    });
  }//end of donate block


  function DoAjaxSave() {
      var AjaxUrl         = TCControlParams.AjaxUrl,
      query = {
          action  : 'hide_donate',
          TCnonce :  TCControlParams.TCNonce
      },
      request = $.post( AjaxUrl, query );
      request.done( function( response ) {
          // Check if the user is logged out.
          if ( '0' === response ) {
              return;
          }
          // Check for cheaters.
          if ( '-1' === response ) {
              return;
          }
      });
  }//end of function

})( wp, jQuery );

/**
 * Theme Customizer enhancements for a better user experience.
 * @package Customizr
 * @since Customizr 1.0
 */

(function ($) {
  var api = wp.customize;

  $.each({
    'tc_theme_options[tc_show_featured_pages]': {
      controls: TCControlParams.FPControls,
      callback: function (to) {
        return '1' == to
      }
    },
    'tc_theme_options[tc_front_slider]': {
      controls: [
        'tc_theme_options[tc_slider_width]',
        'tc_theme_options[tc_slider_delay]'
      ],
      callback: function (to) {
        return '0' !== to
      }
    }
  }, function (settingId, o) {
    api(settingId, function (setting) {
      $.each(o.controls, function (i, controlId) {
        api.control(controlId, function (control) {
          var visibility = function (to) {
            control.container.toggle(o.callback(to));
          };
          visibility(setting.get());
          setting.bind(visibility);
        });
      });
    });
  });
  
  /* Contribution to Customizer */
  if ( TCControlParams.HideDonate )
    return;

    var html = '';
    html += '  <div id="tc-donate-customizer">';
    html += '    <span class="tc-close-request button">X</span>';           
    html += '    <h3>We do our best do make Customizr the perfect free theme for you!</h3>';
    html += '    <span class="tc-notice"> Please help support it\'s continued development with a donation of $20, $50, or even $100.</span>';
    html += '      <a class="tc-donate-link" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=8CTH6YFDBQYGU" target="_blank" rel="nofollow">';
    html += '        <img src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" alt="Make a donation for Customizr">';
    html += '      </a>';
    html += '     <div class="donate-alert">';
    html += '       <p class="tc-notice">Once clicked the "Hide forever" button, this donation block will not be displayed anymore.<br/>Either you are using Customizr for personal or business purposes, any kind of sponsorship will be appreciated to support this free theme.<br/><strong>Already donator? Thanks, you rock!<br/><br/> Live long and prosper with Customizr!</strong></p>';
    html += '       <span class="tc-hide-donate button">Hide forever</span>';
    html += '       <span class="tc-cancel-hide-donate button">Let me think twice</span>';
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

    function  DoAjaxSave() {
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

})(jQuery);

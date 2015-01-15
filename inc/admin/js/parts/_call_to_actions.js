/**
 * Call to actions
 */
jQuery(function ($) {

  /* CONTRIBUTION TO CUSTOMIZR */
  var donate_displayed  = false,
      is_pro            = 'customizr-pro' == TCControlParams.themeName;
  if ( is_pro )
    return;

  if (  ! TCControlParams.HideDonate ) {
    _render_donate_block();
    donate_displayed = true;
  }

  //Main call to action
  if ( TCControlParams.ShowCTA && ! donate_displayed ) {
   _render_main_cta();
  }

  //In controls call to action
  _render_wfc_cta();
  _render_fpu_cta();

  function _render_donate_block() {
    // Grab the HTML out of our template tag and pre-compile it.
    var donate_template = _.template(
        $( "script#donate_template" ).html()
    );

    $('#customize-info').after( donate_template() );

     //BIND EVENTS
    $('.tc-close-request').click( function(e) {
      $('.donate-alert').slideToggle("fast");
      $(this).hide();
    });

    $('.tc-hide-donate').click( function(e) {
      _ajax_save();
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


  function _render_main_cta() {
    // Grab the HTML out of our template tag and pre-compile it.
    var main_cta = _.template(
        $( "script#main_cta" ).html()
    );
    $('#customize-info').after( main_cta() );
  }

  function _render_wfc_cta() {
    // Grab the HTML out of our template tag and pre-compile it.
    var wfc_cta = _.template(
        $( "script#wfc_cta" ).html()
    );
    $('li[id*="tc_body_font_size"]').append( wfc_cta() );
  }

  function _render_fpu_cta() {
    // Grab the HTML out of our template tag and pre-compile it.
    var fpu_cta = _.template(
        $( "script#fpu_cta" ).html()
    );
    $('li[id*="tc_featured_text_three"]').append( fpu_cta() );
  }

  function _ajax_save() {
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
});

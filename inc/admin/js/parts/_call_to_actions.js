/**
 * Call to actions
 */
jQuery(function ($) {

  /* CONTRIBUTION TO CUSTOMIZR */
  var donate_displayed  = false,
      is_pro            = 'customizr-pro' == TCControlParams.themeName;
  if (  ! TCControlParams.HideDonate && ! is_pro ) {
    _render_donate_block();
    donate_displayed = true;
  }

  //Main call to action
  if ( TCControlParams.ShowCTA && ! donate_displayed && ! is_pro ) {
   _render_main_cta();
  }

  //In controls call to action
  if ( ! is_pro ) {
    _render_wfc_cta();
    _render_fpu_cta();
    _render_footer_cta();
    _render_gc_cta();
    _render_mc_cta();
  }
  _render_rate_czr();

  function _render_rate_czr() {
    var _cta = _.template(
        $( "script#rate-czr" ).html()
    );
    $('#customize-footer-actions').append( _cta() );
  }

  function _render_donate_block() {
    // Grab the HTML out of our template tag and pre-compile it.
    var donate_template = _.template(
        $( "script#donate_template" ).html()
    );

    $('#customize-info').after( donate_template() );

     //BIND EVENTS
    $('.tc-close-request').click( function(e) {
      e.preventDefault();
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
    var _cta = _.template(
        $( "script#main_cta" ).html()
    );
    $('#customize-info').after( _cta() );
  }

  function _render_wfc_cta() {
    // Grab the HTML out of our template tag and pre-compile it.
    var _cta = _.template(
        $( "script#wfc_cta" ).html()
    );
    $('li[id*="tc_body_font_size"]').append( _cta() );
  }

  function _render_fpu_cta() {
    // Grab the HTML out of our template tag and pre-compile it.
    var _cta = _.template(
        $( "script#fpu_cta" ).html()
    );
    $('li[id*="tc_featured_text_three"]').append( _cta() );
  }

  function _render_gc_cta() {
    // Grab the HTML out of our template tag and pre-compile it.
    var _cta = _.template(
        $( "script#gc_cta" ).html()
    );
    $('li[id*="tc_post_list_show_thumb"] > .tc-customizr-title').before( _cta() );
  }

  function _render_mc_cta() {
    // Grab the HTML out of our template tag and pre-compile it.
    var _cta = _.template(
        $( "script#mc_cta" ).html()
    );
    $('li[id*="tc_theme_options-tc_display_menu_label"]').append( _cta() );
  }

  function _render_footer_cta() {
    // Grab the HTML out of our template tag and pre-compile it.
    var _cta = _.template(
        $( "script#footer_cta" ).html()
    );
    $('li[id*="tc_show_back_to_top"]').append( _cta() );
  }

  function _ajax_save() {
      var AjaxUrl         = TCControlParams.AjaxUrl,
      query = {
          action  : 'hide_donate',
          TCnonce :  TCControlParams.TCNonce,
          wp_customize : 'on'
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

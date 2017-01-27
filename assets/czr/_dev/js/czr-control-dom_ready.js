(function (wp, $) {
        $( function($) {
                /* GRID */
                var _build_control_id = function( _control ) {
                  return [ '#' , 'customize-control-tc_theme_options-', _control ].join('');
                };

                var _get_grid_design_controls = function() {
                  return $( serverControlParams.gridDesignControls.map( function( _control ) {
                    return _build_control_id( _control );
                  }).join(',') );
                };

                //hide design controls on load
                $( _get_grid_design_controls() ).addClass('tc-grid-design').hide();

                $('.tc-grid-toggle-controls').on( 'click', function() {
                  $( _get_grid_design_controls() ).slideToggle('fast');
                  $(this).toggleClass('open');
                } );

                /* ADD GOOGLE IN TITLE */
                $g_logo = $('<img>' , {class : 'tc-title-google-logo' , src : '//www.google.com/images/logos/google_logo_41.png' , height : 20 });
                $('#accordion-section-fonts_sec').prepend($g_logo);


                //http://ivaynberg.github.io/select2/#documentation
                $('select[data-customize-setting-link="tc_theme_options[tc_skin]"]').select2({
                    minimumResultsForSearch: -1, //no search box needed
                    templateResult: paintSkinOptionElement,
                    templateSelection: paintSkinOptionElement,
                    escapeMarkup: function(m) { return m; }
                }).on("select2-highlight", function(e) { //<- doesn't work with recent select2 and it doesn't provide alternatives :(
                  //triggerChange = true @see val method doc here http://ivaynberg.github.io/select2/#documentation
                 $(this).select2("val" , e.val, true );
                });
                //Skins handled with select2
                function paintSkinOptionElement(state) {
                    if (!state.id) return state.text; // optgroup
                    return '<span class="tc-select2-skin-color" style="background:' + $(state.element).data('hex') + '">' + $(state.element).data('hex') + '<span>';
                }

                //FONTS
                $('select[data-customize-setting-link="tc_theme_options[tc_fonts]"]').select2({
                    minimumResultsForSearch: -1, //no search box needed
                    templateResult: paintFontOptionElement,
                    templateSelection: paintFontOptionElement,
                    escapeMarkup: function(m) { return m; },
                }).on("select2-highlight", function(e) {//<- doesn't work with recent select2 and it doesn't provide alternatives :(
                  //triggerChange = true @see val method doc here http://ivaynberg.github.io/select2/#documentation
                  $(this).select2("val" , e.val, true );
                });

                function paintFontOptionElement(state) {
                    if ( ! state.id && ( -1 != state.text.indexOf('Google') ) )
                      return '<img class="tc-google-logo" src="//www.google.com/images/logos/google_logo_41.png" height="20"/> Font pairs'; // google font optgroup
                    else if ( ! state.id )
                      return state.text;// optgroup different than google font
                    return '<span class="tc-select2-font">' + state.text + '</span>';
                }


//CALL TO ACTIONS
                /* CONTRIBUTION TO CUSTOMIZR */
                var donate_displayed  = false,
                    is_pro            = 'customizr-pro' == serverControlParams.themeName;
                if (  ! serverControlParams.HideDonate && ! is_pro ) {
                  _render_donate_block();
                  donate_displayed = true;
                }

                //Main call to action
                if ( serverControlParams.ShowCTA && ! donate_displayed && ! is_pro ) {
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
                //_render_rate_czr();

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
                  $('.czr-close-request').click( function(e) {
                    e.preventDefault();
                    $('.donate-alert').slideToggle("fast");
                    $(this).hide();
                  });

                  $('.czr-hide-donate').click( function(e) {
                    _ajax_save();
                    setTimeout(function(){
                        $('#czr-donate-customizer').slideToggle("fast");
                    }, 200);
                  });

                  $('.czr-cancel-hide-donate').click( function(e) {
                    $('.donate-alert').slideToggle("fast");
                    setTimeout(function(){
                        $('.czr-close-request').show();
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
                  $('li[id*="tc_post_list_show_thumb"] > .czr-customizr-title').before( _cta() );
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
                  $('li[id*="tc_show_back_to_top"]').closest('ul').append( _cta() );
                }

                function _ajax_save() {
                    var AjaxUrl         = serverControlParams.AjaxUrl,
                    query = {
                        action  : 'hide_donate',
                        TCnonce :  serverControlParams.TCNonce,
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
//END OF CTA
        });
}) ( wp, jQuery );
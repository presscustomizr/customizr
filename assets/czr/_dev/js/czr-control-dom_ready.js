(function (wp, $) {
        var api = api || wp.customize;
        
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

                /*
                * Override select2 Results Adapter in order to select on highlight
                * deferred needed cause the selects needs to be instantiated when this override is complete
                * selec2.amd.require is asynchronous
                */
                var selectFocusResults = $.Deferred();
                if ( 'undefined' !== typeof $.fn.select2 && 'undefined' !== typeof $.fn.select2.amd && 'function' === typeof $.fn.select2.amd.require ) {
                    $.fn.select2.amd.require(['select2/results', 'select2/utils'], function (Result, Utils) {
                      var ResultsAdapter = function($element, options, dataAdapter) {
                        ResultsAdapter.__super__.constructor.call(this, $element, options, dataAdapter);
                      };
                      Utils.Extend(ResultsAdapter, Result);
                      ResultsAdapter.prototype.bind = function (container, $container) {
                        var _self = this;
                        container.on('results:focus', function (params) {
                          if ( params.element.attr('aria-selected') != 'true') {
                            _self.trigger('select', {
                                data: params.data
                            });
                          }
                        });
                        ResultsAdapter.__super__.bind.call(this, container, $container);
                      };
                      selectFocusResults.resolve( ResultsAdapter );
                    });
                }
                else {
                  selectFocusResults.resolve( false );
                }

                $.when( selectFocusResults ).done( function( customResultsAdapter ) {
                    var _skin_select2_params = {
                        minimumResultsForSearch: -1, //no search box needed
                        templateResult: paintSkinOptionElement,
                        templateSelection: paintSkinOptionElement,
                        escapeMarkup: function(m) { return m; }
                    },
                        _fonts_select2_params = {
                        minimumResultsForSearch: -1, //no search box needed
                        templateResult: paintFontOptionElement,
                        templateSelection: paintFontOptionElement,
                        escapeMarkup: function(m) { return m; },
                    };
                    /*
                    * Maybe use custom adapter
                    */
                    if ( customResultsAdapter ) {
                        $.extend( _skin_select2_params, {
                          resultsAdapter: customResultsAdapter,
                          closeOnSelect: false,
                        } );
                        $.extend( _fonts_select2_params, {
                          resultsAdapter: customResultsAdapter,
                          closeOnSelect: false,
                        } );
                    }
                    //http://ivaynberg.github.io/select2/#documentation
                    $('select[data-customize-setting-link="tc_theme_options[tc_skin]"]').select2( _skin_select2_params );

                    //Skins handled with select2
                    function paintSkinOptionElement(state) {
                        if (!state.id) return state.text; // optgroup
                        return '<span class="tc-select2-skin-color" style="background:' + $(state.element).data('hex') + '">' + $(state.element).data('hex') + '<span>';
                    }

                    //FONTS
                    $('select[data-customize-setting-link="tc_theme_options[tc_fonts]"]').select2( _fonts_select2_params );

                    function paintFontOptionElement(state) {
                        if ( ! state.id && ( -1 != state.text.indexOf('Google') ) )
                          return '<img class="tc-google-logo" src="//www.google.com/images/logos/google_logo_41.png" height="20"/> Font pairs'; // google font optgroup
                        else if ( ! state.id )
                          return state.text;// optgroup different than google font
                        return '<span class="tc-select2-font">' + state.text + '</span>';
                    }

                    /**
                    * Dependency between the header layout and the horizontal menu positions
                    * What this does:
                    * 1) enable/disale the 'pull-menu-center' among the select option for the horizontal menus position
                    *    this option is available only when the header layout is "centered" (logo centered)
                    * 2) reset to default the horizontal menus position ONLY if the user switches from an header
                    *    centered layout to a logo right/left layout.
                    * 
                    */
                    (function() {
                        var _hm_primary_position_option    = 'tc_theme_options[tc_menu_position]',

                            _hm_secondary_position_option  = 'tc_theme_options[tc_second_menu_position]',
                            _header_layout_setting         = api( 'tc_theme_options[tc_header_layout]' );


                        //if the initial header layout value is not centered
                        //we have to disable the select option 'pull-menu-center'
                        if ( 'centered' != _header_layout_setting.get() )   
                            toggle_select_option_visibility( false );


                        //when user switches the header layout:
                        // if the previous option value was "centered"
                        //1) make sure the menu is correctly aligned if the current header layout is not 'centered' 
                        //   and the previous was 'centered' 
                        //2) disable/enable 'pull-menu-center' menu position option
                        _header_layout_setting.callbacks.add( function(to, from ) {                                                       
                     
                              //1)
                              if ( 'centered' != to && 'centered' == from ) {
                                    reset_menu_position_option();
                              }
                              //2)
                              toggle_select_option_visibility( 'centered' == to );                        
                        
                        } );

                        function reset_menu_position_option() {
                            _.each( [ _hm_primary_position_option, _hm_secondary_position_option], function( option ) {

                                //if the current position of the menu is "centered"
                                //revert it to the default value
                                //Note: this function is called only when the user switches from an header centered layout
                                // to a logo right/left one.
                                if ( 'pull-menu-center' == api( option ).get() )
                                    api( option ).set( serverControlParams.isRTL ? 'pull-menu-left' : 'pull-menu-right' );

                            });
                        }

                        function toggle_select_option_visibility( is_header_centered ) {

                            _.each( [ _hm_primary_position_option, _hm_secondary_position_option], function( option ) {

                                var $_select = api.control( option ).container.find("select");
                                //enable disable "pull-menu-center" select option based on whether or not the header layout is 'centered'
                                $_select.find( 'option[value="pull-menu-center"]' )[ is_header_centered ? 'removeAttr': 'attr']('disabled', 'disabled');
                                $_select.selecter( 'destroy' ).selecter();

                            });  
                        }

                    })();
                });
        });
}) ( wp, jQuery );
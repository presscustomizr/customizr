(function (api, $, _) {
          //@return boolean
          var _is_checked = function( to ) {
                  return 0 !== to && '0' !== to && false !== to && 'off' !== to;
          };
          var _tagline_text;

          //when a dominus object define both visibility and action callbacks, the visibility can return 'unchanged' for non relevant servi
          //=> when getting the visibility result, the 'unchanged' value will always be checked and resumed to the servus control current active() state
          api.CZR_ctrlDependencies.prototype.dominiDeps = _.extend(
                api.CZR_ctrlDependencies.prototype.dominiDeps,
                [
                    {
                            dominus : 'blogdescription',
                            servi   : ['tc_show_tagline', 'tc_sticky_show_tagline'],
                            visibility : function( to, servusShortId ) {
                                var _to_return = !_.isEmpty( to );
                                //cross dependency
                                if ( 'tc_sticky_show_tagline' == servusShortId ) {
                                  _to_return = _to_return && _is_checked( api( api.CZR_Helpers.build_setId( 'tc_sticky_header' ) ).get() ) && _is_checked( api( api.CZR_Helpers.build_setId( 'tc_show_tagline' ) ).get() );
                                }
                                return _to_return;
                            },
                            actions : function( to, servusShortId ) {
                                //save initial state of tagline text
                                if ( typeof undefined === typeof _tagline_text ) {
                                  _tagline_text = to;
                                }

                                var _servus            = api( api.CZR_Helpers.build_setId( servusShortId ) );

                                /*
                                *  trigger partial refresh when tagline text passes from empty to something and vice-versa
                                */
                                if ( to != _tagline_text && 'tc_show_tagline' == servusShortId ) {
                                  /*
                                  * 1. tagline was empty
                                  * 2. new tagline is empty
                                  */
                                  if ( _.isEmpty( _tagline_text ) || _.isEmpty( to ) ) {
                                    _servus( ! _.isEmpty( to ) );
                                  }

                                }
                                //save new state
                                _tagline_text = to;
                            }
                    },
                    {
                          //we have to show restrict blog/home posts when
                          //1. show page on front and a page of posts is selected
                          //2, show posts on front
                            dominus : 'page_for_posts',
                            servi   : ['tc_blog_restrict_by_cat'],
                            visibility : function( to ) {
                                  return _is_checked( to );
                            },
                    },
                    {
                            dominus : 'show_on_front',
                            servi   : ['tc_blog_restrict_by_cat', 'tc_show_post_navigation_home'],
                            visibility : function( to, servusShortId ) {
                                  //not sure the cross dependency actually works ... :/
                                  //otherwise this shouldn't be needed ... right?
                                  if ( 'tc_show_post_navigation_home' == servusShortId ) {
                                    return ( 'nothing' != to  ) && _is_checked( api( api.CZR_Helpers.build_setId( 'tc_show_post_navigation' ) ).get() );
                                  }
                                  // tc_blog_restrict_by_cat.
                                  if ( 'posts' == to ) {
                                    return true;
                                  }
                                  if ( 'page' == to && 'tc_blog_restrict_by_cat' == servusShortId ) { //show cat picker also if a page for posts is set
                                    return _is_checked( api( api.CZR_Helpers.build_setId( 'page_for_posts' ) ).get() );
                                  }
                                  return false;
                            },
                    },
                    {
                            dominus : 'tc_logo_upload',
                            servi   : ['tc_logo_resize'],
                            visibility : function( to ) {
                                  return _.isNumber( to );
                            },
                    },
                    {
                            dominus : 'tc_show_featured_pages',
                            servi   : themeServerControlParams.FPControls,
                            visibility : function( to ) {
                                  return _is_checked( to );
                            },
                    },
                    {
                            dominus : 'tc_front_slider',
                            servi   : [
                              'tc_slider_width',
                              'tc_slider_delay',
                              'tc_slider_default_height',
                              'tc_slider_default_height_apply_all',
                              'tc_slider_change_default_img_size',
                              'tc_posts_slider_number',
                              'tc_posts_slider_stickies',
                              'tc_posts_slider_title',
                              'tc_posts_slider_text',
                              'tc_posts_slider_link',
                              'tc_posts_slider_button_text',
                              'tc_posts_slider_restrict_by_cat' //pro-bundle
                            ],
                            visibility : function( to, servusShortId ) {
                                  //posts slider options must be hidden when the posts slider not choosen
                                  if ( servusShortId.indexOf('tc_posts_slider_') > -1 ) {
                                    return 'tc_posts_slider' == to;
                                  }
                                  if ( _.contains( [ 'tc_slider_default_height_apply_all', 'tc_slider_change_default_img_size' ], servusShortId ) ) {
                                    return _is_checked( to ) && 'demo' != to;
                                  }
                                  return _is_checked( to );
                            },
                            actions : function( to, servusShortId ) {
                                 //if user selects the post slider option, append a notice in the label element
                                 //and hide the notice when no sliders have been created yet
                                 var $_front_slider_container = api.control( api.CZR_Helpers.build_setId('tc_front_slider') ).container,
                                     $_label = $( 'label' , $_front_slider_container ),
                                     $_empty_sliders_notice = $( 'div.czr-notice', $_front_slider_container);

                                  if ( 'tc_posts_slider' == to ) {
                                    if ( 0 !== $_label.length && ! $('.czr-notice' , $_label ).length ) {
                                      var $_notice = $('<span>', { class: 'czr-notice', html : themeServerControlParams.i18n.postSliderNote || '' } );
                                      $_label.append( $_notice );
                                    }
                                    else {
                                      $('.czr-notice' , $_label ).show();
                                    }

                                    //hide no sliders created notice
                                    if ( 0 !== $_empty_sliders_notice.length ) {
                                      $_empty_sliders_notice.hide();
                                    }
                                  }
                                  else {
                                    if ( 0 !== $( '.czr-notice' , $_label ).length )
                                      $( '.czr-notice' , $_label ).hide();
                                    if ( 0 !== $_empty_sliders_notice.length )
                                      $_empty_sliders_notice.show();
                                  }
                            }
                    },
                    {
                            dominus : 'tc_slider_default_height',
                            servi   : ['tc_slider_default_height_apply_all', 'tc_slider_change_default_img_size'],
                            visibility : function( to ) {
                                  //slider height options must be hidden is height = default height (500px), unchanged by user
                                  //and slider is not the demo one
                                  var _defaultHeight = themeServerControlParams.defaultSliderHeight || 500;
                                  return _defaultHeight != to && 'demo' != api( api.CZR_Helpers.build_setId( 'tc_front_slider' ) ).get();
                            },
                    },
                    {
                            dominus : 'tc_posts_slider_link',
                            servi   : ['tc_posts_slider_button_text'],
                            visibility : function( to ) {
                                  return ( to.indexOf('cta') > -1 ) && ( 'tc_posts_slider' == api( api.CZR_Helpers.build_setId( 'tc_front_slider' ) ).get() );
                            },
                    },
                    {
                            dominus : 'tc_post_list_thumb_shape',
                            servi   : ['tc_post_list_thumb_height'],
                            visibility : function( to ) {
                                  return to.indexOf('rectangular') > -1;
                            },
                    },
                    {
                            dominus : 'tc_post_list_thumb_position',
                            servi   : ['tc_post_list_thumb_alternate'],
                            visibility : function( to ) {
                                  return _.contains( [ 'left', 'right'], to );
                            },
                    },
                    {
                            dominus : 'tc_post_list_show_thumb',
                            servi   : [
                              'tc_post_list_use_attachment_as_thumb',
                              'tc_post_list_default_thumb',
                              'tc_post_list_thumb_shape',
                              'tc_post_list_thumb_alternate',
                              'tc_post_list_thumb_position',
                              'tc_post_list_thumb_height',
                              'tc_grid_thumb_height'
                            ],
                            visibility : function( to, servusShortId ) {
                                  if ( 'tc_grid_thumb_height' == servusShortId ) {
                                    //cross
                                    return _is_checked(to)
                                        && $('.tc-grid-toggle-controls').hasClass('open')
                                        && 'grid' == api( api.CZR_Helpers.build_setId( 'tc_post_list_grid' ) ).get();
                                  }
                                  return _is_checked(to) ;
                            },
                    },
                    {
                            dominus : 'tc_post_list_grid',
                            servi   : [
                              'tc_grid_columns',
                              'tc_grid_expand_featured',
                              'tc_grid_in_blog',
                              'tc_grid_in_archive',
                              'tc_grid_in_search',
                              'tc_grid_thumb_height',
                              'tc_grid_bottom_border',
                              'tc_grid_shadow',
                              'tc_grid_icons',
                              'tc_grid_num_words',
                              'tc_post_list_grid',//trick, see the actions
                            ],
                            visibility : function( to, servusShortId ) {
                                  if ( 'tc_post_list_grid' == servusShortId )
                                      return true;

                                  if ( _.contains( themeServerControlParams.gridDesignControls, servusShortId ) ) {
                                      _bool =  $('.tc-grid-toggle-controls').hasClass('open') && 'grid' == to;

                                      if ( 'tc_grid_thumb_height' == servusShortId ) {
                                        //cross
                                          return _bool && _is_checked( api( api.CZR_Helpers.build_setId( 'tc_post_list_show_thumb' ) ).get() );
                                      }
                                      return _bool;
                                  }
                                  return 'grid' == to;
                            },
                            actions : function( to, servusShortId ) {
                                  if ( 'tc_post_list_grid' == servusShortId ) {
                                      $('.tc-grid-toggle-controls').toggle( 'grid' == to );
                                  }
                            }
                    },
                    {
                            dominus : 'tc_infinite_scroll',
                            servi   : [
                              'tc_load_on_scroll_desktop',
                              'tc_load_on_scroll_mobile',
                              'tc_infinite_scroll_in_home',
                              'tc_infinite_scroll_in_archive',
                              'tc_infinite_scroll_in_search'
                            ],
                            visibility : function( to ) {
                                  return _is_checked(to);
                            }
                    },
                    {
                            dominus : 'tc_breadcrumb',
                            servi   : [
                              'tc_show_breadcrumb_home',
                              'tc_show_breadcrumb_in_pages',
                              'tc_show_breadcrumb_in_single_posts',
                              'tc_show_breadcrumb_in_post_lists',
                              'tc_breadcrumb_yoast'
                            ],
                            visibility : function( to ) {
                                  return _is_checked(to);
                            },
                    },
                    {
                            dominus : 'tc_show_title_icon',
                            servi   : [
                              'tc_show_page_title_icon',
                              'tc_show_post_title_icon',
                              'tc_show_archive_title_icon',
                              'tc_show_post_list_title_icon',
                              'tc_show_sidebar_widget_icon',
                              'tc_show_footer_widget_icon'
                            ],
                            visibility : function( to ) {
                                  return _is_checked(to);
                            },
                    },
                    {
                            dominus : 'tc_show_post_metas',
                            servi   : [
                              'tc_show_post_metas_home',
                              'tc_post_metas_design',
                              'tc_show_post_metas_single_post',
                              'tc_show_post_metas_post_lists',
                              'tc_show_post_metas_categories',
                              'tc_show_post_metas_tags',
                              'tc_show_post_metas_publication_date',
                              'tc_show_post_metas_update_date',
                              'tc_post_metas_update_notice_text',
                              'tc_post_metas_update_notice_interval',
                              'tc_show_post_metas_author'
                            ],
                            visibility : function( to ) {
                                  return _is_checked(to);
                            },
                    },
                    {
                            dominus : 'tc_show_post_metas_update_date',
                            servi   : ['tc_post_metas_update_date_format'],
                            visibility: function (to) {
                                  return _is_checked(to);
                            }
                    },
                    {
                            dominus : 'tc_post_metas_update_notice_in_title',
                            servi   : [
                              'tc_post_metas_update_notice_text',
                              'tc_post_metas_update_notice_format',
                              'tc_post_metas_update_notice_interval'
                            ],
                            visibility : function( to ) {
                                  return _is_checked(to);
                            },
                    },
                    {
                            dominus : 'tc_post_list_length',
                            servi   : ['tc_post_list_excerpt_length'],
                            visibility: function (to) {
                                  return 'excerpt' == to;
                            }
                    },
                    {
                            dominus : 'tc_sticky_show_title_logo',
                            servi   : ['tc_sticky_logo_upload'],
                            visibility: function (to) {
                                  return _is_checked(to);
                            }
                    },
                    {
                            dominus : 'tc_sticky_header',
                            servi   : [
                              'tc_sticky_show_tagline',
                              'tc_sticky_show_title_logo',
                              'tc_sticky_shrink_title_logo',
                              'tc_sticky_show_menu',
                              'tc_sticky_transparent_on_scroll',
                              'tc_sticky_logo_upload',
                              'tc_woocommerce_header_cart_sticky'
                            ],
                            visibility : function( to, servusShortId ) {
                                  if ( 'tc_woocommerce_header_cart_sticky' == servusShortId ) {
                                    return _is_checked(to) && _is_checked( api( api.CZR_Helpers.build_setId( 'tc_woocommerce_header_cart' ) ).get() );
                                  }
                                  //cross dependency
                                  if ( 'tc_sticky_show_tagline' == servusShortId ) {
                                    return !_.isEmpty( api( api.CZR_Helpers.build_setId( 'blogdescription' ) ).get() ) && _is_checked( to ) && _is_checked( api( api.CZR_Helpers.build_setId( 'tc_show_tagline' ) ).get() );
                                  }

                                  return _is_checked(to);
                            },
                    },
                    {
                            dominus : 'tc_woocommerce_header_cart',
                            servi   : ['tc_woocommerce_header_cart_sticky'],
                            visibility: function (to) {
                                  return _is_checked(to) && _is_checked( api( api.CZR_Helpers.build_setId( 'tc_sticky_header' ) ).get() );
                            }
                    },
                    {
                            dominus : 'tc_comment_bubble_color_type',
                            servi   : ['tc_comment_bubble_color'],
                            visibility: function (to) {
                                  return 'custom' == to;
                            }
                    },
                    {
                            dominus : 'tc_comment_show_bubble',
                            servi   : [
                              'tc_comment_bubble_shape',
                              'tc_comment_bubble_color_type',
                              'tc_comment_bubble_color'
                            ],
                            visibility : function( to, servusShortId ) {
                                  if ( 'tc_comment_bubble_color' == servusShortId ) {
                                    return _is_checked(to) && 'custom' == api( api.CZR_Helpers.build_setId( 'tc_comment_bubble_color_type' ) ).get();
                                  }
                                  return _is_checked(to);
                            },
                    },
                    {
                            dominus : 'tc_enable_dropcap',
                            servi   : [
                              'tc_dropcap_minwords',
                              'tc_dropcap_design',
                              'tc_post_dropcap',
                              'tc_page_dropcap'
                            ],
                            visibility : function( to, servusShortId ) {
                                  return _is_checked(to);
                            },
                    },
                    {
                            dominus : 'tc_enable_gallery',
                            servi   : [
                              'tc_gallery_fancybox',
                              'tc_gallery_style',
                            ],
                            visibility : function( to, servusShortId ) {
                                  return _is_checked(to);
                            },
                    },
                    {
                            dominus : 'tc_skin_random',
                            servi   : [
                              'tc_skin',
                            ],
                            visibility: function() {
                              return true;
                            },
                            actions : function( to, servusShortId ) {
                                  var $_skin_select = api.control( api.CZR_Helpers.build_setId(servusShortId) ).container;
                                  $_skin_select.find('select').prop('disabled', '1' == to ? 'disabled' : '' );
                            },
                    },
                    {
                            dominus : 'tc_show_post_navigation',
                            servi   : [
                              'tc_show_post_navigation_page',
                              'tc_show_post_navigation_home',
                              'tc_show_post_navigation_single',
                              'tc_show_post_navigation_archive'
                            ],
                            visibility : function( to, servusShortId ) {
                                  return _is_checked(to);
                            },
                    },
                    {
                            dominus : 'tc_display_second_menu',
                            servi   : [
                              'nav_menu_locations[secondary]',
                              'tc_second_menu_position',
                              'tc_second_menu_resp_setting',
                              'tc_menu_type',
                              'tc_menu_submenu_fade_effect',
                              'tc_menu_submenu_item_move_effect'
                            ],
                            visibility : function( to, servusShortId ) {
                                  var _menu_style_val = api( api.CZR_Helpers.build_setId( 'tc_menu_style' )).get();
                                  if ( _.contains( ['nav_menu_locations[secondary]', 'tc_second_menu_resp_setting'], servusShortId ) )
                                    return _is_checked(to) && 'aside' == _menu_style_val;
                                  //effects common to regular menu and second horizontal menu
                                  if ( _.contains( ['tc_menu_submenu_fade_effect', 'tc_menu_submenu_item_move_effect'], servusShortId ) )
                                    return ( _is_checked(to) && 'aside' == _menu_style_val ) || ( !_is_checked(to) && 'aside' != _menu_style_val );
                                  return _is_checked(to);
                            },
                    },
                    {
                            dominus : 'tc_menu_style',
                            servi   : [
                              'tc_menu_type',
                              'tc_menu_submenu_fade_effect',
                              'tc_menu_submenu_item_move_effect',
                              'tc_menu_resp_dropdown_limit_to_viewport',
                              'tc_display_menu_label',
                              'tc_display_second_menu',
                              'tc_second_menu_position',
                              'nav_menu_locations[secondary]',
                              'tc_second_menu_resp_setting',
                              'tc_menu_position', /* used to perform actions on menu position */
                              'tc_mc_effect', /* pro */
                              /* to trigger action once */
                              'tc_menu_style'
                            ],
                            //if the second menu is activated, only the tc_menu_resp_dropdown_limit_to_viewport is hidden
                            //otherwise all of them are hidden
                            visibility : function( to, servusShortId ) {
                                  //CASE 1 : regular menu choosen
                                  if ( 'aside' != to ) {
                                    if ( _.contains([
                                        'tc_display_menu_label',
                                        'tc_display_second_menu',
                                        'nav_menu_locations[secondary]',
                                        'tc_second_menu_position',
                                        'tc_second_menu_resp_setting',
                                        'tc_mc_effect'] , servusShortId ) ) {
                                      return false;
                                    } else {
                                      return true;
                                    }
                                  }
                                  //CASE 2 : side menu choosen
                                  else {
                                    if ( _.contains([
                                        'tc_menu_type',
                                        'tc_menu_submenu_fade_effect',
                                        'tc_menu_submenu_item_move_effect',
                                        'nav_menu_locations[secondary]',
                                        'tc_second_menu_position',
                                        'tc_second_menu_resp_setting'], servusShortId ) ) {
                                      return _is_checked( api( api.CZR_Helpers.build_setId('tc_display_second_menu') ).get() );
                                    }
                                    else if ( _.contains([
                                        'tc_menu_resp_dropdown_limit_to_viewport',
                                        'tc_menu_position'], servusShortId ) ) {
                                      return false;
                                    }
                                    return true;
                                  }
                            },
                            actions : function( to, servusShortId ) {
                                  //show the sidenav position notice
                                  if ( 'tc_menu_style' == servusShortId ) {
                                    var $_container = api.control(api.CZR_Helpers.build_setId( servusShortId )).container;
                                        $_notice    = $_container.children('.czr-notice');
                                    if ( 0 === $_notice.length ) {
                                      $_notice = $('<span>', { class: 'czr-notice', html : themeServerControlParams.i18n.sidenavNote || '' } );

                                      $_container.append( $_notice );
                                    }

                                    $_notice[ 'aside' == to ? 'show' : 'hide' ]();
                                }
                          }
                    },
                    {
                            //when user switches layout, make sure the menu is correctly aligned by default.
                            dominus : 'tc_show_tagline',
                            servi   : ['tc_sticky_show_tagline', 'tc_show_tagline'],
                            visibility: function (to, servusShortId ) {
                                  //since a tc_show_tagline dominus is in another section its visibility might not be processed
                                  //if that section has not been awekened before tc_show_tagline one
                                  var _to_return = !_.isEmpty( api( api.CZR_Helpers.build_setId( 'blogdescription' ) ).get() );
                                  //cross dependency
                                  if ( 'tc_sticky_show_tagline' == servusShortId ) {
                                    _to_return  = _to_return && _is_checked( api( api.CZR_Helpers.build_setId( 'tc_sticky_header' ) ).get() ) && _is_checked( to );
                                  }
                                  return _to_return;
                            },
                    },
                    {
                            //when user switches layout, make sure the menu is correctly aligned by default.
                            dominus : 'tc_hide_all_menus',
                            servi   : ['tc_hide_all_menus'],
                            visibility: function (to) {
                                  return true;
                            },
                            actions : function( to, servusShortId ) {
                                  var $_nav_section_container = api.section('nav').container,
                                      $_controls = $_nav_section_container.find('li.customize-control').not( api.control(api.CZR_Helpers.build_setId(servusShortId)).container );
                                  $_controls.each( function() {
                                    if ( $(this).is(':visible') )
                                      $(this).fadeTo( 500 , true === to ? 0.5 : 1).css('pointerEvents', true === to ? 'none' : ''); //.fadeTo() duration, opacity, callback
                                  });//$.each()
                            }
                    },
                    {
                            dominus : 'tc_show_back_to_top',
                            servi   : ['tc_back_to_top_position'],
                            visibility: function (to) {
                                  return _is_checked(to);
                            }
                    }
                    // {
                    //         dominus : 'tc_header_layout',
                    //         servi   : ['tc_menu_position', 'tc_second_menu_position'],
                    //         visibility: function() { return true; },
                    //         actions : function( to, servusShortId ) {
                    //               var servusId = api.CZR_Helpers.build_setId( servusShortId ),
                    //                   isHeaderCentered = 'centered' == to;

                    //               console.log('ALORS?', to, servusShortId );
                    //               if ( 'pull-menu-center' == api( servusId ).get() ) {
                    //                     api( servusId )( themeServerControlParams.isRTL ? 'pull-menu-left' : 'pull-menu-right' );
                    //               }

                    //               var $_select = api.control( servusId ).container.find("select");

                    //               $_select.find( 'option[value="pull-menu-center"]' )[ isHeaderCentered ? 'removeAttr': 'attr']('disabled', 'disabled');
                    //               $_select.selecter( 'destroy' ).selecter();
                    //         }
                    // },
                ]//dominiDeps {}
          );//_.extend()

}) ( wp.customize, jQuery, _);

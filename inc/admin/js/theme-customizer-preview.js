/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 * Things like site title, description, and background color changes
 * @package Customizr
 * @since Customizr 1.0
 */


( function( $ ) {
	wp.customize( 'tc_theme_options[tc_skin]' , function( value ) {
		value.bind( function( to ) {
			if ( TCPreviewParams && TCPreviewParams.themeFolder ) {
				//add a new link to the live stylesheet instead of replacing the actual skin link => avoid the flash of unstyle content during the skin load
				var $skin_style_element = ( 0 === $('#live-skin-css').length ) ? $('<link>' , { id : 'live-skin-css' , rel : 'stylesheet'}) : $('#live-skin-css');
				$skin_style_element.attr('href' , [ TCPreviewParams.themeFolder , '/inc/assets/css/' , to.replace('.css' , '.min.css') ].join('') );
				if (  0 === $('#live-skin-css').length )
					$('head').append($skin_style_element);
			}
		} );
	} );
	// Site title and description.
	wp.customize( 'blogname' , function( value ) {
		value.bind( function( to ) {
			$( 'a.site-title' ).html( to );
		} );
	} );
	wp.customize( 'blogdescription' , function( value ) {
		value.bind( function( to ) {
			$( 'h2.site-description' ).html( to );
		} );
	} );
	
	//featured page one text
	wp.customize( 'tc_theme_options[tc_featured_text_one]' , function( value ) {
		value.bind( function( to ) {
			$( '.widget-front p.fp-text-one' ).html( to );
		} );
	} );

	//featured page two text
	wp.customize( 'tc_theme_options[tc_featured_text_two]' , function( value ) {
		value.bind( function( to ) {
			$( '.widget-front p.fp-text-two' ).html( to );
		} );
	} );

	//featured page three text
	wp.customize( 'tc_theme_options[tc_featured_text_three]' , function( value ) {
		value.bind( function( to ) {
			$( '.widget-front p.fp-text-three' ).html( to );
		} );
	} );

	//featured page button text
	wp.customize( 'tc_theme_options[tc_featured_page_button_text]' , function( value ) {
		value.bind( function( to ) {
			$( '.fp-button' ).html( to );
		} );
	} );

	// Hook into background color change and adjust body class value as needed.
	wp.customize( 'background_color' , function( value ) {
		value.bind( function( to ) {
			if ( '#ffffff' == to || '#fff' == to )
				$( 'body' ).addClass( 'custom-background-white' );
			else if ( '' === to )
				$( 'body' ).addClass( 'custom-background-empty' );
			else
				$( 'body' ).removeClass( 'custom-background-empty custom-background-white' );
		} );
	} );

	//All icons
	wp.customize( 'tc_theme_options[tc_show_title_icon]' , function( value ) {
		value.bind( function( to ) {
			if ( false === to ) {
				$('.entry-title').add('h1').add('h2').removeClass('format-icon');
				$('.tc-sidebar').add('.footer-widgets').addClass('no-widget-icons');
			}
			else {
				$('.entry-title').add('h1').add('h2').addClass('format-icon');
				$('.tc-sidebar').add('.footer-widgets').removeClass('no-widget-icons');
			}
		} );
	} );

	//Icons : page
	wp.customize( 'tc_theme_options[tc_show_page_title_icon]' , function( value ) {
		value.bind( function( to ) {
			if ( false === to ) {
				$('.entry-title' , '.page').removeClass('format-icon');
			}
			else {
				$('.entry-title' , '.page').addClass('format-icon');
			}
		} );
	} );

	//Icons : single post
	wp.customize( 'tc_theme_options[tc_show_post_title_icon]' , function( value ) {
		value.bind( function( to ) {
			if ( false === to ) {
				$('.entry-title' , '.single').removeClass('format-icon');
			}
			else {
				$('.entry-title' , '.single').addClass('format-icon');
			}
		} );
	} );

	//Icons : Archive title
	wp.customize( 'tc_theme_options[tc_show_archive_title_icon]' , function( value ) {
		value.bind( function( to ) {
			if ( false === to ) {
				$('archive h1.entry-title, .blog h1.entry-title, .search h1, .author h1').removeClass('format-icon');
			}
			else {
				$('archive h1.entry-title, .blog h1.entry-title, .search h1, .author h1').addClass('format-icon');
			}
		} );
	} );

	//Icons : Posts in lists titles
	wp.customize( 'tc_theme_options[tc_show_post_list_title_icon]' , function( value ) {
		value.bind( function( to ) {
			if ( false === to ) {
				$('.archive article .entry-title, .blog article .entry-title, .search article .entry-title, .author article .entry-title').removeClass('format-icon');
			}
			else {
				$('.archive article .entry-title, .blog article .entry-title, .search article .entry-title, .author article .entry-title').addClass('format-icon');
			}
		} );
	} );

	

	//Widget Icons
	wp.customize( 'tc_theme_options[tc_show_sidebar_widget_icon]' , function( value ) {
		value.bind( function( to ) {
			if ( false === to )
				$('.tc-sidebar').addClass('no-widget-icons');
			else
				$('.tc-sidebar').removeClass('no-widget-icons');
		} );
	} );
	wp.customize( 'tc_theme_options[tc_show_footer_widget_icon]' , function( value ) {
		value.bind( function( to ) {
			if ( false === to )
				$('.footer-widgets').addClass('no-widget-icons');
			else
				$('.footer-widgets').removeClass('no-widget-icons');
		} );
	});

	//Post metas
	wp.customize( 'tc_theme_options[tc_show_post_metas]' , function( value ) {
		value.bind( function( to ) {
			if ( false === to ) {
				$('.entry-header .entry-meta' , '.article-container').hide('slow');
				
			}
			else if (! $('body').hasClass('hide-post-metas') ) {
				$('.entry-header .entry-meta' , '.article-container').show('fast');
			}
		} );
	} );
	wp.customize( 'tc_theme_options[tc_show_post_metas_home]' , function( value ) {
		value.bind( function( to ) {
			if ( false === to ) {
				$('.entry-header .entry-meta' , '.home .article-container').hide('slow');
				$('body').addClass('hide-post-metas');
			}
			else {
				$('body').removeClass('hide-post-metas');
				$('.entry-header .entry-meta' , '.home .article-container').show('fast');
			}
		} );
	});
	wp.customize( 'tc_theme_options[tc_show_post_metas_single_post]' , function( value ) {
		value.bind( function( to ) {
			if ( false === to )
				$('.entry-header .entry-meta' , '.single .article-container').hide('slow');
			else
				$('.entry-header .entry-meta' , '.single .article-container').show('fast');
		} );
	});
	wp.customize( 'tc_theme_options[tc_show_post_metas_post_lists]' , function( value ) {
		value.bind( function( to ) {
			if ( false === to )
				$('.entry-header .entry-meta' , '.article-container').not('.single').hide('slow');
			else
				$('.entry-header .entry-meta' , '.article-container').not('.single').show('fast');
		} );
	});
	wp.customize( 'tc_theme_options[tc_link_hover_effect]' , function( value ) {
		value.bind( function( to ) {
			if ( false === to )
				$('body').removeClass('tc-fade-hover-links');
			else
				$('body').addClass('tc-fade-hover-links');
		} );
	});
	wp.customize( 'tc_theme_options[tc_post_list_thumb_height]' , function( value ) {
		value.bind( function( to ) {
			$('.tc-rectangular-thumb').css('max-height' , to + 'px').trigger('refresh-height');
		} );
	});
	wp.customize( 'tc_theme_options[tc_single_post_thumb_height]' , function( value ) {
		value.bind( function( to ) {
			$('.tc-rectangular-thumb').css('height' , to + 'px').css('max-height' , to + 'px').trigger('refresh-height');
		} );
	});
	wp.customize( 'tc_theme_options[tc_show_tagline]' , function( value ) {
		value.bind( function( to ) {
			if ( false === to ) {
				$('.site-description').hide('slow');
				$(window).trigger('resize');
			}
			else {
				$('.site-description').show('fast');
				$(window).trigger('resize');
			}
		} );
	});
	wp.customize( 'tc_theme_options[tc_display_boxed_navbar]' , function( value ) {
		value.bind( function( to ) {
			if ( false === to )
				$('body').addClass('no-navbar');
			else
				$('body').removeClass('no-navbar');
		} );
	});
	wp.customize( 'tc_theme_options[tc_header_layout]' , function( value ) {
		value.bind( function( to ) {
			if ( "centered" == to ) {
				$('.tc-header').removeClass('logo-left')
					.removeClass('logo-right')
					.addClass('logo-centered');
			}
			else if ( "left" == to ) {
				$('.tc-header').removeClass('logo-centered')
					.removeClass('logo-right')
					.addClass('logo-left');
				$('.brand').removeClass('pull-right')
					.addClass('pull-left');
			}
			else if ( "right" == to ) {
				$('.tc-header').removeClass('logo-centered')
					.removeClass('logo-left')
					.addClass('logo-right');
				$('.brand').removeClass('pull-left')
					.addClass('pull-right');
			}
			setTimeout( function() {
				$('.brand').trigger('resize');
			} , 400);
		} );
	});
	wp.customize( 'tc_theme_options[tc_social_in_header]' , function( value ) {
		value.bind( function( to ) {
			if ( false === to ) {
				$('.tc-header .social-block').hide('slow');
				$(window).trigger('resize');
			}
			else {
				$('.tc-header .social-block').show('fast');
				$(window).trigger('resize');
			}
		} );
	});
	wp.customize( 'tc_theme_options[tc_social_in_footer]' , function( value ) {
		value.bind( function( to ) {
			if ( false === to ) {
				$('.tc-footer-social-links-wrapper' , '#footer').hide('slow');
				$(window).trigger('resize');
			}
			else {
				$('.tc-footer-social-links-wrapper' , '#footer').show('fast');
				$(window).trigger('resize');
			}
		} );
	});
	wp.customize( 'tc_theme_options[tc_social_in_left-sidebar]' , function( value ) {
		value.bind( function( to ) {
			if ( false === to ) {
				$('#left .social-block' , '.tc-sidebar').hide('slow');
				$(window).trigger('resize');
			}
			else {
				$('#left .social-block' , '.tc-sidebar').show('fast');
				$(window).trigger('resize');
			}
		} );
	});
	wp.customize( 'tc_theme_options[tc_social_in_right-sidebar]' , function( value ) {
		value.bind( function( to ) {
			if ( false === to ) {
				$('#right .social-block' , '.tc-sidebar').hide('slow');
				$(window).trigger('resize');
			}
			else {
				$('#right .social-block' , '.tc-sidebar').show('fast');
				$(window).trigger('resize');
			}
		} );
	});
	wp.customize( 'tc_theme_options[tc_social_in_sidebar_title]' , function( value ) {
		value.bind( function( to ) {
			$( '.social-block .widget-title' , '.tc-sidebar' ).html( to );
			if ( ! to )
				$('.social-block' , '.tc-sidebar').hide('slow');
			else
				$('.social-block' , '.tc-sidebar').show('fast');
		} );
	});
	wp.customize( 'tc_theme_options[tc_menu_position]' , function( value ) {
		value.bind( function( to ) {
			if ( 'pull-menu-left' == to )
				$('.navbar-wrapper').addClass(to).removeClass('pull-menu-right');
			else
				$('.navbar-wrapper').addClass(to).removeClass('pull-menu-left');
		} );
	});
	wp.customize( 'tc_theme_options[tc_menu_submenu_fade_effect]' , function( value ) {
		value.bind( function( to ) {
			if ( false !== to )
				$('.navbar-wrapper').addClass('tc-submenu-fade');
			else
				$('.navbar-wrapper').removeClass('tc-submenu-fade');
		} );
	});
	wp.customize( 'tc_theme_options[tc_menu_submenu_item_move_effect]' , function( value ) {
		value.bind( function( to ) {
			if ( false !== to )
				$('.navbar-wrapper').addClass('tc-submenu-move');
			else
				$('.navbar-wrapper').removeClass('tc-submenu-move');
		} );
	});
	wp.customize( 'tc_theme_options[tc_sticky_header]' , function( value ) {
		value.bind( function( to ) {
			if ( false !== to ) {
				$('body').addClass('tc-sticky-header').trigger('resize');
				//$('#tc-reset-margin-top').css('margin-top' , '');
			}
			else {
				$('body').removeClass('tc-sticky-header').trigger('resize');
				$('#tc-reset-margin-top').css('margin-top' , '' );
			}
		} );
	});
	wp.customize( 'tc_theme_options[tc_sticky_show_tagline]' , function( value ) {
		value.bind( function( to ) {
			if ( false !== to )
				$('.tc-header').addClass('tc-tagline-on').removeClass('tc-tagline-off').trigger('resize');
			else
				$('.tc-header').addClass('tc-tagline-off').removeClass('tc-tagline-on').trigger('resize');
		} );
	});
	wp.customize( 'tc_theme_options[tc_sticky_show_title_logo]' , function( value ) {
		value.bind( function( to ) {
			if ( false !== to ) {
				$('.tc-header').addClass('tc-title-logo-on').removeClass('tc-title-logo-off').trigger('resize');
			}
			else {
				$('.tc-header').addClass('tc-title-logo-off').removeClass('tc-title-logo-on').trigger('resize');
			}
		} );
	});
	wp.customize( 'tc_theme_options[tc_sticky_shrink_title_logo]' , function( value ) {
		value.bind( function( to ) {
			if ( false !== to )
				$('.tc-header').addClass('tc-shrink-on').removeClass('tc-shrink-off').trigger('resize');
			else
				$('.tc-header').addClass('tc-shrink-off').removeClass('tc-shrink-on').trigger('resize');
		} );
	});
	wp.customize( 'tc_theme_options[tc_sticky_show_menu]' , function( value ) {
		value.bind( function( to ) {
			if ( false !== to )
				$('.tc-header').addClass('tc-menu-on').removeClass('tc-menu-off').trigger('resize');
			else
				$('.tc-header').addClass('tc-menu-off').removeClass('tc-menu-on').trigger('resize');
		} );
	});
	wp.customize( 'tc_theme_options[tc_sticky_z_index]' , function( value ) {
		value.bind( function( to ) {
			$('.tc-no-sticky-header .tc-header, .tc-sticky-header .tc-header').css('z-index' , to);
		} );
	});
	wp.customize( 'tc_theme_options[tc_custom_css]' , function( value ) {
		value.bind( function( to ) {
			$('#option-custom-css').remove();
			var $style_element = ( 0 === $('#live-custom-css').length ) ? $('<style>' , { id : 'live-custom-css'}) : $('#live-custom-css');
			if (  0 === $('#live-custom-css').length )
				$('head').append($style_element.html(to));
			else
				$style_element.html(to);
		} );
	} );
	wp.customize( 'tc_theme_options[tc_slider_default_height]' , function( value ) {
		value.bind( function( to ) {
			$('#customizr-slider').addClass('custom-slider-height');
			$('.carousel .item').css('line-height' , to + 'px').css('max-height', to + 'px').css('min-height', to + 'px').trigger('resize');
			$('.tc-slider-controls').css('line-height' , to + 'px').css('max-height', to + 'px').trigger('resize');
		} );
	});
	wp.customize( 'tc_theme_options[tc_sticky_transparent_on_scroll]' , function( value ) {
		value.bind( function( to ) {
			if ( false !== to ) {
				$('body').addClass('tc-transparent-on-scroll');
				$('body').removeClass('tc-solid-color-on-scroll');
			}
			else {
				$('body').removeClass('tc-transparent-on-scroll');
				$('body').addClass('tc-solid-color-on-scroll');
			}
		} );
	});
	wp.customize( 'tc_theme_options[tc_post_metas_update_notice_text]' , function( value ) {
		value.bind( function( to ) {
			$( '.tc-update-notice' ).html( to );
		} );
	} );

	wp.customize( 'tc_theme_options[tc_comment_bubble_color]' , function( value ) {
		value.bind( function( to ) {
			$('#custom-bubble-color').remove();
			var $style_element	= $('<style>' , { id : 'custom-bubble-color'}),
				bubble_live_css = '';
			//default bubble
			bubble_live_css += '.comments-link .fs1 {color:' + to + ';}';
			//custom bubble
			bubble_live_css += '.comments-link .custom-bubble-one {border-color:' + to + ';color:' + to + '}';
			bubble_live_css += '.comments-link .custom-bubble-one:before {border-color:' + to + ' rgba(0, 0, 0, 0);}';
			$('head').append($style_element.html(bubble_live_css));
		} );
	} );
	wp.customize( 'tc_theme_options[tc_post_metas_update_notice_format]' , function( value ) {
		value.bind( function( to ) {
			$( '.tc-update-notice').each( function() {
				var classes = $(this).attr('class').split(' ');
				for (var key in classes) {
					if ( -1 !== (classes[key]).indexOf('label-') ) {
						classes.splice(key, 1);
					}
				}
				//rebuild the class attr
				$(this).attr('class' , classes.join(' ') );
			});
			$( '.tc-update-notice' ).addClass( to );
		} );
	} );
} )( jQuery );
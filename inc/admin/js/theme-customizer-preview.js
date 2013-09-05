/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 * Things like site title, description, and background color changes
 * @package Customizr
 * @since Customizr 1.0
 */


( function( $ ) {
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
			else if ( '' == to )
				$( 'body' ).addClass( 'custom-background-empty' );
			else
				$( 'body' ).removeClass( 'custom-background-empty custom-background-white' );
		} );
	} );

	//debug tips color
	wp.customize( 'tc_theme_options[tc_debug_tips_color]' , function( value ) {
		value.bind( function( newval ) {
			$( 'a.debug-tip' ).css( 'color' , newval );
		} );
	} );

} )( jQuery );
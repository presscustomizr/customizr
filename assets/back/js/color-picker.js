
/**
 * Set up the color pickers to work with our text input field.
 *
 * @package Customizr
 * @since Customizr 1.0
 */

jQuery( document ).ready(function(){
    "use strict";

    //This if statement checks if the color picker widget exists within jQuery UI
    //If it does exist then we initialize the WordPress color picker on our text input field
    if( typeof jQuery.wp === 'object' && typeof jQuery.wp.wpColorPicker === 'function' ){
        jQuery( '#slide_color_field' ).wpColorPicker();
    }
    else {
        //We use farbtastic if the WordPress color picker widget doesn't exist
        if( jQuery( '#slide_color_field' ).length ) {
            jQuery( '#colorpicker' ).farbtastic( '#slide_color_field' );
        }
    }
});
/**
 * Ajax scripts for slider
 *
 * @package Customizr
 * @since Customizr 1.0
 */

jQuery(document).ready(function(){
      jQuery( 'input#slider_check_field, select#post_slider_field' ).live("change",function(){
        jQuery( '#tc_slider_list .spinner' ).show();
        //check if we are in the post/page screen and return a var
        var tc_post_type = 'attachment';
        if(jQuery( 'input#post_slider_check_field' ).length) {
          tc_post_type = 'post';
        }
        //get checked value
        slider_check_field = 0;
        if (jQuery( 'input#slider_check_field' ).is(":checked"))
            {
              slider_check_field = 1;
            }
        jQuery.post(
           ajaxurl,
           {
              //ADD vars to $_POST
              'action'              :'slider_action' ,
              //get the post_id with an hidden input field
              'tc_post_id'          : jQuery( 'input#tc_post_id' ).val(),
              //add the check current state and inputs
              'slider_check_field'  : slider_check_field,
              'slide_title_field'   : jQuery( 'input#slide_title_field' ).val(),
              'slide_text_field'    : jQuery( 'textarea#slide_text_field' ).val(),
              'slide_color_field'   : jQuery( 'input#slide_color_field' ).val(),
              'slide_button_field'  : jQuery( 'input#slide_button_field' ).val(),
              'slide_link_field'    : jQuery( 'select#slide_link_field' ).val(),
              'new_slider_name'     : jQuery( 'input#slider_field' ).val(),
              'post_slider_name'    : jQuery( 'select#post_slider_field' ).val(),
              // send the nonce along with the request
              'SliderCheckNonce'    : SliderAjax.SliderCheckNonce,
              //post var if we are in a post/page screen
              'tc_post_type'         : tc_post_type
           },
           function(response){
              //alert( 'The server responded: ' + response);
              jQuery("#tc_slider_list").empty();
              jQuery("#tc_slider_list").append(response);

              //reactivate the color picker
              if( typeof jQuery.wp === 'object' && typeof jQuery.wp.wpColorPicker === 'function' ){
                  jQuery( '#slide_color_field' ).wpColorPicker();
              }
              else {
                  //We use farbtastic if the WordPress color picker widget doesn't exist
                  if( jQuery( '#slide_color_field' ).length ) {
                    jQuery( '#colorpicker' ).farbtastic( '#slide_color_field' );
                  }
              }
              //reactivate sortable
              jQuery( '#sortable' ).sortable({
              placeholder: "ui-state-highlight",
              update: function(event, ui) {
                  var newOrder = jQuery(this).sortable( 'toArray' ).toString();
                  jQuery.post(
                     ajaxurl,
                     {
                        //ADD vars to $_POST
                        'action':'slider_action' ,
                        //get the post_id with an hidden input field
                        'tc_post_id': jQuery( 'input#tc_post_id' ).val(),
                         // send the nonce along with the request
                        'SliderCheckNonce' : SliderAjax.SliderCheckNonce,
                        //position array
                        'newOrder':newOrder,
                        //current post slider
                        'currentpostslider':jQuery( 'select#post_slider_field' ).val()
                     },
                     function(response){
                        //alert( 'The server responded: ' +  response);
                         slider_update = jQuery( '<div/>' ).addClass( 'updated' )
                        .css( 'opacity' ,0)
                        .html( '<div class="message">Slider updated</div>' )
                        .appendTo( '#update-status' );
                        slider_update.animate({opacity:0.9}, function(){
                        slider_update.delay(1200).fadeOut(function()
                          {
                            slider_update.remove();
                          });
                        });
                     }
                  );
                }
            });
           jQuery( '#sortable' ).disableSelection();
           jQuery( '#tc_slider_list .spinner' ).hide();
           }//end of response
        );
      });


    jQuery( '#tc_create_slider' ).live("click",function(){
        jQuery( '#tc_slider_list .spinner' ).show();
        //check if we are in the post/page screen and return a var
        var tc_post_type = 'attachment';
        if(jQuery( 'input#post_slider_check_field' ).length) {
          tc_post_type = 'post';
        }
        //get checked value
        var slider_check_field = 0;
        if (jQuery( 'input#slider_check_field' ).is(":checked"))
            {
              slider_check_field = 1;
            }
        jQuery.post(
           ajaxurl,
           {
             //ADD vars to $_POST
              'action'              :'slider_action' ,
              //get the post_id with an hidden input field
              'tc_post_id'          : jQuery( 'input#tc_post_id' ).val(),
              //add the check current state and inputs
              'slider_check_field'  : slider_check_field,
              'slide_title_field'   : jQuery( 'input#slide_title_field' ).val(),
              'slide_text_field'    : jQuery( 'textarea#slide_text_field' ).val(),
              'slide_color_field'   : jQuery( 'input#slide_color_field' ).val(),
              'slide_button_field'  : jQuery( 'input#slide_button_field' ).val(),
              'slide_link_field'    : jQuery( 'select#slide_link_field' ).val(),
              'new_slider_name'     : jQuery( 'input#slider_field' ).val(),
              'post_slider_name'    : jQuery( 'select#post_slider_field' ).val(),
              // send the nonce along with the request
              'SliderCheckNonce'    : SliderAjax.SliderCheckNonce,
              //post var if we are in a post/page screen
              'tc_post_type'         : tc_post_type
           },
           function(response){
              //alert( 'The server responded: ' + response);
              jQuery("#tc_slider_list").empty();
              jQuery("#tc_slider_list").append(response);

              //reactivate the color picker
              if( typeof jQuery.wp === 'object' && typeof jQuery.wp.wpColorPicker === 'function' ){
                  jQuery( '#slide_color_field' ).wpColorPicker();
              }
              else {
                  //We use farbtastic if the WordPress color picker widget doesn't exist
                  if( jQuery( '#slide_color_field' ).length ) {
                    jQuery( '#colorpicker' ).farbtastic( '#slide_color_field' );
                  }
              }
              //reactivate sortable
              jQuery( '#sortable' ).sortable({
                placeholder: "ui-state-highlight"
              });
              jQuery( '#tc_slider_list .spinner' ).hide();
           }
        );
      });


       jQuery( '#sortable' ).sortable({
            placeholder: "ui-state-highlight",
            update: function(event, ui) {
                var newOrder = jQuery(this).sortable( 'toArray' ).toString();
                jQuery.post(
                   ajaxurl,
                   {
                      //ADD vars to $_POST
                      'action':'slider_action' ,
                      //get the post_id with an hidden input field
                      'tc_post_id': jQuery( 'input#tc_post_id' ).val(),
                       // send the nonce along with the request
                      'SliderCheckNonce' : SliderAjax.SliderCheckNonce,
                      //position array
                      'newOrder':newOrder,
                      //current post slider
                      'currentpostslider':jQuery( 'select#post_slider_field' ).val()
                   },
                   function(response){
                      //alert( 'The server responded: ' +  response);
                      slider_update = jQuery( '<div/>' ).addClass( 'updated' )
                      .css( 'opacity' ,0)
                      .html( '<div class="message">Slider updated</div>' )
                      .appendTo( '#update-status' );
                      slider_update.animate({opacity:0.9}, function(){
                      slider_update.delay(1200).fadeOut(function()
                        {
                          slider_update.remove();
                        });
                      });
                   }
                );
            }
        });
       jQuery( '#sortable' ).disableSelection();


       jQuery( '#delete-slider' ).live("click",function(){
        //checks if we are in the post/page/attachment screen and returns a var
        var tc_post_type = 'attachment';
        if(jQuery( 'input#post_slider_check_field' ).length) {
          tc_post_type = 'post';
        }
        jQuery.post(
           ajaxurl,
           {
             //ADD vars to $_POST
              'action'              :'slider_action' ,
              //get the post_id with an hidden input field
              'tc_post_id'          : jQuery( 'input#tc_post_id' ).val(),
              //add the check current state and inputs
              'delete_slider'       : true,
              //reset new_slider_name if needed
              'new_slider_name'    : null,
              //current post sider
              'currentpostslider'   : jQuery( 'select#post_slider_field' ).val(),
              // send the nonce along with the request
              'SliderCheckNonce'    : SliderAjax.SliderCheckNonce,
              //post var if we are in a post/page screen
              'tc_post_type'         : tc_post_type
           },
           function(response){
              //page/post screen case
              if(jQuery( 'input#post_slider_check_field' ).length) {
                jQuery("#post_slider_infos").empty();
                jQuery("#post_slider_infos").append(response);
              }
              //attachment screen case
              else {
                jQuery("#tc_slider_list").empty();
                jQuery("#tc_slider_list").append(response);
              }
              //iphone checkbox
              jQuery( '.iphonecheck' ).iphoneStyle({ checkedLabel: 'Yes' , uncheckedLabel: 'No' });

              //reactivate the color picker
              if( typeof jQuery.wp === 'object' && typeof jQuery.wp.wpColorPicker === 'function' ){
                  jQuery( '#slide_color_field' ).wpColorPicker();
              }
              else {
              //We use farbtastic if the WordPress color picker widget doesn't exist
                if( jQuery( '#slide_color_field' ).length ) {
                    jQuery( '#colorpicker' ).farbtastic( '#slide_color_field' );
                }
              }
              //reactivate sortable
              jQuery( '#sortable' ).sortable({
                placeholder: "ui-state-highlight"
              });
           }
        );
      });

      jQuery( 'input#post_slider_check_field, select#post_slider_field' ).live("change",function(){
        jQuery( '#post_slider_infos .spinner' ).show();
         //checks if we are in the post/page/attachment screen and returns a var
        var tc_post_type = 'attachment';
        if(jQuery( 'input#post_slider_check_field' ).length) {
          tc_post_type = 'post';
        }
        //get checked value
        var post_slider_check_field = 0;
        if (jQuery( 'input#post_slider_check_field' ).is(":checked"))
            {
              post_slider_check_field = 1;
            }
        jQuery.post(
           ajaxurl,
           {
              //ADD vars to $_POST
              'action'                    :'slider_action' ,
              //get the post_id with an hidden input field
              'tc_post_id'                : jQuery( 'input#tc_post_id' ).val(),
              //add the check current state and inputs
              'post_slider_check_field'   : post_slider_check_field,
              'post_slider_name'          : jQuery( 'select#post_slider_field' ).val(),
              // send the nonce along with the request
              'SliderCheckNonce'          : SliderAjax.SliderCheckNonce,
              //post var if we are in a post/page screen
              'tc_post_type'         : tc_post_type
           },
           function(response){
              //alert( 'The server responded: ' + response);
              jQuery("#post_slider_infos").empty();
              jQuery("#post_slider_infos").append(response);

              //iphone checkbox
              jQuery( '.iphonecheck' ).iphoneStyle({ checkedLabel: 'Yes' , uncheckedLabel: 'No' });

              //reactivate sortable
              jQuery( '#sortable' ).sortable({
              placeholder: "ui-state-highlight",
              update: function(event, ui) {
                  var newOrder = jQuery(this).sortable( 'toArray' ).toString();
                  jQuery.post(
                     ajaxurl,
                     {
                        //ADD vars to $_POST
                        'action':'slider_action' ,
                        //get the post_id with an hidden input field
                        'tc_post_id': jQuery( 'input#tc_post_id' ).val(),
                         // send the nonce along with the request
                        'SliderCheckNonce' : SliderAjax.SliderCheckNonce,
                        //position array
                        'newOrder':newOrder,
                        //current post slider
                        'currentpostslider':jQuery( 'select#post_slider_field' ).val()
                     },
                     function(response){
                        //alert( 'The server responded: ' +  response);
                         slider_update = jQuery( '<div/>' ).addClass( 'updated' )
                        .css( 'opacity' ,0)
                        .html( '<div class="message">Slider updated</div>' )
                        .appendTo( '#update-status' );
                        slider_update.animate({opacity:0.9}, function(){
                        slider_update.delay(1200).fadeOut(function()
                          {
                            slider_update.remove();
                          });
                        });
                     }
                  );
              }
            });
            jQuery( '#sortable' ).disableSelection();
            jQuery( '#post_slider_infos .spinner' ).hide();
          }//end of response
        );
      });
});
/*jQuery(document).ready(function(){
  jQuery('select#slider_name_field').change(function(){
    //alert(siteurl);
    //jQuery(formid+"_contactFormbutton").hide();
    jQuery.post(
      //site.siteurl+"/wp-admin/admin-ajax.php",
      "http://192.168.0.29/web/wordpress/wp-admin/admin-ajax.php",
      //Data
      {
        action:"ak_attach",
        'cookie':encodeURIComponent(document.cookie),
        'slider_name_field':jQuery('select#slider_name_field').val(),
        'type':jQuery('#term_meta_slider_type').val()
      },
      //onsuccessfunction
      function(id){
        //DoSomeStuffinheretoupdateelementsonthepage...
        //Resettheform
        jQuery('input#newsliders').val('');
        jQuery('#sliders-adder').addClass('wp-hidden-children');
        return false;
      }
    );
  return false;
  });
});*/
jQuery(document).ready(function(){
  jQuery('select#slider_name_field').change(function(){
    jQuery.post(
       ajaxurl,
       {
          'action':'tc_slider_action',
          'slider_id': jQuery('select#slider_name_field').val(),
          //'data':'action=my_special_action&main_catid=' + $mainCat, alternative way to pass ajax parameters
          // send the nonce along with the request
          'SliderAjaxNonce' : SliderAjax.SliderAjaxNonce
       },
       function(response){
          //alert('The server responded: ' + response);
          jQuery("#tc_slider_infos").empty();
          jQuery("#tc_slider_infos").append(response);
       }
    );
  });
});
<?php
add_filter('czr_js_customizer_control_params', 'czr_fn_add_social_module_data');


function czr_fn_add_social_module_data( $params ) {
  return array_merge(
    $params,
    array(
        'social_el_params' => array(
            //Social Module
            'defaultSocialColor' => 'rgba(255,255,255,0.7)',
        )
    )
  );
}
?>
<?php

/**
* Boolean helper to check if the secondary menu is enabled
* since v3.4+
*/
function czr_fn_is_secondary_menu_enabled() {

    return (bool) esc_attr( czr_fn_opt( 'tc_display_second_menu' ) ) && 'aside' == esc_attr( czr_fn_opt( 'tc_menu_style' ) );
}



?>
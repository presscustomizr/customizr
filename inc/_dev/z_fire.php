<?php
//Creates a new instance
new CZR___;
do_action('czr_load');


if ( czr_fn_isprevdem() && class_exists('CZR_prevdem') ) {
    new CZR_prevdem();
}

//may be load pro
if ( CZR_IS_PRO ) {
    new CZR_init_pro(CZR___::$theme_name );
}
?>
<?php
class CZR_contact_info_model_class extends CZR_Model {
    public $contact_info; //array of contact info

    /*
    * @override
    * fired before the model properties are parsed
    *
    * return model params array()
    */
    function czr_fn_extend_params( $model = array() ) {
        $_contact_info       = array();
        $_phone              = czr_fn_opt( 'tc_contact_info_phone' );
        $_opening_hours      = czr_fn_opt( 'tc_contact_info_opening_hours' );
        $_email              = czr_fn_opt( 'tc_contact_info_email' );

        //create phone link element
        if ( '' != $_phone ) {
            $_contact_info[] = sprintf( '<a class="ci-phone"href="tel:%1$s" title="%1$s"><i class="fas fa-phone"></i><span>%1$s<span></a>', $_phone );
        }

        //create opening hours element
        if ( '' != $_opening_hours ) {
            $_contact_info[] = sprintf( '<span class="ci-oh"><i class="fas fa-clock"></i><span>%1$s<span></span>', $_opening_hours );
        }

        //create email link element
        if ( '' != $_email ) {
            $_contact_info[] = sprintf( '<a class="ci-mail"href="mailto:%1$s" title="%1$s"><i class="fas fa-envelope"></i><span>%1$s<span></a>', $_email );
        }

        if ( ! empty( $_contact_info  ) ) {
            $model[ 'contact_info' ] = array_map( 'czr_fn_li_wrap', $_contact_info );
        }

        return parent::czr_fn_extend_params( $model );
    }

}
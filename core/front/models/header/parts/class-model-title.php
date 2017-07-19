<?php
class CZR_title_model_class extends CZR_Model {

    public $title_class;

    /**
    * @override
    */
    function __construct( $model = array() ) {
        parent::__construct( $model );

        $this -> title_class         = 1 == czr_fn_opt( 'tc_header_title_underline' ) ? ' czr-underline' : '';
        $this -> element_class       = apply_filters( 'czr_logo_class', '' );
    }


    /*
    * Custom CSS
    */
    function czr_fn_user_options_style_cb( $_css ) {
        //title shrink
        if ( 0 != esc_attr( czr_fn_opt( 'tc_sticky_header') ) && 0 != esc_attr( czr_fn_opt( 'tc_sticky_shrink_title_logo') ) ) {
            $_css = sprintf("%s%s", $_css,
              "
              .sticky-enabled .czr-shrink-on .navbar-brand-sitename {
                font-size: 0.8em;
                opacity: 0.8;
              }");
        }
        return $_css;
    }

}
<?php
class CZR_topbar_wrapper_model_class extends CZR_Model {
    public $social_block_class;


    /**
    * @override
    * fired before the model properties are parsed in the constructor
    *
    * return model params array()
    */
    function czr_fn_extend_params( $model = array() ) {
        /*
        * social block class
        * 3 cases:
        * 1) do not display both in mobile and desktop
        * 2) display in mobile and not in desktop
        * 3) display in desktop and not in mobile
        * 4) display in mobile and desktop
        */
        $_socials_visibility = esc_attr( czr_fn_opt( 'tc_header_show_socials' ) );

        switch ( $_socials_visibility ) :
          case 'desktop' : $_social_block_class = array( 'd-none', 'd-lg-block' );
                           break;
          case 'mobile'  : $_social_block_class = array( 'd-lg-none' );
                           break;
          case 'none'    : $_social_block_class = array( 'd-none' );
                           break;
          default        : $_social_block_class = array();
        endswitch;


        $model = array_merge( $model, array(
            'social_block_class' => $_social_block_class
        ) );


        return parent::czr_fn_extend_params( $model );
    }

}
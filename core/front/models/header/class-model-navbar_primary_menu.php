<?php
class CZR_navbar_primary_menu_model_class extends CZR_menu_model_class {

      /*
      * @override
      */
      function czr_fn_get_preset_model() {

            $_preset        = parent::czr_fn_get_preset_model();

            $_menu_position = esc_attr( czr_fn_get_opt( 'tc_primary_menu_position') );

            switch ( $_menu_position ) {

              case 'pull-menu-center' :
                    $menu_position_class = 'justify-content-center';
                    break;

              case 'pull-menu-left' :
                    $menu_position_class = 'justify-content-start';
                    break;

              default :
                    $menu_position_class = 'justify-content-end';
            }

            $_this_preset = array(

                'element_class'       =>  array( 'primary-nav__menu-wrapper_new', $menu_position_class ),
                'theme_location'      => 'main',
                'menu_id'             => 'primary',
                'menu_class'          => array( 'primary-nav__menu_new', 'regular', 'navbar-nav', 'nav__menu' ),

            );

            return array_merge( $_preset, $_this_preset );

      }

}//end class
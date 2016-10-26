<?php
class CZR_navbar_wrapper_model_class extends CZR_Model {

  function czr_fn_setup_children() {
        $children = array(

          //MOBILE TAGLINE
          //array(  'id' => 'mobile_tagline',  'model_class' => array( 'parent' => 'header/tagline', 'name' => 'header/tagline_mobile') ),
          //NAVBAR
          /* Registered as child here as it needs to filter the body class (the header itself is registered in init)
          * and its id is used to add further CSS classe by other elements
          */
//          array( 'model_class' => 'header/navbar_wrapper', 'id' => 'navbar_wrapper' ),
          //socialblock in navbar
          // array( 'model_class' => array( 'parent' => 'modules/social_block', 'name' => 'header/header_social_block' ), 'id' => 'header_social_block', 'controller' => 'social_block' ),
          //tagline in navbar

          //menu in navbar
          array( 'id' => 'navbar_menu', 'model_class' => array( 'parent' => 'header/menu', 'name' => 'header/regular_menu' ) ),

          //sidenav navbar menu button
      //    array( 'id' => 'sidenav_navbar_menu_button', 'model_class' => array( 'parent' => 'header/menu_button', 'name' => 'header/sidenav_menu_button' ) ),
          /* Registered as child here as it needs to filter the body class class and the logos to add custom styles */
      //    array( 'id' => 'sidenav', 'model_class' => 'header/sidenav' ), //<= rendered in page_wrapper template now
        /*
          //second_menu help block
          array(
            'hook'        => '__after_sidenav_navbar_menu_button',
            'template'    => 'modules/help_block',
            'id'          => 'second_menu_help_block',
            'model_class' => array( 'parent' => 'modules/help_block', 'name' => 'modules/second_menu_help_block'),
            'priority'    => 40
          ),
          //main menu help block
          array(
            'hook'        => '__after_mobile_menu_button',
            'template'    => 'modules/help_block',
            'id'          => 'main_menu_help_block',
            'model_class' => array( 'parent' => 'modules/help_block', 'name' => 'modules/main_menu_help_block'),
            'priority'    => 40
          )
          */
        );
        return $children;
  }

}
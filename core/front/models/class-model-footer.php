<?php
class TC_footer_model_class extends TC_Model {

  function tc_setup_children() {
    $children = array(
      //sticky footer
      array( 'hook' => 'after_render_view_main_container', 'template' => 'footer/footer_push', 'priority' => 100 ),

      //widget area in footer
      array( 'hook' => '__footer__', 'id' => 'footer_widgets_wrapper', 'template' => 'modules/widget_area_wrapper', 'model_class' => array( 'parent' => 'modules/widget_area_wrapper', 'name' => 'footer/footer_widgets_area_wrapper' ) ),

      //footer one wrapper and widget area
      array( 'hook' => '__widget_area_footer__', 'id' => 'footer_one', 'priority' => '10', 'template' => 'modules/widget_area', 'model_class' => 'footer/footer_widget_area_wrapper'),

      //footer two wrapper and widget area
      array( 'hook' => '__widget_area_footer__', 'id' => 'footer_two', 'priority' => '20', 'template' => 'modules/widget_area', 'model_class' => 'footer/footer_widget_area_wrapper' ),

      //footer three wrapper and widget area
      array( 'hook' => '__widget_area_footer__', 'id' => 'footer_three', 'priority' => '20', 'template' => 'modules/widget_area', 'model_class' => 'footer/footer_widget_area_wrapper'),

      //colophon
      array( 'hook' => '__footer__', 'template' => 'footer/colophon', 'priority' => 100 ),

      //footer social
      array( 'model_class' => array( 'parent' => 'modules/social_block', 'name' => 'footer/footer_social_block' ), 'id' => 'footer_social_block', 'controller' => 'social_block' ),

      //footer credits
      array( 'hook' => '__colophon_two__', 'template' => 'footer/footer_credits' ),
      //footer colophon btt link

      //btt arrow
      array( 'hook' => '__after_page_wrapper', 'template' => 'footer/btt_arrow')
    );

    return $children;
  }
}//end of class

<?php
if(!function_exists('tc_widgets_init')) :
add_action( 'widgets_init', 'tc_widgets_init' );
  /**
  * 
  * Registers the widget areas
  * @package Customizr
  * @since Customizr 1.0 
  */
  function tc_widgets_init() {

  //POST & PAGES SIDEBARS
    register_sidebar( array(
      'name' => __( 'Right Sidebar', 'customizr' ),
      'id' => 'right',
      'description' => __( 'Appears on posts, static pages, archives and search pages', 'customizr' ),
      'before_widget' => '<aside id="%1$s" class="widget %2$s">',
      'after_widget' => '</aside>',
      'before_title' => '<h3 class="widget-title">',
      'after_title' => '</h3>',
    ) );

    register_sidebar( array(
      'name' => __( 'Left Sidebar', 'customizr' ),
      'id' => 'left',
      'description' => __( 'Appears on posts, static pages, archives and search pages', 'customizr' ),
      'before_widget' => '<aside id="%1$s" class="widget %2$s">',
      'after_widget' => '</aside>',
      'before_title' => '<h3 class="widget-title">',
      'after_title' => '</h3>',
    ) );


   //FOOTER WIDGET AREAS
     register_sidebar( array(
      'name' => __( 'Footer Widget Area One', 'customizr' ),
      'id' => 'footer_one',
      'description' => __( 'Just use it as you want !', 'customizr' ),
      'before_widget' => '<aside id="%1$s" class="widget %2$s">',
      'after_widget' => '</aside>',
      'before_title' => '<h3 class="widget-title">',
      'after_title' => '</h3>',
    ) );
    register_sidebar( array(
      'name' => __( 'Footer Widget Area Two', 'customizr' ),
      'id' => 'footer_two',
      'description' => __( 'Just use it as you want !', 'customizr' ),
      'before_widget' => '<aside id="%1$s" class="widget %2$s">',
      'after_widget' => '</aside>',
      'before_title' => '<h3 class="widget-title">',
      'after_title' => '</h3>',
    ) );
    register_sidebar( array(
      'name' => __( 'Footer Widget Area Three', 'customizr' ),
      'id' => 'footer_three',
      'description' => __( 'Just use it as you want !', 'customizr' ),
      'before_widget' => '<aside id="%1$s" class="widget %2$s">',
      'after_widget' => '</aside>',
      'before_title' => '<h3 class="widget-title">',
      'after_title' => '</h3>',
    ) );
  }
endif;



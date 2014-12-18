<?php
/**
* Menu action
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.0
* @author       Nicolas GUILLAUME <nicolas@themesandco.com>
* @copyright    Copyright (c) 2013, Nicolas GUILLAUME
* @link         http://themesandco.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_menu' ) ) :
  class TC_menu {
      static $instance;
      function __construct () {
          self::$instance =& $this;
          //body > header > navbar action ordered by priority
          add_action ( '__navbar'                   , array( $this , 'tc_menu_display' ), 30, 1);
          add_filter ( 'wp_page_menu'               , array( $this , 'tc_add_menuclass' ));

          //Set menu customizer options (since 3.2.0)
          add_action ( 'init'                       , array( $this , 'tc_set_menu_options') );
      }





      /*
      * Callback of init hook
      *
      * @package Customizr
      * @since Customizr 3.2.0
      */
      function tc_set_menu_options() {
        if ( 1 != esc_attr( tc__f( '__get_option' , 'tc_display_boxed_navbar') ) )
          add_filter( 'body_class'                  , array( $this, 'tc_set_no_navbar' ) );
        add_filter( 'tc_social_header_block_class'  , array( $this, 'tc_set_social_header_class') );

        //add a 100% wide container just after the sticky header to reset margin top
        if ( 1 == esc_attr( tc__f( '__get_option' , 'tc_sticky_header' ) ) )
          add_action( '__after_header'                , array( $this, 'tc_reset_margin_top_after_sticky_header'), 0 );
        add_filter( 'tc_navbar_wrapper_class'       , array( $this, 'tc_set_menu_style_options'), 0 );
      }



      /*
      * Set menu class position
      * Callback of tc_navbar_wrapper_class hook
      *
      * @package Customizr
      * @since Customizr 3.2.0
      */
      function tc_set_menu_style_options( $_classes ) {
        $_classes = ( ! wp_is_mobile() && 0 != esc_attr( tc__f( '__get_option', 'tc_menu_submenu_fade_effect') ) ) ? array_merge( $_classes, array( 'tc-submenu-fade' ) ) : $_classes;
        $_classes = ( 0 != esc_attr( tc__f( '__get_option', 'tc_menu_submenu_item_move_effect') ) ) ? array_merge( $_classes, array( 'tc-submenu-move' ) ) : $_classes;
        $_classes = ( ! wp_is_mobile() && 'hover' == esc_attr( tc__f( '__get_option' , 'tc_menu_type' ) ) ) ? array_merge( $_classes, array( 'tc-open-on-hover' ) ) : array_merge( $_classes, array( 'tc-open-on-click' ) );
        return array_merge( $_classes, array(esc_attr( tc__f( '__get_option', 'tc_menu_position') ) ) );
      }



      /*
      * Callback of body_class hook
      *
      * @package Customizr
      * @since Customizr 3.2.0
      */
      function tc_set_no_navbar($_classes) {
        $_classes[] = 'no-navbar';
        return $_classes;
      }



      /*
      * Callback of tc_social_header_block_class hook
      *
      * @package Customizr
      * @since Customizr 3.2.0
      */
      function tc_set_social_header_class($_classes) {
        return 'span5';
      }


      /*
      * Callback of __after_header hook
      *
      * @package Customizr
      * @since Customizr 3.2.0
      */
      function tc_reset_margin_top_after_sticky_header() {
        echo apply_filters(
          'tc_reset_margin_top_after_sticky_header',
          sprintf('<div id="tc-reset-margin-top" class="container-fluid" style="margin-top:%1$spx"></div>',
            apply_filters('tc_default_sticky_header_height' , 103 )
          )
        );
      }



      /**
      * Menu fallback. Link to the menu editor.
      * Thanks to tosho (http://wordpress.stackexchange.com/users/73/toscho)
      * http://wordpress.stackexchange.com/questions/64515/fall-back-for-main-menu
      *
      * @package Customizr
      * @since Customizr 1.0
      */
      function tc_link_to_menu_editor( $args ) {
        if ( ! current_user_can( 'manage_options' ) )
            return;

        // see wp-includes/nav-menu-template.php for available arguments
        extract( $args );

        $link = sprintf('%1$s<a href="%2$s">%3$s%4$s%5$s</a>%6$s',
          $link_before,
          admin_url( 'nav-menus.php' ),
          $before,
          __('Add a menu','customizr'),
          $after,
          $link_after
        );

        // We have a list
        $link = ( FALSE !== stripos( $items_wrap, '<ul' ) || FALSE !== stripos( $items_wrap, '<ol' ) ) ? '<li>' . $link . '</li>' : $link;

        $output = sprintf( $items_wrap, $menu_id, $menu_class, $link );
        $output = ( ! empty ( $container ) ) ? sprintf('<%1$s class="%2$s" id="%3$s">%4$s</%1$s>',
                                                  $container,
                                                  $container_class,
                                                  $container_id,
                                                  $output
                                              ) : $output;

        if ( $echo ) { echo $output; }
        return $output;
      }


      /**
      * Menu Rendering
      *
      * @package Customizr
      * @since Customizr 3.0
      */
      function tc_menu_display($resp = null) {
        ob_start();
          //renders the responsive button
          if ( 'resp' == $resp ) { //resp is an argument of do_action ('__navbar' , 'resp')
            $button = sprintf('<button type="button" class="%1$s" data-toggle="collapse" data-target=".nav-collapse">%2$s%2$s%2$s</button>',
              apply_filters( 'tc_menu_button_class', 'btn btn-navbar' ),
              '<span class="icon-bar"></span>'
            );
            echo apply_filters( 'resp_menu_button', $button );
          }

          //renders the menu
          $menu_args = apply_filters( 'tc_menu_args',
                      array(
                        'theme_location'  => 'main',
                        'menu_class'      => ( ! wp_is_mobile() && 'hover' == esc_attr( tc__f( '__get_option' , 'tc_menu_type' ) ) ) ? 'nav tc-hover-menu' : 'nav',
                        'fallback_cb'     => array( $this , 'tc_link_to_menu_editor' ),
                        'walker'          => TC_nav_walker::$instance,
                        'echo'            => false,
                    )
          );
          $menu_wrapper_class   = ( ! wp_is_mobile() && 'hover' == esc_attr( tc__f( '__get_option' , 'tc_menu_type' ) ) ) ? 'nav-collapse collapse tc-hover-menu-wrapper' : 'nav-collapse collapse';
          printf('<div class="%1$s">%2$s</div>',
              apply_filters( 'tc_menu_wrapper_class', $menu_wrapper_class ),
              wp_nav_menu( $menu_args )
          );

        $html = ob_get_contents();
        if ($html) ob_end_clean();
        echo apply_filters( 'tc_menu_display', $html, $resp );
      } //end of funtion()





      /**
      * Adds a specific class to the ul wrapper
      *
      * @package Customizr
      * @since Customizr 3.0
      */
      function tc_add_menuclass( $ulclass) {
        $html =  preg_replace( '/<ul>/' , '<ul class="nav">' , $ulclass, 1);
        return apply_filters( 'tc_add_menuclass', $html );
      }
  }//end of class
endif;

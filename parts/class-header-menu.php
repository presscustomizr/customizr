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

class TC_menu {

    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;

    function __construct () {

        self::$instance =& $this;
        //body > header > navbar action ordered by priority
        add_action ( '__navbar'                            , array( $this , 'tc_menu_display' ), 30, 1);

        add_filter ( 'wp_page_menu'                        , array( $this , 'tc_add_menuclass' ));
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
                      'menu_class'      => ( 'hover' == esc_attr( tc__f( '__get_option' , 'tc_menu_type' ) ) ) ? 'nav tc-hover-menu' : 'nav', 
                      'fallback_cb'     => array( $this , 'tc_link_to_menu_editor' ), 
                      'walker'          => TC_nav_walker::$instance,
                      'echo'            => false,
                  )
        );
        $menu_wrapper_class   = ( 'hover' == esc_attr( tc__f( '__get_option' , 'tc_menu_type' ) ) ) ? 'nav-collapse collapse tc-hover-menu-wrapper' : 'nav-collapse collapse';
        printf('<div class="%1$s">%2$s</div>',
            apply_filters( 'menu_wrapper_class', $menu_wrapper_class ),
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

}
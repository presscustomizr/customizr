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
        add_action ( '__navbar'                            , array( $this , 'tc_menu_display' ), 30);

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
        {
            return;
        }
        // see wp-includes/nav-menu-template.php for available arguments
        extract( $args );

        $link = $link_before
            . '<a href="' .admin_url( 'nav-menus.php' ) . '">' . $before . __('Add a menu','customizr') . $after . '</a>'
            . $link_after;

        // We have a list
        if ( FALSE !== stripos( $items_wrap, '<ul' )
            or FALSE !== stripos( $items_wrap, '<ol' )
        )
        {
            $link = "<li>$link</li>";
        }

        $output = sprintf( $items_wrap, $menu_id, $menu_class, $link );
        if ( ! empty ( $container ) )
        {
            $output  = "<$container class='$container_class' id='$container_id'>$output</$container>";
        }

        if ( $echo )
        {
            echo $output;
        }

        return $output;
      }


    /**
    * Menu Rendering
    *
    * @package Customizr
    * @since Customizr 3.0
    */
    function tc_menu_display() {

      tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
      ?>

      <?php ob_start() ?>
      <div class="navbar notresp span9 pull-left">

              <div class="navbar-inner" role="navigation">
              <?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ ); ?>
                  <div class="row-fluid">

                    <div class="social-block span5"><?php do_action( '__social' , 'tc_social_in_header' ) ?></div>

                    <h2 class="span7 inside site-description">
                      <?php bloginfo( 'description' ); ?></h2>
                  </div>

                  <div class="nav-collapse collapse">
                    <?php wp_nav_menu( array( 'theme_location' => 'main' , 'menu_class' => 'nav' , 'fallback_cb' => array( $this , 'tc_link_to_menu_editor' ), 'walker' => tc__ ( 'header' , 'nav_walker' )) );  ?>
                  
                  </div><!-- /.nav-collapse collapse -->

              </div><!-- /.navbar-inner -->

          </div><!-- /.navbar notresp -->

          <div class="navbar resp">
        
              <div class="navbar-inner" role="navigation">
                <?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ ); ?>
                  <div class="social-block"><?php do_action( '__social' , 'tc_social_in_header' ) ?></div>

                      <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                          <span class="icon-bar"></span>
                          <span class="icon-bar"></span>
                          <span class="icon-bar"></span>
                      </button>

                 <div class="nav-collapse collapse">
                      <?php wp_nav_menu( array( 'theme_location' => 'main' , 'menu_class' => 'nav' , 'fallback_cb' => array( $this , 'tc_link_to_menu_editor' ) , 'walker' => tc__ ( 'header' , 'nav_walker' )) );  ?>
                 </div><!-- /.nav-collapse collapse -->

              </div><!-- /.navbar-inner -->
              
          </div><!-- /.navbar resp -->
      <?php
      $html = ob_get_contents();
      ob_end_clean();
      echo apply_filters( 'tc_render_menu', $html );
    } //end of render_menu()





    /**
    * Adds a specific class to the ul wrapper
    *
    * @package Customizr
    * @since Customizr 3.0
    */
    function tc_add_menuclass( $ulclass) {
      tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
      $html =  preg_replace( '/<ul>/' , '<ul class="nav">' , $ulclass, 1);
      return apply_filters( 'tc_add_menuclass', $html );
    }

}
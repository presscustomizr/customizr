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

    function __construct () {
        add_action( '__menu'                       , array( $this , 'tc_render_menu' ));
    }


    /**
      * Menu fallback. Link to the menu editor.
      * Thanks to tosho (http://wordpress.stackexchange.com/users/73/toscho)
      * http://wordpress.stackexchange.com/questions/64515/fall-back-for-main-menu
      *
      * @package Customizr
      * @since Customizr 1.0
     */
      function tc_link_to_menu_editor( $args )
      {
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
    function tc_render_menu() {
      ?>
      <div class="navbar notresp span9 pull-left">

              <div class="navbar-inner" role="navigation">

                  <div class="row-fluid">

                    <div class="social-block span5"><?php do_action( '__social' , 'tc_social_in_header' ) ?></div>

                    <h2 class="span7 inside site-description"><?php bloginfo( 'description' ); ?></h2>
                  </div>

                  <div class="nav-collapse collapse">

                    <?php wp_nav_menu( array( 'theme_location' => 'main' , 'menu_class' => 'nav' , 'fallback_cb' => array( $this , 'tc_link_to_menu_editor' ), 'walker' => tc__ ( 'header' , 'nav_walker' )) );  ?>
                  
                  </div><!-- /.nav-collapse collapse -->

              </div><!-- /.navbar-inner -->

          </div><!-- /.navbar notresp -->

          <div class="navbar resp">

              <div class="navbar-inner" role="navigation">

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
    } //end of render_menu()

}
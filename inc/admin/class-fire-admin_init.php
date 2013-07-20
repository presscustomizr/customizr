<?php
/**
* Init admin actions
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

class TC_admin_init {

    function __construct () {
         global $wp_version;
          //check WP version to include customizer functions, must be >= 3.4
         if (version_compare( $wp_version, '3.4' , '>=' ) ) {

              //require_once( TC_BASE.'inc/admin/tc_customize.php' );

            tc__( 'admin' , 'customize' );

          }

          else {
              //redirect to an upgrade message page on activation if version < 3.4
              add_action ( 'admin_menu'                      , array( $this , 'tc_add_fallback_page' ));
              add_action ( 'admin_init'                      , array( $this , 'tc_theme_activation_fallback' ));
          }
          
          //loads meta boxes
          tc__( 'admin' , 'meta_boxes' );

          //Redirect on activation (first activation only)
          add_action ( 'admin_init'                          , array( $this , 'tc_theme_activation' ));

    }



   /**
    *  On activation, redirect on an upgrade WordPress page if version < 3.4
    * @package Customizr
    * @since Customizr 1.1
    */
    function tc_theme_activation_fallback()  {
        global $pagenow;
        if ( is_admin() && 'themes.php' == $pagenow && isset( $_GET['activated'] ) ) 
        {
          #redirect to options page
          header( 'Location: '.admin_url().'themes.php?page=upgrade_wp.php' ) ;
        }
    }





    /**
     * Add fallback admin page.
     * @package Customizr
     * @since Customizr 1.1
     */
      function tc_add_fallback_page() {
          $theme_page = add_theme_page(
              __( 'Upgrade WP' , 'customizr' ),   // Name of page
              __( 'Upgrade WP' , 'customizr' ),   // Label in menu
              'edit_theme_options' ,          // Capability required
              'upgrade_wp.php' ,             // Menu slug, used to uniquely identify the page
              array( $this , 'tc_fallback_admin_page' )         //function to be called to output the content of this page
          );
      }




    /**
   * Render fallback admin page.
   * @package Customizr
   * @since Customizr 1.1
   */
    function tc_fallback_admin_page() {
      ?>
        <div class="wrap upgrade_wordpress">
          <div id="icon-options-general" class="icon32"><br></div>
          <h2><?php _e( 'This theme requires WordPress 3.4+' , 'customizr' ) ?> </h2>
          <br />
          <p style="text-align:center">
            <a style="padding: 8px" class="button-primary" href="<?php echo admin_url().'update-core.php' ?>" title="<?php _e( 'Upgrade Wordpress Now' , 'customizr' ) ?>">
            <?php _e( 'Upgrade Wordpress Now' , 'customizr' ) ?></a>
            <br /><br />
          <img src="<?php echo TC_BASE_URL . 'screenshot.png' ?>" alt="Customizr" />
          </p>
        </div>
      <?php
    }




  /**
  *  On activation, redirect on the customizer panel page
  * @package Customizr
  * @since Customizr 1.0
  */
  function tc_theme_activation()
  {
    global $pagenow;
    if ( is_admin() && 'themes.php' == $pagenow && isset( $_GET['activated'] ) ) 
    {
      #set frontpage to display_posts
      //update_option( 'show_on_front' , 'posts' );

      #set max number of posts to 10
      //update_option( 'posts_per_page' , 10);

      #redirect to Customizer page
      //do we activate customizr for the first time? => tc_theme_options is not set in options table
      $__options = get_option( 'tc_theme_options' );//return false if not defined
      if (!$__options) {
        header( 'Location: '.admin_url().'customize.php' ) ;
      }

    }
  }

}//end of class       
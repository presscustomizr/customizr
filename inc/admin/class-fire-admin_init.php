<?php
/**
* Init admin actions : loads the meta boxes,
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
          
         //load the meta boxes
          add_action ( 'admin_init'                          , array( $this , 'tc_load_meta_boxes' ));

          //add welcome page in menu
          add_action ( 'admin_menu'                          , array( $this , 'tc_add_welcome_page' ));

          //Redirect on activation (first activation only)
          add_action ( 'admin_init'                          , array( $this , 'tc_theme_activation' ));

          //enqueue additional styling for admin screens
          add_action ( 'admin_init'                          , array( $this , 'tc_admin_style' ));

    }




    /**
     *  load the meta boxes for pages, posts and attachment
    * @package Customizr
    * @since Customizr 3.0.4
    */
    function tc_load_meta_boxes()  {
       //loads meta boxes
          tc__( 'admin' , 'meta_boxes' );
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
  *  On activation, redirect on the welcome panel page
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

      #redirect to welcome page
      header( 'Location: '.admin_url().'themes.php?page=welcome.php' ) ;

    }
  }


   /**
   * Add fallback admin page.
   * @package Customizr
   * @since Customizr 1.1
   */
    function tc_add_welcome_page() {
        $theme_page = add_theme_page(
            __( 'About Customizr' , 'customizr' ),   // Name of page
            __( 'About Customizr' , 'customizr' ),   // Label in menu
            'edit_theme_options' ,          // Capability required
            'welcome.php' ,             // Menu slug, used to uniquely identify the page
            array( $this , 'tc_welcome_panel' )         //function to be called to output the content of this page
        );
    }



    /**
   * Render welcome admin page.
   * @package Customizr
   * @since Customizr 3.0.4
   */
    function tc_welcome_panel() {

       $__options = get_option( 'tc_theme_options' );//return false if not defined

      ?>
      <div class="wrap about-wrap">

        <h1><?php printf( __( 'Welcome to Customizr %s','customizr' ), CUSTOMIZR_VER ); ?></h1>

        <?php  if (!$__options) : //do we activate customizr for the first time? ?>
          <div class="about-text tc-welcome"><?php printf( __( 'Thank you for updating to the latest version! Customizr %s is safer and more stable than ever to help you build an awesome website. Enjoy it!','customizr' ), CUSTOMIZR_VER ); ?></div>
        <?php else: ?>
          <div class="about-text tc-welcome"><?php printf( __( 'Thank you for using Customizr! Customizr %s is safer and more stable than ever to help you build an awesome website. Enjoy it!','customizr' ), CUSTOMIZR_VER ); ?></div>
        <?php endif; ?>

        <div class="changelog point-releases">
        </div>
       <div class="changelog">
         <div class="feature-section col three-col">

            <div>
              <h3><?php _e( 'We need coffee...','customizr' ); ?></h3>
              <p><?php  _e( 'Either you are using Customizr for personal or business purposes, <strong>we do our best do make it the perfect free theme for you</strong>.<br /> Any kind of sponsorship will be appreciated!','customizr' ) ?></br>

                <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8CTH6YFDBQYGU" target="_blank" rel="nofollow"><img class="tc-donate" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" alt="Make a donation for Customizr" /></a>
              </p>
            </div>

            <div>
              <h3><?php _e( 'Happy user of Customizr?','customizr' ); ?></h3>
              <p><?php _e( 'If you are happy with the theme, say it on wordpress.org and give Customizr a nice review!','customizr' ) ?></br></br>
              <a class="button-primary" title="Customizr WordPress Theme" href="http://wordpress.org/support/view/theme-reviews/customizr" target="_blank">Review Customizr &raquo;</a></p>
            </div>

            <div class="last-feature">
              <h3><?php _e( 'Follow us','customizr' ); ?></h3>
              <p class="tc-no-margin"><a href="http://www.themesandco.com" target="_blank"><img src="<?php echo TC_BASE_URL.'inc/admin/img/tc.png' ?>" alt="Themes and co" /></a></p>
              <!-- Place this tag where you want the widget to render. -->
              <div class="g-follow" data-annotation="bubble" data-height="24" data-href="//plus.google.com/102674909694270155854" data-rel="author"></div>

              <!-- Place this tag after the last widget tag. -->
              <script type="text/javascript">
                (function() {
                  var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                  po.src = 'https://apis.google.com/js/plusone.js';
                  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
                })();
              </script>
            </div>

        </div><!-- .feature-section -->
      </div><!-- .changelog -->

      <div class="changelog">

        <h3><?php _e( 'Discover Customizr : quick video introduction' , 'customizr' ); ?></h3>

          <iframe width="853" height="480" src="//www.youtube.com/embed/Hj7lGnZgwQs" frameborder="0" allowfullscreen></iframe>
      
      </div>

      <div class="return-to-dashboard">
        <a href="<?php echo esc_url( self_admin_url() ); ?>"><?php
          is_blog_admin() ? _e( 'Go to Dashboard &rarr; Home','customizr' ) : _e( 'Go to Dashboard','customizr' ); ?></a>
      </div>

    </div>
    <?php
    }



    /**
   * enqueue additional styling for admin screens
   * @package Customizr
   * @since Customizr 3.0.4
   */
    function tc_admin_style() {
       wp_enqueue_style( 'admincss' , TC_BASE_URL.'inc/admin/css/tc_admin.css' );
    }


}//end of class       
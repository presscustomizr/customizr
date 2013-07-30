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

          add_filter('upgrader_post_install'                 , array( $this , 'tc_redirect_after_update' ), 1000);

          remove_filter('upgrader_post_install'              , array( $this , 'tc_redirect_after_update' ), 1100);

          //changelog
          add_action ( 'changelog'                           , array( $this , 'tc_extract_changelog' ));
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
      
      $is_upgrade = false;

      if ( isset($_GET['action']) ) {
        if ( $_GET['action'] == 'customizr-update' ) {
          $is_upgrade = true;
        }
      }

      ?>
      <div class="wrap about-wrap">

        <h1><?php printf( __( 'Welcome to Customizr %s','customizr' ), CUSTOMIZR_VER ); ?></h1>

        <?php  if ($is_upgrade) : ?>

          <div class="about-text tc-welcome"><?php printf( __( 'Thank you for updating to the latest version! Customizr %1$s has more features, is safer and more stable than ever <a href="#customizr-changelog">(see changelog)</a> to help you build an awesome website. Enjoy it! ','customizr' ), CUSTOMIZR_VER   ); ?><a class="twitter-share-button" href="http://twitter.com/share" data-url="http://www.themesandco.com/customizr/" data-text="I just upgraded my WordPress site with the Customizr Theme version <?php echo CUSTOMIZR_VER?>!">Tweet it!</a></div>
        
        <?php else: ?>

          <div class="about-text tc-welcome"><?php printf( __( 'Thank you for using Customizr! Customizr %1$s has more features, is safer and more stable than ever <a href="#customizr-changelog">(see changelog)</a> to help you build an awesome website. Enjoy it! ','customizr' ), CUSTOMIZR_VER ); ?><a class="twitter-share-button" href="http://twitter.com/share" data-url="http://www.themesandco.com/customizr/" data-text="My WordPress website is built with the Customizr Theme version <?php echo CUSTOMIZR_VER ?>!">Tweet it!</a></div>
        
        <?php endif; ?>

        <div id="tweetBtn">
            <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
        </div>

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
              <p><?php _e( 'If you are happy with the theme, say it on wordpress.org and give Customizr a nice review! <br />(We are addicted to your feedbacks...)','customizr' ) ?></br>
              <a class="button-primary review-customizr" title="Customizr WordPress Theme" href="http://wordpress.org/support/view/theme-reviews/customizr" target="_blank">Review Customizr &raquo;</a></p>
            </div>

            <div class="last-feature">
              <h3><?php _e( 'Follow us','customizr' ); ?></h3>
              <p class="tc-follow"><a href="http://www.themesandco.com" target="_blank"><img src="<?php echo TC_BASE_URL.'inc/admin/img/tc.png' ?>" alt="Themes and co" /></a></p>
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
          
          <div style="text-align:center">
            <iframe width="853" height="480" src="//www.youtube.com/embed/Hj7lGnZgwQs" frameborder="0" allowfullscreen></iframe>
          </div>

      </div>

      <div id="customizr-changelog" class="changelog">
        
        <h3><?php printf( __( 'Changelog in version %1$s' , 'customizr' ) , CUSTOMIZR_VER ); ?></h3>

          <p><?php do_action('changelog'); ?></p>
      
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




  /**
   * Redirect after update of Customizr
   * @package Customizr
   * @since Customizr 3.0.5
   */
    function tc_redirect_after_update() {
      //check context
      if ( !isset($_GET['action']) ) {
        return false;
      }
      
      if ( !isset($_GET['theme']) ) {
        return false;
      }

      //is_customizr_upgrade ?
      if ( $_GET['action'] == 'upgrade-theme' && $_GET['theme'] == 'customizr') {
        show_message( '<span class="hide-if-no-js">' . sprintf( __( 'Welcome to the new version of Customizr. You will be redirected to the About WordPress screen. If not, click <a href="%1$s">here</a>.' ), esc_url( self_admin_url( 'themes.php?page=welcome.php&action=customizr-update' ) ) ) . '</span>' );
        ?>
          <script type="text/javascript">
          window.location = '<?php echo self_admin_url( 'themes.php?page=welcome.php&action=customizr-update' ); ?>';
          </script>
        <?php
      }

    }




    /**
   * Extract changelog of latest version from readme.txt file
   * @package Customizr
   * @since Customizr 3.0.5
   */
    function tc_extract_changelog() {
      
      $stylelines = explode("\n", implode('', file(TC_BASE_URL."/readme.txt")));
      $read = false;
      $i = 0;

      foreach ($stylelines as $line) {
        //echo 'i = '.$i.'|read = '.$read.'pos = '.strpos($line, '= ').'|line :'.$line.'<br/>';
        //we stop reading if we reach the next version change
        if ($i == 1 && strpos($line, '= ') === 0 ) {
          $read = false;
          $i = 0;
        }
        //we write the line if between current and previous version
        if ($read) {
          echo $line.'<br/>';
        }
        //we skip all lines before the current version changelog
        if ($line != strpos($line, '= '.CUSTOMIZR_VER.' =')) {
          if ($i == 0) {
            $read = false;
          }
        }
        //we begin to read after current version title
        else {
          $read = true;
          $i = 1;
        }
      }
    }

}//end of class       
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

    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;

    function __construct () {

      self::$instance =& $this;

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

        //add help button to admin bar
        add_action ( 'wp_before_admin_bar_render'          , array( $this , 'tc_add_help_button' ));

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
     * Add help button
     * @package Customizr
     * @since Customizr 1.0 
     */
    function tc_add_help_button() {
       if ( current_user_can( 'edit_theme_options' ) ) {
         global $wp_admin_bar;
         $wp_admin_bar->add_menu( array(
           'parent' => 'top-secondary', // Off on the right side
           'id' => 'tc-customizr-help' ,
           'title' =>  __( 'Help' , 'customizr' ),
           'href' => admin_url( 'themes.php?page=welcome.php&help=true' ),
           'meta'   => array(
              'title'  => __( 'Need help with Customizr? Click here!', 'customizr' ),
            ),
         ));
       }
    }



    /**
   * Render welcome admin page.
   * @package Customizr
   * @since Customizr 3.0.4
   */
    function tc_welcome_panel() {
      
      //CHECK IF WE ARE UPGRADING
      $is_upgrade = false;

      if ( isset($_GET['action']) ) {
        if ( $_GET['action'] == 'customizr-update' ) {
          $is_upgrade = true;
        }
      }

      $is_help = isset($_GET['help'])  ?  true : false;

      //CHECK IF WE ARE USING A CHILD THEME
      $is_child                     = tc_is_child();

      ?>
      <div class="wrap about-wrap">
        <?php  ?>
        
          <?php if ($is_help) : ?>
            <h1 class="need-help-title"><?php _e( 'Need help with Customizr ?','customizr' ) ?></h1>
          <?php else : ?>
            <h1><?php printf( __( 'Welcome to Customizr %s','customizr' ), CUSTOMIZR_VER ); ?></h1>
          <?php endif; ?>

        <?php  if ($is_upgrade) : ?>

          <div class="about-text tc-welcome">
            <?php printf( __( 'Thank you for updating to the latest version! Customizr %1$s has more features, is safer and more stable than ever <a href="#customizr-changelog">(see changelog)</a> to help you build an awesome website. Watch the <a href="#introduction">introduction video</a> and find inspiration in the <a href="#showcase">showcase</a>.<br/> Enjoy it! ','customizr' ),
            CUSTOMIZR_VER
            ); ?>
            <a class="twitter-share-button" href="http://twitter.com/share" data-url="<?php echo TC_WEBSITE ?>customizr/" data-text="I just upgraded my WordPress site with the #Customizr Theme version <?php echo CUSTOMIZR_VER?>!">Tweet it!</a>
          </div>
        
        <?php elseif ($is_help) : ?>
          <div class="changelog">
            <div class="about-text tc-welcome">
            <?php printf( __( 'You can start by watching the <a href="#introduction">introduction video</a> or by reading <a href="%1$scustomizr" target="_blank">the documentation</a>.<br/> If you don\'t find an answer to your issue, don\'t panic! Since Customizr is used by a growing community of webmasters reporting bugs and making continuous improvements, you will probably find a solution to your problem either in the FAQ or in the user forum.','customizr' ),
             TC_WEBSITE
             ); ?>
             </div>
            <div class="feature-section col three-col">
              <div>
                 <br/>
                  <a class="button-secondary customizr-help" title="documentation" href="<?php echo TC_WEBSITE ?>customizr" target="_blank"><?php _e( 'Read the documentation','customizr' ); ?></a>
              </div>
              <div>
                <br/>
                  <a class="button-secondary customizr-help" title="faq" href="<?php echo TC_WEBSITE ?>customizr/faq" target="_blank"><?php _e( 'Check the FAQ','customizr' ); ?></a>
               </div>
               <div class="last-feature">
                <br/>
                  <a class="button-secondary customizr-help" title="forum" href="http://wordpress.org/support/theme/customizr" target="_blank"><?php _e( 'Discuss in the user forum','customizr' ); ?></a>
               </div>
            </div><!-- .two-col -->
          </div><!-- .changelog -->
        
        <?php else: ?>
        
          <div class="about-text tc-welcome">
            <?php printf( __( 'Thank you for using Customizr! Customizr %1$s has more features, is safer and more stable than ever <a href="#customizr-changelog">(see the changelog)</a> to help you build an awesome website. Watch the <a href="#introduction">introduction video</a> and find inspiration in the <a href="#showcase">showcase</a>.<br/>Enjoy it! ','customizr' ),
             CUSTOMIZR_VER 
             ); ?>
             <a class="twitter-share-button" href="http://twitter.com/share" data-url="<?php echo TC_WEBSITE ?>customizr/" data-text="My WordPress website is built with the #Customizr Theme version <?php echo CUSTOMIZR_VER ?>!">Tweet it!</a></div>
        
        <?php endif; ?>

        <div id="tweetBtn">
            <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
        </div>

        <?php if ($is_child) : ?>
          <div class="changelog point-releases"></div>

          <div class="tc-upgrade-notice">
            <p>
            <?php 
              printf( __('You are using a child theme of Customizr %1$s : always check the %2$s after upgrading to see if a function or a template has been deprecated.' , 'customizr'),
                'v'.CUSTOMIZR_VER,
                '<strong><a href="#customizr-changelog">changelog</a></strong>'
                ); 
              ?>
            </p>
          </div>
        <?php endif; ?>

        <div class="changelog point-releases"></div>

       <div class="changelog">
         <div class="feature-section col three-col">

            <div>
              <h3><?php _e( 'We need sponsors!','customizr' ); ?></h3>
              <p><?php  _e( '<strong>We do our best do make Customizr the perfect free theme for you!</strong><br/> Please help support it\'s continued development with a donation of $20, $50, or even $100.','customizr' ) ?></br>

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
              <p class="tc-follow"><a href="<?php echo TC_WEBSITE ?>" target="_blank"><img src="<?php echo TC_BASE_URL.'inc/admin/img/tc.png' ?>" alt="Themes and co" /></a></p>
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

      <div id="dev-box" class="changelog">
        <h3 style=""><?php _e('New! Customizr Developer Tools' ,'customizr') ?></h3>

        <div class="feature-section images-stagger-right">
          <img alt="Customizr developer tools" src="<?php echo TC_BASE_URL.'inc/admin/img/dev-box.jpg' ?>" class="">
          <h4><?php _e('Easily drill down into Customizr code with the dev tools!' ,'customizr') ?></h4>
          <p><?php _e('A new section called "Dev Tools" has been added to the customizer options.<br/> There you will find two optional new features : <br/><strong>-The developer box</strong> : this draggable box is a developer dashboard allowing you to have an overview of your theme settings (plugins, custom post types, theme options,...) and providing useful informations for debug and development : a loading timeline of any pages, contextual data ( like conditional tags and query), the hook\'s structure of the theme and a note section about the code logic of Customizr.<br/><strong>-The embedded tooltips</strong> : this option displays clickable (and draggable) contextual tooltips right inside your website. They help you understand which part of the php code handles any front-end block or element. The informations provided are : class -> method, hook, file, function description and possible filter.<br/><br/><i>Those tools are only displayed to logged in users with an admin capability profile.</i>' , 'customizr') ?>
          </p>
         
        </div>
      </div>

      <div id="showcase" class="changelog">
        <h3 style="text-align:right"><?php _e('Customizr Showcase' ,'customizr') ?></h3>

        <div class="feature-section images-stagger-left">
           <a class="" title="<?php _e('Visit the showcase','customizr') ?>" href="<?php echo TC_WEBSITE ?>customizr/showcase/" target="_blank"><img alt="Customizr Showcase" src="<?php echo TC_BASE_URL.'inc/admin/img/mu2.jpg' ?>" class=""></a>
          <h4 style="text-align: right"><?php _e('Find inspiration for your next Customizr based website!' ,'customizr') ?></h4>
          <p style="text-align: right"><?php _e('This showcase aims to show what can be done with Customizr and helping other users to find inspiration for their web design.' , 'customizr') ?>
          </p>
          <p style="text-align: right"><?php _e('Do you think you made an awesome website that can inspire people? Submitting a site for review is quick and easy to do.' , 'customizr') ?></br>
          </p>
          <p style="text-align:center">    
              <a class="button-primary review-customizr" title="<?php _e('Visit the showcase','customizr') ?>" href="<?php echo TC_WEBSITE ?>customizr/showcase/" target="_blank"><?php _e('Visit the showcase','customizr') ?> &raquo;</a>
          </p>
        </div>
      </div>

      <div id="introduction" class="changelog">

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
        show_message( '<span class="hide-if-no-js">' . sprintf( __( 'Welcome to the new version of Customizr. You will be redirected to the About screen. If not, click <a href="%1$s">here</a>.' ), esc_url( self_admin_url( 'themes.php?page=welcome.php&action=customizr-update' ) ) ) . '</span>' );
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
      
      if( !file_exists(TC_BASE."readme.txt") ) {
        return;
      }
      if( !is_readable(TC_BASE."readme.txt") ) {
        echo '<p>The changelog in readme.txt is not readable.</p>';
        return;
      }
      
      $stylelines = explode("\n", implode('', file(TC_BASE."readme.txt")));
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
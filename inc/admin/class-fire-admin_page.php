<?php
/**
* Init admin page actions : Welcome, help page
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.0
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_admin_page' ) ) :
  class TC_admin_page {
    static $instance;
    public $support_url;

    function __construct () {
      self::$instance =& $this;
      //add welcome page in menu
      add_action( 'admin_menu'             , array( $this , 'tc_add_welcome_page' ));
      //changelog
      add_action( '__after_welcome_panel'  , array( $this , 'tc_extract_changelog' ));
      //config infos
      add_action( '__after_welcome_panel'  , array( $this , 'tc_config_infos' ), 20 );
      //build the support url
      $this -> support_url = TC___::tc_is_pro() ? esc_url( sprintf('%ssupport' , TC_WEBSITE ) ) : esc_url('wordpress.org/support/theme/customizr');
      //fix #wpfooter absolute positioning in the welcome and about pages
      add_action( 'admin_print_styles'      , array( $this, 'tc_fix_wp_footer_link_style') );
    }



   /**
   * Add fallback admin page.
   * @package Customizr
   * @since Customizr 1.1
   */
    function tc_add_welcome_page() {
        $_name = __( 'About Customizr' , 'customizr' );
        $_name = TC___::tc_is_pro() ? sprintf( '%s Pro', $_name ) : $_name;

        $theme_page = add_theme_page(
            $_name,   // Name of page
            $_name,   // Label in menu
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

        $is_help        = isset($_GET['help'])  ?  true : false;
        $_faq_url       = esc_url('http://docs.presscustomizr.com/category/90-faq-and-common-issues');
        $_support_url   = $this -> support_url;
        $_theme_name    = TC___::tc_is_pro() ? 'Customizr Pro' : 'Customizr';

        do_action('__before_welcome_panel');

        ?>
        <div id="customizr-admin-panel" class="wrap about-wrap">
          <?php
            if ( $is_help ) {
              printf( '<h1 style="font-size: 2.5em;" class="need-help-title">%1$s %2$s ?</h1>',
                __( "Need help with", "customizr" ),
                $_theme_name
              );
            } else {
              printf( '<h1 class="need-help-title">%1$s %2$s %3$s</h1>',
                __( "Welcome to", "customizr" ),
                $_theme_name,
                CUSTOMIZR_VER
              );
            }
          ?>

          <?php if ( $is_help ) : ?>

            <div class="changelog">
              <div class="about-text tc-welcome">
              <?php
                printf( '<p>%1$s</p>',
                  sprintf( __( "The best way to start is to read the %s." , "customizr" ),
                    sprintf('<a href="%1$s" title="%2$s" target="_blank">%2$s</a>', esc_url('docs.presscustomizr.com'), __("documentation" , "customizr") )
                  )
                );
                printf( '<p>%1$s</p><p><strong>%2$s</strong></p>',
                  __( "If you don't find an answer to your issue in the documentation, don't panic! The Customizr theme is used by a growing community of webmasters reporting bugs and making continuous improvements. If you have a problem with the theme, chances are that it's already been reported and fixed in the support forums.", "customizr" ),
                  TC___::tc_is_pro() ? '' : sprintf( __( "The easiest way to search in the support forums is to use our Google powered search engine on our %s.", "customizr" ),
                    sprintf('<a href="%1$s" title="%2$s" target="_blank">%2$s</a>', esc_url('presscustomizr.com'), __("home page" , "customizr") )
                  )
                );
                ?>
              </div>
              <div class="feature-section col two-col">
                <div class="col">
                   <br/>
                    <a class="button-secondary customizr-help" title="documentation" href="<?php echo esc_url('docs.presscustomizr.com/') ?>" target="_blank"><?php _e( 'Read the documentation','customizr' ); ?></a>
                </div>
                <div class="last-feature col">
                  <br/>
                    <a class="button-secondary customizr-help" title="faq" href="<?php echo $_faq_url; ?>" target="_blank"><?php _e( 'Check the FAQ','customizr' ); ?></a>
                 </div>
              </div><!-- .two-col -->
              <div class="feature-section col two-col">
                 <div class="col">
                    <a class="button-secondary customizr-help" title="code snippets" href="<?php echo TC_WEBSITE ?>code-snippets/" target="_blank"><?php _e( 'Code snippets for developers','customizr' ); ?></a>
                </div>
                 <div class="last-feature col">
                    <a class="button-secondary customizr-help" title="help" href="<?php echo $_support_url; ?>" target="_blank">
                      <?php TC___::tc_is_pro() ? _e( 'Get support','customizr' ) : _e( 'Get help in the free support forum','customizr' ); ?>
                    </a>
                 </div>
              </div><!-- .two-col -->
            </div><!-- .changelog -->

          <?php else: ?>

            <div class="about-text tc-welcome">
              <?php
                printf( '<p><strong>%1$s %2$s <a href="#customizr-changelog">(%3$s)</a></strong></p>',
                  sprintf( __( "Thank you for using %s!", "customizr" ), $_theme_name ),
                  sprintf( __( "%s %s has more features, is safer and more stable than ever to help you designing an awesome website.", "customizr" ), $_theme_name, CUSTOMIZR_VER ),
                  __( "check the changelog", "customizr")
                );

                printf( '<p><strong>%1$s</strong></p>',
                  sprintf( __( "The best way to start with %s is to read the %s and visit the %s.", "customizr"),
                    $_theme_name,
                    sprintf( '<a href="%1$s" title="%2$s" target="_blank">%2$s</a>', esc_url('docs.presscustomizr.com'), __("documentation", "customizr") ),
                    sprintf( '<a href="%1$s" title="%2$s" target="_blank">%2$s</a>', esc_url('demo.presscustomizr.com'), __("demo website", "customizr") )
                  )
                );
              ?>
            </div>

          <?php endif; ?>

          <?php if ( TC___::$instance -> tc_is_child() ) : ?>
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

          <?php if ( ! TC___::tc_is_pro() ) : ?>
            <div class="changelog">

                <div class="feature-section col three-col">

                  <div class="col">
                    <h3><?php _e( 'We need sponsors!','customizr' ); ?></h3>
                    <p><?php  _e( '<strong>We do our best do make Customizr the perfect free theme for you!</strong><br/> Please help support it\'s continued development with a donation of $20, $50, or even $100.','customizr' ) ?></br>

                      <a href="<?php echo esc_url('paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8CTH6YFDBQYGU'); ?>" target="_blank" rel="nofollow"><img class="tc-donate" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" alt="Make a donation for Customizr" /></a>
                    </p>
                  </div>

                  <div class="col">
                    <h3><?php _e( 'Happy user of Customizr?','customizr' ); ?></h3>
                    <p><?php _e( 'If you are happy with the theme, say it on wordpress.org and give Customizr a nice review! <br />(We are addicted to your feedbacks...)','customizr' ) ?></br>
                    <a class="button-primary review-customizr" title="Customizr WordPress Theme" href="<?php echo esc_url('wordpress.org/support/view/theme-reviews/customizr') ?>" target="_blank">Review Customizr &raquo;</a></p>
                  </div>

                  <div class="last-feature col">
                    <h3><?php _e( 'Follow us','customizr' ); ?></h3>
                    <p class="tc-follow"><a href="<?php echo TC_WEBSITE.'blog' ?>" target="_blank"><img src="<?php echo TC_BASE_URL.'inc/admin/img/pc.png' ?>" alt="Press Customizr" /></a></p>
                    <!-- Place this tag where you want the widget to render. -->

                  </div><!-- .feature-section -->
                </div><!-- .feature-section col three-col -->

            </div><!-- .changelog -->

            <div id="extend" class="changelog">
              <h3 style="text-align:left"><?php _e("Go Customizr Pro" ,'customizr') ?></h3>

              <div class="feature-section images-stagger-right">
                <a class="" title="<?php _e("Visit the extension's page",'customizr') ?>" href="<?php echo TC_WEBSITE ?>customizr-pro/" target="_blank"><img alt="Customizr'extensions" src="<?php echo TC_BASE_URL.'inc/admin/img/customizr-pro.png' ?>" class=""></a>
                <h4 style="text-align: left"><?php _e('Easily take your web design one step further' ,'customizr') ?></h4></br>

                <p style="text-align: left"><?php _e("The Customizr Pro WordPress theme allows anyone to create a beautiful, professional and fully responsive website in a few seconds. In the Pro version, you'll get all the features of the free version plus some really cool and even revolutionary ones." , 'customizr') ?>
                </p>
                <p style="text-align:left">
                    <a class="button-primary review-customizr" title="<?php _e("Discover Customizr Pro",'customizr') ?>" href="<?php echo TC_WEBSITE ?>customizr-pro/" target="_blank"><?php _e("Discover Customizr Pro",'customizr') ?> &raquo;</a>
                </p>
              </div>
            </div>
          <?php endif; //end if ! is_pro ?>

        <div id="showcase" class="changelog">
          <h3 style="text-align:right"><?php _e('Customizr Showcase' ,'customizr') ?></h3>

          <div class="feature-section images-stagger-left">
             <a class="" title="<?php _e('Visit the showcase','customizr') ?>" href="<?php echo TC_WEBSITE ?>customizr/showcase/" target="_blank"><img alt="Customizr Showcase" src="<?php echo TC_BASE_URL.'inc/admin/img/mu2.png' ?>" class=""></a>
            <h4 style="text-align: right"><?php _e('Find inspiration for your next Customizr based website!' ,'customizr') ?></h4>
            <p style="text-align: right"><?php _e('This showcase aims to show what can be done with Customizr and helping other users to find inspiration for their web design.' , 'customizr') ?>
            </p>
            <p style="text-align: right"><?php _e('Do you think you made an awesome website that can inspire people? Submitting a site for review is quick and easy to do.' , 'customizr') ?></br>
            </p>
            <p style="text-align:right">
                <a class="button-primary review-customizr" title="<?php _e('Visit the showcase','customizr') ?>" href="<?php echo TC_WEBSITE ?>customizr/showcase/" target="_blank"><?php _e('Visit the showcase','customizr') ?> &raquo;</a>
            </p>
          </div>
        </div>

        <?php do_action( '__after_welcome_panel' ); ?>

        <div class="return-to-dashboard">
          <a href="<?php echo esc_url( self_admin_url() ); ?>"><?php
            is_blog_admin() ? _e( 'Go to Dashboard &rarr; Home','customizr' ) : _e( 'Go to Dashboard','customizr' ); ?></a>
        </div>

      </div><!-- //#customizr-admin-panel -->
      <?php
    }




    /**
   * Extract changelog of latest version from readme.txt file
   *
   * @package Customizr
   * @since Customizr 3.0.5
   */
    function tc_extract_changelog() {
      if( ! file_exists(TC_BASE."readme.txt") ) {
        return;
      }
      if( ! is_readable(TC_BASE."readme.txt") ) {
        echo '<p>The changelog in readme.txt is not readable.</p>';
        return;
      }

      ob_start();
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
        if ($line != strpos($line, '= '.CUSTOMIZR_VER)) {
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
      $html = ob_get_contents();
      if ($html) ob_end_clean();

      ?>
      <div id="customizr-changelog" class="changelog">
        <h3><?php printf( __( 'Changelog in version %1$s' , 'customizr' ) , CUSTOMIZR_VER ); ?></h3>
          <p><?php echo $html ?></p>
      </div>
      <?php
    }



    /*
    * Inspired by Easy Digital Download plugin by Pippin Williamson
    * @since 3.2.1
    */
    function tc_config_infos() {
      global $wpdb;

      ?>
<div class="wrap">
<h3><?php _e( 'System Informations', 'customizr' ); ?></h3>
<h4 style="text-align: left"><?php _e( 'Please include the following informations when posting support requests' , 'customizr' ) ?></h4>
<textarea readonly="readonly" onclick="this.focus();this.select()" id="system-info-textarea" name="tc-sysinfo" title="<?php _e( 'To copy the system infos, click below then press Ctrl + C (PC) or Cmd + C (Mac).', 'customizr' ); ?>" style="width: 800px;min-height: 800px;font-family: Menlo,Monaco,monospace;background: 0 0;white-space: pre;overflow: auto;display:block;">
<?php do_action( '__system_config_before' ); ?>
# SITE_URL:                 <?php echo site_url() . "\n"; ?>
# HOME_URL:                 <?php echo home_url() . "\n"; ?>
# IS MULTISITE :            <?php echo is_multisite() ? 'Yes' . "\n" : 'No' . "\n" ?>

# THEME | VERSION :         <?php printf( '%1$s | v%2$s', TC___::$theme_name , CUSTOMIZR_VER ) . "\n"; ?>
# WP VERSION :              <?php echo get_bloginfo( 'version' ) . "\n"; ?>
# PERMALINK STRUCTURE :     <?php echo get_option( 'permalink_structure' ) . "\n"; ?>

# ACTIVE PLUGINS :
<?php
$plugins = get_plugins();
$active_plugins = get_option( 'active_plugins', array() );

foreach ( $plugins as $plugin_path => $plugin ) {
  // If the plugin isn't active, don't show it.
  if ( ! in_array( $plugin_path, $active_plugins ) )
    continue;

  echo $plugin['Name'] . ': ' . $plugin['Version'] ."\n";
}

if ( is_multisite() ) :
?>
#  NETWORK ACTIVE PLUGINS:
<?php
$plugins = wp_get_active_network_plugins();
$active_plugins = get_site_option( 'active_sitewide_plugins', array() );

foreach ( $plugins as $plugin_path ) {
  $plugin_base = plugin_basename( $plugin_path );

  // If the plugin isn't active, don't show it.
  if ( ! array_key_exists( $plugin_base, $active_plugins ) )
    continue;

  $plugin = get_plugin_data( $plugin_path );

  echo $plugin['Name'] . ' :' . $plugin['Version'] ."\n";
}
endif;
//GET MYSQL VERSION
global $wpdb;
$mysql_ver =  ( ! empty( $wpdb->use_mysqli ) && $wpdb->use_mysqli ) ? @mysqli_get_server_info( $wpdb->dbh ) : @mysql_get_server_info();
?>

PHP Version:              <?php echo PHP_VERSION . "\n"; ?>
MySQL Version:            <?php echo $mysql_ver . "\n"; ?>
Web Server Info:          <?php echo $_SERVER['SERVER_SOFTWARE'] . "\n"; ?>

WordPress Memory Limit:   <?php echo ( $this -> tc_let_to_num( WP_MEMORY_LIMIT )/( 1024 ) )."MB"; ?><?php echo "\n"; ?>
PHP Safe Mode:            <?php echo ini_get( 'safe_mode' ) ? "Yes" : "No\n"; ?>
PHP Memory Limit:         <?php echo ini_get( 'memory_limit' ) . "\n"; ?>
PHP Upload Max Size:      <?php echo ini_get( 'upload_max_filesize' ) . "\n"; ?>
PHP Post Max Size:        <?php echo ini_get( 'post_max_size' ) . "\n"; ?>
PHP Upload Max Filesize:  <?php echo ini_get( 'upload_max_filesize' ) . "\n"; ?>
PHP Time Limit:           <?php echo ini_get( 'max_execution_time' ) . "\n"; ?>
PHP Max Input Vars:       <?php echo ini_get( 'max_input_vars' ) . "\n"; ?>
PHP Arg Separator:        <?php echo ini_get( 'arg_separator.output' ) . "\n"; ?>
PHP Allow URL File Open:  <?php echo ini_get( 'allow_url_fopen' ) ? "Yes" : "No\n"; ?>

WP_DEBUG:                 <?php echo defined( 'WP_DEBUG' ) ? WP_DEBUG ? 'Enabled' . "\n" : 'Disabled' . "\n" : 'Not set' . "\n" ?>

Show On Front:            <?php echo get_option( 'show_on_front' ) . "\n" ?>
Page On Front:            <?php $id = get_option( 'page_on_front' ); echo get_the_title( $id ) . ' (#' . $id . ')' . "\n" ?>
Page For Posts:           <?php $id = get_option( 'page_for_posts' ); echo get_the_title( $id ) . ' (#' . $id . ')' . "\n" ?>
<?php do_action( '__system_config_after' ); ?>
</textarea>
</div>
</div>
      <?php
      }//end of function


      /**
       * TC Let To Num
       *
       * Does Size Conversions
       *
       *
       * @since 3.2.2
       */
      function tc_let_to_num( $v ) {
        $l   = substr( $v, -1 );
        $ret = substr( $v, 0, -1 );

        switch ( strtoupper( $l ) ) {
          case 'P': // fall-through
          case 'T': // fall-through
          case 'G': // fall-through
          case 'M': // fall-through
          case 'K': // fall-through
            $ret *= 1024;
            break;
          default:
            break;
        }

        return $ret;
      }

    /**
    * hook : admin_print_styles
    * fix the absolute positioning of the wp footer admin link in the welcome pages
    * @return void
    */
    function tc_fix_wp_footer_link_style() {
      /* if ( is_array(get_current_screen()) )
        array_walk_recursive(get_current_screen(), function(&$v) { $v = htmlspecialchars($v); }); */
      $screen = get_current_screen();
      if ( 'appearance_page_welcome' != $screen-> id )
        return;
      ?>
        <style type="text/css" id="tc-fix-wp-footer-position">
          .wp-admin #wpfooter {bottom: inherit;}
        </style>
      <?php
    }

  }//end of class
endif;

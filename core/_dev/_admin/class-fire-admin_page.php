<?php
/**
* Init admin page actions : Welcome, help page
*
*/
if ( ! class_exists( 'CZR_admin_page' ) ) :
  class CZR_admin_page {
    static $instance;
    public $support_url;

    function __construct () {
      self::$instance =& $this;
      //add welcome page in menu
      add_action( 'admin_menu'             , array( $this , 'czr_fn_add_welcome_page' ));
      //config infos
      add_action( '__after_welcome_panel'  , array( $this , 'czr_fn_config_infos' ), 10 );
      //changelog
      add_action( '__after_welcome_panel'  , array( $this , 'czr_fn_print_changelog' ), 20);
      //build the support url
      $this -> support_url = CZR_IS_PRO ? esc_url( sprintf('%ssupport' , CZR_WEBSITE ) ) : esc_url('wordpress.org/support/theme/customizr');
      //fix #wpfooter absolute positioning in the welcome and about pages
      add_action( 'admin_print_styles'     , array( $this, 'czr_fn_fix_wp_footer_link_style') );
      //knowledgebase
      if ( CZR_IS_PRO ) {
          add_action( 'current_screen'         , array( $this , 'czr_schedule_welcome_page_actions') );
      }
    }



   /**
   * Add fallback admin page.
   * @package Customizr
   * @since Customizr 1.1
   */
    function czr_fn_add_welcome_page() {
        $_name = __( 'About Customizr' , 'customizr' );
        $_name = CZR_IS_PRO ? sprintf( '%s Pro', $_name ) : $_name;

        $theme_page = add_theme_page(
            $_name,   // Name of page
            $_name,   // Label in menu
            'edit_theme_options' ,          // Capability required
            'welcome.php' ,             // Menu slug, used to uniquely identify the page
            array( $this , 'czr_fn_welcome_panel' )         //function to be called to output the content of this page
        );
    }



      /**
     * Render welcome admin page.
     * @package Customizr
     * @since Customizr 3.0.4
     */
      function czr_fn_welcome_panel() {

        $is_help        = isset($_GET['help'])  ?  true : false;
        $_faq_url       = esc_url('http://docs.presscustomizr.com/category/90-faq-and-common-issues');
        $_support_url   = $this -> support_url;
        $_theme_name    = CZR_IS_PRO ? 'Customizr Pro' : 'Customizr';

        do_action('__before_welcome_panel');

        ?>
        <div id="customizr-admin-panel" class="wrap about-wrap">
          <?php
            $title = sprintf( '<h1 class="need-help-title">%1$s %2$s %3$s :)</h1>',
              __( "Thank you for using", "customizr" ),
              $_theme_name,
              CUSTOMIZR_VER
            );
            echo convert_smilies( $title );
          ?>

          <?php if ( $is_help && ! CZR_IS_PRO ) : ?>

              <div class="">

              </div><!-- .changelog -->

          <?php else : ?>

            <div class="about-text tc-welcome">
              <?php
                printf( '<p>%1$s</p>',
                  sprintf( __( "The best way to start with %s is to read the %s and visit the %s.", "customizr"),
                    $_theme_name,
                    sprintf( '<a href="%1$s" title="%2$s" target="_blank">%2$s</a>', esc_url('docs.presscustomizr.com'), __("documentation", "customizr") ),
                    sprintf( '<a href="%1$s" title="%2$s" target="_blank">%2$s</a>', esc_url('demo.presscustomizr.com'), __("demo website", "customizr") )
                  )
                );
                printf( '<p><a href="#customizr-changelog">%1$s</a></p>',
                  __( "Read the changelog", "customizr")
                );
              ?>
            </div>

          <?php endif; ?>

          <?php if ( czr_fn_is_child() ) : ?>
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

          <?php do_action( 'czr_after_welcome_admin_intro' ); ?>

          <div class="changelog point-releases"></div>

          <?php if ( ! CZR_IS_PRO ) : ?>
            <div class="changelog">

                <div class="feature-section col two-col">

                  <div class="col">
                    <h3 style="font-size:1.3em;"><?php _e( 'Happy user of Customizr?','customizr' ); ?></h3>
                    <p><?php _e( 'If you are happy with the theme, say it on wordpress.org and give Customizr a nice review! <br />(We are addicted to your feedbacks...)','customizr' ) ?></br>
                    <a class="button-primary review-customizr" title="Customizr WordPress Theme" href="<?php echo esc_url('wordpress.org/support/view/theme-reviews/customizr') ?>" target="_blank">Review Customizr &raquo;</a></p>
                  </div>

                  <div class="last-feature col">
                    <h3 style="font-size:1.3em;"><?php _e( 'Follow us','customizr' ); ?></h3>
                    <p class="tc-follow"><a href="<?php echo esc_url( CZR_WEBSITE . 'blog' ); ?>" target="_blank"><img style="border:none;width:auto;" src="<?php echo CZR_BASE_URL . CZR_ASSETS_PREFIX.'back/img/pc.png' ?>" alt="Press Customizr" /></a></p>
                    <!-- Place this tag where you want the widget to render. -->

                  </div><!-- .feature-section -->
                </div><!-- .feature-section col three-col -->

            </div><!-- .changelog -->

            <div id="extend" class="changelog">
              <h3 style="text-align:left;font-size:1.3em;"><?php _e("Go Customizr Pro" ,'customizr') ?></h3>

              <div class="feature-section two-col images-stagger-right">
                <div class="col" style="float:right">
                  <a class="" title="Go Pro" href="<?php echo esc_url( CZR_WEBSITE . 'customizr-pro?ref=a&utm_source=usersite&utm_medium=link&utm_campaign=customizr-admin-page' ); ?>" target="_blank"><img style="border:none;width:auto;" alt="Customizr Pro" src="<?php echo CZR_BASE_URL . CZR_ASSETS_PREFIX.'back/img/customizr-pro.png?'.CUSTOMIZR_VER ?>" class=""></a>
                </div>
                <div class="col" style="float:left">
                  <h4 style="text-align: left;"><?php _e('Easily take your web design one step further' ,'customizr') ?></h4></br>

                  <p style="text-align: left;"><?php _e("The Customizr Pro WordPress theme allows anyone to create a beautiful, professional and mobile friendly website in a few minutes. In the Pro version, you'll get all features included in the free version plus many conversion oriented ones, to help you attract and retain more visitors on your websites." , 'customizr') ?>
                  </p>
                  <p style="text-align:left;">
                      <a class="button-primary review-customizr hu-go-pro-btn" title="<?php _e("Discover Customizr Pro",'customizr') ?>" href="<?php echo esc_url( CZR_WEBSITE . 'customizr-pro?ref=a&utm_source=usersite&utm_medium=link&utm_campaign=customizr-admin-page' ); ?>" target="_blank"><?php _e("Discover Customizr Pro",'customizr') ?> &raquo;</a>
                  </p>
                </div>
              </div>
            </div>
          <?php endif; //end if ! is_pro ?>

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
    function czr_fn_print_changelog() {
      if ( isset($_GET['help']) )
        return;
      if( ! file_exists( CZR_BASE . "readme.txt" ) ) {
        return;
      }
      if( ! is_readable( CZR_BASE . "readme.txt" ) ) {
        echo '<p>The changelog in readme.txt is not readable.</p>';
        return;
      }

      $html = '';
      $stylelines = explode("\n", implode('', file( CZR_BASE . "readme.txt" ) ) );
      $read = false;
      $is_title = false;

      foreach ($stylelines as $line) {
          $is_title = 0 === strpos($line, '= ');

          //we start reading after current version title
          if ( 0 === strpos($line, '= '. CUSTOMIZR_VER) ) {
            $read = true;
          }

          if ( ! $read )
            continue;

          if ( $is_title ) {
            $html .= sprintf( '<strong>%1$s</strong><br/>', esc_attr( $line ) );
          } else {
            $html .= sprintf( '<i>%1$s</i><br/>', esc_attr( $line ) );
          }
      }
      do_action('__before_changelog')
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
    function czr_fn_config_infos() {
      global $wpdb;
      $theme_data   = wp_get_theme();
      $theme        = $theme_data->Name . ' ' . $theme_data->Version;
      $parent_theme = $theme_data->Template;
      if ( ! empty( $parent_theme ) ) {
        $parent_theme_data = wp_get_theme( $parent_theme );
        $parent_theme      = $parent_theme_data->Name . ' ' . $parent_theme_data->Version;
      }
      ?>
<div class="wrap">
<h3><?php _e( 'System Informations', 'customizr' ); ?></h3>
<h4 style="text-align: left"><?php _e( 'Please include the following informations when posting support requests' , 'customizr' ) ?></h4>
<textarea readonly="readonly" onclick="this.focus();this.select()" id="system-info-textarea" name="tc-sysinfo" title="<?php _e( 'To copy the system infos, click below then press Ctrl + C (PC) or Cmd + C (Mac).', 'customizr' ); ?>" style="width: 800px;min-height: 800px;font-family: Menlo,Monaco,monospace;background: 0 0;white-space: pre;overflow: auto;display:block;">
<?php do_action( '__system_config_before' ); ?>
# SITE_URL:                 <?php echo site_url() . "\n"; ?>
# HOME_URL:                 <?php echo home_url() . "\n"; ?>
# IS MULTISITE :            <?php echo is_multisite() ? 'Yes' . "\n" : 'No' . "\n" ?>

# ACTIVE THEME :            <?php echo $theme . "\n"; ?>
<?php if ( $parent_theme !== $theme ) : ?>
# PARENT THEME :            <?php echo $parent_theme . "\n"; ?>
<?php endif; ?>
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
$mysql_ver =  ( ! empty( $wpdb->use_mysqli ) && $wpdb->use_mysqli ) ? @mysqli_get_server_info( $wpdb->dbh ) : '';
?>

PHP Version:              <?php echo PHP_VERSION . "\n"; ?>
MySQL Version:            <?php echo $mysql_ver . "\n"; ?>
Web Server Info:          <?php echo $_SERVER['SERVER_SOFTWARE'] . "\n"; ?>

WordPress Memory Limit:   <?php echo ( $this -> czr_fn_let_to_num( WP_MEMORY_LIMIT )/( 1024 ) )."MB"; ?><?php echo "\n"; ?>
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
      function czr_fn_let_to_num( $v ) {
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
    function czr_fn_fix_wp_footer_link_style() {
      $screen = get_current_screen();
      if ( ! is_object($screen) )
        return;
      if ( 'appearance_page_welcome' != $screen-> id )
        return;
      ?>
        <style type="text/css" id="tc-fix-wp-footer-position">
          .wp-admin #wpfooter {bottom: inherit;}
        </style>
      <?php
    }

    //hook : current_screen
    function czr_schedule_welcome_page_actions() {
        $screen = get_current_screen();
        if ( 'appearance_page_welcome' != $screen-> id )
          return;

        add_action( 'czr_after_welcome_admin_intro', array( $this, 'czr_print_hs_doc_content') );
        add_action( 'admin_enqueue_scripts', array( $this, 'czr_enqueue_hs_assets' ) );
    }

    //hook : admin_enqueue_scripts
    function czr_enqueue_hs_assets() {
        $screen = get_current_screen();
        if ( 'appearance_page_welcome' != $screen-> id )
          return;
        wp_enqueue_style(
          'czr-admin-hs-css',
          sprintf('%1$sback/css/czr-hs-doc%2$s.css' , CZR_BASE_URL . CZR_ASSETS_PREFIX, ( defined('WP_DEBUG') && true === WP_DEBUG ) ? '' : '.min' ),
          array(),
          ( defined('WP_DEBUG') && true === WP_DEBUG ) ? CUSTOMIZR_VER . time() : CUSTOMIZR_VER
        );
        wp_enqueue_script(
          'czr-hs-js',
          sprintf('%1$sback/js/czr-hs-doc%2$s.js' , CZR_BASE_URL . CZR_ASSETS_PREFIX, ( defined('WP_DEBUG') && true === WP_DEBUG ) ? '' : '.min' ),
          array( 'jquery', 'underscore' ),
          ( defined('WP_DEBUG') && true === WP_DEBUG ) ? CUSTOMIZR_VER . time() : CUSTOMIZR_VER,
          $in_footer = false
        );

        $script_settings = array(
          'debug' => false, // Print debug logs or not
          'searchDelay' => 750, // Delay time in ms after a user stops typing and before search is performed
          'minLength' => 3, // Minimum number of characters required to trigger search
          'limit' => 25, // Max limit for # of results to show
          'text' => array(
            //@translators : keep the strings inside brackets ( like {count} and {minLength} ) untranslated as it will be replaced by a number when parsed in javascript
            'result_found' => __('We found {count} article that may help:' , 'customizr'),
            'results_found' => __('We found {count} articles that may help:' , 'customizr'),
            'no_results_found' => __('No results found&hellip;' , 'customizr'),
            'enter_search' => __('Please enter a search term.' , 'customizr'),
            'not_long_enough' => __('Search must be at least {minLength} characters.' , 'customizr'),
            'error' => __('There was an error fetching search results.' , 'customizr'),
          ),
          'template' => array(
            'wrap_class' => 'docs-search-wrap',
            'before' => '<ul class="docs-search-results">',
            'item' => sprintf( '<li class="article"><a href="{url}" title="%1$s" target="_blank">{name}<span class="article--open-original" ></span></a><p class="article-preview">{preview} ... <a href="{url}" title="%1$s" target="_blank">%2$s</a></p></li>',
              __( 'Read the full article', 'customizr' ),
              __( 'read more', 'customizr' )
            ),
            'after' => '</ul>',
            'results_found' => '<span class="{css_class}">{text}</span>',
          ),
          'collections' => array(), // The collection IDs to search in

          // Do not modify
          '_subdomain' => 'presscustomizr',
        );

        wp_localize_script( 'czr-hs-js', 'CZRHSParams', $script_settings );
    }


    //hook : czr_after_welcome_admin_intro
    function czr_print_hs_doc_content() {
        ?>
          <form enctype="multipart/form-data" method="post" class="frm-show-form " id="form_m3j26q22">
            <div class="frm_form_fields ">
              <fieldset>
                <div id="frm_field_335_container" class="frm_form_field form-field  frm_top_container helpscout-docs">
                  <label for="field_6woxqa" class="frm_primary_label">
                    <h2><?php _e( 'Search the knowledge base', 'customizr' ); ?></h2>
                    <h4 style="text-align:center;font-style: italic;font-weight: normal;"><?php _e( 'In a few keywords, describe the information you are looking for.', 'customizr' ); ?></h4>
                      <span class="frm_required"></span>
                  </label>
                  <input type="text" id="field_6woxqa" name="item_meta[335]" value="" placeholder="<?php _e( 'Ex. Logo upload', 'customizr' ) ;?>" autocomplete="off">

                  <div class="frm_description"><?php _e('<u>Search tips</u> : If you get too many results, try to narrow down your search by prefixing it with "customizr" for example. If there are no results, try different keywords and / or spelling variations', 'customizr' ); ?> </div>
                </div>
              </fieldset>
            </div>
          </form>
        <?php
    }

  }//end of class
endif;

?>
<?php
/**
* Init admin actions : loads the meta boxes,
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
if ( ! class_exists( 'CZR_admin_init' ) ) :
  class CZR_admin_init {
    static $instance;
    function __construct () {
      self::$instance =& $this;
      //enqueue additional styling for admin screens
      add_action( 'admin_init'            , array( $this , 'czr_fn_admin_style' ) );

      //Load the editor-style specific (post formats and RTL), the active skin, the user style.css
      //add user defined fonts in the editor style (@see the query args add_editor_style below)
      add_action( 'after_setup_theme'     , array( $this, 'czr_fn_add_editor_style') );

      add_filter( 'tiny_mce_before_init'  , array( $this, 'czr_fn_user_defined_tinymce_css') );
      //refresh the post / CPT / page thumbnail on save. Since v3.3.2.
      add_action ( 'save_post'            , array( $this , 'czr_fn_refresh_thumbnail') , 10, 2);

      //refresh the posts slider transient on save_post. Since v3.4.9.
      add_action ( 'save_post'            , array( $this , 'czr_fn_refresh_posts_slider'), 20, 2 );
      //refresh the posts slider transient on permanent post/attachment deletion. Since v3.4.9.
      add_action ( 'deleted_post'         , array( $this , 'czr_fn_refresh_posts_slider') );

      //refresh the terms array (categories/tags pickers options) on term deletion
      add_action ( 'delete_term'          , array( $this, 'czr_fn_refresh_terms_pickers_options_cb'), 10, 3 );

      //UPDATE NOTICE
      add_action( 'admin_notices'         , array( $this, 'czr_fn_may_be_display_update_notice') );
      //always add the ajax action
      add_action( 'wp_ajax_dismiss_customizr_update_notice'    , array( $this , 'czr_fn_dismiss_update_notice_action' ) );
      add_action( 'admin_footer'                  , array( $this , 'czr_fn_write_ajax_dismis_script' ) );
      /* beautify admin notice text using some defaults the_content filter callbacks */
      foreach ( array( 'wptexturize', 'convert_smilies', 'wpautop') as $callback )
        add_filter( 'tc_update_notice', $callback );
    }



    /*
    * @return void
    * updates the tc-thumb-fld post meta with the relevant thumb id and type
    * @package Customizr
    * @since Customizr 3.3.2
    */
    function czr_fn_refresh_thumbnail( $post_id, $post ) {
      // If this is just a revision, don't send the email.
      if ( wp_is_post_revision( $post_id ) || ( ! empty($post) && 'auto-draft' == $post->post_status ) )
        return;

      if ( ! class_exists( 'CZR_post_thumbnails' ) || ! is_object(CZR_post_thumbnails::$instance) ) {
        CZR___::$instance -> czr_fn_req_once( 'inc/czr-front.php' );
        new CZR_post_thumbnails();
      }

      CZR_post_thumbnails::$instance -> czr_fn_set_thumb_info( $post_id );
    }

    /*
    * @return void
    * updates the posts slider transient
    * @package Customizr
    * @since Customizr 3.4.9
    */
    function czr_fn_refresh_posts_slider( $post_id, $post = array() ) {
      // no need to build up/refresh the transient it we don't use the posts slider
      // since we always delete the transient when entering the preview.
      if ( 'tc_posts_slider' != CZR_utils::$inst->czr_fn_opt( 'tc_front_slider' ) || ! apply_filters('tc_posts_slider_use_transient' , true ) )
        return;

      if ( wp_is_post_revision( $post_id ) || ( ! empty($post) && 'auto-draft' == $post->post_status ) )
        return;

      if ( ! class_exists( 'CZR_post_thumbnails' ) || ! is_object(CZR_post_thumbnails::$instance) ) {
        CZR___::$instance -> czr_fn_req_once( 'inc/czr-front.php' );
        new CZR_post_thumbnails();
      }
      if ( ! class_exists( 'CZR_slider' ) || ! is_object(CZR_slider::$instance) ) {
        CZR___::$instance -> czr_fn_req_once( 'inc/czr-front.php' );
        new CZR_slider();
      }
      if ( class_exists( 'CZR_slider' ) && is_object( CZR_slider::$instance ) )
        CZR_slider::$instance -> czr_fn_cache_posts_slider();
    }


    /*
    * @return void
    * updates the term pickers related options
    * @package Customizr
    * @since Customizr 3.4.10
    */
    function czr_fn_refresh_terms_pickers_options_cb( $term, $tt_id, $taxonomy ) {
      switch ( $taxonomy ) {

        //delete categories based options
        case 'category':
          $this -> czr_fn_refresh_term_picker_options( $term, $option_name = 'tc_blog_restrict_by_cat' );
          break;
      }
    }


    function czr_fn_refresh_term_picker_options( $term, $option_name, $option_group = null ) {
       //home/blog posts category picker
       $_option = CZR_utils::$inst -> czr_fn_opt( $option_name, $option_group, $use_default = false );
       if ( is_array( $_option ) && ! empty( $_option ) && in_array( $term, $_option ) )
         //update the option
         CZR_utils::$inst -> czr_fn_set_option( $option_name, array_diff( $_option, (array)$term ) );

       //alternative, cycle throughout the cats and keep just the existent ones
       /*if ( is_array( $blog_cats ) && ! empty( $blog_cats ) ) {
         //update the option
         CZR_utils::$inst -> czr_fn_set_option( 'tc_blog_restrict_by_cat', array_filter( $blog_cats, array(CZR_utils::$inst, 'czr_fn_category_id_exists' ) ) );
       }*/
    }


    /*
    * @return css string
    *
    * @package Customizr
    * @since Customizr 3.2.10
    */
    function czr_fn_maybe_add_gfonts_to_editor() {
      $_font_pair         = esc_attr( CZR_utils::$inst->czr_fn_opt('tc_fonts') );
      $_all_font_pairs    = CZR_init::$instance -> font_pairs;
      if ( false === strpos($_font_pair,'_g_') )
        return;
      //Commas in a URL need to be encoded before the string can be passed to add_editor_style.
      return array(
        str_replace(
          ',',
          '%2C',
          sprintf( '//fonts.googleapis.com/css?family=%s', CZR_utils::$inst -> czr_fn_get_font( 'single' , $_font_pair ) )
        )
      );
    }



    /**
   * enqueue additional styling for admin screens
   * @package Customizr
   * @since Customizr 3.0.4
   */
    function czr_fn_admin_style() {
      wp_enqueue_style(
        'tc-admincss',
        sprintf('%1$sinc/admin/css/tc_admin%2$s.css' , TC_BASE_URL, ( defined('WP_DEBUG') && true === WP_DEBUG ) ? '' : '.min' ),
        array(),
        CUSTOMIZR_VER
      );
    }



    /**
   * Extract changelog of latest version from readme.txt file
   * @package Customizr
   * @since Customizr 3.0.5
   */
    function czr_fn_extract_changelog() {

      if( ! file_exists(TC_BASE."readme.txt") ) {
        return;
      }
      if( ! is_readable(TC_BASE."readme.txt") ) {
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
    }


    /**
    * Customizr styles the visual editor to resemble the theme style,
    * Loads the editor-style specific (post formats and RTL), the active skin, the user style.css, the user_defined fonts
    * @package Customizr
    * @since Customizr 3.2.11
    *
    */
    function czr_fn_add_editor_style() {
      $_stylesheets = array(
          TC_BASE_URL.'inc/admin/css/editor-style.min.css',
          CZR_init::$instance -> czr_fn_get_style_src() , get_stylesheet_uri()
      );

      if ( apply_filters( 'tc_add_custom_fonts_to_editor' , false != $this -> czr_fn_maybe_add_gfonts_to_editor() ) )
        $_stylesheets = array_merge( $_stylesheets , $this -> czr_fn_maybe_add_gfonts_to_editor() );

      add_editor_style( $_stylesheets );
    }




    /**
    * Extend TinyMCE config with a setup function.
    * See http://www.tinymce.com/wiki.php/API3:event.tinymce.Editor.onInit
    * http://wordpress.stackexchange.com/questions/120831/how-to-add-custom-css-theme-option-to-tinymce
    * @package Customizr
    * @since Customizr 3.2.11
    *
    */
    function czr_fn_user_defined_tinymce_css( $init ) {
      if ( ! apply_filters( 'tc_add_custom_fonts_to_editor' , true ) )
        return $init;

      if ( 'tinymce' != wp_default_editor() )
        return $init;

      //some plugins fire tiny mce editor in the customizer
      //in this case, the CZR_resource class has to be loaded
      if ( ! class_exists('CZR_resources') || ! is_object(CZR_resources::$instance) ) {
        CZR___::$instance -> czr_fn_req_once( 'inc/czr-init.php' );
        new CZR_resources();
      }


      //fonts
      $_css = CZR_resources::$instance -> czr_fn_write_fonts_inline_css( '', 'mce-content-body');

      $init['content_style'] = trim(preg_replace('/\s+/', ' ', $_css ) );

      return $init;
    }



    /**********************************************************************************
    * UPDATE NOTICE
    * User gets notified when the version stores in the db option 'last_update_notice'
    * is < current version of the theme (CUSTOMIZR_VER)
    * User can dismiss the notice and the option get updated by ajax to the current version
    * The notice will be displayed a maximum of 5 times and will be automatically dismissed until the next update.
    * => users won't be notified again until the next update.
    **********************************************************************************/
    /**
    * hook : admin_notices
    */
    function czr_fn_may_be_display_update_notice() {
      $opt_name                   = "customizr-pro" == CZR___::$theme_name ? 'last_update_notice_pro' : 'last_update_notice';
      $last_update_notice_values  = CZR_utils::$inst -> czr_fn_opt($opt_name);
      $show_new_notice = false;

      if ( ! $last_update_notice_values || ! is_array($last_update_notice_values) ) {
        //first time user of the theme, the option does not exist
        // 1) initialize it => set it to the current Customizr version, displayed 0 times.
        // 2) update in db
        $last_update_notice_values = array( "version" => CUSTOMIZR_VER, "display_count" => 0 );
        CZR_utils::$inst->czr_fn_set_option( $opt_name, $last_update_notice_values );
        //already user of the theme ?
        if ( CZR_utils::$inst->czr_fn_user_started_before_version( CUSTOMIZR_VER, CUSTOMIZR_VER ) )
          $show_new_notice = true;
      }

      $_db_version          = $last_update_notice_values["version"];
      $_db_displayed_count  = $last_update_notice_values["display_count"];

      //user who just upgraded the theme will be notified until he clicks on the dismiss link
      //or until the notice has been displayed 5 times.
      if ( version_compare( CUSTOMIZR_VER, $_db_version , '>' ) ) {
        //CASE 1 : displayed less than 5 times
        if ( $_db_displayed_count < 5 ) {
          $show_new_notice = true;
          //increments the counter
          (int) $_db_displayed_count++;
          $last_update_notice_values["display_count"] = $_db_displayed_count;
          //updates the option val with the new count
          CZR_utils::$inst->czr_fn_set_option( $opt_name, $last_update_notice_values );
        }
        //CASE 2 : displayed 5 times => automatic dismiss
        else {
          //reset option value with new version and counter to 0
          $new_val  = array( "version" => CUSTOMIZR_VER, "display_count" => 0 );
          CZR_utils::$inst->czr_fn_set_option( $opt_name, $new_val );
        }//end else
      }//end if

      if ( ! $show_new_notice )
        return;

      ob_start();
        ?>
        <div class="updated" style="position:relative">
          <?php
            echo apply_filters(
              'tc_update_notice',
              sprintf('<h3>%1$s %2$s %3$s %4$s :D</h3>',
                __( "Good, you've just upgraded to", "customizr"),
                "customizr-pro" == CZR___::$theme_name ? 'Customizr Pro' : 'Customizr',
                __( "version", "customizr"),
                CUSTOMIZR_VER
              )
            );
          ?>
          <?php
            echo apply_filters(
              'tc_update_notice',
              sprintf( '<h4>%1$s</h4><strong><a class="button button-primary" href="%2$s" title="%3$s" target="_blank">%3$s &raquo;</a> <a class="button button-primary" href="%4$s" title="%5$s" target="_blank">%5$s &raquo;</a></strong>',
                __( "We'd like to introduce the new features we've been working on.", "customizr"),
                CZR_WEBSITE . "category/customizr-releases/",
                __( "Read the latest release notes" , "customizr" ),
                esc_url('demo.presscustomizr.com'),
                __( "Visit the demo", "customizr" )
              )
            );
          ?>
          <p style="text-align:right;position: absolute;<?php echo is_rtl()? 'left' : 'right';?>: 7px;bottom: -5px;">
            <?php printf('<em>%1$s <strong><a href="#" title="%1$s" class="tc-dismiss-update-notice"> ( %2$s x ) </a></strong></em>',
                __("I already know what's new thanks !", "customizr" ),
                __('close' , 'customizr')
              );
            ?>
          </p>
        </div>
        <?php
      $_html = ob_get_contents();
      if ($_html) ob_end_clean();
      echo $_html;
    }


    /**
    * hook : wp_ajax_dismiss_customizr_update_notice
    * => sets the last_update_notice to the current Customizr version when user click on dismiss notice link
    */
    function czr_fn_dismiss_update_notice_action() {
      check_ajax_referer( 'dismiss-update-notice-nonce', 'dismissUpdateNoticeNonce' );
      $opt_name = "customizr-pro" == CZR___::$theme_name ? 'last_update_notice_pro' : 'last_update_notice';
      //reset option value with new version and counter to 0
      $new_val  = array( "version" => CUSTOMIZR_VER, "display_count" => 0 );
      CZR_utils::$inst->czr_fn_set_option( $opt_name, $new_val );
      wp_die();
    }



    /**
    * hook : admin_footer
    */
    function czr_fn_write_ajax_dismis_script() {
      ?>
      <script type="text/javascript" id="tc-dismiss-update-notice">
        ( function($){
          var _ajax_action = function( $_el ) {
              var AjaxUrl = "<?php echo admin_url( 'admin-ajax.php' ); ?>",
                  _query  = {
                      action  : 'dismiss_customizr_update_notice',
                      dismissUpdateNoticeNonce :  "<?php echo wp_create_nonce( 'dismiss-update-notice-nonce' ); ?>"
                  },
                  $ = jQuery,
                  request = $.post( AjaxUrl, _query );

              request.fail( function ( response ) {
                //console.log('response when failed : ', response);
              });
              request.done( function( response ) {
                //console.log('RESPONSE DONE', $_el, response);
                // Check if the user is logged out.
                if ( '0' === response )
                  return;
                // Check for cheaters.
                if ( '-1' === response )
                  return;

                $_el.closest('.updated').slideToggle('fast');
              });
          };//end of fn

          //on load
          $( function($) {
            $('.tc-dismiss-update-notice').click( function( e ) {
              e.preventDefault();
              _ajax_action( $(this) );
            } );
          } );

        } )( jQuery );


      </script>
      <?php
    }
  }//end of class
endif;

?><?php
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
      $this -> support_url = CZR___::czr_fn_is_pro() ? esc_url( sprintf('%ssupport' , CZR_WEBSITE ) ) : esc_url('wordpress.org/support/theme/customizr');
      //fix #wpfooter absolute positioning in the welcome and about pages
      add_action( 'admin_print_styles'      , array( $this, 'czr_fn_fix_wp_footer_link_style') );
    }



   /**
   * Add fallback admin page.
   * @package Customizr
   * @since Customizr 1.1
   */
    function czr_fn_add_welcome_page() {
        $_name = __( 'About Customizr' , 'customizr' );
        $_name = CZR___::czr_fn_is_pro() ? sprintf( '%s Pro', $_name ) : $_name;

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
        $_theme_name    = CZR___::czr_fn_is_pro() ? 'Customizr Pro' : 'Customizr';

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
                  CZR___::czr_fn_is_pro() ? '' : sprintf( __( "The easiest way to search in the support forums is to use our Google powered search engine on our %s.", "customizr" ),
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
                    <a class="button-secondary customizr-help" title="code snippets" href="<?php echo CZR_WEBSITE ?>code-snippets/" target="_blank"><?php _e( 'Code snippets for developers','customizr' ); ?></a>
                </div>
                 <div class="last-feature col">
                    <a class="button-secondary customizr-help" title="help" href="<?php echo $_support_url; ?>" target="_blank">
                      <?php CZR___::czr_fn_is_pro() ? _e( 'Get support','customizr' ) : _e( 'Get help in the free support forum','customizr' ); ?>
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

          <?php if ( CZR___::$instance -> czr_fn_is_child() ) : ?>
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

          <?php if ( ! CZR___::czr_fn_is_pro() ) : ?>
            <div class="changelog">

                <div class="feature-section col two-col">

                  <div class="col">
                    <h3 style="font-size:1.3em;"><?php _e( 'Happy user of Customizr?','customizr' ); ?></h3>
                    <p><?php _e( 'If you are happy with the theme, say it on wordpress.org and give Customizr a nice review! <br />(We are addicted to your feedbacks...)','customizr' ) ?></br>
                    <a class="button-primary review-customizr" title="Customizr WordPress Theme" href="<?php echo esc_url('wordpress.org/support/view/theme-reviews/customizr') ?>" target="_blank">Review Customizr &raquo;</a></p>
                  </div>

                  <div class="last-feature col">
                    <h3 style="font-size:1.3em;"><?php _e( 'Follow us','customizr' ); ?></h3>
                    <p class="tc-follow"><a href="<?php echo esc_url( CZR_WEBSITE . 'blog' ); ?>" target="_blank"><img style="border:none" src="<?php echo TC_BASE_URL.'inc/admin/img/pc.png' ?>" alt="Press Customizr" /></a></p>
                    <!-- Place this tag where you want the widget to render. -->

                  </div><!-- .feature-section -->
                </div><!-- .feature-section col three-col -->

            </div><!-- .changelog -->

            <div id="extend" class="changelog">
              <h3 style="text-align:left;font-size:1.3em;"><?php _e("Go Customizr Pro" ,'customizr') ?></h3>

              <div class="feature-section images-stagger-right">
                <a class="" title="Go Pro" href="<?php echo esc_url( CZR_WEBSITE . 'customizr-pro/' ); ?>" target="_blank"><img style="border:none;" alt="Customizr Pro" src="<?php echo TC_BASE_URL.'inc/admin/img/customizr-pro.png' ?>" class=""></a>
                <h4 style="text-align: left;max-width:inherit"><?php _e('Easily take your web design one step further' ,'customizr') ?></h4></br>

                <p style="text-align: lef;max-width:inherit"><?php _e("The Customizr Pro WordPress theme allows anyone to create a beautiful, professional and fully responsive website in a few seconds. In the Pro version, you'll get all the features of the free version plus some really cool and even revolutionary ones." , 'customizr') ?>
                </p>
                <p style="text-align:left;max-width:inherit">
                    <a class="button-primary review-customizr" title="<?php _e("Discover Customizr Pro",'customizr') ?>" href="<?php echo esc_url( CZR_WEBSITE . 'customizr-pro/' ); ?>" target="_blank"><?php _e("Discover Customizr Pro",'customizr') ?> &raquo;</a>
                </p>
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
      if( ! file_exists( TC_BASE . "readme.txt" ) ) {
        return;
      }
      if( ! is_readable( TC_BASE . "readme.txt" ) ) {
        echo '<p>The changelog in readme.txt is not readable.</p>';
        return;
      }

      $html = '';
      $stylelines = explode("\n", implode('', file( TC_BASE . "readme.txt" ) ) );
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

      ?>
<div class="wrap">
<h3><?php _e( 'System Informations', 'customizr' ); ?></h3>
<h4 style="text-align: left"><?php _e( 'Please include the following informations when posting support requests' , 'customizr' ) ?></h4>
<textarea readonly="readonly" onclick="this.focus();this.select()" id="system-info-textarea" name="tc-sysinfo" title="<?php _e( 'To copy the system infos, click below then press Ctrl + C (PC) or Cmd + C (Mac).', 'customizr' ); ?>" style="width: 800px;min-height: 800px;font-family: Menlo,Monaco,monospace;background: 0 0;white-space: pre;overflow: auto;display:block;">
<?php do_action( '__system_config_before' ); ?>
# SITE_URL:                 <?php echo site_url() . "\n"; ?>
# HOME_URL:                 <?php echo home_url() . "\n"; ?>
# IS MULTISITE :            <?php echo is_multisite() ? 'Yes' . "\n" : 'No' . "\n" ?>

# THEME | VERSION :         <?php printf( '%1$s | v%2$s', CZR___::$theme_name , CUSTOMIZR_VER ) . "\n"; ?>
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

  }//end of class
endif;

?><?php
/**
* Posts, pages and attachment actions and filters
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
if ( ! class_exists( 'CZR_meta_boxes' ) ) :
  class CZR_meta_boxes {
      static $instance;
      function __construct () {
          self::$instance =& $this;
          add_action( 'add_meta_boxes'                       , array( $this , 'czr_fn_post_meta_boxes' ));
          add_action( '__post_slider_infos'                  , array( $this , 'czr_fn_get_post_slider_infos' ));
          add_action( 'save_post'                            , array( $this , 'czr_fn_post_fields_save' ));

          add_action( 'add_meta_boxes'                       , array( $this , 'czr_fn_attachment_meta_box' ));
          add_action( '__attachment_slider_infos'            , array( $this , 'czr_fn_get_attachment_slider_infos' ));
          add_action( 'edit_attachment'                      , array( $this , 'czr_fn_slide_save' ));

          add_action( '__show_slides'                        , array( $this , 'czr_fn_show_slides' ), 10, 2);

          add_action( 'wp_ajax_slider_action'                , array( $this , 'czr_fn_slider_cb' ));

          add_action( 'admin_enqueue_scripts'                , array( $this , 'czr_fn_slider_admin_scripts' ));



        /**
         * checks if WP version strictly < 3.5
         * before 3.5, attachements were not managed as posts. But two filter hooks can are very useful
         * @package Customizr
         * @since Customizr 2.0
         */
        global $wp_version;
        if (version_compare( $wp_version, '3.5' , '<' ) ) {
            add_filter( 'attachment_fields_to_edit'           , array( $this , 'czr_fn_attachment_filter' ), 11, 2 );
            add_filter( 'attachment_fields_to_save'           , array( $this , 'czr_fn_attachment_save_filter' ), 11, 2 );
          }

      }//end of __construct



    /*
    ----------------------------------------------------------------
    -------- DEFINE POST/PAGE LAYOUT AND SLIDER META BOXES ---------
    ----------------------------------------------------------------
    */

    /**
     * Adds layout and slider metaboxes to pages and posts
     * @package Customizr
     * @since Customizr 1.0
     */
      function czr_fn_post_meta_boxes() {//id, title, callback, post_type, context, priority, callback_args
           /***
            Determines which screens we display the box
          **/
          //1 - retrieves the custom post types
          $args                 = array(
          //'public'   => true,
          '_builtin' => false
          );
          $custom_post_types    = get_post_types($args);

          //2 - Merging with the builtin post types, pages and posts
          $builtin_post_types   = array(
            'page' => 'page',
            'post' => 'post'
            );
          $screens              = array_merge( $custom_post_types, $builtin_post_types );

          //3- Adding the meta-boxes to those screens
          foreach ( $screens as $key => $screen) {
              //skip if acf or ris_gallery (ultimate responsive image slider)
              if ( in_array( $screen, array( 'acf', 'ris_gallery' ) ) )
                continue;

              add_meta_box(
                  'layout_sectionid' ,
                  __( 'Layout Options' , 'customizr' ),
                  array( $this , 'czr_fn_post_layout_box' ),
                  $screen,
                  ( 'page' == $screen | 'post' == $screen ) ? 'side' : 'normal',//displays meta box below editor for custom post types
                  apply_filters('tc_post_meta_boxes_priority' , 'high', $screen )
              );
              add_meta_box(
                  'slider_sectionid' ,
                  __( 'Slider Options' , 'customizr' ),
                  array( $this , 'czr_fn_post_slider_box' ),
                  $screen,
                  'normal' ,
                  apply_filters('tc_post_meta_boxes_priority' , 'high', $screen)
              );
          }//end foreach
      }







      /**
       * Prints the box content
       * @package Customizr
       * @since Customizr 1.0
       */
      function czr_fn_post_layout_box( $post ) {
            // Use nonce for verification
            wp_nonce_field( plugin_basename( __FILE__ ), 'post_layout_noncename' );

            // The actual fields for data entry
            // Use get_post_meta to retrieve an existing value from the database and use the value for the form
            //Layout name setup
            $layout_id            = 'layout_field';

            $layout_value         = esc_attr(get_post_meta( $post -> ID, $key = 'layout_key' , $single = true ));

            //Generates layouts select list array
            $layouts              = array();
            $global_layout        = apply_filters( 'tc_global_layout' , CZR_init::$instance -> global_layout );
            foreach ( $global_layout as $key => $value ) {
              $layouts[$key]      = call_user_func( '__' , $value['metabox'] , 'customizr' );
            }

            //by default we apply the global default layout
            $tc_sidebar_default_layout  = esc_attr( CZR_utils::$inst->czr_fn_opt('tc_sidebar_global_layout') );

            //get the lists of eligible post types + normal posts (not pages!)
            $args                 = array(
            //'public'   => true,
            '_builtin' => false
            );
            $custom_post_types    = get_post_types($args);
            $add_normal_post      = array(
              'post' => 'post'
              );
            $eligible_posts       = array_merge( $custom_post_types, $add_normal_post );

            //eligible posts (and custom posts types) default layout
            if ( in_array($post->post_type , $eligible_posts ) ) {
              $tc_sidebar_default_layout  = esc_attr( CZR_utils::$inst->czr_fn_opt('tc_sidebar_post_layout') );
            }

            //page default layout
            if ( $post->post_type == 'page' ) {
              $tc_sidebar_default_layout  = esc_attr( CZR_utils::$inst->czr_fn_opt('tc_sidebar_page_layout') );
            }

            //check if the 'force default layout' option is checked
            $force_layout                 = esc_attr( CZR_utils::$inst->czr_fn_opt('tc_sidebar_force_layout') );


            ?>
            <div class="meta-box-item-content">
              <?php if( $layout_value == null) : ?>
                <p><?php printf(__( 'Default %1$s layout is set to : %2$s' , 'customizr' ), $post -> post_type == 'page' ? __( 'pages' , 'customizr' ):__( 'posts' , 'customizr' ), '<strong>'.$layouts[$tc_sidebar_default_layout].'</strong>' ) ?></p>
              <?php endif; ?>

              <?php if ( $force_layout == 1) :?>
              <div style="width:99%; padding: 5px;">
                <p><i><?php _e( 'You have checked the <i>"Force global default layout for all posts and pages"</i>, you must unchecked this option to enable a specific layout for this post.' , 'customizr' ); ?></i></p>
                <p><a class="button-primary" href="<?php echo admin_url( 'customize.php' ); ?>" target="_blank"><?php _e( 'Change layout options' , 'customizr' ) ?></a></p>
              </div>

              <?php else : ?>
                  <i><?php printf(__( 'You can define a specific layout for %1$s by using the pre-defined left and right sidebars. The default layouts can be defined in the WordPress customizer screen %2$s.<br />' , 'customizr' ),
                    $post -> post_type == 'page' ? __( 'this page' , 'customizr' ):__( 'this post' , 'customizr' ),
                    '<a href="'.admin_url( 'customize.php' ).'" target="_blank">'.__( 'here' , 'customizr' ).'</a>'
                    ); ?>
                  </i>
                  <h4><?php printf(__( 'Select a specific layout for %1$s' , 'customizr' ),
                  $post -> post_type == 'page' ? __( 'this page' , 'customizr' ):__( 'this post' , 'customizr' )); ?></h4>
                  <select name="<?php echo $layout_id; ?>" id="<?php echo $layout_id; ?>">
                  <?php //no layout selected ?>
                    <option value="" <?php selected( $layout_value, $current = null, $echo = true ) ?>> <?php printf(__( 'Default layout %1s' , 'customizr' ),
                         '( '.$layouts[$tc_sidebar_default_layout].' )'
                         );
                      ?></option>
                    <?php foreach( $layouts as $key => $l) : ?>
                      <option value="<?php echo $key; ?>" <?php selected( $layout_value, $current = $key, $echo = true ) ?>><?php echo $l; ?></option>
                   <?php endforeach; ?>
                  </select>
             <?php endif; ?>
          </div>

          <?php
      }






      /*
      ----------------------------------------------------------------
      ------------------ POST/PAGE SLIDER BOX ------------------------
      ----------------------------------------------------------------
      */


      /**
       * Prints the slider box content
       * @package Customizr
       * @since Customizr 2.0
       */
        function czr_fn_post_slider_box( $post ) {
            // Use nonce for verification
            wp_nonce_field( plugin_basename( __FILE__ ), 'post_slider_noncename' );

            // The actual fields for data entry
            //title check field setup
            $post_slider_check_id       = 'post_slider_check_field';
            $post_slider_check_value    = esc_attr(get_post_meta( $post -> ID, $key = 'post_slider_check_key' , $single = true ));

            ?>
           <input name="tc_post_id" id="tc_post_id" type="hidden" value="<?php echo $post-> ID ?>"/>
           <div class="meta-box-item-title">
                <h4><?php _e( 'Add a slider to this post/page' , 'customizr' ); ?></h4>
                  <label for="<?php echo $post_slider_check_id; ?>">
              </label>
            </div>
            <div class="meta-box-item-content">
               <?php
                 $post_slider_checked = false;
                 if ( $post_slider_check_value == 1)
                  $post_slider_checked = true;
                ?>
              <input name="<?php echo $post_slider_check_id; ?>" type="hidden" value="0"/>
              <input name="<?php echo $post_slider_check_id ?>" id="<?php echo $post_slider_check_id; ?>" type="checkbox" class="iphonecheck" value="1" <?php checked( $post_slider_checked, $current = true, $echo = true ) ?>/>
            </div>
            <div id="slider-fields-box">
              <?php do_action( '__post_slider_infos' , $post -> ID ); ?>
            </div>
          <?php
      }//end of function





    /**
     * Display post slider dynamic content
     * This function is also called by the ajax call back
     * @package Customizr
     * @since Customizr 2.0
     */
      function czr_fn_get_post_slider_infos( $postid ) {
          //check value is ajax saved ?
          $post_slider_check_value   = esc_attr(get_post_meta( $postid, $key = 'post_slider_check_key' , $single = true ));

         //retrieve all sliders in option array
          $options                   = get_option( 'tc_theme_options' );
          if ( isset($options['tc_sliders']) ) {
            $sliders                   = $options['tc_sliders'];
          }else
            $sliders                   = array();

          //post slider fields setup
          $post_slider_id            = 'post_slider_field';

          //get current post slider
          $current_post_slider       = esc_attr(get_post_meta( $postid, $key = 'post_slider_key' , $single = true ));
          if ( isset( $sliders[$current_post_slider])) {
            $current_post_slides     = $sliders[$current_post_slider];
          }

          //Delay field setup
          $delay_id                  = 'slider_delay_field';
          $delay_value               = esc_attr(get_post_meta( $postid, $key = 'slider_delay_key' , $single = true ));

          //Layout field setup
          $layout_id                 = 'slider_layout_field';
          $layout_value              = esc_attr(get_post_meta( $postid, $key = 'slider_layout_key' , $single = true ));

          //sliders field
          $slider_id                 = 'slider_field';

          if( $post_slider_check_value == true ):
              $selectable_sliders    = apply_filters( 'tc_post_selectable_sliders', $sliders );
              if ( isset( $selectable_sliders ) && ! empty( $selectable_sliders ) ):

          ?>
              <div class="meta-box-item-title">
                <h4><?php _e("Choose a slider", 'customizr' ); ?></h4>
              </div>
          <?php
              //build selectable slider -> ID => label
              //Default in head
              $selectable_sliders = array_merge( array(
                -1 => __( '&mdash; Select a slider &mdash; ' , 'customizr' )
              ), $selectable_sliders );

              //in case of sliders of images we set the label as the slider_id
              if ( isset($sliders) && !empty( $sliders) )
                foreach ( $sliders as $key => $value) {
                  if ( is_array( $value ) )
                    $selectable_sliders[ $key ] = $key;
                }
          ?>
                <div class="meta-box-item-content">
                  <span class="spinner" style="float: left;visibility:visible;display: none;"></span>
                  <select name="<?php echo $post_slider_id; ?>" id="<?php echo $post_slider_id; ?>">
                  <?php //sliders select choices
                    foreach ( $selectable_sliders as $id => $label ) {
                      printf( '<option value="%1$s" %2$s> %3$s</option>',
                          esc_attr( $id ),
                          selected( $current_post_slider, esc_attr( $id ), $echo = false ),
                          $label
                      );
                    }
                  ?>
                  </select>
                   <i><?php _e( 'To create a new slider : open the media library, edit your images and add them to your new slider.' , 'customizr' ) ?></i>
                </div>

                <div class="meta-box-item-title">
                  <h4><?php _e("Delay between each slides in milliseconds (default : 5000 ms)", 'customizr' ); ?></h4>
                </div>
                <div class="meta-box-item-content">
                    <input name="<?php echo esc_attr( $delay_id) ; ?>" id="<?php echo esc_attr( $delay_id); ?>" value="<?php if (empty( $delay_value)) { echo '5000';} else {echo esc_attr( $delay_value);} ?>"/>
                </div>

                <div class="meta-box-item-title">
                    <h4><?php _e("Slider Layout : set the slider in full width", 'customizr' );  ?></h4>
                </div>
                <div class="meta-box-item-content">
                    <?php
                    if ( $layout_value ==null || $layout_value ==1 )
                    {
                      $layout_check_value = true;
                    }
                    else {
                      $layout_check_value = false;
                    }
                    ?>
                    <input name="<?php echo $layout_id; ?>" type="hidden" value="0"/>
                    <input name="<?php echo $layout_id; ?>" id="<?php echo $layout_id; ?>" type="checkbox" class="iphonecheck" value="1"<?php checked( $layout_check_value, $current = true, $echo = true ) ?>/>
                </div>
                <?php if (isset( $current_post_slides)) : ?>
                      <div style="z-index: 1000;position: relative;">
                        <p style="display: inline-block;float: right;"><a href="#TB_inline?width=350&height=100&inlineId=post_slider-warning-message" class="thickbox"><?php _e( 'Delete this slider' , 'customizr' ) ?></a></p>
                      </div>
                      <div id="post_slider-warning-message" style="display:none;">
                        <div style="text-align:center">
                           <p>
                             <?php _e( 'The slider will be deleted permanently (images, call to actions and link will be kept).' , 'customizr' ) ?>
                          </p>
                            <br/>
                             <a class="button-secondary" id="delete-slider" href="#" title="<?php _e( 'Delete slider' , 'customizr' ); ?>" onClick="javascript:window.parent.tb_remove()"><?php _e( 'Delete slider' , 'customizr' ); ?></a>
                        </div>
                      </div>
                    <?php  do_action( '__show_slides' , $current_post_slides, $current_attachement_id = null); ?>
                <?php else: //there are no slides
                  do_action( '__no_slides', $postid, $current_post_slider );
                ?>
              <?php endif; //slides? ?>
            <?php else://if no slider created yet and no slider of posts addon?>

                 <div class="meta-box-item-content">
                   <p class="description"> <?php _e("You haven't create any slider yet. Go to the media library, edit your images and add them to your sliders.", "customizr" ) ?><br/>
                   </p>
                    <br />
                </div>
              <?php endif; //sliders? ?>
            <?php endif; //check slider? ?>
        <?php
      }






      /*
      ----------------------------------------------------------------
      ------- SAVE POST/PAGE FIELDS (LAYOUT AND SLIDER FIELDS) -------
      ----------------------------------------------------------------
      */
      /**
       * When the post/page is saved, saves our custom data for slider and layout options
       * @package Customizr
       * @since Customizr 1.0
       */
      function czr_fn_post_fields_save( $post_id ) {
        // verify if this is an auto save routine.
        // If it is our form has not been submitted, so we dont want to do anything
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return;

        // Check permissions
        if ( isset( $_POST['post_type']) && 'page' == $_POST['post_type'] )
        {
          if ( !current_user_can( 'edit_page' , $post_id ) )
              return;
        }
        else
        {
          if ( !current_user_can( 'edit_post' , $post_id ) )
              return;
        }

        ################# LAYOUT BOX #################
        // verify this came from our screen and with proper authorization,
        if ( isset( $_POST['post_layout_noncename']) && !wp_verify_nonce( $_POST['post_layout_noncename'], plugin_basename( __FILE__ ) ) )
            return;

        // OK, we're authenticated: we need to find and save the data
        //set up the fields array
        $tc_post_layout_fields = array(
            'layout_field'              =>  'layout_key'
            );

        //if saving in a custom table, get post_ID
       if ( isset( $_POST['post_ID'])) {
          $post_ID = $_POST['post_ID'];
          //sanitize user input by looping on the fields
          foreach ( $tc_post_layout_fields as $tcid => $tckey) {
              if ( isset( $_POST[$tcid])) {
                $mydata = sanitize_text_field( $_POST[$tcid] );

                // Do something with $mydata
                // either using
                add_post_meta( $post_ID, $tckey, $mydata, true) or
                  update_post_meta( $post_ID, $tckey , $mydata);
                // or a custom table (see Further Reading section below)
              }
             }
        }

        ################# SLIDER BOX #################
        // verify this came from our screen and with proper authorization,
        if ( isset( $_POST['post_slider_noncename']) && !wp_verify_nonce( $_POST['post_slider_noncename'], plugin_basename( __FILE__ ) ) )
            return;

        // OK, we're authenticated: we need to find and save the data
        //set up the fields array
        $tc_post_slider_fields = array(
            'post_slider_check_field'   => 'post_slider_check_key' ,
            'slider_delay_field'        => 'slider_delay_key' ,
            'slider_layout_field'       => 'slider_layout_key' ,
            'post_slider_field'         => 'post_slider_key' ,
            );

        //if saving in a custom table, get post_ID
       if ( isset( $_POST['post_ID'])) {
          do_action( '__before_save_post_slider_fields', $_POST, $tc_post_slider_fields );
          $post_ID = $_POST['post_ID'];
          //sanitize user input by looping on the fields
          foreach ( $tc_post_slider_fields as $tcid => $tckey) {
            if ( isset( $_POST[$tcid])) {
                $mydata = sanitize_text_field( $_POST[$tcid] );

                // Do something with $mydata
                // either using
                add_post_meta( $post_ID, $tckey, $mydata, true) or
                  update_post_meta( $post_ID, $tckey , $mydata);
                // or a custom table (see Further Reading section below)
            }
          }
          do_action( '__after_save_post_slider_fields', $_POST, $tc_post_slider_fields );
        }
      }





      /*
      ----------------------------------------------------------------
      ------------------ ATTACHMENT SLIDER META BOX ------------------
      ----------------------------------------------------------------
      */
      /**
       * Add a slider metabox to attachments
       * @package Customizr
       * @since Customizr 2.0
       */
        function czr_fn_attachment_meta_box() {//id, title, callback, post_type, context, priority, callback_args
          $screens = array( 'attachment' );
          foreach ( $screens as $screen) {
              add_meta_box(
                  'slider_sectionid' ,
                  __( 'Slider Options' , 'customizr' ),
                  array( $this , 'czr_fn_attachment_slider_box' ),
                  $screen/*,
                  'side' ,
                  'high'*/
              );
            }
        }






      /**
       * Prints the slider box content
       * @package Customizr
       * @since Customizr 2.0
       */
        function czr_fn_attachment_slider_box( $post ) {
            // Use nonce for verification
            //wp_nonce_field( plugin_basename( __FILE__ ), 'slider_noncename' );
            // The actual fields for data entry
            //title check field setup
            $slider_check_id       = 'slider_check_field';
            $slider_check_value    = esc_attr(get_post_meta( $post -> ID, $key = 'slider_check_key' , $single = true ));

            ?>
           <div class="meta-box-item-title">
                <h4><?php _e( 'Add to a slider' , 'customizr' ); ?></h4>
                  <label for="<?php echo $slider_check_id; ?>">
                </i><?php _e( 'Add to a slider (create one if needed)' , 'customizr' ) ?></i>
              </label>
            </div>
            <div class="meta-box-item-content">
              <input name="tc_post_id" id="tc_post_id" type="hidden" value="<?php echo $post-> ID ?>"/>
               <?php
                 $slider_checked = false;
                 if ( $slider_check_value == 1)
                  $slider_checked = true;
                ?>
              <input name="<?php echo $slider_check_id; ?>" type="hidden" value="0"/>
              <input name="<?php echo $slider_check_id ?>" id="<?php echo $slider_check_id; ?>" type="checkbox" class="iphonecheck" value="1" <?php checked( $slider_checked, $current = true, $echo = true ) ?>/>
            </div>
           <div id="slider-fields-box">
             <?php do_action( '__attachment_slider_infos' , $post -> ID); ?>
           </div>
          <?php
      }







      /**
       * Display attachment slider dynamic content
       * This function is also called by the ajax call back function
       * @package Customizr
       * @since Customizr 2.0
       */
        function czr_fn_get_attachment_slider_infos( $postid ) {
          //check value is ajax saved ?
          $slider_check_value     = esc_attr(get_post_meta( $postid, $key = 'slider_check_key' , $single = true ));

          //post slider fields setup
          $post_slider_id         = 'post_slider_field';

          //sliders field
          $slider_id              = 'slider_field';

          //retrieve all sliders in option array
          $options                = get_option( 'tc_theme_options' );
          $sliders                = array();
          if ( isset( $options['tc_sliders'])) {
            $sliders              = $options['tc_sliders'];
          }

          //get_attachment details for default slide values
          $attachment             = get_post( $postid);
          $default_title          = $attachment->post_title;
          $default_description    = $attachment->post_excerpt;

          //title field setup
          $title_id               = 'slide_title_field';
          $title_value            = esc_attr(get_post_meta( $postid, $key = 'slide_title_key' , $single = true ));
          //we define a filter for the slide_text_length
          $default_title_length   = apply_filters( 'tc_slide_title_length', 80 );

          //check if we already have a custom key created for this field, if not apply default value
          if(!in_array( 'slide_title_key' ,get_post_custom_keys( $postid))) {
            $title_value = $default_title;
          }
          if (strlen( $title_value) > $default_title_length) {
            $title_value = substr( $title_value,0,strpos( $title_value, ' ' , $default_title_length));
            $title_value = esc_html( $title_value) . ' ...';
          }
          else {
            $title_value = esc_html( $title_value);
          }


          //text_field setup : sanitize and limit length
          $text_id        = 'slide_text_field';
          $text_value     = esc_html(get_post_meta( $postid, $key = 'slide_text_key' , $single = true ));
           //we define a filter for the slide_title_length
          $default_text_length   = apply_filters( 'tc_slide_text_length', 250 );

           //check if we already have a custom key created for this field, if not apply default value
          if(!in_array( 'slide_text_key' ,get_post_custom_keys( $postid)))
            $text_value = $default_description;

          if (strlen( $text_value) > $default_text_length) {
            $text_value = substr( $text_value,0,strpos( $text_value, ' ' ,$default_text_length));
            $text_value = $text_value . ' ...';
          }
          else {
            $text_value = $text_value;
          }

           //Color field setup
          $color_id       = 'slide_color_field';
          $color_value    = esc_attr(get_post_meta( $postid, $key = 'slide_color_key' , $single = true ));

          //button field setup
          $button_id      = 'slide_button_field';
          $button_value   = esc_attr(get_post_meta( $postid, $key = 'slide_button_key' , $single = true ));
          //we define a filter for the slide text_button length
          $default_button_length   = apply_filters( 'tc_slide_button_length', 80 );

          if (strlen( $button_value) > $default_button_length) {
            $button_value = substr( $button_value,0,strpos( $button_value, ' ' ,$default_button_length));
            $button_value = $button_value . ' ...';
          }
          else {
            $button_value = $button_value;
          }

          //link field setup
          $link_id        = 'slide_link_field';
          $link_value     = esc_attr(get_post_meta( $postid, $key = 'slide_link_key' , $single = true ));

          //retrieve post, pages and custom post types (if any) and generate the ordered select list for the button link
          $post_types     = get_post_types(array( 'public' => true));
          $excludes       = array( 'attachment' );


          foreach ( $post_types as $t) {
              if (!in_array( $t, $excludes)) {
               //get the posts a tab of types
               $tc_all_posts[$t] = get_posts(  array(
                  'numberposts'     =>  100,
                  'orderby'         =>  'date' ,
                  'order'           =>  'DESC' ,
                  'post_type'       =>  $t,
                  'post_status'     =>  'publish' )
                );
              }
            };

          //custom link field setup
          $custom_link_id    = 'slide_custom_link_field';
          $custom_link_value = esc_url( get_post_meta( $postid, $key = 'slide_custom_link_key', $single = true ) );

          //link target setup
          $link_target_id    = 'slide_link_target_field';
          $link_target_value = esc_attr( get_post_meta( $postid, $key = 'slide_link_target_key', $single = true ) ) ;

          //link whole slide setup
          $link_whole_slide_id    = 'slide_link_whole_slide_field';
          $link_whole_slide_value = esc_attr( get_post_meta( $postid, $key = 'slide_link_whole_slide_key', $single = true ) ) ;

          //display fields if slider button is checked
          if ( $slider_check_value == true )  {
             ?>
            <div class="meta-box-item-title">
                <h4><?php _e( 'Title text (80 char. max length)' , 'customizr' ); ?></h4>
            </div>
            <div class="meta-box-item-content">
                <input class="widefat" name="<?php echo esc_attr( $title_id); ?>" id="<?php echo esc_attr( $title_id); ?>" value="<?php echo esc_attr( $title_value); ?>" style="width:50%">
            </div>

            <div class="meta-box-item-title">
                <h4><?php _e( 'Description text (below the title, 250 char. max length)' , 'customizr' ); ?></h4>
            </div>
            <div class="meta-box-item-content">
                <textarea name="<?php echo esc_attr( $text_id); ?>" id="<?php echo esc_attr( $text_id); ?>" style="width:50%"><?php echo esc_attr( $text_value); ?></textarea>
            </div>

             <div class="meta-box-item-title">
                <h4><?php _e("Title and text color", 'customizr' );  ?></h4>
            </div>
            <div class="meta-box-item-content">
                <input id="<?php echo esc_attr( $color_id); ?>" name="<?php echo esc_attr( $color_id); ?>" value="<?php echo esc_attr( $color_value); ?>"/>
                <div id="colorpicker"></div>
            </div>

             <div class="meta-box-item-title">
                <h4><?php _e( 'Button text (80 char. max length)' , 'customizr' ); ?></h4>
            </div>
            <div class="meta-box-item-content">
                <input class="widefat" name="<?php echo esc_attr( $button_id); ?>" id="<?php echo esc_attr( $button_id); ?>" value="<?php echo esc_attr( $button_value); ?>" style="width:50%">
            </div>

            <div class="meta-box-item-title">
                <h4><?php _e("Choose a linked page or post (among the last 100).", 'customizr' ); ?></h4>
            </div>
            <div class="meta-box-item-content">
                <select name="<?php echo esc_attr( $link_id); ?>" id="<?php echo esc_attr( $link_id); ?>">
                  <?php //no link option ?>
                  <option value="" <?php selected( $link_value, $current = null, $echo = true ) ?>> <?php _e( 'No link' , 'customizr' ); ?></option>
                  <?php foreach( $tc_all_posts as $type) : ?>
                      <?php foreach ( $type as $key => $item) : ?>
                    <option value="<?php echo esc_attr( $item -> ID); ?>" <?php selected( $link_value, $current = $item -> ID, $echo = true ) ?>>{<?php echo esc_attr( $item -> post_type) ;?>}&nbsp;<?php echo esc_attr( $item -> post_title); ?></option>
                      <?php endforeach; ?>
                 <?php endforeach; ?>
                </select><br />
            </div>
            <div class="meta-box-item-title">
                <h4><?php _e("or a custom link (leave this empty if you already selected a page or post above)", 'customizr' ); ?></h4>
            </div>
            <div class="meta-box-item-content">
                <input class="widefat" name="<?php echo $custom_link_id; ?>" id="<?php echo $custom_link_id; ?>" value="<?php echo $custom_link_value; ?>" style="width:50%">
            </div>
            <div class="meta-box-item-title">
                <h4><?php _e("Open link in a new page/tab", 'customizr' );  ?></h4>
            </div>
            <div class="meta-box-item-content">
                <input name="<?php echo $link_target_id; ?>" type="hidden" value="0"/>
                <input name="<?php echo $link_target_id; ?>" id="<?php echo $link_target_id; ?>" type="checkbox" class="iphonecheck" value="1" <?php checked( $link_target_value, $current = true, $echo = true ) ?>/>
            </div>
            <div class="meta-box-item-title">
                <h4><?php _e("Link the whole slide", 'customizr' );  ?></h4>
            </div>
            <div class="meta-box-item-content">
                <input name="<?php echo $link_whole_slide_id; ?>" type="hidden" value="0"/>
                <input name="<?php echo $link_whole_slide_id; ?>" id="<?php echo $link_whole_slide_id; ?>" type="checkbox" class="iphonecheck" value="1" <?php checked( $link_whole_slide_value, $current = true, $echo = true ) ?>/>
            </div>
            <div class="meta-box-item-title">
              <h4><?php _e("Choose a slider", 'customizr' ); ?></h4>
            </div>
            <?php if (!empty( $sliders)) : ?>
              <div class="meta-box-item-content">
                  <?php //get current post slider
                    $current_post_slider = null;
                    foreach( $sliders as $slider_name => $slider_posts) {
                       if (in_array( $postid, $slider_posts)) {
                            $current_post_slider = $slider_name;
                            $current_post_slides = $slider_posts;
                        }
                    }
                  ?>
                  <select name="<?php echo esc_attr( $post_slider_id); ?>" id="<?php echo esc_attr( $post_slider_id); ?>">
                    <?php //no link option ?>
                    <option value="" <?php selected( $current_post_slider, $current = null, $echo = true ) ?>> <?php _e( '&mdash; Select a slider &mdash; ' , 'customizr' ); ?></option>
                       <?php foreach( $sliders as $slider_name => $slider_posts) : ?>
                            <option value="<?php echo $slider_name ?>" <?php selected( $slider_name, $current = $current_post_slider, $echo = true ) ?>><?php echo $slider_name?></option>
                       <?php endforeach; ?>
                  </select>
                  <input name="<?php echo $slider_id  ?>" id="<?php echo $slider_id ?>" value=""/>
                  <span class="button-primary" id="tc_create_slider"><?php _e( 'Add a slider' , 'customizr' ) ?></span>
                  <span class="spinner" style="float: left;visibility:visible;display: none;"></span>
                  <?php if (isset( $current_post_slides)) : ?>
                      <p style="text-align:right"><a href="#TB_inline?width=350&height=100&inlineId=slider-warning-message" class="thickbox"><?php _e( 'Delete this slider' , 'customizr' ) ?></a></p>
                      <div id="slider-warning-message" style="display:none;">
                        <div style="text-align:center">
                           <p>
                             <?php _e( 'The slider will be deleted permanently (images, call to actions and link will be kept).' , 'customizr' ) ?>
                          </p>
                            <br/>
                             <a class="button-secondary" id="delete-slider" href="#" title="<?php _e( 'Delete slider' , 'customizr' ); ?>" onClick="javascript:window.parent.tb_remove()"><?php _e( 'Delete slider' , 'customizr' ); ?></a>
                        </div>
                      </div>
                  <?php endif; ?>
                </div>


                <?php
                  if ( isset( $current_post_slides) ) {
                    $current_attachement_id = $postid;
                    do_action( '__show_slides' ,$current_post_slides, $current_attachement_id);
                  }
                ?>

            <?php else : //if no slider created yet ?>

                 <div class="meta-box-item-content">
                   <p class="description"> <?php _e("You haven't create any slider yet. Write a slider name and click on the button to add you first slider.", "customizr" ) ?><br/>
                    <input name="<?php echo $slider_id  ?>" id="<?php echo $slider_id ?>" value=""/>
                    <span class="button-primary" id="tc_create_slider"><?php _e( 'Add a slider' , 'customizr' ) ?></span>
                    <span class="spinner" style="float: left; diplay:none;"></span>
                   </p>
                    <br />
                </div>
            <?php endif; ?>
              <?php
          }//endif slider checked (used for ajax call back!)
      }





      /*
      ----------------------------------------------------------------
      -------------------- SAVE ATTACHMENT FIELDS --------------------
      ----------------------------------------------------------------
      */

      /**
       * When the attachment is saved, saves our custom slider data
       * @package Customizr
       * @since Customizr 2.0
       */
        function czr_fn_slide_save( $post_id ) {
          // verify if this is an auto save routine.
          // If it is our form has not been submitted, so we dont want to do anything


          if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
              return;

          // verify this came from our screen and with proper authorization,
          // because save_post can be triggered at other times

          if ( isset( $_POST['slider_noncename']) && !wp_verify_nonce( $_POST['slider_noncename'], plugin_basename( __FILE__ ) ) )
              return;

          // Check permissions
          if ( !current_user_can( 'edit_post' , $post_id ) )
                return;

          // OK, we're authenticated: we need to find and save the data

          //set up the fields array
          $tc_slider_fields = array(
              'slide_title_field'             => 'slide_title_key' ,
              'slide_text_field'              => 'slide_text_key' ,
              'slide_color_field'             => 'slide_color_key' ,
              'slide_button_field'            => 'slide_button_key' ,
              'slide_link_field'              => 'slide_link_key' ,
              'slide_custom_link_field'       => 'slide_custom_link_key',
              'slide_link_target_field'       => 'slide_link_target_key',
              'slide_link_whole_slide_field'  => 'slide_link_whole_slide_key'
          );

          //if saving in a custom table, get post_ID
          if ( $post_id == null)
            return;

            //sanitize user input by looping on the fields
            foreach ( $tc_slider_fields as $tcid => $tckey) {
                if ( isset( $_POST[$tcid])) {
                  $mydata = sanitize_text_field( $_POST[$tcid] );
                    switch ( $tckey) {
                      //different sanitizations
                      case 'slide_text_key':
                          $default_text_length = apply_filters( 'tc_slide_text_length', 250 );
                          if (strlen( $mydata) > $default_text_length) {
                            $mydata = substr( $mydata,0,strpos( $mydata, ' ' ,$default_text_length));
                            $mydata = esc_html( $mydata) . ' ...';
                          }
                          else {
                            $mydata = esc_html( $mydata);
                          }
                        break;

                      case 'slide_custom_link_key':
                          $mydata = esc_url( $_POST[$tcid] );
                      break;

                      case 'slide_link_target_key';
                      case 'slide_link_whole_slide_key':
                          $mydata = esc_attr( $mydata );
                      break;

                      default://for button, color, title and post link field (actually not a link but an id)
                          $default_title_length = apply_filters( 'tc_slide_title_length', 80 );
                         if (strlen( $mydata) > $default_title_length) {
                          $mydata = substr( $mydata,0,strpos( $mydata, ' ' , $default_title_length));
                          $mydata = esc_attr( $mydata) . ' ...';
                          }
                          else {
                            $mydata = esc_attr( $mydata);
                          }
                        break;
                    }//end switch
                  //write in DB
                  add_post_meta( $post_id, $tckey, $mydata, true) or
                  update_post_meta( $post_id, $tckey , $mydata);
                }//end if isset $tckey
            }//end foreach
        }






      /*
      ----------------------------------------------------------------
      ---------- DISPLAY SLIDES TABLE (post and attachment) ----------
      ----------------------------------------------------------------
      */

      /**
       * Display slides table dynamic content for the selected slider
       * @package Customizr
       * @since Customizr 2.0
       */
      function czr_fn_show_slides ( $current_post_slides,$current_attachement_id) {
          //check if we have slides to show
          ?>
          <?php if(empty( $current_post_slides)) : ?>
            <div class="meta-box-item-content">
               <p class="description"> <?php _e("This slider has not slides to show. Go to the media library and start adding images to it.", "customizr" ) ?><br/>
               </p>
              <br />
            </div>
          <?php else : // render?>
            <div id="tc_slides_table">
              <div id="update-status"></div>
                  <table class="wp-list-table widefat fixed media" cellspacing="0">
                    <thead>
                        <tr>
                          <th scope="col"><?php _e( 'Slide Image' , 'customizr' ) ?></th>
                          <th scope="col"><?php _e( 'Title' , 'customizr' ) ?></th>
                          <th scope="col" style="width: 35%"><?php _e( 'Slide Text' , 'customizr' ) ?></th>
                          <th scope="col"><?php _e( 'Button Text' , 'customizr' ) ?></th>
                          <th scope="col"><?php _e( 'Link' , 'customizr' ) ?></th>
                          <th scope="col"><?php _e( 'Edit' , 'customizr' ) ?></th>
                        </tr>
                      </thead>
                    <tbody id="sortable">
                      <?php
                      //loop on the slides and render if the selected slider is checked
                      foreach ( $current_post_slides as $index => $slide) {
                        //get the attachment object
                        $tc_slide = get_post( $slide );

                        //check if $tc_slide object exists otherwise go to the next iteration
                        if (!isset( $tc_slide))
                          continue;

                        //check if slider is checked for this attachment => otherwise go to the next iteration
                        $slider_check_value     = esc_attr(get_post_meta( $tc_slide -> ID, $key = 'slider_check_key' , $single = true ));
                        if ( $slider_check_value == false)
                          continue;

                        //set up variables
                        $id                     = $tc_slide -> ID;
                        $slide_src              = wp_get_attachment_image_src( $id, 'thumbnail' );
                        $slide_url              = $slide_src[0];
                        $title                  = esc_attr(get_post_meta( $id, $key = 'slide_title_key' , $single = true ));
                        $text                   = esc_html(get_post_meta( $id, $key = 'slide_text_key' , $single = true ));
                        $text_color             = esc_attr(get_post_meta( $id, $key = 'slide_color_key' , $single = true ));
                        $button_text            = esc_attr(get_post_meta( $id, $key = 'slide_button_key' , $single = true ));
                        $link                   = esc_url(get_post_meta( $id, $key = 'slide_custom_link_key' , $single = true ));
                        $button_link            = esc_attr(get_post_meta( $id, $key = 'slide_link_key' , $single = true ));

                        //check if $text_color is set and create an html style attribute
                        $color_style ='';
                        if( $text_color != null) {
                          $color_style = 'style="color:'.$text_color.'"';
                        }
                        ?>
                        <tr id="<?php echo $index ?>" class="ui-state-default" valign="middle">
                          <td style="vertical-align:middle" class="column-icon">
                              <?php if( $slide_url != null) : ?>
                                <img width="100" height="100" src="<?php echo $slide_url; ?>" class="attachment-80x60" alt="Hydrangeas">
                              <?php else : ?>
                                <div style="height:100px;width:100px;background:#eee;text-align:center;line-height:100px;vertical-align:middle">
                                  <?php _e( 'No Image Selected' , 'customizr' ); ?>
                                </div>
                              <?php endif; ?>
                          </td>
                          <td style="vertical-align:middle" class="">
                              <?php if( $title != null) : ?>
                                <p <?php echo $color_style ?>><strong><?php echo $title ?></strong></p>
                              <?php endif; ?>
                          </td>
                          <td style="vertical-align:middle" class="">
                               <?php if( $text != null) : ?>
                                <p <?php echo $color_style ?> class="lead"><?php echo $text ?></p>
                              <?php endif; ?>
                          </td>
                          <td style="vertical-align:middle" class="">
                              <?php if( $button_text != null) : ?>
                                <p class="btn btn-large btn-primary"><?php echo $button_text; ?></p>
                              <?php endif; ?>
                          </td>
                           <td style="vertical-align:middle" class="">
                              <?php if( $button_link != null || $link != null ) : ?>
                                <p class="btn btn-large btn-primary" href="<?php echo $link ? $link : get_permalink( $button_link); ?>"><?php echo $link ? $link : get_the_title( $button_link); ?></p>
                              <?php endif; ?>
                          </td>
                           <td style="vertical-align:middle" class="">
                              <?php if( $id != $current_attachement_id) : ?>
                                <a class="button-primary" href="<?php echo admin_url( 'post.php?post='.$id.'&action=edit' ) ?>" target="_blank"><?php _e( 'Edit this slide' , 'customizr' )?></a>
                              <?php else : ?>
                                <span style="color:#999898"><?php _e( 'Current slide' , 'customizr' )?></span>
                              <?php endif; ?>
                          </td>
                        </tr>
                        <?php
                      }//end foreach
                   echo '</tbody></table><br/>';
                   ?>
                   <div class="tc-add-slide-notice">
                      <?php
                        printf('<p>%1$s</p><p>%2$s <a href="%3$s" title="%4$s" target="_blank">%4$s &raquo;</a>.</p>',
                          __('To add another slide : navigate to your media library (click on Media), open the edit screen of an image ( or add a new image ), and add it to your desired slider by using the dedicated option block at the bottom of the page.' , 'customizr'),
                          __('For more informations about sliders, check the documentation page :' , 'customizr'),
                          esc_url('http://docs.presscustomizr.com/search?query=slider'),
                          __('Slider documentation' , 'customizr')
                        );
                      ?>
                   </div>
              </div><!-- //#tc_slides_table -->
         <?php endif; // empty( $current_post_slides? ?>
        <?php
      }





      /*
      ----------------------------------------------------------------
      ---------------- AJAX SAVE (post and attachment) ---------------
      ----------------------------------------------------------------
      */
      /**
       * Ajax saving of options and meta fields in DB for post and attachement screens
       * works along with tc_ajax_slider.js
       * @package Customizr
       * @since Customizr 2.0
       */
      function czr_fn_slider_ajax_save( $post_id ) {

            //We check the ajax nonce (common for post and attachment)
            if ( isset( $_POST['SliderCheckNonce']) && !wp_verify_nonce( $_POST['SliderCheckNonce'], 'tc-slider-check-nonce' ) )
                return;

            // Check permissions
            if ( !current_user_can( 'edit_post' , $post_id ) )
                return;

            // Do we have a post_id?
            if ( !isset( $_POST['tc_post_id'])) {
                return;
            }
            else {
                $post_ID = $_POST['tc_post_id'];
            }

            //OPTION FIELDS
            //get options and some useful $_POST vars
            $tc_options                 = get_option( 'tc_theme_options' );

            if (isset( $_POST['tc_post_type']))
              $tc_post_type             = esc_attr( $_POST['tc_post_type']);
            if (isset( $_POST['currentpostslider']))
              $current_post_slider      = esc_attr( $_POST['currentpostslider']);
            if (isset( $_POST['new_slider_name']))
              $new_slider_name          = esc_attr( $_POST['new_slider_name'] );

            //Save user input by looping on the fields
            foreach ( $_POST as $tckey => $tcvalue) {
                switch ( $tckey) {
                  //delete slider
                  case 'delete_slider':
                    //first we delete the meta fields related to the deleted slider
                    //which screen are we coming from?
                    if( $tc_post_type == 'attachment' ) {
                      query_posts( 'meta_key=post_slider_key&meta_value='.$current_post_slider);
                      //we loop the posts with the deleted slider meta key
                        if(have_posts()) {
                          while ( have_posts() ) : the_post();
                              //delete the post meta
                              delete_post_meta(get_the_ID(), $key = 'post_slider_key' );
                          endwhile;
                        }
                      wp_reset_query();
                    }

                    //we delete from the post/page screen
                    else {
                      $post_slider_meta = esc_attr(get_post_meta( $post_ID, $key = 'post_slider_key' , $single = true ));
                      if(!empty( $post_slider_meta)) {
                        delete_post_meta( $post_ID, $key = 'post_slider_key' );
                      }
                    }

                    //in all cases, delete DB option
                    unset( $tc_options['tc_sliders'][$current_post_slider]);
                    //update DB with new slider array
                    update_option( 'tc_theme_options' , $tc_options );
                  break;


                  //reorder slides
                  case 'newOrder':
                      //turn new order into array
                      if(!empty( $tcvalue))

                      $neworder = explode( ',' , esc_attr( $tcvalue ));

                      //initialize the newslider array
                      $newslider = array();

                      foreach ( $neworder as $new_key => $new_index) {
                          $newslider[$new_index] =  $tc_options['tc_sliders'][$current_post_slider][$new_index];
                      }

                      $tc_options['tc_sliders'][$current_post_slider] = $newslider;

                       //update DB with new slider array
                      update_option( 'tc_theme_options' , $tc_options );
                    break;




                  //sliders are added in options
                  case 'new_slider_name':
                      //check if we have something to save
                      $new_slider_name                                  = esc_attr( $tcvalue );
                      $delete_slider                                    = false;
                      if ( isset( $_POST['delete_slider']))
                          $delete_slider                                = $_POST['delete_slider'];

                      //prevent saving if we delete
                      if (!empty( $new_slider_name) && $delete_slider != true) {
                          $new_slider_name                              = wp_filter_nohtml_kses( $tcvalue );
                          //remove spaces and special char
                          $new_slider_name                              = strtolower(preg_replace("![^a-z0-9]+!i", "-", $new_slider_name));

                          $tc_options['tc_sliders'][$new_slider_name]      = array( $post_ID);
                          //adds the new slider name in DB options
                          update_option( 'tc_theme_options' , $tc_options );
                        //associate the current post with the new saved slider

                        //looks for a previous slider entry and delete it
                        foreach ( $tc_options['tc_sliders'] as $slider_name => $slider) {

                          foreach ( $slider as $key => $tc_post) {
                             //clean empty values if necessary
                             if ( is_null( $tc_options['tc_sliders'][$slider_name][$key]))
                                unset( $tc_options['tc_sliders'][$slider_name][$key]);

                             //delete previous slider entries for this post
                             if ( $tc_post == $post_ID )
                                unset( $tc_options['tc_sliders'][$slider_name][$key]);
                            }
                          }

                          //update DB with clean option table
                          update_option( 'tc_theme_options' , $tc_options );

                          //push new post value for the new slider and write in DB
                          array_push( $tc_options['tc_sliders'][$new_slider_name], $post_ID);
                          update_option( 'tc_theme_options' , $tc_options );

                        }

                    break;

                    //post slider value
                    case 'post_slider_name':
                        //check if we display the attachment screen
                        if (!isset( $_POST['slider_check_field'])) {
                          break;
                        }
                        //we are in the attachment screen and we uncheck slider options checkbox
                        elseif ( $_POST['slider_check_field'] == 0) {
                          break;
                        }

                        //if we are in the slider creation case, the selected slider has to be the new one!
                        if (!empty( $new_slider_name))
                          break;

                        //check if we have something to save
                        $post_slider_name                   = esc_attr( $tcvalue );

                        //check if we have an input and if we are not in the slider creation case
                        if (!empty( $post_slider_name)) {

                           $post_slider_name                = wp_filter_nohtml_kses( $post_slider_name );
                            //looks for a previous slider entry and delete it.
                           //Important : we check if the slider has slides first!
                              foreach ( $tc_options['tc_sliders'] as $slider_name => $slider) {
                                foreach ( $slider as $key => $tc_post) {

                                  //clean empty values if necessary
                                  if ( is_null( $tc_options['tc_sliders'][$slider_name][$key])) {
                                      unset( $tc_options['tc_sliders'][$slider_name][$key]);
                                  }

                                  //clean slides with no images
                                  $slide_img = wp_get_attachment_image( $tc_options['tc_sliders'][$slider_name][$key]);
                                  if (isset($slide_img) && empty($slide_img)) {
                                      unset( $tc_options['tc_sliders'][$slider_name][$key]);
                                  }

                                 //delete previous slider entries for this post
                                 if ( $tc_post == $post_ID ) {
                                    unset( $tc_options['tc_sliders'][$slider_name][$key]);
                                  }

                                }//end for each
                              }
                              //update DB with clean option table
                              update_option( 'tc_theme_options' , $tc_options );

                            //check if the selected slider is empty and set it as array
                            if( empty( $tc_options['tc_sliders'][$post_slider_name]) ) {
                              $tc_options['tc_sliders'][$post_slider_name] = array();
                            }

                            //push new post value for the slider and write in DB
                              array_push( $tc_options['tc_sliders'][$post_slider_name], $post_ID);
                              update_option( 'tc_theme_options' , $tc_options );
                        }//end if !empty( $post_slider_name)

                        //No slider selected
                        else {
                          //looks for a previous slider entry and delete it
                            foreach ( $tc_options['tc_sliders'] as $slider_name => $slider) {
                              foreach ( $slider as $key => $tc_post) {
                                 //clean empty values if necessary
                                 if ( is_null( $tc_options['tc_sliders'][$slider_name][$key]))
                                    unset( $tc_options['tc_sliders'][$slider_name][$key]);
                                 //delete previous slider entries for this post
                                 if ( $tc_post == $post_ID )
                                    unset( $tc_options['tc_sliders'][$slider_name][$key]);
                              }
                            }
                            //update DB with clean option table
                            update_option( 'tc_theme_options' , $tc_options );
                        }
                      break;
                  }//end switch
               }//end foreach

              //POST META FIELDS
              //set up the fields array
              $tc_slider_fields = array(
                //posts & pages
                'post_slider_name'            => 'post_slider_key' ,
                'post_slider_check_field'     => 'post_slider_check_key' ,
                //attachments
                'slider_check_field'          => 'slider_check_key' ,
              );

              do_action( "__before_ajax_save_slider_{$tc_post_type}", $_POST, $tc_slider_fields );
                //sanitize user input by looping on the fields
                foreach ( $tc_slider_fields as $tcid => $tckey) {
                    if ( isset( $_POST[$tcid])) {
                        switch ( $tckey) {
                          //different sanitizations
                          //the slider name custom field for a post/page
                          case 'post_slider_key' :
                             $mydata = esc_attr( $_POST[$tcid] );
                             //Does the selected slider still exists in options? (we first check if the selected slider is not empty)
                             if(!empty( $mydata) && !isset( $tc_options['tc_sliders'][$mydata]))
                                break;

                             //write in DB
                              add_post_meta( $post_ID, $tckey, $mydata, true) or
                                update_post_meta( $post_ID, $tckey , $mydata);
                          break;


                          //inserted/updated in all cases
                          case 'post_slider_check_key':
                          case 'slider_check_key':
                             $mydata = esc_attr( $_POST[$tcid] );
                             //write in DB
                              add_post_meta( $post_ID, $tckey, $mydata, true) or
                                update_post_meta( $post_ID, $tckey , $mydata);

                             //check if we are in the attachment screen AND slider unchecked
                              if( $tckey == 'slider_check_key' && esc_attr( $_POST[$tcid] ) == 0) {

                                  //if we uncheck the attachement slider, looks for a previous entry and delete it.
                                  //Important : we check if the slider has slides first!
                                  if ( isset( $tc_options['tc_sliders'])) {
                                    foreach ( $tc_options['tc_sliders'] as $slider_name => $slider) {
                                      foreach ( $slider as $key => $tc_post) {
                                         //clean empty values if necessary
                                         if ( is_null( $tc_options['tc_sliders'][$slider_name][$key]))
                                            unset( $tc_options['tc_sliders'][$slider_name][$key]);
                                         //delete previous slider entries for this post
                                         if ( $tc_post == $post_ID )
                                            unset( $tc_options['tc_sliders'][$slider_name][$key]);
                                      }
                                    }
                                  }
                                  //update DB with clean option table
                                  update_option( 'tc_theme_options' , $tc_options );

                              }//endif;

                          break;
                        }//end switchendif;
                    }//end if ( isset( $_POST[$tcid])) {
                }//end foreach
                //attachments
                if( $tc_post_type == 'attachment' )
                  $this -> czr_fn_slide_save( $post_ID );

                do_action( "__after_ajax_save_slider_{$tc_post_type}", $_POST, $tc_slider_fields );
            }//function






  /*
  ----------------------------------------------------------------
  -------- AJAX CALL BACK FUNCTION (post and attachment) ---------
  ----------------------------------------------------------------
  */

  /**
   * Global slider ajax call back function : 1-Saves options and fields, 2-Renders
   * Used in post or attachment context => uses post_slider var to check the context
   * Works along with tc_ajax_slider.js
   * @package Customizr
   * @since Customizr 2.0
   */
     function czr_fn_slider_cb() {

      $nonce = $_POST['SliderCheckNonce'];
      // check if the submitted nonce matches with the generated nonce we created earlier
      if ( ! wp_verify_nonce( $nonce, 'tc-slider-check-nonce' ) ) {
        die();
      }

        Try{
        //get the post_id with the hidden input field
        $tc_post_id         = $_POST['tc_post_id'];

        //save $_POST var in DB
        $this -> czr_fn_slider_ajax_save( $tc_post_id);

        //check if we are in the post or attachment screen and select the appropriate rendering
        //we use the post_slider var defined in tc_ajax_slider.js
        if ( isset( $_POST['tc_post_type'])) {
          if( $_POST['tc_post_type'] == 'post' ) {
            $this -> czr_fn_get_post_slider_infos( $tc_post_id );
          }
          else {
            $this -> czr_fn_get_attachment_slider_infos( $tc_post_id );
          }
        }
        //echo $_POST['slider_id'];
       } catch (Exception $e){
          exit;
       }
       exit;
     }






      /**
       * Loads the necessary scripts and stylesheets to display slider options
       * @package Customizr
       * @since Customizr 1.0
       */
        function czr_fn_slider_admin_scripts( $hook) {
        global $post;

        $_min_version = ( defined('WP_DEBUG') && true === WP_DEBUG ) ? '' : '.min';
        //load scripts only for creating and editing slides options in pages and posts
        if( ( 'media.php'  == $hook)) {
            wp_enqueue_script( 'jquery-ui-sortable' );
        }
        if( ( 'post-new.php' == $hook || 'post.php' == $hook || 'media.php' == $hook) )  {
            //ajax refresh for slider options
            wp_enqueue_script( 'tc_ajax_slider' ,
                sprintf('%1$sinc/admin/js/tc_ajax_slider%2$s.js' , TC_BASE_URL, $_min_version ),
                array( 'jquery' ),
                true
            );

            // Tips to declare javascript variables http://www.garyc40.com/2010/03/5-tips-for-using-ajax-in-wordpress/#bad-ways
            wp_localize_script( 'tc_ajax_slider' , 'SliderAjax' , array(
            // URL to wp-admin/admin-ajax.php to process the request
            //'ajaxurl'          => admin_url( 'admin-ajax.php' ),
            // generate a nonce with a unique ID "myajax-post-comment-nonce"
            // so that you can check it later when an AJAX request is sent
            'SliderNonce' => wp_create_nonce( 'tc-slider-nonce' ),

            //
            'SliderCheckNonce' => wp_create_nonce( 'tc-slider-check-nonce' ),
            )
            );

            //iphone like button style and script
            wp_enqueue_style( 'iphonecheckcss' ,
                sprintf('%1$sinc/admin/css/iphonecheck%2$s.css' , TC_BASE_URL, $_min_version )
            );
            wp_enqueue_script( 'iphonecheck' ,

                sprintf('%1$sinc/admin/js/jqueryIphonecheck%2$s.js' , TC_BASE_URL, $_min_version ),
                array('jquery'),
                true
            );

            //thickbox
            wp_admin_css( 'thickbox' );
            add_thickbox();

            //sortable stuffs
            wp_enqueue_style( 'sortablecss' ,
                sprintf('%1$sinc/admin/css/tc_sortable%2$s.css' , TC_BASE_URL, $_min_version )
            );

            //wp built-in color picker style and script
           //Access the global $wp_version variable to see which version of WordPress is installed.
            global $wp_version;

            //If the WordPress version is greater than or equal to 3.5, then load the new WordPress color picker.
            if ( 3.5 <= $wp_version ){
                //Both the necessary css and javascript have been registered already by WordPress, so all we have to do is load them with their handle.
                wp_enqueue_style( 'wp-color-picker' );
                wp_enqueue_script( 'wp-color-picker' );
                 // load the minified version of custom script
                wp_enqueue_script( 'cp_demo-custom' ,
                    sprintf('%1$sinc/admin/js/color-picker%2$s.js' , TC_BASE_URL, $_min_version ),
                    array( 'jquery' , 'wp-color-picker' ),
                    true
                );
            }
            //If the WordPress version is less than 3.5 load the older farbtasic color picker.
            else {
                //As with wp-color-picker the necessary css and javascript have been registered already by WordPress, so all we have to do is load them with their handle.
                wp_enqueue_style( 'farbtastic' );
                wp_enqueue_script( 'farbtastic' );
                // load the minified version of custom script
                wp_enqueue_script( 'cp_demo-custom' ,
                    sprintf('%1$sinc/admin/js/color-picker%2$s.js' , TC_BASE_URL, $_min_version ),
                    array( 'jquery' , 'farbtastic' ),
                    true
                );
            }
        }//end post type hook check
      }




  /*
  ----------------------------------------------------------------
  ------------- ATTACHMENT FIELDS FILTER IF WP < 3.5 -------------
  ----------------------------------------------------------------
  */
      function czr_fn_attachment_filter( $form_fields, $post = null) {
          $this -> czr_fn_attachment_slider_box ( $post);
           return $form_fields;
      }


      function czr_fn_attachment_save_filter( $post, $attachment ) {
          if ( isset( $_POST['tc_post_id']))
           $postid = $_POST['tc_post_id'];

          $this -> czr_fn_slide_save( $postid );

          return $post;
      }
  }//end of class
endif;

?>
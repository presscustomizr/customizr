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
        add_filter( 'czr_update_notice', $callback );

      //PLACEHOLDERS AJAX SETUP HOOKS
      add_action( 'init'                 , array( $this, 'czr_fn_placeholders_ajax_setup') );
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

        czr_fn_set_thumb_info( $post_id );
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
      if ( 'tc_posts_slider' != czr_fn_get_opt( 'tc_front_slider' ) || ! apply_filters('czr_posts_slider_use_transient' , true ) )
        return;

      if ( wp_is_post_revision( $post_id ) || ( ! empty($post) && 'auto-draft' == $post->post_status ) )
        return;

      $slider_of_posts = $this -> czr_fn_maybe_get_slider_of_posts_instance();

      //Cache posts slider
      if ( $slider_of_posts )
        $slider_of_posts -> czr_fn_cache_posts_slider();
    }


    public function czr_fn_maybe_get_slider_of_posts_instance() {
      $slider_of_posts = null;
      $CZR             = CZR();
      /* Instantiate slider of posts */
       if ( ! class_exists( 'CZR_slider_of_posts_model_class' ) ) {
        $slider          = $CZR -> collection -> czr_fn_instantiate_model( array( 'id' => 'slider', 'model_class' => 'modules/slider/slider') );
        if ( ! $slider )
          return;
        $slider_of_posts = $CZR -> collection -> czr_fn_instantiate_model( array( 'id' => 'slider_of_posts', 'model_class' => 'modules/slider/slider_of_posts' ) );
      } else
        $slider_of_posts = $CZR -> collection -> czr_fn_get_model_instance( 'slider_of_posts' );

      return $slider_of_posts;
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
       $_option = czr_fn_get_opt( $option_name, $option_group, $use_default = false );
       if ( is_array( $_option ) && ! empty( $_option ) && in_array( $term, $_option ) )
         //update the option
         czr_fn_set_option( $option_name, array_diff( $_option, (array)$term ) );

       //alternative, cycle throughout the cats and keep just the existent ones
       /*if ( is_array( $blog_cats ) && ! empty( $blog_cats ) ) {
         //update the option
         czr_fn_set_option( 'tc_blog_restrict_by_cat', array_filter( $blog_cats, 'czr_fn_category_id_exists' ) );
       }*/
    }


    /*
    * @return css string
    *
    * @package Customizr
    * @since Customizr 3.2.10
    */
    function czr_fn_maybe_add_gfonts_to_editor() {
      $_font_pair         = esc_attr( czr_fn_get_opt('tc_fonts') );
      $_all_font_pairs    = CZR_init::$instance -> font_pairs;
      if ( false === strpos($_font_pair,'_g_') )
        return;
      //Commas in a URL need to be encoded before the string can be passed to add_editor_style.
      return array(
        str_replace(
          ',',
          '%2C',
          sprintf( '//fonts.googleapis.com/css?family=%s', czr_fn_get_font( 'single' , $_font_pair ) )
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
        sprintf('%1$sback/css/tc_admin%2$s.css' , CZR_BASE_URL . CZR_ASSETS_PREFIX, ( defined('WP_DEBUG') && true === WP_DEBUG ) ? '' : '.min' ),
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

      if( ! file_exists(CZR_BASE."readme.txt") ) {
        return;
      }
      if( ! is_readable(CZR_BASE."readme.txt") ) {
        echo '<p>The changelog in readme.txt is not readable.</p>';
        return;
      }

      $stylelines = explode("\n", implode('', file(CZR_BASE."readme.txt")));
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
          CZR_BASE_URL . CZR_ASSETS_PREFIX . 'back/css/editor-style.css',
          CZR_init::$instance -> czr_fn_get_style_src() , get_stylesheet_uri()
      );

      if ( apply_filters( 'czr_add_custom_fonts_to_editor' , false != $this -> czr_fn_maybe_add_gfonts_to_editor() ) )
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
      if ( ! apply_filters( 'czr_add_custom_fonts_to_editor' , true ) )
        return $init;

      $CZR = CZR();
      //some plugins fire tiny mce editor in the customizer
      //in this case, the CZR_resource class has to be loaded
      if ( ! class_exists('CZR_resources_styles') )
        $CZR -> czr_fn_load( array('fire' => array( array('inc' , 'resources_styles') ) ), true );
      if ( ! class_exists('CZR_resources_fonts') )
        $CZR -> czr_fn_load( array('fire' => array( array('inc' , 'resources_fonts') ) ), true );
      if ( ! class_exists('CZR_resources_scripts') )
        $CZR -> czr_fn_load( array('fire' => array( array('inc' , 'resources_scripts') ) ), true );

      //fonts
      $_css = CZR_resources::$instance -> czr_fn_write_fonts_inline_css( '', 'mce-content-body');
      //icons
      $_css .= CZR_resources::$instance -> czr_fn_get_inline_font_icons_css();
     ?>

        <script type="text/javascript">
          function add_user_defined_CSS( ed ) {
            //http://www.tinymce.com/wiki.php/Tutorial:Migration_guide_from_3.x
              ed.on('init', function() {
                  tinyMCE.activeEditor.dom.addStyle(<?php echo json_encode($_css) ?>);
              } );
          };
        </script>

        <?php
        if (wp_default_editor() == 'tinymce')
            $init['setup'] = 'add_user_defined_CSS';

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
      $opt_name                   = "customizr-pro" == CZR_THEMENAME ? 'last_update_notice_pro' : 'last_update_notice';
      $last_update_notice_values  = czr_fn_get_opt($opt_name);
      $show_new_notice = false;

      if ( ! $last_update_notice_values || ! is_array($last_update_notice_values) ) {
        //first time user of the theme, the option does not exist
        // 1) initialize it => set it to the current Customizr version, displayed 0 times.
        // 2) update in db
        $last_update_notice_values = array( "version" => CUSTOMIZR_VER, "display_count" => 0 );
        czr_fn_set_option( $opt_name, $last_update_notice_values );
        //already user of the theme ?
        if ( czr_fn_user_started_before_version( CUSTOMIZR_VER, CUSTOMIZR_VER ) )
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
          czr_fn_set_option( $opt_name, $last_update_notice_values );
        }
        //CASE 2 : displayed 5 times => automatic dismiss
        else {
          //reset option value with new version and counter to 0
          $new_val  = array( "version" => CUSTOMIZR_VER, "display_count" => 0 );
          czr_fn_set_option( $opt_name, $new_val );
        }//end else
      }//end if

      if ( ! $show_new_notice )
        return;

      ob_start();
        ?>
        <div class="updated" style="position:relative">
          <?php
            echo apply_filters(
              'czr_fn_update_notice',
              sprintf('<h3>%1$s %2$s %3$s %4$s :D</h3>',
                __( "Good, you've just upgraded to", "customizr"),
                "customizr-pro" == CZR_THEMENAME ? 'Customizr Pro' : 'Customizr',
                __( "version", "customizr"),
                CUSTOMIZR_VER
              )
            );
          ?>
          <?php
            echo apply_filters(
              'czr_fn_update_notice',
              sprintf( '<h4>%1$s</h4><strong><a class="button button-primary" href="%2$s" title="%3$s" target="_blank">%3$s &raquo;</a> <a class="button button-primary" href="%4$s" title="%5$s" target="_blank">%5$s &raquo;</a></strong>',
                __( "We'd like to introduce the new features we've been working on.", "customizr"),
                CZR_WEBSITE . "category/customizr-releases/",
                __( "Read the latest release notes" , "customizr" ),
                esc_url('demo.presscustomizr.com'),
                __( "Visit the demo", "customizr" )
              )
            );
          ?>
          <p style="text-align:right;position: absolute;right: 7px;bottom: -5px;">
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
      $opt_name = "customizr-pro" == CZR_THEMENAME ? 'last_update_notice_pro' : 'last_update_notice';
      //reset option value with new version and counter to 0
      $new_val  = array( "version" => CUSTOMIZR_VER, "display_count" => 0 );
      czr_fn_set_option( $opt_name, $new_val );
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


    /*****************************************************
    * ADMIN AJAX HOOKS ALL PLACEHOLDERS
    *****************************************************/
    /**
    * hook : init => because we need to fire this function before the admin_ajax.php call
    * @since v3.4+
    */
    function czr_fn_placeholders_ajax_setup() {
      if ( czr_fn_get_opt('tc_display_front_help') )
        add_action( 'wp_ajax_czr_notice_actions'         , array( $this, 'czr_fn_notice_ajax_actions' ) );
    }


    /*****************************************************
    * PLACEHOLDERS : AJAX JS AND CALLBACK
    *****************************************************/
    /**
    * Two cases :
    * 1) remove block/disable option
    * 2) dismiss notice
    * hook : wp_ajax_tc_notice_actions
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    function czr_fn_notice_ajax_actions() {
      if ( isset( $_POST['remove_action'] ) )
        $_remove_action = esc_attr( $_POST['remove_action'] );
      else
        wp_die(0);
      check_ajax_referer( 'czr-notice-nonce', 'CZRNoticeNonce' );
      switch ($_remove_action) {
        case 'remove_block':
          if ( isset( $_POST[ 'user_option' ] ) )
            czr_fn_set_option( esc_attr( $_POST['user_option'] ) , 0 );
        break;
        case 'remove_notice':
          if ( isset( $_POST[ 'notice_id' ] ) )
            set_transient( 'tc_' . esc_attr( $_POST['notice_id'] ), 'disabled' , 60*60*24*365*20 );//20 years of peace
        break;
      }
      wp_die();
    }

  }//end of class
endif;

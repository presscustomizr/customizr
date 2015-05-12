<?php
/**
* Handles various plugins compatibilty (Jetpack, Bbpress, Qtranslate, Woocommerce, The Event Calendar ...)
*
* @package      Customizr
* @subpackage   classes
* @since        3.3+
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_plugins_compat' ) ) :
  class TC_plugins_compat {
    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;
    function __construct () {
      self::$instance =& $this;
      //add various plugins compatibilty (Jetpack, Bbpress, Qtranslate, Woocommerce, The Event Calendar ...)
      add_action ('after_setup_theme'                      , array( $this , 'tc_set_plugins_supported'), 20 );
      add_action ('after_setup_theme'                      , array( $this , 'tc_plugins_compatibility'), 30 );
    }//end of constructor



    /**
    * Set plugins supported ( before the plugin compat function is fired )
    * => allows to easily remove support by firing remove_theme_support() (with a priority < tc_plugins_compatibility) on hook 'after_setup_theme'
    * hook : after_setup_theme
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    function tc_set_plugins_supported() {
      //add support for plugins (added in v3.1+)
      add_theme_support( 'jetpack' );
      add_theme_support( 'bbpress' );
      add_theme_support( 'buddy-press' );
      add_theme_support( 'qtranslate-x' );
      add_theme_support( 'polylang' );
      add_theme_support( 'woocommerce' );
      add_theme_support( 'the-events-calendar' );
      add_theme_support( 'nextgen-gallery' );
      add_theme_support( 'optimize-press' );
      add_theme_support( 'sensei' );
    }



    /**
    * This function handles the following plugins compatibility : Jetpack (for the carousel addon), Bbpress, Qtranslate, Woocommerce
    *
    * @package Customizr
    * @since Customizr 3.0.15
    */
    function tc_plugins_compatibility() {
      /* JETPACK */
      //adds compatibilty with the jetpack image carousel
      if ( current_theme_supports( 'jetpack' ) && $this -> tc_is_plugin_active('jetpack/jetpack.php') )
        add_filter( 'tc_gallery_bool', '__return_false' );

      /* BBPRESS */
      //if bbpress is installed and activated, we can check the existence of the contextual boolean function is_bbpress() to execute some code
      if ( current_theme_supports( 'bbpress' ) && $this -> tc_is_plugin_active('bbpress/bbpress.php') )
        $this -> tc_set_bbpress_compat();

      /* BUDDYPRESS */
      //if buddypress is installed and activated, we can check the existence of the contextual boolean function is_buddypress() to execute some code
      // we have to use buddy-press instead of buddypress as string for theme support as buddypress makes some checks on current_theme_supports('buddypress') which result in not using its templates
      if ( current_theme_supports( 'buddy-press' ) && $this -> tc_is_plugin_active('buddypress/bp-loader.php') )
        $this -> tc_set_buddypress_compat();

      /*
      * QTranslatex
      * Credits : @acub, http://websiter.ro
      */
      if ( current_theme_supports( 'qtranslate-x' ) && $this -> tc_is_plugin_active('qtranslate-x/qtranslate.php') )
        $this -> tc_set_qtranslatex_compat();

      /*
      * Polylang
      * Credits : Rocco Aliberti
      */
      if ( current_theme_supports( 'polylang' ) && $this -> tc_is_plugin_active('polylang/polylang.php') )
        $this -> tc_set_polylang_compat();

      /* Optimize Press */
      if ( current_theme_supports( 'optimize-press' ) && $this -> tc_is_plugin_active('optimizePressPlugin/optimizepress.php') )
        $this -> tc_set_optimizepress_compat();

      /* Woocommerce */
      if ( current_theme_supports( 'woocommerce' ) && $this -> tc_is_plugin_active('woocommerce/woocommerce.php') )
        $this -> tc_set_woocomerce_compat();

      /* Nextgen gallery */
      if ( current_theme_supports( 'nextgen-gallery') && $this -> tc_is_plugin_active('nextgen-gallery/nggallery.php') )
        $this -> tc_set_nggallery_compat();

      /* Sensei woocommerce addon */
      if ( current_theme_supports( 'sensei') && $this -> tc_is_plugin_active('woothemes-sensei/woothemes-sensei.php') )
        $this -> tc_set_sensei_compat();
    }//end of plugin compatibility function



    /**
    * BBPress compat hooks
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    private function tc_set_bbpress_compat() {
      // hide tax archive title
      add_filter( 'tc_show_tax_archive_title', 'tc_bbpress_disable_tax_archive_title');
      function tc_bbpress_disable_tax_archive_title( $bool ){
        return ( function_exists('is_bbpress') && is_bbpress() ) ? false : $bool;
      }

      //disables thumbnails and excerpt for post lists
      add_filter( 'tc_show_post_list_thumb', 'tc_bbpress_disable_thumbnail' );
      function tc_bbpress_disable_thumbnail($bool) {
         return ( function_exists('is_bbpress') && is_bbpress() ) ? false : $bool;
      }
      add_filter( 'tc_show_excerpt', 'tc_bbpress_disable_excerpt' );
      function tc_bbpress_disable_excerpt($bool) {
         return ( function_exists('is_bbpress') && is_bbpress() ) ? false : $bool;
      }

      //disables Customizr author infos on forums
      add_filter( 'tc_show_author_metas_in_post', 'tc_bbpress_disable_author_meta' );
      function tc_bbpress_disable_author_meta($bool) {
        return ( function_exists('is_bbpress') && is_bbpress() ) ? false : $bool;
      }

      //disables post navigation
      add_filter( 'tc_show_post_navigation', 'tc_bbpress_disable_post_navigation' );
      function tc_bbpress_disable_post_navigation($bool) {
         return ( function_exists('is_bbpress') && is_bbpress() ) ? false : $bool;
      }

      //disables post metas
      add_filter( 'tc_show_post_metas', 'tc_bbpress_disable_post_metas', 100);
      function tc_bbpress_disable_post_metas($bool) {
         return ( function_exists('is_bbpress') && is_bbpress() ) ? false : $bool;
      }

      //disable the grid
      add_filter( 'tc_set_grid_hooks' , 'tc_bbpress_disable_grid', 100 );
      function tc_bbpress_disable_grid($bool) {
         return ( function_exists('is_bbpress') && is_bbpress() ) ? false : $bool;
      }
    }

    /**
    * BuddyPress compat hooks
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    private function tc_set_buddypress_compat() {
      add_filter( 'tc_are_comments_enabled', 'tc_buddypress_disable_comments' );
      function tc_buddypress_disable_comments($bool){
        return ( is_page() && function_exists('is_buddypress') && is_buddypress() ) ? false : $bool;
      }
    }

    /**
    * QtranslateX compat hooks
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    private function tc_set_qtranslatex_compat() {
      function tc_url_lang($url) {
        return ( function_exists( 'qtrans_convertURL' ) ) ? qtrans_convertURL($url) : $url;
      }
      function tc_apply_qtranslate ($text) {
        return call_user_func(  '__' , $text );
      }
      function tc_remove_char_limit() {
        return 99999;
      }
      function tc_change_transport( $value , $set ) {
        return ('transport' == $set) ? 'refresh' : $value;
      }
      //outputs correct urls for current language : in logo, slider
      add_filter( 'tc_slide_link_url' , 'tc_url_lang' );
      add_filter( 'tc_logo_link_url' , 'tc_url_lang');
      //outputs the qtranslate translation for slider
      add_filter( 'tc_slide_title', 'tc_apply_qtranslate' );
      add_filter( 'tc_slide_text', 'tc_apply_qtranslate' );
      add_filter( 'tc_slide_button_text', 'tc_apply_qtranslate' );
      add_filter( 'tc_slide_background_alt', 'tc_apply_qtranslate' );

      //outputs the qtranslate translation for archive titles;
      $tc_archive_titles = array( 'tag_archive', 'category_archive', 'author_archive', 'search_results');
      foreach ( $tc_archive_titles as $title )
        add_filter("tc_{$title}_title", 'tc_apply_qtranslate' , 20);
      //sets no character limit for slider (title, lead text and button title) => allow users to use qtranslate tags for as many languages they wants ([:en]English text[:de]German text...and so on)
      add_filter( 'tc_slide_title_length'  , 'tc_remove_char_limit');
      add_filter( 'tc_slide_text_length'   , 'tc_remove_char_limit');
      add_filter( 'tc_slide_button_length' , 'tc_remove_char_limit');
      // QtranslateX for FP when no FPC or FPU running
      if ( ! class_exists('TC_fpu') && ! class_exists('TC_fpc') ) {
        //outputs correct urls for current language : fp
        add_filter( 'tc_fp_link_url' , 'tc_url_lang');
        //outputs the qtranslate translation for featured pages
        add_filter( 'tc_fp_text', 'tc_apply_qtranslate' );
        add_filter( 'tc_fp_button_text', 'tc_apply_qtranslate' );

        //sets no character limit for featured pages (text) => allow users to use qtranslate tags for as many languages they wants ([:en]English text[:de]German text...and so on)
        add_filter( 'tc_fp_text_length' , 'tc_remove_char_limit');
        //modify the page excerpt=> uses the wp page excerpt instead of the generated excerpt with the_content
        add_filter( 'tc_fp_text', 'tc_use_page_excerpt', 20, 3 );
        function tc_use_page_excerpt( $featured_text , $fp_id , $page_id ) {
          $page = get_post($page_id);
          return ( empty($featured_text) && !post_password_required($page_id) ) ? strip_tags(apply_filters( 'the_content' , $page->post_excerpt )) : $featured_text ;
        }
        /* The following is pretty useless at the momment since we should inhibit preview js code */
        //modify the customizer transport from post message to null for some options
        add_filter( 'tc_featured_page_button_text_customizer_set' , 'tc_change_transport', 20, 2);
        add_filter( 'tc_featured_text_one_customizer_set' , 'tc_change_transport', 20, 2);
        add_filter( 'tc_featured_text_two_customizer_set' , 'tc_change_transport', 20, 2);
        add_filter( 'tc_featured_text_three_customizer_set' , 'tc_change_transport', 20, 2);
      }
    }


    /**
    * Polylang compat hooks
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    private function tc_set_polylang_compat() {
      // If Polylang is active, hook function on the admin pages
      if ( function_exists( 'pll_register_string' ) )
        add_action( 'admin_init', 'tc_pll_strings_setup' );
      function tc_pll_strings_setup() {

        // grab theme options
        $pll_tc_options = tc__f('__options');
        // grab settings map, useful for some options labels
        $tc_settings_map = TC_utils_settings_map::$instance -> tc_customizer_map( $get_default = true );
        $tc_controls_map = $tc_settings_map['add_setting_control'];
        // set $polylang_group;
        $polylang_group = 'customizr-pro' == TC___::$theme_name ? 'Customizr-Pro' : 'Customizr';

        // Add front page slider name to Polylang's string translation panel
        if ( isset( $pll_tc_options['tc_front_slider'] ) )
          pll_register_string( 'Front page slider name', esc_attr($pll_tc_options['tc_front_slider']), $polylang_group );
        // Add archive title strings to Polylang's string translation panel
        $archive_titles_settings =  array( 'tc_tag_title', 'tc_cat_title', 'tc_author_title', 'tc_search_title');
        foreach ( $archive_titles_settings as $archive_title_setting_name )
          if ( isset( $pll_tc_options[$archive_title_setting_name] ) )
            pll_register_string( $tc_controls_map["tc_theme_options[$archive_title_setting_name]"]["label"], esc_attr($pll_tc_options[$archive_title_setting_name]), $polylang_group );
        // Featured Pages
        if ( ! class_exists('TC_fpu') && ! class_exists('TC_fpc') ) {
          $pll_tc_fp_areas = TC_init::$instance -> fp_ids;
          // Add featured pages button text to Polylang's string translation panel
          if ( isset( $pll_tc_options[ 'tc_featured_page_button_text'] ) )
            pll_register_string( $tc_controls_map["tc_theme_options[tc_featured_page_button_text]"]["label"], esc_attr($pll_tc_options[ 'tc_featured_page_button_text']), $polylang_group );

          // Add featured pages excerpt text to Polylang's string translation panel
          foreach ( $pll_tc_fp_areas as $area )
            if ( isset( $pll_tc_options["tc_featured_text_$area"] ) )
              pll_register_string( $tc_controls_map["tc_theme_options[tc_featured_text_$area]"]["label"], esc_attr($pll_tc_options['tc_featured_text_'.$area]), $polylang_group );

        } //end Featured Pages
      }// end tc_pll_strings_setup function

      // Front
      // If Polylang is active, translate/swap featured page buttons/text/link and slider
      if ( function_exists( 'pll_get_post' ) && function_exists( 'pll__' ) && ! is_admin() ) {
        // Substitute any registered slider name
        add_filter( 'tc_slider_name_id', 'pll__' );
        // Substitue archive titles
        $pll_tc_archive_titles = array( 'tag_archive', 'category_archive', 'author_archive', 'search_results');

        foreach ( $pll_tc_archive_titles as $title )
          add_filter("tc_{$title}_title", 'pll__' , 20);
        // Featured Pages
        if ( ! class_exists('TC_fpu') && ! class_exists('TC_fpc') ) {
          // Substitute any page id with the equivalent page in current language (if found)
          add_filter( 'tc_fp_id', 'pll_tc_page_id' );
          function pll_tc_page_id( $fp_page_id ) {
            return is_int( pll_get_post( $fp_page_id ) ) ? pll_get_post( $fp_page_id ) : $fp_page_id;
          }

          // Substitute the featured page button text with the current language button text
          add_filter( 'tc_fp_button_text', 'pll__' );

          // Substitute the featured page text with the translated featured page text
          add_filter( 'tc_fp_text', 'pll__' );

        }
      }//end Front
    }//end polylang compat


    /**
    * NextGen Gallery compat hooks
    * @package Customizr
    * @since Customizr 3.3+
    */
    private function tc_set_nggallery_compat() {
      /* Make Customizr smart load work with nextgen galleries and fix small bug which resulted in displaying plain image attributes */
     add_action('wp_head', 'tc_content_parse_imgs_rehook');
     function tc_content_parse_imgs_rehook(){
       // smartload doesn't work at all for nggalleries in pages, looks like they add "data-src" to their images in pages .. mah
       if ( is_page() || is_admin() || 0 == esc_attr( TC_utils::$inst->tc_opt( 'tc_img_smart_load' ) ) )
        return;

       remove_filter('the_content', array(TC_utils::$instance, 'tc_parse_imgs') );
       // they add the actual images filtering the content with priority PHP_INT_MAX -1
       add_filter('the_content'   , array(TC_utils::$instance, 'tc_parse_imgs'), PHP_INT_MAX );
     }
    }


    /**
    * OptimizePress compat hooks
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    private function tc_set_optimizepress_compat() {
      add_action('wp_print_scripts', 'tc_op_dequeue_fancybox_js');
      function tc_op_dequeue_fancybox_js(){
        if ( function_exists('is_le_page') ){
          /* Op Back End: Dequeue tc-scripts */
          if ( is_le_page() || defined('OP_LIVEEDITOR') ) {
            wp_dequeue_script('tc-scripts');
            wp_dequeue_script('tc-fancybox');
          }
          else {
            /* Front End: Dequeue Fancybox maybe already embedded in Customizr */
            wp_dequeue_script('tc-fancybox');
            //wp_dequeue_script(OP_SN.'-fancybox');
          }
        }
      }

      /* Remove fancybox loading icon*/
      add_action('wp_footer','tc_op_remove_fancyboxloading');
      function tc_op_remove_fancyboxloading(){
        echo "<script>
                if (typeof(opjq) !== 'undefined') {
                  opjq(document).ready(function(){
                    opjq('#fancybox-loading').remove();
                  });
                }
             </script>";
      }
    }//end optimizepress compat



    /**
    * Sensei compat hooks
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    private function tc_set_sensei_compat() {
      //unkooks the default sensei wrappers and add customizr's content wrapper and action hooks
      global $woothemes_sensei;
      remove_action( 'sensei_before_main_content', array( $woothemes_sensei->frontend, 'sensei_output_content_wrapper' ), 10 );
      remove_action( 'sensei_after_main_content', array( $woothemes_sensei->frontend, 'sensei_output_content_wrapper_end' ), 10 );

      add_action('sensei_before_main_content', 'tc_sensei_wrappers', 10);
      add_action('sensei_after_main_content', 'tc_sensei_wrappers', 10);


      function tc_sensei_wrappers() {
        switch ( current_filter() ) {
          case 'sensei_before_main_content': TC_plugins_compat::$instance -> tc_mainwrapper_start();
                                             break;

          case 'sensei_after_main_content' : TC_plugins_compat::$instance -> tc_mainwrapper_end();
                                             break;
        }//end of switch on hook
      }//end of nested function

      // hide tax archive title
      add_filter( 'tc_show_tax_archive_title', 'tc_sensei_disable_tax_archive_title');
      function tc_sensei_disable_tax_archive_title( $bool ){
        return ( function_exists('is_sensei') && is_sensei() ) ? false : $bool;
      }

      //disables post navigation
      add_filter( 'tc_show_post_navigation', 'tc_sensei_disable_post_navigation' );
      function tc_sensei_disable_post_navigation($bool) {
        return ( function_exists('is_sensei') && is_sensei() ) ? false : $bool;
      }
      //removes post comment action on after_loop hook
      add_filter( 'tc_are_comments_enabled', 'tc_sensei_disable_comments' );
      function tc_sensei_disable_comments($bool) {
        return ( function_exists('is_sensei') && is_sensei() ) ? false : $bool;
      }
    }//end sensei compat




    /**
    * Woocommerce compat hooks
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    private function tc_set_woocomerce_compat() {
      //unkooks the default woocommerce wrappersv and add customizr's content wrapper and action hooks
      remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
      remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
      add_action('woocommerce_before_main_content', 'tc_woocommerce_wrappers', 10);
      add_action('woocommerce_after_main_content', 'tc_woocommerce_wrappers', 10);

      //disable WooCommerce default breadcrumb
      if ( apply_filters( 'tc_disable_woocommerce_breadcrumb', true ) )
        remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );

      function tc_woocommerce_wrappers() {
        switch ( current_filter() ) {
          case 'woocommerce_before_main_content': TC_plugins_compat::$instance -> tc_mainwrapper_start();
                                                  break;

          case 'woocommerce_after_main_content' : TC_plugins_compat::$instance -> tc_mainwrapper_end();
                                                  break;
        }//end of switch on hook
      }//end of nested function

      // use Customizr title
      // initially used to display the edit button
      add_filter( 'the_title', 'tc_woocommerce_the_title' );
      function tc_woocommerce_the_title( $_title ){
        if ( function_exists('is_woocommerce') && is_woocommerce() && ! is_page() )
            return apply_filters( 'tc_title_text', $_title );
        return $_title;
      }

      // hide tax archive title
      add_filter( 'tc_show_tax_archive_title', 'tc_woocommerce_disable_tax_archive_title');
      function tc_woocommerce_disable_tax_archive_title( $bool ){
        return ( function_exists('is_woocommerce') && is_woocommerce() ) ? false : $bool;
      }

      //allow slider in the woocommerce shop page
      add_filter('tc_show_slider', 'tc_woocommerce_enable_shop_slider');
      function tc_woocommerce_enable_shop_slider( $bool ){
        return ( function_exists('is_woocommerce') && is_woocommerce() && function_exists('is_shop') && is_shop() ) ? true : $bool;
      }
      //to allow the slider in the woocommerce shop page we need the shop page id
      add_filter('tc_slider_get_real_id', 'tc_woocommerce_shop_page_id');
      function tc_woocommerce_shop_page_id( $id ){
        return ( function_exists('is_woocommerce') && is_woocommerce() && function_exists('is_shop') && is_shop() && function_exists('wc_get_page_id') ) ? wc_get_page_id('shop') : $id;
      }

      //handles the woocomerce sidebar : removes action if sidebars not active
      if ( !is_active_sidebar( 'shop') ) {
        remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
      }


      //disables post navigation
      add_filter( 'tc_show_post_navigation', 'tc_woocommerce_disable_post_navigation' );
      function tc_woocommerce_disable_post_navigation($bool) {
         return ( function_exists('is_woocommerce') && is_woocommerce() ) ? false : $bool;
      }


      //removes post comment action on after_loop hook
      add_filter( 'tc_are_comments_enabled', 'tc_woocommerce_disable_comments' );
      function tc_woocommerce_disable_comments($bool) {
         return ( function_exists('is_woocommerce') && is_woocommerce() ) ? false : $bool;
      }

      //changes customizr meta boxes priority (slider and layout not on top) if displaying woocommerce products in admin
      add_filter( 'tc_post_meta_boxes_priority', 'tc_woocommerce_change_meta_boxes_priority' , 2 , 10 );
      function tc_woocommerce_change_meta_boxes_priority($priority , $screen) {
         return ( 'product' == $screen ) ? 'default' : $priority ;
      }
    }

    /**
    * CUSTOMIZR WRAPPERS
    * print the customizr wrappers
    *
    * @since 3.3+
    *
    * originally used for woocommerce compatibility
    */
    function tc_mainwrapper_start() {
      ?>
      <div id="main-wrapper" class="<?php echo implode(' ', apply_filters( 'tc_main_wrapper_classes' , array('container') ) ) ?>">

        <?php do_action( '__before_main_container' ); ##hook of the featured page (priority 10) and breadcrumb (priority 20)...and whatever you need! ?>

        <div class="container" role="main">
          <div class="<?php echo implode(' ', apply_filters( 'tc_column_content_wrapper_classes' , array('row' ,'column-content-wrapper') ) ) ?>">

            <?php do_action( '__before_article_container'); ##hook of left sidebar?>

              <div id="content" class="<?php echo implode(' ', apply_filters( 'tc_article_container_class' , array( TC_utils::tc_get_layout( TC_utils::tc_id() , 'class' ) , 'article-container' ) ) ) ?>">

                <?php do_action ('__before_loop');##hooks the header of the list of post : archive, search... ?>
      <?php
    }

    function tc_mainwrapper_end() {
      ?>
                <?php do_action ('__after_loop');##hook of the comments and the posts navigation with priorities 10 and 20 ?>

              </div><!--.article-container -->

              <?php do_action( '__after_article_container'); ##hook of left sidebar?>

            </div><!--.row -->
        </div><!-- .container role: main -->

        <?php do_action( '__after_main_container' ); ?>

      </div><!--#main-wrapper"-->
      <?php
    }



    /**
    * HELPER
    * Check whether the plugin is active by checking the active_plugins list.
    * copy of is_plugin_active declared in wp-admin/includes/plugin.php
    *
    * @since 3.3+
    *
    * @param string $plugin Base plugin path from plugins directory.
    * @return bool True, if in the active plugins list. False, not in the list.
    */
    function tc_is_plugin_active( $plugin ) {
      return in_array( $plugin, (array) get_option( 'active_plugins', array() ) ) || $this -> tc_is_plugin_active_for_network( $plugin );
    }


    /**
    * HELPER
    * Check whether the plugin is active for the entire network.
    * copy of is_plugin_active_for_network declared in wp-admin/includes/plugin.php
    *
    * @since 3.3+
    *
    * @param string $plugin Base plugin path from plugins directory.
    * @return bool True, if active for the network, otherwise false.
    */
    function tc_is_plugin_active_for_network( $plugin ) {
      if ( ! is_multisite() )
        return false;

      $plugins = get_site_option( 'active_sitewide_plugins');
      if ( isset($plugins[$plugin]) )
        return true;

      return false;
    }

  }//end of class
endif;

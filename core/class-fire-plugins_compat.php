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
if ( ! class_exists( 'CZR_plugins_compat' ) ) :
  class CZR_plugins_compat {
    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;
    //credits @Srdjan
    public $default_language, $current_language;

    function __construct () {

      self::$instance =& $this;
      //add various plugins compatibilty (Jetpack, Bbpress, Qtranslate, Woocommerce, The Event Calendar ...)
      add_action ('after_setup_theme'          , array( $this , 'czr_fn_set_plugins_supported'), 20 );
      add_action ('after_setup_theme'          , array( $this , 'czr_fn_plugins_compatibility'), 30 );
      // remove qtranslateX theme options filter
      remove_filter('option_tc_theme_options', 'qtranxf_translate_option', 5);
    }//end of constructor



    /**
    * Set plugins supported ( before the plugin compat function is fired )
    * => allows to easily remove support by firing remove_theme_support() (with a priority < czr_fn_plugins_compatibility) on hook 'after_setup_theme'
    * hook : after_setup_theme
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    function czr_fn_set_plugins_supported() {
      //add support for plugins (added in v3.1+)
      add_theme_support( 'jetpack' );
      add_theme_support( 'bbpress' );
      add_theme_support( 'buddy-press' );
      add_theme_support( 'qtranslate-x' );
      add_theme_support( 'polylang' );
      add_theme_support( 'wpml' );
      add_theme_support( 'woocommerce' );
      add_theme_support( 'the-events-calendar' );
      add_theme_support( 'optimize-press' );
      add_theme_support( 'sensei' );
      add_theme_support( 'visual-composer' );//or js-composer as they call it
      add_theme_support( 'disqus' );
      add_theme_support( 'uris' );
    }



    /**
    * This function handles the following plugins compatibility : Jetpack (for the carousel addon and photon), Bbpress, Qtranslate, Woocommerce
    *
    * @package Customizr
    * @since Customizr 3.0.15
    */
    function czr_fn_plugins_compatibility() {
      /* JETPACK */
      //adds compatibilty with the jetpack image carousel and photon
      if ( current_theme_supports( 'jetpack' ) && $this -> czr_fn_is_plugin_active('jetpack/jetpack.php') )
        $this -> czr_fn_set_jetpack_compat();

      /* BBPRESS */
      //if bbpress is installed and activated, we can check the existence of the contextual boolean function is_bbpress() to execute some code
      if ( current_theme_supports( 'bbpress' ) && $this -> czr_fn_is_plugin_active('bbpress/bbpress.php') )
        $this -> czr_fn_set_bbpress_compat();

      /* BUDDYPRESS */
      //if buddypress is installed and activated, we can check the existence of the contextual boolean function is_buddypress() to execute some code
      // we have to use buddy-press instead of buddypress as string for theme support as buddypress makes some checks on current_theme_supports('buddypress') which result in not using its templates
      if ( current_theme_supports( 'buddy-press' ) && $this -> czr_fn_is_plugin_active('buddypress/bp-loader.php') )
        $this -> czr_fn_set_buddypress_compat();

      /*
      * QTranslatex
      * Credits : @acub, http://websiter.ro
      */
      if ( current_theme_supports( 'qtranslate-x' ) && $this -> czr_fn_is_plugin_active('qtranslate-x/qtranslate.php') )
        $this -> czr_fn_set_qtranslatex_compat();

      /*
      * Polylang
      * Credits : Rocco Aliberti
      */
      if ( current_theme_supports( 'polylang' ) && $this -> czr_fn_is_plugin_active('polylang/polylang.php') )
        $this -> czr_fn_set_polylang_compat();

      /*
      * WPML
      */
      if ( current_theme_supports( 'wpml' ) && $this -> czr_fn_is_plugin_active('sitepress-multilingual-cms/sitepress.php') )
        $this -> czr_fn_set_wpml_compat();

      /* The Events Calendar */
      if ( current_theme_supports( 'the-events-calendar' ) && $this -> czr_fn_is_plugin_active('the-events-calendar/the-events-calendar.php') )
        $this -> czr_fn_set_the_events_calendar_compat();

      /* Optimize Press */
      if ( current_theme_supports( 'optimize-press' ) && $this -> czr_fn_is_plugin_active('optimizePressPlugin/optimizepress.php') )
        $this -> czr_fn_set_optimizepress_compat();

      /* Woocommerce */
      if ( current_theme_supports( 'woocommerce' ) && $this -> czr_fn_is_plugin_active('woocommerce/woocommerce.php') )
        $this -> czr_fn_set_woocomerce_compat();

      /* Sensei woocommerce addon */
      if ( current_theme_supports( 'sensei') && $this -> czr_fn_is_plugin_active('woothemes-sensei/woothemes-sensei.php') )
        $this -> czr_fn_set_sensei_compat();

      /* Visual Composer */
      if ( current_theme_supports( 'visual-composer') && $this -> czr_fn_is_plugin_active('js_composer/js_composer.php') )
        $this -> czr_fn_set_vc_compat();

      /* Disqus Comment System */
      if ( current_theme_supports( 'disqus') && $this -> czr_fn_is_plugin_active('disqus-comment-system/disqus.php') )
        $this -> czr_fn_set_disqus_compat();

      /* Ultimate Responsive Image Slider  */
      if ( current_theme_supports( 'uris' ) && $this -> czr_fn_is_plugin_active('ultimate-responsive-image-slider/ultimate-responsive-image-slider.php') )
        $this -> czr_fn_set_uris_compat();
    }//end of plugin compatibility function



    /**
    * Jetpack compat hooks
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    private function czr_fn_set_jetpack_compat() {
      //jetpack image carousel
      //this filter doesn't exist anymore it has been replaced by
      //tc_is_gallery_enabled
      //I think we can remove the following compatibility as everything seems to work (considering that it doesn't do anything atm)
      //and we haven't received any complain
      //Also we now have a whole gallery section of settings and we coul redirect users there to fine tune it
      add_filter( 'czr_gallery_bool', '__return_false' );

      //Photon jetpack's module conflicts with our smartload feature:
      //Photon removes the width,height attribute in php, then in js it compute them (when they have the special attribute 'data-recalc-dims')
      //based on the img src. When smartload is enabled the images parsed by its js which are not already smartloaded are dummy
      //and their width=height is 1. The image is correctly loaded but the space
      //assigned to it will be 1x1px. Photon js, is compatible with Auttomatic plugin lazy load and it sets the width/height
      //attribute only when the img is smartloaded. This is pretty useless to me, as it doesn't solve the main issue:
      //document's height change when the img are smartloaded.
      //Anyway to avoid the 1x1 issue we alter the img attribute (data-recalc-dims) which photon adds to the img tag(php) so
      //the width/height will not be erronously recalculated
      if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'photon' ) )
        add_filter( 'czr_img_smartloaded', 'czr_fn_jp_smartload_img');
      function czr_fn_jp_smartload_img( $img ) {
        return str_replace( 'data-recalc-dims', 'data-tcjp-recalc-dims', $img );
      }
    }//end jetpack compat




    /**
    * BBPress compat hooks
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    private function czr_fn_set_bbpress_compat() {
      //disables post navigation
      add_filter( 'czr_show_post_navigation', 'czr_fn_bbpress_disable_post_navigation' );
      function czr_fn_bbpress_disable_post_navigation($bool) {
         return ( function_exists('is_bbpress') && is_bbpress() ) ? false : $bool;
      }

      //disables post metas
      add_filter( 'czr_show_post_metas', 'czr_fn_bbpress_disable_post_metas', 100);
      function czr_fn_bbpress_disable_post_metas($bool) {
         return ( function_exists('is_bbpress') && is_bbpress() ) ? false : $bool;
      }

      //disables comments
      add_filter( 'czr_are_comments_enabled', 'czr_fn_bbpress_disable_comments', 100);
      function czr_fn_bbpress_disable_comments($bool) {
         return ( function_exists('is_bbpress') && is_bbpress() ) ? false : $bool;
      }

      //disable title icons
      add_filter( 'czr_opt_tc_show_title_icon', 'czr_fn_bbpress_disable_title_icon' );
      function czr_fn_bbpress_disable_title_icon($bool) {
         return ( function_exists('is_bbpress') && is_bbpress() ) ? false : $bool;
      }

    }

    /**
    * BuddyPress compat hooks
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    private function czr_fn_set_buddypress_compat() {
      //disable title icons
      add_filter( 'czr_opt_tc_show_title_icon', 'czr_fn_buddypress_disable_title_icon' );
      function czr_fn_buddypress_disable_title_icon($bool) {
         return ( function_exists('is_buddypress') && is_buddypress() ) ? false : $bool;
      }
      add_filter( 'czr_are_comments_enabled', 'czr_fn_buddypress_disable_comments' );
      function czr_fn_buddypress_disable_comments($bool){
        return ( is_page() && function_exists('is_buddypress') && is_buddypress() ) ? false : $bool;
      }
      //disable smartload in change-avatar buddypress profile page
      //to avoid the img tag (in a template loaded with backbone) being parsed on server side but
      //not correctly processed by the front js.
      //the action hook "xprofile_screen_change_avatar" is a buddypress specific hook
      //fired before wp_head where we hook czr_fn_parse_imgs
      //side-effect: all the images in this pages will not be smartloaded, this isn't a big deal
      //as there should be at maximum 2 images there:
      //1) the avatar, if already set
      //2) a cover image, if already set
      //anyways this page is not a regular "front" page as it pertains more to a "backend" side
      //if we can call it that way.
      add_action( 'xprofile_screen_change_avatar', 'czr_fn_buddypress_maybe_disable_img_smartload' );
      function czr_fn_buddypress_maybe_disable_img_smartload() {
        add_filter( 'czr_opt_tc_img_smart_load', '__return_false' );
      }

    }

    /**
    * QtranslateX compat hooks
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    private function czr_fn_set_qtranslatex_compat() {
      function czr_fn_url_lang($url) {
        return ( function_exists( 'qtrans_convertURL' ) ) ? qtrans_convertURL($url) : $url;
      }
      function czr_fn_apply_qtranslate ($text) {
        return call_user_func(  '__' , $text );
      }
      function czr_fn_remove_char_limit() {
        return 99999;
      }
      function czr_fn_change_transport( $value , $set ) {
        return ('transport' == $set) ? 'refresh' : $value;
      }

      //outputs correct urls for current language : in logo, slider
      foreach ( array( 'czr_slide_link_url', 'czr_logo_link_url') as $filter )
        add_filter( $filter, 'czr_fn_url_lang' );

      //outputs the qtranslate translation for slider
      foreach ( array( 'czr_slide_title', 'czr_slide_text', 'czr_slide_button_text', 'czr_slide_background_alt' ) as $filter )
        add_filter( $filter, 'czr_fn_apply_qtranslate' );
      //sets no character limit for slider (title, lead text and button title) => allow users to use qtranslate tags for as many languages they wants ([:en]English text[:de]German text...and so on)
      foreach ( array( 'czr_slide_title_length', 'czr_slide_text_length', 'czr_slide_button_length' ) as $filter )
        add_filter( $filter  , 'czr_fn_remove_char_limit');

      //outputs the qtranslate translation for archive titles;
      $tc_archive_titles = array( 'tag_archive', 'category_archive', 'author_archive', 'search_results_archive');
      foreach ( $tc_archive_titles as $title )
        add_filter("czr_{$title}_title", 'czr_fn_apply_qtranslate' , 20);

      // QtranslateX for FP when no FPC or FPU running
      if ( ! class_exists('TC_fpu') && ! class_exists('TC_fpc') ) {
        //outputs correct urls for current language : fp
        add_filter( 'czr_fp_link_url' , 'czr_fn_url_lang');
        //outputs the qtranslate translation for featured pages
        add_filter( 'czr_fp_text', 'czr_fn_apply_qtranslate' );
        add_filter( 'czr_fp_button_text', 'czr_fn_apply_qtranslate' );

        /* The following is pretty useless at the momment since we should inhibit preview js code */
        //modify the customizer transport from post message to null for some options
        add_filter( 'czr_featured_page_button_text_customizer_set' , 'czr_fn_change_transport', 20, 2);
        add_filter( 'czr_featured_text_one_customizer_set' , 'czr_fn_change_transport', 20, 2);
        add_filter( 'czr_featured_text_two_customizer_set' , 'czr_fn_change_transport', 20, 2);
        add_filter( 'czr_featured_text_three_customizer_set', 'czr_fn_change_transport', 20, 2);
      }

      //posts slider (this filter is not fired in admin )
      add_filter('czr_posts_slider_pre_model', 'czr_fn_posts_slider_qtranslate', 10, 2);
      function czr_fn_posts_slider_qtranslate( $pre_slides, $posts_slider_instance ){
        if ( empty($pre_slides) )
          return $pre_slides;

        // remove useles q-translation of the slider view
        foreach ( array( 'czr_slide_title', 'czr_slide_text', 'czr_slide_button_text', 'czr_slide_background_alt' ) as $filter )
          remove_filter( $filter, 'czr_fn_apply_qtranslate' );

        // allow q-translation pre trim/sanitize
        foreach ( array( 'czr_posts_slider_button_text_pre_trim', 'czr_post_title_pre_trim', 'czr_post_excerpt_pre_sanitize', 'czr_posts_slide_background' ) as $filter )
          add_filter( $filter, 'czr_fn_apply_qtranslate' );

        //translate button text
        $pre_slides['common']['button_text'] = $pre_slides['common']['button_text'] ? $posts_slider_instance -> czr_fn_get_post_slide_button_text( $pre_slides['common']['button_text'] ) : '';

        //translate title and excerpt if needed
        $_posts = &$pre_slides['posts'];

        foreach ($_posts as &$_post) {
          $ID = $_post['ID'];
          $_p = get_post( $ID );
          if ( ! $_p ) continue;

          $_post['title'] = $_post['title'] ? $posts_slider_instance -> czr_fn_get_post_slide_title($_p, $ID) : '';
          $_post['text']  = $_post['text'] ? $posts_slider_instance -> czr_fn_get_post_slide_excerpt($_p, $ID) : '';
        }
        return $pre_slides;
      }
    }


    /**
    * Polylang compat hooks
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    private function czr_fn_set_polylang_compat() {
      // Disable posts slider transient caching
      add_filter('czr_posts_slider_use_transient', '__return_false');

      // If Polylang is active, hook function on the admin pages
      if ( function_exists( 'pll_register_string' ) )
        add_action( 'admin_init', 'czr_fn_pll_strings_setup' );

      function czr_fn_pll_strings_setup() {
        // grab theme options
        $czr_options = czr_fn_get_theme_options();
        // grab settings map, useful for some options labels
        $czr_fn_settings_map = czr_fn_get_customizer_map( $get_default = true );
        $tc_controls_map = $czr_fn_settings_map['add_setting_control'];
        // set $polylang_group;
        $polylang_group = 'customizr-pro' == CZR_THEMENAME ? 'Customizr-Pro' : 'Customizr';

        //get options to translate
        $tc_translatable_raw_options = CZR_plugins_compat::$instance -> czr_fn_get_string_options_to_translate();
        $tc_pll_options              = array();

        //build array if option => array( label (gettext-ed), option )
        foreach ( $tc_translatable_raw_options as $tc_translatable_option )
          if ( isset( $czr_options[$tc_translatable_option] ) ) {
            switch ( $tc_translatable_option ) {
              case 'tc_front_slider'             : $label = __( 'Front page slider name', 'customizr' );
                                                   break;
              case 'tc_posts_slider_button_text' : $label = __( 'Posts slider button text', 'customizr' );
                                                   break;
              default:                             $label = $tc_controls_map[$tc_translatable_option]['label'];
                                                   break;
            }//endswitch
            $tc_pll_options[$tc_translatable_option]= array(
                'label'  => $label,
                'value'  => $czr_options[$tc_translatable_option]
            );
          }

        //register the strings to translate
        foreach ( $tc_pll_options as $tc_pll_option )
          pll_register_string( $tc_pll_option['label'], $tc_pll_option['value'], $polylang_group);
      }// end tc_pll_strings_setup function

      // Front
      // If Polylang is active, translate/swap featured page buttons/text/link and slider
      if ( function_exists( 'pll_get_post' ) && function_exists( 'pll__' ) && ! is_admin() ) {
        //strings translation
        //get the options to translate
        $tc_translatable_options = CZR_plugins_compat::$instance -> czr_fn_get_string_options_to_translate();
        //translate
        foreach ( $tc_translatable_options as $tc_translatable_option )
          add_filter("czr_opt_$tc_translatable_option", 'pll__');

        /**
        * Tax filtering (home/blog posts filtered by cat)
        * @param array of term ids
        */
        function czr_fn_pll_translate_tax( $term_ids ){
          if ( ! ( is_array( $term_ids ) && ! empty( $term_ids ) ) )
            return $term_ids;

          $translated_terms = array();
          foreach ( $term_ids as $id ){
              $translated_term = pll_get_term( $id );
              $translated_terms[] = $translated_term ? $translated_term : $id;
          }
          return array_unique( $translated_terms );
        }

        //Translate category ids for the filtered posts in home/blog
        add_filter('czr_opt_tc_blog_restrict_by_cat', 'czr_fn_pll_translate_tax');
        /*end tax filtering*/

        /* Slider of posts */
        if ( function_exists( 'pll_current_language') ) {
        // Filter the posts query for the current language
          add_filter( 'czr_query_posts_slider_join'      , 'pll_posts_slider_join' );
          add_filter( 'czr_query_posts_slider_join_where', 'pll_posts_slider_join' );
        }
        function pll_posts_slider_join( $join ) {
          global $wpdb;
          switch ( current_filter() ){
            case 'czr_query_posts_slider_join'        : $join .= " INNER JOIN $wpdb->term_relationships AS pll_tr";
                                                       break;
            case 'czr_query_posts_slider_join_where'  : $_join = $wpdb->prepare("pll_tr.object_id = posts.ID AND pll_tr.term_taxonomy_id=%d ",
                                                                                pll_current_language( 'term_taxonomy_id' )
                                                       );
                                                       $join .= $join ? 'AND ' . $_join : 'WHERE '. $_join;
                                                       break;
          }

          return $join;
        }
        /*end Slider of posts */

        //Featured pages ids "translation"
        // Substitute any page id with the equivalent page in current language (if found)
        add_filter( 'czr_fp_id', 'czr_fn_pll_page_id', 20 );
        function czr_fn_pll_page_id( $fp_page_id ) {
          return is_int( pll_get_post( $fp_page_id ) ) ? pll_get_post( $fp_page_id ) : $fp_page_id;
        }
      }//end Front
    }//end polylang compat


    /**
    * WPML compat hooks
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    private function czr_fn_set_wpml_compat() {
      //credits : @Srdjan
      $this->default_language = apply_filters( 'wpml_default_language', null );
      $this->current_language = apply_filters( 'wpml_current_language', null );

      // Disable posts slider transient caching
      add_filter('czr_posts_slider_use_transient', '__return_false');
      //define the CONSTANT wpml context. This means that user have to set the translations again when switching from Customizr, to Customizr-Pro.
      //If we don't want to do this, let's go with 'Customizr-option' in any case.
      //Also I choose to use "-option" suffix to avoid confusions as with WPML you can also translate theme's strings ( gettexted -> __() ) and WPML by default assigns to theme the context 'customizr' (textdomain)
      define( 'CZR_WPML_CONTEXT' ,  'customizr-option' );

      // We cannot use wpml-config.xml to translate theme options because we use to update the option even in front page after retrieved, so we have to act on
      // a different filter.
      // When registering and translate strings WPML requires a 'context' and a 'name' (in a readable format for translators) plus the string to translate
      // context will be concatenated to the name and md5 will run on the result. The new result will represent the KEY for the WPML translations cache array.
      // This means that
      // 1) We cannot use translated string for the "name" param (which actually they say should be in a readable format ..)
      // 2) We need a way to use the same "name" both when registering the string to translate and retrieving its translations
      function czr_fn_wpml_get_options_names_config() {
        $_wp_cache_key     = 'czr_wpml_get_options_names_config';
        $option_name_assoc = wp_cache_get( $_wp_cache_key );

        if ( false === $option_name_assoc ) {
          $options_to_translate = CZR_plugins_compat::$instance -> czr_fn_get_string_options_to_translate();

          $option_name_assoc = apply_filters( 'czr_wpml_options_names_config', array(
 //           'tc_front_slider'              => 'Front page slider name', //Handled in a different way by Srdjan
            'tc_posts_slider_button_text'  => 'Posts slider button text',
            'tc_tag_title'                 => 'Tag pages title',
            'tc_cat_title'                 => 'Category pages title',
            'tc_author_title'              => 'Author pages title',
            'tc_search_title'              => 'Search pages title',
            'tc_social_in_sidebar_title'   => 'Social link title in sidebars',
            'tc_featured_page_button_text' => 'Featured button text',
            'tc_featured_text_one'         => 'Featured text one',
            'tc_featured_text_two'         => 'Featured text two',
            'tc_featured_text_three'       => 'Featured text three'
          ) );

          foreach ( $option_name_assoc as $key => $value ) {
            //use array_key_exists when and if options_to_translate will be an associative array
            if ( ! in_array( $key, $options_to_translate ) )
              unset( $option_name_assoc[$key] );
            else
              //md5 and html are stripped in wpml string table rendering, we add it for a better key
              $option_name_assoc[$key]    = $value . ' - ' . md5($key); //name
          }

          $option_name_assoc = apply_filters( 'czr_wpml_options_names_config_pre_cache', $option_name_assoc );
          //cache this 'cause is used several times in filter callbacks
          wp_cache_set( $_wp_cache_key, $option_name_assoc );
        }
        return apply_filters( 'czr_wpml_get_options_names_config', $option_name_assoc );
      }

      //Wras wpml_object_id in a more convenient function which recursevely translates array of values
      //$object can be an array or a single value
      function czr_fn_wpml_object_id( $object_id, $type ) {
        if ( empty( $object_id ) )
          return $object_id;
        if ( is_array( $object_id ) )
          return array_map( 'czr_fn_wpml_object_id', $object_id, array_fill( 0, sizeof( $object_id ), $type ) );
        return apply_filters( 'wpml_object_id', $object_id, $type, true );
      }

      //credits: @Srdjan -> filter the slides in the current language
      function sliders_filter( $sliders ) {
        if ( is_array( $sliders ) )
          foreach ( $sliders as $name => $slides ) {
            foreach ( $slides as $key => $attachment_id ) {
              // Get current slide language
              $slide_language = apply_filters( 'wpml_element_language_code',
                            null, array('element_id' => $attachment_id,
                                'element_type' => 'attachment') );
              if ( CZR_plugins_compat::$instance->current_language != $slide_language ) {
                // Replace with translated slide
                $translated_slide_id = apply_filters( 'wpml_object_id',
                                $attachment_id, 'attachment', false );
                if ( $translated_slide_id )
                  $sliders[$name][$key] = $translated_slide_id;
              }
            }
            $sliders[$name] = array_unique( $sliders[$name] );
          }

        return $sliders;
      }
      //credits: @Srdjan,
      function add_theme_options_filter() {
        add_filter( 'option_tc_theme_options', 'theme_options_filter', 99 );
      }
      //credits: @Srdjan
      function theme_options_filter( $options ) {
        if ( isset( $options['tc_sliders'] ) ) {
            $options['tc_sliders'] = sliders_filter( $options['tc_sliders'] );
        }
        return $options;
      }
      //credits: @Srdjan
      function edit_attachment_action( $attachment_id ) {
        $languages = apply_filters( 'wpml_active_languages', array() );
        // TODO check which meta keys are a must
        $meta_data = get_post_custom( $attachment_id );
        foreach ( $languages as $language) {
            $translated_attachment_id = apply_filters( 'wpml_object_id',
                    $attachment_id, 'attachment', false, $language['code'] );
            // Update post meta
            foreach ( array('post_slider_key', 'slider_check_key') as $meta_key ) {
                if ( isset( $meta_data[$meta_key][0] ) ) {
                    update_post_meta( $translated_attachment_id, $meta_key, $meta_data[$meta_key][0] );
                }
            }
        }
      }

      function pre_update_option_filter( $options ) {
        if ( isset( $options['tc_sliders'] ) ) {
            // Force default language
            $current_language = CZR_plugins_compat::$instance->current_language;
            CZR_plugins_compat::$instance->current_language = CZR_plugins_compat::$instance->default_language;
            $options['tc_sliders'] = sliders_filter( $options['tc_sliders'] );
            CZR_plugins_compat::$instance->current_language = $current_language;
        }
        return $options;
      }

      add_action( 'admin_init', 'czr_fn_wpml_admin_setup' );

      function czr_fn_wpml_admin_setup() {
        // If wpml-string-translation is active perform admin pages translation
        if ( function_exists( 'icl_register_string' ) ) {
          $tc_wpml_option_name = czr_fn_wpml_get_options_names_config();
          $tc_wpml_options     = array_keys($tc_wpml_option_name);

          // grab theme options
          $czr_options = czr_fn_get_theme_options();

          // build array of options to translate
          foreach ( $tc_wpml_options as $tc_wpml_option )
            if ( isset( $czr_options[$tc_wpml_option] ) )
              icl_register_string( CZR_WPML_CONTEXT,
                $tc_wpml_option_name[$tc_wpml_option],
                esc_attr($czr_options[$tc_wpml_option]) //value
            );
        }//end of string based admin translation
        //Taxonomies/Pages "transposing" in the Customizer
        //We actually could just do this instead of A) and B) in front, but we retrieve the options in front before the compat method is called (after_setup_theme with lower priority) and I prefer to keep front and back separated in this case. Different opinions are welcome, but not too much :P.
        //we have to filter the interesting options so they appear "translated" in the customizer too, 'cause wpml filters the pages/cats to choose (fp, cat pickers), and we kinda like this :), right (less memory)?
        //Side effect example for categories: TODO
        //In English we have set to filter blog posts for cat A,B and C.
        //In Italian we do not have cat C so there will be displayed transposed cats A and B
        //if we change this option in the Customizer with lang IT removing B, e.g., when we switch to EN we'll have that the array of cats contains just A, as it as been overwritten with the new setting
        if ( czr_fn_is_customize_left_panel() )
          add_filter( 'option_tc_theme_options', 'czr_fn_wpml_customizer_options_transpose' );
        function czr_fn_wpml_customizer_options_transpose( $options ) {
          $options_to_transpose = apply_filters ( 'czr_wpml_customizer_translate_options', array(
            'page'     => ( ! class_exists('TC_fpu') && ! class_exists('TC_fpc') ) ? array( 'tc_featured_page_one', 'tc_featured_page_two', 'tc_featured_page_three' ) : array(),
            'category' => array( 'tc_blog_restrict_by_cat' )
            )
          );
          foreach ( $options_to_transpose as $type => $option_to_transpose )
            foreach ( $option_to_transpose as $option )
              if ( isset( $options[$option] ) )
                $options[$option] = czr_fn_wpml_object_id( $options[$option], $type);
          return $options;
        }

        //credits @Srdjan
        // Filter slides in admin screens
        add_action( '__attachment_slider_infos', 'add_theme_options_filter', 9 );
        add_action( '__post_slider_infos', 'add_theme_options_filter', 9 );
        // Update translated slide post meta
        add_action( 'edit_attachment', 'edit_attachment_action', 99 );
        // Pre-save hook
        add_filter( 'pre_update_option_tc_theme_options', 'pre_update_option_filter', 99 );

      }// end tc_wpml_admin_setup function

      // Front
      // If WPML string translator is active, translate/swap featured page buttons/text/link and slider
      if ( ! is_admin() ) {
        // String transaltion binders : requires wpml icl_t function
        if ( function_exists( 'icl_t') ) {
          /*** TC - WPML bind, wrap WPML string translator function into convenient tc functions ***/
          //define our icl_t wrapper for options filtered with czr_opt_{$option}
          if ( ! function_exists( 'czr_fn_wpml_t_opt' ) ) {
            function czr_fn_wpml_t_opt( $string ) {
              return czr_fn_wpml_t( $string, str_replace('czr_opt_', '', current_filter() ) );
            }
          }
          //special function for the post slider button text pre trim filter
          if ( ! function_exists( 'czr_fn_wpml_t_ps_button_text' ) ) {
            function czr_fn_wpml_t_ps_button_text( $string ) {
              return czr_fn_wpml_t( $string, 'czr_fn_posts_slider_button_text' );
            }
          }
          //define our icl_t wrapper
          if ( ! function_exists( 'czr_fn_wpml_t' ) ) {
            function czr_fn_wpml_t( $string, $opt ) {
              $tc_wpml_options_names = czr_fn_wpml_get_options_names_config();
              return icl_t( CZR_WPML_CONTEXT, $czr_fn_wpml_options_names[$opt], $string );
            }
          }
          /*** End TC - WPML bind ***/

          //get the options to translate
          $tc_wpml_options = array_keys( czr_fn_wpml_get_options_names_config() );

          //strings translation
          foreach ( $tc_wpml_options as $tc_wpml_option )
            add_filter("czr_opt_$tc_wpml_option", 'czr_fn_wpml_t_opt', 20 );

          //translates sliders? credits @Srdjan
          add_filter( 'czr_opt_tc_sliders', 'sliders_filter', 99 );

        }
        /*A) FP*/
        // Featured pages ids "translation"
        add_filter( 'czr_fp_id', 'czr_fn_wpml_page_id', 20 );
        function czr_fn_wpml_page_id( $fp_page_id ) {
          return czr_fn_wpml_object_id( $fp_page_id, 'page');
        }

        /*B) Tax */
        /**
        * Cat filtering (home/blog posts filtered by cat)
        *
        * AFAIK wpml needs to exactly know which kind of tax we're looking for, category, tag ecc..
        * @param array of term ids
        */
        function czr_fn_wpml_translate_cat( $cat_ids ){
          if ( ! ( is_array( $cat_ids ) && ! empty( $cat_ids ) ) )
            return $cat_ids;
          return array_unique( czr_fn_wpml_object_id( $cat_ids, 'category' ) );
        }
        //Translate category ids for the filtered posts in home/blog
        add_filter('czr_opt_tc_blog_restrict_by_cat', 'czr_fn_wpml_translate_cat');
        /*end tax filtering*/

        /* Slider of posts */
        if ( defined( 'ICL_LANGUAGE_CODE') ) {
        // Filter the posts query for the current language
          add_filter( 'czr_query_posts_slider_join'      , 'wpml_posts_slider_join' );
          add_filter( 'czr_query_posts_slider_join_where', 'wpml_posts_slider_join' );
        }
        function wpml_posts_slider_join( $join ) {
          global $wpdb;
          switch ( current_filter() ){
            case 'czr_query_posts_slider_join'        : $join .= " INNER JOIN {$wpdb->prefix}icl_translations AS wpml_tr";
                                                       break;
            case 'czr_query_posts_slider_join_where'  : $_join = $wpdb->prepare("wpml_tr.element_id = posts.ID AND wpml_tr.language_code=%s AND wpml_tr.element_type=%s",
                                                                    ICL_LANGUAGE_CODE,
                                                                    'post_post'
                                                       );
                                                       $join .= $join ? 'AND ' . $_join : 'WHERE '. $_join;
                                                       break;
          }

          return $join;
        }
        /*end Slider of posts */
        /*end Slider*/
      }//end Front
    }//end wpml compat




    /**
    * The Events Calendar compat hooks
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    private function czr_fn_set_the_events_calendar_compat() {
      /*
      * Are we in the Events list context?
      */
      if ( ! ( function_exists( 'czr_fn_is_tec_events_list' ) ) ) {
        function czr_fn_is_tec_events_list() {
          return function_exists( 'tribe_is_event_query' ) && tribe_is_event_query() && is_post_type_archive();
        }
      }

      //disables post navigation
      add_filter( 'czr_show_post_navigation', 'czr_fn_tec_disable_post_navigation' );
      function czr_fn_tec_disable_post_navigation($bool) {
        return ( function_exists('tribe_is_event_query') && tribe_is_event_query() ) ? false : $bool;
      }

      // Force the tax name in the breadcrumb when list of events shown as 'Month'
      // The Events Calendar adds a filter on post_type_archive_title with __return_false callback
      // for their own reasons. This impacts on our breadcrumb 'cause we use the function post_type_archive_title() to build up the trail arg in posty_type_archives contexts.
      // What we do here is unhooking their callback before the breadcrumb is built and re-hook it after it has been displayed
      add_action( 'wp_head', 'czr_fn_tec_allow_display_breadcrumb_in_month_view');
      function czr_fn_tec_allow_display_breadcrumb_in_month_view() {
        if ( ! ( czr_fn_is_tec_events_list() && function_exists( 'tribe_is_month' ) && tribe_is_month() ) )
          return;

        add_filter( 'czr_breadcrumb_trail_args', 'czr_fn_tec_unhook_empty_post_type_archive_title');
        function czr_fn_tec_unhook_empty_post_type_archive_title( $args = null ) {
          remove_filter( 'post_type_archive_title', '__return_false', 10 );
          return $args;
        }
        add_filter( 'czr_breadcrumb_trail_display', 'czr_fn_tec_rehook_empty_post_type_archive_title', PHP_INT_MAX );
        function czr_fn_tec_rehook_empty_post_type_archive_title( $breadcrumb = null ) {
          add_filter( 'post_type_archive_title', '__return_false', 10 );
          return $breadcrumb;
        }
      }

    }//end the-events-calendar compat



    /**
    * OptimizePress compat hooks
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    private function czr_fn_set_optimizepress_compat() {
      add_action('wp_print_scripts', 'czr_fn_op_dequeue_fancybox_js');
      function czr_fn_op_dequeue_fancybox_js(){
        if ( function_exists('is_le_page') ){
          /* Op Back End: Dequeue tc-scripts */
          if ( is_le_page() || defined('OP_LIVEEDITOR') ) {
            wp_dequeue_script('czr-scripts');
            wp_dequeue_script('czr-fancybox');
          }
          else {
            /* Front End: Dequeue Fancybox maybe already embedded in Customizr */
            wp_dequeue_script('czr-fancybox');
            //wp_dequeue_script(OP_SN.'-fancybox');
          }
        }
      }

      /* Remove fancybox loading icon*/
      add_action('wp_footer','czr_fn_op_remove_fancyboxloading');
      function czr_fn_op_remove_fancyboxloading(){
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
    private function czr_fn_set_sensei_compat() {
      //unkooks the default sensei wrappers and add customizr's content wrapper and action hooks
      global $woothemes_sensei;
      remove_action( 'sensei_before_main_content', array( $woothemes_sensei->frontend, 'sensei_output_content_wrapper' ), 10 );
      remove_action( 'sensei_after_main_content', array( $woothemes_sensei->frontend, 'sensei_output_content_wrapper_end' ), 10 );

      add_action('sensei_before_main_content', 'czr_fn_sensei_wrappers', 10);
      add_action('sensei_after_main_content', 'czr_fn_sensei_wrappers', 10);


      function czr_fn_sensei_wrappers() {
        switch ( current_filter() ) {
          case 'sensei_before_main_content': CZR_plugins_compat::$instance -> czr_fn_mainwrapper_start();
                                             break;

          case 'sensei_after_main_content' : CZR_plugins_compat::$instance -> czr_fn_mainwrapper_end();
                                             break;
        }//end of switch on hook
      }//end of nested function

      // hide tax archive title
      add_filter( 'czr_show_tax_archive_title', 'czr_fn_sensei_disable_tax_archive_title');
      function czr_fn_sensei_disable_tax_archive_title( $bool ){
        return ( function_exists('is_sensei') && is_sensei() ) ? false : $bool;
      }

      //disables post navigation
      add_filter( 'czr_show_post_navigation', 'czr_fn_sensei_disable_post_navigation' );
      function czr_fn_sensei_disable_post_navigation($bool) {
        return ( function_exists('is_sensei') && is_sensei() ) ? false : $bool;
      }
      //removes post comment action on after_loop hook
      add_filter( 'czr_are_comments_enabled', 'czr_fn_sensei_disable_comments' );
      function czr_fn_sensei_disable_comments($bool) {
        return ( function_exists('is_sensei') && is_sensei() ) ? false : $bool;
      }
    }//end sensei compat




    /**
    * Woocommerce compat hooks
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    private function czr_fn_set_woocomerce_compat() {
      //unkooks the default woocommerce wrappersv and add customizr's content wrapper and action hooks
      remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
      remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
      add_action('woocommerce_before_main_content', 'czr_fn_woocommerce_wrappers', 10);
      add_action('woocommerce_after_main_content', 'czr_fn_woocommerce_wrappers', 10);


      //disable WooCommerce default breadcrumb
      if ( apply_filters( 'czr_disable_woocommerce_breadcrumb', true ) )
        remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );

      function czr_fn_woocommerce_wrappers() {
        switch ( current_filter() ) {
          case 'woocommerce_before_main_content': CZR_plugins_compat::$instance -> czr_fn_mainwrapper_start();
                                                  break;

          case 'woocommerce_after_main_content' : CZR_plugins_compat::$instance -> czr_fn_mainwrapper_end();
                                                  break;
        }//end of switch on hook
      }//end of nested function

      //Helpers
      function czr_fn_wc_is_checkout_cart() {
        return is_checkout() || is_cart() || defined('WOOCOMMERCE_CHECKOUT') || defined('WOOCOMMERCE_CART');
      }

      function czr_fn_woocommerce_wc_cart_enabled() {
        return 1 == esc_attr( czr_fn_get_opt( 'tc_woocommerce_header_cart' ) );
      }

      //disable title icons
      add_filter( 'czr_opt_tc_show_title_icon', 'czr_fn_woocommerce_disable_title_icon' );
      function czr_fn_woocommerce_disable_title_icon($bool) {
        return ( function_exists('czr_fn_wc_is_checkout_cart') && czr_fn_wc_is_checkout_cart() ) ? false : $bool;
      }
      // use Customizr title
      // initially used to display the edit button
    //  add_filter( 'the_title', 'czr_fn_woocommerce_the_title' );
      function czr_fn_woocommerce_the_title( $_title ){
        if ( function_exists('is_woocommerce') && is_woocommerce() && ! is_page() )
          return apply_filters( 'czr_title_text', $_title );
        return $_title;
      }

      // hide tax archive title
      add_filter( 'czr_show_tax_archive_title', 'czr_fn_woocommerce_disable_tax_archive_title');
      function czr_fn_woocommerce_disable_tax_archive_title( $bool ){
        return ( function_exists('is_woocommerce') && is_woocommerce() ) ? false : $bool;
      }

      //allow slider in the woocommerce shop page
      add_filter('czr_show_slider', 'czr_fn_woocommerce_enable_shop_slider');
      function czr_fn_woocommerce_enable_shop_slider( $bool ){
        return ( function_exists('is_woocommerce') && is_woocommerce() && function_exists('is_shop') && is_shop() ) ? true : $bool;
      }

      //to allow the slider in the woocommerce shop page we need the shop page id
      add_filter('czr_slider_get_real_id', 'czr_fn_woocommerce_shop_page_id');
      function czr_fn_woocommerce_shop_page_id( $id ){
        return ( function_exists('is_woocommerce') && is_woocommerce() && function_exists('is_shop') && is_shop() && function_exists('wc_get_page_id') ) ? wc_get_page_id('shop') : $id;
      }

      //handles the woocomerce sidebar : removes action if sidebars not active
      if ( !is_active_sidebar( 'shop') ) {
        remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
      }

      //disables post navigation
      add_filter( 'czr_show_post_navigation', 'czr_fn_woocommerce_disable_post_navigation' );
      function czr_fn_woocommerce_disable_post_navigation($bool) {
         return ( function_exists('is_woocommerce') && is_woocommerce() ) ? false : $bool;
      }


      //removes post comment action on after_loop hook
      add_filter( 'czr_are_comments_enabled', 'czr_fn_woocommerce_disable_comments' );
      function czr_fn_woocommerce_disable_comments($bool) {
         return ( function_exists('is_woocommerce') && is_woocommerce() ) ? false : $bool;
      }

      //link smooth scroll: exclude woocommerce tabs
      add_filter( 'czr_anchor_smoothscroll_excl', 'czr_fn_woocommerce_disable_link_scroll' );
      function czr_fn_woocommerce_disable_link_scroll( $excl ){
        if ( false == esc_attr( czr_fn_get_opt('tc_link_scroll') ) ) return $excl;

        if ( function_exists('is_woocommerce') && is_woocommerce() ) {
          if ( ! is_array( $excl ) )
            $excl = array();

          if ( ! is_array( $excl['deep'] ) )
            $excl['deep'] = array() ;

          if ( ! is_array( $excl['deep']['classes'] ) )
              $excl['deep']['classes'] = array();

          $excl['deep']['classes'][] = 'wc-tabs';
        }
        return $excl;
      }


      //changes customizr meta boxes priority (slider and layout not on top) if displaying woocommerce products in admin
      add_filter( 'czr_post_meta_boxes_priority', 'czr_fn_woocommerce_change_meta_boxes_priority' , 2 , 10 );
      function czr_fn_woocommerce_change_meta_boxes_priority($priority , $screen) {
         return ( 'product' == $screen ) ? 'default' : $priority ;
      }

      // Allow HEADER CART OPTIONS in the customizer
      // Returns a callback function needed by 'active_callback' to enable the options in the customizer
      add_filter( 'czr_woocommerce_options_enabled', 'czr_fn_woocommerce_options_enabled_cb' );
      function czr_fn_woocommerce_options_enabled_cb() {
        return '__return_true';
      }

      if ( ! is_admin() )
        //register wc cart in front
        add_action( 'wp', 'czr_fn_woocommerce_register_wc_cart' );

      function czr_fn_woocommerce_register_wc_cart() {
        czr_fn_register( array( 'model_class' => 'header/woocommerce_cart', 'id' => 'woocommerce_cart', 'controller' => 'czr_fn_woocommerce_wc_cart_enabled' ) );
      }
    }//end woocommerce compat


    /**
    * Visual Composer compat hooks
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    private function czr_fn_set_vc_compat() {
      //link smooth scroll: exclude all anchor links inside vc wrappers (.vc_row)
      add_filter( 'czr_anchor_smoothscroll_excl', 'czr_fn_vc_disable_link_scroll' );
      function czr_fn_vc_disable_link_scroll( $excl ){
        if ( false == esc_attr( czr_fn_get_opt('tc_link_scroll') ) ) return $excl;

        if ( ! is_array( $excl ) )
          $excl = array();

        if ( ! is_array( $excl['deep'] ) )
          $excl['deep'] = array() ;

        if ( ! is_array( $excl['deep']['classes'] ) )
            $excl['deep']['classes'] = array();

        $excl['deep']['classes'][] = 'vc_row';

        return $excl;
      }
    }//end woocommerce compat


    /**
    * Disqus Comment System compat hooks
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    private function czr_fn_set_disqus_compat() {
      if ( ! function_exists( 'czr_fn_disqus_comments_enabled' ) ) {
        function czr_fn_disqus_comments_enabled() {
          return function_exists( 'dsq_is_installed' ) && function_exists( 'dsq_can_replace' )
                 && dsq_is_installed() && dsq_can_replace();
        }
      }
      /* Since 3.5.0 the comments_template is wrapped it the "comments" id
      so we don't need the disqus wrapper anymore */
      //replace the default comment link anchor with a more descriptive disqus anchor
      add_filter( 'czr_comment_info_anchor', 'czr_fn_disqus_comment_info_anchor' );
      function czr_fn_disqus_comment_info_anchor( $anchor ) {
        return czr_fn_disqus_comments_enabled() ? '#disqus_thread' : $anchor;
      }

      /*
      * Add disqus specific attribute to the comment link
      */
      add_filter( 'czr_comment_info_link_attributes', 'czr_fn_disqus_comment_info_link_attributes' );
      function czr_fn_disqus_comment_info_link_attributes( $attributes ) {
        if ( czr_fn_disqus_comments_enabled() && is_array( $attributes ) )
          array_push( $attributes, 'data-disqus-identifier="javascript:this.page.identifier"');
        return $attributes;
      }

    }//end disqus compat


    /**
    * Ultimate Responsive Image Slider compat hooks
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    private function czr_fn_set_uris_compat() {
      add_filter ( 'czr_img_smart_load_options', 'czr_fn_uris_disable_img_smartload' ) ;
      function czr_fn_uris_disable_img_smartload( $options ){
        if ( ! is_array( $options ) )
          $options = array();

        if ( ! is_array( $options['opts'] ) )
          $options['opts'] = array();

        if ( ! is_array( $options['opts']['excludeImg'] ) )
          $options['opts']['excludeImg'] = array();

        $options['opts']['excludeImg'][] = '.sp-image';

        return $options;
      }
      //exclude uris cpt among those post types to which add customizer
      //meta boxes (layout/slider)
      add_filter( 'czr_post_metaboxes_cpt', 'czr_fn_uris_exclude_uris_cpt' );
      function czr_fn_uris_exclude_uris_cpt( $cpt = array() ) {
        if ( ! empty( $cpt ) && array_key_exists( 'ris_gallery', $cpt ) )
          unset( $cpt[ 'ris_gallery' ] );
        return $cpt;
      }
    }//end uris compat



    /**
    * CUSTOMIZR WRAPPERS
    * print the customizr wrappers
    *
    * @since 3.3+
    *
    * originally used for woocommerce compatibility
    */
    function czr_fn_mainwrapper_start() {

      /* SLIDERS : standard or slider of posts */
      if ( czr_fn_has('main_slider') ) {
        czr_fn_render_template( 'modules/slider/slider', array( 'model_id' => 'main_slider') );
      }

      elseif( czr_fn_has( 'main_posts_slider' ) ) {
        czr_fn_render_template( 'modules/slider/slider', array( 'model_id' => 'main_posts_slider') );
      }


      do_action('__before_main_wrapper');

      ?>
      <div id="main-wrapper" class="section">

        <?php if ( czr_fn_has('breadcrumb') ) : ?>
          <div class="container">
            <?php czr_fn_render_template( 'modules/breadcrumb' ) ?>
          </div>
        <?php endif ?>

        <?php do_action('__before_main_container'); ?>

        <div class="<?php czr_fn_main_container_class() ?>" role="main">

          <?php do_action('__before_content_wrapper'); ?>

          <div class="<?php czr_fn_column_content_wrapper_class() ?>">

            <?php do_action('__before_content'); ?>

            <div id="content" class="<?php czr_fn_article_container_class() ?>">
      <?php
    }

    function czr_fn_mainwrapper_end() {
      ?>
            </div>

            <?php do_action('__after_content'); ?>

            <?php
              /*
              * SIDEBARS
              */
              /* By design do not display sidebars in 404 */
              if ( ! is_404() ) {
                if ( czr_fn_has('left_sidebar') )
                  get_sidebar( 'left' );

                if ( czr_fn_has('right_sidebar') )
                  get_sidebar( 'right' );
              }
            ?>
          </div><!-- .column-content-wrapper -->

          <?php do_action('__after_content_wrapper'); ?>

        </div><!-- .container -->

        <?php do_action('__after_main_container'); ?>

      </div><!-- #main-wrapper -->

      <?php do_action('__after_main_wrapper');

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
    function czr_fn_is_plugin_active( $plugin ) {
      return in_array( $plugin, (array) get_option( 'active_plugins', array() ) ) || $this -> czr_fn_is_plugin_active_for_network( $plugin );
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
    function czr_fn_is_plugin_active_for_network( $plugin ) {
      if ( ! is_multisite() )
        return false;

      $plugins = get_site_option( 'active_sitewide_plugins');
      if ( isset($plugins[$plugin]) )
        return true;

      return false;
    }

    public function czr_fn_get_string_options_to_translate() {
      $string_options = array(
        'tc_front_slider',
        'tc_posts_slider_button_text',
        'tc_tag_title',
        'tc_cat_title',
        'tc_author_title',
        'tc_search_title',
        'tc_social_in_sidebar_title',
      );
      if ( ! class_exists('TC_fpu') && ! class_exists('TC_fpc') ) {
        $fp_areas = CZR_init::$instance -> fp_ids;
        foreach ( $fp_areas as $fp_area )
          $string_options[] = 'tc_featured_text_' . $fp_area;

        $string_options[] = 'tc_featured_page_button_text';
      }
      return apply_filters( 'czr_get_string_options_to_translate', $string_options );
    }
  }//end of class
endif;
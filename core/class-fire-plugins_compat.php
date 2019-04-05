<?php
/**
* Handles various plugins compatibilty (Jetpack, Bbpress, Qtranslate, Woocommerce, The Event Calendar ...)
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
      // TODO: check, might be not needed anymore as we don't re-store filtered options anymore in front
      remove_filter('option_tc_theme_options', 'qtranxf_translate_option', 5);

      /* ------------------------------------------------------------------------- *
       *  Add filters for tc_theme_options when using the contextualizer
       *  action "ctx_set_filters_for_opt_group_{$opt_group}" is declared in the contextualizer module => Contx_Options::ctx_setup_option_filters()
      /* ------------------------------------------------------------------------- */
      add_action( "ctx_set_filters_for_opt_group___tc_theme_options"  , array( $this , 'czr_fn_add_support_for_contextualizer') );
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
      add_theme_support( 'wc-product-gallery-zoom' );
      add_theme_support( 'wc-product-gallery-lightbox' );
      add_theme_support( 'wc-product-gallery-slider' );
      add_theme_support( 'the-events-calendar' );
      add_theme_support( 'event-tickets' );
      add_theme_support( 'optimize-press' );
      add_theme_support( 'woo-sensei' );
      add_theme_support( 'visual-composer' );//or js-composer as they call it
      add_theme_support( 'disqus' );
      add_theme_support( 'uris' );
      add_theme_support( 'tc-unlimited-featured-pages' );
      add_theme_support( 'learnpress' );
      add_theme_support( 'coauthors' );
    }



    /**
    * This function handles the following plugins compatibility : Jetpack (for the carousel addon and photon), Bbpress, Qtranslate, Woocommerce
    *
    * @package Customizr
    * @since Customizr 3.0.15
    */
    function czr_fn_plugins_compatibility() {
      /* Unlimited Featured Pages  */
      if ( current_theme_supports( 'tc-unlimited-featured-pages' ) && czr_fn_is_plugin_active('tc-unlimited-featured-pages/tc_unlimited_featured_pages.php') )
        $this -> czr_fn_set_tc_unlimited_featured_pages_compat();

      /* JETPACK */
      //adds compatibilty with the jetpack image carousel and photon
      if ( current_theme_supports( 'jetpack' ) && czr_fn_is_plugin_active('jetpack/jetpack.php') )
        $this -> czr_fn_set_jetpack_compat();


      /* BBPRESS */
      //if bbpress is installed and activated, we can check the existence of the contextual boolean function is_bbpress() to execute some code
      if ( current_theme_supports( 'bbpress' ) && czr_fn_is_plugin_active('bbpress/bbpress.php') )
        $this -> czr_fn_set_bbpress_compat();

      /* BUDDYPRESS */
      //if buddypress is installed and activated, we can check the existence of the contextual boolean function is_buddypress() to execute some code
      // we have to use buddy-press instead of buddypress as string for theme support as buddypress makes some checks on current_theme_supports('buddypress') which result in not using its templates
      if ( current_theme_supports( 'buddy-press' ) && czr_fn_is_plugin_active('buddypress/bp-loader.php') )
        $this -> czr_fn_set_buddypress_compat();

      /*
      * QTranslatex
      * Credits : @acub, http://websiter.ro
      */
      if ( current_theme_supports( 'qtranslate-x' ) && czr_fn_is_plugin_active('qtranslate-x/qtranslate.php') )
        $this -> czr_fn_set_qtranslatex_compat();

      /*
      * Polylang
      * Credits : Rocco Aliberti
      */
      if ( current_theme_supports( 'polylang' ) && ( czr_fn_is_plugin_active('polylang/polylang.php') || czr_fn_is_plugin_active('polylang-pro/polylang.php') ) )
        $this -> czr_fn_set_polylang_compat();

      /*
      * WPML
      */
      if ( current_theme_supports( 'wpml' ) && czr_fn_is_plugin_active('sitepress-multilingual-cms/sitepress.php') )
        $this -> czr_fn_set_wpml_compat();

      /* The Events Calendar */
      if ( current_theme_supports( 'the-events-calendar' ) && czr_fn_is_plugin_active('the-events-calendar/the-events-calendar.php') )
        $this -> czr_fn_set_the_events_calendar_compat();

      /* Event Tickets */
      if ( current_theme_supports( 'event-tickets' ) && czr_fn_is_plugin_active('event-tickets/event-tickets.php') )
        $this -> czr_fn_set_event_tickets_compat();

      /* Optimize Press */
      if ( current_theme_supports( 'optimize-press' ) && czr_fn_is_plugin_active('optimizePressPlugin/optimizepress.php') )
        $this -> czr_fn_set_optimizepress_compat();

      /* Woocommerce */
      if ( current_theme_supports( 'woocommerce' ) && czr_fn_is_plugin_active('woocommerce/woocommerce.php') )
        $this -> czr_fn_set_woocomerce_compat();

      /* Sensei woocommerce addon */
      if ( current_theme_supports( 'woo-sensei') && czr_fn_is_plugin_active('woothemes-sensei/woothemes-sensei.php') )
        $this -> czr_fn_set_sensei_compat();

      /* Visual Composer */
      if ( current_theme_supports( 'visual-composer') && czr_fn_is_plugin_active('js_composer/js_composer.php') )
        $this -> czr_fn_set_vc_compat();

      /* Disqus Comment System */
      if ( current_theme_supports( 'disqus' ) && czr_fn_is_plugin_active('disqus-comment-system/disqus.php') )
        $this -> czr_fn_set_disqus_compat();

      /* Ultimate Responsive Image Slider  */
      if ( current_theme_supports( 'uris' ) && czr_fn_is_plugin_active('ultimate-responsive-image-slider/ultimate-responsive-image-slider.php') )
        $this -> czr_fn_set_uris_compat();

      /* LearnPress */
      if ( current_theme_supports( 'learnpress' ) && czr_fn_is_plugin_active('learnpress/learnpress.php') )
        $this -> czr_fn_set_lp_compat();

      /* Coauthors-Plus */
      if ( current_theme_supports( 'coauthors' ) && czr_fn_is_plugin_active('co-authors-plus/co-authors-plus.php') )
        $this -> czr_fn_set_coauthors_compat();
    }//end of plugin compatibility function


    /*
    * Same in czr classic
    */

    /**
    * Jetpack compat hooks
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    private function czr_fn_set_jetpack_compat() {
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
      if ( ! function_exists( 'czr_fn_bbpress_is_bbpress' ) ) {
        function czr_fn_bbpress_is_bbpress() {
          return ( function_exists('is_bbpress') && is_bbpress() );
        }
      }
      if ( ! function_exists( 'czr_fn_bbpress_disable_feature' ) ) {
        function czr_fn_bbpress_disable_feature( $bool ) {
          return czr_fn_bbpress_is_bbpress() ? false : $bool;
        }
      }
      if ( ! function_exists( 'czr_fn_bbpress_enable_feature' ) ) {
        function czr_fn_bbpress_enable_feature( $bool ) {
          return czr_fn_bbpress_is_bbpress() ? true : $bool;
        }
      }

      //inform czr we're not in a list of posts
      add_filter( 'czr_is_list_of_posts', 'czr_fn_bbpress_disable_feature' );

      //force display singular_title
      add_filter( 'czr_display_page_heading', 'czr_fn_bbpress_enable_feature' );

      //disables post metas
      add_filter( 'czr_show_post_metas', 'czr_fn_bbpress_disable_feature', 100);

      //disables post navigation
      add_filter( 'czr_show_post_navigation', 'czr_fn_bbpress_disable_feature' );

      //disable the smartload help block
      add_filter( 'tc_is_img_smartload_help_on', 'czr_fn_bbpress_disable_feature' );

      //disable author info in posts
      add_filter( 'czr_show_author_metas_in_post', 'czr_fn_bbpress_disable_feature' );

      //disable related posts
      add_filter( 'czr_display_related_posts', 'czr_fn_bbpress_disable_feature' );

      //custom css -> TODO: IMPROVE!
      add_filter( 'czr_user_options_style', 'czr_fn_bbpress_custom_style' );
      function czr_fn_bbpress_custom_style( $_css ) {

        if ( czr_fn_bbpress_is_bbpress() ) {
          return $_css . '
#subscription-toggle {
    float: right;
}
#bbpress-forums p.bbp-topic-meta img.avatar,
#bbpress-forums ul.bbp-reply-revision-log img.avatar,
#bbpress-forums ul.bbp-topic-revision-log img.avatar,
#bbpress-forums div.bbp-template-notice img.avatar,
#bbpress-forums .widget_display_topics img.avatar,
#bbpress-forums .widget_display_replies img.avatar {
    margin-bottom: 0;
}
.bbpress.post-type-archive .entry-header .btn-edit {
  display: none;
}
';
        }

        return $_css;
      }
    }


    /*
    * Same in czr classic except for comments enabled filter prefix (tc_ -> czr_)
    */
    /**
    * BuddyPress compat hooks
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    private function czr_fn_set_buddypress_compat() {
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
        add_filter( 'tc_opt_tc_img_smart_load', '__return_false' );
      }

    }


    /*
    * same in czr classic with filter prefixes change ( tc_ -> czr_ )
    */
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
      foreach ( array( 'czr_slide_link_url', 'tc_logo_link_url') as $filter )
        add_filter( $filter, 'czr_fn_url_lang' );

      //outputs the qtranslate translation for slider
      foreach ( array( 'czr_slide_title', 'czr_slide_text', 'czr_slide_button_text', 'czr_slide_background_alt', 'czr_posts_slider_button_text_pre_trim' ) as $filter )
        add_filter( $filter, 'czr_fn_apply_qtranslate' );
      //sets no character limit for slider (title, lead text and button title) => allow users to use qtranslate tags for as many languages they wants ([:en]English text[:de]German text...and so on)
      foreach ( array( 'czr_slide_title_length', 'czr_slide_text_length', 'czr_slide_button_length' ) as $filter )
        add_filter( $filter  , 'czr_fn_remove_char_limit');

      //outputs the qtranslate translation for archive titles;
      $tc_archive_titles = array( 'tag_archive', 'category_archive', 'author_archive', 'search_results');
      foreach ( $tc_archive_titles as $title )
        add_filter("tc_{$title}_title", 'czr_fn_apply_qtranslate' , 20);

      // QtranslateX for FP when no FPC or FPU running
      if ( ! apply_filters( 'tc_other_plugins_force_fpu_disable', class_exists('TC_fpu') ) && ! class_exists('TC_fpc') ) {
        //outputs correct urls for current language : fp
        add_filter( 'czr_fp_link_url' , 'czr_fn_url_lang');
        //outputs the qtranslate translation for featured pages
        add_filter( 'czr_fp_text', 'czr_fn_apply_qtranslate' );
        add_filter( 'czr_fp_button_text', 'czr_fn_apply_qtranslate' );

        /* The following is pretty useless at the momment since we should inhibit preview js code */
        //modify the customizer transport from post message to null for some options
        add_filter( 'tc_featured_page_button_text_customizer_set' , 'czr_fn_change_transport', 20, 2);
        add_filter( 'tc_featured_text_one_customizer_set' , 'czr_fn_change_transport', 20, 2);
        add_filter( 'tc_featured_text_two_customizer_set' , 'czr_fn_change_transport', 20, 2);
        add_filter( 'tc_featured_text_three_customizer_set', 'czr_fn_change_transport', 20, 2);
      }

    }


    /*
    * same in czr classic
    */
    /**
    * Polylang compat hooks
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    private function czr_fn_set_polylang_compat() {

      // If Polylang is active, hook function on the admin pages
      if ( function_exists( 'pll_register_string' ) )
        add_action( 'admin_init', 'czr_fn_pll_strings_setup' );

      function czr_fn_pll_strings_setup() {
        // grab theme options
        $tc_options = czr_fn_get_theme_options();
        // grab settings map, useful for some options labels
        $tc_settings_map = czr_fn_get_customizer_map( $get_default = true );
        $tc_controls_map = $tc_settings_map['add_setting_control'];
        // set $polylang_group;
        $polylang_group = CZR_IS_PRO ? 'Customizr-Pro' : 'Customizr';

        //get options to translate
        $tc_translatable_raw_options = CZR_plugins_compat::$instance -> czr_fn_get_string_options_to_translate();
        $tc_pll_options              = array();

        //build array if option => array( label (gettext-ed), option )
        foreach ( $tc_translatable_raw_options as $tc_translatable_option )
          if ( isset( $tc_options[$tc_translatable_option] ) ) {
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
                'value'  => $tc_options[$tc_translatable_option]
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
          add_filter("tc_opt_{$tc_translatable_option}", 'pll__');

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
        add_filter('tc_opt_tc_blog_restrict_by_cat', 'czr_fn_pll_translate_tax');
        /*end tax filtering*/

        //Featured pages ids "translation"
        // Substitute any page id with the equivalent page in current language (if found)
        add_filter( 'czr_fp_id', 'czr_fn_pll_page_id', 20 );
        function czr_fn_pll_page_id( $fp_page_id ) {
          return is_int( pll_get_post( $fp_page_id ) ) ? pll_get_post( $fp_page_id ) : $fp_page_id;
        }
      }//end Front
    }//end polylang compat

    /*
    * same in czr classic
    */
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

      //define the CONSTANT wpml context. This means that user have to set the translations again when switching from Customizr, to Customizr-Pro.
      //If we don't want to do this, let's go with 'Customizr-option' in any case.
      //Also I choose to use "-option" suffix to avoid confusions as with WPML you can also translate theme's strings ( gettexted -> __() ) and WPML by default assigns to theme the context 'customizr' (textdomain)
      define( 'TC_WPML_CONTEXT' ,  'customizr-option' );

      // We cannot use wpml-config.xml to translate theme options because we use to update the option even in front page after retrieved, so we have to act on
      // a different filter.
      // When registering and translate strings WPML requires a 'context' and a 'name' (in a readable format for translators) plus the string to translate
      // context will be concatenated to the name and md5 will run on the result. The new result will represent the KEY for the WPML translations cache array.
      // This means that
      // 1) We cannot use translated string for the "name" param (which actually they say should be in a readable format ..)
      // 2) We need a way to use the same "name" both when registering the string to translate and retrieving its translations
      function czr_fn_wpml_get_options_names_config() {
        $_wp_cache_key     = 'tc_wpml_get_options_names_config';
        $option_name_assoc = wp_cache_get( $_wp_cache_key );

        if ( false === $option_name_assoc ) {
          $options_to_translate = CZR_plugins_compat::$instance -> czr_fn_get_string_options_to_translate();

          $option_name_assoc = apply_filters( 'tc_wpml_options_names_config', array(
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

          $option_name_assoc = apply_filters( 'tc_wpml_options_names_config_pre_cache', $option_name_assoc );
          //cache this 'cause is used several times in filter callbacks
          wp_cache_set( $_wp_cache_key, $option_name_assoc );
        }
        return apply_filters( 'tc_wpml_get_options_names_config', $option_name_assoc );
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
      function czr_fn_wpml_sliders_filter( $sliders ) {
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
      function czr_fn_wpml_add_theme_options_filter() {
        add_filter( 'option_tc_theme_options', 'czr_fn_wpml_theme_options_filter', 99 );
      }
      //credits: @Srdjan
      function czr_fn_wpml_theme_options_filter( $options ) {
        if ( isset( $options['tc_sliders'] ) ) {
            $options['tc_sliders'] = czr_fn_wpml_sliders_filter( $options['tc_sliders'] );
        }
        return $options;
      }
      //credits: @Srdjan
      function czr_fn_wpml_edit_attachment_action( $attachment_id ) {
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

      function czr_fn_wpml_pre_update_option_filter( $options ) {
        if ( isset( $options['tc_sliders'] ) ) {
            // Force default language
            $current_language = CZR_plugins_compat::$instance->current_language;
            CZR_plugins_compat::$instance->current_language = CZR_plugins_compat::$instance->default_language;
            $options['tc_sliders'] = czr_fn_wpml_sliders_filter( $options['tc_sliders'] );
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
          $tc_options = czr_fn_get_theme_options();

          // build array of options to translate
          foreach ( $tc_wpml_options as $tc_wpml_option )
            if ( isset( $tc_options[$tc_wpml_option] ) )
              icl_register_string( TC_WPML_CONTEXT,
                $tc_wpml_option_name[$tc_wpml_option],
                esc_attr($tc_options[$tc_wpml_option]) //value
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
          $options_to_transpose = apply_filters ( 'tc_wpml_customizer_translate_options', array(
            'page'     => ( ! apply_filters( 'tc_other_plugins_force_fpu_disable', class_exists('TC_fpu') ) && ! class_exists('TC_fpc') ) ? array( 'tc_featured_page_one', 'tc_featured_page_two', 'tc_featured_page_three' ) : array(),
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
        add_action( '__attachment_slider_infos', 'czr_fn_wpml_add_theme_options_filter', 9 );
        add_action( '__post_slider_infos', 'czr_fn_wpml_add_theme_options_filter', 9 );
        // Update translated slide post meta
        add_action( 'edit_attachment', 'czr_fn_wpml_edit_attachment_action', 99 );
        // Pre-save hook
        add_filter( 'pre_update_option_tc_theme_options', 'czr_fn_wpml_pre_update_option_filter', 99 );

      }// end tc_wpml_admin_setup function

      // Front
      // If WPML string translator is active, translate/swap featured page buttons/text/link and slider
      if ( ! is_admin() ) {
        // String transaltion binders : requires wpml icl_t function
        if ( function_exists( 'icl_t') ) {
          /*** TC - WPML bind, wrap WPML string translator function into convenient tc functions ***/
          //define our icl_t wrapper for options filtered with tc_opt_{$option}
          if ( ! function_exists( 'czr_fn_wpml_t_opt' ) ) {
            function czr_fn_wpml_t_opt( $string ) {
              return czr_fn_wpml_t( $string, str_replace('tc_opt_', '', current_filter() ) );
            }
          }

          //define our icl_t wrapper
          if ( ! function_exists( 'czr_fn_wpml_t' ) ) {
            function czr_fn_wpml_t( $string, $opt ) {
              $tc_wpml_options_names = czr_fn_wpml_get_options_names_config();
              return icl_t( TC_WPML_CONTEXT, $tc_wpml_options_names[$opt], $string );
            }
          }
          /*** End TC - WPML bind ***/

          //get the options to translate
          $tc_wpml_options = array_keys( czr_fn_wpml_get_options_names_config() );

          //strings translation
          foreach ( $tc_wpml_options as $tc_wpml_option )
            add_filter("tc_opt_{$tc_wpml_option}", 'czr_fn_wpml_t_opt', 20 );

          //translates sliders? credits @Srdjan
          add_filter( 'tc_opt_tc_sliders', 'czr_fn_wpml_sliders_filter', 99 );

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
        add_filter('tc_opt_tc_blog_restrict_by_cat', 'czr_fn_wpml_translate_cat');
        /*end tax filtering*/

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
      /*
      * Are we in single Event context?
      */
      if ( ! ( function_exists( 'czr_fn_is_tec_single_event' ) ) ) {
        function czr_fn_is_tec_single_event() {
          return function_exists( 'tribe_is_event_query' ) && tribe_is_event_query() && is_single();
        }
      }

      //disable related posts
      add_filter( 'czr_display_related_posts', 'czr_fn_tec_disable_related_posts' );
      function czr_fn_tec_disable_related_posts( $bool ) {
        return czr_fn_is_tec_single_event() ? false : $bool;
      }

      // Events archive is displayed, wrongly, with our post lists classes, we have to prevent this
      add_filter( 'czr_is_list_of_posts', 'czr_fn_tec_disable_post_list');
      function czr_fn_tec_disable_post_list( $bool ) {
        return czr_fn_is_tec_events_list() ? false : $bool;
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

      /*
      * Avoid php smartload image php parsing in events list content
      * See: https://github.com/presscustomizr/hueman/issues/285
      */
      add_filter( 'czr_disable_img_smart_load', 'czr_fn_tec_disable_img_smart_load_events_list', 999, 2);
      function czr_fn_tec_disable_img_smart_load_events_list( $_bool, $parent_filter ) {
        if ( 'the_content' == $parent_filter && czr_fn_is_tec_events_list() )
          return true;//disable
        return $_bool;
      }

    }//end the-events-calendar compat




    /**
    * Event Tickets compat hooks
    *
    * @package Customizr
    */
    private function czr_fn_set_event_tickets_compat() {
      // Workaround because of a bug on tec tickets that makes it require wp-content/themes/customizr/Custom Page Example (localized)
      // in place of wp-content/themes/customizr/custom-page.php
      add_filter( 'tribe_tickets_attendee_registration_page_template', 'czr_fn_et_ticket_fix_custom_page' );
      function czr_fn_et_ticket_fix_custom_page( $what ) {
        return str_replace( __( 'Custom Page Example', 'customizr' ), 'custom-page.php', $what );
      }

      add_filter( 'czr_is_list_of_posts', 'czr_fn_et_ticket_disable_post_list' );
      function czr_fn_et_ticket_disable_post_list( $bool ) {
        return function_exists( 'tribe' ) && tribe( 'tickets.attendee_registration' )->is_on_page() ? false : $bool;
      }
    }//end event-tickets compat





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
            wp_dequeue_script('tc-scripts');
          }
        }
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

      //removes related posts on __after_loop/__after_content hook
      add_filter( 'tc_opt_tc_related_posts', 'czr_fn_sensei_disable_related_posts' );
      function czr_fn_sensei_disable_related_posts( $bool ) {
          return ( function_exists('is_sensei') && is_sensei() ) ? 'disabled' : $bool;
      }

      //removes author info on __after_loop/__after_content hook
      add_filter( 'tc_opt_tc_show_author_info', 'czr_fn_sensei_disable_author_info' );
      function czr_fn_sensei_disable_author_info( $bool ) {
          return ( function_exists('is_sensei') && is_sensei() ) ? false : $bool;
      }

      function czr_fn_sensei_wrappers() {
        switch ( current_filter() ) {
          case 'sensei_before_main_content': CZR_plugins_compat::$instance -> czr_fn_mainwrapper_start();
                                             break;

          case 'sensei_after_main_content' : CZR_plugins_compat::$instance -> czr_fn_mainwrapper_end();
                                             break;
        }//end of switch on hook
      }//end of nested function


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
        return ( function_exists( 'is_checkout' ) && function_exists( 'is_cart' ) ) && ( is_checkout() || is_cart() || defined('WOOCOMMERCE_CHECKOUT') || defined('WOOCOMMERCE_CART') );
      }
      //Helper
      function czr_fn_woocommerce_shop_page_id( $id = null ){
        return ( function_exists('is_woocommerce') && is_woocommerce() && function_exists('is_shop') && is_shop() && function_exists('wc_get_page_id') ) ? wc_get_page_id( 'shop' ) : $id;
      }
      //Helper
      function czr_fn_woocommerce_shop_enable( $bool ){
        return ( function_exists('is_woocommerce') && is_woocommerce() && function_exists('is_shop') && is_shop() ) ? true : $bool;
      }
      //Helper
      function czr_fn_is_woocommerce_disable( $bool ) {
        return ( function_exists('is_woocommerce') && is_woocommerce() ) ? false : $bool;
      }

      //enable lightbox for images in the wc short description
      add_filter( 'czr_enable_lightbox_in_wc_short_description', '__return_true' );


      //enable images smartload in the wc short description
      add_filter( 'czr_enable_img_smart_load_in_wc_short_description', '__return_true' );


      //when in the woocommerce shop page use the "shop" id
      add_filter( 'czr_id', 'czr_fn_woocommerce_shop_page_id' );


      //allow slider in the woocommerce shop page
      add_filter( 'czr_show_slider', 'czr_fn_woocommerce_shop_enable');


      //allow page layout post meta in 'shop'
      add_filter( 'czr_is_page_layout', 'czr_fn_woocommerce_shop_enable' );


      //removes post comment action on __after_loop/__after_content hook
      add_filter( 'czr_are_comments_enabled', 'czr_fn_is_woocommerce_disable' );

      //removes related posts on __after_loop/__after_content hook
      add_filter( 'tc_opt_tc_related_posts', 'czr_fn_woocommerce_disable_related_posts' );
      function czr_fn_woocommerce_disable_related_posts( $bool ) {
          return ( function_exists('is_woocommerce') && is_woocommerce() ) ? 'disabled' : $bool;
      }

      //removes author info on __after_loop/__after_content hook
      add_filter( 'tc_opt_tc_show_author_info', 'czr_fn_is_woocommerce_disable' );

      //handles the woocomerce sidebar : removes action if sidebars not active
      if ( !is_active_sidebar( 'shop') ) {
        remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
      }


      //link smooth scroll: exclude woocommerce tabs
      add_filter( 'czr_anchor_smoothscroll_excl', 'czr_fn_woocommerce_disable_link_scroll' );
      function czr_fn_woocommerce_disable_link_scroll( $excl ){
        if ( false == esc_attr( czr_fn_opt('tc_link_scroll') ) ) return $excl;

        if ( function_exists('is_woocommerce') && is_woocommerce() ) {
          if ( ! is_array( $excl ) )
            $excl = array();

          if ( ! is_array( $excl['deep'] ) )
            $excl['deep'] = array() ;

          if ( ! is_array( $excl['deep']['classes'] ) )
              $excl['deep']['classes'] = array();

          $excl['deep']['classes'][] = 'wc-tabs';
          $excl['deep']['classes'][] = 'woocommerce-product-rating';
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
      add_filter( 'tc_woocommerce_options_enabled', 'czr_fn_woocommerce_options_enabled_cb' );
      function czr_fn_woocommerce_options_enabled_cb() {
        return function_exists( 'WC' ) ? '__return_true' : '__return_false';
      }

      add_filter( 'czr_woocommerce_options_enabled_controller', 'czr_fn_woocommerce_options_enabled_controller' );
      function czr_fn_woocommerce_options_enabled_controller() {
        return function_exists( 'WC' );
      }

      // Maybe display
      add_filter( 'tc_opt_tc_single_post_thumb_location', 'czr_fn_woocommerce_single_product_thumb_location' );
      function czr_fn_woocommerce_single_product_thumb_location( $where ) {
        if ( function_exists( 'is_product' ) && is_product() ) {
            return czr_fn_is_checked('tc_woocommerce_display_product_thumb_before_mw' ) ?  '__before_main_wrapper' : 'hide';
        }
        return $where;
      }

      //additional woocommerce skin style
      foreach ( array(
                  'skin_color_color',
                  'skin_color_background-color',
                  'skin_color_border-color',

                  'skin_dark_color_color',
                  'skin_dark_color_background-color',
                  'skin_dark_color_border-color',
               )  as $filter_key ) {

         add_filter( "czr_dynamic_{$filter_key}_prop_selectors", str_replace('-', '_', "czr_fn_wc_{$filter_key}_prop_selectors") );
      }

      function czr_fn_wc_skin_color_color_prop_selectors( $selectors ) {
         return array_merge( $selectors, array(
            '.woocommerce button.button[type=submit]:hover',
            '.woocommerce #respond input#submit:hover',
            '.woocommerce input#submit:hover',
            '.woocommerce input.button:hover',
            '.woocommerce a.button:hover',
            '.woocommerce .button.add_to_cart_button:hover',
            '.woocommerce #respond input#submit:focus',
            '.woocommerce input#submit:focus',
            '.woocommerce input.button:focus',
            '.woocommerce a.button:focus',
            '.woocommerce .button.add_to_cart_button:focus',
            '.woocommerce #respond input#submit:active',
            '.woocommerce input#submit:active',
            '.woocommerce input.button:active',
            '.woocommerce a.button:active',
            '.woocommerce .button.add_to_cart_button:active'
         ));
      }

      function czr_fn_wc_skin_color_background_color_prop_selectors( $selectors ) {
         return array_merge( $selectors, array(
            '.woocommerce button.button[type=submit]',
            '.woocommerce #respond input#submit',
            '.woocommerce input#submit',
            '.woocommerce input.button',
            '.woocommerce a.button',
            '.woocommerce .button.add_to_cart_button'
         ));
      }

      function czr_fn_wc_skin_color_border_color_prop_selectors( $selectors ) {
         return array_merge( $selectors, array(
            '.woocommerce .woocommerce-info',
            '.woocommerce .woocommerce-message',
            '.woocommerce button.button[type=submit]',
            '.woocommerce #respond input#submit',
            '.woocommerce input#submit',
            '.woocommerce input.button',
            '.woocommerce a.button',
            '.woocommerce .button.add_to_cart_button',
            '.woocommerce button.button[type=submit]:hover',
            '.woocommerce #respond input#submit:hover',
            '.woocommerce input#submit:hover',
            '.woocommerce input.button:hover',
            '.woocommerce a.button:hover',
            '.woocommerce .button.add_to_cart_button:hover',
            '.woocommerce button.button[type=submit]:focus',
            '.woocommerce #respond input#submit:focus',
            '.woocommerce input#submit:focus',
            '.woocommerce input.button:focus',
            '.woocommerce a.button:focus',
            '.woocommerce .button.add_to_cart_button:focus',
            '.woocommerce button.button[type=submit]:active',
            '.woocommerce #respond input#submit:active',
            '.woocommerce input#submit:active',
            '.woocommerce input.button:active',
            '.woocommerce a.button:active',
            '.woocommerce .button.add_to_cart_button:active'
         ));
      }


      function czr_fn_wc_skin_dark_color_color_prop_selectors( $selectors ) {
         return array_merge( $selectors, array(
            '.woocommerce input#submit[class*=alt]:hover',
            '.woocommerce input.button[class*=alt]:hover',
            '.woocommerce a.button[class*=alt]:hover',
            '.woocommerce button.button[class*=alt]:hover',
            '.woocommerce input#submit.alt.disabled:hover',
            '.woocommerce input.button.alt.disabled:hover',
            '.woocommerce button.button.alt.disabled:hover',
            '.woocommerce a.button.alt.disabled:hover',
            '.woocommerce input#submit[class*=alt]:focus',
            '.woocommerce input.button[class*=alt]:focus',
            '.woocommerce a.button[class*=alt]:focus',
            '.woocommerce button.button[class*=alt]:focus',
            '.woocommerce input#submit.alt.disabled:focus',
            '.woocommerce input.button.alt.disabled:focus',
            '.woocommerce button.button.alt.disabled:focus',
            '.woocommerce a.button.alt.disabled:focus',
            '.woocommerce input#submit[class*=alt]:active',
            '.woocommerce input.button[class*=alt]:active',
            '.woocommerce a.button[class*=alt]:active',
            '.woocommerce button.button[class*=alt]:active',
            '.woocommerce input#submit.alt.disabled:active',
            '.woocommerce input.button.alt.disabled:active',
            '.woocommerce button.button.alt.disabled:active',
            '.woocommerce a.button.alt.disabled:active',
            '.woocommerce #content div.product .woocommerce-tabs ul.tabs li a:hover',
            '.woocommerce #content div.product .woocommerce-tabs ul.tabs li.active a',
         ));
      }

      function czr_fn_wc_skin_dark_color_background_color_prop_selectors( $selectors ) {
         return array_merge( $selectors, array(
            '.woocommerce input#submit[class*=alt]',
            '.woocommerce input.button[class*=alt]',
            '.woocommerce a.button[class*=alt]',
            '.woocommerce button.button[class*=alt]',
            '.woocommerce input#submit.alt.disabled',
            '.woocommerce input.button.alt.disabled',
            '.woocommerce button.button.alt.disabled',
            '.woocommerce a.button.alt.disabled',
            '.woocommerce #content div.product .woocommerce-tabs ul.tabs li.active a::before',
            '.czr-link-hover-underline .widget_product_categories a:not(.btn)::before'
         ));
      }

      function czr_fn_wc_skin_dark_color_border_color_prop_selectors( $selectors ) {
         return array_merge( $selectors, array(
            '.woocommerce input#submit[class*=alt]:hover',
            '.woocommerce input.button[class*=alt]:hover',
            '.woocommerce a.button[class*=alt]:hover',
            '.woocommerce button.button[class*=alt]:hover',
            '.woocommerce input#submit.alt.disabled:hover',
            '.woocommerce input.button.alt.disabled:hover',
            '.woocommerce button.button.alt.disabled:hover',
            '.woocommerce a.button.alt.disabled:hover',
            '.woocommerce input#submit[class*=alt]:focus',
            '.woocommerce input.button[class*=alt]:focus',
            '.woocommerce a.button[class*=alt]:focus',
            '.woocommerce button.button[class*=alt]:focus',
            '.woocommerce input#submit.alt.disabled:focus',
            '.woocommerce input.button.alt.disabled:focus',
            '.woocommerce button.button.alt.disabled:focus',
            '.woocommerce a.button.alt.disabled:focus',
            '.woocommerce input#submit[class*=alt]:active',
            '.woocommerce input.button[class*=alt]:active',
            '.woocommerce a.button[class*=alt]:active',
            '.woocommerce button.button[class*=alt]:active',
            '.woocommerce input#submit.alt.disabled:active',
            '.woocommerce input.button.alt.disabled:active',
            '.woocommerce button.button.alt.disabled:active',
            '.woocommerce a.button.alt.disabled:active',
            '.woocommerce input#submit[class*=alt]',
            '.woocommerce input.button[class*=alt]',
            '.woocommerce a.button[class*=alt]',
            '.woocommerce button.button[class*=alt]',
            '.woocommerce input#submit.alt.disabled',
            '.woocommerce input.button.alt.disabled',
            '.woocommerce button.button.alt.disabled',
            '.woocommerce a.button.alt.disabled'
         ));
      }

    }//end woocommerce compat


    /*
    * same in czr classic except for the filter prefix (tc_ -> czr_)
    */
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
        if ( false == esc_attr( czr_fn_opt('tc_link_scroll') ) ) return $excl;

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
      /* Since 3.5.0 the comments_template is wrapped in the "comments" id
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

    /*
    * same in czr classic except for the filter prefix (tc_ -> czr_)
    */
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
    }//end uris compat


    /**
    * LearnPress compat hooks
    *
    * @package Customizr
    * @since Customizr 3.5+
    */
    private function czr_fn_set_lp_compat() {
      //do nothing if is admin
      if ( is_admin() ) {
        return;
      }
      //Helpers
      if ( ! function_exists( 'tc_lp_is_learnpress' ) ):
        function tc_lp_is_learnpress() {
          return function_exists( 'is_learnpress' ) && is_learnpress();
        }
      endif;
      if ( ! function_exists( 'tc_lp_is_learnpress_disable' ) ):
        function tc_lp_is_learnpress_disable( $bool ) {
          return tc_lp_is_learnpress() ? false : $bool;
        }
      endif;
      if ( ! function_exists( 'tc_lp_is_learnpress_enable' ) ):
        function tc_lp_is_learnpress_enable( $bool ) {
          return tc_lp_is_learnpress() ? true : $bool;
        }
      endif;
      if ( ! function_exists( 'tc_lp_is_learnpress_archive_disable' ) ):
        function tc_lp_is_learnpress_archive_disable( $bool ) {
          if ( function_exists( 'learn_press_is_course' )  && function_exists( 'learn_press_is_quiz' ) ) {
            return tc_lp_is_learnpress() && ! ( learn_press_is_course() || learn_press_is_quiz() )  ? false : $bool;
          }
          return $bool;
        }
      endif;


      //lp filters template_include falling back on the page.php theme template
      //without checking its existence, since we have no page.php template, we have to fall back
      //on index.php if what that filter returns is false ().
      //is_learnpress() function should be our reference conditional tag
      if ( ! function_exists( 'tc_lp_maybe_fall_back_on_index' ) ):
        function tc_lp_maybe_fall_back_on_index( $template ) {
          if ( ! tc_lp_is_learnpress() )
            return $template;

          if ( ! empty( $template ) )
            return $template;

          return get_template_part( 'index' );
        }
      endif;
      //See: plugins\learnpress\inc\class-lp-request-handler.php::process_request
      //where lp processes the course Enroll request at template_include|50
      //That's why we here use a prudential priority of 100
      //https://github.com/presscustomizr/customizr/issues/1589
      add_filter( 'template_include', 'tc_lp_maybe_fall_back_on_index', 100 );

      // Disable post lists and single views in lp contexts
      add_filter( 'czr_is_list_of_posts', 'tc_lp_is_learnpress_disable');
      add_filter( 'czr_is_single_post', 'tc_lp_is_learnpress_disable');

      //enable page view for lp archives
      add_filter( 'czr_is_single_page', 'tc_lp_is_learnpress_enable');
      //todo: display arhive title, do ot display metas in lp archives

      //do not display post navigation, lp uses its own, when relevant
      add_filter( 'tc_opt_tc_show_post_navigation', 'tc_lp_is_learnpress_disable' );

      //disable lp breadcrumb, we'll use our own
      remove_action( 'learn_press_before_main_content', 'learn_press_breadcrumb' );

    }//end lp compat


    /* same in czr classic */
    /*
    * Coauthors-Plus plugin compat hooks
    */
    private function czr_fn_set_coauthors_compat() {
      if ( !function_exists( 'coauthors_ids' )  ) {
        return;
      }

      add_filter( 'tc_post_author_id', 'tc_coauthors_post_author_ids' );
      if ( !function_exists( 'tc_coauthors_post_author_ids' ) ) {
        function tc_coauthors_post_author_ids() {
          $author_ids = coauthors_ids( $between = ',', $betweenLast = ',', $before = null, $after = null, $echo = false );
          return explode(  ',' , $author_ids );
        }
      }
    }



    /* same in czr classic */
    /**
    * TC Unlimited Featured Pages compat hooks
    * Since Customizr 3.4.24 we changed the functions and class prefixes
    * Olf fpu versions might refer to them throwing PHP errors
    * with the code below we basically "soft-disable" the plugin
    * without actyally disabling it to allow the user to update it
    * @package Customizr
    * @since Customizr 3.4.24
    */
    private function czr_fn_set_tc_unlimited_featured_pages_compat() {
      //This has to be fired after : tc_generates_featured_pages | 10 (priority)
      if ( class_exists( 'TC_fpu' ) &&  version_compare( TC_fpu::$instance -> plug_version, '2.0.24', '<' ) ) {

        //back
        if ( method_exists( 'TC_back_fpu', 'tc_add_controls_class' ) )
          remove_action ( 'customize_register'         , array( TC_back_fpu::$instance , 'tc_add_controls_class' ) ,10,1);

        if ( method_exists( 'TC_back_fpu', 'tc_customize_controls_js_css' ) )
          remove_action ( 'customize_controls_enqueue_scripts' , array(TC_back_fpu::$instance , 'tc_customize_controls_js_css' ), 100);

        if ( method_exists( 'TC_back_fpu', 'tc_customize_register' ) )
          remove_action ( 'customize_register'         , array( TC_back_fpu::$instance , 'tc_customize_register' ) , 20, 1 );

        if ( method_exists( 'TC_back_fpu', 'tc_customize_preview_js' ) )
          remove_action ( 'customize_preview_init'     , array( TC_back_fpu::$instance , 'tc_customize_preview_js' ));

        //do not remove customizr free featured pages option panel
        if ( method_exists( 'TC_fpu', 'tc_delete_fp_options' ) )
          remove_filter ( 'tc_front_page_option_map'   , array( TC_fpu::$instance , 'tc_delete_fp_options' ), 20 );

        //front
        if ( class_exists( 'TC_front_fpu' ) ) {
          if ( method_exists( 'TC_front_fpu', 'tc_set_fp_hook' ) )
            remove_action( 'template_redirect'         , array( TC_front_fpu::$instance , 'tc_set_fp_hook'), 10 );
          if ( method_exists( 'TC_front_fpu', 'tc_set_colors' ) )
            remove_action( 'wp_head'                   , array( TC_front_fpu::$instance , 'tc_set_colors'), 10 );
          if ( method_exists( 'TC_front_fpu', 'tc_enqueue_plug_resources' ) )
            remove_action( 'wp_enqueue_scripts'        , array( TC_front_fpu::$instance , 'tc_enqueue_plug_resources') );
        }

        //needed as some plugins (lang) will check the TC_fpu class existence
        add_filter( 'tc_other_plugins_force_fpu_disable', '__return_false' );
      }

    }

    /**
    * CUSTOMIZR WRAPPERS
    * print the customizr wrappers
    *
    * @since 3.3+
    *
    * originally used for woocommerce compatibility
    *
    */
    /* no archive headings */
    function czr_fn_mainwrapper_start() {

        // This hook is used to render the following elements(ordered by priorities) :
        // slider
        // singular thumbnail
        do_action('__before_main_wrapper')
      ?>

      <div id="main-wrapper" class="section">



          <?php
            //this was the previous implementation of the big heading.
            //The next one will be implemented with the slider module
          ?>
        <?php  if ( apply_filters( 'big_heading_enabled', false && ! czr_fn_is_real_home() && ! is_404() ) ): ?>
          <div class="container-fluid">
            <?php
              if ( czr_fn_is_registered_or_possible( 'archive_heading' ) )
                $_heading_template = 'content/post-lists/headings/archive_heading';
              elseif ( czr_fn_is_registered_or_possible( 'search_heading' ) )
                $_heading_template = 'content/post-lists/headings/search_heading';
              elseif ( czr_fn_is_registered_or_possible('post_heading') )
                $_heading_template = 'content/singular/headings/post_heading';
              else //pages and fallback
                $_heading_template = 'content/singular/headings/page_heading';

              czr_fn_render_template( $_heading_template );
            ?>
          </div>
        <?php endif ?>

        <?php
          /*
          * Featured Pages | 10
          * Breadcrumbs | 20
          */
          do_action('__before_main_container')
        ?>


        <div class="<?php czr_fn_main_container_class() ?>" role="main">

          <?php do_action('__before_content_wrapper'); ?>

          <div class="<?php czr_fn_column_content_wrapper_class() ?>">

            <?php do_action('__before_content'); ?>

            <div id="content" class="<?php czr_fn_article_container_class() ?>">

              <?php do_action ('__before_loop');

    }


    /* no navigation */
    function czr_fn_mainwrapper_end() {
                    /*
                     * Optionally attached to this hook :
                     * - In single posts:
                     *   - Author bio | 10
                     *   - Related posts | 20
                     * - In posts and pages
                     *   - Comments | 30
                     */
                    do_action ('__after_loop');
      ?>
            </div>

            <?php
                /*
                 * Optionally attached to this hook :
                 * - In single posts:
                 *   - Author bio | 10
                 *   - Related posts | 20
                 * - In posts and pages
                 *   - Comments | 30
                 */
                do_action('__after_content');

                /*
                * SIDEBARS
                */
                /* By design do not display sidebars in 404 or home empty */
                if ( ! ( czr_fn_is_home_empty() || is_404() ) ) {
                  if ( czr_fn_is_registered_or_possible('left_sidebar') )
                    get_sidebar( 'left' );

                  if ( czr_fn_is_registered_or_possible('right_sidebar') )
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


    /* same in czr classic */
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
        $fp_areas = CZR___::$instance -> fp_ids;
        foreach ( $fp_areas as $fp_area )
          $string_options[] = 'tc_featured_text_' . $fp_area;

        $string_options[] = 'tc_featured_page_button_text';
      }
      return apply_filters( 'tc_get_string_options_to_translate', $string_options );
    }


    // hook ctx_set_filters_for_opt_group___tc_theme_options
    // @param $opt_names = array() of Customizr options short name
    function czr_fn_add_support_for_contextualizer( $opt_names = array() ) {
        if ( ! is_array( $opt_names ) || ! function_exists( 'ctx_get_opt_val' ) )
          return;

        foreach ( $opt_names as $opt_name ) {
            add_filter( "tc_opt_{$opt_name}", function( $opt_value, $opt_name ) {
                return ctx_get_opt_val( $opt_value, $opt_name, 'tc_theme_options'  );
            }, 100, 2 );
        }
    }

  }//end of class
endif;
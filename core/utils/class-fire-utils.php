<?php
/**
* Defines filters and actions used in several templates/classes
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
if ( ! class_exists( 'CZR_cl_utils' ) ) :
  class CZR_cl_utils {

      //Access any method or var of the class with classname::$instance -> var or method():
      static $inst;
      static $instance;
      public $default_options;
      public $db_options;
      public $options;//not used in customizer context only
      public $is_customizing;
      public $czr_options_prefixes;

      function __construct () {
        self::$inst =& $this;
        self::$instance =& $this;

        //Various WP filters for
        //content
        //thumbnails => parses image if smartload enabled
        //title
        add_action( 'wp_head'                 , array( $this , 'czr_fn_wp_filters') );
      }





      /**
      * hook : after_setup_theme
      * @package Customizr
      * @since Customizr 3.3.0
      */
      function czr_fn_wp_filters() {
        add_filter( 'the_content'                         , array( $this , 'czr_fn_fancybox_content_filter' ) );
        if ( esc_attr( czr_fn_get_opt( 'tc_img_smart_load' ) ) ) {
          add_filter( 'the_content'                       , array( $this , 'czr_fn_parse_imgs' ), PHP_INT_MAX );
          add_filter( 'czr_thumb_html'                     , array( $this , 'czr_fn_parse_imgs' ) );
        }
        add_filter( 'wp_title'                            , array( $this , 'czr_fn_wp_title' ), 10, 2 );
      }


      /**
      * hook : the_content
      * Inspired from Unveil Lazy Load plugin : https://wordpress.org/plugins/unveil-lazy-load/ by @marubon
      *
      * @return string
      * @package Customizr
      * @since Customizr 3.3.0
      */
      function czr_fn_parse_imgs( $_html ) {
        if( is_feed() || is_preview() || ( wp_is_mobile() && apply_filters('czr_disable_img_smart_load_mobiles', false ) ) )
          return $_html;

        return preg_replace_callback('#<img([^>]+?)src=[\'"]?([^\'"\s>]+)[\'"]?([^>]*)>#', array( $this , 'czr_fn_regex_callback' ) , $_html);
      }


      /**
      * callback of preg_replace_callback in czr_fn_parse_imgs
      * Inspired from Unveil Lazy Load plugin : https://wordpress.org/plugins/unveil-lazy-load/ by @marubon
      *
      * @return string
      * @package Customizr
      * @since Customizr 3.3.0
      */
      private function czr_fn_regex_callback( $matches ) {
        $_placeholder = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

        if ( false !== strpos( $matches[0], 'data-src' ) ||
            preg_match('/ data-smartload *= *"false" */', $matches[0]) )
          return $matches[0];
        else
          return apply_filters( 'czr_img_smartloaded',
            str_replace( 'srcset=', 'data-srcset=',
                sprintf('<img %1$s src="%2$s" data-src="%3$s" %4$s>',
                    $matches[1],
                    $_placeholder,
                    $matches[2],
                    $matches[3]
                )
            )
          );
      }




      /**
      * Returns the current skin's primary color
      *
      * @package Customizr
      * @since Customizr 3.1.23
      */
      function czr_fn_getskincolor( $_what = null ) {
        $_color_map    = CZR_cl_init::$instance -> skin_color_map;
        $_color_map    = ( is_array($_color_map) ) ? $_color_map : array();

        $_active_skin =  str_replace('.min.', '.', basename( CZR_cl_init::$instance -> czr_fn_get_style_src() ) );
        //falls back to blue3 ( default #27CDA5 ) if not defined
        $_to_return = array( '#27CDA5', '#1b8d71' );

        switch ($_what) {
          case 'all':
            $_to_return = $_color_map;
          break;

          case 'pair':
            $_to_return = ( false != $_active_skin && array_key_exists( $_active_skin, $_color_map ) && is_array( $_color_map[$_active_skin] ) ) ? $_color_map[$_active_skin] : $_to_return;
          break;

          default:
            $_to_return = ( false != $_active_skin && isset($_color_map[$_active_skin][0]) ) ? $_color_map[$_active_skin][0] : $_to_return[0];
          break;
        }
        //Custom skin backward compatibility : different filter prefix
        $_to_return = apply_filters( 'tc_get_skincolor' , $_to_return , $_what );
        return apply_filters( 'czr_get_skincolor' , $_to_return , $_what );
      }




      /**
      * Returns the "real" queried post ID or if !isset, get_the_ID()
      * Checks some contextual booleans
      *
      * @package Customizr
      * @since Customizr 1.0
      */
      public static function czr_fn_id()  {
        if ( in_the_loop() ) {
          $tc_id            = get_the_ID();
        } else {
          global $post;
          $queried_object   = get_queried_object();
          $tc_id            = ( ! empty ( $post ) && isset($post -> ID) ) ? $post -> ID : null;
          $tc_id            = ( isset ($queried_object -> ID) ) ? $queried_object -> ID : $tc_id;
        }
        return ( is_404() || is_search() || is_archive() ) ? null : $tc_id;
      }




      /**
      * This function returns the layout (sidebar(s), or full width) to apply to a context
      *
      * @package Customizr
      * @since Customizr 1.0
      */
      public static function czr_fn_get_layout( $post_id , $sidebar_or_class = 'class' ) {
          $__options                    = CZR_cl_utils_options::$inst -> czr_fn_get_theme_options();
          global $post;
          //Article wrapper class definition
          $global_layout                = apply_filters( 'czr_global_layout' , CZR_cl_init::$instance -> global_layout );

          /* DEFAULT LAYOUTS */
          //what is the default layout we want to apply? By default we apply the global default layout
          $tc_sidebar_default_layout    = esc_attr( $__options['tc_sidebar_global_layout'] );

          //checks if the 'force default layout' option is checked and return the default layout before any specific layout
          if( isset($__options['tc_sidebar_force_layout']) && 1 == $__options['tc_sidebar_force_layout'] ) {
            $class_tab  = $global_layout[$tc_sidebar_default_layout];
            $class_tab  = $class_tab['content'];
            $tc_screen_layout = array(
              'sidebar' => $tc_sidebar_default_layout,
              'class'   => $class_tab
            );
            return $tc_screen_layout[$sidebar_or_class];
          }


          if ( is_single() )
            $tc_sidebar_default_layout  = esc_attr( $__options['tc_sidebar_post_layout'] );
          if ( is_page() )
            $tc_sidebar_default_layout  = esc_attr( $__options['tc_sidebar_page_layout'] );

          //builds the default layout option array including layout and article class
          $class_tab  = $global_layout[$tc_sidebar_default_layout];
          $class_tab  = $class_tab['content'];
          $tc_screen_layout             = array(
                      'sidebar' => $tc_sidebar_default_layout,
                      'class'   => $class_tab
          );

          //The following lines set the post specific layout if any, and if not keeps the default layout previously defined
          $tc_specific_post_layout    = false;
          global $wp_query;
          //if we are displaying an attachement, we use the parent post/page layout
          if ( $post && 'attachment' == $post -> post_type ) {
            $tc_specific_post_layout  = esc_attr( get_post_meta( $post->post_parent , $key = 'layout_key' , $single = true ) );
          }
          //for a singular post or page OR for the posts page
          elseif ( is_singular() || $wp_query -> is_posts_page ) {
            $tc_specific_post_layout  = esc_attr( get_post_meta( $post_id, $key = 'layout_key' , $single = true ) );
          }

          //checks if we display home page, either posts or static page and apply the customizer option
          if( (is_home() && 'posts' == get_option( 'show_on_front' ) ) || is_front_page()) {
             $tc_specific_post_layout = $__options['tc_front_layout'];
          }

          if( $tc_specific_post_layout ) {
              $class_tab  = $global_layout[$tc_specific_post_layout];
              $class_tab  = $class_tab['content'];
              $tc_screen_layout = array(
              'sidebar' => $tc_specific_post_layout,
              'class'   => $class_tab
            );
          }

        return apply_filters( 'czr_screen_layout' , $tc_screen_layout[$sidebar_or_class], $post_id , $sidebar_or_class );
      }


      /**
      * This function returns the column content wrapper class
      *
      * @package Customizr
      * @since Customizr 3.5
      */
      public static function czr_fn_get_column_content_wrapper_class() {
        return apply_filters( 'czr_column_content_wrapper_classes' , array('row', 'column-content-wrapper') );
      }

      /**
      * This function returns the article container class
      *
      * @package Customizr
      * @since Customizr 3.5
      */
      public static function czr_fn_get_article_container_class() {
        return apply_filters( 'czr_article_container_class' , array( self::czr_fn_get_layout( CZR_cl_utils::czr_fn_id() , 'class' ) , 'article-container' ) );
      }




      /**
       * Add an optional rel="tc-fancybox[]" attribute to all images embedded in a post.
       *
       * @package Customizr
       * @since Customizr 2.0.7
       */
      function czr_fn_fancybox_content_filter( $content) {
        $tc_fancybox = esc_attr( czr_fn_get_opt( 'tc_fancybox' ) );

        if ( 1 != $tc_fancybox )
          return $content;

        global $post;
        if ( ! isset($post) )
          return $content;

        $pattern ="/<a(.*?)href=( '|\")(.*?).(bmp|gif|jpeg|jpg|png)( '|\")(.*?)>/i";
        $replacement = '<a$1href=$2$3.$4$5 class="grouped_elements" rel="tc-fancybox-group'.$post -> ID.'"$6>';
        $r_content = preg_replace( $pattern, $replacement, $content);
        $content = $r_content ? $r_content : $content;
        return apply_filters( 'czr_fancybox_content_filter', $content );
      }




      /**
      * Title element formating
      *
      * @since Customizr 2.1.6
      *
      */
      function czr_fn_wp_title( $title, $sep ) {
        if ( function_exists( '_wp_render_title_tag' ) )
          return $title;

        global $paged, $page;

        if ( is_feed() )
          return $title;

        // Add the site name.
        $title .= get_bloginfo( 'name' );

        // Add the site description for the home/front page.
        $site_description = get_bloginfo( 'description' , 'display' );
        if ( $site_description && CZR_cl_utils::$inst -> czr_fn_is_home() )
          $title = "$title $sep $site_description";

        // Add a page number if necessary.
        if ( $paged >= 2 || $page >= 2 )
          $title = "$title $sep " . sprintf( __( 'Page %s' , 'customizr' ), max( $paged, $page ) );

        return $title;
      }




      /**
      * Check if we are displaying posts lists or front page
      *
      * @since Customizr 3.0.6
      *
      */
      function czr_fn_is_home() {
        //get info whether the front page is a list of last posts or a page
        return ( is_home() && ( 'posts' == get_option( 'show_on_front' ) || 'nothing' == get_option( 'show_on_front' ) ) ) || is_front_page();
      }





      /**
      * Check if we show posts or page content on home page
      *
      * @since Customizr 3.0.6
      *
      */
      function czr_fn_is_home_empty() {
        //check if the users has choosen the "no posts or page" option for home page
        return ( ( is_home() || is_front_page() ) && 'nothing' == get_option( 'show_on_front' ) ) ? true : false;
      }




      /**
      * Return object post type
      *
      * @since Customizr 3.0.10
      *
      */
      function czr_fn_get_post_type() {
        global $post;

        if ( ! isset($post) )
          return;

        return $post -> post_type;
      }






      /**
      * Boolean : check if we are in the no search results case
      *
      * @package Customizr
      * @since 3.0.10
      */
      function czr_fn_is_no_results() {
        global $wp_query;
        return ( is_search() && 0 == $wp_query -> post_count ) ? true : false;
      }





      /**
      * Gets the social networks list defined in customizer options
      *
      * @package Customizr
      * @since Customizr 3.0.10
      */
      function czr_fn_get_social_networks() {
        $__options    = CZR_cl_utils_options::$inst -> czr_fn_get_theme_options();

        //gets the social network array
        $socials      = apply_filters( 'czr_default_socials' , CZR_cl_init::$instance -> socials );

        //declares some vars
        $html         = '';

        foreach ( $socials as $key => $data ) {
          if ( $__options[$key] != '' ) {
              //gets height and width from image, we check if getimagesize can be used first with the error control operator
              $width = $height = '';
              if ( isset($data['custom_icon_url']) && @getimagesize($data['custom_icon_url']) ) { list( $width, $height ) = getimagesize($data['custom_icon_url']); }
              $type = isset( $data['type'] ) && ! empty( $data['type'] ) ? $data['type'] : 'url';
              $link = 'email' == $type ? 'mailto:' : '';
              $link .=  call_user_func( array( CZR_cl_utils_settings_map::$instance, 'czr_fn_sanitize_'.$type ), $__options[$key] );
              //there is one exception : rss feed has no target _blank and special icon title
              $html .= sprintf('<a class="%1$s" href="%2$s" title="%3$s" %4$s %5$s>%6$s</a>',
                  apply_filters( 'czr_social_link_class',
                                sprintf('social-icon icon-%1$s' ,
                                  ( $key == 'tc_rss' ) ? 'feed' : str_replace('tc_', '', $key)
                                ),
                                $key
                  ),
                  $link,
                  isset($data['link_title']) ?  call_user_func( '__' , $data['link_title'] , 'customizr' ) : '' ,
                  ( in_array( $key, array('tc_rss', 'tc_email') ) ) ? '' : apply_filters( 'czr_socials_target', 'target=_blank', $key ),
                  apply_filters( 'czr_additional_social_attributes', '' , $key),
                  ( isset($data['custom_icon_url']) && !empty($data['custom_icon_url']) ) ? sprintf('<img src="%1$s" width="%2$s" height="%3$s" alt="%4$s"/>',
                                                          $data['custom_icon_url'],
                                                          $width,
                                                          $height,
                                                          isset($data['link_title']) ? call_user_func( '__' , $data['link_title'] , 'customizr' ) : ''
                                                        ) : ''
              );
          }
        }
        return $html;
      }




    /**
    * Retrieve the file type from the file name
    * Even when it's not at the end of the file
    * copy of wp_check_filetype() in wp-includes/functions.php
    *
    * @since 3.2.3
    *
    * @param string $filename File name or path.
    * @param array  $mimes    Optional. Key is the file extension with value as the mime type.
    * @return array Values with extension first and mime type.
    */
    function czr_fn_check_filetype( $filename, $mimes = null ) {
      $filename = basename( $filename );
      if ( empty($mimes) )
        $mimes = get_allowed_mime_types();
      $type = false;
      $ext = false;
      foreach ( $mimes as $ext_preg => $mime_match ) {
        $ext_preg = '!\.(' . $ext_preg . ')!i';
        //was ext_preg = '!\.(' . $ext_preg . ')$!i';
        if ( preg_match( $ext_preg, $filename, $ext_matches ) ) {
          $type = $mime_match;
          $ext = $ext_matches[1];
          break;
        }
      }

      return compact( 'ext', 'type' );
    }

    /**
    * Check whether a category exists.
    * (wp category_exists isn't available in pre_get_posts)
    * @since 3.4.10
    *
    * @see term_exists()
    *
    * @param int $cat_id.
    * @return bool
    */
    public function czr_fn_category_id_exists( $cat_id ) {
      return term_exists( (int) $cat_id, 'category');
    }



    /**
    * @return a date diff object
    * @uses  date_diff if php version >=5.3.0, instantiates a fallback class if not
    *
    * @since 3.2.8
    *
    * @param date one object.
    * @param date two object.
    */
    private function czr_fn_date_diff( $_date_one , $_date_two ) {
      //if version is at least 5.3.0, use date_diff function
      if ( version_compare( PHP_VERSION, '5.3.0' ) >= 0) {
        return date_diff( $_date_one , $_date_two );
      } else {
        $_date_one_timestamp   = $_date_one->format("U");
        $_date_two_timestamp   = $_date_two->format("U");
        return new CZR_cl_DateInterval( $_date_two_timestamp - $_date_one_timestamp );
      }
    }



    /**
    * Return boolean OR number of days since last update OR PHP version < 5.2
    *
    * @package Customizr
    * @since Customizr 3.2.6
    */
    function czr_fn_post_has_update( $_bool = false) {
      //php version check for DateTime
      //http://php.net/manual/fr/class.datetime.php
      if ( version_compare( PHP_VERSION, '5.2.0' ) < 0 )
        return false;

      //first proceed to a date check
      $dates_to_check = array(
        'created'   => get_the_date('Y-m-d g:i:s'),
        'updated'   => get_the_modified_date('Y-m-d g:i:s'),
        'current'   => date('Y-m-d g:i:s')
      );
      //ALL dates must be valid
      if ( 1 != array_product( array_map( array($this , 'czr_fn_is_date_valid') , $dates_to_check ) ) )
        return false;

      //Import variables into the current symbol table
      extract($dates_to_check);

      //Instantiate the different date objects
      $created                = new DateTime( $created );
      $updated                = new DateTime( $updated );
      $current                = new DateTime( $current );

      $created_to_updated     = $this -> czr_fn_date_diff( $created , $updated );
      $updated_to_today       = $this -> czr_fn_date_diff( $updated, $current );

      if ( true === $_bool )
        //return ( 0 == $created_to_updated -> days && 0 == $created_to_updated -> s ) ? false : true;
        return ( $created_to_updated -> s > 0 || $created_to_updated -> i > 0 ) ? true : false;
      else
        //return ( 0 == $created_to_updated -> days && 0 == $created_to_updated -> s ) ? false : $updated_to_today -> days;
        return ( $created_to_updated -> s > 0 || $created_to_updated -> i > 0 ) ? $updated_to_today -> days : false;
    }



    /*
    * @return boolean
    * http://stackoverflow.com/questions/11343403/php-exception-handling-on-datetime-object
    */
    private function czr_fn_is_date_valid($str) {
      if ( ! is_string($str) )
         return false;

      $stamp = strtotime($str);
      if ( ! is_numeric($stamp) )
         return false;

      if ( checkdate(date('m', $stamp), date('d', $stamp), date('Y', $stamp)) )
         return true;

      return false;
    }



    /**
    * @return an array of font name / code OR a string of the font css code
    * @parameter string name or google compliant suffix for href link
    *
    * @package Customizr
    * @since Customizr 3.2.9
    */
    function czr_fn_get_font( $_what = 'list' , $_requested = null ) {
      $_to_return = ( 'list' == $_what ) ? array() : false;
      $_font_groups = apply_filters(
        'tc_font_pairs',
        CZR_cl_init::$instance -> font_pairs
      );
      foreach ( $_font_groups as $_group_slug => $_font_list ) {
        if ( 'list' == $_what ) {
          $_to_return[$_group_slug] = array();
          $_to_return[$_group_slug]['list'] = array();
          $_to_return[$_group_slug]['name'] = $_font_list['name'];
        }

        foreach ( $_font_list['list'] as $slug => $data ) {
          switch ($_requested) {
            case 'name':
              if ( 'list' == $_what )
                $_to_return[$_group_slug]['list'][$slug] =  $data[0];
            break;

            case 'code':
              if ( 'list' == $_what )
                $_to_return[$_group_slug]['list'][$slug] =  $data[1];
            break;

            default:
              if ( 'list' == $_what )
                $_to_return[$_group_slug]['list'][$slug] = $data;
              else if ( $slug == $_requested ) {
                  return $data[1];
              }
            break;
          }
        }
      }
      return $_to_return;
    }



    /**
    * Returns a boolean
    * check if user started to use the theme before ( strictly < ) the requested version
    *
    * @package Customizr
    * @since Customizr 3.2.9
    */
    function czr_fn_user_started_before_version( $_czr_ver, $_pro_ver = null ) {
      $_ispro = CZR___::czr_fn_is_pro();

      if ( $_ispro && ! get_transient( 'started_using_customizr_pro' ) )
        return false;

      if ( ! $_ispro && ! get_transient( 'started_using_customizr' ) )
        return false;

      $_trans = $_ispro ? 'started_using_customizr_pro' : 'started_using_customizr';
      $_ver   = $_ispro ? $_pro_ver : $_czr_ver;
      if ( ! $_ver )
        return false;

      $_start_version_infos = explode('|', esc_attr( get_transient( $_trans ) ) );

      if ( ! is_array( $_start_version_infos ) )
        return false;

      switch ( $_start_version_infos[0] ) {
        //in this case with now exactly what was the starting version (most common case)
        case 'with':
          return version_compare( $_start_version_infos[1] , $_ver, '<' );
        break;
        //here the user started to use the theme before, we don't know when.
        //but this was actually before this check was created
        case 'before':
          return true;
        break;

        default :
          return false;
        break;
      }
    }


    /**
    * Boolean helper to check if the secondary menu is enabled
    * since v3.4+
    */
    function czr_fn_is_secondary_menu_enabled() {
      return (bool) esc_attr( czr_fn_get_opt( 'tc_display_second_menu' ) ) && 'aside' == esc_attr( czr_fn_get_opt( 'tc_menu_style' ) );
    }



    /***************************
    * CTX COMPAT
    ****************************/
    /**
    * Helper : define a set of options not impacted by ctx like tc_slider, last_update_notice.
    * @return  array of excluded option names
    */
    function czr_fn_get_ctx_excluded_options() {
      return apply_filters(
        'czr_fn_get_ctx_excluded_options',
        array(
          'defaults',
          'tc_sliders',
          'tc_blog_restrict_by_cat',
          'last_update_notice',
          'last_update_notice_pro'
        )
      );
    }


    /**
    * Boolean helper : tells if this option is excluded from the ctx treatments.
    * @return bool
    */
    function czr_fn_is_option_excluded_from_ctx( $opt_name ) {
      return in_array( $opt_name, $this -> czr_fn_get_ctx_excluded_options() );
    }


    /**
    * Returns the url of the customizer with the current url arguments + an optional customizer section args
    *
    * @param $autofocus(optional) is an array indicating the elements to focus on ( control,section,panel).
    * Ex : array( 'control' => 'tc_front_slider', 'section' => 'frontpage_sec').
    * Wordpress will cycle among autofocus keys focusing the existing element - See wp-admin/customize.php.
    * The actual focused element depends on its type according to this priority scale: control, section, panel.
    * In this sense when specifying a control, additional section and panel could be considered as fall-back.
    *
    * @param $control_wrapper(optional) is a string indicating the wrapper to apply to the passed control. By default is "tc_theme_options".
    * Ex: passing $aufocus = array('control' => 'tc_front_slider') will produce the query arg 'autofocus'=>array('control' => 'tc_theme_options[tc_front_slider]'
    *
    * @return url string
    * @since Customizr 3.4+
    */
    static function czr_fn_get_customizer_url( $autofocus = null, $control_wrapper = 'tc_theme_options' ) {
      $_current_url       = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
      $_customize_url     = add_query_arg( 'url', urlencode( $_current_url ), wp_customize_url() );
      $autofocus  = ( ! is_array($autofocus) || empty($autofocus) ) ? null : $autofocus;

      if ( is_null($autofocus) )
        return $_customize_url;

      // $autofocus must contain at least one key among (control,section,panel)
      if ( ! count( array_intersect( array_keys($autofocus), array( 'control', 'section', 'panel') ) ) )
        return $_customize_url;

      // wrap the control in the $control_wrapper if neded
      if ( array_key_exists( 'control', $autofocus ) && ! empty( $autofocus['control'] ) && $control_wrapper ){
        $autofocus['control'] = $control_wrapper . '[' . $autofocus['control'] . ']';
      }
      // We don't really have to care for not existent autofocus keys, wordpress will stash them when passing the values to the customize js
      return add_query_arg( array( 'autofocus' => $autofocus ), $_customize_url );
    }


    /**
    * Is there a menu assigned to a given location ?
    * Used in class-header-menu and class-fire-placeholders
    * @return bool
    * @since  v3.4+
    */
    function czr_fn_has_location_menu( $_location ) {
      $_all_locations  = get_nav_menu_locations();
      return isset($_all_locations[$_location]) && is_object( wp_get_nav_menu_object( $_all_locations[$_location] ) );
    }


  }//end of class
endif;


//Helper class to build a simple date diff object
//Alternative to date_diff for php version < 5.3.0
//http://stackoverflow.com/questions/9373718/php-5-3-date-diff-equivalent-for-php-5-2-on-own-function
if ( ! class_exists( 'CZR_cl_DateInterval' ) ) :
Class CZR_cl_DateInterval {
    /* Properties */
    public $y = 0;
    public $m = 0;
    public $d = 0;
    public $h = 0;
    public $i = 0;
    public $s = 0;

    /* Methods */
    public function __construct ( $time_to_convert ) {
      $FULL_YEAR = 60*60*24*365.25;
      $FULL_MONTH = 60*60*24*(365.25/12);
      $FULL_DAY = 60*60*24;
      $FULL_HOUR = 60*60;
      $FULL_MINUTE = 60;
      $FULL_SECOND = 1;

      //$time_to_convert = 176559;
      $seconds = 0;
      $minutes = 0;
      $hours = 0;
      $days = 0;
      $months = 0;
      $years = 0;

      while($time_to_convert >= $FULL_YEAR) {
          $years ++;
          $time_to_convert = $time_to_convert - $FULL_YEAR;
      }

      while($time_to_convert >= $FULL_MONTH) {
          $months ++;
          $time_to_convert = $time_to_convert - $FULL_MONTH;
      }

      while($time_to_convert >= $FULL_DAY) {
          $days ++;
          $time_to_convert = $time_to_convert - $FULL_DAY;
      }

      while($time_to_convert >= $FULL_HOUR) {
          $hours++;
          $time_to_convert = $time_to_convert - $FULL_HOUR;
      }

      while($time_to_convert >= $FULL_MINUTE) {
          $minutes++;
          $time_to_convert = $time_to_convert - $FULL_MINUTE;
      }

      $seconds = $time_to_convert; // remaining seconds
      $this->y = $years;
      $this->m = $months;
      $this->d = $days;
      $this->h = $hours;
      $this->i = $minutes;
      $this->s = $seconds;
      $this->days = ( 0 == $years ) ? $days : ( $years * 365 + $months * 30 + $days );
    }
}
endif;

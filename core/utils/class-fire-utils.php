<?php
/**
* Defines filters and actions used in several templates/classes
*
*/
/**
* hook : after_setup_theme
* @package Customizr
* @since Customizr 3.3.0
*/
function czr_fn_wp_filters() {
    //add_filter( 'the_content'     , 'czr_fn_fancybox_content_filter'  );
    if ( esc_attr( czr_fn_get_opt( 'tc_img_smart_load' ) ) ) {
        add_filter( 'the_content'   , 'czr_fn_parse_imgs' , PHP_INT_MAX );
        add_filter( 'czr_thumb_html' , 'czr_fn_parse_imgs'  );
    }
    add_filter( 'wp_title'        , 'czr_fn_wp_title' , 10, 2 );
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

    return preg_replace_callback('#<img([^>]+?)src=[\'"]?([^\'"\s>]+)[\'"]?([^>]*)>#', 'czr_fn_regex_callback' , $_html);
}


/**
* callback of preg_replace_callback in czr_fn_parse_imgs
* Inspired from Unveil Lazy Load plugin : https://wordpress.org/plugins/unveil-lazy-load/ by @marubon
*
* @return string
* @package Customizr
* @since Customizr 3.3.0
*/
function czr_fn_regex_callback( $matches ) {
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
function czr_fn_get_skin_color( $_what = null ) {
    $_color_map    = CZR_init::$instance -> skin_color_map;
    $_color_map    = ( is_array($_color_map) ) ? $_color_map : array();

    $_active_skin =  str_replace('.min.', '.', basename( CZR_init::$instance -> czr_fn_get_style_src() ) );
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
function czr_fn_get_id()  {
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
* This function returns the filtered global layout defined in CZR_init
*
* @package Customizr
* @since Customizr 4.0
*/
function czr_fn_get_global_layout() {
  return apply_filters( 'czr_global_layout' , CZR_init::$instance -> global_layout );
}

/**
* This function returns the CSS class to apply to content's element based on the layout
* @return array
*
*
* @package Customizr
* @since Customizr 4.0
*/
function czr_fn_get_in_content_width_class() {
  $global_sidebar_layout                 = czr_fn_get_layout( czr_fn_get_id() , 'sidebar' );

  switch ( $global_sidebar_layout ) {
    case 'b': $_class = 'narrow';
              break;
    case 'f': $_class = 'full';
              break;
    default : $_class = 'semi-narrow';
  }

  return apply_filters( 'czr_in_content_width_class' , array( $_class ) );
}

/**
* This function returns the layout (sidebar(s), or full width) to apply to a context
*
* @package Customizr
* @since Customizr 1.0
*/
function czr_fn_get_layout( $post_id , $sidebar_or_class = 'class' ) {
      global $post;
      //Article wrapper class definition
      $global_layout                 = czr_fn_get_global_layout();

      /* Force 404 layout */
      //checks if the 'force default layout' option is checked and return the default layout before any specific layout
      if ( is_404() ) {
        $czr_screen_layout = array(
          'sidebar' => false,
          'class'   => 'col-xs-12 col-md-8 push-md-2'
        );
        return apply_filters( 'czr_screen_layout' , $czr_screen_layout[$sidebar_or_class], $post_id , $sidebar_or_class );
      }

      /* DEFAULT LAYOUTS */
      //what is the default layout we want to apply? By default we apply the global default layout
      $czr_sidebar_default_layout    = esc_attr( czr_fn_get_opt('tc_sidebar_global_layout') );
      $czr_sidebar_force_layout      = esc_attr( czr_fn_get_opt('tc_sidebar_force_layout') );

      //checks if the 'force default layout' option is checked and return the default layout before any specific layout
      if( $czr_sidebar_force_layout ) {
        $class_tab  = $global_layout[$czr_sidebar_default_layout];
        $class_tab  = $class_tab['content'];
        $czr_screen_layout = array(
          'sidebar' => $czr_sidebar_default_layout,
          'class'   => $class_tab
        );
        return apply_filters( 'czr_screen_layout' , $czr_screen_layout[$sidebar_or_class], $post_id , $sidebar_or_class );
      }


      if ( is_single() )
        $czr_sidebar_default_layout  = esc_attr( czr_fn_get_opt('tc_sidebar_post_layout') );
      if ( is_page() )
        $czr_sidebar_default_layout  = esc_attr( czr_fn_get_opt('tc_sidebar_page_layout') );

      //builds the default layout option array including layout and article class
      $class_tab  = $global_layout[$czr_sidebar_default_layout];
      $class_tab  = $class_tab['content'];
      $czr_screen_layout             = array(
                  'sidebar' => $czr_sidebar_default_layout,
                  'class'   => $class_tab
      );

      //The following lines set the post specific layout if any, and if not keeps the default layout previously defined
      $czr_specific_post_layout    = false;
      global $wp_query;
      //if we are displaying an attachement, we use the parent post/page layout
      if ( $post && 'attachment' == $post -> post_type ) {
        $czr_specific_post_layout  = esc_attr( get_post_meta( $post->post_parent , $key = 'layout_key' , $single = true ) );
      }
      //for a singular post or page OR for the posts page
      elseif ( is_singular() || $wp_query -> is_posts_page ) {
        $czr_specific_post_layout  = esc_attr( get_post_meta( $post_id, $key = 'layout_key' , $single = true ) );
      }

      //checks if we display home page, either posts or static page and apply the customizer option
      if( (is_home() && 'posts' == get_option( 'show_on_front' ) ) || is_front_page()) {
         $czr_specific_post_layout = czr_fn_get_opt('tc_front_layout');
      }

      if( $czr_specific_post_layout ) {
          $class_tab  = $global_layout[$czr_specific_post_layout];
          $class_tab  = $class_tab['content'];
          $czr_screen_layout = array(
            'sidebar' => $czr_specific_post_layout,
            'class'   => $class_tab
        );
      }

      return apply_filters( 'czr_screen_layout' , $czr_screen_layout[$sidebar_or_class], $post_id , $sidebar_or_class );
}


/**
* This function returns the column content wrapper class
*
* @package Customizr
* @since Customizr 3.5
*/
function czr_fn_get_column_content_wrapper_class() {
    return apply_filters( 'czr_column_content_wrapper_classes' , array('row', 'column-content-wrapper') );
}

/**
* This function returns the main container class
*
* @package Customizr
* @since Customizr 3.5
*/
function czr_fn_get_main_container_class() {
    return apply_filters( 'czr_main_container_classes' , array('container') );
}

/**
* This function returns the article container class
*
* @package Customizr
* @since Customizr 3.5
*/
function czr_fn_get_article_container_class() {
    return apply_filters( 'czr_article_container_class' , array( czr_fn_get_layout( czr_fn_get_id() , 'class' ) , 'article-container' ) );
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
  if ( $site_description && czr_fn_is_home() )
    $title = "$title $sep $site_description";

  // Add a page number if necessary.
  if ( $paged >= 2 || $page >= 2 )
    $title = "$title $sep " . sprintf( __( 'Page %s' , 'customizr' ), max( $paged, $page ) );

  return $title;
}









/**
* Gets the social networks list defined in customizer options
*
*
* @package Customizr
* @since Customizr 3.0.10
*
* @since Customizr 3.4.55 Added the ability to retrieve them as array
* @param $output_type optional. Return type "string" or "array"
*/
function czr_fn_get_social_networks( $output_type = 'string' ) {

    $_socials = czr_fn_get_opt('tc_social_links');

    if ( empty( $_socials ) )
      return;

    $_social_links = array();
    foreach( $_socials as $key => $item ) {
      array_push( $_social_links, sprintf('<a rel="nofollow" class="social-icon" %1$s title="%2$s" href="%3$s" %4$s style="color:%5$s"><i class="fa %6$s"></i></a>',
      //do we have an id set ?
      //Typically not if the user still uses the old options value.
      //So, if the id is not present, let's build it base on the key, like when added to the collection in the customizer

      // Put them together
        ! czr_fn_is_customizing() ? '' : sprintf( 'data-model-id="%1$s"', ! isset( $item['id'] ) ? 'hu_socials_'. $key : $item['id'] ),
        isset($item['title']) ? esc_attr( $item['title'] ) : '',
        ( isset($item['social-link']) && ! empty( $item['social-link'] ) ) ? esc_url( $item['social-link'] ) : 'javascript:void(0)',
        ( isset($item['social-target']) && false != $item['social-target'] ) ? 'target="_blank"' : '',
        isset($item['social-color']) ? esc_attr($item['social-color']) : '#000',
        isset($item['social-icon']) ? esc_attr($item['social-icon']) : ''
      ) );
    }

    /*
    * return
    */
    switch ( $output_type ) :
      case 'array' : return $_social_links;
      default      : return implode( '', $_social_links );
    endswitch;
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
function czr_fn_category_id_exists( $cat_id ) {
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
function czr_fn_date_diff( $_date_one , $_date_two ) {
  //if version is at least 5.3.0, use date_diff function
  if ( version_compare( PHP_VERSION, '5.3.0' ) >= 0) {
    return date_diff( $_date_one , $_date_two );
  } else {
    $_date_one_timestamp   = $_date_one->format("U");
    $_date_two_timestamp   = $_date_two->format("U");
    return new CZR_DateInterval( $_date_two_timestamp - $_date_one_timestamp );
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
    if ( 1 != array_product( array_map( 'czr_fn_is_date_valid' , $dates_to_check ) ) )
      return false;

    //Import variables into the current symbol table
    extract($dates_to_check);

    //Instantiate the different date objects
    $created                = new DateTime( $created );
    $updated                = new DateTime( $updated );
    $current                = new DateTime( $current );

    $created_to_updated     = czr_fn_date_diff( $created , $updated );
    $updated_to_today       = czr_fn_date_diff( $updated, $current );

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
function czr_fn_is_date_valid($str) {
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
      CZR_init::$instance -> font_pairs
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
    $_ispro = CZR_IS_PRO;

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
    return (bool) esc_attr( czr_fn_get_opt( 'tc_display_second_menu' ) ) /* && 'aside' == esc_attr( czr_fn_get_opt( 'tc_menu_style' ) )*/;
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
function czr_fn_get_customizer_url( $autofocus = null, $control_wrapper = 'tc_theme_options' ) {
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


//hook : czr_dev_notice
function czr_fn_print_r($message) {
    if ( ! is_user_logged_in() || ! current_user_can( 'edit_theme_options' ) || is_feed() )
      return;
    ?>
      <pre><h6 style="color:red"><?php echo $message ?></h6></pre>
    <?php
}




/* FMK MODEL / VIEW / COLLECTION HELPERS */
function czr_fn_stringify_array( $array, $sep = ' ' ) {
    if ( is_array( $array ) )
      $array = join( $sep, array_unique( array_filter( $array ) ) );
    return $array;
}


//A callback helper
//a callback can be function or a method of a class
//the class can be an instance!
function czr_fn_fire_cb( $cb, $params = array(), $return = false ) {
    $to_return = false;
    //method of a class => look for an array( 'class_name', 'method_name')
    if ( is_array($cb) && 2 == count($cb) ) {
      if ( is_object($cb[0]) ) {
        $to_return = call_user_func( array( $cb[0] ,  $cb[1] ), $params );
      }
      //instantiated with an instance property holding the object ?
      else if ( class_exists($cb[0]) && isset($cb[0]::$instance) && method_exists($cb[0]::$instance, $cb[1]) ) {
        $to_return = call_user_func( array( $cb[0]::$instance ,  $cb[1] ), $params );
      }
      else {
        $_class_obj = new $cb[0]();
        if ( method_exists($_class_obj, $cb[1]) )
          $to_return = call_user_func( array( $_class_obj, $cb[1] ), $params );
      }
    } else if ( is_string($cb) && function_exists($cb) ) {
      $to_return = call_user_func($cb, $params);
    }

    if ( $return )
      return $to_return;
}


function czr_fn_return_cb_result( $cb, $params = array() ) {
    return czr_fn_fire_cb( $cb, $params, $return = true );
}




/* Same as helpers above but passing the param argument as an exploded array of params*/
//A callback helper
//a callback can be function or a method of a class
//the class can be an instance!
function czr_fn_fire_cb_array( $cb, $params = array(), $return = false ) {
    $to_return = false;
    //method of a class => look for an array( 'class_name', 'method_name')
    if ( is_array($cb) && 2 == count($cb) ) {
      if ( is_object($cb[0]) ) {
        $to_return = call_user_func_array( array( $cb[0] ,  $cb[1] ), $params );
      }
      //instantiated with an instance property holding the object ?
      else if ( class_exists($cb[0]) && isset($cb[0]::$instance) && method_exists($cb[0]::$instance, $cb[1]) ) {
        $to_return = call_user_func_array( array( $cb[0]::$instance ,  $cb[1] ), $params );
      }
      else {
        $_class_obj = new $cb[0]();
        if ( method_exists($_class_obj, $cb[1]) )
          $to_return = call_user_func_array( array( $_class_obj, $cb[1] ), $params );
      }
    } else if ( is_string($cb) && function_exists($cb) ) {
      $to_return = call_user_func_array($cb, $params);
    }

    if ( $return )
      return $to_return;
}

function czr_fn_return_cb_result_array( $cb, $params = array() ) {
    return czr_fn_fire_cb_array( $cb, $params, $return = true );
}




/**
* helper
* returns the actual page id if we are displaying the posts page
* @return  boolean
*
*/
function czr_fn_is_slider_active( $queried_id = null ) {
  $queried_id = $queried_id ? $queried_id : czr_fn_get_real_id();
  //is the slider set to on for the queried id?
  if ( czr_fn_is_home() && czr_fn_get_opt( 'tc_front_slider' ) )
    return apply_filters( 'czr_slider_active_status', true , $queried_id );
  $_slider_on = esc_attr( get_post_meta( $queried_id, $key = 'post_slider_check_key' , $single = true ) );
  if ( ! empty( $_slider_on ) && $_slider_on )
    return apply_filters( 'czr_slider_active_status', true , $queried_id );
  return apply_filters( 'czr_slider_active_status', false , $queried_id );
}

/**
* helper
* returns the slider name id
* @return  string
*
*/
function czr_fn_get_current_slider( $queried_id = null ) {
  $queried_id = $queried_id ? $queried_id : czr_fn_get_real_id();
  //gets the current slider id
  $_home_slider     = czr_fn_get_opt( 'tc_front_slider' );
  $slider_name_id   = ( czr_fn_is_home() && $_home_slider ) ? $_home_slider : esc_attr( get_post_meta( $queried_id, $key = 'post_slider_key' , $single = true ) );
  return apply_filters( 'czr_slider_name_id', $slider_name_id , $queried_id );
}


function czr_fn_post_has_title() {
    return ! in_array(
      get_post_format(),
      apply_filters( 'czr_post_formats_with_no_heading', array( 'aside' , 'status' , 'link' , 'quote' ) )
    );
}

/* TODO: caching system */
function czr_fn_get_logo_atts( $logo_type = '', $backward_compatibility = true ) {
    $logo_type_sep      = $logo_type ? '_sticky_' : '_';
    $accepted_formats   = apply_filters( 'czr_logo_img_formats' , array('jpg', 'jpeg', 'png' ,'gif', 'svg', 'svgz' ) );

    //check if the logo is a path or is numeric
    //get src for both cases
    $_logo_src          = '';
    $_width             = false;
    $_height            = false;
    $_attachment_id     = false;
    $_logo_option       = esc_attr( czr_fn_get_opt( "tc{$logo_type_sep}logo_upload") );
    //check if option is an attachement id or a path (for backward compatibility)
    if ( is_numeric($_logo_option) ) {
      $_attachment_id   = $_logo_option;
      $_attachment_data = apply_filters( "tc{$logo_type_sep}logo_attachment_img" , wp_get_attachment_image_src( $_logo_option , 'full' ) );
      $_logo_src        = $_attachment_data[0];
      $_width           = ( isset($_attachment_data[1]) && $_attachment_data[1] > 1 ) ? $_attachment_data[1] : $_width;
      $_height          = ( isset($_attachment_data[2]) && $_attachment_data[2] > 1 ) ? $_attachment_data[2] : $_height;
    } elseif ( $backward_compatibility ) { //old treatment
      //rebuild the logo path : check if the full path is already saved in DB. If not, then rebuild it.
      $upload_dir       = wp_upload_dir();
      $_saved_path      = esc_url ( czr_fn_get_opt( "tc{$logo_type_sep}logo_upload") );
      $_logo_src        = ( false !== strpos( $_saved_path , '/wp-content/' ) ) ? $_saved_path : $upload_dir['baseurl'] . $_saved_path;
    }
    //hook + makes ssl compliant
    $_logo_src          = apply_filters( "tc{$logo_type_sep}logo_src" , is_ssl() ? str_replace('http://', 'https://', $_logo_src) : $_logo_src ) ;
    $filetype           = czr_fn_check_filetype ($_logo_src);

    if( ! empty($_logo_src) && in_array( $filetype['ext'], $accepted_formats ) )
      return array(
                'logo_src'           => $_logo_src,
                'logo_attachment_id' => $_attachment_id,
                'logo_width'         => $_width,
                'logo_height'        => $_height,
                'logo_type'          => trim($logo_type_sep,'_')
      );

    return array();
}
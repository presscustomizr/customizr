<?php
/**
* Posts thumbnails functions
*/
/**********************
* THUMBNAIL MODELS
**********************/
/**
* Gets the thumbnail or the first images attached to the post if any
* inside loop
* @return array( $tc_thumb(image object), $tc_thumb_width(string), $tc_thumb_height(string) )
*
* @package Customizr
* @since Customizr 1.0
*/
function czr_fn_get_thumbnail_model( $args = array() ) {
    $defaults = array(
      'requested_size'            => null,
      'post_id'                   => null,
      'custom_thumb_id'           => null,
      'enable_wp_responsive_imgs' => true,
      'filtered_thumb_size_name'  => null,
      'placeholder'               => false
    );

    $args = wp_parse_args( $args, $defaults);
    extract( $args );

    $post_id = is_null($post_id) ? get_the_ID() : $post_id;

    $cached_index = empty( $post_id ) ? 'post_id__no_post_id' : 'post_id_'.$post_id;
    if ( array_key_exists($cached_index, CZR_BASE::$cached_thumbnail_models) ) {
        return CZR_BASE::$cached_thumbnail_models[$cached_index];
    }

    CZR_BASE::$cached_thumbnail_models[$cached_index] = array();

    //czr_fn_has_thumb() checks if there is a thumbnail or an attachment img ( typically img embedded in single post ) that we can use
    //=> the check on the attachement is done if true == czr_fn_opt( 'tc_post_list_use_attachment_as_thumb' )
    if ( !czr_fn_has_thumb( $post_id, $custom_thumb_id ) ) {
      if ( !$placeholder ) {
        return array();
      } else {
        CZR_BASE::$cached_thumbnail_models[$cached_index] = array( 'tc_thumb' => czr_fn_get_placeholder_thumb(), 'is_placeholder' => true );
        return CZR_BASE::$cached_thumbnail_models[$cached_index];
      }
    }

    $tc_thumb_size              = is_null($requested_size) ? apply_filters( 'czr_thumb_size_name' , 'tc-thumb' ) : $requested_size;

    $filtered_thumb_size_name   = ! is_null( $filtered_thumb_size_name ) ? $filtered_thumb_size_name : 'tc_thumb_size';
    $_filtered_thumb_size       = apply_filters( $filtered_thumb_size_name, $filtered_thumb_size_name ? CZR___::$instance -> $filtered_thumb_size_name : null );

    $_model                     = array();
    $_img_attr                  = array();
    $tc_thumb_height            = '';
    $tc_thumb_width             = '';

    //when null set it as the image setting for reponsive thumbnails (default)
    //because this method is also called from the slider of posts which refers to the slider responsive image setting
    //limit this just for wp version >= 4.4
    if ( version_compare( $GLOBALS['wp_version'], '4.4', '>=' ) )
      $enable_wp_responsive_imgs = is_null( $enable_wp_responsive_imgs ) ? 1 == czr_fn_opt('tc_resp_thumbs_img') : $enable_wp_responsive_imgs;

    //try to extract $_thumb_id and $_thumb_type
    extract( czr_fn_maybe_set_and_get_thumb_info( $post_id, $custom_thumb_id ) );
    if ( ! apply_filters( 'tc_has_thumb_info', isset($_thumb_id) && false != $_thumb_id && ! is_null($_thumb_id) ) )
      return array();

    //Try to get the image
    $image                      = wp_get_attachment_image_src( $_thumb_id, $tc_thumb_size);
    if ( ! apply_filters('tc_has_wp_thumb_image', ! empty( $image[0] ) ) )
        return array();

    //check also if this array value isset. (=> JetPack photon bug)
    if ( isset($image[3]) && false == $image[3] && 'tc-thumb' == $tc_thumb_size )
      $tc_thumb_size          = 'large';
    if ( isset($image[3]) && false == $image[3] && 'tc_rectangular_size' == $tc_thumb_size )
      $tc_thumb_size          = 'slider';

    $_img_attr['class']     = sprintf(
      'attachment-%1$s tc-thumb-type-%2$s czr-img',
      $tc_thumb_size ,
      $_thumb_type
    );
    //Add the style value
    $_style                 = apply_filters( 'czr_post_thumb_inline_style' , '', $image, $_filtered_thumb_size );
    if ( $_style )
      $_img_attr['style']   = $_style;
    $_img_attr              = apply_filters( 'czr_post_thumbnail_img_attributes' , $_img_attr );

    //we might not want responsive images
    if ( false === $enable_wp_responsive_imgs ) {
      //trick, will produce an empty attr srcset as in wp-includes/media.php the srcset is calculated and added
      //only when the passed srcset attr is not empty. This will avoid us to:
      //a) add a filter to get rid of already computed srcset
      // or
      //b) use preg_replace to get rid of srcset and sizes attributes from the generated html
      //Side effect:
      //we'll see an empty ( or " " depending on the browser ) srcset attribute in the html
      //to avoid this we filter the attributes getting rid of the srcset if any.
      //Basically this trick, even if ugly, will avoid the srcset attr computation
      $_img_attr['srcset']  = " ";
      add_filter( 'wp_get_attachment_image_attributes', 'czr_fn_remove_srcset_attr' );
    }
    //get the thumb html
    if ( is_null($custom_thumb_id) && has_post_thumbnail( $post_id ) ) {
      //get_the_post_thumbnail( $post_id, $size, $attr )
      $tc_thumb = get_the_post_thumbnail( $post_id , $tc_thumb_size , $_img_attr);
    }
    else {
      //wp_get_attachment_image( $attachment_id, $size, $icon, $attr )
      $tc_thumb = wp_get_attachment_image( $_thumb_id, $tc_thumb_size, false, $_img_attr );
    }

    //get height and width if not empty
    if ( ! empty($image[1]) && ! empty($image[2]) ) {
      $tc_thumb_height        = $image[2];
      $tc_thumb_width         = $image[1];
    }
    //used for smart load when enabled
    $tc_thumb = apply_filters( 'czr_thumb_html', $tc_thumb, $requested_size, $post_id, $custom_thumb_id, $_img_attr, $tc_thumb_size );
    CZR_BASE::$cached_thumbnail_models[$cached_index] = ( isset($tc_thumb) && ! empty($tc_thumb) && false != $tc_thumb ) ? compact( "tc_thumb" , "tc_thumb_height" , "tc_thumb_width", "_thumb_id" ) : array();

    return apply_filters( 'czr_get_thumbnail_model',
      CZR_BASE::$cached_thumbnail_models[$cached_index],
      $post_id,
      $_thumb_id,
      $enable_wp_responsive_imgs
    );
}



/**
* inside loop
* @return array( "_thumb_id" , "_thumb_type" )
*/
function czr_fn_maybe_set_and_get_thumb_info( $_post_id = null, $_thumb_id = null ) {
    $_post_id     = is_null($_post_id) ? get_the_ID() : $_post_id;
    $_meta_thumb  = get_post_meta( $_post_id , 'tc-thumb-fld', true );
    //get_post_meta( $post_id, $key, $single );
    //always refresh the thumb meta if user logged in and current_user_can('upload_files')
    //When do we refresh ?
    //1) empty( $_meta_thumb )
    //2) is_user_logged_in() && current_user_can('upload_files')
    $_refresh_bool = empty( $_meta_thumb ) || ! $_meta_thumb;
    $_refresh_bool = ! isset($_meta_thumb["_thumb_id"]) || ! isset($_meta_thumb["_thumb_type"]);
    $_refresh_bool = ( is_user_logged_in() && current_user_can('upload_files') ) ? true : $_refresh_bool;
    //if a custom $_thumb_id is requested => always refresh
    $_refresh_bool = ! is_null( $_thumb_id ) ? true : $_refresh_bool;

    if ( ! $_refresh_bool )
      return $_meta_thumb;

    return czr_fn_set_thumb_info( $_post_id , $_thumb_id, true );
}

/**************************
* EXPOSED HELPERS / SETTERS
**************************/
/*
* @return bool
*/
function czr_fn_has_thumb( $_post_id = null , $_thumb_id = null ) {
    $_post_id  = is_null($_post_id) ? get_the_ID() : $_post_id;

    //try to extract (OVERWRITE) $_thumb_id and $_thumb_type
    extract( czr_fn_maybe_set_and_get_thumb_info( $_post_id, $_thumb_id ) );
    return apply_filters( 'tc_has_thumb', wp_attachment_is_image($_thumb_id) && isset($_thumb_id) && false != $_thumb_id && ! empty($_thumb_id) );
}


/**
* update the thumb meta and maybe return the info
* also fired from admin on save_post
* @param post_id and (bool) return
* @return void or array( "_thumb_id" , "_thumb_type" )
*/
function czr_fn_set_thumb_info( $post_id = null , $_thumb_id = null, $_return = false ) {
    $post_id      = is_null($post_id) ? get_the_ID() : $post_id;
    $_thumb_type  = 'none';

    //IF a custom thumb id is requested
    if ( ! is_null( $_thumb_id ) && false !== $_thumb_id ) {
      $_thumb_type  = false !== $_thumb_id ? 'custom' : $_thumb_type;
    }
    //IF no custom thumb id :
    //1) check if has thumbnail
    //2) check attachements
    //3) default thumb
    else {
      if ( has_post_thumbnail( $post_id ) ) {
        $_thumb_id    = get_post_thumbnail_id( $post_id );
        $_thumb_type  = false !== $_thumb_id ? 'thumb' : $_thumb_type;
      } else {
        $_thumb_id    = czr_fn_get_id_from_attachment( $post_id );
        $_thumb_type  = false !== $_thumb_id ? 'attachment' : $_thumb_type;
      }
    }
    $_thumb_id = ( ! $_thumb_id || empty($_thumb_id) || ! is_numeric($_thumb_id) ) ? false : $_thumb_id;

    //update_post_meta($post_id, $meta_key, $meta_value, $prev_value);
    update_post_meta( $post_id , 'tc-thumb-fld', compact( "_thumb_id" , "_thumb_type" ) );
    if ( $_return )
      return apply_filters( 'czr_set_thumb_info' , compact( "_thumb_id" , "_thumb_type" ), $post_id );
}//end of fn


function czr_fn_get_id_from_attachment( $post_id ) {
    //define a filtrable boolean to set if attached images can be used as thumbnails
    //1) must be a non single post context
    //2) user option should be checked in customizer
    $_bool = czr_fn_is_checked('tc_post_list_use_attachment_as_thumb' );

    if ( ! is_admin() )
      $_bool = ! czr_fn_is_single_post() && $_bool;

    if ( ! apply_filters( 'czr_use_attachment_as_thumb' , $_bool ) )
      return;

    //Case if we display a post or a page
    if ( 'attachment' != get_post_type( $post_id ) ) {
      //look for the last attached image in a post or page
      $tc_args = apply_filters('czr_attachment_as_thumb_query_args' , array(
          'numberposts'             =>  1,
          'post_type'               =>  'attachment',
          'post_status'             =>  null,
          'post_parent'             =>  $post_id,
          'post_mime_type'          =>  array( 'image/jpeg' , 'image/gif' , 'image/jpg' , 'image/png' ),
          'orderby'                 => 'post_date',
          'order'                   => 'DESC'
        )
      );
      $attachments              = get_posts( $tc_args );
    }

    //case were we display an attachment (in search results for example)
    elseif ( 'attachment' == get_post_type( $post_id ) && wp_attachment_is_image( $post_id ) ) {
      $attachments = array( get_post( $post_id ) );
    }

    if ( ! isset($attachments) || empty($attachments ) )
      return;
    return isset( $attachments[0] ) && isset( $attachments[0] -> ID ) ? $attachments[0] -> ID : false;
}//end of fn



/**********************
* THUMBNAIL VIEW
**********************/
/**
* Display or return the thumbnail view
* @param : thumbnail model (img, width, height), layout value, echo bool
* @package Customizr
* @since Customizr 3.0.10
*/
function czr_fn_render_thumb_view( $_thumb_model , $layout = 'span3', $_echo = true ) {
    if ( empty( $_thumb_model ) )
      return;
    //extract "tc_thumb" , "tc_thumb_height" , "tc_thumb_width"
    extract( $_thumb_model );
    $thumb_img        = ! isset( $_thumb_model) ? false : $tc_thumb;
    $thumb_img        = apply_filters( 'czr_post_thumb_img', $thumb_img, czr_fn_get_id() );
    if ( ! $thumb_img )
      return;

    //handles the case when the image dimensions are too small
    $thumb_size       = apply_filters( 'czr_thumb_size' , CZR___::$instance -> tc_thumb_size, czr_fn_get_id()  );
    $no_effect_class  = ( isset($tc_thumb) && isset($tc_thumb_height) && ( $tc_thumb_height < $thumb_size['height']) ) ? 'no-effect' : '';
    $no_effect_class  = ( esc_attr( czr_fn_opt( 'tc_center_img') ) || ! isset($tc_thumb) || empty($tc_thumb_height) || empty($tc_thumb_width) ) ? '' : $no_effect_class;

    //default hover effect
    $thumb_wrapper    = sprintf('<div class="%4$s %1$s"><div class="round-div"></div><a class="round-div %1$s" href="%2$s"></a>%3$s</div>',
                                  implode( " ", apply_filters( 'czr_thumbnail_link_class', array( $no_effect_class ) ) ),
                                  get_permalink( get_the_ID() ),
                                  $thumb_img,
                                  implode( " ", apply_filters( 'czr_thumb_wrapper_class', array('thumb-wrapper') ) )
    );

    $thumb_wrapper    = apply_filters_ref_array( 'czr_post_thumb_wrapper', array( $thumb_wrapper, $thumb_img, czr_fn_get_id() ) );

    //cache the thumbnail view
    $html             = sprintf('<section class="tc-thumbnail %1$s">%2$s</section>',
      apply_filters( 'czr_post_thumb_class', $layout ),
      $thumb_wrapper
    );
    $html = apply_filters_ref_array( 'czr_render_thumb_view', array( $html, $_thumb_model, $layout ) );
    if ( ! $_echo )
      return $html;
    echo $html;
}//end of function



/* ------------------------------------------------------------------------- *
*  Placeholder thumbs for preview demo mode
/* ------------------------------------------------------------------------- */
/* Echoes the <img> tag of the placeholder thumbnail
*  + an animated svg icon
*  the src property can be filtered
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_get_placeholder_thumb' ) ) {
  function czr_fn_get_placeholder_thumb( $_requested_size = 'thumb-standard' ) {
    $_unique_id = uniqid();
    $filter = false;

    $_sizes = array( 'thumb-medium', 'thumb-small', 'thumb-standard' );
    if ( ! in_array($_requested_size, $_sizes) )
      $_requested_size = 'thumb-medium';

    //default $img_src
    $_img_src = czr_fn_get_theme_file_url( CZR_ASSETS_PREFIX . "front/img/{$_requested_size}.png" );
    if ( apply_filters( 'czr-use-svg-thumb-placeholder', true ) ) {
        $_size = $_requested_size . '-empty';
        $_img_src = czr_fn_get_theme_file_url( CZR_ASSETS_PREFIX . "front/img/{$_size}.png" );
        $_svg_height = in_array($_size, array( 'thumb-medium', 'thumb-standard' ) ) ? 100 : 60;
        ob_start();
        ?>
        <svg class="czr-svg-placeholder <?php echo $_size; ?>" id="<?php echo $_unique_id; ?>" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M928 832q0-14-9-23t-23-9q-66 0-113 47t-47 113q0 14 9 23t23 9 23-9 9-23q0-40 28-68t68-28q14 0 23-9t9-23zm224 130q0 106-75 181t-181 75-181-75-75-181 75-181 181-75 181 75 75 181zm-1024 574h1536v-128h-1536v128zm1152-574q0-159-112.5-271.5t-271.5-112.5-271.5 112.5-112.5 271.5 112.5 271.5 271.5 112.5 271.5-112.5 112.5-271.5zm-1024-642h384v-128h-384v128zm-128 192h1536v-256h-828l-64 128h-644v128zm1664-256v1280q0 53-37.5 90.5t-90.5 37.5h-1536q-53 0-90.5-37.5t-37.5-90.5v-1280q0-53 37.5-90.5t90.5-37.5h1536q53 0 90.5 37.5t37.5 90.5z"/></svg>
        <?php
        $_svg_placeholder = ob_get_clean();
    }
    $_img_src = apply_filters( 'czr_placeholder_thumb_src', $_img_src, $_requested_size );
    $filter = apply_filters( 'czr_placeholder_thumb_filter', false );
    //make sure we did not lose the img_src
    if ( false == $_img_src )
      $_img_src = czr_fn_get_theme_file_url( CZR_ASSETS_PREFIX . "front/img/{$_requested_size}.png" );

    $thumb_to_sizes = array(
      //thumb => width, height
      'thumb-medium' => array( '520', '245' ),
      'thumb-small' => array( '160', '160' ),
      'thumb-standard' => array( '300', '300' )
    );

    $attr = array(
      'class'             => 'czr-img-placeholder',
      'src'               => $_img_src,
      'alt'               => trim( strip_tags( get_the_title() ) ),
      'data-czr-post-id'  => $_unique_id,
      //$_requested_size is always forced to have a value present in this array $_sizes = array( 'thumb-medium', 'thumb-small', 'thumb-standard' );
      'width'             => $thumb_to_sizes[$_requested_size][0],
      'height'            => $thumb_to_sizes[$_requested_size][1]
    );

    $attr = apply_filters( 'czr_placeholder_image_attributes', $attr );
    $attr = array_filter( array_map( 'esc_attr', $attr ) );

    return sprintf( '%1$s%2$s<img class="%3$s" src="%4$s" alt="%5$s" data-czr-post-id="%6$s" width="%7$s" height="%8$s"/>',
      isset($_svg_placeholder) ? $_svg_placeholder : '',
      false !== $filter ? $filter : '',
      isset( $attr[ 'class' ] ) ? $attr[ 'class' ] : '',
      isset( $attr[ 'src' ] ) ? $attr[ 'src' ] : '',
      isset( $attr[ 'alt' ] ) ? $attr[ 'alt' ] : '',
      isset( $attr[ 'data-czr-post-id' ] ) ? $attr[ 'data-czr-post-id' ] : '',
      isset( $attr[ 'width' ] ) ? $attr[ 'width' ] : '',
      isset( $attr[ 'height' ] ) ? $attr[ 'height' ] : ''
    );
  }
}

/**********************
* HELPER CALLBACK
**********************/
/**
* hook wp_get_attachment_image_attributes
* Get rid of the srcset attribute (responsive images)
* @param $attr array of image attributes
* @return  array of image attributes
*
* @package Customizr
* @since Customizr 3.4.16
*/
function czr_fn_remove_srcset_attr( $attr ) {
    if ( isset( $attr[ 'srcset' ] ) ) {
      unset( $attr['srcset'] );
      //to ensure a "local" removal we have to remove this filter callback, so it won't hurt
      //responsive images sitewide
      remove_filter( current_filter(), 'czr_fn_remove_srcset_attr' );
    }
    return $attr;
}

?>
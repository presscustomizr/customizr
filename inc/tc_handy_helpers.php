<?php

if(!function_exists('tc_get_options')) :
 /**
 * Returns the options array for the theme.
 *
 * @package Customizr
 * @since Customizr 1.0
 */
  function tc_get_options($option_name) {
    
      global $tc_theme_options;
      $saved = (array) get_option( 'tc_theme_options' );

      $defaults = tc_get_default_options(); //located in admin/tc_customize.php

      //$defaults = apply_filters( 'tc_default_theme_options', $defaults );

      $options = wp_parse_args( $saved, $defaults );

      $options = array_intersect_key( $options, $defaults );
   
      return $options[$option_name];
  }
endif;




if(!function_exists('tc_get_the_ID')) :
  /**
  * This function is similiar to the wordpress function get_the_ID but takes into account the id of the page initially called
  * @package Customizr
  * @since Customizr 1.0
  */
  function tc_get_the_ID()
    {
        global $tc_theme_options;
        if (is_404() || is_search())
          return null;
        if (!isset($tc_theme_options['another_query_in_the_main_loop'])) 
        {
            $id = get_the_ID();
        }
        else 
        {
            $id = $tc_theme_options['original_ID'];
        }
        return $id;
    }
endif;




if(!function_exists('tc_get_current_screen_layout')) :
  /**
  *
  * @package Customizr
  * @since Customizr 1.0
  */
    function tc_get_current_screen_layout ($post_id) {
      global $tc_theme_options;
      
    //Article wrapper class definition
        $class_tab = array(
          'r' => 'span9',
          'l' => 'span9',
          'b' => 'span6',
          'f' => 'span12',
          );

      /* DEFAULT LAYOUTS */
      //get the global default layout
      $tc_sidebar_global_layout     = $tc_theme_options['tc_sidebar_global_layout'];
      //get the post default layout
      $tc_sidebar_post_layout       = $tc_theme_options['tc_sidebar_post_layout'];
      //get the page default layout
      $tc_sidebar_page_layout       = $tc_theme_options['tc_sidebar_page_layout'];

      //what is the default layout we want to apply? By default we apply the global default layout
      $tc_sidebar_default_layout    = $tc_sidebar_global_layout;
      if (is_single())
        $tc_sidebar_default_layout  = $tc_sidebar_post_layout;
      if (is_page())
        $tc_sidebar_default_layout  = $tc_sidebar_page_layout;

      //build the default layout option array including layout and article class
      $tc_screen_layout = array(
          'sidebar' => $tc_sidebar_default_layout,
          'class'   => $class_tab[$tc_sidebar_default_layout]
        );

      //finally we check if the 'force default layout' option is checked and return the default layout before any specific layout
      $force_layout = $tc_theme_options['tc_sidebar_force_layout'];
      if($force_layout == 1) {
        $tc_screen_layout = array(
          'sidebar' => $tc_sidebar_global_layout,
          'class'   => $class_tab[$tc_sidebar_global_layout]
        );
        return $tc_screen_layout;
      }

      //get the front page layout
      $tc_front_layout =  $tc_theme_options['tc_front_layout'];

      //get info whether the front page is a list of last posts or a page
      $tc_what_on_front  = get_option( 'show_on_front');


      //get the post specific layout if any, and if we don't apply the default layout
      $tc_specific_post_layout = esc_attr(get_post_meta( $post_id, $key = 'layout_key', $single = true ));
      
      if((is_home() && $tc_what_on_front == 'posts') || is_front_page())
         $tc_specific_post_layout = $tc_front_layout;

      if($tc_specific_post_layout) {
          $tc_screen_layout = array(
          'sidebar' => $tc_specific_post_layout,
          'class'   => $class_tab[$tc_specific_post_layout]
        );
      }
      return $tc_screen_layout;
    };
endif;



if(!function_exists('new_excerpt_more')) :
  /**
  *
  * @package Customizr
  * @since Customizr 1.0
  */
  function new_excerpt_more($more) {
         global $post;
    return ' <a href="'. get_permalink($post->ID) . '">'.__('Read more...','customizr').'</a>';
  }
  add_filter('excerpt_more', 'new_excerpt_more');
endif;



if(!function_exists('tc_post_thumbnail')) :
  /**
  *
  * @package Customizr
  * @since Customizr 1.0
  */
  function tc_post_thumbnail($thumb_class) {
    //handle the no search results and 404 error cases
    global $post;
    if(!$post)
      return false;
    
    //define the default thumb size
    $tc_thumb_size = 'tc-thumb';

    //define the default thumnail if has thumbnail
    if (has_post_thumbnail()) {
        $tc_thumb_id = get_post_thumbnail_id();

        //check if tc-thumb size exists for attachment and return large if not
        $image = wp_get_attachment_image_src( $tc_thumb_id, $tc_thumb_size);
        if (null == $image[3])
          $tc_thumb_size = 'medium';

        $tc_thumb = get_the_post_thumbnail( get_the_ID(),$tc_thumb_size);
        //get height and width
        $tc_thumb_height = $image[2];
        $tc_thumb_width = $image[1];
    }
      //check if there is a thumbnail and if not uses the first attached image
    else {
        //look for attachements
        $tc_args = array(
          'numberposts'     =>  1,
          'post_type'       =>  'attachment',
          'post_status'     =>  null,
          'post_parent'     =>  get_the_ID(),
          'post_mime_type'  =>  array('image/jpeg','image/gif','image/jpg','image/png')
          ); 
          $attachments = get_posts($tc_args);
          if ($attachments) {
            foreach ($attachments as $attachment) {
               //check if tc-thumb size exists for attachment and return large if not
              $image = wp_get_attachment_image_src( $attachment->ID, $tc_thumb_size);
              if (false == $image[3])
                $tc_thumb_size = 'medium';
              $tc_thumb = wp_get_attachment_image($attachment->ID, $tc_thumb_size);
              //get height and width
              $tc_thumb_height = $image[2];
              $tc_thumb_width = $image[1];
            }
          }
    }

    //handle the case when the image dimensions are too small
    $no_effect_class = '';
    if (isset($tc_thumb) && ($tc_thumb_width < 270)) {
      $no_effect_class = 'no-effect';
    }

    //render the thumbnail
    if(isset($tc_thumb) && !is_single()) {
          $html = '<div class="'.$thumb_class.'">';
             $html .= '<div class="thumb-wrapper">';
                $html .=  '<a class="round-div '.$no_effect_class.'" href="'.get_permalink( get_the_ID() ).'" title="'.get_the_title( get_the_ID()).'"></a>';
                //$html .= '<div class="round-div"></div>';
                  $html .= $tc_thumb;
            $html .= '</div>';
          $html .= '</div><!--.span4-->';
        return $html; 
    }
    else {
        return false;
    }
  }
endif;



/**
 * Cleaner walker for wp_nav_menu()
 *
 * Walker_Nav_Menu (WordPress default) example output:
 *   <li id="menu-item-8" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-8"><a href="/">Home</a></li>
 *   <li id="menu-item-9" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-9"><a href="/sample-page/">Sample Page</a></l
 *
 * Roots_Nav_Walker example output:
 *   <li class="menu-home"><a href="/">Home</a></li>
 *   <li class="menu-sample-page"><a href="/sample-page/">Sample Page</a></li>
 */

class TC_Nav_Walker extends Walker_Nav_Menu {
  function check_current($classes) {
    return preg_match('/(current[-_])|active|dropdown/', $classes);
  }

  function start_lvl(&$output, $depth = 0, $args = array()) {
    $output .= "\n<ul class=\"dropdown-menu\">\n";
  }

  function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
    $item_html = '';
    parent::start_el($item_html, $item, $depth, $args);

    if ($item->is_dropdown && ($depth === 0)) {
      $item_html = str_replace('<a', '<a class="dropdown-toggle" data-toggle="dropdown" data-target="#"', $item_html);
      $item_html = str_replace('</a>', ' <b class="caret"></b></a>', $item_html);
    }
    elseif (stristr($item_html, 'li class="divider')) {
      $item_html = preg_replace('/<a[^>]*>.*?<\/a>/iU', '', $item_html);
    }
    elseif (stristr($item_html, 'li class="nav-header')) {
      $item_html = preg_replace('/<a[^>]*>(.*)<\/a>/iU', '$1', $item_html);
    }

    $output .= $item_html;
  }

  function display_element($element, &$children_elements, $max_depth, $depth = 0, $args, &$output) {
    $element->is_dropdown = !empty($children_elements[$element->ID]);

    if ($element->is_dropdown) {
      if ($depth === 0) {
        $element->classes[] = 'dropdown';
      } elseif ($depth > 0) {
        $element->classes[] = 'dropdown-submenu';
      }
    }

    parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
  }
}




if(!function_exists('link_to_menu_editor')) :
/**
  * Menu fallback. Link to the menu editor.
  * Thanks to tosho (http://wordpress.stackexchange.com/users/73/toscho)
  * http://wordpress.stackexchange.com/questions/64515/fall-back-for-main-menu
  *
  * @package Customizr
  * @since Customizr 1.0
 */
  function link_to_menu_editor( $args )
  {
      if ( ! current_user_can( 'manage_options' ) )
      {
          return;
      }
      // see wp-includes/nav-menu-template.php for available arguments
      extract( $args );

      $link = $link_before
          . '<a href="' .admin_url( 'nav-menus.php' ) . '">' . $before . 'Add a menu' . $after . '</a>'
          . $link_after;

      // We have a list
      if ( FALSE !== stripos( $items_wrap, '<ul' )
          or FALSE !== stripos( $items_wrap, '<ol' )
      )
      {
          $link = "<li>$link</li>";
      }

      $output = sprintf( $items_wrap, $menu_id, $menu_class, $link );
      if ( ! empty ( $container ) )
      {
          $output  = "<$container class='$container_class' id='$container_id'>$output</$container>";
      }

      if ( $echo )
      {
          echo $output;
      }

      return $output;
  }
endif;



if(!function_exists('tc_get_featured_pages')) :
  /**
  *
  * @package Customizr
  * @since Customizr 1.0
  */
  function tc_get_featured_pages($area) {
    switch ($area) {
      case 'not-set':
          //admin link if user logged in
          $featured_page_link = '';
          $admin_link         = '';
          if (is_user_logged_in()) {
          $featured_page_link = admin_url().'customize.php';
          $admin_link = '<a href="'.admin_url().'customize.php" title="'.__('Customizer screen','customizr').'">'.__(' here','customizr').'</a>';
          }

          //rendering
          $featured_page_id     =  null;
          $featured_page_title  =  __('Featured page','customizr');
          $text                 =  sprintf(__('Featured page description text : use the page excerpt or set your own custom text in the Customizr screen%s.','customizr'),
          $admin_link 
            );
          $tc_thumb             =  '<img data-src="holder.js/270x250" alt="Holder Thumbnail">';

        break;
      


      default://for areas one, two, three
          //get saved options
          global $tc_theme_options;
          $featured_page_id     = $tc_theme_options['featured_page_'.$area];
          $featured_page_link   = get_permalink( $featured_page_id );
          $featured_page_title  = get_the_title( $featured_page_id );
          $featured_text        = esc_attr( $tc_theme_options['featured_text_'.$area] );

          //get the page/post object
          $page                 =  get_post($featured_page_id);
          
          //limit text to 200 car
          $text                 = strip_tags($featured_text);
          if (empty($text))
            $text               = strip_tags($page->post_content);
          if (strlen($text) > 200) {
            $text               = substr($text,0,strpos($text,' ',200));
            $text               = esc_html($text) . ' ...';
          }
          else {
            $text               = esc_textarea( $text );
          }
          
          
          //set the image : uses thumbnail if any then >> the first attached image then >> a holder script
        $tc_thumb_size = 'tc-thumb';
         if (has_post_thumbnail($featured_page_id)) {
              $tc_thumb_id       = get_post_thumbnail_id($featured_page_id);

              //check if tc-thumb size exists for attachment and return large if not
              $image = wp_get_attachment_image_src( $tc_thumb_id, $tc_thumb_size);
              if (null == $image[3])
                $tc_thumb_size  = 'medium';

              $tc_thumb         = get_the_post_thumbnail( $featured_page_id,$tc_thumb_size);
              //get height and width
              $tc_thumb_height  = $image[2];
              $tc_thumb_width   = $image[1];
          }

          //If not uses the first attached image
          else {
              //look for attachements
              $tc_args = array(
                'numberposts'     =>  1,
                'post_type'       =>  'attachment',
                'post_status'     =>  null,
                'post_parent'     =>  $featured_page_id,
                'post_mime_type'  =>  array('image/jpeg','image/gif','image/jpg','image/png')
                ); 
                $attachments = get_posts($tc_args);
                if ($attachments) {
                  foreach ($attachments as $attachment) {
                     //check if tc-thumb size exists for attachment and return large if not
                    $image = wp_get_attachment_image_src( $attachment->ID, $tc_thumb_size);
                    if (false == $image[3])
                      $tc_thumb_size = 'medium';
                    $tc_thumb = wp_get_attachment_image($attachment->ID, $tc_thumb_size);
                    //get height and width
                    $tc_thumb_height = $image[2];
                    $tc_thumb_width = $image[1];
                  }
                }
          }
          if (!isset($tc_thumb))
            $tc_thumb            = '<img data-src="holder.js/270x250" alt="Holder Thumbnail" />';
        break;
      }//end switch

      //Rendering
      ?>
        <div class="widget-front">
          <div class="thumb-wrapper <?php if(!has_post_thumbnail( $featured_page_id )) {echo 'tc-holder';} ?>">
                  <a class="round-div" href="<?php echo $featured_page_link ?>" title="<?php echo $featured_page_title ?>"></a>
              <?php echo $tc_thumb; ?>
            </div>
              <h2><?php echo $featured_page_title ?></h2>
            <p><?php echo $text;  ?></p>
              <p><a class="btn btn-primary" href="<?php echo $featured_page_link ?>" title="<?php echo $featured_page_title ?>"><?php _e( 'Read more &raquo;', 'customizr' ) ?></a></p>
        </div><!-- /.span4 -->
      <?php
  }
endif;




if(!function_exists('tc_get_sidebar')) :
  /**
  * Returns the sidebar or the front page featured pages area
  * @param Name of the widgetized area
  * @package Customizr
  * @since Customizr 1.0 
  */
    function tc_get_sidebar($name) {
        //get layout options
        global $tc_theme_options;
        $sidebar            = $tc_theme_options['tc_current_screen_layout']['sidebar'];
        $class              = $tc_theme_options['tc_current_screen_layout']['class'];
       
        //get info whether the front page is a list of last posts or a page
        $tc_what_on_front  = get_option( 'show_on_front');

      switch ($name) {
        case 'front':
          if((is_home() && $tc_what_on_front == 'posts') || is_front_page()) {
               get_template_part( 'featured', 'pages' );
          }
          break;

        case 'left':
          if($sidebar == 'l' || $sidebar == 'b' ) {
            echo '<div class="span3 left">';
              get_sidebar($name);
            echo '</div>';
          }
          break;

          case 'right':
          if($sidebar == 'r' || $sidebar == 'b' ) {
            echo '<div class="span3 right">';
              get_sidebar($name);
            echo '</div>';
          }
        break;
      }
    }
endif;



if ( ! function_exists( 'tc_customizr_entry_date' ) ) :
/**
  * Prints HTML with date information for current post.
  * @package Customizr
  * @since Customizr 1.0 
 */
    function tc_customizr_entry_date( $echo = true ) {
      $format_prefix = ( has_post_format( 'chat' ) || has_post_format( 'status' ) ) ? _x( '%1$s on %2$s', '1: post format name. 2: date', 'customizr' ): '%2$s';

      $date = sprintf( '<span class="date"><a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a></span>',
        esc_url( get_permalink() ),
        esc_attr( sprintf( __( 'Permalink to %s', 'customizr' ), the_title_attribute( 'echo=0' ) ) ),
        esc_attr( get_the_date( 'c' ) ),
        esc_html( sprintf( $format_prefix, get_post_format_string( get_post_format() ), get_the_date() ) )
      );

      if ( $echo )
        echo $date;

      return $date;
    }
endif;




if ( ! function_exists( 'tc_comment_callback' ) ) :
/**
 * Template for comments and pingbacks.
 *
 *
  * Used as a callback by wp_list_comments() for displaying the comments.
  *  Inspired from Twenty Twelve 1.0
  * @package Customizr
  * @since Customizr 1.0 
 */
    function tc_comment_callback( $comment, $args, $depth ) {
      $GLOBALS['comment'] = $comment;
      switch ( $comment->comment_type ) :
        case 'pingback' :
        case 'trackback' :
        // Display trackbacks differently than normal comments.
      ?>
      <li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
        <article id="comment-<?php comment_ID(); ?>" class="comment">
          <p><?php _e( 'Pingback:', 'customizr' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)', 'customizr' ), '<span class="edit-link btn btn-success btn-mini">', '</span>' ); ?></p>
        </article>
      <?php
          break;
        default :
        // Proceed with normal comments.
        global $post;
      ?>
      <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
        <article class="comment">
            <div class="row-fluid">
              <div class="comment-avatar span2">
                <?php echo get_avatar( $comment, 80 ); ?>
              </div>
              <div class="span10">
                <?php if(get_option('thread_comments') == 1) : //check if the nested comment option is checked?>
                    <div class="reply btn btn-small">
                      <?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply', 'customizr' ), 'after' => ' <span>&darr;</span>', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
                    </div><!-- .reply -->
                <?php endif; ?>
                <header class="comment-meta comment-author vcard">
                    <?php
                    printf( '<cite class="fn">%1$s %2$s %3$s</cite>',
                      get_comment_author_link(),
                      // If current post author is also comment author, make it known visually.
                      ( $comment->user_id === $post->post_author ) ? '<span> ' . __( 'Post author', 'customizr' ) . '</span>' : '',
                      edit_comment_link( __( 'Edit', 'customizr' ), '<p class="edit-link btn btn-success btn-mini">', '</p>' )
                    );
                    printf( '<a class="comment-date" href="%1$s"><time datetime="%2$s">%3$s</time></a>',
                      esc_url( get_comment_link( $comment->comment_ID ) ),
                      get_comment_time( 'c' ),
                      /* translators: 1: date, 2: time */
                      sprintf( __( '%1$s at %2$s', 'customizr' ), get_comment_date(), get_comment_time() )
                    );
                  ?>
                </header><!-- .comment-meta -->

                <?php if ( '0' == $comment->comment_approved ) : ?>
                  <p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'customizr' ); ?></p>
                <?php endif; ?>

                <section class="comment-content comment">
                  <?php comment_text(); ?>
                </section><!-- .comment-content -->
            </div><!-- .span8 -->
          </div><!-- .row -->
        </article><!-- #comment-## -->
      <?php
        break;
      endswitch; // end comment_type check
    }
endif;



if ( ! function_exists( 'tc_get_the_category_list' ) ) :
/**
 * Template for comments and pingbacks.
 *
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 * Inspired from Twenty Twelve 1.0
  * @package Customizr
  * @since Customizr 1.0 
 */
    function tc_get_the_category_list() {
      $postcats = get_the_category();
        if ($postcats) {
          $html = '';
          foreach($postcats as $cat) {
            $html .= '<a class="btn btn-mini" href="'.get_category_link( $cat->term_id ).'" title="' . esc_attr( sprintf( __( "View all posts in %s",'customizr' ), $cat->name ) ) . '">';
              $html .= ' '.$cat->cat_name.' ';
            $html .= '</a>';
          }
          //$html .= '</div>';
         return $html;
        }
      }
endif;



if ( ! function_exists( 'tc_get_the_tag_list' ) ) :
/**
 * Template for comments and pingbacks.
 *
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 * Inspired from Twenty Twelve 1.0
 * @package Customizr
 * @since Customizr 1.0 
 *
 */
    function tc_get_the_tag_list() {
      $posttags = get_the_tags();
        if ($posttags) {
          $html = '';
          foreach($posttags as $tag) {
            $html .= '<a class="btn btn-mini btn-info" href="'.get_tag_link( $tag->term_id ).'" title="' . esc_attr( sprintf( __( "View all posts in %s",'customizr' ), $tag->name ) ) . '">';
               $html .= ' '.$tag->name.' ';
            $html .= '</a>';
          }
          //$html .= '</div>';
         return $html;
        }
      }
endif;



if ( ! function_exists( 'tc_get_breadcrumb' ) ) :
/**
  * 
  * @package Customizr
  * @since Customizr 1.0 
 */
    function tc_get_breadcrumb() {
      global $tc_theme_options;
      //get the default layout
        $tc_breadcrumb = $tc_theme_options['tc_breadcrumb'];
        if($tc_breadcrumb != 1)
          return;
      $args = array(
      'container'  => 'div', // div, nav, p, etc.
      'separator'  => '&raquo;',
      'before'     => false,
      'after'      => false,
      'front_page' => true,
      'show_home'  => __( 'Home', 'breadcrumb-trail' ),
      'network'    => false,
      'echo'       => true
      );

      //do not display breadcrumb on home page
      if (is_home() || is_front_page())
        return;
        ?>
        <div class="tc-hot-crumble container" role="navigation">
          <div class="row">
            <div class="span12">
            <?php breadcrumb_trail($args); ?>
            </div>
          </div>
        </div>
        <?php
    }
endif;




if ( ! function_exists( 'tc_get_social' ) ) :
/**
  * 
  * @package Customizr
  * @since Customizr 1.0 
 */
  function tc_get_social($pos) {

    global $tc_theme_options;
    
    if($tc_theme_options[$pos] == 0)
      return;

    $socials = array (
          'tc_rss'            => __('feed','customizr'),
          'tc_twitter'        => __('twitter','customizr'),
          'tc_facebook'       => __('facebook','customizr'),
          'tc_google'         => __('google','customizr'),
          'tc_youtube'        => __('youtube','customizr'),
          'tc_pinterest'      => __('pinterest','customizr'),
          'tc_github'         => __('github','customizr'),
          'tc_dribbble'       => __('dribbble','customizr'),
          'tc_linkedin'       => __('linkedin','customizr')
          );
      
      $html = '';
      //check if sidebar option is checked
      if (preg_match('/left|right/', $pos)) {
        $html = '<h3 class="widget-title">'.__('Social links','customizr').'</h3>';
      }
      //$html .= '<ul>';
        foreach ($socials as $key => $nw) {
          //all cases except rss
          $title = __('Follow me on ','tc_boostrap').$nw;
          $target = 'target=_blank';
          //rss case
          if ($key == 'tc_rss') {
            $title = __('Suscribe to my rss feed','tc_boostrap');
            $target = '';
          }

          if ($tc_theme_options[$key] != '') {
            //$html .= '<li>';
              $html .= '<a class="social-icon icon-'.$nw.'" href="'.esc_url($tc_theme_options[$key]).'" title="'.$title.'" '.$target.'></a>';
          }
       }
      //$html .= '</li></ul>';
   
    return $html;
  }
endif;



if(!function_exists('tc_get_favicon')) :
/**
  * 
  * @package Customizr
  * @since Customizr 1.0 
 */
  function tc_get_favicon()
  {
    global $tc_theme_options;
    $fav_link = '';
    $url = esc_url($tc_theme_options['tc_fav_upload']);
    if($url != null)
    {
      $type = "image/x-icon";
      if(strpos($url,'.png' )) $type = "image/png";
      if(strpos($url,'.gif' )) $type = "image/gif";
    
      $fav_link = '<link rel="shortcut icon" href="'.$url.'" type="'.$type.'">';
    }
    echo $fav_link;
  }
endif;

<?php
class CZR_cl_help_block_model_class extends CZR_cl_Model {
  public $element_tag = 'div';
  public $help_message;
  public $help_secondary_message;
  public $help_title;
  public $help_block_data;
  public $data_notice_id;
  public $data_user_option;

  public static $js_enqueued = false;

  /*
  * @override
  */
  function __construct( $model ) {
    /*
     * this notice is on when CZR_cl_DEV or
     * when the specific notice is enabled
     * tc_is_notice_on is stronger than tc_is_notice_enabled.
     * The latter generally checks whether or not the transient is disabled
     * the earlier puts the above in OR with CZR_cl_DEV, as we always want to display help blocks for testing purposes.
     * So when extending this class, to actually *contextually* forbid an help block be sure you override
     * tc_is_notice_on!
     */
    if ( ! apply_filters( "tc_is_{$this -> czr_fn_get_the_data_notice_id()}_on", $this -> tc_is_notice_on() ) ) {
      $model['id'] = '';
    }
    parent::__construct( $model );

    //emulate an "enqueue once"
    if ( ! self::$js_enqueued ) {
      add_action( 'wp_footer'   , array( $this, 'tc_write_tc_notice_js'),  100 );
      self::$js_enqueued = true;
    }
  }

  function czr_fn_extend_params( $model = array() ) {
    $model[ 'help_title'  ]              = $this -> czr_fn_get_the_help_title();
    $model[ 'help_message']              = $this -> czr_fn_get_the_help_message();
    $model[ 'help_secondary_message']    = $this -> czr_fn_get_the_help_secondary_message();
    $model[ 'help_block_data']           = array_filter( array(
        $this -> czr_fn_get_the_data_notice_id() ? 'data-notice_id="' . $this->czr_fn_get_the_data_notice_id() . '"' : '',
        $this -> czr_fn_get_the_data_user_option() ? 'data-user_option="' . $this->czr_fn_get_the_data_user_option() . '"' : ''
    ));
    return $model;
  }

  /*
  * The notice is enabled when the associated transient is not disabled
  */
  function czr_fn_is_notice_enabled() {
    return 'disabled' != get_transient( $this -> czr_fn_get_the_notice_transient() );
  }

  /*
  * The notice is on when the notice is enabled or CZR_cl_DEV
  * The main difference between this method and tc_is_notice_enabled consists on the fact
  * that some notices should be displayed only under some contextual conditions which are
  * stronger than the DEV mode and/or the notice enabled (e.g. smartload helps in single or post list)
  */
  function czr_fn_is_notice_on() {
    return ( defined('CZR_cl_DEV') && true === CZR_cl_DEV ) || $this -> tc_is_notice_enabled();
  }


  function czr_fn_get_the_help_title() {
    return $this -> help_title;
  }

  function czr_fn_get_the_help_message() {
    return $this -> help_message;
  }

  function czr_fn_get_the_help_secondary_message() {
    return $this -> help_secondary_message;
  }

  function czr_fn_get_the_data_user_option() {
    return $this -> data_user_option;
  }

  function czr_fn_get_the_data_notice_id() {
    return $this -> data_notice_id;
  }

  function czr_fn_get_the_notice_transient() {
    return "tc_{$this -> czr_fn_get_the_data_notice_id()}";
  }

  /**
  * @override
  * parse this model properties for rendering
  */
  function czr_fn_sanitize_model_properties( $model ) {
    parent::tc_sanitize_model_properties( $model );
    $model -> help_block_data = $this -> tc_stringify_model_property( "help_block_data" );
  }

  /**
  * Prints dismiss notice js in the footer
  * Two cases :
  * 1) dismiss notice
  * 2) remove element
  * hook : wp_footer
  *
  * @package Customizr
  * @since Customizr 3.4+
  */
  function czr_fn_write_tc_notice_js() {
    ?>
    <script type="text/javascript" id="tc-notice-actions">
      ( function( $ ) {
        var tc_ajax_request = function( remove_action, $_el ) {
          var AjaxUrl         = "<?php echo admin_url( 'admin-ajax.php' ); ?>",
              _query = {
                  action        : 'tc_notice_actions',
                  remove_action : remove_action,
                  notice_id     : $_el.data( 'notice_id' ),
                  user_option   : $_el.data( 'user_option' ),
                  TCNoticeNonce :  "<?php echo wp_create_nonce( 'tc-notice-nonce' ); ?>"
              },
              $ = jQuery,
              request = $.post( AjaxUrl, _query );

          request.done( function( response ) {
            // Check if the user is logged out.
            if ( '0' === response )
              return;
            // Check for cheaters.
            if ( '-1' === response )
              return;

            switch ( remove_action ) {
              case 'remove_block' :
                $_el.parent().fadeOut('slow');
                break;
              case 'remove_notice':
                //remove all notices sharing the same notice_id (useful for sidebars)
                $('.tc-placeholder-wrap[data-notice_id="' + $_el.data('notice_id') + '"]').slideToggle('fast');
            }
          });
        };//end of fn

        //DOM READY
        $( function($) {
          $('.tc-dismiss-notice, .tc-inline-dismiss-notice').click( function( e ) {
            e.preventDefault();
            tc_ajax_request( 'remove_notice', $(this).closest( '.tc-placeholder-wrap' ) );
          } );
          $('.tc-inline-remove').click( function( e ) {
            e.preventDefault();
            tc_ajax_request( 'remove_block', $(this).closest( '.tc-placeholder-wrap' ) );
          } );
        } );

    }) (jQuery)
  </script>
  <?php
  }
}

/**************************
*** Sidebars help block ***
**************************/
abstract class CZR_cl_sidebar_help_block_model_class extends CZR_cl_help_block_model_class {
  public $data_notice_id  = 'widget_placehold_sidebar';
  public $position;
  public $element_class   = 'tc-widget-placeholder';
  public $element_tag     = 'aside';

  /*
  * @override
  */
  function czr_fn_get_the_help_message() {
    $_customizer_lnk =  CZR_cl_utils::czr_fn_get_customizer_url( array( 'panel' => 'widgets', 'section' => 'sidebar-widgets-' . $this -> position ) );
    return sprintf( __("Add widgets to this sidebar %s or %s.", "customizr"),
                sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', $_customizer_lnk, __( "Add widgets", "customizr"), __("now", "customizr") ),
                sprintf('<a class="tc-inline-dismiss-notice" href="#" title="%1$s">%1$s</a>',
                  __( 'dismiss this notice', 'customizr')
                )
           );
  }

  /*
  * @override
  */
  function czr_fn_get_the_help_secondary_message() {
    return sprintf('<p><i>%1s <a href="http:%2$s" title="%3$s" target="blank">%4$s</a></i></p>',
              __( 'You can also remove this sidebar by changing the current page layout.', 'customizr' ),
              '//docs.presscustomizr.com/article/107-customizr-theme-options-pages-and-posts-layout',
              __( 'Changing the layout in the Customizr theme' , 'customizr'),
              __( 'See the theme documentation.' , 'customizr' )
          );

  }

  /*
  * @override
  * the notice isn't enabled also when the sidebar is active
  */
  function czr_fn_is_notice_enabled() {
    return parent::tc_is_notice_enabled() && ! is_active_sidebar( $this -> position );
  }
}

class CZR_cl_right_sidebar_help_block_model_class extends CZR_cl_sidebar_help_block_model_class {
  public $position = 'right';

  /*
  * @override
  * here 'cause the entire message should be translatable
  */
  function czr_fn_get_the_help_title() {
    return __( 'The right sidebar has no widgets', 'customizr');
  }
}
class CZR_cl_left_sidebar_help_block_model_class extends CZR_cl_sidebar_help_block_model_class {
  public $position = 'left';

  /*
  * @override
  * here 'cause the entire message should be translatable
  */
  function czr_fn_get_the_help_title() {
    return __( 'The left sidebar has no widgets', 'customizr');
  }

}
/**************************
**** Footer help block ****
**************************/
class CZR_cl_footer_widgets_help_block_model_class extends CZR_cl_sidebar_help_block_model_class {
  public $position        = 'footer';
  public $data_notice_id  = 'widget_placehold_footer';
  public $element_class   = 'tc-widget-placeholder';
  public $element_tag     = 'aside';

  /*
  * @override
  */
  function czr_fn_get_the_help_message() {
    return sprintf( __("Add widgets to the footer %s or %s.", "customizr"),
                sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', CZR_cl_utils::czr_fn_get_customizer_url( array( 'panel' => 'widgets') ), __( "Add widgets", "customizr"), __("now", "customizr") ),
                sprintf('<a class="tc-inline-dismiss-notice" href="#" title="%1$s">%1$s</a>',
                  __( 'dismiss this notice', 'customizr')
                )
            );
  }

  /*
  * @override
  */
  function czr_fn_get_the_help_title() {
    return  __( 'The footer has no widgets', 'customizr' );
  }

  /*
  * @override
  */
  function czr_fn_get_the_help_secondary_message() {
    return '';
  }

  /*
  * @override
  * the notice isn't enabled also when at least one footer widget area is active
  */
  function czr_fn_is_notice_enabled() {
    $bool = true;
    foreach ( apply_filters( 'tc_footer_widgets', CZR_cl_init::$instance -> footer_widgets ) as $key => $area )
      if ( is_active_sidebar( $key ) ) {
        $bool = false;
        break;
      }
    return parent::tc_is_notice_enabled() && $bool;
  }

}

/*********************************
*** Featured Pages help block  ***
*********************************/
class CZR_cl_featured_pages_help_block_model_class extends CZR_cl_help_block_model_class {
  public $element_class    = 'tc-fp-notice';
  public $data_notice_id   = 'fp_notice';
  public $data_user_option = 'tc_show_featured_pages';

  /*
  * @override
  */
  function czr_fn_get_the_help_message() {
    $_customizer_lnk = apply_filters( 'tc_fp_notice_customizer_url', CZR_cl_utils::czr_fn_get_customizer_url( array( 'control' => 'tc_show_featured_pages', 'section' => 'frontpage_sec') ) );
    return sprintf( __("Edit those featured pages %s, or %s (you'll be able to add yours later)." , "customizr"),
              sprintf( '<a href="%3$s" title="%1$s">%2$s</a>', __( "Edit those featured pages", "customizr" ), __( "now", "customizr" ), $_customizer_lnk ),
              sprintf( '<a href="#" class="tc-inline-remove" title="%1$s">%2$s</a>', __( "Remove the featured pages", "customizr" ), __( "remove them", "customizr" ) )
            );
  }

  /*
  * @override
  * we do not complete the fp notice instanciation if we're not displaying the featured pages,
  * or not in home
  * or in Customizr Pro
  */
  function czr_fn_is_notice_on() {
    return ! CZR___::czr_fn_is_pro()
        && (bool)CZR_cl_utils::$inst->czr_fn_opt('tc_show_featured_pages')
        && CZR_cl_utils::$inst -> tc_is_home()
        && parent::tc_is_notice_on();
  }

  /*
  * @override
  * the notice isn't enabled also when one fp is set
  */
  function czr_fn_is_notice_enabled() {
    return parent::tc_is_notice_enabled() && $this -> tc_is_one_fp_set();
  }

  /**
  * Helper to check if at least one featured page has been set by the user.
  * @return bool
  * @since v3.4+
  */
  function czr_fn_is_one_fp_set() {
    $_fp_sets = array();
    $fp_ids = apply_filters( 'tc_featured_pages_ids' , CZR_cl_init::$instance -> fp_ids);
    if ( ! is_array($fp_ids) )
      return;
    foreach ($fp_ids as $fp_single_id ) {
      $_fp_sets[] = (bool)CZR_cl_utils::$inst->czr_fn_opt( 'tc_featured_page_'.$fp_single_id );
    }
    //returns true if at least one fp has been set.
    return (bool)array_sum($_fp_sets);
  }
}

/*********************************
******* Slider help block  *******
*********************************/
class CZR_cl_slider_help_block_model_class extends CZR_cl_help_block_model_class {
  public $element_class    = 'tc-slider-notice';
  public $data_notice_id   = 'slider_notice';
  public $data_user_option = 'tc_front_slider';

  /*
  * @override
  */
  function czr_fn_get_the_help_message() {
    $_customizer_lnk = CZR_cl_utils::czr_fn_get_customizer_url( array( 'control' => 'tc_front_slider', 'section' => 'frontpage_sec') );
    return sprintf( __("Select your own slider %s, or %s (you'll be able to add one back later)." , "customizr"),
        sprintf( '<a href="%3$s" title="%1$s">%2$s</a>', __( "Select your own slider", "customizr" ), __( "now", "customizr" ), $_customizer_lnk ),
        sprintf( '<a href="#" class="tc-inline-remove" title="%1$s">%2$s</a>', __( "Remove the home page slider", "customizr" ), __( "remove this demo slider", "customizr" ) )
    );
  }

  /*
  * @override
  * we do not complete the slider notice instanciation if we're not displaying the demo slider (and we're in the front page)
  */
  function czr_fn_is_notice_on() {
    return CZR_cl_utils::$inst-> tc_is_home()
        && parent::tc_is_notice_on();
  }
}

/********************************************
******* Thumbnail (single) help block  *******
********************************************/
class CZR_cl_thumbnail_help_block_model_class extends CZR_cl_help_block_model_class {
  public $element_class    = 'tc-thumbnail-help';
  public $data_notice_id   = 'thumbnail_help';

  /*
  * @override
  */
  function czr_fn_get_the_help_message() {
    return __( "You can display your post's featured image here if you have set one.", "customizr" );
  }

  /*
  * @override
  */
  function czr_fn_get_the_help_secondary_message() {
    $_customizer_lnk = CZR_cl_utils::czr_fn_get_customizer_url( array( "section" => "single_posts_sec") );
    return sprintf('<p>%1$s</p><p>%2$s</p>',
              sprintf( __("%s to display a featured image here.", "customizr"),
                sprintf( '<strong><a href="%1$s" title="%2$s">%2$s</a></strong>', $_customizer_lnk , __( "Jump to the customizer now", "customizr") )
              ),
              sprintf( __( "Don't know how to set a featured image to a post? Learn how in the %s.", "customizr" ),
                sprintf('<a href="%1$s" title="%2$s" target="_blank">%2$s</a><span class="tc-external"></span>' , esc_url('codex.wordpress.org/Post_Thumbnails#Setting_a_Post_Thumbnail'), __("WordPress documentation" , "customizr" ) )
            )
        );
  }

  /*
  * @override
  * The notice is also disabled when the user chose to display a thumb in single posts
  */
  function czr_fn_is_notice_enabled() {
    return 'hide' == CZR_cl_utils::$inst->czr_fn_opt('tc_single_post_thumb_location') && parent::tc_is_notice_enabled();
  }

  /*
  * @override
  * we do not complete the single thumb notice instanciation if we're not in single contexts
  */
  function czr_fn_is_notice_on() {
    return CZR_cl_utils_query::$instance -> tc_is_single_post()
        && parent::tc_is_notice_on();
  }
}

/*********************************************
******* Smartload help block  *******
*********************************************/
class CZR_cl_smartload_help_block_model_class extends CZR_cl_help_block_model_class {
  public $element_class   = 'tc-img-smartload-help';
  public $data_notice_id  = 'img_smartload_help';

  /*
  * @override
  */
  function czr_fn_get_the_help_message() {
    return __( "Did you know you can easily speed up your page load by deferring the loading of the non visible images?", "customizr" );
  }

  /*
  * @override
  */
  function czr_fn_get_the_help_secondary_message() {
    $_customizer_lnk = CZR_cl_utils::czr_fn_get_customizer_url( array( "control" => "tc_img_smart_load", "section" => "performances_sec" ) );
    return sprintf("<p>%s</p>",
            sprintf( __("%s and check the option 'Load images on scroll' under 'Website Performances' section.", "customizr"),
                sprintf( '<strong><a href="%1$s" title="%2$s">%2$s</a></strong>', $_customizer_lnk, __( "Jump to the customizer now", "customizr") )
            )
        );
  }

  /*
  * @override
  * The notice is also disabled when the user checked the smartload option
  */
  function czr_fn_is_notice_enabled() {
    return parent::tc_is_notice_enabled() &&  1 != esc_attr( CZR_cl_utils::$inst->czr_fn_opt( 'tc_img_smart_load' ) );
  }

}
/*********************************************
******* Smartload (singular) help block  *******
*********************************************/
class CZR_cl_singular_smartload_help_block_model_class extends CZR_cl_smartload_help_block_model_class {

  /*
  * @override
  * In single post the smartload help will be displaye only if there is at least 1 image
  */
  function czr_fn_is_notice_enabled() {
    global $post;
    /*
     * for this purpose we should filter the content in order to get also gallery shortcode computed
     * but I think it's too much expensive for just having an help block
     */
    return parent::tc_is_notice_enabled() &&
      apply_filters('tc_img_smartload_help_n_images', 2 ) <= preg_match_all( '/(<img[^>]+>)/i', $post->post_content, $matches );

  }

  /*
  * @override
  * Notice enabled only in sinle context
  */
  function czr_fn_is_notice_on() {
    global $post;

    return is_singular() && parent::tc_is_notice_on();
  }
}
/*********************************************
******* Smartload (post list) help block  *******
*********************************************/
class CZR_cl_post_list_smartload_help_block_model_class extends CZR_cl_smartload_help_block_model_class {

  /*
  * @override
  * Notice enabled if not in singular contexts
  */
  function czr_fn_is_notice_on() {
    global $post;

    return parent::tc_is_notice_on() && CZR_cl_utils_query::$instance -> tc_is_list_of_posts();
  }
}
/*********************************************
******* Sidenav help block  *******
*********************************************/
class CZR_cl_sidenav_help_block_model_class extends CZR_cl_help_block_model_class {
  public $element_class   = 'tc-sidenav-help';
  public $data_notice_id  = 'sidenav_help';

  /*
  * @override
  */
  function czr_fn_get_the_help_message() {
    return __( "This is a default page menu.", "customizr" );

  }

  /*
  * @override
  */
  function czr_fn_get_the_help_secondary_message() {
    $_customizer_lnk = CZR_cl_utils::czr_fn_get_customizer_url( array( "section" => "nav") );
    return sprintf('<p>%1$s</p><p>%2$s</p>',
              __( "( If you don't have any pages in your website, then this side menu is empty for the moment. )" , "customizr"),
              sprintf( __("If you have already created menu(s), you can %s. If you need to create a new menu, jump to the %s.", "customizr"),
                sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', $_customizer_lnk, __( "change the default menu", "customizr"), __("replace this default menu by another one", "customizr") ),
                sprintf( '<a href="%1$s" title="%2$s" target="blank">%2$s</a>', admin_url('nav-menus.php'), __( "menu creation screen", "customizr") )
              )
          );
  }

  /*
  * @override
  * we do not complete the sidenav menu notice instanciation if we're not displaying the sidenav menu
  */
  function czr_fn_is_notice_on() {
    return 'navbar' != CZR_cl_utils::$inst->czr_fn_opt('tc_menu_style') && parent::tc_is_notice_on();
  }

  /*
  * @override
  * the notice isn't enabled also if the main location has a menu assigned
  */
  function czr_fn_is_notice_enabled() {
    return ! CZR_cl_utils::$inst->czr_fn_has_location_menu('main') && parent::tc_is_notice_enabled();
  }
}

/*********************************************
****** Primary regular menu help block  ******
*********************************************/
class CZR_cl_main_menu_help_block_model_class extends CZR_cl_help_block_model_class {
  public $element_class   = 'tc-main-menu-notice';
  public $data_notice_id  = 'main_menu_notice';

  /*
  * @override
  */
  function czr_fn_get_the_help_message() {
    $_customizer_lnk = CZR_cl_utils::czr_fn_get_customizer_url( array( "section" => "nav") );
    return sprintf('%1$s<br/>%2$s',
              __( "You can now display your menu as a vertical and mobile friendly side menu, animated when revealed.", "customizr" ),
              sprintf( __("%s or %s.", "customizr"),
                sprintf( '<a href="%1$s" title="%2$s" target="blank">%2$s</a><span class="tc-external"></span>', esc_url('demo.presscustomizr.com?design=nav'), __( "Try it with the demo", "customizr") ),
                sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', $_customizer_lnk, __( "open the customizer menu section", "customizr"), __("change your menu design now", "customizr") )
              )
          );
  }

  /*
  * @override
  * we do not complete the main menu notice instanciation if we're displaying the sidenav menu
  */
  function czr_fn_is_notice_on() {
    return 'navbar' == CZR_cl_utils::$inst->czr_fn_opt('tc_menu_style') && parent::tc_is_notice_on();
  }

  /*
  * @override
  * the notice isn't enabled also when the second menu is on
  */
  function czr_fn_is_notice_enabled() {
    return (bool)CZR_cl_utils::$inst->czr_fn_opt('tc_display_second_menu') && parent::tc_is_notice_enabled();
  }
}
/*********************************************
****** Secondary regular menu help block  ******
*********************************************/
class CZR_cl_second_menu_help_block_model_class extends CZR_cl_help_block_model_class {
  public $element_class   = 'nav-collapse collapse tc-menu-placeholder';
  public $data_notice_id  = 'second_menu_placehold';

  /*
  * @override
  */
  function czr_fn_get_the_help_message() {
    $_customizer_lnk = CZR_cl_utils::czr_fn_get_customizer_url( array( "section" => "nav") );
    return sprintf('%1$s<br/>%2$s',
              __( "You can display your main menu or a second menu here horizontally.", "customizr" ),
              sprintf( __("%s or read the %s.", "customizr"),
                sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', $_customizer_lnk, __( "Manage menus in the header", "customizr"), __("Manage your menus in the header now", "customizr") ),
                sprintf( '<a href="%1$s" title="%2$s" target="blank">%2$s</a><span class="tc-external"></span>', esc_url('http://docs.presscustomizr.com/article/101-customizr-theme-options-header-settings/#navigation'), __( "documentation", "customizr") )
              )
          );
  }

  /*
  * @override
  * we do not complete the second menu notice instanciation if we're not displaying the sidenav menu
  */
  function czr_fn_is_notice_on() {
    return 'navbar' != CZR_cl_utils::$inst->czr_fn_opt('tc_menu_style') && parent::tc_is_notice_on();
  }

  /*
  * @override
  * the notice isn't enabled also if the second menu option is checked
  */
  function czr_fn_is_notice_enabled() {
    return ! (bool)CZR_cl_utils::$inst->czr_fn_opt('tc_display_second_menu') && parent::tc_is_notice_enabled();
  }

}//end class


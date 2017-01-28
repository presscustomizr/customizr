<?php
/**
* Sidebar actions
* The default widgets areas are defined as properties of the CZR_utils class in class-fire-utils.php
* CZR_utils::$inst -> sidebar_widgets for left and right sidebars
* CZR_utils::$inst -> footer_widgets for the footer
* The widget area are then fired in class-fire-widgets.php
* You can modify those default widgets with 3 filters : tc_default_widgets, tc_footer_widgets, tc_sidebar_widgets
*
* @package      Customizr
* @subpackage   classes
* @since        3.0
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'CZR_sidebar' ) ) :
  class CZR_sidebar {
      static $instance;
      function __construct () {
        self::$instance =& $this;
        add_action ( 'wp'       , array( $this , 'czr_fn_set_sidebar_hooks' ) );
      }


      /******************************************
      * HOOK
      ******************************************/
      /**
      * Set sidebar hooks
      * hook : wp
      *
      * @since Customizr 3.3+
      */
      function czr_fn_set_sidebar_hooks() {
        //displays left sidebar
    		add_action ( '__before_article_container'  , array( $this , 'czr_fn_sidebar_display' ) );
    		add_action ( '__before_left_sidebar'       , array( $this , 'czr_fn_social_in_sidebar' ) );

        //displays right sidebar
    		add_action ( '__after_article_container'   , array( $this , 'czr_fn_sidebar_display' ) );
    		add_action ( '__before_right_sidebar'      , array( $this , 'czr_fn_social_in_sidebar' ) );

        //since 3.2.0 show/hide the WP built-in widget icons
        add_filter ( 'tc_left_sidebar_class'       , array( $this , 'czr_fn_set_sidebar_wrapper_widget_class' ) );
        add_filter ( 'tc_right_sidebar_class'      , array( $this , 'czr_fn_set_sidebar_wrapper_widget_class' ) );
      }



      /******************************************
      * VIEW
      ******************************************/
      /**
      * Displays the sidebar or the front page featured pages area
      * If no widgets are set, displays a placeholder
      *
      * @param Name of the widgetized area
      * @package Customizr
      * @since Customizr 1.0
      */
      function czr_fn_sidebar_display() {
        //first check if home and no content option is choosen
        if ( czr_fn__f( '__is_home_empty') )
          return;
        //gets current screen layout
        $screen_layout        = CZR_utils::czr_fn_get_layout( CZR_utils::czr_fn_id() , 'sidebar'  );
		    // GY: add relative right and left for LTR/RTL sites
        $rel_left             = is_rtl() ? 'right' : 'left';
        $rel_right            = is_rtl() ? 'left' : 'right';
        //gets position from current hook and checks the context
        $position             = apply_filters(
                                'tc_sidebar_position',
                                strpos(current_filter(), 'before') ? $rel_left : $rel_right
        );

        if ( 'left' == $position && $screen_layout != 'l' && $screen_layout != 'b' )
          return;
        if ( 'right' == $position && $screen_layout != 'r' && $screen_layout != 'b' )
          return;

        //gets the global layout settings
        $global_layout        = apply_filters( 'tc_global_layout' , CZR_init::$instance -> global_layout );
        $sidebar_layout       = $global_layout[$screen_layout];

        //defines the sidebar wrapper class
        $class                = implode(" ", apply_filters( "tc_{$position}_sidebar_class" , array( $sidebar_layout['sidebar'] , $position , 'tc-sidebar' ) ) );
        ob_start();
        ?>

        <div class="<?php echo $class  ?>">
           <div id="<?php echo $position ?>" class="widget-area" role="complementary">
              <?php
                do_action( "__before_{$position}_sidebar" );##hook of social icons

                if ( apply_filters( 'tc_has_sidebar_widgets', is_active_sidebar( $position ), $position ) )
                  get_sidebar( $position );
                else
                  $this -> czr_fn_display_sidebar_placeholder($position);

                do_action( "__after_{$position}_sidebar" );
              ?>
            </div><!-- //#left or //#right -->
        </div><!--.tc-sidebar -->

        <?php
        $html = ob_get_contents();
        if ($html) ob_end_clean();
        echo apply_filters( 'tc_sidebar_display', $html, $sidebar_layout, $position );
      }//end of function




      /**
      * When do we display this placeholder ?
      * User logged in
      * + Admin
      * + User did not dismissed the notice
      * @param : string position left or right
      * @since Customizr 3.3
      */
      private function czr_fn_display_sidebar_placeholder( $position ) {
        if ( ! CZR_placeholders::czr_fn_is_widget_placeholder_enabled( 'sidebar' ) )
          return;
        ?>
        <aside class="tc-placeholder-wrap tc-widget-placeholder">
          <?php
            printf('<span class="tc-admin-notice">%1$s</span>',
              __( 'This block is visible for admin users only.', 'customizr')
            );

            printf('<h4>%1$s</h4>',
              sprintf( __( 'The %s sidebar has no widgets.', 'customizr'), $position )
            );

            printf('<p><strong>%1$s</strong></p>',
              sprintf( __("Add widgets to this sidebar %s or %s.", "customizr"),
                sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', CZR_utils::czr_fn_get_customizer_url( array( 'panel' => 'widgets') ), __( "Add widgets", "customizr"), __("now", "customizr") ),
                sprintf('<a class="tc-inline-dismiss-notice" data-position="sidebar" href="#" title="%1$s">%1$s</a>',
                  __( 'dismiss this notice', 'customizr')
                )
              )
            );

            printf('<p><i>%1s <a href="http:%2$s" title="%3$s" target="blank">%4$s</a></i></p>',
              __( 'You can also remove this sidebar by changing the current page layout.', 'customizr' ),
              '//docs.presscustomizr.com/article/107-customizr-theme-options-pages-and-posts-layout',
              __( 'Changing the layout in the Customizr theme' , 'customizr'),
              __( 'See the theme documentation.' , 'customizr' )
            );

            printf('<a class="tc-dismiss-notice" data-position="sidebar" href="#" title="%1$s">%1$s x</a>',
              __( 'dismiss notice', 'customizr')
            );
        ?>
        </aside>
        <?php
      }




      /**
      * Displays the social networks in sidebars
      *
      * @package Customizr
      * @since Customizr 1.0
      */
      function czr_fn_social_in_sidebar() {
        //get option from current hook
        $option               = ( false != strpos(current_filter(), 'left') ) ? 'tc_social_in_left-sidebar' : 'tc_social_in_right-sidebar';

        //when do we display this block ?
        //1) if customizing: must be enabled
        //2) if not customizing : must be enabled and have social networks.
        $_nothing_to_render         = 0 == esc_attr( CZR_utils::$inst->czr_fn_opt( $option ) );

        $_nothing_to_render_front   = $_nothing_to_render || ! ( $_socials = czr_fn__f( '__get_socials' ) ) ? true : $_nothing_to_render;

        //only when partial refresh enabled, otherwise we fall back on refresh
        $_nothing_to_render         = CZR___::$instance -> czr_fn_is_customizing() && czr_fn_is_partial_refreshed_on() ? $_nothing_to_render : $_nothing_to_render_front;

        if ( $_nothing_to_render )
          return;

        $_title = esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_social_in_sidebar_title') );
        $html = sprintf('<aside class="%1$s">%2$s<div class="social-links">%3$s</div></aside>',
            implode( " " , apply_filters( 'tc_sidebar_block_social_class' , array('social-block', 'widget', 'widget_social') ) ),
            ! $_title ? '' : apply_filters( 'tc_sidebar_socials_title' , sprintf( '<h3 class="widget-title">%1$s</h3>', $_title ) ),
            $_socials
        );
        echo apply_filters( 'tc_social_in_sidebar', $html, current_filter() );
      }




      /**
      * Displays the widget icons if option is enabled in customizer
      * @uses filter tc_footer_widget_wrapper_class
      *
      * @package Customizr
      * @since Customizr 3.2.0
      */
      function czr_fn_set_sidebar_wrapper_widget_class($_original_classes) {
        $_no_icons_classes = array_merge($_original_classes, array('no-widget-icons'));

        if ( 1 == esc_attr( CZR_utils::$inst->czr_fn_opt('tc_show_sidebar_widget_icon' ) ) )
          return ( 0 == esc_attr( CZR_utils::$inst->czr_fn_opt('tc_show_title_icon' ) ) ) ? $_no_icons_classes : $_original_classes;
         //last condition
        return $_no_icons_classes;
      }

  }//end of class
endif;

?>
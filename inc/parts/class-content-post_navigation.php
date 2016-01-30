<?php
/**
* Navigation action
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
if ( ! class_exists( 'TC_post_navigation' ) ) :
  class TC_post_navigation {
      static  $instance;

      function __construct () {
        self::$instance =& $this;

        add_action ( '__after_loop'             , array( $this , 'tc_post_nav' ), 20 );

      }


      /***********************
      * VISIBILITY SETUP
      ***********************/
      /**
      * Set the post navigation visibility based on Customizer options
      *
      * returns an array which contains, @bool whether or not show the navigation , @array css classes of the navigation, @string the context
      * @package Customizr
      * @since Customizr 3.3.22
      */
      function tc_set_visibility_options(){

        $_nav_classes              = array('navigation');
        $_context                  = $this -> tc_get_context();
        $_post_nav_enabled         = $this -> tc_is_post_navigation_enabled();
        $_post_nav_context_enabled = $this -> tc_is_post_navigation_context_enabled( $_context );

        $_is_customizing           = TC___::$instance -> tc_is_customizing() ;

        if ( $_is_customizing ){
          if ( ! $_post_nav_enabled )
            array_push( $_nav_classes, 'hide-all-post-navigation' );
          if ( ! $_post_nav_context_enabled )
            array_push( $_nav_classes, 'hide-post-navigation' );
          $_post_nav_enabled       = true;
        }else
          $_post_nav_enabled       = $_post_nav_enabled && $_post_nav_context_enabled;

        return array(
            apply_filters( 'tc_show_post_navigation', $_post_nav_enabled ),
            implode( ' ', apply_filters( 'tc_show_post_navigation_class' , $_nav_classes ) ),
            $_context
        );
      }

      /**
       * The template part for displaying nav links
       *
       * @package Customizr
       * @since Customizr 3.0
       */
      function tc_post_nav() {

        list( $post_navigation_bool, $post_nav_class, $_context) = $this -> tc_set_visibility_options();

        if( ! $post_navigation_bool )
          return;

        $prev_arrow = is_rtl() ? '&rarr;' : '&larr;' ;
        $next_arrow = is_rtl() ? '&larr;' : '&rarr;' ;
        $html_id = 'nav-below';
        global $wp_query;

        ob_start();
        ?>

        <?php if ( in_array($_context, array('single', 'page') ) ) : ?>

          <?php echo apply_filters( 'tc_singular_nav_separator' , '<hr class="featurette-divider '.current_filter().'">'); ?>

        <nav id="<?php echo $html_id; ?>" class="<?php echo $post_nav_class; ?>" role="navigation">

              <h3 class="assistive-text">
                <?php echo apply_filters( 'tc_singular_nav_title', __( 'Post navigation' , 'customizr' ) ) ; ?>
              </h3>

              <ul class="pager">
                <?php if ( get_previous_post() != null ) : ?>
                  <li class="previous">
                    <span class="nav-previous">
                      <?php
                        $singular_nav_previous_text   = apply_filters( 'tc_singular_nav_previous_text', call_user_func( '_x',  $prev_arrow , 'Previous post link' , 'customizr' ) );
                        $previous_post_link_args      = apply_filters(
                          'tc_previous_single_post_link_args' ,
                          array(
                            'format'        => '%link',
                            'link'          => '<span class="meta-nav">' . $singular_nav_previous_text . '</span> %title',
                            'in_same_term'  => false,
                            'excluded_terms' => '',
                            'taxonomy'      => 'category'
                          )
                        );
                        extract( $previous_post_link_args , EXTR_OVERWRITE );
                        previous_post_link( $format , $link , $in_same_term, $excluded_terms, $taxonomy );
                      ?>
                    </span>
                  </li>
                <?php endif; ?>
                <?php if ( get_next_post() != null ) : ?>
                  <li class="next">
                    <span class="nav-next">
                        <?php
                        $singular_nav_next_text       = apply_filters( 'tc_singular_nav_next_text', call_user_func( '_x', $next_arrow , 'Next post link' , 'customizr' ) );
                        $next_post_link_args      = apply_filters(
                          'tc_next_single_post_link_args' ,
                          array(
                            'format'        => '%link',
                            'link'          => '%title <span class="meta-nav">' . $singular_nav_next_text . '</span>',
                            'in_same_term'  => false,
                            'excluded_terms' => '',
                            'taxonomy'      => 'category'
                          )
                        );
                        extract( $next_post_link_args , EXTR_OVERWRITE );
                        next_post_link( $format , $link , $in_same_term, $excluded_terms, $taxonomy );
                        ?>
                    </span>
                  </li>
                <?php endif; ?>
              </ul>

          </nav><!-- //#<?php echo $html_id; ?> .navigation -->

        <?php elseif ( $wp_query->max_num_pages > 1 && in_array($_context, array('archive', 'home') ) ) : ?>

          <nav id="<?php echo $html_id; ?>" class="<?php echo $post_nav_class; ?>" role="navigation">

            <h3 class="assistive-text">
              <?php echo apply_filters( 'tc_list_nav_title', __( 'Post navigation' , 'customizr' ) ) ; ?>
            </h3>

              <ul class="pager">

                <?php if(get_next_posts_link() != null) : ?>

                  <li class="previous">
                    <span class="nav-previous">
                      <?php
                        $next_posts_link_args      = apply_filters(
                          'tc_next_posts_link_args' ,
                          array(
                            'label'        => apply_filters( 'tc_list_nav_next_text', __( '<span class="meta-nav">&larr;</span> Older posts' , 'customizr' ) ),
                            'max_pages'    => 0
                          )
                        );
                        extract( $next_posts_link_args , EXTR_OVERWRITE );
                        next_posts_link( $label , $max_pages );
                      ?>
                    </span>
                  </li>

                <?php endif; ?>

                <?php if(get_previous_posts_link() != null) : ?>

                  <li class="next">
                    <span class="nav-next">
                      <?php
                        $previous_posts_link_args      = apply_filters(
                          'tc_previous_posts_link_args' ,
                          array(
                            'label'        => apply_filters( 'tc_list_nav_previous_text', __( 'Newer posts <span class="meta-nav">&rarr;</span>' , 'customizr' ) ),
                            'max_pages'    => 0
                          )
                        );
                        extract( $previous_posts_link_args , EXTR_OVERWRITE );
                        previous_posts_link( $label , $max_pages );
                      ?>
                    </span>
                  </li>

                <?php endif; ?>

              </ul>

          </nav><!-- //#<?php echo $html_id; ?> .navigation -->

        <?php endif; ?>

        <?php
        $html = ob_get_contents();
        if ($html) ob_end_clean();
        echo apply_filters( 'tc_post_nav' , $html );
      }



      /******************************
      VARIOUS HELPERS
      *******************************/
      /**
      *
      * @return string or bool
      *
      */
      function tc_get_context(){
        if ( is_page() )
          return 'page';
        if ( is_single() && ! is_attachment() )
          return 'single'; // exclude attachments
        if ( is_home() && 'posts' == get_option('show_on_front') )
          return 'home';
        if ( !is_404() && !tc__f( '__is_home_empty') )
          return 'archive';

        return false;

      }

      /*
      * @param (string or bool) the context
      * @return bool
      */
      function tc_is_post_navigation_context_enabled( $_context ) {
        return $_context && 1 == esc_attr( TC_utils::$inst -> tc_opt( "tc_show_post_navigation_{$_context}" ) );
      }

      /*
      * @return bool
      */
      function tc_is_post_navigation_enabled(){
        return 1 == esc_attr( TC_utils::$inst -> tc_opt( 'tc_show_post_navigation' ) ) ;
      }

  }//end of class
endif;

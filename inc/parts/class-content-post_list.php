<?php
/**
* Posts content actions
*
* 
* @package      Customizr
* @subpackage   classes
* @since        3.0.5
* @author       Nicolas GUILLAUME <nicolas@themesandco.com>
* @copyright    Copyright (c) 2013, Nicolas GUILLAUME
* @link         http://themesandco.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_post_list' ) ) :
  class TC_post_list {
      static $instance;
      function __construct () {
          self::$instance =& $this;
          //displays the article with filtered layout : content + thumbnail
          add_action ( '__loop'                         , array( $this , 'tc_post_list_display'));
          //Include attachments in search results
          add_filter ( 'pre_get_posts'                  , array( $this , 'tc_include_attachments_in_search' ));
          //Include all post types in archive pages
          add_filter ( 'pre_get_posts'                  , array( $this , 'tc_include_cpt_in_lists' ));
          //Set customizer options (since 3.2.0)
          add_action( 'template_redirect'               , array( $this, 'tc_set_post_list_options'));
      }


      /**
      * Controller of the posts list view
      *
      * @package Customizr
      * @since Customizr 3.2.0
      */
      function tc_post_list_controller() {
        global $wp_query;
        //must be archive or search result. Returns false if home is empty in options.
        return ! is_singular()
              && ! is_404()
              && 0 != $wp_query -> post_count
              && ! tc__f( '__is_home_empty');
      }


      /**
      * The default template for displaying posts lists.
      *
      * @package Customizr
      * @since Customizr 3.0.10
      */
      function tc_post_list_display() {
        global $post;
        if ( ! isset($post) || empty($post) || ! apply_filters( 'tc_show_post_in_post_list', $this -> tc_post_list_controller() , $post ) )
          return;

        global $wp_query;
        
        //When do we show the post excerpt?
        //1) when set in options
        //2) + other filters conditions
        $tc_show_post_list_excerpt      = ( 'full' == esc_attr( tc__f( '__get_option' , 'tc_post_list_length' )) ) ? false : true;
        $tc_show_post_list_excerpt      = apply_filters( 'tc_show_post_list_excerpt', $tc_show_post_list_excerpt );
       
        //get the thumbnail data (src, width, height) if any
        $thumb_data                     = TC_post_thumbnails::$instance -> tc_get_thumbnail_data();

        //get the filtered post list layout
        $layout                         = apply_filters( 'tc_post_list_layout', TC_init::$instance -> post_list_layout );

        //when do we display the thumbnail ?
        //1) there must be a thumbnail
        //2) the excerpt option is not set to full
        //3) user settings in customizer
        //4) filter's conditions
        $tc_show_post_list_thumb        = $tc_show_post_list_excerpt ? true : false;
        $tc_show_post_list_thumb        = empty($thumb_data[0]) ? false : $tc_show_post_list_thumb;
        $tc_show_post_list_thumb        = ( 0 == esc_attr( tc__f( '__get_option' , 'tc_post_list_show_thumb' ) ) ) ? false : $tc_show_post_list_thumb;
        $tc_show_post_list_thumb        = apply_filters( 'tc_show_post_list_thumb', $tc_show_post_list_thumb );
       
        //what is determining the layout ? if no thumbnail then full width + filter's conditions
        $post_list_content_class        = $tc_show_post_list_thumb  ? $layout['content'] : 'span12';
        $post_list_content_class        = implode( " " , apply_filters( 'tc_post_list_content_class', array($post_list_content_class) , $tc_show_post_list_thumb , $layout ) );

        //Renders the filtered layout for content + thumbnail
        if ( isset($layout['alternate']) && $layout['alternate'] ) {
          if ( 0 == $wp_query->current_post % 2 ) {
            $this -> tc_display_post_content($post_list_content_class, $tc_show_post_list_excerpt);
            $tc_show_post_list_thumb ? TC_post_thumbnails::$instance -> tc_display_post_thumbnail( $thumb_data , $layout['thumb'] ) : false;
          }
          else {
            $tc_show_post_list_thumb ? TC_post_thumbnails::$instance -> tc_display_post_thumbnail( $thumb_data , $layout['thumb'] ) : false;
            $this -> tc_display_post_content($post_list_content_class, $tc_show_post_list_excerpt);
          }
        } else if ( isset($layout['show_thumb_first']) && !$layout['show_thumb_first'] ) {
            $this -> tc_display_post_content($post_list_content_class, $tc_show_post_list_excerpt);
            $tc_show_post_list_thumb ? TC_post_thumbnails::$instance -> tc_display_post_thumbnail( $thumb_data , $layout['thumb'] ) : false;
         
        }
        else {
          $tc_show_post_list_thumb ? TC_post_thumbnails::$instance -> tc_display_post_thumbnail( $thumb_data , $layout['thumb'] ) : false;
          $this -> tc_display_post_content($post_list_content_class, $tc_show_post_list_excerpt);
        }

        //renders the hr separator after each article
        echo apply_filters( 'tc_post_list_separator', '<hr class="featurette-divider '.current_filter().'">' );

      }






      /**
      * Displays the posts list content
      *
      * @package Customizr
      * @since Customizr 3.0
      */
      function tc_display_post_content( $post_list_content_class, $tc_show_post_list_excerpt ) {
        ob_start();
        ?>
        <section class="tc-content <?php echo $post_list_content_class; ?>">
            <?php do_action( '__before_content' ); ?>
            
            <?php //display an icon for div if there is no title
                    $icon_class = in_array(get_post_format(), array(  'quote' , 'aside' , 'status' , 'link' )) ? apply_filters( 'tc_post_list_content_icon', 'format-icon' ) :'';
                ?>
            <?php if (!get_post_format()) :  // Only display Excerpts for lists of posts with format different than quote, status, link, aside ?>
                
                <section class="entry-summary">
                    <?php if ( !$tc_show_post_list_excerpt ) : ?>
                      <?php the_content(); ?>
                    <?php else : ?>
                      <?php the_excerpt(); ?>
                    <?php endif; ?>
                </section><!-- .entry-summary -->
            
            <?php elseif ( in_array(get_post_format(), array( 'image' , 'gallery' ))) : ?>
                
                <section class="entry-content">
                    <p class="format-icon"></p>
                </section><!-- .entry-content -->
            
            <?php else : ?>
            
                <section class="entry-content <?php echo $icon_class ?>">
                    <?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>' , 'customizr' ) ); ?>
                    <?php wp_link_pages( array( 'before' => '<div class="pagination pagination-centered">' . __( 'Pages:' , 'customizr' ), 'after' => '</div>' ) ); ?>
                </section><!-- .entry-content -->
            <?php endif; ?>

            <?php do_action( '__after_content' ) ?>

        </section>

        <?php
        $html = ob_get_contents();
        if ($html) ob_end_clean();
        echo apply_filters( 'tc_post_list_content', $html, $post_list_content_class, $tc_show_post_list_excerpt );
      }


      /**
      * Includes Custom Posts Types (set to public and excluded_from_search_result = false) in archives and search results
      * In archives, it handles the case where a CPT has been registered and associated with an existing built-in taxonomy like category or post_tag
      *
      * @package Customizr
      * @since Customizr 3.1.20
      */
      function tc_include_cpt_in_lists($query) {
        if ( 
          is_admin()
          || ! $query->is_main_query()
          || ! apply_filters('tc_include_cpt_in_archives' , false)
          || ! ( $query->is_search || $query->is_archive )
          )
          return;

        //filter the post types to include, they must be public and not excluded from search
        $post_types     = get_post_types( array( 'public' => true, 'exclude_from_search' => false) );
        
        $query->set('post_type', $post_types );
      }



      /**
      * Includes attachments in search results
      *
      * @package Customizr
      * @since Customizr 3.0.10
      */
      function tc_include_attachments_in_search( $query ) {
          if (! is_search() || ! apply_filters( 'tc_include_attachments_in_search_results' , true ) )
            return $query;    

          // add post status 'inherit' 
          $post_status = $query->get( 'post_status' );
          if ( ! $post_status || 'publish' == $post_status )
            $post_status = array( 'publish', 'inherit' );
          if ( is_array( $post_status ) ) 
            $post_status[] = 'inherit';

          $query->set( 'post_status', $post_status );
         
          return $query;
      }

      
      /**
      * Set hooks for the customizer options
      * callback of template_redirect hook
      *
      * @package Customizr
      * @since Customizr 3.2.0
      */
      function tc_set_post_list_options() {
        if ( ! $this -> tc_post_list_controller() )
          return;
        add_filter ( 'tc_post_list_layout'            , array( $this , 'tc_set_post_list_layout'));
        add_filter ( 'post_class'                     , array( $this , 'tc_set_content_class'));
        add_filter ( 'excerpt_length'                 , array( $this , 'tc_set_excerpt_length') , 999 );
        add_filter ( 'post_class'                     , array( $this , 'tc_add_thumb_shape_name'));
      }
      


      /**
      * Callback of filter post_class
      *
      * @package Customizr
      * @since Customizr 3.2.0
      */
      function tc_add_thumb_shape_name( $_classes ) {
        return array_merge( $_classes , array(esc_attr( tc__f( '__get_option' , 'tc_post_list_thumb_shape') ) ) );
      }



      /**
      * callback of excerpt_length hook
      *
      * @package Customizr
      * @since Customizr 3.2.0
      */
      function tc_set_excerpt_length( $length ) {
        $_custom = esc_attr( tc__f( '__get_option' , 'tc_post_list_excerpt_length' ) );
        return ( false === $_custom || !is_numeric($_custom) ) ? $length : $_custom;
      }



      /**
      * Callback of filter tc_post_list_layout
      *
      * @package Customizr
      * @since Customizr 3.2.0
      */
      function tc_set_post_list_layout($layout) {
        $_position                  = esc_attr( tc__f( '__get_option' , 'tc_post_list_thumb_position' ) );
        $layout['alternate']        = ( 0 == esc_attr( tc__f( '__get_option' , 'tc_post_list_thumb_alternate' ) ) ) ? false : true;
        $layout['show_thumb_first'] = ( 'left' == $_position || 'top' == $_position ) ? true : false;
        $layout['content']          = ( 'left' == $_position || 'right' == $_position ) ? $layout['content'] : 'span12';
        $layout['thumb']            = ( 'top' == $_position || 'bottom' == $_position ) ? 'span12' : $layout['thumb'];
        return $layout;
      }



      /**
      * Callback of WP filter post_class
      *
      * @package Customizr
      * @since Customizr 3.2.0
      */
      function tc_set_content_class( $_classes ) {
        $_position                  = esc_attr( tc__f( '__get_option' , 'tc_post_list_thumb_position' ) );
        return array_merge($_classes , array( "thumb-position-{$_position}"));
      }

  }//end of class
endif;
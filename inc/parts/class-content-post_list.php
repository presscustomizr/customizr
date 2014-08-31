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
       * The default template for displaying posts lists.
       *
       * @package Customizr
       * @since Customizr 3.0.10
       */
      function tc_post_list_display() {
        global $wp_query;

        //must be archive or search result. Returns false if home is empty in options.
        if ( is_singular() || is_404() || (is_search() && 0 == $wp_query -> post_count) || tc__f( '__is_home_empty') )
          return;

        //When do we show the post excerpt?
        //1) when set in options
        //2) + other filters conditions
        $tc_show_post_list_excerpt      = ( 'full' == esc_attr( tc__f( '__get_option' , 'tc_post_list_length' )) ) ? false : true;
        $tc_show_post_list_excerpt      = apply_filters( 'tc_show_post_list_excerpt', $tc_show_post_list_excerpt );
       
        //we get the thumbnail data (src, width, height) if any
        $thumb_data                     = $this -> tc_get_post_list_thumbnail();

        //gets the filtered post list layout
        $layout                         = apply_filters( 'tc_post_list_layout', TC_init::$instance -> post_list_layout );

        //when do we display the thumbnail ?
        //1) there must be a thumbnail
        //2) the excerpt option is not set to full
        //3) filter's conditions
        $tc_show_post_list_thumb        = $tc_show_post_list_excerpt ? true : false;
        $tc_show_post_list_thumb        = empty($thumb_data[0]) ? false : $tc_show_post_list_thumb;
        $tc_show_post_list_thumb        = apply_filters( 'tc_show_post_list_thumb', $tc_show_post_list_thumb );
       
        //what is determining the layout ? if no thumbnail then full width + filter's conditions
        $post_list_content_class        = $tc_show_post_list_thumb  ? $layout['content'] : 'span12';
        $post_list_content_class        = apply_filters( 'tc_post_list_content_class', $post_list_content_class , $tc_show_post_list_thumb );

        //Renders the filtered layout for content + thumbnail
        if ( isset($layout['alternate']) && $layout['alternate'] ) {
          if ( 0 == $wp_query->current_post % 2 ) {
            $this -> tc_post_list_content($post_list_content_class, $tc_show_post_list_excerpt);
            $tc_show_post_list_thumb ? $this -> tc_post_list_thumbnail( $thumb_data , $layout['thumb'] ) : false;
          }
          else {
            $tc_show_post_list_thumb ? $this -> tc_post_list_thumbnail( $thumb_data , $layout['thumb'] ) : false;
            $this -> tc_post_list_content($post_list_content_class, $tc_show_post_list_excerpt);
          }
        } else if ( isset($layout['show_thumb_first']) && !$layout['show_thumb_first'] ) {
            $this -> tc_post_list_content($post_list_content_class, $tc_show_post_list_excerpt);
            $tc_show_post_list_thumb ? $this -> tc_post_list_thumbnail( $thumb_data , $layout['thumb'] ) : false;
         
        }
        else {
          $tc_show_post_list_thumb ? $this -> tc_post_list_thumbnail( $thumb_data , $layout['thumb'] ) : false;
          $this -> tc_post_list_content($post_list_content_class, $tc_show_post_list_excerpt);
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
      function tc_post_list_content( $post_list_content_class, $tc_show_post_list_excerpt ) {
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
        * Gets the thumbnail or the first images attached to the post if any
        *
        * @package Customizr
        * @since Customizr 1.0
        */
        function tc_get_post_list_thumbnail() {

          //output vars declaration
          $tc_thumb            = '';
          $tc_thumb_height      = '';
          $tc_thumb_width       = '';

          //define the default thumb size
          $tc_thumb_size                  = 'tc-thumb';

          //define the default thumnail if has thumbnail
          if (has_post_thumbnail()) {
              $tc_thumb_id                = get_post_thumbnail_id();

              //check if tc-thumb size exists for attachment and return large if not
              $image                      = wp_get_attachment_image_src( $tc_thumb_id, $tc_thumb_size);
              if (null == $image[3]) {
                $tc_thumb_size            = 'medium';
               }
              $tc_thumb                   = get_the_post_thumbnail( get_the_ID(),$tc_thumb_size);
              //get height and width
              $tc_thumb_height            = $image[2];
              $tc_thumb_width             = $image[1];
          }

          //check if no thumbnail then uses the first attached image if any
          else {
            //Case if we display a post or a page
             if ( 'attachment' != tc__f('__post_type') ) {
               //look for attachements in post or page
               $tc_args = array(
                          'numberposts'             =>  1,
                          'post_type'               =>  'attachment' ,
                          'post_status'             =>  null,
                          'post_parent'             =>  get_the_ID(),
                          'post_mime_type'          =>  array( 'image/jpeg' , 'image/gif' , 'image/jpg' , 'image/png' )
                  );

                $attachments              = get_posts( $tc_args);
              }

              //case were we display an attachment (in search results for example)
              elseif ( 'attachment' == tc__f('__post_type') && wp_attachment_is_image() ) {
                $attachments = array( get_post() );
              }


            if ( isset($attachments) ) {
              foreach ( $attachments as $attachment) {
                 //check if tc-thumb size exists for attachment and return large if not
                $image                = wp_get_attachment_image_src( $attachment->ID, $tc_thumb_size);
                if (false == $image[3]) {
                  $tc_thumb_size      = 'medium';
                 }
                $tc_thumb             = wp_get_attachment_image( $attachment->ID, $tc_thumb_size);
                //get height and width
                $tc_thumb_height      = $image[2];
                $tc_thumb_width       = $image[1];
              }
            }
          }

          //the current post id is included in the array of parameters for a better granularity.
          return apply_filters( 'tc_get_post_list_thumbnail' , array( $tc_thumb, $tc_thumb_width, $tc_thumb_height ), tc__f('__ID') );

        }//end of function
          



        /**
        * Displays the thumbnail or the first images attached to the post if any
        * Takes 2 parameters : thumbnail data array (img, width, height) and layout value
        * 
        * @package Customizr
        * @since Customizr 3.0.10
        */
        function tc_post_list_thumbnail( $thumb_data , $layout ) {
          $thumb_img                  = !isset( $thumb_data) ? false : $thumb_data[0];
          $thumb_img                  = apply_filters( 'tc_post_thumb_img', $thumb_img, tc__f('__ID') );
          if ( ! $thumb_img )
            return;

          //handles the case when the image dimensions are too small
          $thumb_size                 = apply_filters( 'tc_thumb_size' , TC_init::$instance -> tc_thumb_size, tc__f('__ID')  );
          $no_effect_class            = ( isset($thumb_data[0]) && isset($thumb_data[1]) && ( $thumb_data[1] < $thumb_size['width']) ) ? 'no-effect' : '';
          $no_effect_class            = apply_filters( 'tc_no_round_thumb', $no_effect_class, tc__f('__ID') );

          //default hover effect
          $thumb_wrapper              = sprintf('<div class="thumb-wrapper %1$s"><div class="round-div"></div><a class="round-div %1$s" href="%2$s" title="%3$s"></a>%4$s</div>',
                                        $no_effect_class,
                                        get_permalink( get_the_ID() ),
                                        get_the_title( get_the_ID() ),
                                        $thumb_img
          );
          $thumb_wrapper              = apply_filters_ref_array( 'tc_post_thumb_wrapper', array( $thumb_wrapper, $thumb_img, tc__f('__ID') ) );

          //renders the thumbnail
          $html = sprintf('<section class="tc-thumbnail %1$s">%2$s</section>',
            apply_filters( 'tc_post_thumb_class', $layout ),
            $thumb_wrapper
          );

          echo apply_filters_ref_array( 'tc_post_list_thumbnail', array( $html, $thumb_data, $layout ) );

        }//end of function




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
  }//end of class
endif;
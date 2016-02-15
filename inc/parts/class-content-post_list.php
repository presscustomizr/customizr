<<<<<<< HEAD
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
=======
<?php
/**
* Posts content actions
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.0.5
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_post_list' ) ) :
class TC_post_list {
  static $instance;
  function __construct () {
    self::$instance =& $this;
    //Set new image size can be set here ( => wp hook would be too late) (since 3.2.0)
    add_action( 'init'                    , array( $this, 'tc_set_thumb_early_options') );
    //modify the query with pre_get_posts
    //! wp_loaded is fired after WordPress is fully loaded but before the query is set
    add_action( 'wp_loaded'               , array( $this, 'tc_set_early_hooks') );
    //Set __loop hooks and customizer options (since 3.2.0)
    add_action( 'wp_head'                 , array( $this, 'tc_set_post_list_hooks'));
    //append inline style to the custom stylesheet
    //! tc_user_options_style filter is shared by several classes => must always check the local context inside the callback before appending new css
    //fired on hook : wp_enqueue_scripts
    //Set thumbnail specific design based on user options
    add_filter( 'tc_user_options_style'   , array( $this , 'tc_write_thumbnail_inline_css') );
  }



  /***************************
  * POST LIST HOOKS SETUP
  ****************************/
  /**
  * hook : init
  * @return void
  *
  * @package Customizr
  * @since Customizr 3.2.6
  */
  function tc_set_thumb_early_options() {
    //Set thumb size depending on the customizer thumbnail position options (since 3.2.0)
    add_filter ( 'tc_thumb_size_name'     , array( $this , 'tc_set_thumb_size') );
  }


  /**
  * Set __loop hooks and various filters based on customizer options
  * hook : wp_loaded
  *
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function tc_set_early_hooks() {
    //Filter home/blog postsa (priority 9 is to make it act before the grid hook for expanded post)
    add_action ( 'pre_get_posts'         , array( $this , 'tc_filter_home_blog_posts_by_tax' ), 9);
    //Include attachments in search results
    add_action ( 'pre_get_posts'         , array( $this , 'tc_include_attachments_in_search' ));
    //Include all post types in archive pages
    add_action ( 'pre_get_posts'         , array( $this , 'tc_include_cpt_in_lists' ));
  }


  /**
  * Set __loop hooks and various filters based on customizer options
  * hook : wp_head
  *
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function tc_set_post_list_hooks() {
    if ( ! $this -> tc_post_list_controller() )
      return;
    //displays the article with filtered layout : content + thumbnail
    add_action ( '__loop'               , array( $this , 'tc_prepare_section_view') );
    
    //page help blocks
    add_filter( '__before_loop'         , array( $this , 'tc_maybe_display_img_smartload_help') );

    //based on customizer user options
    add_filter( 'tc_post_list_layout'   , array( $this , 'tc_set_post_list_layout') );
    add_filter( 'post_class'            , array( $this , 'tc_set_content_class') );
    add_filter( 'excerpt_length'        , array( $this , 'tc_set_excerpt_length') , 999 );
    add_filter( 'post_class'            , array( $this , 'tc_add_thumb_shape_name') );

    //add current context to the body class
    add_filter( 'body_class'            , array( $this , 'tc_add_post_list_context') );
    //Set thumb shape with customizer options (since 3.2.0)
    add_filter( 'tc_post_thumb_wrapper' , array( $this , 'tc_set_thumb_shape'), 10 , 2 );

    add_filter( 'tc_the_content'        , array( $this , 'tc_add_support_for_shortcode_special_chars') );

    // => filter the thumbnail inline style tc_post_thumb_inline_style and replace width:auto by width:100%
    // 3 args = $style, $_width, $_height
    add_filter( 'tc_post_thumb_inline_style'  , array( $this , 'tc_change_thumbnail_inline_css_width'), 20, 3 );
  }


  /***************************
  * POST LIST MODEL
  ****************************/
  /**
  * Prepare default posts lists view
  * hook : __loop
  * inside loop
  * @package Customizr
  * @since Customizr 3.0.10
  */
  function tc_prepare_section_view() {
    global $post;
    if ( ! isset( $post ) || empty( $post ) || ! apply_filters( 'tc_show_post_in_post_list', $this -> tc_post_list_controller() , $post ) )
      return;

    //get the filtered post list layout
    $_layout        = apply_filters( 'tc_post_list_layout', TC_init::$instance -> post_list_layout );
    $_content_model = $this -> tc_get_content_model( $_layout );
    $_thumb_model   = $this -> tc_show_thumb() ? TC_post_thumbnails::$instance -> tc_get_thumbnail_model() : array();

    $this -> tc_render_section_view( $_layout, $_content_model, $_thumb_model );
  }


  /**
  * Return the default post list model for the content
  * inside loop
  * @return array() "_layout" , "_show_thumb" , "_css_class"
  * @package Customizr
  * @since Customizr 3.3.2
  */
  private function tc_get_content_model($_layout) {
    $_content      = '';
    if ( $this -> tc_show_excerpt() )
      $_content = apply_filters( 'the_excerpt', get_the_excerpt() );
    else
      $_content = apply_filters( 'tc_the_content', get_the_content() );

    //what is determining the layout ? if no thumbnail then full width + filter's conditions
    $_layout_class = $this -> tc_show_thumb() ? $_layout['content'] : 'span12';
    $_layout_class = implode( " " , apply_filters( 'tc_post_list_content_class', array($_layout_class) , $this -> tc_show_thumb() , $_layout ) );

    //display an icon for div if there is no title
    $_icon_class    = in_array(get_post_format(), array(  'quote' , 'aside' , 'status' , 'link' )) ? apply_filters( 'tc_post_list_content_icon', 'format-icon' ) :'';

    return compact( "_layout_class" , "_icon_class" , "_content" );
  }




  /**
  * @return boolean whether excerpt instead of full content
  * @package Customizr
  * @since Customizr 3.3.2
  */
  private function tc_show_excerpt() {
    //When do we show the post excerpt?
    //1) when set in options
    //2) + other filters conditions
    return (bool) apply_filters( 'tc_show_excerpt', 'full' != esc_attr( TC_utils::$inst->tc_opt( 'tc_post_list_length' ) ) );
  }


  /**
  * @return boolean
  * @package Customizr
  * @since Customizr 3.3.2
  */
  private function tc_show_thumb() {
    //when do we display the thumbnail ?
    //1) there must be a thumbnail
    //2) the excerpt option is not set to full
    //3) user settings in customizer
    //4) filter's conditions
    return apply_filters( 'tc_show_thumb', array_product(
        array(
          $this -> tc_show_excerpt(),
          TC_post_thumbnails::$instance -> tc_has_thumb(),
          0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_post_list_show_thumb' ) )
        )
      )
    );
  }


  /***************************
  * POST LIST VIEW
  ****************************/
  /**
  * Render each post list section view
  *
  * @package Customizr
  * @since Customizr 3.0.10
  */
  private function tc_render_section_view( $_layout, $_content_model, $_thumb_model ) {
    global $wp_query;
    //Renders the filtered layout for content + thumbnail
    if ( isset($_layout['alternate']) && $_layout['alternate'] ) {
      if ( 0 == $wp_query->current_post % 2 ) {
        $this -> tc_render_content_view( $_content_model ) ;
        TC_post_thumbnails::$instance -> tc_render_thumb_view( $_thumb_model , $_layout['thumb'] );
      }
      else {
        TC_post_thumbnails::$instance -> tc_render_thumb_view( $_thumb_model , $_layout['thumb'] );
        $this -> tc_render_content_view( $_content_model );
      }
    }
    else if ( isset($_layout['show_thumb_first']) && ! $_layout['show_thumb_first'] ) {
        $this -> tc_render_content_view( $_content_model );
        TC_post_thumbnails::$instance -> tc_render_thumb_view( $_thumb_model , $_layout['thumb'] );
    }
    else {
      TC_post_thumbnails::$instance -> tc_render_thumb_view( $_thumb_model , $_layout['thumb'] );
      $this -> tc_render_content_view( $_content_model );
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
  private function tc_render_content_view( $_content_model ) {
    //extract "_layout_class" , "_icon_class" , "_content"
    extract($_content_model);
    $_sub_class = 'entry-summary';

    if ( in_array( get_post_format(), array( 'image' , 'gallery' ) ) )
    {
      $_sub_class = 'entry-content';
      $_content   = '<p class="format-icon"></p>';
    }
    elseif ( in_array( get_post_format(), array( 'quote', 'status', 'link', 'aside', 'video' ) ) )
    {
      $_sub_class = sprintf( 'entry-content %s' , $_icon_class );
      $_content   = sprintf( '%1$s%2$s',
        apply_filters( 'tc_the_content', get_the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>' , 'customizr' ) ) ),
        wp_link_pages( array(
          'before'  => '<div class="pagination pagination-centered">' . __( 'Pages:' , 'customizr' ),
          'after'   => '</div>',
          'echo'    => 0
          ) )
      );
    }

    ob_start();
    ?>
    <section class="tc-content <?php echo $_layout_class; ?>">
      <?php
        do_action( '__before_content' );

          printf('<section class="%1$s">%2$s</section>',
            $_sub_class,
            $_content
          );

        do_action( '__after_content' );
    ?>
    </section>
    <?php
    $_html = ob_get_contents();
    if ($_html) ob_end_clean();
    echo apply_filters( 'tc_post_list_content', $_html, $_content_model );
  }



  /******************************
  * SETTERS / HELPERS / CALLBACKS
  *******************************/
  /**
  * hook : tc_post_thumb_wrapper
  * ! 2 cases here : posts lists and single posts
  *
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function tc_set_thumb_shape( $thumb_wrapper, $thumb_img ) {
    $_shape = esc_attr( TC_utils::$inst->tc_opt( 'tc_post_list_thumb_shape') );

    //1) check if shape is rounded, squared on rectangular
    if ( ! $_shape || false !== strpos($_shape, 'rounded') || false !== strpos($_shape, 'squared') )
      return $thumb_wrapper;

    $_position = esc_attr( TC_utils::$inst->tc_opt( 'tc_post_list_thumb_position' ) );
    return sprintf('<div class="%4$s"><a class="tc-rectangular-thumb" href="%1$s" title="%2s">%3$s</a></div>',
          get_permalink( get_the_ID() ),
          esc_attr( strip_tags( get_the_title( get_the_ID() ) ) ),
          $thumb_img,
          ( 'top' == $_position || 'bottom' == $_position ) ? '' : implode( " ", apply_filters( 'tc_thumb_wrapper_class', array('') ) )
    );
  }


  /**
  * hook : body_class
  * @return  array of classes
  *
  * @package Customizr
  * @since Customizr 3.3.2
  */
  function tc_add_post_list_context( $_classes ) {
    return array_merge( $_classes , array( 'tc-post-list-context' ) );
  }


  /**
  * @return  bool
  * Controller of the posts list view
  *
  * @package Customizr
  * @since Customizr 3.2.0
  */
  public function tc_post_list_controller() {
    global $wp_query;
    //must be archive or search result. Returns false if home is empty in options.
    return apply_filters( 'tc_post_list_controller',
      ! is_singular()
      && ! is_404()
      && 0 != $wp_query -> post_count
      && ! tc__f( '__is_home_empty')
    );
  }


  /**
  * hook : pre_get_posts
  * Includes Custom Posts Types (set to public and excluded_from_search_result = false) in archives and search results
  * In archives, it handles the case where a CPT has been registered and associated with an existing built-in taxonomy like category or post_tag
  * @return modified query object
  * @package Customizr
  * @since Customizr 3.1.20
  */
  function tc_include_cpt_in_lists( $query ) {
    if (
      is_admin()
      || ! $query->is_main_query()
      || ! apply_filters('tc_include_cpt_in_archives' , false)
      || ! ( $query->is_search || $query->is_archive )
      )
      return;

    //filter the post types to include, they must be public and not excluded from search
    //we also exclude the built-in types, to exclude pages and attachments, we'll add standard posts later
    $post_types         = get_post_types( array( 'public' => true, 'exclude_from_search' => false, '_builtin' => false) );
    
    //add standard posts
    $post_types['post'] = 'post';
    if ( $query -> is_search ){
      // add standard pages in search results => new wp behavior
      $post_types['page'] = 'page';
      // allow attachments to be included in search results by tc_include_attachments_in_search method
      if ( apply_filters( 'tc_include_attachments_in_search_results' , false ) )      
        $post_types['attachment'] = 'attachment';    
    }
    
    // add standard pages in search results
    $query->set('post_type', $post_types );
  }


  /**
  * hook : pre_get_posts
  * Includes attachments in search results
  * @return modified query object
  * @package Customizr
  * @since Customizr 3.0.10
  */
  function tc_include_attachments_in_search( $query ) {
      if (! is_search() || ! apply_filters( 'tc_include_attachments_in_search_results' , false ) )
        return;

      // add post status 'inherit'
      $post_status = $query->get( 'post_status' );
      if ( ! $post_status || 'publish' == $post_status )
        $post_status = array( 'publish', 'inherit' );
      if ( is_array( $post_status ) )
        $post_status[] = 'inherit';

      $query->set( 'post_status', $post_status );
  }

  /**
  * hook : pre_get_posts
  * Filter home/blog posts by tax: cat
  * @return modified query object
  * @package Customizr
  * @since Customizr 3.4.10
  */
  function tc_filter_home_blog_posts_by_tax( $query ) {
      // when we have to filter?
      // in home and blog page
      if (
        ! $query->is_main_query()
        || ! ( ( is_home() && 'posts' == get_option('show_on_front') ) || $query->is_posts_page )
      )
        return;

     // categories
     // we have to ignore sticky posts (do not prepend them) 
     // disable grid sticky post expansion
     $cats = TC_utils::$inst -> tc_opt('tc_blog_restrict_by_cat');
     $cats = array_filter( $cats, array( TC_utils::$inst , 'tc_category_id_exists' ) ); 
     
     if ( is_array( $cats ) && ! empty( $cats ) ){
         $query->set('category__in', $cats );     
         $query->set('ignore_sticky_posts', 1 );     
         add_filter('tc_grid_expand_featured', '__return_false');
     }
  }
  /**
  * Callback of filter post_class
  * @return  array() of classes
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function tc_add_thumb_shape_name( $_classes ) {
    return array_merge( $_classes , array(esc_attr( TC_utils::$inst->tc_opt( 'tc_post_list_thumb_shape') ) ) );
  }


  /**
  * hook : excerpt_length hook
  * @return string
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function tc_set_excerpt_length( $length ) {
    $_custom = esc_attr( TC_utils::$inst->tc_opt( 'tc_post_list_excerpt_length' ) );
    return ( false === $_custom || !is_numeric($_custom) ) ? $length : $_custom;
  }


  /**
  * hook : tc_post_list_layout
  * @return array() of layout data
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function tc_set_post_list_layout( $_layout ) {
    $_position                  = esc_attr( TC_utils::$inst->tc_opt( 'tc_post_list_thumb_position' ) );
    //since 3.4.16 the alternate layout is not available when the position is top or bottom
    $_layout['alternate']        = ( 0 == esc_attr( TC_utils::$inst->tc_opt( 'tc_post_list_thumb_alternate' ) ) 
                                   || in_array( $_position, array( 'top', 'bottom') ) ) ? false : true;
    $_layout['show_thumb_first'] = ( 'left' == $_position || 'top' == $_position ) ? true : false;
    $_layout['content']          = ( 'left' == $_position || 'right' == $_position ) ? $_layout['content'] : 'span12';
    $_layout['thumb']            = ( 'top' == $_position || 'bottom' == $_position ) ? 'span12' : $_layout['thumb'];
    return $_layout;
  }


  /**
  * hook : WP filter post_class
  * @return array() of classes
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function tc_set_content_class( $_classes ) {
    $_position                  = esc_attr( TC_utils::$inst->tc_opt( 'tc_post_list_thumb_position' ) );
    return array_merge( $_classes , array( "thumb-position-{$_position}") );
  }


  /**
  * hook tc_post_thumb_inline_style (declared in TC_post_thumbnails)
  * Replace default widht:auto by width:100%
  * @param array of args passed by apply_filters_ref_array method
  * @return  string
  *
  * @package Customizr
  * @since Customizr 3.2.6
  */
  function tc_change_thumbnail_inline_css_width( $_style,  $image, $_filtered_thumb_size) {
    //conditions :
    //note : handled with javascript if tc_center_img option enabled
    $_bool = array_product(
      array(
        ! esc_attr( TC_utils::$inst->tc_opt( 'tc_center_img') ),
        false != $image,
        ! empty($image),
        isset($_filtered_thumb_size['width']),
        isset($_filtered_thumb_size['height'])
      )
    );
    if ( ! $_bool )
      return $_style;

    $_width     = $_filtered_thumb_size['width'];
    $_height    = $_filtered_thumb_size['height'];
    $_shape     = esc_attr( TC_utils::$inst->tc_opt( 'tc_post_list_thumb_shape') );
    $_is_rectangular = ! $_shape || false !== strpos($_shape, 'rounded') || false !== strpos($_shape, 'squared') ? false : true;
    if ( ! is_single() && ! $_is_rectangular )
      return $_style;

    return sprintf('min-width:%1$spx;min-height:%2$spx;max-width: none;width:100%%;max-height: none;', $_width, $_height );
  }


  /**
  * hook : tc_user_options_style
  * @return css string
  *
  * @package Customizr
  * @since Customizr 3.2.6
  */
  function tc_write_thumbnail_inline_css( $_css ) {
    if ( ! $this -> tc_post_list_controller() )
      return $_css;
    $_list_thumb_height     = esc_attr( TC_utils::$inst->tc_opt( 'tc_post_list_thumb_height' ) );
    $_list_thumb_height     = (! $_list_thumb_height || ! is_numeric($_list_thumb_height) ) ? 250 : $_list_thumb_height;

    return sprintf("%s\n%s",
      $_css,
      ".tc-rectangular-thumb {
        max-height: {$_list_thumb_height}px;
        height :{$_list_thumb_height}px
      }\n"
    );
  }


  /**
  * hook : tc_thumb_size_name (declared in TC_post_thumbnails)
  *
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function tc_set_thumb_size( $_default_size ) {
    $_shape = esc_attr( TC_utils::$inst->tc_opt( 'tc_post_list_thumb_shape') );
    if ( ! $_shape || false !== strpos($_shape, 'rounded') || false !== strpos($_shape, 'squared') )
      return $_default_size;

    $_position                  = esc_attr( TC_utils::$inst->tc_opt( 'tc_post_list_thumb_position' ) );
    return ( 'top' == $_position || 'bottom' == $_position ) ? 'tc_rectangular_size' : $_default_size;
  }


  /**
  * hook : tc_the_content
  * Applies tc_the_content filter to the passed string
  *
  * @param string
  * @return  string
  *
  * @package Customizr
  * @since Customizr 3.3+
  */
  function tc_add_support_for_shortcode_special_chars( $_content ) {
    return str_replace( ']]>', ']]&gt;', apply_filters( 'the_content', $_content ) );
  }


  /***************************
  * LIST OF POSTS IMG SMARTLOAD HELP VIEW
  ****************************/
  /**
  * Displays a help block about images smartload for list of posts before the actual list
  * hook : __before_loop
  * @since Customizr 3.4+
  */
  function tc_maybe_display_img_smartload_help( $the_content ) {
    if ( ! ( $this -> tc_post_list_controller() && TC_placeholders::tc_is_img_smartload_help_on( $text = '', $min_img_num = 0 ) ) )
      return;
    
    TC_placeholders::tc_get_smartload_help_block( $echo = true );
  }

}//end of class
endif;
>>>>>>> upstream/master

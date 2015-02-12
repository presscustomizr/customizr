<?php
/**
* Post lists grid content actions
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.2.11
* @author       Rocco Aliberti <rocco@themesandco.com>, Nicolas GUILLAUME <nicolas@themesandco.com>
* @copyright    Copyright (c) 2015, Rocco Aliberti, Nicolas GUILLAUME
* @link         http://themesandco.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_post_list_grid' ) ) :
    class TC_post_list_grid {
        static $instance;
        private $has_expanded_featured;
        function __construct () {
            self::$instance =& $this;
            $this -> has_expanded_featured = false;
            add_action ( 'pre_get_posts'            , array( $this , 'tc_maybe_excl_first_sticky') );
            add_action ( 'wp'                       , array( $this , 'tc_set_grid_hooks') );
        }


        /***************************************
        * HOOKS SETTINGS $$*********************
        ****************************************/
        /*
        * hook : wp
        */
        function tc_set_grid_hooks(){
          if ( ! $this -> tc_is_grid_enabled() )
              return;

          do_action( '__post_list_grid' );
          add_filter( 'tc_user_options_style'       , array( $this , 'tc_grid_write_inline_css'), 100 );
          // pre loop hooks
          add_action( '__before_article_container'  , array( $this, 'tc_set_grid_before_loop_hooks'), 5 );
          // loop hooks
          add_action( '__before_article'            , array( $this, 'tc_set_grid_loop_hooks'), 0 );
        }


        /*
        * hook : __before_article_container
        * before loop
        */
        function tc_set_grid_before_loop_hooks(){
          // LAYOUT
          add_filter( 'tc_post_list_layout'         , array( $this, 'tc_grid_set_content_layout') );
          add_filter( 'tc_post_list_selectors'      , array( $this, 'tc_grid_set_article_selectors') );
          add_action( '__before_article_container'  , array( $this, 'tc_grid_prepare_expand_featured' ) );

          // THUMBNAILS
          remove_filter( 'post_class'               , array( TC_post_list::$instance , 'tc_add_thumb_shape_name'));
          remove_filter( 'tc_thumb_size_name'       , array( TC_post_thumbnails::$instance, 'tc_set_thumb_size') );
          add_filter( 'tc_thumb_size_name'          , array( $this, 'tc_set_thumb_size_name') );
          add_filter( 'tc_thumb_size'               , array( $this, 'tc_set_thumb_size') );
          //remove inline style for grid thumbs (handled in javascript on page load)
          add_filter( 'tc_post_thumb_inline_style'  , '__return_false' );

          // force title displaying for all post types
          add_filter( 'tc_post_formats_with_no_heading', '__return_empty_array');

          // SINGLE POST CONTENT IN GRID
          $_content_priorities = apply_filters('tc_grid_post_content_priorities' , array( 'featured_title' => 10, 'content' => 20, 'link' =>30 ));
          add_action( '__grid_single_post_content'  , array( $this, 'tc_grid_display_expanded_post_title'), $_content_priorities['featured_title'] );
          add_action( '__grid_single_post_content'  , array( $this, 'tc_grid_display_figcaption_content') , $_content_priorities['content'] );
          add_action( '__grid_single_post_content'  , array( $this, 'tc_grid_display_post_link')          , $_content_priorities['link'] );
        }

        /*
        * hook : __before_article
        * inside loop
        */
        function tc_set_grid_loop_hooks(){
          add_action( '__before_article'            , array( $this, 'tc_print_row_fluid_section_wrapper' ), 1 );
          add_action( '__after_article'             , array( $this, 'tc_print_row_fluid_section_wrapper' ), 0 );

          remove_action( '__loop'                   , array( TC_post_list::$instance, 'tc_post_list_display') );
          add_action( '__loop'                      , array( $this, 'tc_grid_single_post_display') );
        }



        /******************************************
        * SET VARIOUS VALUES **********************
        ******************************************/

        /* Layout
        * hook : tc_post_list_layout
        * force content + thumb layout : Force the title to be displayed always on bottom
        */
        function tc_grid_set_content_layout( $_layout ){
          $_layout['show_thumb_first'] = true;
          $_layout['content']          = 'tc-grid-excerpt';
          $_layout['thumb']            = 'span12 tc-grid-post-container';

          return $_layout;
        }


        /*
        * Apply proper class to articles selectors to control articles width
        * hook : tc_post_list_selectors
        */
        function tc_grid_set_article_selectors($selectors){
          $_class = sprintf( '%1$s tc-grid span%2$s',
            $this -> tc_force_current_post_expansion() ? 'expanded' : '',
            is_numeric($this -> tc_get_grid_section_cols()) ? 12 / $this -> tc_get_grid_section_cols() : 6
          );
          return str_replace( 'row-fluid', $_class, $selectors );
        }


        /*
        * hook : __before_article_container
        */
        function tc_grid_prepare_expand_featured(){
          global $wp_query;
          if ( ! ( $this -> tc_is_sticky_expanded() &&
                 $wp_query -> query_vars[ 'paged' ] == 0 ) )
              return;
          // prepend the first sticky
          $first_sticky = get_post( get_option( 'sticky_posts' )[0] );
          array_unshift( $wp_query -> posts, $first_sticky );
          $wp_query -> post_count = $wp_query -> post_count + 1;
          $this -> has_expanded_featured = true;
        }


        /*
        * hook : tc_thumb_size_name
        */
        function tc_set_thumb_size_name(){
          return  ( $this-> tc_get_grid_section_cols() == '1' ) ?
                          'tc-grid-full' : 'tc-grid';
        }

        /*
        * hook : tc_thumb_size
        */
        function tc_set_thumb_size(){
          $thumb = ( $this -> tc_get_grid_section_cols() == '1' ) ?
                          'tc_grid_full_size' : 'tc_grid_size';
          return TC_init::$instance -> $thumb;
        }

        // function tc_change_tumbnail_inline_css_width($_style, $width, $height){
        //   return sprintf('width:100%%;height:auto;');
        // }



        /******************************************
        * RENDER VIEWS ****************************
        ******************************************/
        /*
        * hook : __before_article
        * Wrap articles in a grid section
        */
        function tc_print_row_fluid_section_wrapper(){
          global $wp_query;
          $current_post = $wp_query -> current_post;
          $start_post = $this -> has_expanded_featured ? 1 : 0;
          $cols = $this -> tc_get_grid_section_cols();
          $current_filter = current_filter();

          if ( '__before_article' == $current_filter &&
              ( $start_post == $current_post ||
                  0 == ( $current_post - $start_post ) % $cols ) )
                  echo apply_filters( 'tc_grid_grid_section',
                      '<section class="tc-post-list-grid row-fluid grid-cols-'.$cols.'">' );
          elseif ( '__after_article' == $current_filter &&
                    ( $wp_query->post_count == ( $current_post + 1 ) ||
                    0 == ( ( $current_post - $start_post + 1 ) % $cols ) ) ) {

              echo '</section><!--end section.tc-post-list-grid.row-fluid-->';
              echo apply_filters( 'tc_grid_separator',
                  '<hr class="featurette-divider post-list-grid">' );
          }
        }


        /*
        * hook : __loop
        * display single post content + thumbnail
        */
        function tc_grid_single_post_display(){
          //creates vars $_single_grid_post_wrapper_class, $_thumb_html, $_post_content_html
          extract( $this -> tc_prepare_grid_single_post_content() , EXTR_OVERWRITE );

          ob_start();
            do_action( '__before_grid_single_post');//<= open <section> and maybe display title + metas

              echo apply_filters( 'tc_grid_single_post_thumb_content',
                sprintf('<section class="tc-grid-post"><figure class="%1$s">%2$s %3$s</figure></section>',
                  $_single_grid_post_wrapper_class,
                  $_thumb_html,
                  $_post_content_html
                )
              );


            do_action('__after_grid_single_post');//<= close </section> and maybe display title + metas
            //renders the hr separator after each article
            echo apply_filters( 'tc_post_list_separator', '<hr class="featurette-divider '.current_filter().'">' );

          $html = ob_get_contents();
          if ($html) ob_end_clean();

          echo apply_filters('tc_grid_display', $html);
        }


        /*
        * hook : __grid_single_post_content
        */
        function tc_grid_display_post_link(){
          printf( '<a href="%1$s" title="%2s"></a>',
              get_permalink( get_the_ID() ),
              esc_attr( strip_tags( get_the_title( get_the_ID() ) ) ) );
        }


        /*
        * hook : __grid_single_post_content
        */
        function tc_grid_display_expanded_post_title(){
          if ( ! $this -> tc_force_current_post_expansion() )
              return;
          global $post;
          $title = sprintf('<%1$s class="%2$s">%3$s</%1$s>',
              apply_filters( 'tc_grid_caption_title_tag', 'h1'),
              implode( ' ' , apply_filters( 'tc_grid_caption_title_class', array('tcd-title') ) ),
              $post->post_title
          );
          echo apply_filters( 'tc_grid_caption_title', $title );
        }



        /*
        * hook : __grid_single_post_content
        */
        function tc_grid_display_figcaption_content(){
          ob_start();
          ?>
              <div class="entry-summary">
                  <?php the_excerpt(); ?>
              </div>
          <?php
          $html = ob_get_contents();
          if ($html) ob_end_clean();
          echo apply_filters('tc_grid_display_figcaption_content', $html);
        }


        /*
        * hook : {$hook_prefix}_grid_single_post
        */
        function tc_grid_display_title_metas(){
          ob_start();
              do_action('__before_content');
          $html = ob_get_contents();
          if ($html) ob_end_clean();
          echo apply_filters('tc_grid_display_title_metas', $html);
        }



        /*
        * hook : pre_get_posts
        * exclude the first sticky post
        */
        function tc_maybe_excl_first_sticky( $query ){
          if ( $this -> tc_is_grid_enabled() &&
                   $this -> tc_is_sticky_expanded( $query ) )
              $query->set('post__not_in', array(get_option('sticky_posts')[0]) );
        }


        /*
        * @return css string
        * hook : tc_user_options_style
        * @since Customizr 3.2.18
        */
        function tc_grid_write_inline_css( $_css){
          /* retrieve the height/width ratios */
          $thumb_full_size  = apply_filters( 'tc_grid_full_size',
                                  TC_init::$instance -> tc_grid_full_size );
          $thumb_full_width = $thumb_full_size['width'];
          $thumb_size       = apply_filters( 'tc_grid_size',
                                  TC_init::$instance -> tc_grid_size );

          if ( ! isset($thumb_size['width']) || ! isset($thumb_size['height']) || ! isset($thumb_full_size['width']) || !isset($thumb_full_size['height']) )
            return $_css;
          if ( ! is_numeric($thumb_size['width']*$thumb_size['height']*$thumb_full_size['width']*$thumb_full_size['height']) )
            return $_css;

          $thumb_width      = $thumb_size['width'];
          $thumb_full_ratio = $thumb_full_size['height'] / $thumb_full_size['width'] * 100;
          $thumb_ratio      = $thumb_size['height'] / $thumb_size['width'] * 100;
          $_cols_class      = sprintf('grid-cols-%s' , $this -> tc_get_grid_section_cols() );
          $_grid_column_height = apply_filters(
            'tc_grid_figure_height' ,
            array(
              'grid-cols-1' => 350,
              'grid-cols-2' => 350,
              'grid-cols-3' => 225,
              'grid-cols-4' => 165
            )
          );
          $_figure_height = isset($_grid_column_height[$_cols_class]) ? $_grid_column_height[$_cols_class] : 350;

          $_css = sprintf("%s\n%s",
              $_css,
              "
              /*.tc-post-list-grid figure {
                  height: 0;
                  width: 100%;
                  padding-bottom: {$thumb_ratio}%;
                  max-width : {$thumb_width}px;
              }*/
              .{$_cols_class} figure {
                  height:{$_figure_height}px;
              }
              @media (max-width: 767px){
                  .tc-post-list-grid header,
                  .tc-post-list-grid .tc-grid-post{
                      margin: 0 auto !important;
                      float: none;
                      width: auto;
                      max-width : {$thumb_width}px;
                  }
                  .tc-post-list-grid.cols1 .tc-grid-post {
                      max-width: ${thumb_full_width}px;
                  }
              }
              \n
              "
          );

          return $_css;
        }




        /**********************************************
        * PREPARE VARIOUS HTML CONTENT ****************
        ***********************************************/
        /*
        * @return the figcation content parts as an array of html strings
        * inside loop
        */
        private function tc_prepare_grid_single_post_content() {
          global $post;
          if ( ! isset($post) || empty($post) || ! apply_filters( 'tc_show_post_in_post_list', $this -> tc_is_grid_context_matching() , $post ) )
            return;

          // get the filtered post list layout
          $_layout                          = apply_filters( 'tc_post_list_layout', TC_init::$instance -> post_list_layout );

          // SET HOOKS FOR POST TITLES AND METAS (only for non featured post)
          if ( ! $this -> tc_force_current_post_expansion() ){
              $hook_prefix = '__before';
              if ( $_layout['show_thumb_first'] )
                  $hook_prefix = '__after';

              add_action( "{$hook_prefix}_grid_single_post",  array( $this, 'tc_grid_display_title_metas' ) );
          }

          // THUMBNAIL : get the thumbnail data (src, width, height) if any
          $_thumb_data                      = $this -> tc_get_grid_thumb_data( TC_post_thumbnails::$instance -> tc_get_thumbnail_data() );
          $_thumb_html  = isset($_thumb_data[0]) ? $_thumb_data[0] : '';

          // CONTENT : get the figcaption content => post content
          $_post_content_html               = $this -> tc_grid_get_single_post_html( isset( $_layout['content']) ? $_layout['content'] : 'span6' );

          // WRAPPER CLASS : build single grid post wrapper class
          $_tc_show_thumb                   = ( empty($_thumb_data[0]) || ! esc_attr( tc__f('__get_option', 'tc_post_list_show_thumb') ) ) ? false : true;
          $_single_grid_post_wrapper_class  = implode( ' ' , apply_filters('tc_single_grid_post_wrapper_class', array('tc-grid-figure') ) );
          $_single_grid_post_wrapper_class  = ( $_tc_show_thumb ) ? $_single_grid_post_wrapper_class : sprintf( '%s no-thumb' , $_single_grid_post_wrapper_class );

          return apply_filters( 'tc_prepare_grid_single_post_content' , compact( '_single_grid_post_wrapper_class', '_thumb_html', '_post_content_html') );
        }


        /*
        * hook : tc_grid_thumb_data
        */
        private function tc_get_grid_thumb_data( $_thumb_data ){
          if ( ! empty($_thumb_data[0]) )
              return $_thumb_data;

          $default_thumb_id = apply_filters('tc_grid_default_thumb',
              esc_attr( tc__f( '__get_option', 'tc_grid_default_thumb' ) ) );

          if ( ! $default_thumb_id )
              return $_thumb_data;

          $tc_thumb_size = apply_filters( 'tc_thumb_size_name' , 'tc-thumb' );
          $image = wp_get_attachment_image_src( $default_thumb_id, $tc_thumb_size);

          if ( empty( $image[0] ) )
              return $_thumb_data;

          $_class_attr = array( 'class' => "attachment-{$tc_thumb_size} tc-grid-default-thumb");

          $tc_thumb               = wp_get_attachment_image( $default_thumb_id, $tc_thumb_size, false, $_class_attr );
          $tc_thumb_height        = '';
          $tc_thumb_width         = '';

          //get height and width if not empty
          if ( ! empty($image[1]) && ! empty($image[2]) ) {
              $tc_thumb_height        = $image[2];
              $tc_thumb_width         = $image[1];
          }

          return array($tc_thumb, $tc_thumb_height, $tc_thumb_width);
        }


        /*
        * @return the figcation content as a string
        * inside loop
        */
        private function tc_grid_get_single_post_html($post_list_content_class){
          global $post;
          ob_start();
          ?>
          <figcaption class="<?php echo $post_list_content_class ?>">
            <?php do_action( '__grid_single_post_content' ) ?>
          </figcaption>
          <?php
          $html = ob_get_contents();
          if ($html) ob_end_clean();
          return apply_filters('tc_grid_get_single_post_html', $html, $post_list_content_class, $post -> ID);
        }



        /******************************
        ***** HELPERS *****************
        *******************************/
        /*
        * @return bool
        * check if we have to expand the first sticky post
        */
        private function tc_is_sticky_expanded( $query = null ){
          global $wp_query;
          $query = ( $query ) ? $query : $wp_query;

          if ( ! $query->is_main_query() )
              return false;
          if ( ! ( ( is_home() && 'posts' == get_option('show_on_front') ) ||
                  $wp_query->is_posts_page ) )
              return false;

          $_expand_feat_post_opt = apply_filters( 'tc_grid_expand_featured', esc_attr( tc__f('__get_option', 'tc_grid_expand_featured') ) );
          if ( ! ( $_expand_feat_post_opt &&
                  isset( get_option ('sticky_posts')[0]) ) )
              return false;

          return true;
        }


        /*
        * @return bool
        * returns if the current post is the expanded one
        */
        private function tc_force_current_post_expansion(){
          global $wp_query;
          return ( $this -> has_expanded_featured && 0 == $wp_query -> current_post );
        }


        /*
        * @return bool
        */
        private function tc_is_grid_enabled(){
          return apply_filters( 'tc_is_grid_enabled', 'grid' == esc_attr( tc__f('__get_option', 'tc_post_list_grid') ) && $this -> tc_is_grid_context_matching() );
        }


        /* retrieves number of cols option, and wrap it into a filter */
        private function tc_get_post_list_cols(){
          return apply_filters( 'tc_grid_columns', esc_attr( tc__f('__get_option', 'tc_grid_columns') ) );
        }


        /* returns articles wrapper section columns */
        private function tc_get_grid_section_cols(){
          return apply_filters( 'tc_grid_section_cols',
            $this -> tc_force_current_post_expansion() ? '1' : $this -> tc_get_post_list_cols()
          );
        }


        /* returns the type of post list we're in if any, an empty string otherwise */
        private function tc_get_grid_context(){
          global $wp_query;
          if ( ( is_home() && 'posts' == get_option('show_on_front') ) ||
                  $wp_query->is_posts_page )
              return 'blog';
          else if ( is_search() )
              return 'search';
          else if ( is_archive() )
              return 'archive';
          return '';
        }


        /* performs the match between the option where to use post list grid
         * and the post list we're in */
        private function tc_is_grid_context_matching() {
          $_type = $this -> tc_get_grid_context();
          $_apply_grid_to_post_type = apply_filters( 'tc_grid_in_' . $_type, esc_attr( tc__f('__get_option', 'tc_grid_in_' . $_type ) ) );
          return apply_filters('tc_grid_do',  $_type && $_apply_grid_to_post_type );
        }

  }//end of class
endif;
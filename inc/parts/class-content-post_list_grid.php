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
            //customizer
            add_action( '__after_setting_control'   , array( $this , 'tc_render_grid_control_link') );
            add_action( '__before_setting_control'  , array( $this , 'tc_render_link_to_grid') );
        }


        /***************************************
        * HOOKS SETTINGS ***********************
        ****************************************/
        /*
        * hook : wp
        */
        function tc_set_grid_hooks(){
          if ( ! $this -> tc_is_grid_enabled() )
              return;

          do_action( '__post_list_grid' );

          //Various CSS filters
          add_filter( 'tc_grid_figure_height'       , array( $this, 'tc_set_grid_column_height'), 10, 2 );
          add_filter( 'tc_grid_title_sizes'         , array( $this, 'tc_set_grid_title_size'), 10, 2 );
          add_filter( 'tc_user_options_style'       , array( $this, 'tc_grid_write_inline_css'), 100 );

          //icon option
          add_filter( 'tc-grid-thumb-html'          , array( $this, 'tc_set_grid_icon_visibility') );

          //Layout filter
          add_filter( 'tc_get_grid_cols'            , array( $this, 'tc_set_grid_section_cols'), 20 , 2 );

          // pre loop hooks
          add_action( '__before_article_container'  , array( $this, 'tc_set_grid_before_loop_hooks'), 5 );

          // loop hooks
          add_action( '__before_loop'               , array( $this, 'tc_set_grid_loop_hooks'), 0 );
        }


        /* PRE LOOP HOOKS
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

          // SINGLE POST CONTENT IN GRID
          $_content_priorities = apply_filters('tc_grid_post_content_priorities' , array( 'content' => 20, 'link' =>30 ));
          add_action( '__grid_single_post_content'  , array( $this, 'tc_grid_display_figcaption_content') , $_content_priorities['content'] );
          add_action( '__grid_single_post_content'  , array( $this, 'tc_grid_display_post_link'), $_content_priorities['link'] );
          //expanded featured post : filter the figcaption content to include the post title
          add_filter( 'tc_grid_display_figcaption_content' , array( $this, 'tc_grid_set_expanded_post_title') );

          //SECTION CSS CLASSES TO HANDLE EFFECT LIKE SHADOWS
          add_filter( 'tc_grid_section_class'       , array( $this, 'tc_grid_section_set_classes' ) );

          //COMMENT BUBBLE
          remove_filter( 'tc_the_title'             , array( TC_comments::$instance, 'tc_display_comment_bubble' ) , 1 );
          add_filter( 'tc_grid_get_single_post_html'  , array( $this, 'tc_grid_display_comment_bubble' ) );

          //POST METAS
          remove_filter( 'tc_meta_utility_text'      , array( TC_post_metas::$instance , 'tc_add_link_to_post_after_metas'), 20 );
        }



        /**
        * hook : __before_loop
        * actions and filters inside loop
        * @return  void
        */
        function tc_set_grid_loop_hooks() {
          add_action( '__before_article'       , array( $this, 'tc_print_row_fluid_section_wrapper' ), 1 );
          add_action( '__after_article'        , array( $this, 'tc_print_article_sep' ), 0 );
          add_action( '__after_article'        , array( $this, 'tc_print_row_fluid_section_wrapper' ), 1 );

          remove_action( '__loop'              , array( TC_post_list::$instance, 'tc_prepare_section_view') );
          add_action( '__loop'                 , array( $this, 'tc_grid_prepare_single_post') );
        }



        /******************************************
        * PREPARE AND RENDER VIEWS ****************
        ******************************************/
        /*
        * hook : __before_article
        * Wrap articles in a grid section
        */
        function tc_print_row_fluid_section_wrapper(){
          global $wp_query;
          $current_post   = $wp_query -> current_post;
          $start_post     = $this -> has_expanded_featured ? 1 : 0;
          $cols           = $this -> tc_get_grid_section_cols();

          if ( '__before_article' == current_filter() &&
              ( $start_post == $current_post ||
                  0 == ( $current_post - $start_post ) % $cols ) ) {
            printf( '<section class="%s">',
              implode( " ", apply_filters( 'tc_grid_section_class' ,  array( "tc-post-list-grid", "row-fluid", "grid-cols-{$cols}" ) ) )
            );
          }
          elseif ( '__after_article' == current_filter() &&
                    ( $wp_query->post_count == ( $current_post + 1 ) ||
                    0 == ( ( $current_post - $start_post + 1 ) % $cols ) ) ) {
              printf( '</section><!--end section.tc-post-list-grid.row-fluid-->%s',
                apply_filters( 'tc_grid_separator', '<hr class="featurette-divider post-list-grid">')
              );
          }//end if
        }



        /**
        * hook : __loop
        * Prepare single post view model
        * inject it in the single post view
        * @return the figcation content parts as an array of html strings
        * inside loop
        */
        function tc_grid_prepare_single_post() {
          global $post;
          if ( ! isset($post) || empty($post) || ! apply_filters( 'tc_show_post_in_post_list', $this -> tc_is_grid_context_matching() , $post ) )
            return;

          // get the filtered post list layout
          $_layout   = apply_filters( 'tc_post_list_layout', TC_init::$instance -> post_list_layout );

          // SET HOOKS FOR POST TITLES AND METAS (only for non featured post)
          if ( ! $this -> tc_force_title_in_caption() ){
              $hook_prefix = '__before';
              if ( $_layout['show_thumb_first'] )
                  $hook_prefix = '__after';

              add_action( "{$hook_prefix}_grid_single_post",  array( TC_headings::$instance, 'tc_render_headings_view' ) );
          }

          // THUMBNAIL : cache the post format icon first
          //add thumbnail html (src, width, height) if any
          $_thumb_html = '';
          if ( TC_post_thumbnails::$instance -> tc_has_thumb() ) {
            $_thumb_model = TC_post_thumbnails::$instance -> tc_get_thumbnail_model();
            $_thumb_html  = $_thumb_model['tc_thumb'];
          }
          $_thumb_html = apply_filters( 'tc-grid-thumb-html' , $_thumb_html );


          // CONTENT : get the figcaption content => post content
          $_post_content_html               = $this -> tc_grid_get_single_post_html( isset( $_layout['content'] ) ? $_layout['content'] : 'span6' );

          // WRAPPER CLASS : build single grid post wrapper class
          $_classes  = array('tc-grid-figure');

          //may be add class no-thumb
          if ( ! TC_post_thumbnails::$instance -> tc_has_thumb() )
            array_push( $_classes, 'no-thumb' );

          //if 1 col layout or current post is the expanded => golden ratio should be disabled
          if ( ( '1' == $this -> tc_get_grid_cols() || $this -> tc_force_current_post_expansion() ) && ! wp_is_mobile() )
            array_push( $_classes, 'no-gold-ratio' );

          $_classes  = implode( ' ' , apply_filters('tc_single_grid_post_wrapper_class', $_classes ) );

          //RENDER VIEW
          $this -> tc_grid_render_single_post( $_classes, $_thumb_html, $_post_content_html );
          //return apply_filters( 'tc_prepare_grid_single_post_content' , compact( '_classes', '_thumb_html', '_post_content_html') );
        }


        /**
        * Single post view in the grid
        * display single post content + thumbnail
        * @return html string
        *
        */
        private function tc_grid_render_single_post( $_classes, $_thumb_html, $_post_content_html ) {
          ob_start();
            do_action( '__before_grid_single_post');//<= open <section> and maybe display title + metas

              echo apply_filters( 'tc_grid_single_post_thumb_content',
                sprintf('<section class="tc-grid-post"><figure class="%1$s">%2$s %3$s</figure></section>',
                  $_classes,
                  $_thumb_html,
                  $_post_content_html
                )
              );
            do_action('__after_grid_single_post');//<= close </section> and maybe display title + metas

          $html = ob_get_contents();
          if ($html) ob_end_clean();

          echo apply_filters('tc_grid_display', $html);
        }


        /**
        * hook : __grid_single_post_content
        */
        function tc_grid_display_post_link(){
          if ( ! apply_filters( 'tc_grid_display_post_link' , true ) )
            return;
          printf( '<a href="%1$s" title="%2s"></a>',
              get_permalink( get_the_ID() ),
              esc_attr( strip_tags( get_the_title( get_the_ID() ) ) ) );
        }


        /*
        * hook : __grid_single_post_content
        */
        function tc_grid_display_figcaption_content() {
          ?>
              <div class="entry-summary">
                <?php
                  echo apply_filters( 'tc_grid_display_figcaption_content',
                    sprintf('<div class="tc-grid-excerpt-content">%s</div>',
                      get_the_excerpt()
                    )
                  );
                ?>
              </div>
          <?php
        }


        /**
        * Separator after each grid article
        * hook : __after_article (declared in index.php)
        * print a separator after each article => revealed in responsive mode
        */
        function tc_print_article_sep() {
          //renders the hr separator after each article
          echo apply_filters( 'tc_grid_single_post_sep', '<hr class="featurette-divider '.current_filter().'">' );
        }



        /******************************************
        * SETTERS / GETTTERS / CALLBACKS
        ******************************************/
        /**
        * hook : pre_get_posts
        * exclude the first sticky post
        */
        function tc_maybe_excl_first_sticky( $query ){
          if ( $this -> tc_is_grid_enabled() &&
                   $this -> tc_is_sticky_expanded( $query ) )
              $query->set('post__not_in', array(get_option('sticky_posts')[0]) );
        }


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


        /**
        * Grid columns = fn(current-layout)
        */
        function tc_set_grid_section_cols( $_cols, $_current_layout ) {
          $_map = apply_filters(
            'tc_grid_col_layout_map',
            array(
              'span12'  => '4',
              'span9'   => '3',
              'span6'   => '2'
            )
          );
          if ( (int) $_map[$_current_layout] >= (int) $_cols )
            return (string) $_cols;
          return (string) $_map[$_current_layout];
        }



        /**
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
          return ( $this -> tc_get_grid_section_cols() == '1' ) ? 'tc-grid-full' : 'tc-grid';
        }

        /*
        * hook : tc_thumb_size
        */
        function tc_set_thumb_size(){
          $thumb = ( $this -> tc_get_grid_section_cols() == '1' ) ?
                          'tc_grid_full_size' : 'tc_grid_size';
          return TC_init::$instance -> $thumb;
        }


        /**
        * hook : tc_grid_grid_section_class
        * inside loop
        * add custom classes to each grid section
        */
        function tc_grid_section_set_classes( $_classes ) {
          if ( esc_attr( TC_utils::$inst->tc_opt( 'tc_grid_shadow') ) )
            array_push( $_classes, 'tc-grid-shadow' );
          if ( esc_attr( TC_utils::$inst->tc_opt( 'tc_grid_bottom_border') ) )
            array_push( $_classes, 'tc-grid-border' );
          return $_classes;
        }


        /**
        * @return the figcation content as a string
        * inside loop
        */
        private function tc_grid_get_single_post_html( $post_list_content_class ) {
          global $post;
          ob_start();
          ?>
            <figcaption class="<?php echo $post_list_content_class ?>">
              <?php do_action( '__grid_single_post_content' ) ?>
            </figcaption>
          <?php
          $html = ob_get_contents();
          if ($html) ob_end_clean();
          return apply_filters( 'tc_grid_get_single_post_html', $html, $post_list_content_class );
        }


        /**
        * hook : tc_grid_get_single_post_html
        * @return the comment_bubble as a string
        * inside loop
        */
        function tc_grid_display_comment_bubble( $_html ) {
          return TC_comments::$instance -> tc_display_comment_bubble() . $_html;
        }



        /**
        * hook : __grid_single_post_content
        * @return  html string
        * hook : tc_grid_display_figcaption_content
        */
        function tc_grid_set_expanded_post_title( $_html ){
          if ( ! $this -> tc_force_title_in_caption() )
              return $_html;
          global $post;
          $_html = sprintf('%1$s<h2 class="entry-title">%2$s</h2>',
              $_html,
              apply_filters( 'tc_the_title', $post->post_title )
          );
          return apply_filters( 'tc_grid_expanded_title', $_html );
        }


        /**
        * @param (number) $_height, (string) $col_layout
        * @return array
        * hook : tc_grid_figure_height
        */
        function tc_set_grid_column_height( $_height, $_cols_class ) {
          $_grid_col_height_map =  apply_filters(
              'tc_grid_col_height_map',
              array(
                'grid-cols-1' => $this -> tc_get_user_thumb_height(),
                'grid-cols-2' => $this -> tc_get_user_thumb_height(),
                'grid-cols-3' => 225,
                'grid-cols-4' => 165
              )
          );
          return isset( $_grid_col_height_map[$_cols_class] ) ? $_grid_col_height_map[$_cols_class] : $_height;
        }



        /**
        * @param (array) $_sizes, (string) $col_layout
        * @return array
        * hook :tc_grid_title_sizes
        */
        function tc_set_grid_title_size( $_sizes, $_cols_class ) {
          $_grid_col_height_map =  apply_filters(
              'tc_grid_col_title_map',
              array(
                'grid-cols-1' => array( 'font-size' => 32 , 'line-height' => 40 ),
                'grid-cols-2' => array( 'font-size' => 25 , 'line-height' => 30 ),
                'grid-cols-3' => array( 'font-size' => 20 , 'line-height' => 24 ),
                'grid-cols-4' => array( 'font-size' => 16 , 'line-height' => 22 )
              )
          );
          return isset( $_grid_col_height_map[$_cols_class] ) ? $_grid_col_height_map[$_cols_class] : $_sizes;
        }


        /*
        * @return css string
        * hook : tc_user_options_style
        * @since Customizr 3.2.18
        */
        function tc_grid_write_inline_css( $_css){
          /* retrieve the height/width ratios */
          $thumb_full_size  = apply_filters( 'tc_grid_full_size', TC_init::$instance -> tc_grid_full_size );
          $thumb_full_width = $thumb_full_size['width'];
          $thumb_size       = apply_filters( 'tc_grid_size', TC_init::$instance -> tc_grid_size );

          if ( ! isset($thumb_size['width']) || ! isset($thumb_size['height']) || ! isset($thumb_full_size['width']) || !isset($thumb_full_size['height']) )
            return $_css;
          if ( ! is_numeric($thumb_size['width']*$thumb_size['height']*$thumb_full_size['width']*$thumb_full_size['height']) )
            return $_css;

          // DEFINITIONS
          $thumb_width      = $thumb_size['width'];
          $thumb_full_ratio = $thumb_full_size['height'] / $thumb_full_size['width'] * 100;
          $thumb_ratio      = $thumb_size['height'] / $thumb_size['width'] * 100;
          $_cols_class      = sprintf('grid-cols-%s' , $this -> tc_get_grid_section_cols() );
          $_figure_height   = apply_filters( 'tc_grid_figure_height' , $this -> tc_get_user_thumb_height() , $_cols_class );
          $_title_sizes     = apply_filters( 'tc_grid_title_sizes', array( 'font-size' => 32 , 'line-height' => 40 ) , $_cols_class );
          $_font_size       = $_title_sizes['font-size'];
          $_line_height     = $_title_sizes['line-height'];
          $_expanded_featured_css = '';


          //ADD THE HEIGHT FOR EXP FEATURED POST
          $_height = isset($_grid_column_height['grid-cols-1']) ? $_grid_column_height['grid-cols-1'] : $this -> tc_get_user_thumb_height();
          $_expanded_featured_css = "
          .grid-cols-1 figure {
              height:{$_height}px;
              max-height:{$_height}px;
              line-height:{$_height}px;
          }";

          $_css = sprintf("%s\n%s\n%s",
              $_expanded_featured_css,
              $_css,
              "
              .{$_cols_class} figure {
                  height:{$_figure_height}px;
                  max-height:{$_figure_height}px;
                  line-height:{$_figure_height}px;
              }
              .{$_cols_class} .entry-title {
                  font-size:{$_font_size}px;
                  line-height:{$_line_height}px;
              }
              \n
              "
          );

          return $_css;
        }


        /**
        * hook : tc-grid-thumb-html
        * @return modified html string
        */
        function tc_set_grid_icon_visibility( $_html ) {
          $_icon_enabled = (bool) esc_attr( TC_utils::$inst->tc_opt( 'tc_grid_icons') );
          if ( TC___::$instance -> tc_is_customizing() )
            return sprintf('<div class="tc-grid-icon format-icon" style="display:%1$s"></div>%2$s',
                $_icon_enabled ? 'inline-block' : 'none',
                $_html
            );
          if ( $_icon_enabled )
            return sprintf('<div class="tc-grid-icon format-icon"></div>%1$s',
                $_html
            );
          else
            return $_html;
        }



        /******************************
        HELPERS
        *******************************/
        /**
        * @return (number) customizer user defined height for the grid thumbnails
        */
        private function tc_get_user_thumb_height() {
          $_opt = esc_attr( TC_utils::$inst->tc_opt( 'tc_grid_thumb_height') );
          return ( is_numeric($_opt) && $_opt > 1 ) ? $_opt : 350;
        }


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

          $_expand_feat_post_opt = apply_filters( 'tc_grid_expand_featured', esc_attr( TC_utils::$inst->tc_opt( 'tc_grid_expand_featured') ) );
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
        * returns whether display the title in caption for the current post
        */
        private function tc_force_title_in_caption(){
          return apply_filters( 'tc_force_title_in_caption' , $this -> tc_force_current_post_expansion() );
        }
        
        
        /*
        * @return bool
        */
        public function tc_is_grid_enabled() {
          return apply_filters( 'tc_is_grid_enabled', 'grid' == esc_attr( TC_utils::$inst->tc_opt( 'tc_post_list_grid') ) && $this -> tc_is_grid_context_matching() );
        }


        /* retrieves number of cols option, and wrap it into a filter */
        private function tc_get_grid_cols() {
          return apply_filters( 'tc_get_grid_cols',
            esc_attr( TC_utils::$inst->tc_opt( 'tc_grid_columns') ),
            TC_utils::$inst -> tc_get_current_screen_layout( get_the_ID() , 'class' )
          );
        }


        /* returns articles wrapper section columns */
        private function tc_get_grid_section_cols() {
          return apply_filters( 'tc_grid_section_cols',
            $this -> tc_force_current_post_expansion() ? '1' : $this -> tc_get_grid_cols()
          );
        }



        /* returns the type of post list we're in if any, an empty string otherwise */
        private function tc_get_grid_context() {
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
          $_apply_grid_to_post_type = apply_filters( 'tc_grid_in_' . $_type, esc_attr( TC_utils::$inst->tc_opt( 'tc_grid_in_' . $_type ) ) );
          return apply_filters('tc_grid_do',  $_type && $_apply_grid_to_post_type );
        }


        /**
        * hook __after_setting_control (declared in class-controls.php)
        * @echo link
        */
        function tc_render_grid_control_link( $set_id ) {
          if ( false !== strpos( $set_id, 'tc_grid_expand_featured' ) )
            printf('<span class="tc-grid-toggle-controls" title="%1$s">%1$s</span>' , __('More grid design options' , 'customizr'));
        }

        /**
        * hook __before_setting_control (declared in class-controls.php)
        * @echo link
        */
        function tc_render_link_to_grid( $set_id ) {
          if ( false !== strpos( $set_id, 'tc_front_layout' ) )
            printf('<span class="button tc-navigate-to-post-list" title="%1$s">%1$s &raquo;</span>' , __('Jump to the blog design options' , 'customizr') );
        }

  }//end of class
endif;

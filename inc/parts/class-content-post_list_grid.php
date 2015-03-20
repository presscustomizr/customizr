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
        private $expanded_sticky;

        function __construct () {
          self::$instance =& $this;
          $this -> expanded_sticky = null;

          add_action ( 'pre_get_posts'              , array( $this , 'tc_maybe_excl_first_sticky') );
          add_action ( 'wp_head'                    , array( $this , 'tc_set_grid_hooks') );

          //Various CSS filters
          //those filters are fired on hook : tc_user_options_style => fired on hook : wp_enqueue_scripts
          add_filter( 'tc_grid_title_sizes'         , array( $this , 'tc_set_grid_title_size'), 10, 2 );
          add_filter( 'tc_grid_p_sizes'             , array( $this , 'tc_set_grid_p_size'), 10, 2 );

          //append inline style to the custom stylesheet
          //! tc_user_options_style filter is shared by several classes => must always check the local context inside the callback before appending new css
          //fired on hook : wp_enqueue_scripts
          add_filter( 'tc_user_options_style'       , array( $this , 'tc_grid_write_inline_css'), 100 );

          //customizer
          add_action( '__after_setting_control'     , array( $this , 'tc_render_grid_control_link') );
          add_action( '__before_setting_control'    , array( $this , 'tc_render_link_to_grid') );
        }


        /***************************************
        * HOOKS SETTINGS ***********************
        ****************************************/
        /*
        * hook : wp
        */
        function tc_set_grid_hooks(){
          if ( ! apply_filters( 'tc_set_grid_hooks' , $this -> tc_is_grid_enabled() ) )
              return;

          do_action( '__post_list_grid' );
          //icon option
          add_filter( 'tc-grid-thumb-html'          , array( $this, 'tc_set_grid_icon_visibility') );
          //Layout filter
          add_filter( 'tc_get_grid_cols'            , array( $this, 'tc_set_grid_section_cols'), 20 , 2 );
          //pre loop hooks
          add_action( '__before_article_container'  , array( $this, 'tc_set_grid_before_loop_hooks'), 5 );
          //loop hooks
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
          add_action( '__grid_single_post_content'  , array( $this, 'tc_grid_display_fade_excerpt'), 100 );
          //expanded sticky post : filter the figcaption content to include the post title
          add_filter( 'tc_grid_display_figcaption_content' , array( $this, 'tc_grid_set_expanded_post_title') );

          //SECTION CSS CLASSES TO HANDLE EFFECT LIKE SHADOWS
          add_filter( 'tc_grid_section_class'       , array( $this, 'tc_grid_section_set_classes' ) );

          //COMMENT BUBBLE
          remove_filter( 'tc_the_title'             , array( TC_comments::$instance, 'tc_display_comment_bubble' ) , 1 );
          add_filter( 'tc_grid_get_single_post_html'  , array( $this, 'tc_grid_display_comment_bubble' ) );

          //POST METAS
          remove_filter( 'tc_meta_utility_text'     , array( TC_post_metas::$instance , 'tc_add_link_to_post_after_metas'), 20 );

          //TITLE LENGTH
          add_filter( 'tc_title_text'               , array( $this, 'tc_grid_set_title_length' ) );
        }


        /**
        * hook : __before_loop
        * actions and filters inside loop
        * @return  void
        */
        function tc_set_grid_loop_hooks() {
          add_action( '__before_article'            , array( $this, 'tc_print_row_fluid_section_wrapper' ), 1 );
          add_action( '__after_article'             , array( $this, 'tc_print_article_sep' ), 0 );
          add_action( '__after_article'             , array( $this, 'tc_print_row_fluid_section_wrapper' ), 1 );

          remove_action( '__loop'                   , array( TC_post_list::$instance, 'tc_prepare_section_view') );
          add_action( '__loop'                      , array( $this, 'tc_grid_prepare_single_post') );
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
          $start_post     = $this -> expanded_sticky ? 1 : 0;
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

          // SET HOOKS FOR POST TITLES AND METAS
          // Default condition : must be a non sticky post
          if ( apply_filters( 'tc_render_grid_headings_view' , ! $this -> tc_force_current_post_expansion() ) ) {
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
          else
            array_push( $_classes, 'has-thumb' );

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
          printf( '<a class="tc-grid-bg-link" href="%1$s" title="%2s"></a>',
              get_permalink( get_the_ID() ),
              esc_attr( strip_tags( get_the_title( get_the_ID() ) ) ) );
        }



        /**
        * hook : __grid_single_post_content
        */
        function tc_grid_display_fade_excerpt(){
          if ( ! apply_filters( 'tc_grid_fade_excerpt' , ! $this -> tc_force_current_post_expansion() ) )
            return;
          printf( '<span class="tc-grid-fade_expt"></span>' );
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
        * hook : tc_title_text
        * Limits the length of the post titles in grids to a custom number of characters
        * @return string
        */
        function tc_grid_set_title_length( $_title ) {
          $_max = esc_attr( TC_utils::$inst->tc_opt( 'tc_grid_num_words') );
          $_max = ( empty($_max) || ! $_max ) ? 10 : $_max;
          $_max = $_max <= 0 ? 1 : $_max;

          if ( empty($_title) || ! is_string($_title) )
            return $_title;

          if ( count( explode( ' ', $_title ) ) > $_max ) {
            $_words = array_slice( explode( ' ', $_title ), 0, $_max );
            $_title = sprintf( '%s ...',
              implode( ' ', $_words )
            );
          }
          return $_title;
        }


        /**
        * hook : pre_get_posts
        * exclude the first sticky post
        */
        function tc_maybe_excl_first_sticky( $query ){
          if ( $this -> tc_is_grid_enabled() && $this -> tc_is_sticky_expanded( $query ) )
            $query->set('post__not_in', array( $this -> expanded_sticky ) );
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
              'span11'  => '4',
              'span10'  => '4',
              'span9'   => '3',
              'span8'   => '3',
              'span7'   => '2',
              'span6'   => '2',
              'span5'   => '2',
              'span4'   => '1',
              'span3'   => '1',
              'span2'   => '1',
              'span1'   => '1',
            )
          );
          if ( ! isset($_map[$_current_layout]) )
            return $_cols;
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
            apply_filters( 'tc_grid_add_expanded_class', $this -> tc_force_current_post_expansion() ) ? 'expanded' : '',
            is_numeric( $this -> tc_get_grid_section_cols() ) ? 12 / $this -> tc_get_grid_section_cols() : 6
          );
          return str_replace( 'row-fluid', $_class, $selectors );
        }


        /*
        * hook : __before_article_container
        */
        function tc_grid_prepare_expand_featured(){
          global $wp_query;
          if ( ! ( $this -> tc_is_sticky_expanded() &&
                 $wp_query -> query_vars[ 'paged' ] == 0 ) ){
            $this -> expanded_sticky = null;
            return;
          }
          // prepend the first sticky
          $first_sticky = get_post( $this -> expanded_sticky );
          array_unshift( $wp_query -> posts, $first_sticky );
          $wp_query -> post_count = $wp_query -> post_count + 1;
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
          $thumb = ( $this -> tc_get_grid_section_cols() == '1' ) ? 'tc_grid_full_size' : 'tc_grid_size';
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
          if ( ! $this -> tc_force_current_post_expansion() )
              return $_html;
          global $post;
          $_html = sprintf('%1$s<h2 class="entry-title">%2$s</h2>',
              $_html,
              apply_filters( 'tc_the_title', $post->post_title )
          );
          return apply_filters( 'tc_grid_expanded_title', $_html );
        }



        /**
        * @return css string
        * hook : tc_user_options_style
        * @since Customizr 3.2.18
        */
        function tc_grid_write_inline_css( $_css ){
          if ( ! $this -> tc_is_grid_enabled() )
            return $_css;

          $_col_nb  = $this -> tc_get_grid_cols();

          //GENERATE THE FIGURE HEIGHT CSS
          $_current_col_figure_css  = $this -> tc_grid_get_figure_css( $_col_nb );

          //GENERATE THE MEDIA QUERY CSS FOR FONT-SIZES
          $_current_col_media_css = $this -> tc_get_grid_font_css( $_col_nb );

          $_css = sprintf("%s\n%s\n%s\n",
              $_css,
              $_current_col_media_css,
              $_current_col_figure_css
          );
         /* ?>
            <pre>
              <?php print_r($_css); ?>
            </pre>
          <?php*/
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
        * @param (string) $col_layout
        * @return css media query string
        * Returns the grid paragraph and title media queries for a given string layout
        */
        private function tc_get_grid_font_css($_col = '3') {
          $_rules = $this -> tc_get_grid_font_matrix($_col);
          $_media_queries_css = '';
          foreach ($_rules as $_query => $_css) {
            $_rules = $this -> tc_grid_get_font_rules( $_query , $_css );
            $_media_queries_css .= "
              @media {$_query} {{$_rules}}
            ";
          }
          return $_media_queries_css;
        }



        /**
        * @return css string
        * @since Customizr 3.3+
        * @param $_media_query = string of current media query. $_css_array = array of css rules for paragraph and titles for a given column layout
        * adds the one column css if (OR) :
        * 1) there's a sticky post
        * 2) user layout is one column
        */
        private function tc_grid_get_font_rules( $_media_query, $_css_array ) {
          $_css = '';
          //Add one column font rules if there's a sticky post
          if ( $this -> tc_is_sticky_expanded() || '1' == $_col_nb ) {
            $_one_col_rules = $this -> tc_get_grid_font_matrix('1');
            $_h_one_col = $this -> tc_get_css_rules_from_array( $_one_col_rules[$_media_query]['h'] );
            $_p_one_col = $this -> tc_get_css_rules_from_array( $_one_col_rules[$_media_query]['p'] );
            $_css .= "
                .tc-post-list-grid.grid-cols-1 .entry-title {{$_h_one_col}}
                .tc-post-list-grid.grid-cols-1 .tc-grid-excerpt-content {{$_p_one_col}}
            ";
          }
          $_h = $this -> tc_get_css_rules_from_array( $_css_array['h'] );
          $_p = $this -> tc_get_css_rules_from_array( $_css_array['p'] );
          $_css .= "
              .tc-post-list-grid .entry-title {{$_h}}
              .tc-post-list-grid .tc-grid-excerpt-content {{$_p}}
          ";
          return $_css;
        }



        /**
        * @return css string
        * @since Customizr 3.3+
        * adds the one column css if (OR) :
        * 1) there's a sticky post
        * 2) user layout is one column
        */
        private function tc_grid_get_figure_css( $_col_nb = '3' ) {
          $_height = $this -> tc_get_grid_column_height( $_col_nb );
          $_cols_class      = sprintf( 'grid-cols-%s' , $_col_nb );
          $_css = '';
          //Add one column height if there's a sticky post
          if ( $this -> tc_is_sticky_expanded() && '1' != $_col_nb ) {
            $_height_col_one = $this -> tc_get_grid_column_height( '1' );
            $_css .= ".grid-cols-1 figure {
                  height:{$_height_col_onet}px;
                  max-height:{$_height_col_one}px;
                  line-height:{$_height_col_one}px;
            }";
          }
          $_css .= "
            .{$_cols_class} figure {
                  height:{$_height}px;
                  max-height:{$_height}px;
                  line-height:{$_height}px;
            }";
          return $_css;
        }


        /**
        * @param (string) $col_layout
        * @return string
        *
        */
        private function tc_get_grid_column_height( $_cols_nb = '3' ) {
          $_height = $this -> tc_grid_get_thumb_height();
          $_grid_col_height_map =  apply_filters(
              'tc_grid_col_height_map',
              array(
                '1' => $_height,
                '2' => $_height,
                '3' => 225,
                '4' => 165
              )
          );
          $_height = isset( $_grid_col_height_map[$_cols_nb] ) ? $_grid_col_height_map[$_cols_nb] : $_height;
          return apply_filters( 'tc_get_grid_column_height' , $_height, $_cols_nb );
        }



        /**
        * @param (string) $col_layout
        * @return associate array of media query rules for a given layout
        *
        */
        private function tc_get_grid_font_matrix( $_cols = '3' ) {
          //1200 / 1199-980 / 979-768 / 767 / 480
          $_col_media_matrix = array(
            '1' => array( 'xxxl' , 'xxl' , 'xl' , 'l', 'm' ),
            '2' => array( 'xxl' , 'xl' , 'l', 'l', 'm' ),
            '3' => array( 'xl' , 'l' , 'm', 'l', 'm' ),
            '4' => array( 'l' , 'm' , 's', 'l', 'm' )
          );

          $_col_sizes = $_col_media_matrix[$_cols];

          $_h_font_rules = array(
            'xxxl' => array( 'font-size' => 32 , 'line-height' => 40 ),
            'xxl' => array( 'font-size' => 28 , 'line-height' => 36 ),
            'xl' => array( 'font-size' => 25 , 'line-height' => 30 ),
            'l' => array( 'font-size' => 22 , 'line-height' => 26 ),
            'm' => array( 'font-size' => 19 , 'line-height' => 23 ),
            's' => array( 'font-size' => 16 , 'line-height' => 22 )
          );
          //@to do : use user defined font-size
          $_p_font_rules = array(
            'xxl' => array( 'font-size' => 15 , 'line-height' => 20 ),
            'xxl' => array( 'font-size' => 15 , 'line-height' => 20 ),
            'xl' => array( 'font-size' => 15 , 'line-height' => 20 ),
            'l' => array( 'font-size' => 14 , 'line-height' => 18 ),
            'm' => array( 'font-size' => 13 , 'line-height' => 16 ),
            's' => array( 'font-size' => 13 , 'line-height' => 16 )
          );

          $_media_queries = array(
            '(min-width: 1200px)', '(max-width: 1199px) and (min-width: 980px)', '(max-width: 979px) and (min-width: 768px)', '(max-width: 767px)', '(max-width: 480px)'
          );

          $_col_rules = array();
          //flatten the matrix
          foreach ($_media_queries as $key => $_size) {
            $_cur_key = $_col_sizes[$key];
            $_col_rules[$_size] = array(
              'h' => $_h_font_rules[$_cur_key] ,
              'p' => $_p_font_rules[$_cur_key]
            ) ;
          }
          return apply_filters( 'tc_grid_font_rules' , $_col_rules , $_cols ) ;
        }



        /**
        * @param (string) $col_layout
        * @return css string
        * return a string of css rules
        * only used for font-size and line-height
        */
        private function tc_get_css_rules_from_array( $_array ) {
          if ( ! is_array($_array) )
            return '';

          $_json = json_encode($_array);
          $_json = str_replace( '"','', substr($_json, 1, -1) );
          $_json = explode(',', $_json);
          $_new  = array();
          foreach ($_json as $_ind => $_val) {
            $_new[$_ind] = $_val .'px';
          }
          $_new = implode(';' , $_new);
          return $_new .';';
        }



        /**
        * @return (number) customizer user defined height for the grid thumbnails
        */
        private function tc_grid_get_thumb_height() {
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

          $_sticky_posts = get_option('sticky_posts');
          $this -> expanded_sticky = ( is_array($_sticky_posts) && isset( $_sticky_posts[0] ) ) ? $_sticky_posts[0] : null;

          if ( ! ( $_expand_feat_post_opt && $this -> expanded_sticky ) )
              return false;

          return true;
        }


        /*
        * @return bool
        * returns if the current post is the expanded one
        */
        private function tc_force_current_post_expansion(){
          global $wp_query;
          return ( $this -> expanded_sticky && 0 == $wp_query -> current_post );
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
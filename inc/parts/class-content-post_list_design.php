<?php
/**
* Post lists content actions
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
if ( ! class_exists( 'TC_post_list_design' ) ) :
    class TC_post_list_design {
        static $instance;

        private $has_expanded_featured;

        function __construct () {
            self::$instance =& $this;

            $this -> has_expanded_featured = false;
            
            add_action ( 'pre_get_posts', array( $this, 'tc_post_list_design_sticky_post') );
            add_action ( 'wp', array( $this , 'tc_post_list_design_hooks') );
        }

        function tc_post_list_design_hooks(){
            $is_design = ( 'default' == esc_attr( tc__f('__get_option',
                'tc_post_list_design') ) ) ? false : true;

            $is_design = apply_filters( 'tc_post_list_design', $is_design );

            if ( ! $this -> tc_post_list_is_design() )
                return;
            
            add_filter( 'tc_user_options_style',
                array( $this , 'tc_post_list_design_write_inline_css') );

            // pre loop hooks
 /*           add_action('__post_list_design',
                            array( $this, 'tc_force_post_list_excerpt') );
  */
            add_action('__post_list_design',
                            array( $this, 'tc_force_post_list_thumbnails') );
            add_action('__post_list_design',
                            array( $this, 'tc_post_list_design_layout') );
            add_action('__post_list_design',
                            array( $this, 'tc_post_list_design_selectors') );
            add_action('__post_list_design',
                            array( $this, 'tc_post_list_prepare_expand_featured' ) );
    
            add_action ( '__before_article_container', 
                array( $this , 'tc_post_list_design_actions'), 0);
           
            // loop hooks
            add_action('__post_list_design_loop', 
                array( $this, 'tc_post_list_design_loop_hooks') );
        }
 
        function tc_post_list_design_loop_hooks(){
            add_action( '__before_article',
                            array( $this, 'tc_print_row_fluid_section_wrapper' ), 1 );
            add_action( '__after_article',
                            array( $this, 'tc_print_row_fluid_section_wrapper' ), 0 );
            add_action( '__before_post_list_design_entry_summary',
                    array( $this, 'tc_post_list_design_expanded_post_title'));
        }
    
        function tc_post_list_design_actions(){
            add_action('__before_article_container',
                    array( $this, 'tc_post_list_design_before_loop_actions') , 1);
            add_action( '__before_article', 
                array( $this, 'tc_post_list_design_loop_actions'), 0 );

            remove_action( '__loop',
                array( TC_post_list::$instance, 'tc_post_list_display') );
            add_action( '__loop',
                array( $this, 'tc_post_list_display') );
        }

        function tc_post_list_design_before_loop_actions(){
            do_action('__post_list_design');
            do_action('__post_list_design_thumbnails');
        }

        function tc_post_list_design_loop_actions(){
            do_action('__post_list_design_loop');
        }
        
        function tc_post_list_design_selectors(){        
            add_filter( 'tc_post_list_selectors',
                            array( $this, 'tc_post_list_design_article_selectors') );
        }

        function tc_force_post_list_excerpt(){
            add_filter('tc_force_show_post_list_excerpt', '__return_true', 0);
            add_filter('tc_show_post_list_excerpt', '__return_true', 0);
        }
        
        function tc_post_list_design_layout(){
            remove_filter('tc_post_list_layout',
                            array( TC_post_list::$instance, 'tc_set_post_list_layout') );
            add_filter('tc_post_list_layout',
                            array( $this, 'tc_set_post_list_layout'), 99999 );
        }

        function tc_post_list_prepare_expand_featured(){
            global $wp_query;
            
            if ( ! ( $this -> tc_consider_sticky_post() &&
                   $wp_query -> query_vars['paged'] == 0 ) )
                return;
            // prepend the first sticky
            $first_sticky = get_post(get_option('sticky_posts')[0]);
            array_unshift($wp_query->posts, $first_sticky);
            $wp_query->post_count = $wp_query->post_count + 1;
            $this -> has_expanded_featured = true;
        }

        function tc_force_post_list_thumbnails(){
            add_action( '__post_list_design_thumbnails',
                    array( $this, 'tc_post_list_design_thumb_shape_name') );
            add_action( '__post_list_design_thumbnails',
                    array( $this, 'tc_post_list_design_thumb_size_name') );
            add_action( '__post_list_design_thumbnails',
                    array( $this, 'tc_post_list_design_post_thumb_wrapper') );
            add_filter( 'tc_post_thumb_inline_style',
                array( $this, 'tc_change_tumbnail_inline_css_width'), 20, 3 );
        }

        function tc_post_list_design_thumb_shape_name(){
            remove_filter( 'post_class',
                    array( TC_post_list::$instance , 'tc_add_thumb_shape_name'));
            /*add_filter( 'post_class',
                array( $this , 'tc_add_thumb_shape_name'));*/
        }

        function tc_post_list_design_thumb_size_name(){
            remove_filter('tc_thumb_size_name',
                    array( TC_post_thumbnails::$instance, 'tc_set_thumb_size') );
            add_filter('tc_thumb_size_name',
                   array( $this, 'tc_set_thumb_size_name') );
            add_filter('tc_thumb_size',
                   array( $this, 'tc_set_thumb_size') );
        }

        function tc_post_list_design_post_thumb_wrapper(){
            add_filter('tc_post_list_design_container',
                    array( $this, 'tc_post_list_design_post_container'), 20, 2 );
        }

        function tc_add_thumb_shape_name($_classes){
            $_shape = esc_attr( tc__f( '__get_option' , 'tc_post_list_thumb_shape') );
            $_class =  ( ! $_shape || false !== strpos($_shape, 'rounded') ||
                    false !== strpos($_shape, 'squared') ) ? 'rectangular' : $_shape;
            return array_merge( $_classes, array( $_class ) );
        }

        function tc_set_thumb_size_name(){
            return  ( $this-> is_expanded_featured() ) ? 'tc-design-full' : 'tc-design';
        }

        function tc_set_thumb_size(){
            $thumb = ( $this -> is_expanded_featured() ) ? 
                        'tc_design_full_size' : 'tc_design_size';
            return TC_init::$instance -> $thumb;
        }

        function tc_post_list_design_post_container($figure_class, $content){
            return sprintf('<section class="tc-design-post"><figure class="%1$s"><a href="%2$s" title="%3s">%4$s</a></figure></section>',
                apply_filters('tc_post_list_design_figure_class', $figure_class),
                get_permalink( get_the_ID() ),
                esc_attr( strip_tags( get_the_title( get_the_ID() ) ) ),
                $content
            );

        }

        function tc_change_tumbnail_inline_css_width($_style, $width, $height){
            return sprintf('width:100%%;height:auto;');
        }
 
        /* force content + thumb layout */
        function tc_set_post_list_layout( $layout ){
            $_position                  = in_array( esc_attr( tc__f( '__get_option' ,
                                                    'tc_post_list_thumb_position' ) ),
                                            array('top', 'left') ) ? 'top' : 'bottom';

            $layout['alternate']        = false;
            $layout['show_thumb_first'] = ( 'top' == $_position ) ? true : false;
            $layout['content']          = 'tc-design-excerpt';
            $layout['thumb']            = 'span12 tc-design-post-container';
            
            return $layout;
        }

        /* Apply proper class to articles selectors to control articles width*/
        function tc_post_list_design_article_selectors($selectors){
            $class = $this -> is_expanded_featured() ?
                        'span12 expanded tc-design' : '';
            $class = ( $class ) ? $class :
                    'tc-design span'. ( 12 / $this -> tc_get_post_list_cols() );

            return str_replace( 'row-fluid', $class, $selectors );
        }

        /* Wrap articles in a grid section*/
        function tc_print_row_fluid_section_wrapper(){
            global $wp_query;
            $current_post = $wp_query -> current_post;
            $start_post = $this -> has_expanded_featured ? 1 : 0;
            $is_expanded_featured = ( $start_post ) ? $this -> is_expanded_featured() : false;
            $current_filter = current_filter();
            $cols = ( $is_expanded_featured ) ? 1 : $this -> tc_get_post_list_cols();
            
            if ( '__before_article' == $current_filter && ( $is_expanded_featured ||
                ( $start_post == $current_post ||
                    0 == ( $current_post - $start_post ) % $cols ) ) )
                    echo apply_filters( 'tc_post_list_design_grid_section',
                        '<section class="tc-post-list-design-grid row-fluid cols-'.$cols.'">' );
            elseif ( '__after_article' == $current_filter && ( $is_expanded_featured ||
                      ( $wp_query->post_count == ( $current_post + 1 ) ||
                        0 == ( ( $current_post - $start_post + 1 ) % $cols ) ) ) ){
                            
                echo '</section><!--end section.tc-post-list-design.row-fluid-->';
                echo apply_filters( 'tc_post_list_design_separator',
                    '<hr class="featurette-divider post-list-design">' );
            }
        }

        function tc_post_list_display(){
            global $post;
            if ( ! isset($post) || empty($post) || ! apply_filters( 'tc_show_post_in_post_list', $this -> tc_post_list_design_match_type()  , $post ) )
              return;

            //get the thumbnail data (src, width, height) if any
            $thumb_data                     = apply_filters( 'tc_thumb_data', null );
            
            //get the filtered post list layout
            $layout                         = apply_filters( 'tc_post_list_layout', TC_init::$instance -> post_list_layout );
            
            $tc_show_post_list_thumb        = empty($thumb_data[0]) ? false : true;
            //what is determining the layout ? if no thumbnail then full width + filter's conditions
            $post_list_content_class        = $layout['content'];

            if ( ! $this -> is_expanded_featured() ){
                $hook_prefix = '__before';
                if ( $layout['show_thumb_first'] )
                    $hook_prefix = '__after';

                add_action( $hook_prefix.'_post_list_post',
                    array( $this, 'tc_post_list_design_display_title_metas' ) );       
            }

            ob_start();
            do_action( '__before_post_list_post');
            
                $post_content = $this -> tc_post_list_design_prepare_post_content($post_list_content_class);
                $post_content = ( $tc_show_post_list_thumb ) ?
                                    $post_content . $thumb_data[0] :
                                    $post_content;

                $figure_class = 'tc-design-figure span12';
                $figure_class = ( $tc_show_post_list_thumb ) ?
                                    $figure_class :
                                    $figure_class . ' no-thumb';

                echo apply_filters('tc_post_list_design_container', $figure_class, $post_content);
                    
            do_action('__after_post_list_post');        
            //renders the hr separator after each article
            echo apply_filters( 'tc_post_list_separator', '<hr class="featurette-divider '.current_filter().'">' );

            $html = ob_get_contents();
            if ($html) ob_end_clean();

            echo apply_filters('tc_post_list_design_display', $html);
        }

        function tc_post_list_design_prepare_post_content($post_list_content_class){
            ob_start();
            ?>
            <figcaption class="<?php echo $post_list_content_class ?>">
                <?php do_action( '__before_post_list_design_entry_summary' ) ?>
                    <div class="entry-summary">
                        <?php the_excerpt(); ?>
                    </div>
                <?php do_action( '__after_post_list_design_entry_summary' ) ?>
            </figcaption>
            <?php
            $html = ob_get_contents();
            if ($html) ob_end_clean();
            return apply_filters('tc_post_list_design_content', $html, $post_list_content_class);
        }

        function tc_post_list_design_expanded_post_title(){
            if ( ! $this -> is_expanded_featured() )
                return;
            global $post;
            echo '<h2>'.$post->post_title.'</h2>';
        }
        
        function tc_post_list_design_display_title_metas(){
            ob_start();
            ?>
                <section class="tc-design-title-metas">
                    <?php do_action('__before_content'); ?>
                </section>
            <?php
            $html = ob_get_contents();
            if ($html) ob_end_clean();
            echo apply_filters('tc_post_list_design_display_title_metas', $html);
        }

        /* Callback pre_get_posts */
        // exclude the first sticky post
        function tc_post_list_design_sticky_post( $query ){
            if ( $this -> tc_post_list_is_design() &&
                     $this -> tc_consider_sticky_post( $query ) )
                $query->set('post__not_in', array(get_option('sticky_posts')[0]) );
        }

        function tc_post_list_design_write_inline_css( $_css){
            return sprintf("%s\n%s",
                $_css,
                "
                .tc-design-figure{
                    position: relative;
                    float: left;
                    width: 100%;
                }
                .tc-design-excerpt{
                    height: 100%;
                    background: rgba(0,0,0,.2);
                    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#33000000', endColorstr='#33000000', GradientType=0);
                    position: absolute;
                    display: inline-block;
                    visibility: hidden;
                    vertical-align: middle;
                    margin-left: 0;
                    width: 100%;
                    -webkit-box-sizing: border-box;
                    -moz-box-sizing: border-box;
                    box-sizing: border-box;
                    padding: 2%;
                }
                .expanded .tc-design-post .tc-design-excerpt{
                    visibility: visible;
                    width: auto;
                    max-width: 50%;
                    line-height: 10px;
                    height: auto;
                    top: 5%;
                    margin-left: 11%;
                }
                .tc-post-list-design-grid figure {
                    margin: 0;
                }
                figure.no-thumb .tc-design-excerpt{
                    visibility: visible;
                    position: relative;
                }

                .tc-post-list-design-grid article.hover .tc-design-excerpt{
                    visibility: visible;
                }
                .tc-post-list-design-grid .entry-summary{
                    display: block;
                }
                .tc-post-list-design-grid article.sticky{
                    text-align: justify;
                }
                .tc-post-list-design-grid .featurette-divider.__loop{
                    display: none;
                }
                @media (max-width: 767px){
                    .featurette-divider.post-list-design{
                        display: none;
                    }
                    .tc-post-list-design-grid .featurette-divider.__loop{
                        display: block;
                    }
                    .tc-post-list-design-grid article section + section {
                        margin-bottom: 30px;
                    }
                }
                \n"   
            );
        }
        /* Helpers */
        
        /* check if we have to expand the first sticky post */
        function tc_consider_sticky_post( $query = null ){
            global $wp_query;
            $query = ( $query ) ? $query : $wp_query;

            if ( !$query->is_main_query() )
                return false;
            if ( ! ( ( is_home() && 'posts' == get_option('show_on_front') ) ||
                    $wp_query->is_posts_page ) )
                return false;

            if ( ! ( $this -> tc_get_post_list_expand_featured() &&
                    isset( get_option ('sticky_posts')[0]) ) )
                return false;

            return true;
        }

        /* retrieves number of cols option, and wrap it into a filter */
        function tc_get_post_list_cols(){
            return apply_filters( 'tc_post_list_design_columns',
                esc_attr( tc__f('__get_option', 'tc_post_list_design_columns') ) );
        }
 
        /* retrieves the expand featured option, and wrap it into a filter */
        function tc_get_post_list_expand_featured(){
            return apply_filters( 'tc_post_list_expand_featured',
                esc_attr( tc__f('__get_option', 'tc_post_list_design_expand_featured') ) );
        }

        /* retrieves where to apply the post list design option, and wrap it into a filter 
         * dinamically
         * input param: post list type.
         */
        function tc_get_post_list_design_in( $type ){
            return apply_filters( 'tc_post_list_design_in_' . $type,
                esc_attr( tc__f('__get_option', 'tc_post_list_design_in_' . $type ) ) );
        }
 
        /* checks the option tc_post_list_design and wraps it into a filter */
        function tc_get_post_list_design(){
            return apply_filters( 'tc_post_list_design',
                'default' != esc_attr( tc__f('__get_option', 'tc_post_list_design') ) );
        }       

        function tc_post_list_is_design(){
            return apply_filters( 'tc_post_list_is_design',
                $this -> tc_get_post_list_design() && $this -> tc_post_list_design_match_type() );
        }

        /* returns if the current post is the expanded one */
        function is_expanded_featured(){
            global $wp_query;
            $current_post = $wp_query -> current_post;
            return ( $this -> has_expanded_featured && $current_post == 0 );
        }

        /* returns the type of post list we're in if any, an empty string otherwise */
        function tc_post_list_type(){
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

        /* performs the match between the option where to use post list design 
         * and the post list we're in */
        function tc_post_list_design_match_type(){
            $post_list_type = $this -> tc_post_list_type();
            return ( apply_filters('tc_post_list_design_do',
                $post_list_type && $this -> tc_get_post_list_design_in( $post_list_type ) ) );
        }
    
    }
endif;

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
            $is_design = ( 'default' == esc_attr( tc__f('__get_option',
                            'tc_post_list_design') ) ) ? false : true;
            $is_design = apply_filters( 'tc_post_list_design', $is_design );

            if ( ! $is_design )
                return;

            $this -> has_expanded_featured = false;

            add_action ( 'pre_get_posts', array( $this, 'tc_post_list_design_sticky_post') );
            add_action ( 'wp', array( $this , 'tc_post_list_design_hooks') );
        }

        function tc_post_list_design_hooks(){
            // pre loop hooks
            add_action('__post_list_design', array( $this, 'tc_force_post_list_excerpt') );
            add_action('__post_list_design', array( $this, 'tc_force_post_list_thumbnails') );
            add_action('__post_list_design', array( $this, 'tc_post_list_design_layout') );
            add_action('__post_list_design', array( $this, 'tc_post_list_design_selectors') );


            add_action ( '__before_article_container', array( $this , 'tc_post_list_design'), 0);
            // loop hooks
            add_action('__post_list_design_loop', array( $this, 'tc_post_list_design_loop') );
        }

        function tc_post_list_design(){
            if ( ! TC_post_list::$instance -> tc_post_list_controller() )
                return;

            add_action('__before_article_container',
                    array( $this, 'tc_post_list_design_actions') , 1);
            add_action( '__before_article', array($this,
                    'tc_post_list_design_loop_actions'), 0 );
        }
    
        function tc_post_list_design_actions(){
            add_action('__before_loop',
                array( $this, 'tc_post_list_prepare_expand_featured' ) );
            
            do_action('__post_list_design');
            do_action('__post_list_design_thumbnails');
        }

        function tc_post_list_design_loop_actions(){
            $expand_featured = $this -> tc_post_list_is_expanded_featured();
    
            $display_grid = ! $expand_featured;

            if ( ! $display_grid )
                return;
            
            do_action('__post_list_design_loop');
        }

        function tc_post_list_design_selectors(){        
            //TODO if on what kind of post list + options
            //case simple post_list
            add_filter( 'tc_post_list_selectors',
                            array($this, 'tc_post_list_design_article_selectors') );
        }
        
        function tc_post_list_design_loop(){
            add_action( '__before_article',
                            array( $this, 'tc_print_row_fluid_section_wrapper' ), 1 );
            add_action( '__after_article',
                            array( $this, 'tc_print_row_fluid_section_wrapper' ), 0 );
            add_filter( 'tc_post_list_separator', '__return_empty_string' );
        }
        
        function tc_force_post_list_excerpt(){
            add_filter('tc_force_show_post_list_excerpt', '__return_true', 0);
            add_filter('tc_show_post_list_excerpt', '__return_true', 0);
        }
        
        function tc_post_list_design_layout(){
            remove_filter('tc_post_list_layout',
                            array( TC_post_list::$instance, 'tc_set_post_list_layout') );
            add_filter('tc_post_list_layout',
                            array( $this, 'tc_set_post_list_layout') );
        }

        function tc_post_list_prepare_expand_featured(){
            global $wp_query;
            
            if ( ! ( $this -> tc_consider_sticky_post($wp_query) &&
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
            add_filter( 'post_class',
                    array( $this , 'tc_add_thumb_shape_name'));
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
            remove_filter('tc_post_thumb_wrapper',
                    array( TC_post_thumbnails::$instance, 'tc_set_thumb_shape') );
            add_filter('tc_post_thumb_wrapper',
                    array( $this, 'tc_set_thumb_shape'), 10, 2 );
        }

        function tc_add_thumb_shape_name($_classes){
            $_shape = esc_attr( tc__f( '__get_option' , 'tc_post_list_thumb_shape') );
            $_class =  ( ! $_shape || false !== strpos($_shape, 'rounded') ||
                    false !== strpos($_shape, 'squared') ) ? 'rectangular' : $_shape;
            return array_merge( $_classes, array( $_class ) );
        }

        function tc_set_thumb_size_name(){
            return  ( $this->tc_post_list_is_expanded_featured() ) ? 'tc-design-full' : 'tc-design';
        }

        function tc_set_thumb_size(){
            $thumb = ( $this -> tc_post_list_is_expanded_featured() ) ? 
                        'tc_design_full_size' : 'tc_design_size';
            return TC_init::$instance -> $thumb;
        }

        function tc_set_thumb_shape($thumb_wrapper, $thumb_img){
            return sprintf('<div><a class="%1$s" href="%2$s" title="%3s">%4$s</a></div>',
                'tc-design-thumb',
                get_permalink( get_the_ID() ),
                esc_attr( strip_tags( get_the_title( get_the_ID() ) ) ),
                $thumb_img
            );
        }
        function tc_change_tumbnail_inline_css_width($_style, $width, $height){
            //check on if we're in a design context
            $design_context = true;
            if ( ! $design_context )
                return $_style;
            return sprintf('width:100%%;height:auto;');
        }
        /* force content + thumb layout */
        function tc_set_post_list_layout( $layout ){
            $_position                  = in_array( esc_attr( tc__f( '__get_option' ,
                                                    'tc_post_list_thumb_position' ) ),
                                            array('top', 'left') ) ? 'top' : 'bottom';

            $layout['alternate']        = false;
            $layout['show_thumb_first'] = ( 'top' == $_position ) ? true : false;
            $layout['content']          = 'span12';
            $layout['thumb']            = 'span12';
            
            return $layout;
        }

        /* Apply proper class to articles selectors to control articles width*/
        function tc_post_list_design_article_selectors($selectors){
            $class = $this -> tc_post_list_is_expanded_featured() ?
                        'row-fluid expand' : '';
            $class = ( $class ) ? $class :
                    'span'. ( 12 / $this -> tc_get_post_list_cols() );

            return str_replace( 'row-fluid', $class, $selectors );
        }

        /* Wrap articles in a grid section*/
        function tc_print_row_fluid_section_wrapper(){
            global $wp_query;
            $current_post = $wp_query -> current_post;
            $start_post = $this -> has_expanded_featured ? 1 : 0;
            
            $current_filter = current_filter();
            $cols = $this -> tc_get_post_list_cols();
            
            if ( '__before_article' == $current_filter &&
                ( $start_post == $current_post ||
                    0 == ( $current_post - $start_post ) % $cols ) )
                    echo apply_filters( 'tc_post_list_design_grid_section',
                        '<section class="tc-post-list-design-grid row-fluid cols-'.$cols.'">' );
            elseif (  '__after_article' == $current_filter &&
                      ( $wp_query->post_count == ( $current_post + 1 ) ||
                        0 == ( ( $current_post - $start_post + 1 ) % $cols ) ) ){
                            
                echo '</section><!--end section.tc-post-list-design.row-fluid-->';
                echo apply_filters( 'tc_post_list_design_separator',
                    '<hr class="featurette-divider post-list-design">' );
            }
        }

        /* Callback pre_get_posts */
        // exclude the first sticky post
        function tc_post_list_design_sticky_post($query){
            if ( $this -> tc_consider_sticky_post($query) )
                $query->set('post__not_in', array(get_option('sticky_posts')[0]) );
        }

        /* Helpers */

        function tc_consider_sticky_post($query){
            global $wp_query;

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

        function tc_get_post_list_cols(){
            return apply_filters( 'tc_post_list_design_cols',
                esc_attr( tc__f('__get_option', 'tc_post_list_design_cols') ) );
        }
 
        function tc_get_post_list_expand_featured(){
            return ( apply_filters('tc_post_list_expand_featured', 
                tc__f('__get_option', 'tc_post_list_expand_featured') ) );
        }
   
        function tc_post_list_is_expanded_featured(){
            global $wp_query;
            $current_post = $wp_query -> current_post;
            return ( $this -> has_expanded_featured &&
                        $current_post == 0 ) ? true : false;
        }
    }
endif;

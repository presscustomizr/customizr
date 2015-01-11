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
        function __construct () {
            self::$instance =& $this;
            add_action ( '__before_article_container', array( $this , 'tc_post_list_design'), 0);
        }

        function tc_post_list_design(){
            if ( ! TC_post_list::$instance -> tc_post_list_controller() )
                return;

            $design = ( 'default' == esc_attr( tc__f('__get_option', 'tc_post_list_design') ) ) ? false : true;
            $design = apply_filters( 'tc_post_list_design', $design );
            if ( ! $design )
                return;
            $this -> tc_post_list_design_hooks();
            add_action( '__before_article', array($this, 'tc_post_list_design_loop_hooks'), 0 );
        }
        
        function tc_post_list_design_hooks(){

            $this -> tc_force_post_list_excerpt();
            
            $this -> tc_force_post_list_thumbnails();
       
            remove_filter('tc_post_list_layout', 
                            array( TC_post_list::$instance, 'tc_set_post_list_layout') );
            add_filter('tc_post_list_layout', 
                            array( $this, 'tc_set_post_list_layout') );
 
            //TODO if on what kind of post list + options
            //case simple post_list
            add_filter( 'tc_post_list_selectors', 
                            array($this, 'tc_post_list_design_article_selectors') );
        }

        function tc_post_list_design_loop_hooks(){

            $expand_featured = $this -> tc_get_post_list_expand_featured();

            $display_grid = ! $expand_featured;

            if ( ! $display_grid )
                return;

            $this -> tc_print_row_fluid_section_wrapper();
            
            add_action( '__after_article', 
                            array( $this, 'tc_print_row_fluid_section_wrapper' ), 0 );
            add_filter( 'tc_post_list_separator', '__return_empty_string' );

        }

        function tc_get_post_list_cols(){
            return apply_filters( 'tc_post_list_design_cols', 
                esc_attr( tc__f('__get_option', 'tc_post_list_design_cols') ) );
        }

        function tc_force_post_list_excerpt(){
            add_filter('tc_force_show_post_list_excerpt', '__return_true', 0);
            add_filter('tc_show_post_list_excerpt', '__return_true', 0);
        }

        function tc_get_post_list_expand_featured(){
            global $wp_query;
            $current_post = $wp_query -> current_post;

            return ( apply_filters('tc_post_list_expand_featured', tc__f('__get_option', 'tc_post_list_expand_featured') ) && $current_post == 0 ) ? true : false;
        }

        function tc_force_post_list_thumbnails(){
            remove_filter( 'post_class',
                array( TC_post_list::$instance , 'tc_add_thumb_shape_name'));
            add_filter( 'post_class',
                array( $this , 'tc_add_thumb_shape_name'));

            remove_filter('tc_thumb_size_name', 
                array( TC_post_thumbnails::$instance, 'tc_set_thumb_size') );
            add_filter('tc_thumb_size_name', 
                array( $this, 'tc_post_list_design_thumbs') );

            remove_filter('tc_post_thumb_wrapper', 
                array( TC_post_thumbnails::$instance, 'tc_set_thumb_shape') );
            add_filter('tc_post_thumb_wrapper', 
                array( $this, 'tc_set_thumb_shape'), 10, 2 );
        }

        function tc_add_thumb_shape_name($_classes){
            $_shape = esc_attr( tc__f( '__get_option' , 'tc_post_list_thumb_shape') );
            $_class =  ( ! $_shape || false !== strpos($_shape, 'rounded') || false !== strpos($_shape, 'squared') ) ? 'rectangular' : $_shape;
            return array_merge( $_classes, array( $_class ) );
        }

        function tc_post_list_design_thumbs(){
            return 'tc_rectangular_size';
        }

        function tc_set_thumb_shape($thumb_wrapper, $thumb_img){
            return sprintf('<div><a class="tc-rectangular-thumb" href="%1$s" title="%2s">%3$s</a></div>',
                get_permalink( get_the_ID() ),
                esc_attr( strip_tags( get_the_title( get_the_ID() ) ) ),
                $thumb_img
            );
        }

        // force content + thumb layout
        function tc_set_post_list_layout( $layout ){
            $_position                  = in_array(esc_attr( tc__f( '__get_option' , 'tc_post_list_thumb_position' ) ), array('top', 'left') ) ? 'top' : 'bottom';

            $layout['alternate']        = false;
            $layout['show_thumb_first'] = ( 'top' == $_position ) ? true : false;
            $layout['content']          = 'span12';
            $layout['thumb']            = 'span12';
            
            return $layout;
        }

        function tc_post_list_design_article_selectors($selectors){
            $class = ( $this -> tc_get_post_list_expand_featured() ) ?
                        'row-fluid expand' : '';
            $class = ( $class ) ? $class :
                    'span'. ( 12 / $this -> tc_get_post_list_cols() );

            return str_replace( 'row-fluid', $class, $selectors );
        }

        function tc_print_row_fluid_section_wrapper(){
            global $wp_query;
            $current_post = $wp_query -> current_post;
            $start_post = ( apply_filters( 'tc_post_list_expand_featured', false ) ) ? 1 : 0 ;

            $current_filter = current_filter();
            $cols = $this -> tc_get_post_list_cols();
            
            if ( '__before_article' == $current_filter && 
                ( $start_post == $current_post || 0 == ( $current_post - $start_post ) % $cols ) )
                echo apply_filters( 'tc_post_list_design_grid_section', '<section class="tc-post-list-design-grid row-fluid cols-'.$cols.'">' );
            elseif (  '__after_article' == $current_filter && 
                      ( $wp_query->post_count == ( $current_post + 1 ) ||
                        0 == ( ( $current_post - $start_post + 1 ) % $cols ) ) ){
                            
                echo '</section><!--end section.tc-post-list-design.row-fluid-->';
                echo apply_filters( 'tc_post_list_design_separator', '<hr class="featurette-divider post-list-design">' );
            }
        }
    }
endif;

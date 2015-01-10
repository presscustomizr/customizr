<?php
/**
* Post lists content actions
*
* 
* @package      Customizr
* @subpackage   classes
* @since        3.2.11
* @author       Rocco Aliberti <rocco@themesandco.com>, Nicolas GUILLAUME <nicolas@themesandco.com>
* @copyright    Copyright (c) 2013, Rocco Aliberti, Nicolas GUILLAUME
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
            
            add_action( '__before_article', array($this, 'tc_post_list_design_hooks'), 0 );
        }

        function tc_get_post_list_cols(){
            return esc_attr( tc__f('__get_option', 'tc_post_list_design_cols') );
        }

        function tc_force_post_list_excerpt($bool){
            $function_suffix = $bool ? 'true' : 'false';
            add_filter('tc_force_show_post_list_excerpt', "__return_{$function_suffix}", 0);
            add_filter('tc_show_post_list_excerpt', "__return_{$function_suffix}", 0);
        }

        function tc_post_list_design_hooks(){

            global $wp_query;
            $current_post = $wp_query -> current_post;

            $display_grid = ( apply_filters('tc_post_list_expand_featured', tc__f('__get_option', 'tc_post_list_expand_featured') ) && $current_post == 0 ) ? false : true;
            $show_excerpt = $display_grid;

            $this -> tc_force_post_list_excerpt( $show_excerpt );

            if ( ! $display_grid )
                return;

            $this -> tc_print_row_fluid_section_wrapper();
            
            add_action( '__after_article', array( $this, 'tc_print_row_fluid_section_wrapper' ), 0 );
            
            //TODO if on what kind of post list + options
            //case simple post_list
            
            add_filter( 'tc_post_list_cols', array($this, 'tc_get_post_list_cols') , 0 );
            add_filter( 'tc_post_list_selectors', array($this, 'tc_post_list_design_article_selectors') );

            remove_filter('tc_post_list_layout', array( TC_post_list::$instance, 'tc_set_post_list_layout') );
            add_filter('tc_post_list_layout', array( $this, 'tc_set_post_list_layout') );

            add_filter('tc_post_list_separator', '__return_empty_string');
        }

        // force content + thumb layout
        function tc_set_post_list_layout( $layout ){
            $_position                  = in_array(esc_attr( tc__f( '__get_option' , 'tc_post_list_thumb_position' ) ), array('top', 'left') ) ? 'top' : 'bottom';

            $layout['alternate']        = false;
            $layout['show_thumb_first'] = ( 'top' == $_position ) ? true : false;
            $layout['content']          = ( 'top' == $_position ) ? $layout['content'] : 'span12';
            $layout['thumb']            = ( 'top' == $_position || 'bottom' == $_position ) ? 'span12' : $layout['thumb'];
            
            return $layout;
        }

        function tc_post_list_design_article_selectors($selectors){
            $cols = apply_filters( 'tc_post_list_design_cols', 2 );
            return str_replace( 'row-fluid', 'span'.( 12 / $cols ), $selectors );
        }

        function tc_print_row_fluid_section_wrapper(){
            global $wp_query;
            $current_post = $wp_query -> current_post;
            $start_post = ( apply_filters( 'tc_post_list_expand_featured', false ) ) ? 1 : 0 ;

            $current_filter = current_filter();
            $cols = apply_filters( 'tc_post_list_design_cols', 2 );
            
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

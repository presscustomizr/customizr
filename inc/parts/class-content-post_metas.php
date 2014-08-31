<?php
/**
* Post metas content actions
* Since 3.1.20, displays all levels of any hierarchical taxinomies by default and for all types of post (including hierarchical CPT). This feature can be disabled with a the filter : tc_display_taxonomies_in_breadcrumb (set to true by default). In the case of hierarchical post types (like page or hierarchical CPT), the taxonomy trail is only displayed for the higher parent.
* 
* @package      Customizr
* @subpackage   classes
* @since        3.0.5
* @author       Nicolas GUILLAUME <nicolas@themesandco.com>
* @copyright    Copyright (c) 2013, Nicolas GUILLAUME
* @link         http://themesandco.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_post_metas' ) ) :
    class TC_post_metas {
        static $instance;
        function __construct () {
            self::$instance =& $this;
            add_action  ( '__after_content_title'                , array( $this , 'tc_post_metas' ));
        }


        /**
         * The template part for displaying entry metas
         *
         * @package Customizr
         * @since Customizr 1.0
         */
        function tc_post_metas() {
            global $post;
            //when do we display the metas ?
            //1) we don't show metas on home page, 404, search page by default
            //2) +filter conditions
            $post_metas_bool            = ( tc__f('__is_home') || is_404() || 'page' == $post -> post_type ) ? false : true ;
            $post_metas_bool            = apply_filters('tc_show_post_metas', $post_metas_bool ); 
            
            if ( ! $post_metas_bool )
                return;

            ob_start();
            ?>

            <div class="entry-meta">
                <?php
                if ( 'attachment' == $post -> post_type ) {
                    $metadata       = wp_get_attachment_metadata();
                    printf( '%1$s <span class="entry-date"><time class="entry-date updated" datetime="%2$s">%3$s</time></span> %4$s %5$s',
                        '<span class="meta-prep meta-prep-entry-date">'.__('Published' , 'customizr').'</span>',
                        apply_filters('tc_use_the_post_modified_date' , false ) ? esc_attr( get_the_date( 'c' ) ) : esc_attr( get_the_modified_date('c') ),
                        esc_html( get_the_date() ),
                        ( isset($metadata['width']) && isset($metadata['height']) ) ? __('at dimensions' , 'customizr').'<a href="'.esc_url( wp_get_attachment_url() ).'" title="'.__('Link to full-size image' , 'customizr').'"> '.$metadata['width'].' &times; '.$metadata['height'].'</a>' : '',
                        __('in' , 'customizr').'<a href="'.esc_url( get_permalink( $post->post_parent ) ).'" title="'.__('Return to ' , 'customizr').esc_attr( strip_tags( get_the_title( $post->post_parent ) ) ).'" rel="gallery"> '.get_the_title( $post->post_parent ).'</a>.'
                    );
                }

                else {

                    $categories_list    = $this -> tc_category_list();

                    $tag_list           = $this -> tc_tag_list();

                    $date               = apply_filters( 'tc_date_meta',
                                        sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date updated" datetime="%3$s">%4$s</time></a>' ,
                                            esc_url( get_day_link( get_the_time( 'Y' ), get_the_time( 'm' ), get_the_time( 'd' ) ) ),
                                            esc_attr( get_the_time() ),
                                            apply_filters('tc_use_the_post_modified_date' , false ) ? esc_attr( get_the_date( 'c' ) ) : esc_attr( get_the_modified_date('c') ),
                                            esc_html( get_the_date() )
                                        )
                    );//end filter

                    $author             = apply_filters( 'tc_author_meta',
                                        sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>' ,
                                            esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
                                            esc_attr( sprintf( __( 'View all posts by %s' , 'customizr' ), get_the_author() ) ),
                                            get_the_author()
                                        )
                    );//end filter

                    // Translators: 1 is category, 2 is tag, 3 is the date and 4 is the author's name.
                    $utility_text       = '';
                    if ( $tag_list ) {
                        $utility_text   = __( 'This entry was posted in %1$s and tagged %2$s on %3$s<span class="by-author"> by %4$s</span>.' , 'customizr' );
                        } elseif ( $categories_list ) {
                        $utility_text   = __( 'This entry was posted in %1$s on %3$s<span class="by-author"> by %4$s</span>.' , 'customizr' );
                        } else {
                        $utility_text   = __( 'This entry was posted on %3$s<span class="by-author"> by %4$s</span>.' , 'customizr' );
                    }
                    $utility_text       = apply_filters( 'tc_meta_utility_text', $utility_text );

                    //echoes every metas components
                    printf(
                        $utility_text,
                        $categories_list,
                        $tag_list,
                        $date,
                        $author
                    );
                }//endif attachment
                ?>

            </div><!-- .entry-meta -->

            <?php
            $html = ob_get_contents();
            if ($html) ob_end_clean();
            echo apply_filters( 'tc_post_metas', $html );
        }





         /**
         * Displays all the hierarchical taxonomy terms (including the category list for posts)
         *
         *
         * @package Customizr
         * @since Customizr 3.0 
         */
        function tc_category_list() {
            $post_terms                 = apply_filters( 'tc_cat_meta_list', $this -> _get_terms_of_tax_type( $hierarchical = true ) );
            $html                       = false;
            if ( false != $post_terms) {
                foreach( $post_terms as $term_id => $term ) {
                    $html                 .= sprintf('<a class="%1$s" href="%2$s" title="%3$s"> %4$s </a>',
                                                apply_filters( 'tc_category_list_class', 'btn btn-mini' ),
                                                get_term_link( $term_id , $term -> taxonomy ),
                                                esc_attr( sprintf( __( "View all posts in %s", 'customizr' ), $term -> name ) ),
                                                $term -> name
                    );
                }//end foreach
            }//end if $postcats
            return apply_filters( 'tc_category_list', $html );
        }





        /**
         * Displays all the non-hierarchical taxonomy terms (including the tag list for posts)
         * Handles tag like terms
         * Alternative
         *
         * @package Customizr
         * @since Customizr 3.0 
         *
         */
        function tc_tag_list() {
            $post_terms                 = apply_filters( 'tc_tag_meta_list', $this -> _get_terms_of_tax_type( $hierarchical = false ) );
            $html                       = false;

            if ( false != $post_terms) {
                foreach( $post_terms as $term_id => $term ) {
                    $html               .= sprintf('<a class="%1$s" href="%2$s" title="%3$s"> %4$s </a>',
                                                apply_filters( 'tc_tag_list_class', 'btn btn-mini btn-tag' ),
                                                get_term_link( $term_id , $term -> taxonomy ),
                                                esc_attr( sprintf( __( "View all posts in %s", 'customizr' ), $term -> name ) ),
                                                $term -> name
                    );
                }//end foreach
            }//end if
            return apply_filters( 'tc_tag_list', $html );
        }


        /**
         * Helper to return the current post terms of specified taxonomy type : hierarchical or not
         *
         * @return boolean (false) or array
         * @param  boolean : hierarchical or not
         * @package Customizr
         * @since Customizr 3.1.20
         *
         */
        private function _get_terms_of_tax_type ( $hierarchical = true ) {
            //var declaration
            $post_type              = get_post_type( tc__f('__ID') );
            $tax_list               = get_object_taxonomies( $post_type, 'object' );
            $_tax_type_list         = array();
            $_tax_type_terms_list   = array();

            if ( empty($tax_list) )
                return false;

            //filter the post taxonomies
            while ( $el = current($tax_list) ) {
                //skip the post format taxinomy
                if ( in_array( key($tax_list) , apply_filters_ref_array ( 'tc_exclude_taxonomies_from_metas' , array( array('post_format') , $post_type , tc__f('__ID') ) ) ) ) {
                    next($tax_list);
                    continue;
                }
                if ( (bool) $hierarchical === (bool) $el -> hierarchical )
                    $_tax_type_list[key($tax_list)] = $el;
                next($tax_list);
            }

            if ( empty($_tax_type_list) )
                return false;

            //fill the post terms array
            foreach ($_tax_type_list as $tax_name => $data ) {
                $_current_tax_terms = get_the_terms( tc__f('__ID') , $tax_name );

                //If current post support this tax but no terms has been assigned yet = continue
                if ( ! $_current_tax_terms )
                    continue;

                while( $term = current($_current_tax_terms) ) {
                    $_tax_type_terms_list[$term -> term_id] = $term;
                    next($_current_tax_terms);
                }
            }
            return empty($_tax_type_terms_list) ? false : $_tax_type_terms_list;
        }

    }//end of class
endif;
//this only purpose of this function is to use the_tags() wp function.
function tc_get_the_tags() {
    return the_tags();
}
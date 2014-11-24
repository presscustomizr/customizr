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
            add_action  ( '__after_content_title'           , array( $this , 'tc_post_metas' ));
            //Show / hide metas based on customizer user options (@since 3.2.0)
            add_action( 'template_redirect'                 , array( $this , 'tc_set_post_metas' ));
            //Set metas content based on customizer user options (@since 3.2.6)
            add_filter( 'tc_meta_utility_text'              , array( $this , 'tc_set_meta_content'));
        }



        /**
        * Post metas controller
        *
        * @package Customizr
        * @since Customizr 3.2.6
        */
        function tc_post_metas_controller() {
            global $post;
            //when do we display the metas ?
            //1) default is : not on home page, 404, search page
            //2) +filter conditions
            return apply_filters(
                'tc_show_post_metas',
                ! tc__f('__is_home') 
                && ! is_404()
                && ! 'page' == $post -> post_type
                && in_array( get_post_type(), apply_filters('tc_show_metas_for_post_types' , array( 'post') ) )
            );
        }



        /**
        * Metas views router
        *
        * @package Customizr
        * @since Customizr 1.0
        */
        function tc_post_metas() {
            global $post;
            if ( ! $this -> tc_post_metas_controller() )
                return;

            //Two cases : attachment and not attachment
            if ( 'attachment' == $post -> post_type )
                $this -> tc_attachment_post_metas_view();
            else
                $this -> tc_post_post_metas_view();
        }




        /**
        * Default post metas view
        *
        * @package Customizr
        * @since Customizr 3.2.6
        */
        function tc_post_post_metas_view() {
            ob_start();
            ?>
            <div class="entry-meta">
                <?php
                //echoes every metas components
                printf(
                    apply_filters( 'tc_meta_utility_text', __( 'This entry was posted on %3$s<span class="by-author"> by %4$s</span>.' , 'customizr' ) ),
                    $this -> tc_get_category_list(),
                    $this -> tc_get_tag_list(),
                    $this -> tc_get_meta_date('publication'),
                    $this -> tc_get_meta_author(),
                    $this -> tc_get_meta_date('update'),
                    $this -> tc_has_update()
                );
                ?>
            </div><!-- .entry-meta -->
            <?php
            $html = ob_get_contents();
            if ($html) ob_end_clean();
            echo apply_filters( 'tc_post_metas', $html );
        }


 

        /**
        * Attachment post metas view
        *
        *
        * @package Customizr
        * @since Customizr 3.2.6
        */
        function tc_attachment_post_metas_view() {
            global $post;
            ob_start();
            ?>
            <div class="entry-meta">
                <?php
                    $metadata       = wp_get_attachment_metadata();
                    printf( '%1$s <span class="entry-date"><time class="entry-date updated" datetime="%2$s">%3$s</time></span> %4$s %5$s',
                        '<span class="meta-prep meta-prep-entry-date">'.__('Published' , 'customizr').'</span>',
                        apply_filters('tc_use_the_post_modified_date' , false ) ? esc_attr( get_the_date( 'c' ) ) : esc_attr( get_the_modified_date('c') ),
                        esc_html( get_the_date() ),
                        ( isset($metadata['width']) && isset($metadata['height']) ) ? __('at dimensions' , 'customizr').'<a href="'.esc_url( wp_get_attachment_url() ).'" title="'.__('Link to full-size image' , 'customizr').'"> '.$metadata['width'].' &times; '.$metadata['height'].'</a>' : '',
                        __('in' , 'customizr').'<a href="'.esc_url( get_permalink( $post->post_parent ) ).'" title="'.__('Return to ' , 'customizr').esc_attr( strip_tags( get_the_title( $post->post_parent ) ) ).'" rel="gallery"> '.get_the_title( $post->post_parent ).'</a>.'
                    );
                ?>
            </div><!-- .entry-meta -->
            <?php
            $html = ob_get_contents();
            if ($html) ob_end_clean();
            echo apply_filters( 'tc_post_metas', $html );
        }




        /**
        * Returns all the hierarchical taxonomy terms (including the category list for posts)
        *
        *
        * @package Customizr
        * @since Customizr 3.0 
        */
        function tc_get_category_list() {
            $post_terms                 = apply_filters( 'tc_cat_meta_list', $this -> tc_get_term_of_tax_type( $hierarchical = true ) );
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
        * Returns all the non-hierarchical taxonomy terms (including the tag list for posts)
        * Handles tag like terms
        * Alternative
        *
        * @package Customizr
        * @since Customizr 3.0 
        *
        */
        function tc_get_tag_list() {
            $post_terms                 = apply_filters( 'tc_tag_meta_list', $this -> tc_get_term_of_tax_type( $hierarchical = false ) );
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
        * Return 'no-updates' if never updated OR number of days since last update
        *
        * @package Customizr
        * @since Customizr 3.2.6
        */
        function tc_has_update() {
            //Instantiates the different date objects
            $created                = new DateTime( get_the_date('Y-m-d g:i:s') );
            $updated                = new DateTime( get_the_modified_date('Y-m-d g:i:s') );
            $current                = new DateTime( date('Y-m-d g:i:s') );
            
            $created_to_updated     = TC_utils::$instance -> tc_date_diff( $created , $updated );
            $updated_to_today       = TC_utils::$instance -> tc_date_diff( $updated, $current );
            
            return ( 0 == $created_to_updated -> days && 0 == $created_to_updated -> s ) ? 'no-updates' : $updated_to_today -> days;
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
        private function tc_get_term_of_tax_type ( $hierarchical = true ) {
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



        /**
        * Return the date post metas
        *
        * @package Customizr
        * @since Customizr 3.2.6
        */
        function tc_get_meta_date( $pub_or_update = 'publication' ) {
            return apply_filters(
                'tc_date_meta',
                sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date updated" datetime="%3$s">%4$s</time></a>' ,
                    esc_url( get_day_link( get_the_time( 'Y' ), get_the_time( 'm' ), get_the_time( 'd' ) ) ),
                    esc_attr( get_the_time() ),
                    apply_filters( 'tc_use_the_post_modified_date' , 'publication' != $pub_or_update ) ? esc_attr( get_the_modified_date('c') ) : esc_attr( get_the_date( 'c' ) ),
                    apply_filters( 'tc_use_the_post_modified_date' , 'publication' != $pub_or_update ) ? esc_html( get_the_modified_date() ) : esc_html( get_the_date() )
                )
            );//end filter
        }




        /**
        * Return the post author metas
        *
        * @package Customizr
        * @since Customizr 3.2.6
        */
        function tc_get_meta_author() {
            return apply_filters( 
                'tc_author_meta',
                sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>' ,
                    esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
                    esc_attr( sprintf( __( 'View all posts by %s' , 'customizr' ), get_the_author() ) ),
                    get_the_author()
                )
            );//end filter
        }



        /**
        * Set the post metas based on Customizer options
        *
        * @package Customizr
        * @since Customizr 3.2.0
        */
        function tc_set_post_metas() {
            //if customizing context, always render. Will be hidden in the DOM with a body class filter is disabled.
            if ( is_singular() && ! is_page() && ! tc__f('__is_home') ) {
                if ( 0 != esc_attr( tc__f( '__get_option' , 'tc_show_post_metas_single_post' ) ) ) {
                    add_filter( 'tc_show_post_metas' , '__return_true' );
                    return;
                }
                
                if ( TC_utils::$instance -> tc_is_customizing() ) {
                    add_filter( 'body_class' , array( $this , 'tc_hide_post_metas') );
                    add_filter( 'tc_show_post_metas' , '__return_true' );
                }
                else
                    add_filter( 'tc_show_post_metas' , '__return_false' );

            }
            if ( ! is_singular() && ! tc__f('__is_home') && ! is_page() ) {
                if ( 0 != esc_attr( tc__f( '__get_option' , 'tc_show_post_metas_post_lists' ) ) ) {
                    add_filter( 'tc_show_post_metas' , '__return_true' );
                    return;
                }

                    if ( TC_utils::$instance -> tc_is_customizing() ) {
                        add_filter( 'body_class' , array( $this , 'tc_hide_post_metas') );
                        add_filter( 'tc_show_post_metas' , '__return_true' );
                    }
                    else
                        add_filter( 'tc_show_post_metas' , '__return_false' );
            }
            if ( tc__f('__is_home') ) {
                if ( 0 != esc_attr( tc__f( '__get_option' , 'tc_show_post_metas_home' ) ) ) {
                    add_filter( 'tc_show_post_metas' , '__return_true' );
                    return;
                }
                if ( TC_utils::$instance -> tc_is_customizing() ) {
                    add_filter( 'body_class' , array( $this , 'tc_hide_post_metas') );
                    add_filter( 'tc_show_post_metas' , '__return_true' );
                }
                else
                    add_filter( 'tc_show_post_metas' , '__return_false' );
            }
        }




        /**
        * Callback of the body_class filter
        *
        * @package Customizr
        * @since Customizr 3.2.0
        */
        function tc_hide_post_metas( $_classes ) {
            return array_merge($_classes , array('hide-post-metas') );
        }



        /**
        * Set meta content based on user options
        * callback of tc_meta_utility_text
        *
        * @package Customizr
        * @since Customizr 3.2.6
        */
        function tc_set_meta_content( $_default ) {
            if ( ! $this -> tc_post_metas_controller() )
                return;
            $_show_cats         = 0 != esc_attr( tc__f( '__get_option' , 'tc_show_post_metas_categories' ) ) && false != $this -> tc_get_category_list();
            $_show_tags         = 0 != esc_attr( tc__f( '__get_option' , 'tc_show_post_metas_tags' ) ) && false != $this -> tc_get_tag_list();
            $_show_pub_date     = 0 != esc_attr( tc__f( '__get_option' , 'tc_show_post_metas_publication_date' ) );
            $_show_upd_date     = 0 != esc_attr( tc__f( '__get_option' , 'tc_show_post_metas_update_date' ) ) && 'no-updates' !== $this -> tc_has_update();
            $_show_upd_in_days  = 'days' == esc_attr( tc__f( '__get_option' , 'tc_post_metas_update_date_format' ) );
            $_show_date         = $_show_pub_date || $_show_upd_date;
            $_show_author       = 0 != esc_attr( tc__f( '__get_option' , 'tc_show_post_metas_author' ) );
            
            //TAGS / CATS
            $_tax_text              = '';
            if ( $_show_cats && $_show_tags )
                $_tax_text   .= __( 'This entry was posted in %1$s and tagged %2$s' , 'customizr' );
            if ( $_show_cats && ! $_show_tags )
                $_tax_text   .= __( 'This entry was posted in %1$s' , 'customizr' );
            if ( ! $_show_cats && $_show_tags )
                $_tax_text   .= __( 'This entry was tagged %2$s' , 'customizr' );
            
            //PUBLICATION DATE
            $_date_text = '';
            if ( $_show_pub_date ) {
                $_date_text        = empty($_tax_text) ? __( 'This entry was posted on %3$s' , 'customizr' ) : $_date_text;
                if ( $_show_cats )
                    $_date_text   .= __( 'on %3$s' , 'customizr' );
                if ( ! $_show_cats && $_show_tags )
                    $_date_text   .= __( 'and posted on %3$s' , 'customizr' );
            }

            //AUTHOR
            $_author_text = '';
            if ( $_show_author ) {
                if ( empty($_tax_text) && empty($_date_text) ) {
                    $_author_text = sprintf( '%1$s <span class="by-author">%2$s</span>' , __( 'This entry was posted', 'customizr' ), __('by %4$s' , 'customizr') );
                } else {
                    $_author_text = sprintf( '<span class="by-author">%1$s</span>' , __('by %4$s' , 'customizr') );
                }
            }

            //UPDATE DATE
            $_update_text           = '';
            if ( $_show_upd_date ) {
                if ( $_show_upd_in_days ) {
                    $_update_text = ( 0 == $this -> tc_has_update() ) ? __( '(updated today)' , 'customizr' ) : __( '(updated %6$s days ago)' , 'customizr' );
                    $_update_text = ( 1 == $this -> tc_has_update() ) ? __( '(updated 1 day ago)' , 'customizr' ) : $_update_text;
                }
                else {
                    $_update_text = __( '(updated on %5$s)' , 'customizr' );
                }
            }

            return sprintf( '%1$s %2$s %3$s %4$s' , $_tax_text , $_date_text, $_author_text, $_update_text );
        }

    }//end of class
endif;

//the only purpose of this function is to use the_tags() wp function in the theme...
function tc_get_the_tags() {
    return the_tags();
}
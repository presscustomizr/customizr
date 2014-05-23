<?php
/**
* Post metas content actions
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

class TC_post_metas {

    //Access any method or var of the class with classname::$instance -> var or method():
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
        
        if (!$post_metas_bool)
            return;
        
        ob_start();
        ?>

        <div class="entry-meta">
            <?php
            if ( 'attachment' == $post -> post_type ) {
                $metadata       = wp_get_attachment_metadata();
                printf( '%1$s <span class="entry-date"><time class="entry-date updated" datetime="%2$s">%3$s</time></span> %4$s %5$s',
                    '<span class="meta-prep meta-prep-entry-date">'.__('Published' , 'customizr').'</span>',
                    esc_attr( get_the_date( 'c' ) ),
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
                                        esc_attr( get_the_date( 'c' ) ),
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
     * Displays the category list
     *
     *
     * @package Customizr
     * @since Customizr 3.0 
     */
    function tc_category_list() {

        $postcats                 = apply_filters( 'tc_cat_meta_list', get_the_category() );
        $html                     = false;
        if ( $postcats) {
            foreach( $postcats as $cat) {
                $html                 .= sprintf('<a class="%1$s" href="%2$s" title="%3$s"> %4$s </a>',
                                            apply_filters( 'tc_category_list_class', 'btn btn-mini' ),
                                            get_category_link( $cat->term_id ),
                                            esc_attr( sprintf( __( "View all posts in %s", 'customizr' ), $cat->name ) ),
                                            $cat->cat_name
                );
            }//end foreach
        }//end if $postcats

        return apply_filters( 'tc_category_list', $html );
    }





    /**
     * Template for displaying the tag list
     *
     *
     * @package Customizr
     * @since Customizr 3.0 
     *
     */
    function tc_tag_list() {

        
        
        $posttags                   = apply_filters( 'tc_tag_meta_list', get_the_tags() );
        $html                       = false;
        if ( $posttags) {
            foreach( $posttags as $tag) {
                $html               .= sprintf('<a class="%1$s" href="%2$s" title="%3$s"> %4$s </a>',
                                            apply_filters( 'tc_tag_list_class', 'btn btn-mini btn-tag' ),
                                            get_tag_link( $tag->term_id ),
                                            esc_attr( sprintf( __( "View all posts in %s", 'customizr' ), $tag->name ) ),
                                            $tag->name
                );
            }//end foreach
        }//end if
        return apply_filters( 'tc_tag_list', $html );
    }

}//end of class
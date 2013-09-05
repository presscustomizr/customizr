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

        add_action  ( '__post_metas'                   , array( $this , 'tc_post_metas' ));
        
        add_filter  ( '__category_list'                , array( $this , 'tc_category_list' ));
        add_filter  ( '__tag_list'                     , array( $this , 'tc_tag_list' ));
    }


    /**
     * The template part for displaying entry metas
     *
     * @package Customizr
     * @since Customizr 1.0
     */
    function tc_post_metas() {

        tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );

        ob_start();

        $categories_list    = tc__f( '__category_list' );

        $tag_list           = tc__f( '__tag_list' );

        $date               = sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a>' ,
                esc_url( get_day_link( get_the_time( 'Y' ), get_the_time( 'm' ), get_the_time( 'd' ) ) ),
                esc_attr( get_the_time() ),
                esc_attr( get_the_date( 'c' ) ),
                esc_html( get_the_date() )
        );

        $author             = sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>' ,
                esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
                esc_attr( sprintf( __( 'View all posts by %s' , 'customizr' ), get_the_author() ) ),
                get_the_author()
        );

        // Translators: 1 is category, 2 is tag, 3 is the date and 4 is the author's name.
        if ( $tag_list ) {
            $utility_text   = __( 'This entry was posted in %1$s and tagged %2$s on %3$s<span class="by-author"> by %4$s</span>.' , 'customizr' );
            } elseif ( $categories_list ) {
            $utility_text   = __( 'This entry was posted in %1$s on %3$s<span class="by-author"> by %4$s</span>.' , 'customizr' );
            } else {
            $utility_text   = __( 'This entry was posted on %3$s<span class="by-author"> by %4$s</span>.' , 'customizr' );
        }

        printf(
            $utility_text,
            $categories_list,
            $tag_list,
            $date,
            $author
        );

        tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__);

        $html = ob_get_contents();
        ob_end_clean();
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

        tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );

      $postcats                 = get_the_category();
        if ( $postcats) {
          $html                 = '';
          foreach( $postcats as $cat) {
            $html               .= '<a class="btn btn-mini" href="'.get_category_link( $cat->term_id ).'" title="' . esc_attr( sprintf( __( "View all posts in %s", 'customizr' ), $cat->name ) ) . '">';
              $html                 .= ' '.$cat->cat_name.' ';
            $html               .= '</a>'.tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__, 'right');
          }
          //$html .= '</div>';
         return apply_filters( 'tc_category_list', $html );
        }
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

        tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
        
      $posttags                 = get_the_tags();
        if ( $posttags) {
          $html                 = '';
          foreach( $posttags as $tag) {
            $html               .= '<a class="btn btn-mini btn-tag" href="'.get_tag_link( $tag->term_id ).'" title="' . esc_attr( sprintf( __( "View all posts in %s", 'customizr' ), $tag->name ) ) . '">';
               $html                .= ' '.$tag->name.' ';
            $html               .= '</a>'.tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__, 'left');
          }
          //$html .= '</div>';
         return apply_filters( 'tc_tag_list', $html );
        }
     }

}//end of class
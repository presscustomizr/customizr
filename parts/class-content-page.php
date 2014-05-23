<?php
/**
* Pages content actions
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

class TC_page {

    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;

    function __construct () {

        self::$instance =& $this;

        //pages templates
        add_action ( '__loop'                       , array( $this , 'tc_page_content' ));
        add_filter ( '__article_selectors'          , array( $this , 'tc_page_selectors' ));
    }



     /**
     * Displays the conditional selectors of the article
     * 
     * @package Customizr
     * @since 3.0.10
     */
    function tc_page_selectors () {
        if ( 'page' != tc__f('__post_type') || !is_singular() || tc__f( '__is_home_empty') )
            return;
        tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );

        echo 'id="page-'.get_the_ID().'" '.tc__f('__get_post_class' , 'row-fluid');
    }



    /**
     * The template part for displaying page content
     *
     * @package Customizr
     * @since Customizr 3.0
     */
    function tc_page_content() {
        if ( 'page' != tc__f('__post_type') || !is_singular() || tc__f( '__is_home_empty') )
            return;
        
        tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
        ?>

        <?php ob_start(); ?>

        
        <?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__); ?>
            <header>
                 <?php if(!is_front_page()) : ?>
                     <?php
                        $bubble_style        = ( 0 == get_comments_number() ) ? 'style="color:#ECECEC" ':'';

                        $comments_enable     = ( 1 == esc_attr( tc__f( '__get_option' , 'tc_page_comments' )) && comments_open() && get_comments_number() != 0 && !post_password_required() ) ? true : false;

                        printf( '<h1 class="entry-title format-icon">%1$s %2$s %3$s</h1>' ,
                            get_the_title(),
                            $comments_enable ? '<span class="comments-link">
                                <a href="'.get_permalink().'#tc-comment-title" title="'.__( 'Comment(s) on ' , 'customizr' ).get_the_title().'"><span '.$bubble_style.' class="fs1 icon-bubble"></span><span class="inner">'.get_comments_number().'</span></a>
                            </span>' : '',
                            ((is_user_logged_in()) && current_user_can('edit_pages')) ? '<span class="edit-link btn btn-inverse btn-mini"><a class="post-edit-link" href="'.get_edit_post_link().'" title="'.__( 'Edit page' , 'customizr' ).'">'.__( 'Edit page' , 'customizr' ).'</a></span>' : ''
                        ); 
                    ?>
                    <hr class="featurette-divider">
                 <?php endif; ?>
            </header>

            <div class="entry-content">
                <?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>' , 'customizr' ) ); ?>
            </div>

               <?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:' , 'customizr' ), 'after' => '</div>' ) ); ?>

            <footer class="entry-meta">

            </footer><!-- .entry-meta -->
        <?php
        $html = ob_get_contents();
        ob_end_clean();
        echo apply_filters( 'tc_page_content', $html );
    }

}//end of class
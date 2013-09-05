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

    function __construct () {
        //pages templates
        add_action  ( '__page'                  , array( $this , 'tc_content_page' ));
    }



    /**
     * The template part for displaying page content
     *
     * @package Customizr
     * @since Customizr 3.0
     */
    
    function tc_content_page() {
        ?>
        <header>
             <?php if(!is_front_page()) : ?>
                 <?php 
                    printf( '<h1 class="entry-title format-icon">%1$s %2$s</h1>' ,
                        get_the_title(),
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
    }

}//end of class
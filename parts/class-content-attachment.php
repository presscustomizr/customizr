<?php
/**
* Attachments content actions
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

class TC_attachment {

    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;

    function __construct () {

        self::$instance =& $this;

        add_action  ( '__before_content'              , array( $this , 'tc_attachment_header' ));
        add_action  ( '__loop'			              , array( $this , 'tc_attachment_content' ));

        //selector filter
        add_filter ( '__article_selectors'            , array( $this , 'tc_attachment_selectors' ));
    }


    

     /**
     * Displays the conditional selectors of the article
     * 
     * @package Customizr
     * @since 3.0.10
     */
    function tc_attachment_selectors () {
        //check conditional tags
        global $post;
        if ( !isset($post) )
        return;
        if ('attachment' != $post -> post_type || !is_singular() )
            return;
        
        //check if attachement is image and add a selector
        $format_image = wp_attachment_is_image() ? 'format-image' : '';

        tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );

        echo 'id="post-'.get_the_ID().'" '.tc__f('__get_post_class' , array('row-fluid', $format_image) );
    }




    /**
     * The template part for displaying the attachment header
     *
     * @package Customizr
     * @since Customizr 3.0.10
     */
    function tc_attachment_header() {
    //check conditional tags
        global $post;
        if ('attachment' != $post -> post_type || !is_singular() )
            return;

        tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );

        ob_start();

        tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__); 
        ?>

        <header class="entry-header">

            <?php 
                $bubble_style                      = ( 0 == get_comments_number() ) ? 'style="color:#ECECEC" ':'';

                printf( '<h1 class="entry-title format-icon">%1$s %2$s %3$s</h1>' ,
                get_the_title(),
                ( comments_open() && get_comments_number() != 0 && !post_password_required() ) ? '<span class="comments-link">
                    <a href="'.get_permalink().'#tc-comment-title" title="'.__( 'Comment(s) on ' , 'customizr' ).get_the_title().'"><span '.$bubble_style.' class="fs1 icon-bubble"></span><span class="inner">'.get_comments_number().'</span></a>
                </span>' : '',
                ((is_user_logged_in()) && current_user_can('edit_posts')) ? '<span class="edit-link btn btn-inverse btn-mini"><a class="post-edit-link" href="'.get_edit_post_link().'" title="'.__( 'Edit' , 'customizr' ).'">'.__( 'Edit' , 'customizr' ).'</a></span>' : ''
                );

            ?>
            <div class="entry-meta">
                 
                <?php //meta not displayed on home page, only in archive or search pages
                    if ( !tc__f('__is_home') ) { 
                        do_action( '__post_metas' );
                    }
                ?>

            </div><!-- .entry-meta -->

        </header><!-- .entry-header -->
      <?php
      $html = ob_get_contents();
      ob_end_clean();

      echo apply_filters( 'tc_attachment_header', $html);
    }





    /**
     * The template part for displaying attachment content
     * Inspired from Twenty Twelve WP Theme
     * @package Customizr
     * @since Customizr 3.0
     */
    function tc_attachment_content() {
        //check conditional tags
        global $post;
        if (isset($post) && 'attachment' != $post -> post_type || !is_singular() )
            return;

        tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
        ?>
        <?php  ob_start(); ?>

        <?php do_action( '__before_content' ); ?>

        <?php echo '<hr class="featurette-divider">' ?>

        <nav id="image-navigation" class="navigation" role="navigation">

            <span class="previous-image"><?php previous_image_link( false, __( '&larr; Previous' , 'customizr' ) ); ?></span>

            <span class="next-image"><?php next_image_link( false, __( 'Next &rarr;' , 'customizr' ) ); ?></span>

        </nav><!-- #image-navigation -->

        <?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ ); ?>    
        <section class="entry-content">

            <div class="entry-attachment">

                <div class="attachment">
                    <?php

                    $attachments = array_values( get_children( array( 'post_parent' => $post->post_parent, 'post_status' => 'inherit' , 'post_type' => 'attachment' , 'post_mime_type' => 'image' , 'order' => 'ASC' , 'orderby' => 'menu_order ID' ) ) );

                    //did we activate the fancy box in customizer?
                    $tc_fancybox = esc_attr( tc__f( '__get_option' , 'tc_fancybox' ) );

                    ?>
                    
                    <?php if ( $tc_fancybox == 0 ) : //fancy box not checked! ?> 
                        
                        <?php
                        /**
                        * Grab the IDs of all the image attachments in a gallery so we can get the URL of the next adjacent image in a gallery,
                        * or the first image (if we're looking at the last image in a gallery), or, in a gallery of one, just the link to that image file
                        */

                        foreach ( $attachments as $k => $attachment )  {
                            if ( $attachment->ID == $post->ID ) {
                                break;
                            }
                        }

                        $k++;

                        // If there is more than 1 attachment in a gallery
                        if ( count( $attachments ) > 1 ) {

                            if ( isset( $attachments[ $k ] ) ) {
                                // get the URL of the next image attachment
                                $next_attachment_url = get_attachment_link( $attachments[ $k ]->ID );
                            }
                            
                            else {
                                // or get the URL of the first image attachment
                                $next_attachment_url = get_attachment_link( $attachments[ 0 ]->ID );
                            }
                        }

                        else {
                            // or, if there's only 1 image, get the URL of the image
                            $next_attachment_url = wp_get_attachment_url();
                        }

                        ?>

                        <a href="<?php echo esc_url( $next_attachment_url ); ?>" title="<?php the_title_attribute(); ?>" rel="attachment"><?php
                        $attachment_size = apply_filters( 'customizr_attachment_size' , array( 960, 960 ) );
                        echo wp_get_attachment_image( $post->ID, $attachment_size );
                        ?></a>
                    
                    <?php else : // if fancybox option checked ?>
                        
                        <?php
                        //get attachement src
                        $attachment_infos       = wp_get_attachment_image_src( $post->ID , 'large' );
                        $attachment_src         = $attachment_infos[0];
                        ?>

                        <a href="<?php echo $attachment_src; ?>" title="<?php the_title_attribute(); ?>" class="grouped_elements" rel="tc-fancybox-group<?php echo $post -> ID ?>"><?php
                        $attachment_size = apply_filters( 'customizr_attachment_size' , array( 960, 960 ) );
                        echo wp_get_attachment_image( $post->ID, $attachment_size );
                        ?></a>
                        
                        <div id="hidden-attachment-list" style="display:none">

                            <?php foreach ( $attachments as $k => $attachment ) : //get all related galery attachement for lightbox navigation ?>

                                <?php
                                $rel_attachment_infos       = wp_get_attachment_image_src( $attachment->ID , 'large' );
                                $rel_attachment_src         = $rel_attachment_infos[0];
                                ?>

                                <a href="<?php echo $rel_attachment_src ; ?>" title="<?php printf('%1$s', !empty( $attachment->post_excerpt ) ? $attachment->post_excerpt :  $attachment->post_title ) ?>" class="grouped_elements" rel="tc-fancybox-group<?php echo $post -> ID ?>"><?php echo $rel_attachment_src ; ?></a>
                                
                            <?php endforeach ?>

                        </div><!--/#hidden-attachment-list-->

                    <?php endif //end if fancybox option checked ?>

                    <?php if ( ! empty( $post->post_excerpt ) ) : ?>

                        <div class="entry-caption">
                            <?php the_excerpt(); ?>
                        </div>

                    <?php endif; ?>

                </div><!-- .attachment -->

            </div><!-- .entry-attachment -->

        </section><!-- .entry-content -->

        <?php do_action( '__after_content' ) ?>

        <?php
        $html = ob_get_contents();
        ob_end_clean();
        echo apply_filters( 'tc_attachment_content', $html );

    }//end of function

}//end of class
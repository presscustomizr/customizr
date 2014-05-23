<?php
/**
* Single post content actions
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

class TC_post {

   //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;

    function __construct () {

        self::$instance =& $this;

        //posts parts actions
        add_action  ( '__before_content'              , array( $this , 'tc_post_header' ));
        add_action  ( '__after_content'               , array( $this , 'tc_post_footer' ));

        //add post header, content and footer to the __loop
        add_action  ( '__loop'                        , array( $this , 'tc_post_content' ));

        //selector filter
        add_filter ( '__article_selectors'            , array( $this , 'tc_post_selectors' ));
    }


    

     /**
     * Displays the conditional selectors of the article
     * 
     * @package Customizr
     * @since 3.0.10
     */
    function tc_post_selectors () {
      //check conditional tags : we want to show single post or single custom post types
      global $post;
      if ( !isset($post) )
      return;
      if ( 'page' == $post -> post_type || 'attachment' == $post -> post_type || !is_singular() )
        return;

        tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );

        echo 'id="post-'.get_the_ID().'" '.tc__f('__get_post_class' , 'row-fluid');
    }




    /**
     * The template part for displaying the single posts header
     *
     * @package Customizr
     * @since Customizr 3.0
     */
    function tc_post_header() {
      //check conditional tags : we want to show single post or single custom post types
      global $post;
      if ( 'page' == $post -> post_type || 'attachment' == $post -> post_type || !is_singular() )
        return;
      if ( tc__f( '__is_home_empty') )
        return;
      if( in_array( get_post_format(), tc__f('post_type_with_no_headers') ) )
        return;

      tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );

      ob_start();

      ?>

      <?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ ); ?>

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

      echo apply_filters( 'tc_post_header', $html);
    }



    /**
     * The default template for displaying single post content
     *
     * @package Customizr
     * @since Customizr 3.0
     */
    function tc_post_content() {
      //check conditional tags : we want to show single post or single custom post types
      global $post;
      if ( !isset($post) )
      return;
      if ( 'page' == $post -> post_type || 'attachment' == $post -> post_type || !is_singular() )
        return;
      if ( tc__f( '__is_home_empty') )
        return;
      
      tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );

      //display an icon for div if there is no title
      $icon_class = in_array(get_post_format(), array(  'quote' , 'aside' , 'status' , 'link' )) ? 'format-icon':'';

      ob_start();
        ?>
              
              <?php do_action( '__before_content' ); ?>

              <?php echo '<hr class="featurette-divider">' ?>

              <?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ ); ?>    

              <section class="entry-content <?php echo $icon_class ?>">
                  <?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>' , 'customizr' ) ); ?>
                  <?php wp_link_pages( array( 'before' => '<div class="pagination pagination-centered">' . __( 'Pages:' , 'customizr' ), 'after' => '</div>' ) ); ?>
              </section><!-- .entry-content -->

              <?php do_action( '__after_content' ) ?>
                  
        <?php
        $html = ob_get_contents();
        ob_end_clean();

        echo apply_filters( 'tc_post_content', $html );
    }




    /**
     * The template part for displaying the posts footer
     *
     * @package Customizr
     * @since Customizr 3.0
     */
    function tc_post_footer() {
      //check conditional tags : we want to show single post or single custom post types
      global $post;
      if ( 'page' == $post -> post_type || 'attachment' == $post -> post_type || !is_singular() )
        return;

      if ( !is_singular() )
        return;

      tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );

       ob_start();
      ?>
        <footer class="entry-meta">

            <?php if ( is_singular() && get_the_author_meta( 'description' ) ) : // If a user has filled out their description and this is a multi-author blog, show a bio on their entries. ?>
                <hr class="featurette-divider">
                <div class="author-info">
                  <div class="row-fluid">
                    <div class="comment-avatar author-avatar span2">
                       <?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'tc_author_bio_avatar_size' , 100 ) ); ?>
                    </div><!-- .author-avatar -->
                    <div class="author-description span10">
                        <?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ ); ?>
                       
                        <h2><?php printf( __( 'About %s' , 'customizr' ), get_the_author() ); ?></h2>
                        <p><?php the_author_meta( 'description' ); ?></p>

                        <div class="author-link">
                            <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
                                <?php printf( __( 'View all posts by %s <span class="meta-nav">&rarr;</span>' , 'customizr' ), get_the_author() ); ?>
                            </a>
                        </div><!-- .author-link -->
                    </div><!-- .author-description -->
                  </div>
                </div><!-- .author-info -->

            <?php endif; ?>

        </footer><!-- .entry-meta -->
      <?php
      $html = ob_get_contents();
      ob_end_clean();

      echo apply_filters( 'tc_post_footer', $html );
    }

}//end of class
<?php
/*
Template Name: Custom Page Example
*/
if ( apply_filters( 'czr_ms', false ) ):
    /*
    *  This is the reference Custom Page Example template if you use the theme Modern style
    */

    get_header();
    // This hook is used to render the following elements(ordered by priorities) :
    // slider
    // singular thumbnail
    do_action('__before_main_wrapper')
  ?>

    <div id="main-wrapper" class="section">

          <?php
            /*
            * Featured Pages | 10
            * Breadcrumbs | 20
            */
            do_action('__before_main_container')
          ?>

          <div class="<?php czr_fn_main_container_class() ?>" role="main">

            <?php do_action('__before_content_wrapper'); ?>

            <div class="<?php czr_fn_column_content_wrapper_class() ?>">

                <?php do_action('__before_content'); ?>

                <div id="content" class="<?php czr_fn_article_container_class() ?>">

                  <?php

                    do_action( '__before_loop' );

                    if ( have_posts() ) {
                        /**
                        * this will render the WordPress loop template located in templates/content/loop.php
                        * that will be responsible to load the page part template located in templates/content/singular/page_content.php
                        */
                        czr_fn_render_template( 'loop' );
                    }

                    /*
                     * Optionally attached to this hook :
                     * Comments | 30
                     */
                    do_action( '__after_loop' );
                  ?>
                </div>

                <?php
                  /*
                   * Optionally attached to this hook :
                   * Comments | 30
                   */
                  do_action('__after_content'); ?>

                <?php
                  /*
                  * SIDEBARS
                  */
                  if ( czr_fn_is_registered_or_possible('left_sidebar') )
                    get_sidebar( 'left' );

                  if ( czr_fn_is_registered_or_possible('right_sidebar') )
                    get_sidebar( 'right' );
                ?>

            </div><!-- .column-content-wrapper -->

            <?php do_action('__after_content_wrapper'); ?>


          </div><!-- .container -->

          <?php do_action('__after_main_container'); ?>

    </div><!-- #main-wrapper -->

    <?php do_action('__after_main_wrapper'); ?>

    <?php
      if ( czr_fn_is_registered_or_possible('posts_navigation') ) :
    ?>
      <div class="container-fluid">
        <?php
            czr_fn_render_template( "content/singular/navigation/singular_posts_navigation" );
        ?>
      </div>
    <?php endif ?>

<?php get_footer() ?>
<?php
  return;
endif;

/*
*  This is the reference Custom Page Example template if you use the theme Classical style
*/

do_action( '__before_main_wrapper' ); ##hook of the header with get_header

?>
<div id="main-wrapper" class="<?php echo implode(' ', apply_filters( 'tc_main_wrapper_classes' , array('container') ) ) ?>">

    <?php do_action( '__before_main_container' ); ##hook of the featured page (priority 10) and breadcrumb (priority 20)...and whatever you need! ?>

    <div class="container" role="main">
        <div class="<?php echo implode(' ', apply_filters( 'tc_column_content_wrapper_classes' , array('row' ,'column-content-wrapper') ) ) ?>">

            <?php do_action( '__before_article_container' ); ##hook of left sidebar?>

                <div id="content" class="<?php echo implode(' ', apply_filters( 'tc_article_container_class' , array( CZR_utils::czr_fn_get_layout(  czr_fn_get_id() , 'class' ) , 'article-container' ) ) ) ?>">

                    <?php do_action( '__before_loop' );##hooks the header of the list of post : archive, search... ?>

                        <?php if ( have_posts() ) : ?>

                            <?php while ( have_posts() ) : ##all other cases for single and lists: post, custom post type, page, archives, search, 404 ?>

                                <?php the_post(); ?>

                                <?php do_action( '__before_article' ) ?>
                                    <article <?php czr_fn__f( '__article_selectors' ) ?>>
                                        <?php do_action( '__loop' ); ?>
                                    </article>
                                <?php do_action( '__after_article' ) ?>

                            <?php endwhile; ?>

                        <?php endif; ##end if have posts ?>

                    <?php do_action( '__after_loop' );##hook of the comments and the posts navigation with priorities 10 and 20 ?>

                </div><!--.article-container -->

           <?php do_action( '__after_article_container' ); ##hook of left sidebar ?>

        </div><!--.row -->
    </div><!-- .container role: main -->

    <?php do_action( '__after_main_container' ); ?>

</div><!-- //#main-wrapper -->

<?php do_action( '__after_main_wrapper' );##hook of the footer with get_get_footer ?>

<?php
/**
 * The main template file. Includes the loop.
 *
 *
 * @package Customizr
 * @since Customizr 1.0
 */
if ( apply_filters( 'czr_ms', false ) ) {
  do_action( 'czr_ms_tmpl' );
  return;
}

// Developers => if you need to customize some templates of the theme, you'll find them in the templates/ folder.
// The code hereafter is used only when activating the classic style in the customizer > advanced options.

// When adding custom code, make sure to use a child theme and to always test in a staging environment.
// https://docs.presscustomizr.com/article/24-why-and-how-to-create-a-child-theme-with-wordpress
?>
<?php do_action( '__before_main_wrapper' ); ##hook of the header with get_header ?>
<div id="main-wrapper" class="<?php echo implode(' ', apply_filters( 'tc_main_wrapper_classes' , array('container') ) ) ?>">

    <?php do_action( '__before_main_container' ); ##hook of the featured page (priority 10) and breadcrumb (priority 20)...and whatever you need!?>

    <div class="container" role="main">
        <div class="<?php echo implode(' ', apply_filters( 'tc_column_content_wrapper_classes' , array('row' ,'column-content-wrapper') ) ) ?>">

            <?php do_action( '__before_article_container'); ##hook of left sidebar?>

                <div id="content" class="<?php echo implode(' ', apply_filters( 'tc_article_container_class' , array( CZR_utils::czr_fn_get_layout( czr_fn_get_id() , 'class' ) , 'article-container' ) ) ) ?>">

                    <?php do_action ('__before_loop');##hooks the heading of the list of post : archive, search... ?>

                        <?php if ( czr_fn__f('__is_no_results') || is_404() ) : ##no search results or 404 cases ?>

                            <article <?php czr_fn__f('__article_selectors') ?>>
                                <?php do_action( '__loop' ); ?>
                            </article>

                        <?php endif; ?>

                        <?php if ( have_posts() && !is_404() ) : ?>
                            <?php while ( have_posts() ) : ##all other cases for single and lists: post, custom post type, page, archives, search, 404 ?>
                                <?php the_post(); ?>

                                <?php do_action ('__before_article') ?>
                                    <article <?php czr_fn__f('__article_selectors') ?>>
                                        <?php do_action( '__loop' ); ?>
                                    </article>
                                <?php do_action ('__after_article') ?>

                            <?php endwhile; ?>

                        <?php endif; ##end if have posts ?>

                    <?php do_action ('__after_loop');##hook of the comments and the posts navigation with priorities 10 and 20 ?>

                 <?php // <hr> introduced in july 2019 to fix https://github.com/presscustomizr/customizr/issues/1767 ?>
                  <hr class="featurette-divider tc-mobile-separator">
                </div><!--.article-container -->

           <?php do_action( '__after_article_container'); ##hook of right sidebar ?>

        </div><!--.row -->
    </div><!-- .container role: main -->

    <?php do_action( '__after_main_container' ); ?>

</div><!-- //#main-wrapper -->

<?php do_action( '__after_main_wrapper' );##hook of the footer with get_footer ?>

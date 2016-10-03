<?php
/**
 * The template for displaying the article wrapper in a post list context
 *
 * In WP loop
 *
 * @package Customizr
 */
?>
<?php if ( czr_fn_get( 'is_loop_start' ) ) : ?>
<div class="post-list-plain short grid-container__full <?php czr_fn_echo('element_class') ?>"  <?php czr_fn_echo('element_attributes') ?>>
<?php endif ?>
  <article <?php czr_fn_echo( 'article_selectors' ) ?> >
    <div class="sections-wrapper <?php czr_fn_echo( 'sections_wrapper_class' ) ?>">
    <?php
      if ( czr_fn_has('media') ) { czr_fn_render_template('content/post-lists/post_list_media', 'post_list_media'); }
    ?>
      <section class="tc-content entry-content__holder">
        <?php if ( czr_fn_has('post_list_header') ) czr_fn_render_template('content/post-lists/headings/post_list_header-no_metas', 'post_list_header') ?>
        <div class="entry-content__wrapper row <?php czr_fn_echo('inner_wrapper_class') ?>">
          <?php if ( czr_fn_has('post_metas') && $cat = czr_fn_get( 'cat_list', 'post_metas' ) ) : ?>

            <div class="entry-meta col-md-3 col-xs-12 small caps">
              <?php echo $cat ?>
            </div>

          <?php  endif; ?>
          <div class="tc-content-inner <?php czr_fn_echo('content_inner_col') ?> <?php czr_fn_echo( 'content_class' ) ?>">
            <?php
              czr_fn_echo( 'the_post_list_content', 'post_list_content' );
            ?>
          </div>
        </div>
        <?php if ( czr_fn_has('post_list_footer') ) czr_fn_render_template('content/post-lists/footers/post_list_footer_author', 'post_list_footer' ); ?>
      </section>
    </div>
  </article>
<?php if ( czr_fn_get( 'is_loop_end' ) ) : ?>
</div>
<?php endif ?>
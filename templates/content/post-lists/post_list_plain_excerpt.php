<?php
/**
 * The template for displaying the article wrapper in a post list context
 *
 * In WP loop
 *
 * @package Customizr
 */
?>
<?php if ( czr_fn_is_loop_start() ) : ?>
<div class="grid-container__plain short <?php czr_fn_echo('element_class') ?>" <?php czr_fn_echo('element_attributes') ?>>
<?php
  do_action( '__post_list_plain_loop_start', czr_fn_get('id') );
endif ?>
  <article <?php czr_fn_echo( 'article_selectors' ) ?> >
    <div class="sections-wrapper <?php czr_fn_echo( 'sections_wrapper_class' ) ?>">
      <?php
        if ( ( $has_post_media = czr_fn_get('has_post_media') ) && czr_fn_has('media') ) {
          czr_fn_render_template('content/post-lists/singles/post_list_single_media', 'post_list_media', array(
             'has_post_media'           => $has_post_media,
             'is_full_image'            => czr_fn_get( 'is_full_image'  )
            )
          );
        }
      ?>
      <section class="tc-content entry-content__holder">
        <?php
        if ( czr_fn_has('post_list_header') )
          czr_fn_render_template('content/post-lists/singles/headings/post_list_single_header-no_metas', 'post_list_header', array(
            'entry_header_inner_class' => czr_fn_get( 'entry_header_inner_class' ),
            'element_class'            => czr_fn_get( 'entry_header_class' )
          ));
        ?>
        <div class="entry-content__wrapper row <?php czr_fn_echo('inner_wrapper_class') ?>">
          <?php
          if ( czr_fn_has('post_metas') && $cat = czr_fn_get( 'cat_list', 'post_metas', array( 'limit' => 3 ) ) ) : ?>

            <div class="entry-meta tax__container col-md-3 col-xs-12 small caps">
              <?php echo $cat ?>
            </div>

          <?php
          endif;
          /* Content Inner */
          czr_fn_render_template('content/post-lists/singles/contents/post_list_single_content_inner', 'post_list_content_inner',
            array(
              'content_inner_class' => czr_fn_get( 'content_inner_class' )
            )
          );

          ?>

        </div>
        <?php if ( czr_fn_has('post_list_footer') ) czr_fn_render_template('content/post-lists/singles/footers/post_list_single_footer_author', 'post_list_footer' ); ?>
      </section>
    </div>
  </article>
<?php if ( czr_fn_is_loop_end() ) :
  do_action( '__post_list_plain_loop_start', czr_fn_get('id') );
?>
</div>
<?php endif;
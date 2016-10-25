<?php
/**
 * The template for displaying the masonry article wrapper
 *
 * In WP loop
 *
 */
?>
<?php if ( czr_fn_is_loop_start() ) : ?>
<div class="grid grid-container__masonry <?php czr_fn_echo('element_class') ?>"  <?php czr_fn_echo('element_attributes') ?>>
<?php
  do_action( '__masonry_loop_start', czr_fn_get('id') );
endif ?>
  <article <?php czr_fn_echo( 'article_selectors' ) ?> >
    <div class="sections-wrapper grid-post">
    <?php
        if ( ( $has_post_media = czr_fn_get('has_post_media') ) && czr_fn_has('media') ) {
          czr_fn_render_template('content/post-lists/post_list_media', 'post_list_media', array(
             'has_post_media'           => $has_post_media,
             'is_full_image'            => czr_fn_get( 'is_full_image'  )
            )
          );
        }
        if ( czr_fn_has('content') ) {
          czr_fn_render_template('content/post-lists/post_list_content', 'post_list_content',
            array(
              'has_header_format_icon'  => czr_fn_get( 'has_header_format_icon' )
            )
          );
        }
    ?>
    </div>
  </article>
<?php if ( czr_fn_is_loop_end() ) :
  do_action( '__masonry_loop_end', czr_fn_get('id') );
?>
</div>
<?php endif ?>
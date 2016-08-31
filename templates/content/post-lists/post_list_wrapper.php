<?php
/**
 * The template for displaying the article wrapper in a post list context
 *
 * In WP loop
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<?php if ( czr_fn_get( 'is_loop_start' ) ) : ?>
<div class="row grid-container__alternate">
<?php endif ?>
  <article <?php czr_fn_echo( 'article_selectors' ) ?> <?php czr_fn_echo('element_attributes') ?>>
    <div class="sections-wrapper <?php czr_fn_echo( 'sections_wrapper_class' ) ?>">
    <?php

      if ( 'content' == czr_fn_get( 'place_1' ) ) {
        if ( czr_fn_has('content') ) { czr_fn_render_template('content/post-lists/post_list_content', 'post_list_content'); }
        if ( czr_fn_has('media') ) { czr_fn_render_template('content/post-lists/post_list_media', 'post_list_media'); }
      } else {
        if ( czr_fn_has('media') ) { czr_fn_render_template('content/post-lists/post_list_media', 'post_list_media'); }
        if ( czr_fn_has('content') ) { czr_fn_render_template('content/post-lists/post_list_content', 'post_list_content'); }
      }
    ?>
    </div>
  </article>
<?php if ( czr_fn_get( 'is_loop_end' ) ) : ?>
</div>
<?php endif ?>
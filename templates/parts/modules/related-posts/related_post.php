<?php
/*
* Related posts item template
*/
?>
<article <?php czr_fn_echo('article_selectors') ?> <?php czr_fn_echo('element_attributes') ?>>
  <div class="grid__item flex-wrap flex-lg-nowrap flex-row">
    <?php
      czr_fn_render_template(
        'content/common/media',
        array(
          'model_args' => array(
              'element_class'         => czr_fn_get_property('media_cols'),
              'media_type'            => 'czr-thumb',
              'thumb_size'            => 'tc-sq-thumb',
              'use_thumb_placeholder' => true
          )
        )
      );
      /* Content */
    ?>
      <section class="tc-content entry-content__holder <?php czr_fn_echo('content_cols') ?>">
        <div class="entry-content__wrapper">
        <?php
          /* header */
          czr_fn_render_template(
            'content/post-lists/item-parts/headings/post_list_item_header_date',
            array(
              'model_class' => 'content/post-lists/item-parts/headings/post_list_item_header'
            )
          );
          /* content inner */
          czr_fn_render_template( 'content/post-lists/item-parts/contents/post_list_item_content_inner' );
        ?>
        </div>
      </section>
  </div>
</article>
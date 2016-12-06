<?php
/*
* Related posts item template
*/
?>
<article <?php czr_fn_echo('article_selectors') ?> <?php czr_fn_echo('element_attributes') ?>>
  <div class="grid__item">
    <?php
      czr_fn_render_template(
        'content/post-lists/singles/post_list_single_media',
        array(
          'model_args' => array(
            'element_class' => czr_fn_get('media_cols'),
            'only_thumb'    => true,
            'has_post_media' => true
          )
        )
      );
      /* Content */
    ?>
      <section class="tc-content entry-content__holder <?php czr_fn_echo('content_cols') ?>" <?php czr_fn_echo('element_attributes') ?> >
        <div class="entry-content__wrapper">
        <?php
          /* header */
          czr_fn_render_template(
            'content/post-lists/singles/headings/post_list_single_header',
            array(
              'model_args' => array( 'cat_limit'  => 2 )
            )
          );
          /* content inner */
          czr_fn_render_template( 'content/post-lists/singles/contents/post_list_single_content_inner' );
        ?>
        </div>
      </section>
  </div>
</article>
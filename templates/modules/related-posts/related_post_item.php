<?php
/*
* Related posts item template
*/
?>
<article <?php czr_fn_echo('article_selectors') ?> <?php czr_fn_echo('element_attributes') ?>>
  <div class="grid-post tc-grid-post">
    <?php
      czr_fn_render_template('content/post-lists/post_list_media', 'post_list_media', array(
        'element_class' => czr_fn_get('media_col'),
        'only_thumb'    => true,
        'has_post_media' => true
      ) );
      czr_fn_render_template('content/post-lists/post_list_content', 'post_list_content', array(
        'element_class' => czr_fn_get('content_col'),
        'has_footer'    => false,
        'cat_limit'     => 2
      ));
    ?>
  </div>
</article>
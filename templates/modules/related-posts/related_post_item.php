<?php
/*
*
*/
?>
<article <?php czr_fn_echo('article_selectors') ?> <?php czr_fn_echo('element_attributes') ?>>
  <div class="grid-post tc-grid-post">
    <?php
      czr_fn_render_template('content/post-lists/post_list_media', 'post_list_media', array(
        'element_class' => 'col-xs-6',
        'only_thumb' => true,
        'has_post_media' => true
      ) );
      czr_fn_render_template('content/post-lists/post_list_content', 'post_list_content', array(
        'element_class' => 'col-xs-6',
        'has_footer'    => false,
      ));
    ?>
  </div>
</article>
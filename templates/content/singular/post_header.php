<?php
/**
* The template for displaying the post titles
*/
/*
* TODO: what to show? featured image, header image video .. ????
*/
?>
<div class="row page__header plain <?php czr_fn_echo( 'element_class' ) ?>" <?php czr_fn_echo('element_attributes') ?>>
  <div class="container header-content">
    <div class="header-content-inner">
      <?php if ( czr_fn_has('post_metas') && $cat = czr_fn_get( 'cat_list', 'post_metas', array( 'limit' => 3 ) ) ) : ?>
        <div class="entry-meta category-info">
          <?php echo $cat ?>
        </div>
      <?php endif; ?>
      <h1 class="header-title"><?php the_title() ?></h1>
      <div class="post-info">
        <?php
          if ( czr_fn_has('post_metas') && $pub_date = czr_fn_get( 'publication_date', 'post_metas' ) )
            echo $pub_date;

          if ( czr_fn_has( 'comment_info' ) && CZR() -> controllers -> czr_fn_is_possible( 'comment_info' ) ) :
        ?>
          <span class="v-separator">|</span>
          <?php czr_fn_render_template( 'modules/comment_info', 'comment_info' ) ?>
        <?php endif ?>
        <?php if ( czr_fn_has('post_metas') && $author = czr_fn_get( 'author', 'post_metas' ) ) : ?>
          <span class="v-separator">|</span>
          <?php echo $author ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
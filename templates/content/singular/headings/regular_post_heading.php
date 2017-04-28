<?php
/**
 * The template for displaying the header of a single post
 * In loop
 *
 * @package Customizr
 */
?>
<header class="entry-header <?php czr_fn_echo( 'element_class' ) ?>" <?php czr_fn_echo('element_attributes') ?>>
  <div class="entry-header-inner">
    <?php if ( czr_fn_has('post_metas') && $cat = czr_fn_get( 'cat_list', 'post_metas' ) ) : ?>
      <div class="entry-meta">
        <div class="tax__container">
          <?php echo $cat ?>
        </div>
      </div>
    <?php endif;

    if ( get_the_title() ) :

    ?>
    <h1 class="entry-title"><?php the_title() ?></h1>
    <?php

    endif;

    if ( czr_fn_has('edit_button') && (bool) $edit_post_link = get_edit_post_link() )
        czr_fn_edit_button( array( 'link'  => $edit_post_link ) );
    ?>
    <div class="header-bottom entry-meta">
      <div class="post-info">
        <?php
          if ( czr_fn_has('post_metas') && $author = czr_fn_get( 'author', 'post_metas' ) )
            echo $author;
          if ( czr_fn_has('post_metas') && $date = czr_fn_get( 'publication_date', 'post_metas' ) )
            if ( $author ) : ?><span class="v-separator">|</span><?php endif; echo $date;

          czr_fn_comment_info( $before = $date || $author ? '<span class="v-separator">|</span>' : '' );
        ?>
      </div>
    </div>
  </div>
</header>
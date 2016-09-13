<?php
/**
 * The template for displaying the footer of a post in a post list
 * In WP loop
 *
 * @package Customizr
 */
?>
<footer class="entry-footer" <?php czr_fn_echo('element_attributes') ?>>    
  <div class="entry-meta">
  <?php if ( czr_fn_has('post_metas') && czr_fn_get( 'tag_list', 'post_metas' ) ) : ?>
    <div class="post-tags">
      <?php czr_fn_echo( 'tag_list', 'post_metas' ) ?>
    </div> 
  <?php endif; ?>
    <div class="post-info">
      <?php if ( czr_fn_has('post_metas') && czr_fn_get( 'publication_date', 'post_metas' ) ) : ?>
        <?php czr_fn_echo( 'publication_date', 'post_metas' ) ?>
      <?php endif; ?>
      <?php if ( czr_fn_has( 'comment_info' ) && CZR() -> controllers -> czr_fn_is_possible( 'comment_info' ) ) : ?>
        <span class="v-separator">|</span>
        <?php czr_fn_render_template( 'modules/comment_info', 'comment_info' ) ?>        
      <?php endif?>
    </div>  
  </div>
</footer>
<?php
/**
* The template for displaying the author info small
*
*/
?>
<div class="author-info">
  <?php echo get_avatar( get_the_author_meta( 'user_email' ), 48 ) ?>
  <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) ?>" rel="author" title="<?php _e('View all the posts of the author', 'customizr'); ?>">
    <?php the_author() ?>
  </a>
</div>
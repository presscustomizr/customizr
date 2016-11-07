<?php
/**
 * The template for displaying the read more button
 *
 * In WP Loop
 */
?>
<div class="readmore-holder">
  <a class="moretag btn btn-more" href="<?php echo esc_url( get_permalink() ) ?>"><span><?php _e('Read more', 'customizr' ) ?></span></a>
</div>
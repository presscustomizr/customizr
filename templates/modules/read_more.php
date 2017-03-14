<?php
/**
 * The template for displaying the read more button
 *
 * In WP Loop
 */
?>
<div class="readmore-holder">
  <a class="moretag btn btn-more btn-dark" href="<?php echo esc_url( get_permalink() ) ?>"><span><?php _e('Read more &raquo;', 'customizr' ) ?></span></a>
</div>
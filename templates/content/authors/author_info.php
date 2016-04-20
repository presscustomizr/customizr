<?php
/**
 * The template for displaying the author bio
 * used in the single post footer and in the list of posts of a specific author as description below the list of posts title
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<hr class="featurette-divider">
<div class="author-info" <?php tc_echo('element_attributes') ?>>
  <div class="row-fluid">
    <div class="comment-avatar author-avatar span2">
     <?php echo get_avatar( get_the_author_meta( 'user_email' ), 100 ) ?>
    </div>
    <div class="span10">
      <h3><?php _e( 'About' , 'customizr' ) ?> <?php the_author() ?></h3>
      <p><?php the_author_meta( 'description' ) ?></p>
      <div class="author-link">
        <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) ?>" rel="author">
          <?php _e( 'View all posts by', 'customizr' ) ?> <?php the_author() ?><span class="meta-nav">&rarr;</span>
        </a>
      </div>
    </div>
  </div>
</div>

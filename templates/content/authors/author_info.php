<?php
/**
 * The template for displaying the author bio
 * used in the single post footer and in the list of posts of a specific author as description below the list of posts title
*/
?>
<section class="post-author author-info" <?php czr_fn_echo('element_attributes') ?>>
  <figure class="author-avatar">
    <?php echo get_avatar( get_the_author_meta( 'user_email' ), 120 ) ?>
    <figcaption>
      <span class="post-author-title hidden-xs-down"><?php _e('AUTHOR', 'customizr' ) ?></span>
      <h5 class="post-author-name"><?php the_author() ?></h5>
      <p><?php the_author_meta( 'description' ) ?></p>
      <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) ?>" rel="author" class="action-link" title="<?php _e('View all the posts of the author', 'customizr'); ?>">
        <?php the_author_posts() ?>&nbsp;<?php _e( 'posts', 'customizr') ?>
      </a>
      <!-- fake need to have social links somewhere -->
      <ul class="socials">
        <li><a href="http://facebook.com/"><i class="fa fa-facebook"></i></a></li>
        <li><a href="http://linkedin.com/"><i class="fa fa-linkedin"></i></a></li>
        <li><a href="http://twitter.com/"><i class="fa fa-twitter"></i></a></li>
        <li><a href="http://plus.google.com/"><i class="fa fa-instagram"></i></a></li>
        <li><a href="http://plus.google.com/"><i class="fa fa-pinterest"></i></a></li>
        <li><a href="http://plus.google.com/"><i class="fa fa-google-plus"></i> </a></li>
      </ul>
    </figcaption>
  </figure>
</section>
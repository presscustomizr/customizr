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
      <?php
        if ( czr_fn_has('author_socials') )
          czr_fn_render_template( 'modules/social_block', array( 'model_id' => 'author_socials' ) );
      ?>
    </figcaption>
  </figure>
</section>
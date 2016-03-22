<hr class="featurette-divider">
<div class="author-info">
  <div class="<?php echo tc_get( 'author_wrapper_class' ) ?>"> 
    <div class="<?php echo tc_get( 'author_avatar_class' ) ?>">
     <?php echo get_avatar( get_the_author_meta( 'user_email' ), tc_get( 'author_avatar_size' ) ) ?>
    </div>
    <div class="<?php echo tc_get( 'author_content_class' ) ?>">
      <h3><?php _e( 'About' , 'customizr' ) ?> <?php the_author() ?></h3>
      <p><?php the_author_meta( 'description' ) ?></p>
      <div class="author-link">
        <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) ?>" rel="author">
          <?php _e( 'View all posts by') ?> <?php the_author() ?><span class="meta-nav">&rarr;</span>
        </a>
      </div>
    </div>
  </div>
</div>

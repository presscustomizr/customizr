<?php
/**
 * The template for displaying the author bio
 * used in the single post footer and in the list of posts of a specific author as description below the list of posts title
*/
  $authors_id     = czr_fn_get_property( 'authors_id' );
  $authors_number = czr_fn_get_property( 'authors_number' );
?>
<section class="post-author <?php czr_fn_echo('element_class') ?>" <?php czr_fn_echo('element_attributes') ?>>
  <div class="post-author__wrapper">
    <h4 class="post-author-title"><?php echo _n( 'AUTHOR', 'AUTHORS', $authors_number, 'customizr' ) ?></h4>
  <?php foreach ( $authors_id as $author_id ) :?>
    <figure class="author-info">
      <span class="author-avatar"><?php echo get_avatar( get_the_author_meta( 'user_email', $author_id ), 120 ) ?></span>
      <figcaption>
        <h5 class="post-author-name author_name"><?php echo get_the_author_meta( 'display_name', $author_id ) ?></h5>
        <div class="post-author-description"><?php the_author_meta( 'description', $author_id ) ?></div>
        <a href="<?php echo esc_url( get_author_posts_url( $author_id ) ) ?>" rel="author" class="action-link" title="<?php _e( 'View all the posts of the author', 'customizr' ) ?>">
          <?php
            $author_posts = count_user_posts( $author_id );
            printf( _n('%s post', '%s posts', $author_posts , 'customizr' ), $author_posts );
          ?>
        </a>
        <!-- fake need to have social links somewhere -->
        <?php
          if ( czr_fn_is_registered_or_possible( 'author_socials' ) ) {
            czr_fn_render_template( 'modules/common/social_block', array( 'model_id' => 'author_socials' ) );
          }
        ?>
      </figcaption>
    </figure>
  <?php endforeach; ?>
  </div>
</section>
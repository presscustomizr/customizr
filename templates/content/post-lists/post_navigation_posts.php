<?php
/**
 * The template for displaying the post navigation in the lists of posts
 *
 * @package WordPress
 * @subpackage Customizr
 * @since Customizr 3.5.0
 */
?>
<nav id="nav-below" class="<?php tc_echo( 'element_class' ) ?>" <?php tc_echo('element_attributes') ?>>
  <hr class="featurette-divider">
  <h3 class="assistive-text">
    <?php  _e( 'Post navigation' , 'customizr' ) ?>
  </h3>
  <ul class="pager" <?php tc_echo('element_attributes') ?>>
  <?php if ( null != $prev_link = get_next_posts_link(
         __( '<span class="meta-nav">&larr;</span> Older posts' , 'customizr' ), //label
         0 //max pages
        ) ) : ?>
    <li class="previous">
      <span class="nav-previous">
        <?php echo $prev_link ?>
      </span>
    </li>
  <?php endif; ?>
  <?php if ( null != $next_link = get_previous_posts_link(
         __( 'Newer posts <span class="meta-nav">&rarr;</span>' , 'customizr' ), //label
         0 //max pages
        ) ) : ?>
  <li class="next">
    <span class="nav-next">
    <?php echo $next_link ?>
    </span>
  </li>
  <?php endif ?>
</ul>
</nav>

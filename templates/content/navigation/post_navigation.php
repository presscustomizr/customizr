<?php
/**
 * The template for displaying the post navigation wrapper (both for singular and post list types )
 *
 * @package WordPress
 * @subpackage Customizr
 * @since Customizr 3.5.0
 */
?>
<nav id="nav-below" class="<?php tc_echo( 'element_class' ) ?>" <?php tc_echo('element_attributes') ?>>

  <?php if ( 'post_list' != tc_get( 'type' ) ) : ?>
    <hr class="featurette-divider">
  <?php endif; ?>

  <h3 class="assistive-text">
    <?php  _e( 'Post navigation' , 'customizr' ) ?>
  </h3>

  <?php
    if ( 'post_list' != tc_get( 'type' ) ) {
      //do_action( '__post_navigation_singular__' );
      tc_render_template('post_navigation_singular', 'post_navigation_links_singular');
    }
    else {
      //do_action( '__post_navigation_posts__' );
      tc_render_template('post_navigation_posts', 'post_navigation_links_posts');
    }
  ?>

</nav>
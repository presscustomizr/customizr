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

<?php

/* Case: post lists context */
if ( 'post_list' == tc_get( 'type' ) ):

?>
  <h3 class="assistive-text">
    <?php  _e( 'Post navigation' , 'customizr' ) ?>
  </h3>
  <?php do_action( '__post_navigation_posts__' ) ?>
<?php

/* Case: singular context */
else :

?>
  <h3 class="assistive-text">
    <?php  _e( 'Post navigation' , 'customizr' ) ?>
  </h3>
  <?php do_action( '__post_navigation_singular__' ) ?>
<?php

  endif

?>
</nav>

<?php
/**
 * The template for displaying the headings in post lists and singular
 *
 * @package WordPress
 * @subpackage Customizr
 * @since Customizr 3.5.0
 */

/* Case we're displaying the headings of the list of posts, archives, categories, tags, search ,, */
if ( 'content' != tc_get( 'type' ) ) :

?>
<header class="<?php tc_echo( 'element_class' ) ?>" <?php tc_echo('element_attributes') ?>>
  <?php do_action( '__headings_posts_list__' ) ?>
  <hr class="featurette-divider headings post-lists">
</header>
<?php

/* Case we're displaying the headings of the contents such as posts/pages/attachments both as singular and as elements of lists of posts */

else :

?>
<header class="<?php tc_echo( 'element_class' ) ?>" <?php tc_echo('element_attributes') ?>>
  <?php do_action( '__headings_content__' ) ?>
  <?php if ( is_singular() ) : ?>
    <hr class="featurette-divider headings singular-content">
  <?php endif ?>
</header>
<?php endif;

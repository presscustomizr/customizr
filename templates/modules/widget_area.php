<?php
/**
 * The template for displaying the single widget area
 *
 * @package WordPress
 * @subpackage Customizr
 * @since Customizr 3.5.0
 */

/* Has this a wrapper */
if ( tc_get('element_id') ):

?>
<div class="<?php tc_echo('element_class') ?>" id="<?php tc_echo('element_id') ?> <?php tc_echo('element_attributes') ?>">
<?php

  endif

?>
  <?php dynamic_sidebar(  tc_get( 'id' ) ) ?>
<?php

  /* close the wrapper if needed */
if ( tc_get('element_id') ) :

?>
  </div>
<?php endif;

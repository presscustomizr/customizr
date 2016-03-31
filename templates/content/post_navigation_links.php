<?php
/**
 * The template for displaying the post navigation links (prev/next buttons)
 * both in singular and post list context
 *
 * @package WordPress
 * @subpackage Customizr
 * @since Customizr 3.5.0
 */
?>
<ul class="pager" <?php tc_echo('element_attributes') ?>>
  <?php if ( tc_get( 'prev_link' ) ): ?>
  <li class="previous">
    <span class="nav-previous">
    <?php echo  tc_get( 'prev_link' ) ?>
    </span>
  </li>
  <?php endif; ?>
  <?php if ( tc_get( 'next_link' ) ): ?>
  <li class="next">
    <span class="nav-next">
    <?php echo  tc_get( 'next_link' ) ?>
    </span>
  </li>
  <?php endif ?>
</ul>

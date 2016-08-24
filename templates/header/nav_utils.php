<?php
/**
 * The template for displaying the navbar wrapper.
 * The navbar wrapper contains:
 * Social Block
 * Tagline
 * ( Woocommerce Cart Icon )
 * Navbar menus
 * Navbar menu buttons
 */
?>
<div class="primary-nav__utils" <?php czr_fn_echo('element_attributes') ?>>
  <ul class="nav navbar-nav utils inline-list">
    <?php czr_fn_render_template('header/nav_search'); ?>
    <?php if ( czr_fn_has('wc_cart', null, $only_registered = true ) ) : ?>
      <li class="primary-nav__woocart hidden-tablet">
        <?php czr_fn_render_template('header/woocommerce_cart'); ?>
      </li>  
    <?php endif ?>
  </ul>   
    <?php  if ( ! ( czr_fn_has('navbar_secondary_menu') ) ) : ?>
      <?php czr_fn_render_template('header/header_socials'); ?>
    <?php endif ?>
  </ul>
</div>


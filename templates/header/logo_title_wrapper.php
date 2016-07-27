<?php
/**
 * The template for displaying the logo wrapper
 */
?>
  <div class="col-lg-0 col-md-12 branding__container">
    <div class="branding">
      <?php czr_fn_render_template('header/title', 'new_title'); ?>
      <?php czr_fn_render_template('header/tagline', 'new_tagline'); ?>
    </div>
    <div class="mobile-utils__wrapper">
      <?php if ( czr_fn_has('wc_cart', null, $only_registered = true ) ) : ?>
        <div class="mobile-woocart__container">
          <a href="#" class="woocart"><i class="icn-icn-shoppingcart"></i><sup>0</sup></a>
        </div>
      <?php endif ?>
      <?php czr_fn_render_template('header/menu_button'); ?>
    </div>
  </div>

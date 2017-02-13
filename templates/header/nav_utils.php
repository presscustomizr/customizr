<?php
/**
 * The template for displaying the primary navbar utils.
 * Contains:
 * Search Button
 * ( Woocommerce Cart Icon )
 * (Socials)
 */
?>
<div class="primary-nav__utils" <?php czr_fn_echo('element_attributes') ?>>
  <ul class="nav navbar-nav utils inline-list">
    <?php
    if ( czr_fn_has( 'nav_search' ) ) czr_fn_render_template( 'header/nav_search' );

    if ( czr_fn_has( 'woocommerce_cart', null, $only_registered = true ) ) : ?>
      <?php
        czr_fn_render_template( 'header/woocommerce_cart', array(
          'model_args' => array(
            'element_class' => array('primary-nav__woocart', 'hidden-md-down'),
            'element_tag' => 'li'
          )
        ) );
      ?>
    <?php
    endif ?>
  </ul>
  <?php if ( ( !czr_fn_has('topnav') ||  !czr_fn_has('social_in_topnav') ) && czr_fn_has('header_social_block') ) : ?>
    <div class="primary-nav__socials social-links">
      <?php czr_fn_render_template('modules/social_block' ) ?>
    </div>
  <?php endif ?>
</div>
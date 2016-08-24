<?php
/**
 * The template for displaying the Woocommerce Cart in the header
 */
?>

<a href="<?php echo esc_url( WC()->cart->get_cart_url() ); ?>" title="<?php _e( 'View your shopping cart', 'customizr' ); ?>" class="woocart cart-contents" <?php czr_fn_echo('element_attributes') ?>>
  <i class="icn-shoppingcart"></i><?php czr_fn_echo( 'wc_cart_count' ) ?>
</a>



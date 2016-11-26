<?php
/**
 * The template for displaying the Woocommerce Cart in the header
 */
?>
<?php $WC = WC() ?>
<a href="<?php echo esc_url( $WC->cart->get_cart_url() ); ?>" title="<?php _e( 'View your shopping cart', 'customizr' ); ?>" class="woocart cart-contents" <?php czr_fn_echo('element_attributes') ?>>
  <i class="icn-shoppingcart"></i><?php czr_fn_echo( 'wc_cart_count_html' ) ?>
</a>
<?php
/* Actually the following should not be added in the mobile template
* to achieve that we should have to different models and set a flag to check here.
*/
if ( czr_fn_get( 'display_widget' ) ) :
?>
<ul class="dropdown-menu">
  <li>
    <?php the_widget( 'WC_Widget_Cart', 'title=' ); ?>
  </li>
</ul>
<?php endif;



<?php
/**
 * The template for displaying the Woocommerce Cart in the header
 */
?>
<div class="tc-wc-menu tc-open-on-hover span1">
 <ul class="tc-wc-header-cart nav tc-hover-menu">
   <li class="<?php czr_echo( 'current_menu_item_class' ) ?> menu-item">
     <a class="cart-contents" href="<?php echo esc_url( WC()->cart->get_cart_url() ); ?>" title="<?php _e( 'View your shopping cart', 'customizr' ); ?>">
       <span class="count btn-link tc-wc-count"><?php echo WC()->cart->get_cart_contents_count() ? WC()->cart->get_cart_contents_count() : '' ?></span>
     </a>
    <?php
    ?>
    <?php if ( ! czr_get( 'is_checkout_cart' ) ) : //do not display the dropdown in the cart or checkout page ?>
      <ul class="dropdown-menu">
       <li>
         <?php the_widget( 'WC_Widget_Cart', 'title=' ); ?>
        </li>
      </ul>
    <?php endif; ?>
   </li>
  </ul>
</div>


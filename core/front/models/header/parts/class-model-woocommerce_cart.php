<?php
class CZR_woocommerce_cart_model_class extends CZR_Model {

    public $defaults = array( 'display_widget' => true );

    private static $_woocart_filter_added;
    private static $_woocart_style_printed;

    public function __construct( $model ) {
        parent::__construct( $model);

        //This filter should be added once only.
        //There might be various instances of this object but we don't want it to be added more than once
        if ( empty( self::$_woocart_filter_added ) ) {
            self::$_woocart_filter_added = true;
            add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'czr_fn_woocommerce_add_to_cart_fragment' ) );
        }

    }

    function czr_fn_get_display_widget() {
        if ( $this->display_widget ) {
            return function_exists( 'czr_fn_wc_is_checkout_cart' ) ? ! czr_fn_wc_is_checkout_cart() : true;
        }

        return false;
    }

    // Ensure cart contents update when products are added to the cart via AJAX (place the following in functions.php)
    function czr_fn_woocommerce_add_to_cart_fragment( $fragments ) {
        $fragments['sup.czr-wc-count'] = $this -> czr_fn_get_wc_cart_count_html();

        return $fragments;
    }


    function czr_fn_get_wc_cart_count_html() {
        $WC          = WC();
        $_cart_count = $WC->cart->get_cart_contents_count();
        return sprintf( '<sup class="count czr-wc-count">%1$s</sup>', $_cart_count ? $_cart_count : '' );
    }


    /**
    * @hook czr_fn_user_options_style
    */
    function czr_fn_user_options_style_cb( $_css ) {
        //This css should be added once only.
        //There might be various instances of this object but we don't want it to be added more than once
        if ( empty( self::$_woocart_style_printed ) ) {
            self::$_woocart_style_printed = true;

            return sprintf( "%s\n%s",
                  $_css,
                  ".sticky-enabled .czr-wccart-off .primary-nav__woocart { display: none; }
                  .logo-center .primary-nav__woocart .dropdown-menu,
                  .logo-left .primary-nav__woocart .dropdown-menu{ right: 0; left: auto; }/*open left*/
            ");
        }

        return $_css;
    }/*end rendering the cart icon in the header */

}//end class
<?php
class CZR_woocommerce_cart_model_class extends CZR_Model {

  public $display_widget;

  public function __construct( $model ) {
    parent::__construct( $model);
    add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'czr_fn_woocommerce_add_to_cart_fragment' ) );
    add_action( 'pre_rendering_view_header'        , array( $this, 'pre_rendering_view_header_cb' ) );
    //print_r($model);
  }

  function czr_fn_extend_params( $model = array() ) {
    $_model = array(
      'display_widget' => function_exists( 'czr_fn_wc_is_checkout_cart' ) ? ! czr_fn_wc_is_checkout_cart() : true
    );

    return array_merge( $model, $_model );
  }

  // Ensure cart contents update when products are added to the cart via AJAX (place the following in functions.php)
  function czr_fn_woocommerce_add_to_cart_fragment( $fragments ) {
    if ( 1 == esc_attr( czr_fn_get_opt( 'tc_woocommerce_header_cart' ) ) ) {
      $fragments['sup.tc-wc-count'] = $this -> czr_fn_get_wc_cart_count_html();
    }
    return $fragments;
  }

  function czr_fn_get_wc_cart_count_html() {
    $WC          = WC();
    $_cart_count = $WC->cart->get_cart_contents_count();
    return sprintf( '<sup class="count tc-wc-count">%1$s</sup>', $_cart_count ? $_cart_count : '' );
  }

  /*
  * parse header model before rendering to add 'sticky' wccart visibility class
  */
  function pre_rendering_view_header_cb( $header_model ) {
    if ( ! is_array( $header_model -> element_class ) )
      $header_model -> element_class = explode( ' ', $header_model -> element_class );
    $_class = ( 1 != esc_attr( czr_fn_get_opt( 'tc_woocommerce_header_cart_sticky' ) ) ) ? 'tc-wccart-off' : 'tc-wccart-on';
    array_push( $header_model -> element_class, $_class );
  }

  /**
  * @hook czr_fn_user_options_style
  */
  function czr_fn_user_options_style_cb( $_css ) {
    /* The only real decision I took here is the following:
    * I let the "count" number possibily overflow the parent (span1) width
    * so that as it grows it won't break on a new line. This is quite an hack to
    * keep the cart space as small as possible (span1) and do not hurt the tagline too much (from span7 to span6). Also nobody will, allegedly, have more than 10^3 products in its cart
    */
    $_header_layout      = esc_attr( czr_fn_get_opt( 'tc_header_layout') );
    $_resp_pos_css       = 'right' == $_header_layout ? 'float: left;' : '';
    $_wc_t_align         = 'left';
    //dropdown top arrow, as we open the drodpdown on the right we have to move the top arrow accordingly
    $_dd_top_arrow       = '.navbar .tc-wc-menu .nav > li > .dropdown-menu:before { right: 9px; left: auto;} .navbar .tc-wc-menu .nav > li > .dropdown-menu:after { right: 10px; left: auto; }';
    //rtl custom css
    if ( is_rtl() ) {
      $_wc_t_align       = 'right';
      $_dd_top_arrow     = '';
    }
    return sprintf( "%s\n%s",
          $_css,
          ".sticky-enabled .tc-header .tc-wccart-off .tc-wc-menu { display: none; }
          .primary-nav__woocart.open .dropdown-menu {
            display: block;
            right: 0;
            left: auto;
          }
    ");
  }/*end rendering the cart icon in the header */
}//end class
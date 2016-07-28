<?php
class CZR_cl_woocommerce_cart_model_class extends CZR_cl_Model {

  public $is_checkout_cart;
  public $current_menu_item_class;
  
  public function __construct( $model ) {
    parent::__construct( $model);
    add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'czr_fn_woocommerce_add_to_cart_fragment' ) );
    add_action( 'pre_rendering_view_header'        , array( $this, 'pre_rendering_view_header_cb' ) );
  }

  function czr_fn_extend_params( $model = array() ) {
    $is_checkout_cart        = function_exists( 'czr_fn_wc_is_checkout_cart' ) ? czr_fn_wc_is_checkout_cart() : false;
    $current_menu_item_class = $is_checkout_cart ? 'current-menu-item' : '';
    return array_merge( $model, compact( 'is_checkout_cart', 'current_menu_item_class' ) );
  }
  // Ensure cart contents update when products are added to the cart via AJAX (place the following in functions.php)
  function czr_fn_woocommerce_add_to_cart_fragment( $fragments ) {
    if ( 1 == esc_attr( czr_fn_get_opt( 'tc_woocommerce_header_cart' ) ) ) {
      $_cart_count = WC()->cart->get_cart_contents_count();
      $fragments['span.tc-wc-count'] = sprintf( '<span class="count btn-link tc-wc-count">%1$s</span>', $_cart_count ? $_cart_count : '' );
    }
    return $fragments;
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
    return;
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
          ".sticky-enabled .tc-header.tc-wccart-off .tc-wc-menu { display: none; }
           .sticky-enabled .tc-tagline-off.tc-wccart-on .tc-wc-menu { margin-left: 0; margin-top: 3px; }
           .sticky-enabled .tc-tagline-off.tc-wccart-on .btn-toggle-nav { margin-top: 5px; }
           .tc-header .tc-wc-menu .nav { text-align: right; }
           $_dd_top_arrow
           .tc-header .tc-wc-menu .dropdown-menu {
              right: 0; left: auto; width: 250px; padding: 2px;
           }
           .tc-header .tc-wc-menu {
             float: right; clear:none; margin-top: 1px;
           }
           .tc-header .tc-wc-menu .nav > li {
             float:none;
           }
           .tc-wc-menu ul.dropdown-menu .buttons a,
           .tc-wc-menu ul {
             width: 100%;
             -webkit-box-sizing: border-box;
             -moz-box-sizing: border-box;
             box-sizing: border-box;
           }
           .tc-wc-menu ul.dropdown-menu .buttons a {
             margin: 10px 5px 0 0px; text-align: center;
           }
           .tc-wc-menu .nav > li > a:before {
             content: '\\f07a';
             position:absolute;
             font-size:1.6em; left: 0;
           }
           .tc-header .tc-wc-menu .nav > li > a {
             position: relative;
             padding-right: 0 !important;
             padding-left: 0 !important;
             display:inline-block;
             border-bottom: none;
             text-align: right;
             height: 1em;
             min-width:1.8em;
           }
           .tc-wc-menu .count {
             font-size: 0.7em;
             margin-left: 2.1em;
             position: relative;
             top: 1em;
             pointer-events: none;
           }
           .tc-wc-menu .woocommerce.widget_shopping_cart li {
             padding: 0.5em;
           }
           .tc-header .tc-wc-menu .woocommerce.widget_shopping_cart p,
           .tc-header .tc-wc-menu .woocommerce.widget_shopping_cart li {
             padding-right: 1em;
             padding-left: 1em;
             text-align: $_wc_t_align;
             font-size: inherit; font-family: inherit;
           }
           .tc-wc-menu .widget_shopping_cart .product_list_widget li a.remove {
             position: relative; float: left; top: auto; margin-right: 0.2em;
           }
           /* hack for the first letter issue */
           .tc-wc-menu .count:before {
             content: '';
           }
           .tc-wc-menu .widget_shopping_cart .product_list_widget {
             max-height: 10em;
             overflow-y: auto;
             padding: 1em 0;
           }
           @media (max-width: 979px) {
            .tc-wc-menu[class*=span] { width: auto; margin-top:7px; $_resp_pos_css }
            .tc-wc-menu .dropdown-menu { display: none !important;}
          }
          @media (max-width: 767px) { .sticky-enabled .tc-wccart-on .brand { width: 50%;} }
    ");
  }/*end rendering the cart icon in the header */
}//end class
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
<div class="navbar-wrapper clearfix <?php czr_echo( 'element_class' ) ?>" <?php czr_echo('element_attributes') ?>>
  <div class="navbar resp">
    <div class="navbar-inner" role="navigation">
        <div class="row-fluid">
        <?php
          if ( ! is_rtl() ) {
            if ( czr_has('header_social_block') )
              czr_render_template('modules/social_block', 'header_social_block');
            if ( czr_has('wc_cart', null, $only_registered = true ) )
              czr_render_template('header/woocommerce_cart', 'wc_cart');
            if ( czr_has('tagline') )
              czr_render_template('header/tagline');
          } else {
            if ( czr_has('tagline') )
              czr_render_template('header/tagline');
           if ( czr_has('wc_cart', null, $only_registered = true ) )
              czr_render_template('header/woocommerce_cart', 'wc_cart');
            if ( czr_has('header_social_block') )
                czr_render_template('modules/social_block', 'header_social_block');
          }


          if ( czr_has('navbar_menu') )
            czr_render_template('header/menu', 'navbar_menu');

          if ( czr_has('mobile_menu_button') )
            czr_render_template('header/menu_button', 'mobile_menu_button');

          if ( czr_has('sidenav_navbar_menu_button') )
            czr_render_template('header/menu_button', 'sidenav_navbar_menu_button');

          if ( czr_has('navbar_secondary_menu') )
            czr_render_template('header/menu', 'navbar_secondary_menu');
        ?>
        </div><!-- /.row-fluid -->
    </div><!-- /.navbar-inner -->
  </div><!-- /.navbar  -->
</div>

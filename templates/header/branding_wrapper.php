<?php
/**
 * The template for displaying the branding wrapper
 */
?>
  <div class="branding__container <?php czr_fn_echo('element_class') ?>" <?php czr_fn_echo('element_attributes') ?>>
    <div class="branding flex-column">
    <?php
      if ( czr_fn_has('logo_wrapper') ){
        czr_fn_render_template( 'header/logo_wrapper' );
      } else
        czr_fn_render_template( 'header/title' );

      if ( czr_fn_has( 'branding_tagline' ) )
        czr_fn_render_template( 'header/tagline' );

    ?>
    </div>
    <div class="mobile-utils__wrapper hidden-lg-up nav__utils">
      <ul class="nav utils row flex-row flex-nowrap">
<?php
        if ( czr_fn_has('woocommerce_cart', null, $only_registered = true ) ) :
            czr_fn_render_template( 'header/woocommerce_cart', array(
              'model_args' => array(
                'element_class'  => array('mobile-woocart__container'),
                'display_widget' => false
              )
            ) );
        endif;
        if ( czr_fn_has( 'sidenav' ) ) :
            czr_fn_render_template( 'header/menu_button', array(
              'model_args' => array(
                'data_attributes' => 'data-toggle="sidenav" aria-expanded="false"',
              )
            ) );
        else :
            czr_fn_render_template( 'header/menu_button', array( 'model_args' => array() ) );
        endif;
?>
      </ul>
    </div>
    <?php if ( czr_fn_get('with_nav_utils') && czr_fn_has('nav_utils') ) czr_fn_render_template( 'header/nav_utils' ) ?>
  </div>

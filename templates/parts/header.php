<?php
/**
 * The template for displaying the site header
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<?php do_action( '__before_header' ) ?>
<header class="tpnav-header__header tc-header <?php czr_fn_echo('element_class') ?>" role="banner" <?php czr_fn_echo('element_attributes') ?>>
  <div class="header-navbars__wrapper <?php czr_fn_echo('elements_container_class') ?>">

    <div class="container-fluid topnav-navbars__container">
          <?php
            //czr_fn_render_template always check if the model is registered or possible.
            czr_fn_render_template( 'header/topbar',
              array(
                'model_args' => array(
                  'element_class' => czr_fn_get_property( 'topbar_nbwrapper_class' )
                )
              )
            );
          ?>
          <?php
              czr_fn_render_template( 'header/navbar_wrapper',//<=header/navbar_wrapper
                array(
                  'model_id' => 'navbar_wrapper',
                  'model_args' => array(
                    'element_class' => czr_fn_get_property( 'primary_nbwrapper_class' )
                  )
                )
              );
          ?>
          <?php
            czr_fn_render_template( 'header/mobile_navbar_wrapper',
              array(
                'model_args' => array(
                  'element_class' => czr_fn_get_property( 'mobile_nbwrapper_class' )
                )
              )
            )
          ?>
    </div>

  </div>
</header>
<?php do_action( '__after_header' ) ?>

<?php
/**
 * The template for displaying the site header
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<?php do_action( '__before_header' ) ?>
<header class="tpnav-header__header tc-header <?php czr_fn_echo('element_class') ?>" <?php czr_fn_echo('element_attributes') ?>>
    <?php
      //czr_fn_render_template always check if the model is registered or possible.
      czr_fn_render_template( 'header/topbar_wrapper',
        array(
          'model_args' => array(
            'element_class' => czr_fn_get_property( 'topbar_nbwrapper_class' ),//for ex : 'd-none d-lg-block desktop-sticky'
            'element_inner_class' => czr_fn_get_property( 'topbar_nbwrapper_container_class' ),//for ex: 'container-fluid'
          )
        )
      );
    ?>
    <?php
        czr_fn_render_template( 'header/navbar_wrapper',//<=header/navbar_wrapper
          array(
            'model_id' => 'navbar_wrapper',
            'model_args' => array(
              'element_class' => czr_fn_get_property( 'primary_nbwrapper_class' ),//for ex : 'primary-navbar__wrapper row align-items-center flex-lg-row d-none d-lg-block'
              'element_inner_class' => czr_fn_get_property( 'primary_nbwrapper_container_class' )//for ex: 'container-fluid'
            )
          )
        );
    ?>
    <?php
      czr_fn_render_template( 'header/mobile_navbar_wrapper',
        array(
          'model_args' => array(
            'element_class'        => czr_fn_get_property( 'mobile_nbwrapper_class' ),
            'inner_elements_class' => czr_fn_get_property( 'mobile_inner_contained_class' )//for ex: 'container-fluid'
          )
        )
      )
    ?>
</header>
<?php do_action( '__after_header' ) ?>

<?php
/**
 * The template for displaying a menu ( both main and secondary in navbar or/and the sidenav one)
 */
?>
<div class="nav__menu-wrapper <?php czr_fn_echo('element_class') ?>" <?php czr_fn_echo('element_attributes') ?>>
  <?php
  wp_nav_menu( array(
    'theme_location'  => czr_fn_get_property( 'theme_location' ),
    'container'       => null,
    'menu_class'      => czr_fn_get_property( 'menu_class' ),
    'fallback_cb'     => czr_fn_get_property( 'fallback_cb' ),
    'walker'          => czr_fn_get_property( 'walker' ),
    'menu_id'         => czr_fn_get_property( 'menu_id' ),
    'link_before'     => '<span>',
    'link_after'      => '</span>',
    'dropdown_type'   => czr_fn_get_property( 'dropdown_type' )
  ) );
  ?>
</div>
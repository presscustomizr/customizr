<?php
/**
 * The template for displaying a menu ( both main and secondary in navbar or/and the sidenav one)
 */
?>
<div class="<?php czr_fn_echo('element_class') ?>" <?php czr_fn_echo('element_attributes') ?>>
  <?php
  wp_nav_menu( array(
    'theme_location'  => czr_fn_get( 'theme_location' ),
    'container'       => null,
    'menu_class'      => czr_fn_get( 'menu_class' ),
    'fallback_cb'     => czr_fn_get( 'fallback_cb' ),
    'walker'          => czr_fn_get( 'walker' ),
    'menu_id'         => czr_fn_get( 'menu_id' ),
    'link_before'     => '<span>',
    'link_after'      => '</span>'
  ) );
  ?>  
</div>
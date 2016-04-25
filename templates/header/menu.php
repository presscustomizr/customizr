<?php
/**
 * The template for displaying a menu ( both main and secondary in navbar or/and the sidenav one)
 */
?>
<div class="<?php czr_echo('element_class') ?>" <?php czr_echo('element_attributes') ?>>
  <?php
  wp_nav_menu( array(
    'theme_location'  => czr_get( 'theme_location' ),
    'menu_class'      => czr_get( 'menu_class' ),
    'fallback_cb'     => czr_get( 'fallback_cb' ),
    'walker'          => czr_get( 'walker' )
  ) );
  ?>
</div>

<?php
/**
 * The template for displaying a menu ( both main and secondary in navbar or/and the sidenav one)
 */
?>
<div class="<?php tc_echo('element_class') ?>" <?php tc_echo('element_attributes') ?>>
  <?php
  wp_nav_menu( array(
    'theme_location'  => tc_get( 'theme_location' ),
    'menu_class'      => tc_get( 'menu_class' ),
    'fallback_cb'     => tc_get( 'fallback_cb' ),
    'walker'          => tc_get( 'walker' )
  ) );
  ?>
</div>

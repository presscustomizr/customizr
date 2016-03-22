<?php
wp_nav_menu( array(
  'theme_location'  => tc_get( 'theme_location' ),
  'menu_class'      => tc_get( 'menu_class' ),
  'fallback_cb'     => tc_get( 'fallback_cb' ),
  'walker'          => tc_get( 'walker' )
) );
?>

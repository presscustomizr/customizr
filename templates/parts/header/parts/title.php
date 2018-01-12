<?php
/**
 * The template for displaying the site title (with its wrapper)
 */
?>
<h1 class="navbar-brand align-self-start <?php czr_fn_echo( 'element_class' ) ?>" <?php czr_fn_echo('element_attributes') ?>>
  <a class="navbar-brand-sitename <?php czr_fn_echo( 'title_class' ) ?>" href="<?php echo esc_url( home_url( '/' ) ) ?>"  title="<?php bloginfo( 'name' ) ?> | <?php echo get_bloginfo( 'description', 'display' ) ?>">
    <span><?php bloginfo( 'name' ) ?></span>
  </a>
</h1>


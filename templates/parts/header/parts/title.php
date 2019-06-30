<?php
/**
 * The template for displaying the site title (with its wrapper)
 * Modified in June 2019 to fix the multiple possible H1 problem on singular pages
 * @see https://github.com/presscustomizr/customizr/issues/1760
 */
?>
<?php if ( czr_fn_is_real_home() ) : ?>
<h1 class="navbar-brand col-auto <?php czr_fn_echo( 'element_class' ) ?>" <?php czr_fn_echo('element_attributes') ?>>
    <a class="navbar-brand-sitename <?php czr_fn_echo( 'title_class' ) ?>" href="<?php echo esc_url( home_url( '/' ) ) ?>">
    <span><?php bloginfo( 'name' ) ?></span>
  </a>
</h1>
<?php else : ?>
<span class="navbar-brand col-auto <?php czr_fn_echo( 'element_class' ) ?>" <?php czr_fn_echo('element_attributes') ?>>
    <a class="navbar-brand-sitename <?php czr_fn_echo( 'title_class' ) ?>" href="<?php echo esc_url( home_url( '/' ) ) ?>">
    <span><?php bloginfo( 'name' ) ?></span>
  </a>
</span>
<?php endif; ?>
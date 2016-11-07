<?php
/**
 * The template for displaying the site head
 */
?>
<head <?php czr_fn_echo('element_attributes') ?>>
  <meta charset="<?php bloginfo( 'charset' ); ?>" />
  <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE" />
  <?php if ( ! function_exists( '_wp_render_title_tag' ) ) :?>
    <title><?php wp_title( '|' , true, 'right' ); ?></title>
  <?php endif; ?>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="profile" href="http://gmpg.org/xfn/11" />
  <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
  <?php wp_head(); ?>
</head>

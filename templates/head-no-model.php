<?php
/**
 * The template for displaying the site head
 */
?>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>" />
  <meta http-equiv="X-UA-Compatible" content="IE=EDGE" />
<?php if ( ! function_exists( '_wp_render_title_tag' ) ) :?>
    <title><?php wp_title( '|' , true, 'right' ); ?></title>
<?php endif ?>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="profile"  href="https://gmpg.org/xfn/11" />
  <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
  <!-- scripts for IE8 and less  -->
  <!--[if lt IE 9]>
    <script src="<?php echo CZR_FRONT_ASSETS_URL ?>js/libs/html5.js"></script>
  <![endif]-->
<?php
  wp_head();
?>
</head>

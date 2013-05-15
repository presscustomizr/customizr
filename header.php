<?php
/**
 * The Header for Customizr.
 *
 * Displays all of the <head> section and everything up till <div id="main-wrapper">
 *
 * @package Customizr
 * @since Customizr 1.0
 */
?>
<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>                          
<!--<![endif]-->

<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <title><?php
      /*
       * Print the <title> tag based on what is being viewed.
       */
      global $page, $paged;

      wp_title( '|', true, 'right' );

      // Add the blog name.
      bloginfo( 'name' );

      // Add the blog description for the home/front page.
      $site_description = get_bloginfo( 'description', 'display' );
      if ( $site_description && ( is_home() || is_front_page() ) )
        echo " | $site_description";

      // Add a page number if necessary:
      if ( $paged >= 2 || $page >= 2 )
        echo ' | ' . sprintf( __( 'Page %s', 'customizr' ), max( $paged, $page ) );

      ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="http://gmpg.org/xfn/11" />
    <?php
      /* We add some JavaScript to pages with the comment form
       * to support sites with threaded comments (when in use).
       */
      if ( is_singular() && get_option( 'thread_comments' ) )
        wp_enqueue_script( 'comment-reply' );

      /* Always have wp_head() just before the closing </head>
       * tag of your theme, or you will break many plugins, which
       * generally use this hook to add elements to <head> such
       * as styles, scripts, and meta tags.
       */
      wp_head();
     global $tc_theme_options ;
     
    ?>
  <!-- Favicon -->
    <?php tc_get_favicon(); ?>
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
   
   <!-- Icons font support for IE6-7 -->
    <!--[if lt IE 8]>
      <script src="<?php echo TC_BASE_URL ?>inc/css/fonts/lte-ie7.js"></script>
    <![endif]-->

    <!-- Fav and touch icons -->
        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo TC_BASE_URL ?>inc/ico/apple-touch-icon-144-precomposed.png">
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo TC_BASE_URL ?>inc/ico/apple-touch-icon-114-precomposed.png">
          <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo TC_BASE_URL ?>inc/ico/apple-touch-icon-72-precomposed.png">
                        <link rel="apple-touch-icon-precomposed" href="<?php echo TC_BASE_URL ?>inc/ico/apple-touch-icon-57-precomposed.png">
</head>

<body <?php body_class(); ?> itemscope itemtype="http://schema.org/WebPage">
	<header class="tc-header clearfix" role="banner">
    	<?php
           $logo_src    = esc_url( $tc_theme_options['tc_logo_upload'] ) ;
           $logo_resize = $tc_theme_options['tc_logo_resize']
        ?>
        <div class="navbar-wrapper clearfix row-fluid">
          <!-- Wrap the .navbar in .container to center it within the absolutely positioned parent. -->
            <?php if($logo_src != null) :?>
              <div class="brand span3">
                <?php //logo styling option
                  $logo_img_style = '';
                  if($logo_resize == 1)
                    $logo_img_style = 'style="max-width:250px;max-height:100px"';
                ?>
                <h1><a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?> | <?php bloginfo( 'description' ); ?>"><img src="<?php echo $logo_src ?>" alt="<?php _e('Back Home', 'customizr'); ?>" <?php echo $logo_img_style ?>/></a>
                </h1>
              </div>
            <?php else : ?>
              <div class="brand span3 pull-left">
                 <h1><a class="site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?> | <?php bloginfo( 'description' ); ?>"><?php bloginfo( 'name' ); ?></a>
                  </h1>
              </div>
            <?php endif; ?>
            <div class="container outside">
              <h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
            </div>
           <div class="navbar notresp span9 pull-left">
              <div class="navbar-inner" role="navigation">
                <div class="row-fluid">
                  <div class="social-block span5"><?php echo tc_get_social('tc_social_in_header') ?></div>
                  <h2 class="span7 inside site-description"><?php bloginfo( 'description' ); ?></h2>
                </div>
                <div class="nav-collapse collapse">
                  <?php wp_nav_menu( array( 'theme_location' => 'main', 'menu_class' => 'nav', 'fallback_cb' => 'tc_link_to_menu_editor', 'walker' => new TC_Nav_Walker()) );  ?>
                </div><!-- /.nav-collapse collapse -->
            </div><!-- /.navbar-inner -->
          </div><!-- /.navbar notresp -->

          <div class="navbar resp">
              <div class="navbar-inner" role="navigation">
                  <div class="social-block"><?php echo tc_get_social('tc_social_in_header') ?></div>
                      <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                      </button>
                <div class="nav-collapse collapse">
                  <?php wp_nav_menu( array( 'theme_location' => 'main', 'menu_class' => 'nav', 'fallback_cb' => 'tc_link_to_menu_editor','walker' => new TC_Nav_Walker()) );  ?>
                </div><!-- /.nav-collapse collapse -->
            </div><!-- /.navbar-inner -->
          </div><!-- /.navbar resp -->

        </div><!-- /.navbar-wrapper -->
	</header>
<?php  tc_get_slider(); ?>
<div id="main-wrapper" class="container">


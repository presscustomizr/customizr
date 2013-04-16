<?php
/**
 * The template part for displaying additional header for posts list.
 *
 * @package Customizr
 * @since Customizr 1.0
 */
?>
<?php if (is_404()) : ?>
  <header class="entry-header">
    <h1 class="entry-title"><?php _e( 'Ooops, page not found', 'customizr' ); ?></h1>
  </header>

<?php elseif(is_search()) : ?>

  <header class="search-header">
    <h1 class="page-title">
      <?php 
        printf( __( '%1sSearch Results for: %2s', 'customizr' ), 
        have_posts() ? '' : 'No ',
        '<span>' . get_search_query() . '</span>' );
      ?>
    </h1>
  </header>

<?php elseif(is_author()) : ?>
<?php
/* Get the user ID. */
$user_id = get_query_var( 'author' );
?>
  <header class="archive-header">
    <h1 class="archive-title"><?php printf( __( 'Author Archives: %s', 'customizr' ), '<span class="vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( $user_id ) ) . '" title="' . esc_attr( get_the_author_meta( 'display_name', $user_id ) ) . '" rel="me">' . get_the_author_meta( 'display_name', $user_id ) . '</a></span>' ); ?></h1>
  </header><!-- .archive-header -->

<?php elseif(is_category()) : ?>

  <header class="archive-header">
    <h1 class="archive-title"><?php printf( __( 'Category Archives: %s', 'customizr' ), '<span>' . single_cat_title( '', false ) . '</span>' ); ?></h1>

    <?php if ( category_description() ) : // Show an optional category description ?>
      <div class="archive-meta"><?php echo category_description(); ?></div>
    <?php endif; ?>
  </header><!-- .archive-header -->

<?php elseif(is_tag()) : ?>

  <header class="archive-header">
    <h1 class="archive-title"><?php printf( __( 'Tag Archives: %s', 'customizr' ), '<span>' . single_tag_title( '', false ) . '</span>' ); ?></h1>

  <?php if ( tag_description() ) : // Show an optional tag description ?>
    <div class="archive-meta"><?php echo tag_description(); ?></div>
  <?php endif; ?>
  </header><!-- .archive-header -->

<?php elseif(is_archive()) : ?>

  <header class="archive-header">
    <h1 class="archive-title"><?php
      if ( is_day() ) :
        printf( __( 'Daily Archives: %s', 'customizr' ), '<span>' . get_the_date() . '</span>' );
      elseif ( is_month() ) :
        printf( __( 'Monthly Archives: %s', 'customizr' ), '<span>' . get_the_date( _x( 'F Y', 'monthly archives date format', 'customizr' ) ) . '</span>' );
      elseif ( is_year() ) :
        printf( __( 'Yearly Archives: %s', 'customizr' ), '<span>' . get_the_date( _x( 'Y', 'yearly archives date format', 'customizr' ) ) . '</span>' );
      else :
        _e( 'Archives', 'customizr' );
      endif;
    ?></h1>
  </header><!-- .archive-header -->
<?php endif; ?>

<?php if(!is_single() && !is_page() && (!is_home() || !is_front_page())) : ?>
  <hr class="featurette-divider">
<?php endif; ?>
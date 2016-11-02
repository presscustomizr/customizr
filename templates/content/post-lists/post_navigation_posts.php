<?php
/**
 * The template for displaying the post navigation in the lists of posts
 */

$next_arr     = is_rtl() ? 'right' : 'left';
$prev_arr     = is_rtl() ? 'left' : 'right';
$_older_label = __( 'Older posts' , 'customizr' );
$_newer_label = __( 'Newer posts' , 'customizr' );

/* Generate links */
$prev_link = get_next_posts_link(
        '<span class="meta-nav"><span class="meta-nav-title">' . $_older_label . '</span><i class="arrow icn-' . $prev_arr . '-open-big"></i></span>', //label
        0 //max pages
      );

$next_link  = get_previous_posts_link(
      '<span class="meta-nav"><i class="arrow icn-' . $next_arr . '-open-big"></i><span class="meta-nav-title">' . $_newer_label . '</span></span>', //label
        0 //max pages
      );


/* If no links are present do not display this */
if ( null != $prev_link || null != $next_link ) :

?>
<section class="col-md-12 post-navigation <?php czr_fn_echo( 'element_class' ) ?>" <?php czr_fn_echo('element_attributes') ?>>
  <nav id="nav-below" class="" role="navigation">
    <h2 class="sr-only"><?php _e('Posts navigation', 'customizr') ?></h2>
    <ul class="pager clearfix">
      <li class="next col-xs-2 col-sm-4">
      <?php if ( null != $next_link ) : ?>
        <span class="sr-only"><?php echo $_newer_label ?></span>
        <span class="nav-next"><?php echo $next_link ?></span>
      <?php endif ?>
      </li>
      <li class="pagination col-xs-8 col-sm-4">
        <ul>
        <?php
          $_paginate_links = paginate_links( array(
            'prev_next' => false,
            'type'      => 'array',
          ));
          foreach ( $_paginate_links as $_page ) {
            echo "<li>$_page</li>";
          }
        ?>
        </ul>
      </li>
      <li class="previous col-xs-2 col-sm-4">
      <?php if ( null != $prev_link ) : ?>
        <span class="sr-only"><?php echo $_older_label ?></span>
        <span class="nav-previous"><?php echo $prev_link ?></span>
      <?php endif; ?>
      </li>
  </ul>
  </nav>
</section>
<?php endif;
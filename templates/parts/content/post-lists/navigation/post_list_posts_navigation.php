<?php
/**
 * The template for displaying the post navigation in the lists of posts
 */

$next_dir          = is_rtl() ? 'right' : 'left';
$prev_dir          = is_rtl() ? 'left' : 'right';
$tnext_align_class = "text-{$next_dir}";
$tprev_align_class = "text-{$prev_dir}";
$_older_label      = __( 'Older posts' , 'customizr' );
$_newer_label      = __( 'Newer posts' , 'customizr' );

/* Generate links */
$prev_link = get_next_posts_link(
        '<span class="meta-nav"><span class="meta-nav-title">' . $_older_label . '</span><i class="arrow icn-' . $prev_dir . '-open-big"></i></span>', //label
        0 //max pages
      );

$next_link  = get_previous_posts_link(
      '<span class="meta-nav"><i class="arrow icn-' . $next_dir . '-open-big"></i><span class="meta-nav-title">' . $_newer_label . '</span></span>', //label
        0 //max pages
      );


/* If no links are present do not display this */
if ( null != $prev_link || null != $next_link ) :

?>
<div class="row post-navigation <?php czr_fn_echo( 'element_class' ) ?>" <?php czr_fn_echo('element_attributes') ?>>
  <nav id="nav-below" class="col-12">
    <h2 class="sr-only"><?php _e('Posts navigation', 'customizr') ?></h2>
    <ul class="czr_pager row flex-row flex-no-wrap">
      <li class="next col-2 col-sm-4 <?php echo $tnext_align_class ?> ">
      <?php if ( null != $next_link ) : ?>
        <span class="sr-only"><?php echo $_newer_label ?></span>
        <span class="nav-next nav-dir"><?php echo $next_link ?></span>
      <?php endif ?>
      </li>
      <li class="pagination col-8 col-sm-4">
        <ul class="pag-list">
        <?php
          $_paginate_links = paginate_links( array(
            'prev_next' => false,
            'mid_size'  => 1,
            'type'      => 'array',
          ));
          if ( is_array( $_paginate_links ) ) {
            foreach ( $_paginate_links as $_page ) {
              echo "<li class='pag-item'>$_page</li>";
            }
          }
        ?>
        </ul>
      </li>
      <li class="previous col-2 col-sm-4 <?php echo $tprev_align_class ?>">
      <?php if ( null != $prev_link ) : ?>
        <span class="sr-only"><?php echo $_older_label ?></span>
        <span class="nav-previous nav-dir"><?php echo $prev_link ?></span>
      <?php endif; ?>
      </li>
  </ul>
  </nav>
</div>
<?php endif;
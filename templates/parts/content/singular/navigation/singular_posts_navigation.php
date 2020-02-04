<?php
/**
 * The template for displaying the post navigation in singular (post/page)
 */


$prev_dir          = is_rtl() ? 'right' : 'left';
$next_dir          = is_rtl() ? 'left' : 'right';
$tprev_align_class = "text-{$prev_dir}";
$tnext_align_class = "text-{$next_dir}";

/* Generate links */
$prev_link = get_previous_post_link(
      '%link', //format
      '<span class="meta-nav"><i class="arrow icn-' . $prev_dir . '-open-big"></i><span class="meta-nav-title">%title</span></span>', //title
      false, //in_same_term
      '', //excluded_terms
      'category'//taxonomy
    );

$next_link  = get_next_post_link(
      '%link', //format
      '<span class="meta-nav"><span class="meta-nav-title">%title</span><i class="arrow icn-' . $next_dir . '-open-big"></i></span>', //title
      false, //in_same_term
      '', //excluded_terms
      'category'//taxonomy
    );

/* If no links are present do not display this */
if ( null != $prev_link || null != $next_link ) :

?>
<div class="post-navigation row <?php czr_fn_echo( 'element_class' ) ?>" <?php czr_fn_echo('element_attributes') ?>>
  <nav id="nav-below" class="col-12">
    <h2 class="sr-only"><?php _e('Post navigation', 'customizr') ?></h2>
    <ul class="czr_pager row flex-row flex-no-wrap">
      <li class="previous col-5 <?php echo $tprev_align_class ?>">
      <?php if ( null != $prev_link ) : ?>
        <span class="sr-only"><?php _e('Previous post', 'customizr') ?></span>
        <span class="nav-previous nav-dir"><?php echo $prev_link ?></span>
      <?php endif; ?>
      </li>
      <li class="nav-back col-2 text-center">
        <?php if ( is_single() ) : ?>
        <a href="<?php echo esc_url( ( 'page' != get_option( 'show_on_front' ) || ! get_option( 'page_for_posts' ) ) ? home_url( '/' ) : get_permalink( get_option( 'page_for_posts' ) ) ) ?>" title="<?php _e( 'Back to post list', 'customizr') ?>">
          <span><i class="icn-grid-empty"></i></span>
          <span class="sr-only"><?php _e( 'Back to post list', 'customizr') ?></span>
        </a>
        <?php endif ?>
      </li>
      <li class="next col-5 <?php echo $tnext_align_class ?>">
      <?php if ( null != $next_link ) : ?>
        <span class="sr-only"><?php _e('Next post', 'customizr') ?></span>
        <span class="nav-next nav-dir"><?php echo $next_link ?></span>
      <?php endif ?>
      </li>
  </ul>
  </nav>
</div>
<?php endif;
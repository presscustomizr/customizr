<?php
/**
 * The template for displaying the post navigation in singular (post/page)
 */
$prev_dir          = is_rtl() ? 'right' : 'left';
$next_dir          = is_rtl() ? 'left' : 'right';

$tprev_align_class = "text-{$prev_dir}";
$tnext_align_class = "text-{$next_dir}";
?>
<nav id="image-navigation" class="attachment-image-navigation">
  <h2 class="sr-only"><?php _e('Images navigation', 'customizr') ?></h2>
  <ul class="czr_pager row flex-row">
    <li class="previous-image col-6 <?php echo $tprev_align_class ?>">
      <?php previous_image_link(
      $size = false,
      '<span class="meta-nav"><i class="arrow icn-' . $prev_dir . '-open-big"></i><span class="meta-nav-title">' . __( 'Previous', 'customizr' ) . '</span></span>' //title
      );?>
    </li>
    <li class="next-image col-6 <?php echo $tnext_align_class ?>">
      <?php
      next_image_link(
        $size = false,
        '<span class="meta-nav"><span class="meta-nav-title">' . __( 'Next', 'customizr' ) . '</span><i class="arrow icn-' . $next_dir . '-open-big"></i></span>' //title
      );
      ?>
    </li>
  </ul>
</nav><!-- //#image-navigation -->
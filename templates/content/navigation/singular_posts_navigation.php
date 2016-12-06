<?php
/**
 * The template for displaying the post navigation in singular (post/page)
 */


$prev_arr     = is_rtl() ? 'right' : 'left';
$next_arr     = is_rtl() ? 'left' : 'right';

/* Generate links */
$prev_link = get_previous_post_link(
      '%link', //format
      '<span class="meta-nav"><i class="arrow icn-' . $prev_arr . '-open-big"></i><span class="meta-nav-title">%title</span></span>', //title
      false, //in_same_term
      '', //excluded_terms
      'category'//taxonomy
    );

$next_link  = get_next_post_link(
      '%link', //format
      '<span class="meta-nav"><span class="meta-nav-title">%title</span><i class="arrow icn-' . $next_arr . '-open-big"></i></span>', //title
      false, //in_same_term
      '', //excluded_terms
      'category'//taxonomy
    );

/* If no links are present do not display this */
if ( null != $prev_link || null != $next_link ) :

?>
<section class="col-md-12 post-navigation <?php czr_fn_echo( 'element_class' ) ?>" <?php czr_fn_echo('element_attributes') ?>>
  <nav id="nav-below" class="" role="navigation">
    <h2 class="sr-only"><?php _e('Post navigation', 'customizr') ?></h2>
    <ul class="pager clearfix">
      <li class="previous col-xs-5">
      <?php if ( null != $prev_link ) : ?>
        <span class="sr-only"><?php _e('Previous post', 'customizr') ?></span>
        <span class="nav-previous"><?php echo $prev_link ?></span>
      <?php endif; ?>
      </li>
      <li class="nav-back col-xs-2">
        <?php if ( is_single() ) :  ?>
        <a href="<?php echo esc_url( ! get_option( 'page_for_posts' ) ? home_url( '/' ) : get_permalink( get_option( 'page_for_posts' ) ) ) ?>" title="<?php _e( 'Back to post list', 'customizr') ?>">
          <span><i class="icn-grid-empty"></i></span>
          <span class="sr-only"><?php _e( 'Back to post list', 'customizr') ?></span>
        </a>
        <?php endif ?>
      </li>
      <li class="next col-xs-5">
      <?php if ( null != $next_link ) : ?>
        <span class="sr-only"><?php _e('Next post', 'customizr') ?></span>
        <span class="nav-next"><?php echo $next_link ?></span>
      <?php endif ?>
      </li>
  </ul>
  </nav>
</section>
<?php endif;
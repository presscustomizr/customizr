<?php
/**
 * The template for displaying the post navigation in singular (post/page)
 *
 * @package WordPress
 * @subpackage Customizr
 * @since Customizr 3.5.0
 */

$l_arr        = __( '&larr;', 'customizr' );
$r_arr        = __( '&rarr;', 'customizr' );
$prev_arr     = is_rtl() ? $r_arr : $l_arr;
$next_arr     = is_rtl() ? $l_arr : $r_arr;
?>
<nav id="nav-below" class="<?php tc_echo( 'element_class' ) ?>" <?php tc_echo('element_attributes') ?>>
  <hr class="featurette-divider">
  <h3 class="assistive-text">
    <?php  _e( 'Post navigation' , 'customizr' ) ?>
  </h3>
  <ul class="pager">
    <?php if ( null != $prev_link = get_previous_post_link(
        '%link', //format
        '<span class="meta-nav">' . $prev_arr . '</span> %title', //title
        false, //in_same_term
        '', //excluded_terms
        'category'//taxonomy
        ) ) : ?>
    <li class="previous">
      <span class="nav-previous">
        <?php echo $prev_link ?>
      </span>
    </li>
  <?php endif; ?>
  <?php if ( null != $next_link = get_next_post_link(
        '%link', //format
        '%title <span class="meta-nav">' . $next_arr . '</span>', //title
        false, //in_same_term
        '', //excluded_terms
        'category'//taxonomy
        ) ) : ?>
  <li class="next">
    <span class="nav-next">
    <?php echo $next_link ?>
    </span>
  </li>
  <?php endif ?>
</ul>
</nav>

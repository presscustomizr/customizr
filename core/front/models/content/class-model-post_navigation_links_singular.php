<?php
class TC_post_navigation_links_singular_model_class extends TC_post_navigation_links_model_class {

  /* override */
  function tc_get_the_previous_link( $model ) {
    $singular_nav_previous_text   = apply_filters( 'tc_singular_nav_previous_text', call_user_func( '_x',  $model[ 'prev_arrow' ] , 'Previous post link' , 'customizr' ) );
    $previous_post_link_args      = apply_filters(
      'tc_previous_single_post_link_args' ,
      array(
        'format'        => '%link',
        'link'          => '<span class="meta-nav">' . $singular_nav_previous_text . '</span> %title',
        'in_same_term'  => false,
        'excluded_terms' => '',
        'taxonomy'      => 'category'
      )
    );
    extract( $previous_post_link_args , EXTR_OVERWRITE );
    return get_previous_post_link( $format , $link , $in_same_term, $excluded_terms, $taxonomy );
  }

  /* override */
  function tc_get_the_next_link( $model ) {
    $singular_nav_next_text       = apply_filters( 'tc_singular_nav_next_text', call_user_func( '_x', $model[ 'next_arrow' ] , 'Next post link' , 'customizr' ) );
    $next_post_link_args      = apply_filters(
      'tc_next_single_post_link_args' ,
      array(
        'format'        => '%link',
        'link'          => '%title <span class="meta-nav">' . $singular_nav_next_text . '</span>',
        'in_same_term'  => false,
        'excluded_terms' => '',
        'taxonomy'      => 'category'
      )
    );
    extract( $next_post_link_args , EXTR_OVERWRITE );
    return get_next_post_link( $format , $link , $in_same_term, $excluded_terms, $taxonomy );
  }
}

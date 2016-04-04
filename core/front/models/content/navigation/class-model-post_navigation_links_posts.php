<?php
class TC_post_navigation_links_posts_model_class extends TC_post_navigation_links_model_class {

  /* override */
  function tc_get_the_previous_link( $model ) {
    $next_posts_link_args      = apply_filters(
      'tc_next_posts_link_args' ,
      array(
        'label'        => apply_filters( 'tc_list_nav_next_text', __( '<span class="meta-nav">&larr;</span> Older posts' , 'customizr' ) ),
        'max_pages'    => 0
      )
    );
    extract( $next_posts_link_args , EXTR_OVERWRITE );
    return get_next_posts_link( $label , $max_pages );
  }

  /* override */
  function tc_get_the_next_link( $model ) {
    $previous_posts_link_args      = apply_filters(
      'tc_previous_posts_link_args' ,
      array(
        'label'        => apply_filters( 'tc_list_nav_previous_text', __( 'Newer posts <span class="meta-nav"> &rarr;</span>' , 'customizr' ) ),
        'max_pages'    => 0
      )
    );
    extract( $previous_posts_link_args , EXTR_OVERWRITE );
    return get_previous_posts_link( $label , $max_pages );
  }
}

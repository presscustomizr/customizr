<?php
class CZR_cl_post_navigation_posts_model_class extends CZR_cl_post_navigation_model_class {
  /*
  * @override
  */
  function czr_fn_get_context() {
    if ( is_home() && 'posts' == get_option('show_on_front') )
      return 'home';
    if ( !is_404() && !CZR_cl_utils::$inst -> czr_fn_is_home_empty() )
      return 'archive';

    return false;
  }
}

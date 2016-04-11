<?php
class TC_post_navigation_posts_model_class extends TC_post_navigation_model_class {
  /*
  * @override
  */
  function tc_get_context() {
    if ( is_home() && 'posts' == get_option('show_on_front') )
      return 'home';
    if ( !is_404() && !tc__f( '__is_home_empty') )
      return 'archive';

    return false;
  }
}

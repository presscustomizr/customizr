<?php
class TC_post_navigation_singular_model_class extends TC_post_navigation_model_class {
  /* @override */
  function tc_get_context() {  
    if ( is_page() )
      return 'page';
    if ( is_single() && ! is_attachment() )
      return 'single'; // exclude attachments
    return false;
  }
}

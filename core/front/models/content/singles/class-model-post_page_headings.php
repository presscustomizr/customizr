<?php
class TC_post_page_headings_model_class extends TC_Model {
  function tc_setup_children() {
    $children = array (
      array(
        'id' => 'post_page_title',
        'model_class' => 'content/singles/post_page_title'
      ),
      //comment bubble
      array(
        'hook'      => '__after_inner_post_page_title__',
        'template'  => 'modules/comment_bubble'
      ),
      //edit post links
      array(
        'hook'      => '__after_inner_post_page_title__',
        'template'  => 'modules/edit_button',
        'priority'  => 20
      ),
      //recently updated
      array(
        'hook'      => '__after_inner_post_page_title__',
        'template'  => 'modules/recently_updated',
        'priority'  => 30
      )
    );
    return $children;

   }
}

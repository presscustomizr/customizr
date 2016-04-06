<?php
class TC_post_page_headings_model_class extends TC_headings_model_class {
  public $type = 'content';

  function tc_setup_children() {
    $children = array (

      //the text meta one uses a different template
      array(
        'id' => 'post_metas_text',
        'model_class' => array( 'parent' => 'content/post-metas/post_metas', 'name' => 'content/post-metas/post_metas_text' ),

      ),
      //attachment post mestas
      array(
        'id' => 'post_metas_attachment',
        'model_class' => array( 'parent' => 'content/posts-metas/post_metas', 'name' => 'content/posts-metas/attachment_post_metas' )
      )
    );
    return $children;

   }

  /**
  * @override
  */
  function tc_get_class( $model = array() ) {
    return 'entry-header';
  }
}

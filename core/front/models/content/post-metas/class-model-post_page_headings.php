<?php
class TC_post_page_headings_model_class extends TC_headings_model_class {
  public $type = 'content';

  function tc_setup_children() {
    $children = array (
      /*
      * post and page titles
      * comment_buble, edit_button, recently_update registrations inside
      */
      array(
        'hook'        => '__headings_content__',
        'template'    => 'content/post_page_title'
      ),
      //Post metas ( in the headings )
      //the default class/template is for the buttons type
      array(
        'hook' => '__headings_content__',
        'template' => 'content/post_metas',
        'id' => 'post_metas_button',
        'priority' => 20
      ),
      //the text meta one uses a different template
      array(
        'hook' => '__headings_content__',
        'template' => 'content/post_metas',
        'id' => 'post_metas_text',
        'model_class' => array( 'parent' => 'content/post_metas', 'name' => 'content/post_metas_text' ),
        'priority' => 20,
      ),
      //attachment post mestas
      array(
        'hook' => '__headings_content__',
        'id' => 'post_metas_attachment',
        'template' => 'content/attachment_post_metas',
        'priority' => 20,
        'model_class' => array( 'parent' => 'content/post_metas', 'name' => 'content/attachment_post_metas' )
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

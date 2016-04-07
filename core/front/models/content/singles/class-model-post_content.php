<?php
class TC_post_content_model_class extends TC_Model {

  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function tc_extend_params( $model = array() ) {
    $icon_class             = in_array( get_post_format(), array(  'quote' , 'aside' , 'status' , 'link' ) ) ? apply_filters( 'tc_post_format_icon', 'format-icon' ) :'' ;

    $model['element_class'] = array( $icon_class );

    return $model;
  }

  function tc_setup_children() {
    $children = array(
      //single post thumbnail
      array(
        'hook'        => '__before_post',
        'template'    => 'content/thumbnail_single',
        'id'          => 'post_thumbnail',
        'model_class' => array( 'parent' => 'content/thumbnail', 'name' => 'content/singles/thumbnail_single')
      )
    );
    return $children;
  }
}

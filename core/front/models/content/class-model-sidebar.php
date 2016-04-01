<?php
class TC_sidebar_model_class extends TC_widget_area_wrapper_model_class {
  public $position;
  private static $sidebar_map = array(
      //id => allowed layout (- b both )
      'right'  => 'r',
      'left'   => 'l'
  );

  function tc_setup_children() {
    $children = array(
      //left/right sidebar content
      array(
        'hook'        => "__widget_area_{$this->position}__",
        'template'    => 'modules/social_block',
        'model_class' => array( 'parent' => 'modules/social_block', 'name' => 'content/sidebar_social_block' )
      ),
      //socialblock in left/right sidebar
      array(
        'hook'        => "__widget_area_{$this->position}__",
        'id'          => $this->position,
        'template'    => 'modules/widget_area'
      )
    );
    return $children;
  }

  /*
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function tc_extend_params( $model = array() ) {
    $model                = parent::tc_extend_params( $model );
    $screen_layout        = TC_utils::tc_get_layout( TC_utils::tc_id() , 'sidebar'  );

    //extract the position
    $this -> position     = substr( $model['id'], 0 ,strpos( $model['id'], '_sidebar' ) );

    if ( ! in_array( $this -> position, array('right', 'left' ) ) )
      return array();

    $global_layout        = apply_filters( 'tc_global_layout' , TC_init::$instance -> global_layout );
    $sidebar_layout       = $global_layout[$screen_layout];

    //defines the sidebar wrapper class
    $model['element_class']           = apply_filters( "tc_{$this -> position }_sidebar_class", array( $sidebar_layout['sidebar'], $this -> position, 'tc-sidebar') );

    $model['inner_class']             = array('widget-area');
    $model['action_hook_suffix']      = '_'. $this -> position;
    $model['inner_id']                = $this -> position;

    return $model;
  }
}

<?php
class CZR_cl_sidebar_model_class extends CZR_cl_Model {
  public $position;
  private static $sidebar_map = array(
      //id => allowed layout (- b both )
      'right'  => 'r',
      'left'   => 'l'
  );

  function tc_setup_children() {

    $children = array(
      //left/right sidebar social block
      array(
        'id'          => "{$this -> position}_sidebar_social_block",
        'model_class' => array( 'parent' => 'modules/social_block', 'name' => 'content/sidebars/sidebar_social_block' ),
        'controller'  => 'social_block'
      ),
      //helpblock in left/right sidebar
      array(
        'hook'        => "__before_inner_{$this->position}_sidebar",
        'id'          => "{$this->position}_sidebar_help_block",
        'template'    => 'modules/help_block',
        'model_class' => array( 'parent' => 'modules/help_block', 'name' => "modules/{$this->position}_sidebar_help_block" )
      ),
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
    $screen_layout        = CZR_cl_utils::czr_get_layout( CZR_cl_utils::tc_id() , 'sidebar'  );

    //extract the position
    $this -> position     = substr( $model['id'], 0 ,strpos( $model['id'], '_sidebar' ) );

    if ( ! in_array( $this -> position, array('right', 'left' ) ) )
      return array();

    $global_layout        = apply_filters( 'tc_global_layout' , CZR_cl_init::$instance -> global_layout );
    $sidebar_layout       = $global_layout[$screen_layout];

    //defines the sidebar wrapper class
    $model['element_class']           = apply_filters( "tc_{$this -> position }_sidebar_class", array( $sidebar_layout['sidebar'], $this -> position, 'tc-sidebar') );

    $model['inner_class']             = array('widget-area');
    $model['action_hook_suffix']      = '_'. $this -> position;
    $model['inner_id']                = $this -> position;

    return $model;
  }
}

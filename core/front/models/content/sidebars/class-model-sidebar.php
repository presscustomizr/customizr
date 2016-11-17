<?php
class CZR_sidebar_model_class extends CZR_Model {
  private static $sidebar_map = array(
      //id => allowed layout (- b both )
      'right'  => 'r',
      'left'   => 'l'
  );

  function czr_fn_setup_children() {

    $children = array( /*
      //helpblock in left/right sidebar
      array(
        'hook'        => "__before_inner_{$this->position}_sidebar",
        'id'          => "{$this->position}_sidebar_help_block",
        'template'    => 'modules/help_block',
        'model_class' => array( 'parent' => 'modules/help_block', 'name' => "modules/{$this->position}_sidebar_help_block" )
      ),*/
    );
    return $children;
  }

  /*
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function czr_fn_extend_params( $model = array() ) {
    $screen_layout        = czr_fn_get_layout( czr_fn_get_id() , 'sidebar'  );

    //extract the position
    $position             = substr( $model['id'], 0 ,strpos( $model['id'], '_sidebar' ) );

    if ( ! in_array( $position, array('right', 'left' ) ) )
      return array();

    $global_layout        = czr_fn_get_global_layout();
    $sidebar_layout       = $global_layout[$screen_layout];
    $sidebar_prefix       = self::$sidebar_map[$position];

    //defines the sidebar wrapper class
    $model['element_class']  = $sidebar_layout[ "{$sidebar_prefix}-sidebar" ];

    return parent::czr_fn_extend_params( $model );
  }
}
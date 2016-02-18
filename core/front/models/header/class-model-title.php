<?php
class TC_title_model_class extends TC_Model {
  public $content;
  static $instance;

  function __construct( $model = array() ) {
    self::$instance =& $this;

    //grab the model's id
    //=> at this stage the properties have not yet been overriden
    $_id = $model['id'];
    
    $this -> tc_set_default_properties();
    //do things before firing the parent model's constructor
    //add_filter("_da_hook_{$_id}", array($this, 'tc_change_hook') );
    //set this model's properties

    //Fires the parent constructor
    parent::__construct( $model );

  }

  function tc_set_default_properties() {
    $this -> tc_set_property( 'content', __( esc_attr( get_bloginfo( 'name' ) ) ));
  }
}

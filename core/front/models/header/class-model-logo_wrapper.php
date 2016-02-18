<?php
class TC_logo_wrapper_model_class extends TC_Model {
  public $logo_wrapper_class;
  public $link_class;
  public $link_title;
  public $link_url;
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

  function tc_set_default_properties(){
    $this -> tc_set_property( 'logo_wrapper_class', 'brand span3 pull-left' );
    $this -> tc_set_property( 'link_class', 'site-title' );
    $this -> tc_set_property( 'link_title', sprintf( '%1$s | %2$s' , __( esc_attr( get_bloginfo( 'name' ) ) ) , __( esc_attr( get_bloginfo( 'description' ) ) ) ) );
    $this -> tc_set_property( 'link_url', esc_url( home_url( '/' ) ) );
  }
}

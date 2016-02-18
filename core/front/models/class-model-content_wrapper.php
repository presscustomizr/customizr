<?php
class TC_content_wrapper_model_class extends TC_Model {
  public $content_layout;
  static $instance;

  function __construct( $model = array() ) {
    self::$instance =& $this;

    //grab the model's id
    //=> at this stage the properties have not yet been overriden
    $_id = $model['id'];

    //do things before firing the parent model's constructor
    //add_filter("_da_hook_{$_id}", array($this, 'tc_change_hook') );
    //set this model's properties
    $this -> tc_set_property('content_layout', 'span12');

    //Fires the parent constructor
    parent::__construct( $model );

  }

  function tc_change_hook() {
    return '__footer__';
  }

  /*public function tc_render() {
    ?>
      <h1>MY ID IS <span style="color:blue"><?php echo $this -> id ?></span>, AND I AM RENDERED BY THE VIEW CLASS</h1>
    <?php
  }*/
}
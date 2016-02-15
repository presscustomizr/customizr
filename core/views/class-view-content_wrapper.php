<?php
class TC_content_wrapper_view_class extends TC_View {
  public $content_layout;
  static $instance;

  function __construct( $model = array() ) {
    self::$instance =& $this;
    //Fires the parent constructor
    if ( ! isset(parent::$instance) )
      parent::__construct( $model );

    //set this model's properties
    $this -> tc_add_view_properties();
  }


  private function tc_add_view_properties() {
    $this -> content_layout = 'span12';//@to do make this dynamic
  }


  /*public function tc_render() {
    ?>
      <h1>MY ID IS <span style="color:blue"><?php echo $this -> id ?></span>, AND I AM RENDERED BY THE VIEW CLASS</h1>
    <?php
  }*/
}
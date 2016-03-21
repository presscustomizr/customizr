<?php
class TC_grid_title_model_class extends TC_model {
  public  $the_grid_title;

  function __construct( $model = array() ) {
    parent::__construct( $model );
    //inside the loop but before rendering set some properties
    //we need the -1 (or some < 0 number) as priority, as the thumb in single post page can be rendered at a certain hook with priority 0 (option based)
    add_action( $model['hook']          , array( $this, 'tc_set_this_properties' ), -1 );


  } 

  function tc_set_this_properties() {
    $this -> tc_set_property( 'the_grid_title',
        $this -> tc_grid_set_title_length( get_the_title() ) );    
  }

  /**
  * hook : tc_title_text
  * Limits the length of the post titles in grids to a custom number of characters
  * @return string
  */
  function tc_grid_set_title_length( $_title ) {
    $_max = esc_attr( TC_utils::$inst->tc_opt( 'tc_grid_num_words') );
    $_max = ( empty($_max) || ! $_max ) ? 10 : $_max;
    $_max = $_max <= 0 ? 1 : $_max;

    if ( empty($_title) || ! is_string($_title) )
      return $_title;

    if ( count( explode( ' ', $_title ) ) > $_max ) {
      $_words = array_slice( explode( ' ', $_title ), 0, $_max );
      $_title = sprintf( '%s ...',
        implode( ' ', $_words )
      );
    }
    return $_title;
  }

}

<?php
class TC_slide_model_class extends TC_Model {
  public $item_class;
  public $img_wrapper_class;
  public $slides = array();
  public $name_id;
  public $img;

  function __construct( $model ) {
    parent::__construct( $model );

    //inside the slider loop but before rendering set some properties    
    add_action( $this -> hook          , array( $this, 'tc_set_this_properties' ), -1 );    
  }


  function tc_set_this_properties() {
    //get the current slide;
    $slide   = get_query_var( 'tc_slide', null );
    if ( empty( $slide ) ) {
      $this -> tc_set_property( 'visibility', false );
      return;      
    }
    //array( $id, $data , $slider_name_id, $img_size )    
    extract( $this -> tc_prepare_slide_view( $slide ) );
    
    $item_class = sprintf('%1$s %2$s',
      $data['active'],
      'slide-'.$id
    );

    $img               = $data['slide_background'];
    $img_wrapper_class = apply_filters( 'tc_slide_content_class', sprintf('carousel-image %1$s' , $img_size ) );

    $this -> tc_update( compact('item_class', 'img', 'img_wrapper_class') );
  }

  function tc_prepare_slide_view( $slide ) {
    static $i = 0;
    $i = $i + 1;

    return array_merge( $slide, 
      array(  'id' => $i )
    );    
  
  }

}//end class

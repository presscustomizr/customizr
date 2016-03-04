<?php
class TC_main_wrapper_model_class extends TC_Model {

  /**
  * parse this model properties for rendering
  */ 
  function pre_rendering_my_view_cb( $model ) {
    if ( is_array( $model -> element_class ) )
      $model -> element_class = join( ' ', array_unique( $model -> element_class ) );
  }
}

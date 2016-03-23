<?php
abstract class TC_post_navigation_links_model_class extends TC_model {
  public $prev_arrow;
  public $next_arrow;
  public $prev_link;
  public $next_link;

  /**
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
   $model[ 'prev_arrow' ]       = is_rtl() ? '&rarr;' : '&larr;'; 
   $model[ 'next_arrow' ]       = is_rtl() ? '&larr;' : '&rarr;';
   $model[ 'prev_link' ]        = $this -> tc_get_the_previous_link( $model );
   $model[ 'next_link' ]        = $this -> tc_get_the_next_link( $model );
 
   return $model;
  }

  abstract function tc_get_the_previous_link( $model );
  abstract function tc_get_the_next_link( $model );

}

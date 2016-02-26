<?php
class TC_404_model_class extends TC_Model {
  public $class;
  public $inner_class;
  public $quote;
  public $author;
  public $text;
  public $featurette_class;

  /*
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) { 
    $quotes                               = $this -> tc_get_quotes();
    $format_icon                          = apply_filters( 'tc_404_content_icon', 'format-icon');
    $model[ 'quote' ]                     = $quotes[ 'quote' ];
    $model[ 'text' ]                      = $quotes[ 'text' ];
    $model[ 'author' ]                    = $quotes[ 'author' ];
    $model[ 'class' ]                     = apply_filters( 'tc_404_wrapper_class', array('tc-content', 'span12', 'format-quote' ) );
    $model[ 'inner_class' ]               = array( 'entry-content', $format_icon );
    $model[ 'featurette_class']           = array( 'featurette-divider', $model[ 'hook' ] );

    return $model;
  }

  protected function tc_get_quotes() {
    //Default 404 content
    return array(
      'quote'             => __( 'Speaking the Truth in times of universal deceit is a revolutionary act.' , 'customizr' ),
      'author'            => __( 'George Orwell' , 'customizr' ),
      'text'              => __( 'Sorry, but the requested page is not found. You might try a search below.' , 'customizr' )
    );        
  }

  /**
  * parse this model properties for rendering
  */ 
  function pre_rendering_my_view_cb( $model ) {
    if ( is_array( $model -> class ) )
      $model -> class = join( ' ', array_unique( $model -> class ) );
    if ( is_array( $model -> inner_class ) )
      $model -> inner_class = join( ' ', array_unique( $model -> inner_class ) );
    if ( is_array( $model -> featurette_class ) )
      $model -> featurette_class = join( ' ', array_unique( $model -> featurette_class ) );

  }
}

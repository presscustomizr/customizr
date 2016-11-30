<?php
class CZR_logo_model_class extends CZR_Model {
  public $src = '';
  public $logo_type = '';

  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function czr_fn_extend_params( $model = array() ) {
    $atts = czr_fn_get_logo_atts( $this -> logo_type );
    if ( empty( $atts ) )
      return $model;

    $model[ 'src' ]           = $atts[ 'logo_src' ];
    $model[ 'element_class' ] = array( $this -> logo_type );

    $logo_resize              = ( 'sticky' != $this->logo_type ) ? esc_attr( czr_fn_get_opt( 'tc_logo_resize') ) : '';
    //build other attrs
    $model[ 'element_attributes' ] = trim( sprintf('%1$s %2$s %3$s %4$s',
        $atts[ 'logo_width' ] ? sprintf( 'width="%1$s"', $atts[ 'logo_width' ] ) : '',
        $atts[ 'logo_height' ] ? sprintf( 'height="%1$s"', $atts[ 'logo_height' ] ) : '',
        ( 1 == $logo_resize ) ? sprintf( 'style="max-width:%1$spx;max-height:%2$spx"',
                                apply_filters( 'czr_logo_max_width', 250 ),
                                apply_filters( 'czr_logo_max_height', 100 )
                                ) : '',
        implode(' ' , apply_filters('czr_logo_other_attributes' , ( 0 == czr_fn_get_opt( 'tc_retina_support' ) ) ? array('data-no-retina') : array() ) )
    ));

    return $model;
  }

  /*
  * Custom CSS
  */
  function czr_fn_user_options_style_cb( $_css ) {
    //logos shrink
    //fire once
    static $_fired = false;
    if ( $_fired ) return $_css;
    $_fired        = true;


    //when to print the shrink logo CSS?
    //1) In the customizer as the sticky_header is passed as postMessage
    //or
    //2) The sticky header is enabled
    //and
    //2.1) the shrink title_logo option is enabled
    if ( czr_fn_is_customizing() ||
        ( 0 != esc_attr( czr_fn_get_opt( 'tc_sticky_header') ) && 0 != esc_attr( czr_fn_get_opt( 'tc_sticky_shrink_title_logo') ) ) ) {
      $_logo_shrink  = implode (';' , apply_filters('czr_logo_shrink_css' , array("height:30px!important","width:auto!important") ) );
      $_css = sprintf("%s%s",
          $_css,
          "
      .sticky-enabled .tc-shrink-on .navbar-logo img {
        {$_logo_shrink}
      }"
      );
    }
    return $_css;
    //end logos shrink (fire once)
  }
}//end class
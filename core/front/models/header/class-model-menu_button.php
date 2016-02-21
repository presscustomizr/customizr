<?php
class TC_menu_button_model_class extends TC_Model {
  public $wrapper_class;
  public $button_label;
  public $button_title;
  public $button_attr;
  
  /*
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $defaults      = array(  
      'type'          => 'sidenav',
      'where'         => 'right' != esc_attr( TC_utils::$inst->tc_opt( 'tc_header_layout') ) ? 'pull-right' : 'pull-left',
      'in'            => ''
    );
    $attr = isset( $model['params'] ) ? wp_parse_args( $model['params'], $defaults ) : $defaults;

    //specific args treatment
    //wrapper class
    if ( ! isset( $attr[ 'wrapper_class' ] ) ) {
      $attr[ 'wrapper_class' ]  = array( 'btn-toggle-nav', $attr[ 'where' ] );
      $attr[ 'wrapper_class' ]  = 'sidenav' == $attr['type'] ? array_merge( $attr[ 'wrapper_class' ], array( 'sn-toggle') ) : $attr[ 'wrapper_class' ];
    }

    $attr[ 'wrapper_class' ]    = join( ' ', $attr[ 'wrapper_class' ] );

    //button label
    $attr[ 'button_label']     = isset( $attr[ 'button_label' ] ) ? $attr[ 'button_label' ] : 
        sprintf( '<span class="menu-label">%s</span>',
            'sidenav' == $attr[ 'in' ] ? __('Close', 'customizr') : __('Menu' , 'customizr')
        );
    $attr[ 'button_label' ]    =  (bool)esc_attr( TC_utils::$inst->tc_opt('tc_display_menu_label') ) ? $attr[ 'button_label' ] : '';

    //button title
    if ( ! isset( $attr[ 'button_title' ] ) )
      $model[ 'button_title' ] =  '__sidenav__' == $attr[ 'in' ] ? __('Close', 'customizr') : __('Open the menu' , 'customizr');

    //button attr
    if ( ! isset( $attr[ 'button_attr' ] ) )
      $model[ 'button_attr' ]  =  'regular' == $attr['type'] ? 'data-toggle="collapse" data-target=".nav-collapse"' : '';

    unset( $model['params'] );

    return array_merge( $model, $attr );
  }
}

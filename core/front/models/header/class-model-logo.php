<?php
class TC_logo_model_class extends TC_Model {
  public $src = '';
  public $logo_type = '';
  public $alt = '';
  public $attr = '';
  public $class = '';

  function __construct( $model = array() ) {
    parent::__construct( $model );
    //specific inline CSS
    add_filter( 'tc_user_options_style', array( $this, 'tc_logo_css' ) );
  }
  /**
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $params = isset($model['params']) ? $model['params'] : array(); 
    $this -> logo_type = ! $this -> logo_type && isset( $params['type'] ) && 'sticky' == $params['type'] ? $params['type'] : $this -> logo_type;

    echo $this -> logo_type;
    echo "a";
    extract( $this -> tc_get_logo_src_args() );  

    $model[ 'src' ]   = $logo_src;
    $model[ 'alt' ]   = apply_filters( 'tc_logo_alt', __( 'Back Home', 'customizr' ) ) ;
    $model[ 'class' ] = $logo_type ;

    //build other attrs
    $model[ 'attr' ] = trim( sprintf('%1$s %2$s %3$s %4$s',
        $logo_width ? sprintf( 'width="%1$s"', $logo_width ) : '',
        $logo_height ? sprintf( 'height="%1$s"', $logo_height ) : '',
        ( 1 == $logo_resize) ? sprintf( 'style="max-width:%1$spx;max-height:%2$spx"',
                                apply_filters( 'tc_logo_max_width', 250 ),
                                apply_filters( 'tc_logo_max_height', 100 )
                                ) : '',
        implode(' ' , apply_filters('tc_logo_other_attributes' , ( 0 == TC_utils::$inst->tc_opt( 'tc_retina_support' ) ) ? array('data-no-retina') : array() ) )
    ));

    return $model;
  }

  function tc_get_logo_src_args() {
    $logo_type_sep          = $this-> logo_type ? '_sticky_' : '_';
    $accepted_formats		= apply_filters( 'tc_logo_img_formats' , array('jpg', 'jpeg', 'png' ,'gif', 'svg', 'svgz' ) );
    $args                   = array();     
    //check if the logo is a path or is numeric
    //get src for both cases
    $_logo_src 			    = '';
    $_width 				= false;
    $_height 				= false;
    $_attachement_id 		= false;
    $_logo_option  			= esc_attr( TC_utils::$inst->tc_opt( "tc{$logo_type_sep}logo_upload") );
    //check if option is an attachement id or a path (for backward compatibility)
    if ( is_numeric($_logo_option) ) {
      $_attachement_id 	    = $_logo_option;
      $_attachment_data 	= apply_filters( "tc{$logo_type_sep}logo_attachment_img" , wp_get_attachment_image_src( $_logo_option , 'full' ) );
      $_logo_src 			= $_attachment_data[0];
      $_width 			    = ( isset($_attachment_data[1]) && $_attachment_data[1] > 1 ) ? $_attachment_data[1] : $_width;
      $_height 			    = ( isset($_attachment_data[2]) && $_attachment_data[2] > 1 ) ? $_attachment_data[2] : $_height;
    } else { //old treatment
      //rebuild the logo path : check if the full path is already saved in DB. If not, then rebuild it.
      $upload_dir 			= wp_upload_dir();
      $_saved_path 			= esc_url ( TC_utils::$inst->tc_opt( "tc{$logo_type_sep}logo_upload") );
      $_logo_src 			= ( false !== strpos( $_saved_path , '/wp-content/' ) ) ? $_saved_path : $upload_dir['baseurl'] . $_saved_path;
    }
    //hook + makes ssl compliant
    $_logo_src    			= apply_filters( "tc{$logo_type_sep}logo_src" , is_ssl() ? str_replace('http://', 'https://', $_logo_src) : $_logo_src ) ;
    $logo_resize 			= ( $logo_type_sep == '_' ) ? esc_attr( TC_utils::$inst->tc_opt( 'tc_logo_resize') ) : '';
    $filetype 				= TC_utils::$inst -> tc_check_filetype ($_logo_src);
    if( ! empty($_logo_src) && in_array( $filetype['ext'], $accepted_formats ) )
      $args 		= array(
                'logo_src' 				=> $_logo_src,
                'logo_resize' 			=> $logo_resize,
                'logo_attachment_id' 	=> $_attachement_id,
                'logo_width' 			=> $_width,
                'logo_height' 			=> $_height,
                'logo_type'             => trim($logo_type_sep,'_')
      );
   
      return $args;
  }


  function tc_logo_css( $_css ) {
    //logos shrink
    //fire once
    static $_fired = false;
    if ( ! $_fired ) { 
      $_fired = true;  
      if ( ( 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_sticky_header') ) && 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_sticky_shrink_title_logo') ) ) || TC___::$instance -> tc_is_customizing() ) {
        $_logo_shrink  = implode (';' , apply_filters('tc_logo_shrink_css' , array("height:30px!important","width:auto!important") ) );
        $_css = sprintf("%s%s",
            $_css,
            "
        .sticky-enabled .tc-shrink-on .site-logo img {
          {$_logo_shrink}
        }"
        );
      }
    }//end logos shrink (fire once)

    //sticky-logo visibility  
    if ( 'sticky' == $this -> logo_type ) {

      $_css = sprintf( "%s%s",
        $_css,
        "
        .site-logo img.sticky {
            display: none;
         }
        .sticky-enabled .tc-sticky-logo-on .site-logo img {
            display: none;
         }
        .sticky-enabled .tc-sticky-logo-on .site-logo img.sticky {
            display: inline-block;
        }"
      );
    }//end sticky-logo css
    return $_css;  
  }
}//end class

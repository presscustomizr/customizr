<?php
class CZR_cl_post_list_content_model_class extends CZR_cl_Model {
  public  $content_cb;
  private $content;
  public  $content_width_class;

  function __construct( $model = array() ) {
    //Fires the parent constructor
    parent::__construct( $model );
    add_action( "__before_{$this -> id }", array( $this, 'setup_text_hooks') );
    add_action( "__after_{$this -> id }", array( $this, 'reset_text_hooks') );
  }

  function setup_text_hooks() {
    //filter the excerpt length
    add_filter( 'excerpt_length'        , array( $this , 'czr_fn_set_excerpt_length') , 999 );
  }

  function reset_text_hooks() {
    remove_filter( 'excerpt_length'        , array( $this , 'czr_fn_set_excerpt_length') , 999 );
  }

  function czr_fn_get_post_list_content( $more  = null, $link_pages = null ) {
    if ( $this -> content )
      return $this -> content;
    elseif ( 'get_the_excerpt' == $this -> content_cb )
      return '<div class="entry-summary">'. apply_filters( 'the_excerpt', get_the_excerpt() ) . '</div>';
    else
      //filter the content
      return '<div class="entry-summary">'.$this -> czr_fn_add_support_for_shortcode_special_chars( get_the_content( $more ) ) . $link_pages . '</div>';
  }



  /**
  * hook : excerpt_length hook
  * @return string
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function czr_fn_set_excerpt_length( $length ) {
    $_custom = esc_attr( czr_fn_get_opt( 'tc_post_list_excerpt_length' ) );
    return ( false === $_custom || !is_numeric($_custom) ) ? $length : $_custom;
  }


  /**
  *
  * @param string
  * @return  string
  *
  * @package Customizr
  * @since Customizr 3.3+
  */
  function czr_fn_add_support_for_shortcode_special_chars( $_content ) {
    return str_replace( ']]>', ']]&gt;', apply_filters( 'the_content', $_content ) );
  }


  function czr_fn_setup_late_properties() {
    $show_excerpt        = czr_fn_get( 'czr_show_excerpt' );
    $content_width_class = array( 'entry-summary' );
    $content_cb          = $show_excerpt ? 'get_the_excerpt' : 'get_the_content' ;
    $content             = '';
    $element_class       = czr_fn_get( 'czr_content_col' );

    if ( in_array( get_post_format(), array( 'image' , 'gallery' ) ) )
    {
      $content_width_class = array( 'entry-content' );
      $content             = '<p class="format-icon"></p>';
    }
    elseif ( in_array( get_post_format(), array( 'quote', 'status', 'link', 'aside', 'video' ) ) ) {
      $content_cb          = 'get_the_content';
    }
    $this -> czr_fn_update( compact( 'element_class', 'content_witdh_class', 'content_cb', 'content' ) );
  }

  /**
  * parse this model properties for rendering
  */
  function czr_fn_sanitize_model_properties( $model ) {
    parent::czr_fn_sanitize_model_properties( $model );
    $model -> content_width_class = $this -> czr_fn_stringify_model_property( 'content_width_class' );
  }
}
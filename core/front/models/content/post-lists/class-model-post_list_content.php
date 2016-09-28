<?php
class CZR_cl_post_list_content_model_class extends CZR_cl_Model {
  public  $content_cb;
  private $content;
  public  $content_class;
  public  $is_loop_start;
  public  $is_loop_end;
  public  $is_full_image;
  public  $has_header_format_icon;

  function __construct( $model = array() ) {
    //Fires the parent constructor
    parent::__construct( $model );
    add_action( "__before_{$this -> id }_content_retrieve", array( $this, 'setup_text_hooks') );
    add_action( "__after_{$this -> id }_content_retrieve", array( $this, 'reset_text_hooks') );
  }

  function setup_text_hooks() {
    if ( czr_fn_get( 'is_loop_start' ) )
      //filter the excerpt length
      add_filter( 'excerpt_length'        , array( $this , 'czr_fn_set_excerpt_length') , 999 );
  }

  function reset_text_hooks() {
    if ( czr_fn_get( 'is_loop_end' ) )
      remove_filter( 'excerpt_length'     , array( $this , 'czr_fn_set_excerpt_length') , 999 );
  }

  function czr_fn_get_post_list_content( $more  = null, $link_pages = null ) {

    do_action( "__before_{$this -> id }_content_retrieve" );

    if ( $this -> content )
      $to_return = $this -> content;
    elseif ( 'get_the_excerpt' == $this -> content_cb )
      $to_return = apply_filters( 'the_excerpt', get_the_excerpt() );
    elseif ( 'get_the_content' == $this -> content_cb )
      //filter the content
      $to_return = '<p>'.$this -> czr_fn_add_support_for_shortcode_special_chars( get_the_content( $more ) ) . $link_pages . '</p>';
    else
      $to_return = call_user_func( $this -> content_cb );

    do_action( "__after_{$this -> id }_content_retrieve" );

    return $to_return;
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
    $content                = '';
    $element_class          = czr_fn_get( 'czr_content_col' );
    $is_loop_start          = czr_fn_get( 'is_loop_start' );
    $is_loop_end            = czr_fn_get( 'is_loop_end' );
    $is_full_image          = czr_fn_get( 'is_full_image' );
    $has_header_format_icon = czr_fn_get( 'has_header_format_icon' );
    $show_full_content      = czr_fn_get( 'show_full_content' );

    /* The full content should become a total different model ? */
    $content_cb             = $this -> get_content_cb( $show_full_content ? 'get_the_content' : 'get_the_excerpt' );
    $content_class          = 'get_the_content' == $content_cb ? array( 'entry-content' ) : array( 'entry-summary' );


    $this -> czr_fn_update( compact(
      'element_class',
      'content_class',
      'content_cb',
      'content',
      'is_loop_start',
      'is_loop_end',
      'is_full_image',
      'has_header_format_icon'
    ) );
  }

  function get_content_cb( $default ) {
    $post_format         = get_post_format();

    switch( $post_format ) {
      case 'status' :
      case 'aside'  : return 'get_the_content';

      case 'link'   : return array( $this, 'get_the_post_link' );
      case 'quote'  : return array( $this, 'get_the_post_quote' );
      default       : return $default;
    }
  }


  /* Testing purpose */
  function get_the_post_link() {
    return '<p><a class="external" target="_blank" href="http://www.google.it">www.google.it</a></p>';
  }


  function get_the_post_quote() {
    $_content =  "Kogi Cosby sweater ethical squid irony disrupt, organic tote bag gluten-free XOXO wolf typewriter mixtape small batch.";
    if ( empty( get_the_title() ) )
      $_content = '<a title="'. the_title_attribute( array( 'before' => __('Permalink to ', 'customizr'), 'echo' => false ) ).'" href="'. esc_url( apply_filters( 'the_permalink', get_the_permalink() ) ) .'">' . $_content . '</a>';

    return '<blockquote class="blockquote entry-quote">
              <p class="m-b-0">'. $_content .'</p>
              <footer class="blockquote-footer"><cite title="Source Title">Some Crazy Idiot</cite></footer>
            </blockquote>';
  }
}
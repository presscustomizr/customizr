<?php
class CZR_cl_post_list_content_model_class extends CZR_cl_Model {
  public  $content_cb;
  public  $content;
  public  $content_inner_class;

  public  $has_header_format_icon;


  function czr_fn_get_the_post_list_content( $more  = null, $link_pages = null ) {
    do_action( "__before_content_retrieve", $this->id, $this );

    $content                = $this -> content ;
    $content_cb             = $this -> czr_fn_get_content_cb();

    if ( isset($content ) )
      $to_return = $content;
    elseif ( 'get_the_excerpt' == $content_cb )
      $to_return = apply_filters( 'the_excerpt', get_the_excerpt() );
    elseif ( 'get_the_content' == $content_cb )
      //filter the content
      $to_return = '<p>'.$this -> czr_fn_add_support_for_shortcode_special_chars( get_the_content( $more ) ) . $link_pages . '</p>';
    else
      $to_return = call_user_func( $content_cb );

    do_action( "__after_content_retrieve", $this->id, $this );

    return $to_return;
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


  function czr_fn_get_content_inner_class() {
    return 'get_the_excerpt' != $this -> czr_fn_get_content_cb() ? array( 'entry-content' ) : array( 'entry-summary' );
  }

  /* Should be cached at each loop ??? */
  function czr_fn_get_content_cb() {
    if ( isset( $this->content_cb ) )
      return $this->content_cb;

    $post_format         = get_post_format();

    switch( $post_format ) {
      case 'status' :
      case 'aside'  : return 'get_the_content';

      case 'link'   : return array( $this, 'get_the_post_link' );
      case 'quote'  : return array( $this, 'get_the_post_quote' );

      default       : return 'get_the_excerpt';
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
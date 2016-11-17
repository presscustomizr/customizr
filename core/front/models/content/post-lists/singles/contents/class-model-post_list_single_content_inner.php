<?php
class CZR_post_list_single_content_inner_model_class extends CZR_Model {
  public  $content_cb;
  public  $content;


  function czr_fn_get_the_post_list_content( $show_full_content = false, $more  = null ) {
    do_action( "__before_content_retrieve", $this->id, $this );

    $show_full_content      = $show_full_content ? $show_full_content : (
      isset($this->show_full_content) ? $this->show_full_content : false
    );
    $content                = $this -> content ;
    $content_cb             = $this -> czr_fn_get_content_cb( $show_full_content ? 'get_the_content' : 'get_the_excerpt' );

    if ( isset($content ) )
      $to_return = $content;
    elseif ( 'get_the_excerpt' == $content_cb )
      $to_return = apply_filters( 'the_excerpt', get_the_excerpt() );
    elseif ( 'get_the_content' == $content_cb )
      //filter the content
      $to_return = $this -> czr_fn_add_support_for_shortcode_special_chars( get_the_content( $more ) );
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


  function czr_fn_get_element_class() {
    return 'get_the_excerpt' != $this -> czr_fn_get_content_cb( 'get_the_excerpt' ) ? array( 'entry-content' ) : array( 'entry-summary' );
  }


  /* Should be cached at each loop ??? */
  function czr_fn_get_content_cb( $default ) {
    if ( isset( $this->content_cb ) )
      return $this->content_cb;

    $post_format         = get_post_format();

    switch( $post_format ) {
      case 'status'  :
      case 'aside'   : return 'get_the_content';

      case 'image'   :
      case 'video'   :
      case 'gallery' :
      case 'audio'   : return 'get_the_excerpt';

      case 'link'   : return array( $this, 'czr_fn__get_the_post_link' );
      case 'quote'  : return array( $this, 'czr_fn__get_the_post_quote' );

      default       : return $default;
    }
  }


  /* Testing purpose */
  function czr_fn__get_the_post_link() {
    return '<p><a class="external" target="_blank" href="http://www.google.it">www.google.it</a></p>';
  }


  function czr_fn__get_the_post_quote() {
    $_content =  "Kogi Cosby sweater ethical squid irony disrupt, organic tote bag gluten-free XOXO wolf typewriter mixtape small batch.";
    if ( empty( get_the_title() ) )
      $_content = '<a title="'. the_title_attribute( array( 'before' => __('Permalink to ', 'customizr'), 'echo' => false ) ).'" href="'. esc_url( apply_filters( 'the_permalink', get_the_permalink() ) ) .'">' . $_content . '</a>';

    return '<blockquote class="blockquote entry-quote">
              <p>'. $_content .'</p>
              <footer class="blockquote-footer"><cite title="Source Title">Some Crazy Idiot</cite></footer>
            </blockquote>';
  }
}
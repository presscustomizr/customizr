<?php
class CZR_post_list_single_content_inner_model_class extends CZR_Model {
  public  $content;

  public  $defaults = array( 'show_full_content' => false );

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
      else {
         if ( count( $content_cb ) > 1 ) {
            $to_return = call_user_func( $content_cb[0], $content_cb[1] );
         }
         else {
            $to_return = call_user_func( $content_cb[0] );
         }

      }

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
    return 'get_the_excerpt' != $this -> czr_fn_get_content_cb( $this->show_full_content ? 'get_the_content' : 'get_the_excerpt' ) ? array( 'entry-content' ) : array( 'entry-summary' );
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

      case 'link'   : return array( array( $this, 'czr_fn__get_the_post_link' ), $default );
      case 'quote'  : return array( array( $this, 'czr_fn__get_the_post_quote' ), $default );

      default       : return $default;
    }
  }


  /* Testing purpose */
  function czr_fn__get_the_post_link( $default ) {
      $link_id = !czr_fn_is_registered( 'link' ) ? czr_fn_register(
         array(
            'id'          => 'link',
            'render'      => false,
            'template'    => 'content/media/link',
            'model_class' => 'content/media/link',
         )
      ) : 'link' ;

      $link_instance = czr_fn_get_model_instance( $link_id );
      //reset any previous content
      $link_instance->czr_fn_reset();

      $content = $link_instance->czr_fn_get_content();

      if ( ! ( isset( $content[ 'url' ] ) && $content[ 'url' ] ) ) {
         return call_user_func( $default );
      }


      czr_fn_get_view_instance( $link_id ) -> czr_fn_maybe_render();

  }


   function czr_fn__get_the_post_quote( $default ) {

      $quote_id = !czr_fn_is_registered( 'quote' ) ? czr_fn_register(
         array(
            'id'          => 'quote',
            'render'      => false,
            'template'    => 'content/media/quote',
            'model_class' => 'content/media/quote',
         )
      ) : 'quote' ;

      $quote_instance = czr_fn_get_model_instance( $quote_id );
      //reset any previous content
      $quote_instance->czr_fn_reset();

      $content = $quote_instance->czr_fn_get_content();

      if ( ! ( isset( $content[ 'text' ] ) && $content['text'] ) ) {
         return call_user_func( $default );
      }

      czr_fn_get_view_instance( $quote_id ) -> czr_fn_maybe_render();

  }

}
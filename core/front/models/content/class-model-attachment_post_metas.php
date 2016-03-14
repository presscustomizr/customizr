<?php
class TC_attachment_post_metas_model_class extends TC_post_metas_model_class {

  //override
  public function tc_post_has_metas() {
    return is_attachment(); //in post lists. In single attachments the controller will prevent the model instantiation at wp
  }


  /* PUBLIC GETTERS */
  public function tc_get_attachment_width() {
    return $this -> tc_get_meta( 'size', 'width' );
  }

  public function tc_get_attachment_height() {
    return $this -> tc_get_meta( 'size', 'height' );
  }


  public function tc_get_attachment_parent_url() {
    return $this -> tc_get_meta( 'parent', 'url' );    
  }

  public function tc_get_attachment_parent_title() {
    return $this -> tc_get_meta( 'parent', 'title' );    
  }

  public function tc_is_attachment_size_defined() {
    return $this -> tc_get_attachment_width() && $this -> tc_get_attachment_height();    
  }


  public function tc_meta_generate_size( $what ) {
    $metadata = empty ( $this -> _cache['metadata'] ) ? wp_get_attachment_metadata() : $this -> _cache['metadata'];

    return isset( $metadata[$what] ) ? $metadata[$what] : null;
  }

  public function tc_meta_generate_parent( $what ) {
    $parent = $this -> tc_get_attachment_parent();
    
    switch ( $what ) {
      case 'title' : return ( get_the_title( $parent ) );
      case 'url'   : return esc_url( get_permalink( $parent ) );
    }
  }


  /* Helpers */
  private function tc_get_attachment_parent() {
    return $this -> tc_get_meta( 'attachment_parent' );    
  }
 
  public function tc_meta_generate_attachment_parent() {
    global $post;
    return $post -> post_parent;
  }


}//end of class

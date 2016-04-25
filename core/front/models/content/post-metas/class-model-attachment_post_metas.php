<?php
class CZR_cl_attachment_post_metas_model_class extends CZR_cl_post_metas_model_class {

  //override
  function tc_maybe_render_this_model_view() {
    return $this -> visibility && is_attachment(); //in post lists. In single (not attachments) the controller will prevent the model instantiation at wp
  }


  /* PUBLIC GETTERS */
  public function czr_get_attachment_width() {
    return $this -> czr_get_meta( 'size', 'width' );
  }

  public function czr_get_attachment_height() {
    return $this -> czr_get_meta( 'size', 'height' );
  }


  public function czr_get_attachment_parent_url() {
    return $this -> czr_get_meta( 'parent', 'url' );
  }

  public function czr_get_attachment_parent_title() {
    return $this -> czr_get_meta( 'parent', 'title' );
  }

  public function czr_get_attachment_size() {
    return $this -> czr_get_attachment_width() && $this -> czr_get_attachment_height();
  }


  public function tc_meta_generate_size( $what ) {
    $metadata = empty ( $this -> _cache['metadata'] ) ? wp_get_attachment_metadata() : $this -> _cache['metadata'];

    return isset( $metadata[$what] ) ? $metadata[$what] : null;
  }

  public function tc_meta_generate_parent( $what ) {
    $parent = $this -> czr_get_attachment_parent();

    switch ( $what ) {
      case 'title' : return ( get_the_title( $parent ) );
      case 'url'   : return esc_url( get_permalink( $parent ) );
    }
  }


  /* Helpers */
  private function czr_get_attachment_parent() {
    return $this -> czr_get_meta( 'attachment_parent' );
  }

  public function tc_meta_generate_attachment_parent() {
    global $post;
    return $post -> post_parent;
  }

  /**
  * Helper
  * Return the date post metas
  * @override
  *
  * @package Customizr
  * @since Customizr 3.2.6
  */
  protected function czr_get_meta_date( $pub_or_update = 'publication', $_format = '' ) {
    return sprintf( '<time class="entry-date updated" datetime="%1$s">%2$s</time>',
        apply_filters('tc_use_the_post_modified_date' , false ) ? esc_attr( get_the_date( 'c' ) ) : esc_attr( get_the_modified_date('c') ),
        esc_html( get_the_date() )
    );
  }
}//end of class

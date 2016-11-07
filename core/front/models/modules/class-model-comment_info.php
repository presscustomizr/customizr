<?php
class CZR_comment_info_model_class extends CZR_Model {
  public $link_attributes = array();

  /*
  * @param link (stirng url) the link
  * @param add_anchor (bool) whether or not add an anchor to the link, default true
  */
  function czr_fn_get_comment_info_link( $link, $add_anchor = true ) {
    $link = sprintf( "%s%s",
        is_singular() ? '' : esc_url( $link ),
        $add_anchor ? apply_filters( 'czr_comment_info_anchor', '#czr-comments-title') : ''
    );
    return $link;
  }

  function czr_fn_get_link_attributes() {
    return apply_filters( 'czr_comment_info_link_attributes', $this -> link_attributes );
  }
}
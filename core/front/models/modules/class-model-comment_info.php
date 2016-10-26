<?php
class CZR_comment_info_model_class extends CZR_Model {
  /*
  * @param link (stirng url) the link
  * @param add_anchor (bool) whether or not add an anchor to the link, default true
  */
  function czr_fn_get_comment_info_link( $link, $add_anchor = true ) {
    $link = sprintf( "%s%s",
        is_singular() ? '' : esc_url( $link ),
        $add_anchor ? apply_filters( 'czr_info_comment_anchor', '#czr-comments-title') : ''
    );
    return $link;
  }
}
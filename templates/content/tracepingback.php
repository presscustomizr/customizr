<li <?php comment_class() ?> id="li-comment-<?php comment_ID() ?>">
  <article id="comment-<?php comment_ID() ?>" class="comment">
    <p><?php _e( 'Pingback:' , 'customizr' ); ?> <?php comment_author_link(); ?>
    <?php if ( $tracepingback_model -> has_edit_button ) 
      edit_comment_link( __( '(Edit)' , 'customizr' ), '<span class="edit-link btn btn-success btn-mini">' , '</span>' );
    ?>
   </p>
  </article>

<li <?php comment_class() ?> id="li-comment-<?php comment_ID() ?>" <?php tc_echo('element_attributes') ?>>
  <article id="comment-<?php comment_ID() ?>" class="comment">
    <p><?php _e( 'Pingback:' , 'customizr' ); ?> <?php comment_author_link(); ?>
    <?php if ( tc_get( 'has_edit_button' ) ) 
      edit_comment_link( __( '(Edit)' , 'customizr' ), '<span class="edit-link btn btn-success btn-mini">' , '</span>' );
    ?>
   </p>
  </article>

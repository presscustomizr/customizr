<li <?php comment_class() ?> id="li-comment-<?php comment_ID() ?>">
  <article id="comment-<?php comment_ID() ?>" class="comment">
    <div class="<?php echo $comment_model -> comment_wrapper_class ?>">
      <div class="<?php echo $comment_model -> comment_avatar_class ?>">
       <?php echo get_avatar( $comment_model -> comment, $comment_model -> comment_avatar_size ) ?>
      </div>
      <div class="<?php echo $comment_model -> comment_content_class ?>">
        <div class="<?php echo $comment_model -> comment_reply_btn_class ?>">
          <?php comment_reply_link( $comment_model -> comment_reply_link_args ) ?>
        </div>
        <header class="comment-meta comment-author vcard">
          <cite class="fn">
            <?php comment_author_link() ?>
          <?php if ( $comment_model -> is_current_post_author ): ?>
            <span><?php _e( 'Post author' , 'customizr' ) ?></span>
          <?php endif; ?>
          <?php if ( $comment_model -> has_edit_button ): ?>
            <?php edit_comment_link( __( 'Edit' , 'customizr' ), '<p class="edit-link btn btn-success btn-mini">', '</p>' ) 
            ?>
          <?php endif; ?>
          </cite>
          <a class="comment-date" href="<?php comment_link() ?>">
            <time datetime="<?php comment_time() ?>"><?php comment_date();?> <?php _e('at', 'customizr') ?> <?php comment_time() ?></time>
          </a>
        </header>
      <?php if ( $comment_model -> is_awaiting_moderation ): ?>
        <p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.' , 'customizr' ) ?></p>
      <?php endif; ?>
        <section class="comment-content comment"><?php echo $comment_model -> comment_text ?></section>
      </div>
    </div>
  </article>

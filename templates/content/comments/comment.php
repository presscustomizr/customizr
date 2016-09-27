<?php
/**
 * The template for displaying the single comment
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<li <?php comment_class() ?> id="comment-<?php comment_ID() ?>" <?php czr_fn_echo('element_attributes') ?>>
  <div id ="div-comment-<?php comment_ID() ?>" class="comment-section clearfix">
    <div class="col-avatar">
      <figure class="comment-avatar">
        <?php echo get_avatar( $comment, 80 ) ?>
      </figure>
    </div>
    <div class="comment-body" role="complementary">
      <header class="comment-metas">
        <div clas="comment-metas-top">
          <div class="comment-author vcard">
            <?php comment_author_link() ?>
            <?php if ( czr_fn_get('is_current_post_author') ): ?>
              <span><?php _e( 'Post author' , 'customizr' ) ?></span>
            <?php endif; ?>
          </div>
          <time class="comment-date comment-meta commentmetadata" datetime="<?php comment_time() ?>">
            <span>
              <?php comment_date();?>,
            </span> <a class="comment-time" href="<?php comment_link() ?>"><?php comment_time() ?> </a></time>
          </time>
        </div>
        <?php if ( czr_fn_get( 'has_edit_button' ) ) : ?>
          <a class="comment-edit-link btn btn-edit" href="<?php echo esc_url( get_edit_comment_link( $comment ) ); ?>"><i class="icn-edit"></i><?php _e('Edit comment', 'customizr') ?></a>
        <?php endif; ?>
      </header>
      <div class="comment-content"><?php comment_text() ?></div>
      <?php if ( false != $comment_reply_link = get_comment_reply_link( czr_fn_get( 'comment_reply_link_args' ) ) ) : ?>
        <div class="reply btn btn-small">
          <?php echo $comment_reply_link ?>
        </div>
      <?php endif ?>
    </div>
  </div>
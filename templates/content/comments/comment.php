<?php
/**
 * The template for displaying the single comment/track-pingback in the list of comments
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */

/* Case we're displaying a track or ping back */
if ( 'trackpingback' == czr_get('type') ) :

?>
<li <?php comment_class() ?> id="li-comment-<?php comment_ID() ?>" <?php czr_echo('element_attributes') ?>>
  <article id="comment-<?php comment_ID() ?>" class="comment">
    <p><?php _e( 'Pingback:' , 'customizr' ); ?> <?php comment_author_link(); ?>
    <?php if ( czr_get( 'has_edit_button' ) )
      edit_comment_link( __( '(Edit)' , 'customizr' ), '<span class="edit-link btn btn-success btn-mini">' , '</span>' );
    ?>
   </p>
  </article>
<?php
/* Case we're displaying a standard comment */
else :

?>
<li <?php comment_class() ?> id="li-comment-<?php comment_ID() ?>" <?php czr_echo('element_attributes') ?>>
  <article id="comment-<?php comment_ID() ?>" class="comment">
    <div class="row-fluid">
      <div class="comment-avatar span2">
       <?php echo get_avatar( $comment, 80 ) ?>
      </div>
      <div class="span10">
      <?php if ( false != $comment_reply_link = get_comment_reply_link( czr_get( 'comment_reply_link_args' ) ) ) : ?>
        <div class="reply btn btn-small">
          <?php echo $comment_reply_link ?>
        </div>
      <?php endif ?>
        <header class="comment-meta comment-author vcard">
          <cite class="fn">
            <?php comment_author_link() ?>
          <?php if ( czr_get( 'is_current_post_author' ) ): ?>
            <span><?php _e( 'Post author' , 'customizr' ) ?></span>
          <?php endif; ?>
          <?php if ( czr_get( 'has_edit_button' ) ): ?>
            <?php edit_comment_link( __( 'Edit' , 'customizr' ), '<p class="edit-link btn btn-success btn-mini">', '</p>' )
            ?>
          <?php endif; ?>
          </cite>
          <a class="comment-date" href="<?php comment_link() ?>">
            <time datetime="<?php comment_time() ?>"><?php comment_date();?> <?php _e('at', 'customizr') ?> <?php comment_time() ?></time>
          </a>
        </header>
      <?php if ( czr_get( 'is_awaiting_moderation' ) ): ?>
        <p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.' , 'customizr' ) ?></p>
      <?php endif; ?>
        <section class="comment-content comment"><?php czr_echo( 'comment_text' ) ?></section>
      </div>
    </div>
  </article>
<?php endif;

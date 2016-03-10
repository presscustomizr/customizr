<?php
$depth = $comment_model -> depth;
$comment = $comment_model -> comment;
$args = $comment_model -> args;

    //get user defined max comment depth
    $max_comments_depth = get_option('thread_comments_depth');
    $max_comments_depth = isset( $max_comments_depth ) ? $max_comments_depth : 5;

    ob_start();

    switch ( $comment->comment_type ) :
      case 'pingback' :
      case 'trackback' :
      // Display trackbacks differently than normal comments.
    ?>
    <li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
      <article id="comment-<?php comment_ID(); ?>" class="comment">
        <p><?php _e( 'Pingback:' , 'customizr' ); ?> <?php comment_author_link(); ?>
            <?php if ( ! TC___::$instance -> tc_is_customizing() )  edit_comment_link( __( '(Edit)' , 'customizr' ), '<span class="edit-link btn btn-success btn-mini">' , '</span>' ); ?>
        </p>
      </article>
    <?php
        break;
      default :
      // Proceed with normal comments.
      global $post;
    ?>
    <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">

        <?php
          //when do we display the comment content?
          $tc_show_comment_content = 1 == get_option( 'thread_comments' ) && ($depth < $max_comments_depth) && comments_open();

          //gets the comment text => filter parameter!
          $comment_text = get_comment_text( $comment->comment_ID , $args );

          printf('<article id="comment-%9$s" class="comment"><div class="%1$s"><div class="%2$s">%3$s</div><div class="%4$s">%5$s %6$s %7$s %8$s</div></div></article>',
              apply_filters( 'tc_comment_wrapper_class', 'row-fluid' ),
              apply_filters( 'tc_comment_avatar_class', 'comment-avatar span2' ),
              get_avatar( $comment, apply_filters( 'tc_comment_avatar_size', 80 ) ),
              apply_filters( 'tc_comment_content_class', 'span10' ),

              $tc_show_comment_content ? sprintf('<div class="%1$s">%2$s</div>',
                                        apply_filters( 'tc_comment_reply_btn_class', 'reply btn btn-small' ),
                                        get_comment_reply_link( array_merge(
                                                                    $args,
                                                                    array(  'reply_text' => __( 'Reply' , 'customizr' ).' <span>&darr;</span>',
                                                                            'depth' => $depth,
                                                                            'max_depth' => $args['max_depth'] ,
                                                                            'add_below' => apply_filters( 'tc_comment_reply_below' , 'comment' )
                                                                          )
                                                              )
                                        )
              ) : '',

              sprintf('<header class="comment-meta comment-author vcard">%1$s %2$s</header>',
                    sprintf( '<cite class="fn">%1$s %2$s %3$s</cite>' ,
                        get_comment_author_link(),
                        // If current post author is also comment author, make it known visually.
                        ( $comment->user_id === $post->post_author ) ? '<span> ' . __( 'Post author' , 'customizr' ) . '</span>' : '' ,
                        ! TC___::$instance -> tc_is_customizing() && current_user_can( 'edit_comment', $comment->comment_ID ) ? '<p class="edit-link btn btn-success btn-mini"><a class="comment-edit-link" href="' . get_edit_comment_link( $comment->comment_ID ) . '">' . __( 'Edit' , 'customizr' ) . '</a></p>' : ''
                    ),
                    sprintf( '<a class="comment-date" href="%1$s"><time datetime="%2$s">%3$s</time></a>' ,
                        esc_url( get_comment_link( $comment->comment_ID ) ),
                        get_comment_time( 'c' ),
                        /* translators: 1: date, 2: time */
                        sprintf( __( '%1$s at %2$s' , 'customizr' ), get_comment_date(), get_comment_time() )
                    )
              ),

              ( '0' == $comment->comment_approved ) ? sprintf('<p class="comment-awaiting-moderation">%1$s</p>',
                __( 'Your comment is awaiting moderation.' , 'customizr' )
                ) : '',

              sprintf('<section class="comment-content comment">%1$s</section>',
                apply_filters( 'comment_text', $comment_text, $comment, $args )
              ),
              $comment->comment_ID
            );//end printf
        ?>
      <!-- //#comment-## -->
    <?php
      break;
    endswitch; // end comment_type check

    $html = ob_get_contents();
    if ($html) ob_end_clean();
    echo apply_filters( 'tc_comment_callback' , $html, $comment, $args, $depth, $max_comments_depth );


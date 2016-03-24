<h2 id="tc-comment-title" class="comments-title" <?php tc_echo('element_attributes') ?>><?php /* Comments list title */
  comments_number( false, __( 'One thought on', 'customizr'), '% ' . __( 'thoughts on', 'customizr' ) )
?> &ldquo;</span><?php the_title() ?></span>&rdquo;</h2>
<ul class="commentlist">
  <?php wp_list_comments( tc_get('args') )   /* Comments list */ ?>
</ul>
<?php if ( get_option( 'page_comments' ) && get_comment_pages_count() > 1) : /* Comments Navigation */ ?>
<nav id="comment-nav-below" class="navigation" role="navigation">
  <h3 class="assistive-text section-heading"><?php _e( 'Comment navigation' , 'customizr' ); ?></h3>
  <ul class="pager">

    <?php if( get_previous_comments_link() != null ) : ?>

      <li class="previous">
        <span class="nav-previous"><?php previous_comments_link( __( '<span class="meta-nav">&larr;</span> Older Comments' , 'customizr' ) ); ?></span>
      </li>

    <?php endif; ?>

    <?php if( get_next_comments_link() != null ) : ?>

      <li class="next">
        <span class="nav-next"><?php next_comments_link( __( 'Newer Comments <span class="meta-nav">&rarr;</span>' , 'customizr' ) ); ?></span>
      </li>

    <?php endif; ?>

  </ul>
</nav>
<?php endif; /* end comments list navigation */

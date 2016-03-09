<nav id="comment-nav-below" class="navigation" role="navigation">
  <h3 class="assistive-text section-heading"><?php _e( 'Comment navigation' , 'customizr' ); ?></h3>
  <ul class="pager">

    <?php if(get_previous_comments_link() != null) : ?>

      <li class="previous">
        <span class="nav-previous"><?php previous_comments_link( __( '<span class="meta-nav">&larr;</span> Older Comments' , 'customizr' ) ); ?></span>
      </li>

    <?php endif; ?>

    <?php if(get_next_comments_link() != null) : ?>

      <li class="next">
        <span class="nav-next"><?php next_comments_link( __( 'Newer Comments <span class="meta-nav">&rarr;</span>' , 'customizr' ) ); ?></span>
      </li>

    <?php endif; ?>

  </ul>
</nav>

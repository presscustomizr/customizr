<?php
/**
 * The template for displaying the comment list:
 * title
 * wp_list_comments
 * comment navigation
 */
?>
<?php global $wp_query; ?>
<h2 id="czr-comments-title" class="comments-title" <?php czr_fn_echo('element_attributes') ?>><?php /* Comments list title */
  comments_number( false, __( 'One thought on', 'customizr'), '% ' . __( 'thoughts on', 'customizr' ) )
?> &ldquo;</span><?php the_title() ?></span>&rdquo;</h2>

<ul class="nav nav-pills">
  <?php if ( ! empty ( $wp_query->comments_by_type['comment'] ) ) :
    $comments_number = count($wp_query->comments_by_type['comment']);
  ?>
  <!-- WITH COMMENTS PAGINATION THE COMMENT/PINGBACK COUNT IS WRONG AS IS COUNTS JUST THE NUMBER OF ELEMENTS OF THE CURRENT (PAEG) QUERY -->
    <li class="nav-item"><a href="#commentlist-container" class="nav-link active" data-toggle="pill" role="tab"><?php echo $comments_number ?>&nbsp<?php echo _n( 'comment' , 'comments' , $comments_number, 'customizr' ) ?></a></li>
  <?php endif ?>
  <?php if ( ! empty ( $wp_query->comments_by_type['pings'] ) ) :
    $pings_number = count($wp_query->comments_by_type['pings']);
  ?>
    <li class="nav-item"><a href="#pinglist-container" class="nav-link" data-toggle="pill" role="tab"><?php echo $pings_number ?>&nbsp<?php echo _n( 'pingback' , 'pingbacks' , $pings_number, 'customizr' ) ?></a></li>
  <?php endif ?>
</ul>
<div id="comments" class="tab-content">
  <?php if ( ! empty( $wp_query->comments_by_type['comment'] ) ) : ?>
    <div id="commentlist-container" class="tab-pane comments active" role="tabpanel">
      <ul class="comment-list">
        <?php

        /* Comments list */
        wp_list_comments( array_merge( czr_fn_get( 'czr_args' ),  array( 'type' => 'comment' ) ) );

        ?>
      </ul>
    </div>
  <?php
  endif;
  if ( ! empty( $wp_query->comments_by_type['pings'] ) ) :
    $_active = empty( $wp_query->comments_by_type['comment'] ); ?>
    <div id="pinglist-container" class="<?php echo $_active ?> tab-pane pings" role="tabpanel">
      <ul class="pingback-list">
        <?php

        /* Pings list */
        wp_list_comments( array_merge( czr_fn_get( 'czr_args' ),  array( 'type' => 'pings' ) ) );

        ?>
      </ul>
    </div>
  <?php
  endif;
  ?>
</div>
<?php
/*
TO STYLE!!!

Comments Navigation */
if ( get_option( 'page_comments' ) && get_comment_pages_count() > 1 ) :

?>
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
if ( ! comments_open() && get_comments_number() ) : ?>
<p class="nocomments"><?php _e( 'Comments are closed.' , 'customizr' ) ?></p>
<?php endif;

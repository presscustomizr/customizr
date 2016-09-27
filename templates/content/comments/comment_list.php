<?php
/**
 * The template for displaying the comment list:
 * title
 * wp_list_comments
 * comment navigation
 */
?>
<?php global $wp_query; ?>

<ul class="nav nav-pills">
  <?php if ( ! empty ( $wp_query->comments_by_type['comment'] ) ) : ?>
    <li class="nav-item"><a href="#commentlist-container" class="nav-link active" data-toggle="pill" role="tab"><?php echo count($wp_query->comments_by_type['comment']) ?>&nbsp<?php _e( 'comments', 'customizr' ) ?></a></li>
  <?php endif ?>
  <?php if ( ! empty ( $wp_query->comments_by_type['pings'] ) ) : ?>
    <li class="nav-item"><a href="#pinglist-container" class="nav-link" data-toggle="pill" role="tab"><?php echo count($wp_query->comments_by_type['pings']) ?>&nbsp<?php _e( 'pingbacks', 'customizr' ) ?></a></li>
  <?php endif ?>
</ul>
<div id="comments" class="tab-content">
  <?php if ( ! empty( $wp_query->comments_by_type['comment'] ) ) : ?>
    <div id="commentlist-container" class="tab-pane comments active" role="tabpanel">
      <ol class="comment-list">
        <?php

        /* Comments list */
        wp_list_comments( array( 'type' => 'comment', 'callback' => czr_fn_get( 'czr_comments_callback' ) ) );

        ?>
      </ol>
    </div>
  <?php
  endif;
  if ( ! empty( $wp_query->comments_by_type['pings'] ) ) :
    $_active = empty( $wp_query->comments_by_type['comment'] ); ?>
    <div id="pinglist-container" class="<?php echo $_active ?> tab-pane pings" role="tabpanel">
      <ol class="pingback-list">
        <?php

        /* Pings list */
        wp_list_comments( array( 'type' => 'pings', 'callback' => czr_fn_get( 'czr_comments_callback' ) ) );

        ?>
      </ol>
    </div>
  <?php
  endif;
  ?>
</div>
<?php
/*
TO STYLE!!!

Comments Navigation */
if ( get_option( 'page_comments' ) && get_comment_pages_count() > 1) :

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

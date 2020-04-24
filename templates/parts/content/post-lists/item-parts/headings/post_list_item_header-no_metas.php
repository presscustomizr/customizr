<?php
/**
 * The template for displaying the header of a post in a post list
 * In CZR loop
 *
 * @package Customizr
 */
?>
<header class="entry-header <?php czr_fn_echo( 'element_class' ) ?>" <?php czr_fn_echo('element_attributes') ?>>
  <div class="entry-header-inner <?php czr_fn_echo( 'entry_header_inner_class' ) ?>">
    <?php
      if ( czr_fn_get_property( 'has_header_format_icon' ) ):
    ?>
      <div class="post-type__icon"><i class="icn-format"></i></div>
    <?php
      endif;//has_header_format_icon

      if ( czr_fn_get_property( 'the_title' ) ):
    ?>
    <?php do_action( '__before_post_list_heading_title' ); ?>
    <h2 class="entry-title ">
      <a class="czr-title" href="<?php the_permalink() ?>" rel="bookmark"><?php czr_fn_echo( 'the_title' ) ?></a>
    </h2>
    <?php do_action( '__after_post_list_heading_title' ); ?>
    <?php
      endif; //the_title

      czr_fn_comment_info( array( 'before' => '<div class="post-info">', 'after' => '</div>') );

      if ( (bool) $edit_post_link = get_edit_post_link() )
        czr_fn_edit_button( array( 'link'  => $edit_post_link ) );
    ?>
  </div>
</header>
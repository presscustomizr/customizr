<?php
/**
 * The template for displaying the header of a post in a post list
 * In WP loop
 *
 * @package Customizr
 */
?>
<header class="entry-header <?php czr_fn_echo( 'element_class' ) ?>" <?php czr_fn_echo('element_attributes') ?>>
  <div class="entry-header-inner <?php czr_fn_echo( 'entry_header_inner_class' ) ?>">
  <?php /* Maybe treat this case with CSS only */
    if ( czr_fn_get( 'has_header_format_icon' ) ): ?>
      <div class="post-type__icon"><i class="icn-format"></i></div>
  <?php endif; ?>
    <h2 class="entry-title ">
      <a class="czr-title" href="<?php the_permalink() ?>" title="<?php the_title_attribute( array( 'before' => __('Permalink to ', 'customizr') ) ) ?>" rel="bookmark"><?php the_title() ?></a>
    </h2>
    <?php
      if ( czr_fn_has('edit_button') && (bool) $edit_post_link = get_edit_post_link() )
        czr_fn_render_template(
            'modules/edit_button',
            array(
              'model_args' => array(
                'edit_button_link'  => $edit_post_link,
              )
            )
        );
    ?>
  </div>
</header>
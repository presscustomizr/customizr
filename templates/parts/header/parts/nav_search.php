<?php
/**
 * The template for displaying the header search form item button
 */
?>
<li class="nav__search <?php czr_fn_echo('element_class') ?>" <?php czr_fn_echo('element_attributes') ?>>
  <a href="#" class="search-toggle_btn icn-search <?php czr_fn_echo('search_toggle_class'); ?>" <?php czr_fn_echo('search_toggle_attributes'); ?> aria-expanded="false"><span class="sr-only">Search</span></a>
  <?php // May 2020 for https://github.com/presscustomizr/customizr/issues/1807 ?>
  <?php if ( !czr_fn_is_checked('tc_header_search_full_width') ) : ?>
    <div class="czr-search-expand">
      <div class="czr-search-expand-inner"><?php get_search_form(); ?></div>
    </div>
  <?php endif; ?>
  <?php if ( czr_fn_get_property( 'has_dropdown' ) ) : ?>
    <ul class="dropdown-menu czr-dropdown-menu">
      <?php
        czr_fn_render_template( 'header/parts/search_form', array(
          'model_args' => array(
            'element_tag'     => 'li',
            'element_class'   => czr_fn_get_property( 'search_form_container_class' )
          )
        ) );
      ?>
    </ul>
  <?php endif ?>
</li>

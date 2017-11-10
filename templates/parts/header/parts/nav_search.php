<?php
/**
 * The template for displaying the header search form item button
 */
?>
<li class="nav__search <?php czr_fn_echo('element_class') ?>" <?php czr_fn_echo('element_attributes') ?>>
  <a href="#" class="search-toggle_btn icn-search <?php czr_fn_echo('search_toggle_class'); ?>" <?php czr_fn_echo('search_toggle_attributes'); ?> aria-expanded="false"><span class="sr-only">Search</span></a>
  <?php if ( czr_fn_get_property( 'has_dropdown' ) ) : ?>
    <ul class="dropdown-menu czr-dropdown-menu">
      <?php
        czr_fn_render_template( 'header/parts/search_form', array(
          'model_args' => array(
            'element_tag'     => 'li'
          )
        ) );
      ?>
    </ul>
  <?php endif ?>
</li>

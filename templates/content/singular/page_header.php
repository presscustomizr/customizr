<?php
/**
* The template for displaying the page titles
*/
/*
* TODO: what to show? featured image, header image video .. ????
*/
?>
<div class="row page__header plain <?php czr_fn_echo( 'element_class' ) ?>" <?php czr_fn_echo('element_attributes') ?>>
  <div class="container header-content">
    <div class="header-content-inner entry-header">
      <h1 class="header-title"><?php the_title() ?></h1>
      <?php
        if ( czr_fn_has('edit_button') && (bool) $edit_post_link = get_edit_post_link() )
          czr_fn_render_template( 'modules/edit_button', 'edit_button', array(
              'edit_button_class' => '',
              'edit_button_title' => __( 'Edit page', 'customizr' ),
              'edit_button_text'  => __( 'Edit page', 'customizr' ),
              'edit_button_link'  => $edit_post_link,
            ) );
      ?>
      </div>
    </div>
  </div>
</div>
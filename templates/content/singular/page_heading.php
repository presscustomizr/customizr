<?php
/**
* The template for displaying the page titles
*/
/*
* TODO: what to show? featured image, header image video .. ????
*/
?>
<header class="row page__header image__header entry-header" <?php czr_fn_echo('element_attributes') ?>>
  <div class="container header-content">
    <div class="header-content-inner">
      <h1 class="entry-title"><?php the_title() ?></h1>
      <?php
        if ( czr_fn_has('edit_button') && (bool) $edit_post_link = get_edit_post_link() )
          czr_fn_render_template( array(
            'template'   => 'modules/edit_button',
            'model_args' => array(
                'edit_button_link'  => $edit_post_link
            )
          ));
      ?>
    </div>
  </div>
</header>
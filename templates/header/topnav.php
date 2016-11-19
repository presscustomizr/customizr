<?php
/**
 * The template for displaying the topnav
*/
?>
<div class="hidden-md-down secondary-navbar__wrapper row" <?php czr_fn_echo('element_attributes') ?>>
  <div class="col-md-9">
    <nav class="secondary-nav__nav">
      <?php czr_fn_render_template( 'header/menu', array( 'model_id' => 'secondary_menu' ) ) ?>
    </nav>
  </div>
  <div class="col-md-3">
    <div class="secondary-nav__socials">
      <?php
        if ( czr_fn_has('header_socials') )
          czr_fn_render_template(
            'modules/social_block',
            array(
              'model_id'   => 'header_socials',
              'model_args' => array(
                'element_class' => 'nav navbar-nav socials'
              )
            )
          );
      ?>
    </div>
  </div>
</div>
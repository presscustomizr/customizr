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
    <div class="header-content-inner">
      <h1 class="header-title"><?php the_title() ?></h1>
      </div>
    </div>
  </div>
</div>
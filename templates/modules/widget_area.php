<?php if ( tc_get('element_id') ): ?>
<div class="<?php tc_echo('element_class') ?>" id="<?php tc_echo('element_id') ?> <?php tc_echo('element_attributes') ?>">
<?php endif ?>
  <?php dynamic_sidebar(  tc_get( 'id' ) ) ?>
<?php if ( tc_get('element_id') ) : ?>
  </div>
<?php endif;

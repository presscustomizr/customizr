<?php do_action( '__headings_'. tc_get( 'type' ) . '__' ) ?>
<?php if ( 'content' == tc_get( 'type' ) ) : ?>
  <?php do_action( '__post_page_title__' ) ?>
  <?php do_action( '__post_metas__' ) ?>
<?php endif;

<?php do_action( "__headings_{$headings_model -> type}__" ) ?>
<?php if ( 'content' == $headings_model -> type ) : ?>
  <?php do_action( "__post_page_title__" ) ?>
  <?php do_action( "__post_metas__" ) ?>
<?php endif; ?>

<header class="<?php tc_echo( 'element_class' ) ?>" role="banner">
  <?php do_action( '__header__'); ?>
</header>
<?php if ( tc_get('has_sticky_pusher') ): ?>
  <div id="tc-reset-margin-top" class="container-fluid" style="margin-top:<?php tc_echo( 'pusher_margin_top' ) ?>"></div>
<?php endif;

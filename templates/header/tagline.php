<?php if ( 'mobile' == tc_get('context') ) : ?>
<div class="container outside">
<?php endif ?>
  <h2 class="site-description <?php tc_echo( 'class' ) ?>" <?php tc_echo( 'attributes' ) ?>>
    <?php _e( esc_attr( get_bloginfo( 'description' ) ) ) ?>  
  </h2>
<?php if ( 'mobile' == tc_get('context') ) : ?>
</div>
<?php endif;

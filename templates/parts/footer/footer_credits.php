<?php
/**
 * The template for displaying the footer credits
 *
 */
?>
<div id="footer__credits" class="footer__credits" <?php czr_fn_echo('element_attributes') ?>>
  <p class="czr-copyright">
    <span class="czr-copyright-text">&copy;&nbsp;<?php echo esc_html( date('Y') ) ?>&nbsp;</span><a class="czr-copyright-link" href="<?php echo esc_url( home_url() ) ?>" title="<?php echo esc_attr( get_bloginfo() ) ?>"><?php echo esc_html( get_bloginfo() ) ?></a><span class="czr-rights-text">&nbsp;&ndash;&nbsp;<?php _e( 'All rights reserved', 'customizr') ?></span>
  </p>
  <p class="czr-credits">
    <span class="czr-designer">
      <span class="czr-wp-powered"><span class="czr-wp-powered-text"><?php _e( 'Powered by', 'customizr') ?>&nbsp;</span><a class="czr-wp-powered-link" title="<?php _e( 'Powered by WordPress', 'customizr' ) ?>" href="<?php echo esc_url( __( 'https://wordpress.org/', 'customizr' ) ); ?>" target="_blank" rel="noopener noreferrer">WP</a></span><span class="czr-designer-text">&nbsp;&ndash;&nbsp;<?php printf( __('Designed with the %s', 'customizr'), sprintf( '<a class="czr-designer-link" href="%1$s" title="%2$s">%2$s</a>', esc_url( CZR_WEBSITE . 'customizr' ), __('Customizr theme', 'customizr') ) ); ?></span>
    </span>
  </p>
</div>

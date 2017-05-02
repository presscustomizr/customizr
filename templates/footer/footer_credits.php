<?php
/**
 * The template for displaying the footer credits
 *
 */
?>
<div id="footer__credits" class="footer__credits" <?php czr_fn_echo('element_attributes') ?>>
  <p class="czr-copyright">
    <span class="czr-copyright-text">&copy;&nbsp;<?php echo esc_attr( date('Y') ) ?>&nbsp;</span><a class="czr-copyright-link" href="<?php echo esc_url( home_url() ) ?>" title="<?php echo esc_attr( get_bloginfo() ) ?>"><?php echo esc_attr( get_bloginfo() ) ?></a><span class="czr-rights-text">&nbsp;&ndash;&nbsp;<?php _e( 'All rights reserved', 'customizr') ?></span>
  </p>
  <p class="czr-credits">
    <span class="czr-designer"><span class="czr-designer-text"><?php _e('Designed by', 'customizr') ?>&nbsp;<a class="czr-designer-link" href="<?php echo CZR_WEBSITE ?>" title="Press Customizr">Press Customizr</a></span><span class="czr-wp-powered">&nbsp;&ndash;&nbsp;<span class="czr-wp-powered-text"><?php _e( 'Powered by', 'customizr') ?>&nbsp;</span><a class="czr-wp-powered-link fa fa-wordpress" title="<?php _e( 'Powered by WordPress', 'customizr' ) ?>" href="<?php echo esc_url( __( 'https://wordpress.org/', 'customizr' ) ); ?>" target="_blank"></a></span></span>
  </p>
</div>

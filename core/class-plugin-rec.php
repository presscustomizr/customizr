<?php
//@return bool
function czr_fn_rec_notice_is_dismissed() {
  $dismissed = get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true );
  $dismissed_array = array_filter( explode( ',', (string) $dismissed ) );
  return in_array( REC_NOTICE_ID, $dismissed_array );
}

add_action( 'admin_notices', 'czr_fn_maybe_render_rec_notice' );
function czr_fn_maybe_render_rec_notice() {
  $screen = get_current_screen();
  if ( isset( $screen->parent_file ) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id ) {
    return;
  }
  if ( czr_fn_rec_notice_is_dismissed() )
    return;

  $plugin = 'nimble-builder/nimble-builder.php';
  $installed_plugins = get_plugins();
  $is_nimble_installed = isset( $installed_plugins[ $plugin ] );

  $heading = sprintf( __('Customizr theme recommends %1$s.', 'customizr' ),
          sprintf('<a href="%1$s" class="thickbox">%2$s</a>',
              wp_nonce_url( 'plugin-install.php?tab=plugin-information&amp;plugin=nimble-builder&amp;TB_iframe=true&amp;width=640&amp;height=500'),
              __('Nimble Page Builder', 'customizr')
          )
      );

  $message = sprintf( '<span style="font-weight:normal; font-style:italic">%1$s<br/> %2$s<br/>%3$s</span>',
    __( 'Nimble Page Builder is a free, easy to use yet powerful page builder used by 30K+ WordPress websites.',  'customizr'),
    sprintf(
        __( 'It allows you to create mobile ready column layouts, and to drag-and-drop modules like post grids, buttons, widget zones, maps, icons, or beautiful pre-built sections with engaging %1$s, in any page of your site. Nimble Builder uses the live customizer which is the native WordPress interface for real-time design.', 'customizr' ),
        sprintf('<a href="%1$s" target="_blank" title="%2$s">%2$s</a>', esc_url('demo.presscustomizr.com/nimble-builder/'), __('parallax backgrounds', 'customizr') )
    ),
    __( "The plugin has been designed to integrate perfectly with the Customizr theme. Lightweight and safe.", 'customizr')
  );

  if ( $is_nimble_installed ) {
    if ( ! current_user_can( 'activate_plugins' ) ) {
      return;
    }
    $button_text = __( 'Activate Nimble Builder Now', 'customizr' );
    $button_link = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin );
  } else {
    if ( ! current_user_can( 'install_plugins' ) ) {
      return;
    }
    $button_text = __( 'Install Nimble Builder Now', 'customizr' );
    $button_link = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=nimble-builder' ), 'install-plugin_nimble-builder' );
  }
  $notice_id = REC_NOTICE_ID;
  ?>
  <script>
    jQuery( function( $ ) {
    $( <?php echo wp_json_encode( "#$notice_id" ); ?> ).on( 'click', '.notice-dismiss', function() {
      $.post( ajaxurl, {
        pointer: <?php echo wp_json_encode( $notice_id ); ?>,
        action: 'dismiss-wp-pointer'
      } );
    } );
  } );
  </script>
  <div class="notice updated is-dismissible czr-nimble-rec-notice" id="<?php echo esc_attr( $notice_id ); ?>">
    <div class="czr-nimble-rec-notice-inner">
      <div class="czr-rec-text-block">
        <h3><span class="czr-nimble-rec-notice-icon"><img src="<?php echo get_template_directory_uri() . '/assets/back/img/nimble_icon.svg'; ?>" alt="Nimble Builder Logo" /></span><span class="czr-nimble-rec-notice-title"><?php echo $heading; ?></span></h3>
        <p><?php echo $message; ?></p>
        <span class="czr-rec-button"><a class="button button-primary button-hero activate-now" href="<?php echo esc_attr( $button_link ); ?>" data-name="Nimble Builder" data-slug="nimble-builder"><?php echo $button_text; ?></a></span>
      </div>
      <div class="czr-tgmpa-img-block"><img src="<?php echo get_template_directory_uri() . '/assets/back/img/nimble_customizr_145.gif'; ?>" alt="Nimble Builder" title="Nimble Builder" class="czr-nimble-img"></div>
    </div>
  </div>
  <?php
}
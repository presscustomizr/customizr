<?php
//@return bool
function czr_fn_rec_notice_is_dismissed() {
  $dismissed = get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true );
  $dismissed_array = array_filter( explode( ',', (string) $dismissed ) );
  return ( defined('NIMBLE_RECOMMENDATION_OFF') && true === NIMBLE_RECOMMENDATION_OFF ) || in_array( REC_NOTICE_ID, $dismissed_array );
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
          sprintf('<a href="%1$s" class="thickbox" target="_blank">%2$s</a>',
              wp_nonce_url( 'plugin-install.php?tab=plugin-information&amp;plugin=nimble-builder&amp;TB_iframe=true&amp;width=640&amp;height=500'),
              __('Nimble Page Builder', 'customizr')
          )
      );

  $message = sprintf( '<span style="font-weight:normal;">%1$s<br/> %2$s<br/>%3$s<br/>%4$s</span>',
    __( 'Developers of the Customizr theme have created Nimble Builder, a free, powerful yet easy-to-use page builder already active on 50K+ WordPress websites.',  'customizr'),
    __( 'It allows you to drag and drop mobile-ready sections on <i>really</i> any page of your site, including home, posts, pages, products, archives, 404, search pages, ...', 'customizr' ),
    sprintf(
        __( 'You can insert simple text zones, but also create %1$s, insert post grids, column structures, buttons, widget zones, maps, icons, and much more, or use pre-designed sections with professional %2$s.', 'customizr'),
        sprintf('<a href="%1$s" target="_blank" title="%2$s">%2$s</a>', esc_url('nimblebuilder.com/mp4-video-background-with-delay/'), __('video backgrounds', 'customizr') ),
        sprintf('<a href="%1$s" target="_blank" title="%2$s">%2$s</a>', esc_url('demo.presscustomizr.com/nimble-builder/'), __('parallax effect', 'customizr') )
    ),
    __( "The plugin is lightweight and has been designed to integrate seamlessly with Customizr and any WordPress theme.", 'customizr')
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
      <div class="czr-tgmpa-img-block"><img src="https://f060d5e1352d17626dec-db6380d80b2761f95de6177fb4431643.ssl.cf5.rackcdn.com/img/nimble_customizr_145.gif" alt="Nimble Builder" title="Nimble Builder" class="czr-nimble-img"></div>
    </div>
  </div>
  <?php
}
<?php
$header_layouts = esc_attr( czr_fn_opt( 'tc_header_layout' ) );
$is_logo_centered = 'centered' === $header_layouts;
$branding_row_classes = ['branding-row', 'd-flex', 'flex-row','align-items-center'];
if ( !$is_logo_centered ) {
  $branding_row_classes[] = 'align-self-start';
}

/**
 * The template for displaying the branding wrapper
 * July 2017 : no specific model for this template. The 'inner_branding_class' property => is added to the default model when invoking the czr_fn_render_template
 */
?>
<div class="branding__container <?php czr_fn_echo('element_class') ?>" <?php czr_fn_echo('element_attributes') ?>>
  <div class="branding align-items-center flex-column <?php czr_fn_echo( 'inner_branding_class' ); ?>">
    <div class="<?php echo implode(' ', $branding_row_classes); ?>">
      <?php
        if ( czr_fn_is_registered_or_possible('logo_wrapper') ) {
          czr_fn_render_template( 'header/parts/logo_wrapper' );
        } else if ( czr_fn_is_registered_or_possible('title_alone') ) {
          czr_fn_render_template( 'header/parts/title' );
        }
        if ( czr_fn_is_registered_or_possible('title_next_logo') || czr_fn_is_registered_or_possible( 'branding_tagline_aside' ) ) { ?>
          <div class="branding-aside col-auto flex-column d-flex">
          <?php
            if ( czr_fn_is_registered_or_possible('title_next_logo') ) {
              czr_fn_render_template( 'header/parts/title' );
            }
            if ( czr_fn_is_registered_or_possible( 'branding_tagline_aside' ) ) {
              czr_fn_render_template( 'header/parts/tagline' );
            }
          ?>
          </div>
          <?php
        }
        ?>
      </div>
    <?php
    if ( czr_fn_is_registered_or_possible( 'branding_tagline_below' ) ) {
      czr_fn_render_template( 'header/parts/tagline' );
    }
  ?>
  </div>
</div>

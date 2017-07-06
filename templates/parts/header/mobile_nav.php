<div class="mobile-nav__container hidden-lg-up col-12">
   <nav class="mobile-nav__nav collapse navbar-collapse flex-column col" id="mobile-nav">
    <?php
      if ( czr_fn_has( 'nav_search' ) ) {
        czr_fn_render_template( 'header/mobile_search_container' );
      }
      if ( czr_fn_has('mobile_menu') ) {
        czr_fn_render_template( 'header/menu', array(
          'model_id'   =>  'mobile_menu',
        ) );
      };

    ?>
  </nav>
  <?php
    if ( czr_fn_get_property('with_nav_utils') && czr_fn_has('nav_utils') ) czr_fn_render_template( 'header/nav_utils' )
  ?>
</div>
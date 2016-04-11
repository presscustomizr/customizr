<div class="navbar-wrapper clearfix <?php tc_echo( 'element_class' ) ?>" <?php tc_echo('element_attributes') ?>>
  <div class="navbar resp">
    <div class="navbar-inner" role="navigation">
        <div class="row-fluid">
        <?php
          if ( ! is_rtl() ) {
            if ( tc_has('header_social_block') )
              tc_render_template('modules/social_block', 'header_social_block');
            tc_render_template('header/tagline');
          } else {
            tc_render_template('header/tagline');
            if ( tc_has('header_social_block') )
              tc_render_template('modules/social_block', 'header_social_block');
          }


          if ( tc_has('navbar_menu') )
            tc_render_template('header/menu', 'navbar_menu');

          if ( tc_has('navbar_secondary_menu') )
            tc_render_template('header/menu', 'navbar_secondary_menu');

          if ( tc_has('mobile_menu_button') )
            tc_render_template('header/menu_button', 'mobile_menu_button');

          if ( tc_has('sidenav_navbar_menu_button') )
            tc_render_template('header/menu_button', 'sidenav_navbar_menu_button');
          //do_action( '__navbar__' ); //hook of social, tagline, menu, ordered by priorities 10, 20, 20
        ?>
        </div><!-- /.row-fluid -->
    </div><!-- /.navbar-inner -->
  </div><!-- /.navbar  -->
</div>

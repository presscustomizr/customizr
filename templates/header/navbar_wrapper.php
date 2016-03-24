<div class="navbar-wrapper clearfix <?php tc_echo( 'element_class' ) ?>" <?php tc_echo('element_attributes') ?>>
  <div class="navbar resp">
    <div class="navbar-inner" role="navigation">
        <div class="row-fluid">
        <?php
            do_action( '__navbar__' ); //hook of social, tagline, menu, ordered by priorities 10, 20, 20
        ?>
        </div><!-- /.row-fluid -->
    </div><!-- /.navbar-inner -->
  </div><!-- /.navbar  -->
</div>

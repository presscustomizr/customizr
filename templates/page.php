<?php

add_action( '__page__', 'tc_page_loop' );
add_action( '__before_content', 'tc_page_title' );
function tc_page_loop(){
 if ( is_page() && have_posts() ) {
    while ( have_posts() ) {
      the_post();
      tc_page_content();  
    }
 }
}
function tc_page_content() {
  ob_start();
    do_action( '__before_content' );
    ?>

    <div class="entry-content">
      <?php
        the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>' , 'customizr' ) );
        wp_link_pages( array(
            'before'        => '<div class="btn-toolbar page-links"><div class="btn-group">' . __( 'Pages:' , 'customizr' ),
            'after'         => '</div></div>',
            'link_before'   => '<button class="btn btn-small">',
            'link_after'    => '</button>',
            'separator'     => '',
        )
                );
      ?>
    </div>

    <?php
    do_action( '__after_content' );
  $html = ob_get_contents();
  if ($html) ob_end_clean();
  echo apply_filters( 'tc_page_content', $html );
}
function tc_page_title(){
  echo '<h1>' . get_the_title() . '</h1>';
}
?>
<section>
  <h1>I AM THE TEMPLATE OF THE VIEW <span style="color:blue"><?php echo $page_model -> id ?></span></h1>
  <p style="text-align:center"><strong style="font-size:50px;font-family:arial">TEST PAGE CONTENT</strong></p>
  <?php do_action( '__page__'); ?>
</section>

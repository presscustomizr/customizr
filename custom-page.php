<?php
/*
Template Name: Custom Page Example
*/

get_header();
    do_action( '__fp_block' );
    do_action( '__breadcrumb' );
      ?>
        <div class="container" role="main">
            <div class="row">
                <?php
                    do_action( '__sidebar' , 'left' );
			        	do_action( '__loop' );
                    do_action( '__sidebar' , 'right' );
                ?>
            </div><!--#row -->
        </div><!-- #container -->
    <?php
get_footer();
?>
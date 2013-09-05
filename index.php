<?php
/**
 * The main template file.
 *
 *
 * @package Customizr
 * @since Customizr 1.0
 */
get_header();

    do_action( '__fp_block' );

    do_action( '__breadcrumb' );
      ?>
        <div class="container" role="main">

            <div class="row">

                <?php if ( !tc__f( '__is_home_empty')) : ?>
                    <?php 
                        do_action( '__sidebar' , 'left' );

			        	    do_action( '__loop' );

                        do_action( '__sidebar' , 'right' );
                    ?>
                <?php endif; ?>

            </div><!--#row -->

        </div><!-- #container -->
    <?php

get_footer();
?>
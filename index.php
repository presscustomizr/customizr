<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 *
 *
 * @package Customizr
 * @since Twenty Customizr 3.5
 */
?>
    <?php get_header() ?>
        <?php tc_render_template('content', 'main_content'); ?>
    <?php get_footer() ?>

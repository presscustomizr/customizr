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

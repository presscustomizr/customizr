<?php
/**
 * Custom template tags for Customizr
 *
 *
 * @package Customizr
 */

if ( ! function_exists( 'czr_fn_carousel_nav' ) ) :
/* The template display the carousel nav */

function czr_fn_carousel_nav() {
      ?>
      <div class="czr-carousel-nav">
        <span class="czr-carousel-control btn btn-skin-darkest-shaded inverted czr-carousel-prev icn-left-open-big" tabindex="0"></span>
        <span class="czr-carousel-control btn btn-skin-darkest-shaded inverted czr-carousel-next icn-right-open-big" tabindex="0"></span>
      </div>
      <?php
}
endif;



if ( ! function_exists( 'czr_fn_comment_info' ) ) :

/* The template display the comment info */
function czr_fn_comment_info( $before = '', $after = '' ) {

      $_allow_comment_info = (bool) esc_attr( czr_fn_get_opt( 'tc_comment_show_bubble' ) ) && (bool) esc_attr( czr_fn_get_opt( 'tc_show_comment_list' ) );

      if ( ! $_allow_comment_info )
            return;

      if ( ! ( get_comments_number() > 0 && czr_fn_is_possible( 'comment_list' ) &&
              in_array( get_post_type(), apply_filters('czr_show_comment_infos_for_post_types' , array( 'post' , 'page') ) ) ) )
            return;

      $link            = sprintf( "%s%s",
            is_singular() ? '' : esc_url( get_permalink() ),
            //Filter hook used by disqus plugin
            apply_filters( 'czr_comment_info_anchor', '#czr-comments-title')
      );

      //Filter hook used by disqus plugin
      $link_attributes = esc_attr( apply_filters( 'czr_comment_info_link_attributes', '' ) );

      if ( $before )
        echo $before;
      ?>
      <a
      class="comments__link <?php czr_fn_echo( 'element_class' ) ?>" href="<?php echo $link ?>" title="<?php echo get_comments_number() ?> <?php _e( 'Comment(s) on', 'customizr') ?> <?php echo esc_attr( strip_tags( get_the_title() ) ) ?>" <?php echo $link_attributes ?>><span
      ><?php
      echo number_format_i18n( get_comments_number() ) . ' ' . _n( 'comment' , 'comments' , get_comments_number(), 'customizr' ) ?></span></a>
      <?php

      if ( $after )
        echo $after;
}
endif;



if ( ! function_exists( 'czr_fn_post_action' ) ) :

/**
 * The template for displaying the post action button
 * generally shown on thumb hover, will open the lightbox
 */
function czr_fn_post_action( $link, $class ) {

      if ( !$link )
            return;

      $icon     = 'icn-expand';
      $class    = $class ? $class . ' ' . $icon : $icon;

      ?>
      <div class="post-action btn btn-skin-darkest-shaded inverted">
        <a href="<?php echo esc_url( $link ) ?>" class="<?php esc_attr_e( $class ) ?>"></a>
      </div>
      <?php

}
endif;



if ( ! function_exists( 'czr_fn_readmore_button' ) ) :
/**
 * The template for displaying the read more button
 * generally appended to the excerpt_more
 *
 */
function czr_fn_readmore_button( $args = array() ) {
      $defaults = array(
            'class' => '',
            'link'  => get_permalink(),
            'title' => the_title_attribute( array( 'before' => __('Permalink to:&nbsp;', 'customizr'), 'echo' => false ) ),
            'text'  => __('Read more &raquo;', 'customizr' ),
            'echo'  => false,
      );

      $args             = wp_parse_args( $args, $defaults );

      $args[ 'class' ]  = $args[ 'class' ] ? $args[ 'class' ] . ' readmore-holder' : 'readmore-holder';

      $readmore_button = sprintf( '<span class="%1$s"><a class="moretag btn btn-more btn-skin-darkest" href="%2$s" title="%3$s">%4$s</a></span>',
            esc_attr( $args[ 'class' ] ),
            esc_url( $args[ 'link' ] ),
            esc_attr( $args[ 'title' ] ),
            $args[ 'text' ]
      );

      if ( !$args[ 'echo' ] )
        return $readmore_button;

      echo $readmore_button;

}
endif;



if ( ! function_exists( 'czr_fn_edit_button' ) ) :
/**
 * The template for displaying the edit button
 * Used everywhere from the slider to the posts to the comment
 *
 */
function czr_fn_edit_button( $args = array() ) {
      /*
      * No edit buttons if user is not logged in or is customizing
      * Other conditions are checked by the caller
      */
      if ( !is_user_logged_in() || czr_fn_is_customizing() )
        return;

      $defaults = array(
        'class' => '',
        'title' => __( 'Edit', 'customizr' ),
        'text'  => __( 'Edit', 'customizr' ),
        'link'  => '#',
      );

      $args             = wp_parse_args( $args, $defaults );

      $args[ 'class' ]  = $args[ 'class' ] ? $args[ 'class' ] . ' btn btn-edit' : 'btn btn-edit';

      ?>
      <a class="<?php esc_attr_e( $args[ 'class' ] ) ?>"
         title="<?php esc_attr_e( $args[ 'title' ] ) ?>"
         href="<?php echo esc_url( $args[ 'link' ] )?>" target="_blank" rel="nofollow">
         <i class="icn-edit"></i><?php echo $args[ 'text' ] ?>
      </a>
      <?php

}
endif;

if ( ! function_exists( 'czr_fn_link_pages' ) ) :
/**
 * The template for displaying the edit button
 * Used everywhere from the slider to the posts to the comment
 *
 */
function czr_fn_link_pages( $echo = true ) {

      wp_link_pages( array(
            'before'        => '<div class="post-pagination pagination row"><div class="col-md-12">',
            'after'         => '</div></div>',
            'link_before'   => '<span>',
            'link_after'    => '</span>',
            'echo'          => $echo
      ));
}

endif;
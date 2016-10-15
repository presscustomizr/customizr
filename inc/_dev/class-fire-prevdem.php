<?php

if ( ! class_exists( 'CZR_prevdem' ) ) :
  class CZR_prevdem {
    function __construct () {
      add_filter('czr_fn_has_thumb', '__return_true');
      add_filter('czr_fn_has_thumb_info', '__return_true');
      add_filter('tc_has_wp_thumb_image', '__return_true');
      add_filter('tc_thumb_html', array( $this, 'czr_fn_filter_thumb_src'), 10, 6 );
    }

    //@param img :array (url, width, height, is_intermediate), or false, if no image is available.
    function czr_fn_filter_thumb_src( $tc_thumb, $requested_size, $_post_id, $_custom_thumb_id, $_img_attr, $tc_thumb_size ) {
      $new_img_src = $this -> czr_fn_get_prevdem_img_src( $tc_thumb_size );

      /* if ( is_array( ) )
        array_walk_recursive( , function(&$v) { $v = htmlspecialchars($v); }); */
      ?>
        <pre>
          <?php print_r( $tc_thumb_size ); ?>
        </pre>
      <?php
      $_img_attr = is_array($_img_attr) ? $_img_attr : array();
      if ( false == $tc_thumb || empty( $tc_thumb ) ) {
        $tc_thumb = sprintf('<img src="%1$s" class="%2$s">',
          $new_img_src,
          isset($_img_attr['class']) ? $_img_attr['class'] : ''
        );
      } else {
        $regex = '#<img([^>]*) src="([^"/]*/?[^".]*\.[^"]*)"([^>]*)>#';
        $replace = "<img$1 src='$new_img_src'$3>";
        $tc_thumb = preg_replace($regex, $replace, $tc_thumb);
      }
      return $tc_thumb;
    }



    /* Placeholder thumb helper
    *  @return a random img src string
    *  Can be recursive if a specific img size is not found
    */
    function czr_fn_get_prevdem_img_src( $_size = 'tc-grid', $i = 0 ) {
        //prevent infinite loop
        if ( 10 == $i ) {
          return;
        }

        $sizes_suffix_map = array(
            'tc-thumb'     => '270x250',
            'tc-grid-full'    => '1170x350',
            'tc-grid'  => '570x350'
        );
        $requested_size = isset( $sizes_suffix_map[$_size] ) ? $sizes_suffix_map[$_size] : '570x350';
        $path = TC_BASE . 'assets/img/demo/';
        /* if ( is_array() )
          array_walk_recursive(, function(&$v) { $v = htmlspecialchars($v); }); */
        ?>
          <pre>
            <?php print_r($path); ?>
          </pre>
        <?php
        //Build or re-build the global dem img array
        if ( ! isset( $GLOBALS['prevdem_img'] ) || empty( $GLOBALS['prevdem_img'] ) ) {
            $imgs = array();
            if ( is_dir( $path ) ) {
              $imgs = scandir( $path );
            }
             ?>
          <pre>
            <?php print_r($imgs); ?>
          </pre>
        <?php
            $candidates = array();
            if ( ! $imgs || empty( $imgs ) )
              return array();

            foreach ( $imgs as $img ) {
              if ( '.' === $img[0] || is_dir( $path . $img ) ) {
                continue;
              }
              $candidates[] = $img;
            }
            $GLOBALS['prevdem_img'] = $candidates;
        }
        $candidates = $GLOBALS['prevdem_img'];
        //get a random image name
        $rand_key = array_rand($candidates);
        $img_name = $candidates[ $rand_key ];
        //extract img prefix
        $img_prefix_expl = explode( '-', $img_name );
        $img_prefix = $img_prefix_expl[0];

        $requested_size_img_name = "{$img_prefix}-{$requested_size}.jpg";
        //if file does not exists, reset the global and recursively call it again
        if ( ! file_exists( $path . $requested_size_img_name ) ) {
          unset( $GLOBALS['prevdem_img'] );
          $i++;
          return hu_get_prevdem_img_src( $_size, $i );
        }
        //unset all sizes of the img found and update the global
        $new_candidates = $candidates;
        foreach ( $candidates as $_key => $_img ) {
          if ( substr( $_img , 0, strlen( "{$img_prefix}-" ) ) == "{$img_prefix}-" ) {
            unset( $new_candidates[$_key] );
          }
        }
        $GLOBALS['prevdem_img'] = $new_candidates;
        return get_template_directory_uri() . '/assets/front/img/demo/' . $requested_size_img_name;
    }




  }//end of class
endif;

?>
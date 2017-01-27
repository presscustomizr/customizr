<?php
class CZR_grid_wrapper_model_class extends CZR_Model {
  public $expanded_sticky;

  private $queried_id;

  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model preset array()
  */
  function czr_fn_get_preset_model() {
    $_preset = array(
      'grid_columns'          => esc_attr( czr_fn_get_opt( 'tc_grid_columns') ),
      'grid_title_num_words'  => esc_attr( czr_fn_get_opt( 'tc_grid_num_words') ),
      'grid_icons'            => esc_attr( czr_fn_get_opt( 'tc_grid_icons') ),
      'grid_expand_featured'  => esc_attr( czr_fn_get_opt( 'tc_grid_expand_featured') ),
      'show_thumb'            => esc_attr( czr_fn_get_opt( 'tc_post_list_show_thumb' ) ),
      'grid_bottom_border'    => esc_attr( czr_fn_get_opt( 'tc_grid_bottom_border') ),
      'grid_shadow'           => esc_attr( czr_fn_get_opt( 'tc_grid_shadow') ),
      'grid_thumb_height'     => esc_attr( czr_fn_get_opt( 'tc_grid_thumb_height') ),
      'excerpt_length'        => esc_attr( czr_fn_get_opt( 'tc_post_list_excerpt_length' ) ),
      'contained'             => false
    );

    return $_preset;
  }

  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function czr_fn_extend_params( $model = array() ) {
    $this -> queried_id           = czr_fn_get_id();

    return parent::czr_fn_extend_params( $model );
  }

  /*
  * Fired just before the view is rendered
  * @hook: pre_rendering_view_{$this -> id}, 9999
  */
  /*
  * Each time this model view is rendered setup the current post list item
  * and add it to the post_list_items_array
  */
  function czr_fn_setup_late_properties() {
    //all post lists do this
    if ( czr_fn_is_loop_start() )
      $this -> czr_fn_setup_text_hooks();
  }

  /*
  * Fired just before the view is rendered
  * @hook: post_rendering_view_{$this -> id}, 9999
  */
  function czr_fn_reset_late_properties() {
    if ( czr_fn_is_loop_end() )
      //all post lists do this
      $this -> czr_fn_reset_text_hooks();
  }


  function czr_fn_get_is_first_of_row() {
    global $wp_query;

    $current_post      = $wp_query -> current_post;
    $start_post        = ! empty( $this -> expanded_sticky ) ? 1 : 0;
    $section_cols      = $this     -> czr_fn_get_section_cols();

    if ( $start_post == $current_post || 0 == ( $current_post - $start_post ) % $section_cols )
      return true;

    return false;
  }

  function czr_fn_get_is_last_of_row() {
    global $wp_query;

    $current_post      = $wp_query -> current_post;
    $start_post        = ! empty( $this -> expanded_sticky ) ? 1 : 0;
    $section_cols      = $this     -> czr_fn_get_section_cols();


    if ( $wp_query->post_count == ( $current_post + 1 ) || 0 == ( ( $current_post - $start_post + 1 ) % $section_cols ) )
      return true;

    return false;
  }



  /* retrieves number of cols option, and wrap it into a filter */
  private function czr_fn_get_grid_cols() {
    if ( ! isset( $this -> grid_cols ) )
      $grid_cols = $this -> czr_fn_set_grid_cols( $this -> grid_columns, czr_fn_get_layout( $this -> queried_id , 'class' ) );
    else
      $grid_cols = $this -> grid_cols;

    return apply_filters( 'czr_get_grid_cols', $grid_cols );
  }



  /* returns articles wrapper section columns */
  public function czr_fn_get_section_cols() {
    return apply_filters( 'czr_grid_section_cols',
      $this -> czr_fn_force_current_post_expansion() ? '1' : $this -> czr_fn_get_grid_cols()
    );
  }



  /*
  * @return bool
  * returns if the current post is the expanded one
  */
  private function czr_fn_force_current_post_expansion(){
    global $wp_query;
    $is_expanded = $this -> czr_fn_maybe_has_sticky_expanded() && 0 == $wp_query -> current_post && get_query_var( 'paged' ) < 2 && is_sticky() ;
    //set expanded sticky flag
    if ( ! isset( $this -> expanded_sticky ) )
      $this -> czr_fn_set_property( 'expanded_sticky', $is_expanded );
    return $is_expanded;
  }



  /******************************
  GRID ITEM SETUP
  *******************************/

  function czr_fn_get_grid_item() {
    $section_cols           = $this -> czr_fn_get_section_cols();

    $is_expanded            = $this -> czr_fn_force_current_post_expansion();

    //thumb
    $thumb_properties       = $this -> czr_fn_get_grid_item_thumb_properties( $section_cols );
    $has_thumb              = isset( $thumb_properties[ 'has_thumb' ] ) ? $thumb_properties[ 'has_thumb' ] : false;
    $thumb_img              = isset( $thumb_properties[ 'thumb_img' ] ) ? $thumb_properties[ 'thumb_img' ] : '';

    //figure class
    $figure_class           = $this -> czr_fn_get_grid_item_figure_class( $has_thumb, $section_cols );

    //array
    $icon_visibility        = $this -> czr_fn_get_grid_item_icon_visibility();

    $title                  = $this -> czr_fn_get_grid_item_title( get_the_title(), $is_expanded );

    $has_title_in_caption   = $this -> czr_fn_grid_item_has_title_in_caption( $is_expanded );

    $has_edit_in_caption    = $this -> czr_fn_grid_item_has_edit_in_caption( $is_expanded );

    $has_fade_expt          = $this -> czr_fn_grid_item_has_fade_expt( $is_expanded, $thumb_img );

    $article_selectors      = $this -> czr_fn_get_grid_item_article_selectors( $section_cols, $is_expanded );

    //update the model
    return array_merge(
        $icon_visibility,
        compact(
          'thumb_img',
          'figure_class',
          'is_expanded',
          'title',
          'has_title_in_caption',
          'has_fade_expt',
          'has_edit_in_caption',
          'section_cols',
          'article_selectors'
        )
    );
  }

  /*
  * has edit in caption
  */
  function czr_fn_grid_item_has_edit_in_caption( $is_expanded ) {
    return $is_expanded;
  }

  /*
  * has title in caption
  */
  function czr_fn_grid_item_has_title_in_caption( $is_expanded ) {
    return $is_expanded;
  }

  /*
  * has fade expt
  */
  function czr_fn_grid_item_has_fade_expt( $is_expanded, $thumb_img ) {
    return ! ( $is_expanded || $thumb_img );
  }


  /**
  * Limits the length of the post titles in grids to a custom number of characters
  * @return string
  */
  function czr_fn_get_grid_item_title( $_title, $is_expanded ) {
    $_max = $this -> grid_title_num_words;
    $_max = ( empty($_max) || ! $_max ) ? 10 : $_max;
    $_max = $_max <= 0 ? 1 : $_max;

    if ( empty($_title) || ! is_string($_title) )
      return $_title;

    if ( count( explode( ' ', $_title ) ) > $_max ) {
      $_words = array_slice( explode( ' ', $_title ), 0, $_max );
      $_title = sprintf( '%s ...',
        implode( ' ', $_words )
      );
    }
    return $_title;
  }


  /*
  * thumb properties
  */
  function czr_fn_get_grid_item_thumb_properties( $section_cols ) {
    $has_thumb           = $this -> czr_fn_show_thumb();
    $thumb_img           = '';

    if ( $has_thumb ) {
      $thumb_model                   = czr_fn_get_thumbnail_model(
          $thumb_size                = $this -> czr_fn_get_thumb_size_name( $section_cols ),
          null, null, null,
          $_filtered_thumb_size_name = $this -> czr_fn_get_filtered_thumb_size_name( $section_cols )
      );

      if ( ! isset( $thumb_model['tc_thumb'] ) )
        return;

      $thumb_img              = apply_filters( 'czr-grid-thumb-img', $thumb_model[ 'tc_thumb' ], czr_fn_get_id() );
    }

    return compact( 'has_thumb', 'thumb_img' );
  }


  /*
  * figure class
  */
  function czr_fn_get_grid_item_figure_class( $has_thumb, $section_cols ) {
    $figure_class        = array( $has_thumb ? 'has-thumb' : 'no-thumb' );

    //if 1 col layout or current post is the expanded => golden ratio should be disabled
    if ( ( '1' == $section_cols ) && ! wp_is_mobile() )
      array_push( $figure_class, 'no-gold-ratio' );
    return $figure_class;
  }



  /*
  * grid icon visibility
  * @return array
  */
  function czr_fn_get_grid_item_icon_visibility() {
    $icon_enabled        = (bool) $this -> grid_icons && in_array( get_post_format(), array( 'link', 'quote', 'image' ) );
    $icon_attributes     = '';

    if ( czr_fn_is_customizing() )
      $icon_attributes   = sprintf('style="display:%1$s"',
          $icon_enabled ? 'inline-block' : 'none'
      );
    return compact( 'icon_enabled', 'icon_attributes' );
  }

  function czr_fn_get_grid_item_article_selectors( $section_cols, $is_expanded ) {
    $post_class = sprintf( '%1$s tc-grid col-xs-12 col-md-%2$s',
      apply_filters( 'czr_grid_add_expanded_class', $is_expanded ) ? 'expanded' : '',
      is_numeric( $section_cols ) ? 12 / $section_cols : 6
    );

    $id_suffix               = is_main_query() ? '' : "_{$this -> id}";
    return czr_fn_get_the_post_list_article_selectors( $post_class, $id_suffix );

  }

  /**** HELPER ****/

  /**
  * @return  boolean
  */
  /*
  * get the thumb size name to use according to the grid element width
  */
  function czr_fn_get_thumb_size_name( $section_cols ){
    return ( 1 == $section_cols ) ? 'tc-grid-full' : 'tc-grid';
  }


  /*
  * get the thumb size name to set the proper inline style
  * if needed, accordint to the grid element width
  */
  function czr_fn_get_filtered_thumb_size_name( $section_cols ){
    return ( 1 == $section_cols ) ? 'tc_grid_full_size' : 'tc_grid_size';
  }

  private function czr_fn_show_thumb() {
    return 0 != $this -> show_thumb && czr_fn_has_thumb();
  }

  /******************************
  VARIOUS HELPERS
  *******************************/
  /*
  * @return bool
  * check if we have to expand the first sticky post
  */
  private function czr_fn_maybe_has_sticky_expanded(){
    global $wp_query;

    if ( ! $wp_query -> is_main_query() )
      return false;
    if ( ! ( ( is_home() && 'posts' == get_option('show_on_front') ) ||
        $wp_query -> is_posts_page ) )
      return false;

    return apply_filters( 'czr_grid_expand_featured', $this -> grid_expand_featured );
  }


  /**
  * add custom classes to the grid container element
  */
  function czr_fn_get_element_class() {
    $_classes = array();
    if ( ! empty( $this->grid_shadow ) )
      array_push( $_classes, 'tc-grid-shadow' );
    if ( ! empty( $this->grid_bottom_border ) )
      array_push( $_classes, 'tc-grid-border' );
    if ( ! empty( $this->contained ) )
      array_push( $_classes, 'container' );
    return $_classes;
  }



  /**
  * Grid columns = fn(current-layout)
  * Returns the max possible grid column number for a given layout
  *
  * @param $_col_nb = string possible values : 1, 2, 3, 4
  * @param $_current_layout string of layout class like span4
  */
  function czr_fn_set_grid_cols( $_col_nb, $_current_layout ) {
    /* TO FIX and extend considering that we can set different col widths for different
    * viewports, also we could find a way, I'm pretty sure, to avoid the use of the row wrapper
    * putting some element (or with :before :after pseudo-elements) which we can control with
    * CSS classes, e.g.
    * <hr class="col-sm-12 md-hidden"> after a certain grid-item given a certain layout
    */
    preg_match("/(col-md-[0-9]+)/", $_current_layout, $current_md_layout);
    $_current_layout = ! empty( $current_md_layout[0] ) ? $current_md_layout[0] : 'col-md-12';

    $_map = apply_filters(
        'tc_grid_col_layout_map',
        array(
          'col-md-12'  => '3',//no sidebars
          'col-md-11'  => '3',
          'col-md-10'  => '3',
          'col-md-9'   => '3',//one sidebar right or left
          'col-md-8'   => '3',
          'col-md-7'   => '2',
          'col-md-6'   => '2',//two sidebars
          'col-md-5'   => '2',
          'col-md-4'   => '1',
          'col-md-3'   => '1',
          'col-md-2'   => '1',
          'col-md-1'   => '1',
        )
    );

    if ( ! isset($_map[$_current_layout]) )
      return $_col_nb;
    if ( (int) $_map[$_current_layout] >= (int) $_col_nb )
      return (string) $_col_nb;
    return (string) $_map[$_current_layout];
  }



  /**
  * @param (string) $col_layout
  * @return string
  *
  */
  private function czr_fn_get_grid_column_height( $_cols_nb = '3' ) {
    $_h               = $this -> czr_fn_grid_get_thumb_height();
    $_current_layout  = czr_fn_get_layout( $this -> queried_id , 'sidebar' );
    $_layouts         = array('b', 'l', 'r' , 'f');//both, left, right, full (no sidebar)
    $_key             = 3;//default value == full
    if ( in_array( $_current_layout, $_layouts ) )
      //get the key = position of requested size in the current layout
      $_key = array_search( $_current_layout , $_layouts );
    $_grid_col_height_map =  apply_filters(
        'tc_grid_col_height_map',
        array(        // 'b'  'l'  'r'  'f'
          '1' => array( 225 , 225, 225, $_h ),
          '2' => array( 225 , $_h, $_h, $_h ),
          '3' => array( 225 , 225, 225, 225 ),
          '4' => array( 165 , 165, 165, 165 )
        )
    );
    //are we ok ?
    if ( ! isset( $_grid_col_height_map[$_cols_nb] ) )
      return $_h;
    //parse the array to ensure that all values are <= user height
    foreach ( $_grid_col_height_map as $_c => $_heights ) {
      $_grid_col_height_map[$_c] = $this -> czr_fn_set_max_col_height ( $_heights ,$_h );
    }
    $_h = isset( $_grid_col_height_map[$_cols_nb][$_key] ) ? $_grid_col_height_map[$_cols_nb][$_key] : $_h;
    return apply_filters( 'czr_get_grid_column_height' , $_h, $_cols_nb, $_current_layout );
  }



  /**
  * parse the array to ensure that all values are <= user height
  * @param (array) grid_col_height_map
  * @param  (num) user defined max height in pixel
  * @return string
  *
  */
  private function czr_fn_set_max_col_height( $_heights ,$_h ) {
    $_return = array();
    foreach ($_heights as $_value) {
      $_return[] = $_value >= $_h ? $_h : $_value;
    }
    return $_return;
  }




  /******************************
  * HELPERS FOR INLINE CSS
  *******************************/
  /**
  * @param (string) $col_layout
  * @return css media query string
  * Returns the paragraph and title media queries for a given layout
  */
  private function czr_fn_get_grid_font_css( $_col_nb = '3' ) {
    $_media_queries     = $this -> czr_fn_get_grid_media_queries();//returns the simple array of media queries
    $_grid_font_sizes = $this -> czr_fn_get_grid_font_sizes( $_col_nb );//return the array of sizes (ordered by @media queries) for a given column layout
    $_col_rules         = array();
    $_media_queries_css = '';
    //flatten the matrix
    foreach ($_media_queries as $key => $_med_query_sizes ) {
      $_size = $_grid_font_sizes[$key];//=> size like 'xxl'
      $_css_prop = array(
        'h' => $this -> czr_fn_grid_build_css_rules( $_size , 'h' ),
        'p' => $this -> czr_fn_grid_build_css_rules( $_size , 'p' )
      );

      $_rules = $this -> czr_fn_grid_assign_css_rules_to_selectors( $_med_query_sizes , $_css_prop, $_col_nb );
      $_media_queries_css .= "
        @media {$_med_query_sizes} {{$_rules}}
      ";
    }
    return $_media_queries_css;
  }

  /* To port in bootstrap 4 */
  /**
  * @return simple array of media queries
  */
  private function czr_fn_get_grid_media_queries() {
    return apply_filters( 'czr_grid_media_queries' ,  array(
             '(min-width: 1200px)', '(max-width: 1199px) and (min-width: 980px)', '(max-width: 979px) and (min-width: 768px)', '(max-width: 767px)', '(max-width: 480px)'
           ));
  }


  /**
  * Return the array of sizes (ordered by @media queries) for a given column layout
  * @param  $_col_nb string
  * @param  $_requested_media_size
  * @return array()
  * Note : When all sizes are requested (default case), the returned array can be filtered with the current layout param
  * Size array must have the same length of the media query array
  */
  private function czr_fn_get_grid_font_sizes( $_col_nb = '3', $_requested_media_size = null ) {
    $_col_media_matrix = apply_filters( 'czr_grid_font_matrix' , array(
      //=> matrix col nb / media queries
      //            1200 | 1199-980 | 979-768 | 767   | 480
     '1' => array( 'xxxl', 'xxl'   , 'xl'    , 'xl'  , 'l' ),
     '2' => array( 'xxl' , 'xl'    , 'l'     , 'xl'  , 'l' ),
     '3' => array( 'xl'  , 'l'     , 'm'     , 'xl'  , 'l' ),
     '4' => array( 'l'   , 'm'     , 's'     , 'xl'  , 'l' )
     ));

    //if a specific media query is requested, return a string
    if ( ! is_null($_requested_media_size) ) {
      $_media_queries = $this -> czr_fn_get_grid_media_queries();
      //get the key = position of requested size in the current layout
      $_key = array_search( $_requested_media_size, $_media_queries);
      return apply_filters(
        'czr_fn_get_layout_single_font_size',
        isset($_col_media_matrix[$_col_nb][$_key]) ? $_col_media_matrix[$_col_nb][$_key] : 'xl'
      );
    }
    return apply_filters(
      'czr_get_grid_font_sizes',
      isset($_col_media_matrix[$_col_nb]) ? $_col_media_matrix[$_col_nb] : array( 'xl' , 'l' , 'm', 'l', 'm' ),
      $_col_nb,
      $_col_media_matrix,
      czr_fn_get_layout( $this -> queried_id , 'class' )
    );
  }

  /**
  * hook : 'czr_get_grid_font_sizes'
  * Updates the array of sizes for a given sidebar layout
  * @param  $_sizes array. ex : array( 'xl' , 'l' , 'm', 'l', 'm' )
  * @param  $_col_nb string. Ex: '2'
  * @param  $_col_media_matrix : array() matrix 5 x 4 => media queries / Col_nb
  * @param  $_current_layout string. Ex : 'col-md-9'
  * @return array()
  */
  function czr_fn_set_layout_font_size( $_sizes, $_col_nb, $_col_media_matrix, $_current_layout ) {
    //max possible font size key in the col_media_queries matrix for a given sidebar layout
    $_map = apply_filters(
      'tc_layout_font_size_map',
      array(
        'col-md-12'  => '1',//no sidebars
        'col-md-11'  => '1',
        'col-md-10'  => '1',
        'col-md-9'   => '2',//one sidebar right or left
        'col-md-8'   => '2',
        'col-md-7'   => '3',
        'col-md-6'   => '4',//two sidebars
        'col-md-5'   => '4',
        'col-md-4'   => '4',
        'col-md-3'   => '4',
        'col-md-2'   => '4',
        'col-md-1'   => '4',
      )
    );
    if ( ! isset($_map[$_current_layout]) )
      return $_sizes;
    if ( (int) $_col_nb >= (int) $_map[$_current_layout] )
      return $_sizes;
    $_new_key = $_map[$_current_layout];
    return $_col_media_matrix[$_new_key];
  }



  /**
  * @return css string
  * @param size string
  * @param selector type string
  * returns ratio of size / body size for a given selector type ( headings or paragraphs )
  */
  private function czr_fn_get_grid_font_ratios( $_size = 'xl' , $_sel = 'h' ) {
    $_ratios =  apply_filters( 'czr_get_grid_font_ratios' , array(
        'xxxl' => array( 'h' => 2.10, 'p' => 1 ),
        'xxl' => array( 'h' => 1.86, 'p' => 1 ),
        'xl' => array( 'h' => 1.60, 'p' => 0.93 ),
        'l' => array( 'h' => 1.30, 'p' => 0.85 ),
        'm' => array( 'h' => 1.15, 'p' => 0.80 ),
        's' => array( 'h' => 1.0, 'p' => 0.75 )
    ));

    if ( isset($_ratios[$_size]) && isset($_ratios[$_size][$_sel]) )
      return $_ratios[$_size][$_sel];
    return 1;
  }



  /**
  * @return css string
  * @param $_media_query = string of current media query.
  * @param $_css_prop = array of css rules for paragraph and titles for a given column layout
  * @param $_col_nb = current column layout
  * Assigns css rules to predefined grid selectors for headings and paragraphs
  * adds the '1' column css if (OR) :
  * 1) there's a sticky post
  * 2) user layout is one column
  */
  private function czr_fn_grid_assign_css_rules_to_selectors( $_media_query, $_css_prop, $_col_nb ) {
    $_css = '';
    //Add one column font rules if there's a sticky post
    if ( $this -> czr_fn_maybe_has_sticky_expanded() || '1' == $_col_nb ) {
      $_size      = $this -> czr_fn_get_grid_font_sizes( $_col_nb = '1', $_media_query );//size like xxl
      $_h_one_col = $this -> czr_fn_grid_build_css_rules( $_size , 'h' );
      $_p_one_col = $this -> czr_fn_grid_build_css_rules( $_size , 'p' );
      $_css .= "
          .tc-post-list-grid .grid-cols-1 .entry-title {{$_h_one_col}}
          .tc-post-list-grid .grid-cols-1 .tc-g-cont {{$_p_one_col}}
      ";
    }
    $_h = $_css_prop['h'];
    $_p = $_css_prop['p'];
    $_css .= "
        .tc-post-list-grid article .entry-title {{$_h}}
        .tc-post-list-grid .tc-g-cont {{$_p}}
    ";
    return $_css;
  }

  /**
  * @return css string
  * @param column layout (string)
  * adds the one column css if (OR) :
  * 1) there's a sticky post
  * 2) user layout is one column
  */
  private function czr_fn_grid_get_figure_css( $_col_nb = '3' ) {
    $_height = $this -> czr_fn_get_grid_column_height( $_col_nb );
    $_cols_class      = sprintf( 'grid-cols-%s' , $_col_nb );
    $_css = '';
    //Add one column height if there's a sticky post
    if ( $this -> czr_fn_maybe_has_sticky_expanded() && '1' != $_col_nb ) {
      $_height_col_one = $this -> czr_fn_get_grid_column_height( '1' );
      $_css .= ".grid-cols-1 figure {
            height:{$_height_col_one}px;
            max-height:{$_height_col_one}px;
            line-height:{$_height_col_one}px;
      }";
    }
    $_css .= "
      .{$_cols_class} figure {
            height:{$_height}px;
            max-height:{$_height}px;
            line-height:{$_height}px;
      }";
    return $_css;
  }

  /**
  * @return string
  * @param size string
  * @param selector type string
  * returns the font-size and line-height css rules
  */
  private function czr_fn_grid_build_css_rules( $_size = 'xl', $_wot = 'h' ) {
    $_lh_ratio = apply_filters( 'czr_grid_line_height_ratio' , 1.5 ); //line-height / font-size
    $_ratio = $this -> czr_fn_get_grid_font_ratios( $_size , $_wot );
    //body font size
    $_bs = esc_attr( czr_fn_get_opt( 'tc_body_font_size') );
    $_bs = is_numeric($_bs) && 1 >= $_bs ? $_bs : 15;
    return sprintf( 'font-size:%spx;line-height:%sem;' ,
      ceil( $_bs * $_ratio ),
      ceil( $_ratio * $_lh_ratio )
    );
  }

  /**
  * @return (number) customizer user defined height for the grid thumbnails
  */
  private function czr_fn_grid_get_thumb_height() {
    $_opt = $this -> grid_thumb_height;
    return ( is_numeric($_opt) && $_opt > 1 ) ? $_opt : 350;
  }

  /**
  * @package Customizr
  * @since Customizr 4.0
  */
  function czr_fn_setup_text_hooks() {
    //filter the excerpt length
    add_filter( 'excerpt_length'        , array( $this , 'czr_fn_set_excerpt_length') , 999 );
  }


  /**
  * @package Customizr
  * @since Customizr 4.0
  */
  function czr_fn_reset_text_hooks() {
    remove_filter( 'excerpt_length'     , array( $this , 'czr_fn_set_excerpt_length') , 999 );
  }


  /**
  * hook : excerpt_length hook
  * @return string
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function czr_fn_set_excerpt_length( $length ) {
    $_custom = $this -> excerpt_length;
    return ( false === $_custom || !is_numeric($_custom) ) ? $length : $_custom;
  }


  /**
  * @return css string
  * hook : czr_fn_user_options_style
  * @since Customizr 3.2.18
  */
  function czr_fn_user_options_style_cb( $_css ){
    $_col_nb  = $this -> czr_fn_get_grid_cols();
    //GENERATE THE FIGURE HEIGHT CSS
    $_current_col_figure_css  = $this -> czr_fn_grid_get_figure_css( $_col_nb );
    //GENERATE THE MEDIA QUERY CSS FOR FONT-SIZES
    $_current_col_media_css   = $this -> czr_fn_get_grid_font_css( $_col_nb );
    $_css = sprintf("%s\n%s\n%s\n",
        $_css,
        $_current_col_media_css,
        $_current_col_figure_css
    );
    return $_css;
  }

}
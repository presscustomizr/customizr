<?php
class CZR_grid_wrapper_model_class extends CZR_Model {
  public $expanded_sticky;
  public $id_base = 'czr_grid';

  public $grid_item;

  protected $queried_id;

  function __construct( $model ) {
    parent::__construct( $model );
    $this -> element_id = uniqid("{$this->id_base}-");
  }
  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model preset array()
  */
  function czr_fn_get_preset_model() {

    $_preset = array(
      'grid_columns'             => esc_attr( czr_fn_opt( 'tc_grid_columns') ),
      'grid_title_num_words'     => esc_attr( czr_fn_opt( 'tc_grid_num_words') ),
      'grid_icons'               => esc_attr( czr_fn_opt( 'tc_grid_icons') ),
      'grid_expand_featured'     => esc_attr( czr_fn_opt( 'tc_grid_expand_featured') ),
      'show_thumb'               => esc_attr( czr_fn_opt( 'tc_post_list_show_thumb' ) ),
      'grid_bottom_border'       => esc_attr( czr_fn_opt( 'tc_grid_bottom_border') ),
      'grid_shadow'              => esc_attr( czr_fn_opt( 'tc_grid_shadow') ),
      'grid_hover_move'          => true,
      'grid_thumb_shape'         => esc_attr( czr_fn_opt( 'tc_grid_thumb_shape') ),
      'use_thumb_placeholder'    => esc_attr( czr_fn_opt( 'tc_post_list_thumb_placeholder' ) ),
      'excerpt_length'           => esc_attr( czr_fn_opt( 'tc_post_list_excerpt_length' ) ),
      'wrapped'                  => true,
      'masonry'                  => false,
      'contained'                => false,
      'title_in_caption_below'   => true,
      'content_wrapper_breadth'  => czr_fn_get_content_breadth(),
      'image_centering'          => 'js-centering',
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
    $model                              = parent::czr_fn_extend_params( $model );
    //layout dependency
    $model[ 'content_wrapper_breadth' ] = in_array( $model[ 'content_wrapper_breadth' ], array('full', 'semi-narrow', 'narrow' ) ) ? $model[ 'content_wrapper_breadth' ] : 'full';

    $this -> queried_id                 = czr_fn_get_id();

    //layout dependency
    return $model;
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


  function czr_fn_get_grid_section_class() {
    $section_class = array( 'grid__section', sprintf( "cols-%s", $this -> czr_fn_get_section_cols() ) );

    if ( $this -> czr_fn_is_sticky_expanded() )
      $section_class[] = 'clearfix grid-section-featured';
    else {
      if ( $this -> masonry ) {
        $section_class[] =  'masonry__wrapper';
      }
      $section_class[] =  'grid-section-not-featured';
    }
    return $section_class;
  }

  function czr_fn_get_print_start_wrapper() {
    return $this -> wrapped && czr_fn_is_loop_start();
  }

  function czr_fn_get_print_end_wrapper() {
    return $this -> wrapped && czr_fn_is_loop_end();
  }


  function czr_fn_get_is_first_of_grid() {
    global $wp_query;

    $current_post      = $wp_query -> current_post;
    $start_post        = !empty( $this -> expanded_sticky ) ? 1 : 0;

    if ( $start_post == $current_post )
      return $this -> wrapped;

    return false;
  }

  function czr_fn_get_is_last_of_grid() {
    global $wp_query;

    $current_post      = $wp_query -> current_post;

    if ( $wp_query->post_count == $current_post + 1 || ( $this -> expanded_sticky && $current_post == 0 ) )
      return $this -> wrapped;

    return false;
  }



  /* retrieves number of cols option, and wrap it into a filter */
  function czr_fn_get_grid_cols() {
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
  protected function czr_fn_force_current_post_expansion(){
    $is_expanded = $this->czr_fn_is_sticky_expanded();

    //set expanded sticky flag
    if ( ! isset( $this -> expanded_sticky ) )
      $this -> czr_fn_set_property( 'expanded_sticky', $is_expanded );
    return $is_expanded;
  }

  protected function czr_fn_is_sticky_expanded() {
    global $wp_query;
    return $this -> czr_fn_maybe_has_sticky_expanded() && 0 == $wp_query -> current_post && get_query_var( 'paged' ) < 2 && is_sticky();
  }


  /******************************
  GRID ITEM SETUP
  *******************************/

  function czr_fn_get_grid_item() {
    $section_cols           = $this -> czr_fn_get_section_cols();

    $is_expanded            = $this -> czr_fn_force_current_post_expansion();

    $text                   = $this -> czr_fn_get_grid_item_text();

    //thumb
    $thumb_properties       = $this -> czr_fn_get_grid_item_thumb_properties( $section_cols );
    $has_thumb              = isset( $thumb_properties[ 'has_thumb' ] ) ? $thumb_properties[ 'has_thumb' ] : false;
    $thumb_img              = isset( $thumb_properties[ 'thumb_img' ] ) ? $thumb_properties[ 'thumb_img' ] : '';

    //figure class
    $figure_class           = $this -> czr_fn_get_grid_item_figure_class( $has_thumb, $section_cols, $is_expanded );

    //array
    $icon_visibility        = $this -> czr_fn_get_grid_item_icon_visibility();

    $title                  = $this -> czr_fn_get_grid_item_title( get_the_title(), $is_expanded );

    $has_title_in_caption   = $this -> czr_fn_grid_item_has_title_in_caption( $is_expanded );
    $title_in_caption_below = $this -> title_in_caption_below;

    $has_edit_above_thumb   = $this -> czr_fn_grid_item_has_edit_above_thumb( $is_expanded );

    $has_fade_expt          = $this -> czr_fn_grid_item_has_fade_expt( $is_expanded, $thumb_img );

    $article_selectors      = $this -> czr_fn_get_grid_item_article_selectors( $section_cols, $is_expanded );

    $use_thumb_placeholder  = $this -> use_thumb_placeholder;


    //various depending on whether is expanded
    $entry_summary_class    = $this -> czr_fn_get_grid_item_entry_summary_class($is_expanded);
    $gcont_class            = $this -> czr_fn_get_grid_item_gcont_class($is_expanded);


    //update the model
    return array_merge(
        $icon_visibility,
        compact(
          'thumb_img',
          'figure_class',
          'is_expanded',
          'title',
          'has_title_in_caption',
          'title_in_caption_below',
          'has_fade_expt',
          'has_edit_above_thumb',
          'section_cols',
          'article_selectors',
          'use_thumb_placeholder',
          'gcont_class',
          'entry_summary_class',
          'text'
        )
    );
  }

  function czr_fn_get_grid_item_entry_summary_class( $is_expanded ) {
    return $is_expanded ? 'czr-talign' : '';
  }

  function czr_fn_get_grid_item_gcont_class( $is_expanded ) {
    return ! $is_expanded ? 'czr-talign' : '';
  }

  function czr_fn_get_grid_item_text() {
    $_the_excerpt = get_the_excerpt();
    return $_the_excerpt ? sprintf( '<p>%s</p>', $_the_excerpt ) : '';
  }

  /*
  * has edit in caption
  */
  function czr_fn_grid_item_has_edit_above_thumb( $is_expanded ) {
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
  * Limits the length of the post titles in grids to a custom number of words
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
        $_title = sprintf( '%s &hellip;',
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
          $thumb_model                   = czr_fn_get_thumbnail_model( array(
              'requested_size'              => $this -> czr_fn_get_thumb_size_name( $section_cols ),
              'filtered_thumb_size_name'    => $this -> czr_fn_get_filtered_thumb_size_name( $section_cols ),
              'placeholder'                 => $this -> use_thumb_placeholder
          ));

        if ( ! isset( $thumb_model['tc_thumb'] ) )
          return;

        $thumb_img              = apply_filters( 'czr-grid-thumb-img', $thumb_model[ 'tc_thumb' ], czr_fn_get_id() );
      }

      return compact( 'has_thumb', 'thumb_img' );
  }


  /*
  * figure class
  */
  function czr_fn_get_grid_item_figure_class( $has_thumb, $section_cols, $is_expanded ) {
      $figure_class        = array( $has_thumb ? 'has-thumb' : 'no-thumb' );

      //if current post is the expanded => golden ratio should be disabled
      //add the aspect ratio class for the figure
      $figure_class[]     = $this -> czr_fn_get_grid_figure_aspect_ratio_class( $section_cols );
      $figure_class[]     = 'js-centering' == $this -> image_centering ?'js-centering' :  'no-js-centering';

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
    if ( apply_filters( 'czr_grid_add_expanded_class', $is_expanded ) )
      $post_class = 'col-12 expanded grid-item';
    else {
      $cols       = $this -> _build_cols($section_cols ? $section_cols : 2 );
      $post_class = sprintf( 'grid-item col-12 %1$s',
                              implode( ' ', $cols )
                    );
    }
    $id_suffix               = is_main_query() ? '' : "_{$this -> id}";
    return czr_fn_get_the_post_list_article_selectors( $post_class, $id_suffix );

  }

  function _build_cols( $section_cols ) {

    $cols = array ();

    if ( $section_cols > 1 ) {
      array_push( $cols,
        "col-md-6"
      );

      if ( $section_cols > 2 ) {
        $_cols = 12/$section_cols;
        array_push( $cols,
          "col-xl-{$_cols}"
        );

        if ( $section_cols > 3 ) {
          $section_cols = $section_cols-1;
          $_cols = 12/$section_cols;
          array_push( $cols,
            "col-lg-{$_cols}"
          );
        }else {
          $_cols = 12/$section_cols;
          array_push( $cols,
            "col-lg-{$_cols}"
          );
        }
      }
    }

    return array_filter( array_unique( $cols ) );

  }

  /**** HELPER ****/

  /**
  * @return  boolean
  */
  /*
  * get the thumb size name to use according to the grid element width
  */
  function czr_fn_get_thumb_size_name( $section_cols ){
    //layout dependency
    return ( 1 == $section_cols && 'narrow' != $this->content_wrapper_breadth ) ? 'tc-grid-full' : 'tc-grid';
  }

  /**
  * @return  boolean
  */
  /*
  * get the figure aspect ratio
  */
  function czr_fn_get_grid_figure_aspect_ratio_class( $section_cols ){
    //layout dependency
    return ( 1 == $section_cols && 'narrow' != $this->content_wrapper_breadth ) ? 'czr__r-wGOC' : 'czr__r-wGR';
  }

  /*
  * get the thumb size name to set the proper inline style
  * if needed, according to the grid element width
  */
  function czr_fn_get_filtered_thumb_size_name( $section_cols ){
    return ( 1 == $section_cols ) ? 'tc_ws_thumb_size' : 'tc_grid_size';
  }


  protected function czr_fn_show_thumb() {
    return 0 != $this -> show_thumb;
  }

  /******************************
  VARIOUS HELPERS
  *******************************/
  /*
  * @return bool
  * check if we have to expand the first sticky post
  */
  protected function czr_fn_maybe_has_sticky_expanded(){
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
      $_classes[] = 'tc-grid-shadow';
    if ( ! empty( $this->grid_bottom_border ) )
      $_classes[] = 'tc-grid-border';
    if ( ! empty( $this->grid_hover_move ) )
      $_classes[] = 'tc-grid-hover-move';
    if ( ! empty( $this->contained ) )
      $_classes[] = 'container';

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
    * <hr class="col-sm-12 d-none d-md-flex"> after a certain grid-item given a certain layout
    */
    preg_match("/(col-md-[0-9]+)/", $_current_layout, $current_md_layout);
    $_current_layout = ! empty( $current_md_layout[0] ) ? $current_md_layout[0] : 'col-md-12';

    $_map = apply_filters(
        'tc_grid_col_layout_map',
        array(
          'col-md-12'  => '4',//no sidebars
          'col-md-11'  => '4',
          'col-md-10'  => '4',
          'col-md-9'   => '3',//one sidebar right or left
          'col-md-8'   => '3',
          'col-md-7'   => '2',
          'col-md-6'   => '1',//two sidebars
          'col-md-5'   => '1',
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


  /******************************
  * HELPERS FOR INLINE CSS
  *******************************/
  /**
  * @param (string) $col_layout
  * @return css media query string
  * Returns the paragraph and title media queries for a given layout
  */
  protected function czr_fn_get_grid_font_css( $_col_nb = '3' ) {
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

  //NOT USED
  /**
  * Return the array of sizes (ordered by @media queries) for a given column layout
  * @param  $_col_nb string
  * @param  $_requested_media_size
  * @return array()
  * Note : When all sizes are requested (default case), the returned array can be filtered with the current layout param
  * Size array must have the same length of the media query array
  */
  protected function czr_fn_get_grid_font_sizes( $_col_nb = '3', $_requested_media_size = null ) {
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

  //NOT USED
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


  //NOT USED
  /**
  * @return css string
  * @param size string
  * @param selector type string
  * returns ratio of size / body size for a given selector type ( headings or paragraphs )
  */
  protected function czr_fn_get_grid_font_ratios( $_size = 'xl' , $_sel = 'h' ) {
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


  //NOT USED
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
  protected function czr_fn_grid_assign_css_rules_to_selectors( $_media_query, $_css_prop, $_col_nb ) {
    $_css = '';
    //Add one column font rules if there's a sticky post
    if ( $this -> czr_fn_maybe_has_sticky_expanded() || '1' == $_col_nb ) {
      $_size      = $this -> czr_fn_get_grid_font_sizes( $_col_nb = '1', $_media_query );//size like xxl
      $_h_one_col = $this -> czr_fn_grid_build_css_rules( $_size , 'h' );
      $_p_one_col = $this -> czr_fn_grid_build_css_rules( $_size , 'p' );
      $_css .= "
          .grid-container__classic .cols-1 .entry-title {{$_h_one_col}}
          .grid-container__classic .cols-1 .tc-g-cont {{$_p_one_col}}
      ";
    }
    $_h = $_css_prop['h'];
    $_p = $_css_prop['p'];
    $_css .= "
        .grid-container__classic article .entry-title {{$_h}}
        .grid-container__classic .tc-g-cont {{$_p}}
    ";
    return $_css;
  }

  //NOT USED
  /**
  * @return string
  * @param size string
  * @param selector type string
  * returns the font-size and line-height css rules
  */
  protected function czr_fn_grid_build_css_rules( $_size = 'xl', $_wot = 'h' ) {
    $_lh_ratio = apply_filters( 'czr_grid_line_height_ratio' , 1.55 ); //line-height / font-size
    $_ratio = $this -> czr_fn_get_grid_font_ratios( $_size , $_wot );
    //body font size
    $_bs = esc_attr( czr_fn_opt( 'tc_body_font_size') );
    $_bs = is_numeric($_bs) && 1 >= $_bs ? $_bs : 15;
    return sprintf( 'font-size:%spx;line-height:%spx;' ,
      ceil( $_bs * $_ratio ),
      ceil( $_bs * $_ratio * $_lh_ratio )
    );
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
  * @return simple array of media queries
  */
  protected function czr_fn_get_grid_media_queries() {
    return apply_filters( 'czr_grid_media_queries' ,  array(
             '(min-width: 1200px)', '(min-width: 992px)', '(min-width: 768px)', '(min-width: 576px)'
           ));
  }


  /**
  * @return css string
  * hook : czr_fn_user_options_style
  */
  function czr_fn_user_options_style_cb( $_css ){
      $default_1_column_aspect_ratio = '40%';
      $golden_ratio                  = '61.803398%';

      $selector                      = '.grid-container__classic .czr__r-wGOC::before';
      $property                      = 'padding-top';
      /*
      * TODO: USE fittext for font sizing?
      */
      //golden ratio till md devices (min 768px)
      //then it depends on the layout
      $_css = sprintf("%s\n%s\n",
          $_css,
          sprintf( '%1$s{%2$s:%3$s}',
              $selector,
              $property,
              $golden_ratio
          )
      );

      //layout dependency
      //$this->content_wrapper_breadth can be 'narrow' (2 sidebars) | 'semi-narrow'(1 sidebar) | 'full' (no sidebars);
      $_contentbreadth_aspectratio_map = array(
          // 'width (full||semi-narrow||narrow) => ' array( xl, lg, md )
          'full'         => array( '', '', $default_1_column_aspect_ratio ),
          'semi-narrow'  => array( $default_1_column_aspect_ratio, '', '' ),
          'narrow'       => array( '', '', '' )
      );

      if ( ! array_key_exists( $this->content_wrapper_breadth, $_contentbreadth_aspectratio_map ) )
          return $_css;

      $aspect_ratio_map   = $_contentbreadth_aspectratio_map[ $this->content_wrapper_breadth ];
      $media_queries      = $this -> czr_fn_get_grid_media_queries();

      $_media_queries_css = '';

      //create the media queries
      foreach ( array_reverse($media_queries, true)  as $index => $media_query_size ) {

          if ( empty( $aspect_ratio_map[ $index ] ) ) {
              continue;
          }

          $rule = sprintf( '%1$s{%2$s:%3$s}',
                  $selector,
                  $property,
                  $aspect_ratio_map[ $index ]
          );


          $_media_queries_css .= "@media {$media_query_size} {{$rule}}";

      }

      if ( ! empty( $_media_queries_css) ) {
          $_css = sprintf("%s\n%s\n",
              $_css,
              $_media_queries_css
          );
      }

      return $_css;
  }

}
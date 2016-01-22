<?php
/**
* Post metas content actions
* Since 3.1.20, displays all levels of any hierarchical taxinomies by default and for all types of post (including hierarchical CPT). This feature can be disabled with a the filter : tc_display_taxonomies_in_breadcrumb (set to true by default). In the case of hierarchical post types (like page or hierarchical CPT), the taxonomy trail is only displayed for the higher parent.
*
* @package      Customizr
* @subpackage   classes
* @since        3.0.5
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_post_metas' ) ) :
    class TC_post_metas {
        static $instance;
        function __construct () {
          self::$instance =& $this;
          //Show / hide metas based on customizer user options (@since 3.2.0)
          add_action( 'template_redirect'                            , array( $this , 'tc_set_visibility_options' ) , 10 );
           //Show / hide metas based on customizer user options (@since 3.2.0)
          add_action( 'template_redirect'                            , array( $this , 'tc_set_design_options' ) , 20 );
          //Show / hide metas based on customizer user options (@since 3.2.0)
          add_action( '__after_content_title'         , array( $this , 'tc_set_post_metas_hooks' ), 20 );

        }


        /***********************
        * VISIBILITY HOOK SETUP
        ***********************/
        /**
        * Set the post metas visibility based on Customizer options
        * uses hooks tc_show_post_metas, body_class
        * hook : template_redirect
        *
        * @package Customizr
        * @since Customizr 3.2.0
        */
        function tc_set_visibility_options() {
          //if customizing context, always render. Will be hidden in the DOM with a body class filter is disabled.
          if ( 0 == esc_attr( TC_utils::$inst->tc_opt( 'tc_show_post_metas' ) ) ) {
            if ( TC___::$instance -> tc_is_customizing() )
              add_filter( 'body_class' , array( $this , 'tc_hide_all_post_metas') );
            else{
              add_filter( 'tc_show_post_metas' , '__return_false' );
              return;
            }
          }
          if ( is_singular() && ! is_page() && ! tc__f('__is_home') ) {
              if ( 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_show_post_metas_single_post' ) ) ) {
                  add_filter( 'tc_show_post_metas' , '__return_true' );
                  return;
              }

              if ( TC___::$instance -> tc_is_customizing() ) {
                  add_filter( 'body_class' , array( $this , 'tc_hide_post_metas') );
                  add_filter( 'tc_show_post_metas' , '__return_true' );
              }
              else
                  add_filter( 'tc_show_post_metas' , '__return_false' );
              return;
          }
          if ( ! is_singular() && ! tc__f('__is_home') && ! is_page() ) {
              if ( 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_show_post_metas_post_lists' ) ) ) {
                  add_filter( 'tc_show_post_metas' , '__return_true' );
                  return;
              }

              if ( TC___::$instance -> tc_is_customizing() ) {
                  add_filter( 'body_class' , array( $this , 'tc_hide_post_metas') );
                  add_filter( 'tc_show_post_metas' , '__return_true' );
              }
              else
                  add_filter( 'tc_show_post_metas' , '__return_false' );
              return;
          }
          if ( tc__f('__is_home') ) {
              if ( 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_show_post_metas_home' ) ) ) {
                  add_filter( 'tc_show_post_metas' , '__return_true' );
                  return;
              }
              if ( TC___::$instance -> tc_is_customizing() ) {
                  add_filter( 'body_class' , array( $this , 'tc_hide_post_metas') );
                  add_filter( 'tc_show_post_metas' , '__return_true' );
              }
              else
                  add_filter( 'tc_show_post_metas' , '__return_false' );
          }
        }



        /**
        * Default metas visibility controller
        * tc_show_post_metas gets filtered by tc_set_visibility_options() called early in template_redirect
        * @return  boolean
        * @package Customizr
        * @since Customizr 3.2.6
        */
        private function tc_show_post_metas() {
          global $post;
          //when do we display the metas ?
          //1) default is : not on home page, 404, search page
          //2) +filter conditions
          return apply_filters(
              'tc_show_post_metas',
              ! tc__f('__is_home')
              && ! is_404()
              && ! 'page' == $post -> post_type
              && in_array( get_post_type(), apply_filters('tc_show_metas_for_post_types' , array( 'post') ) )
          );
        }



        /***********************
        * DESIGN HOOK SETUP
        ***********************/
        function tc_set_design_options() {
          if ( 'buttons' == esc_attr( TC_utils::$inst->tc_opt( 'tc_post_metas_design' ) ) )
            return;

          add_filter( 'tc_meta_terms_glue'           , array( $this, 'tc_set_term_meta_glue' ) );
          add_filter( 'tc_meta_tax_class'            , '__return_empty_array' );

          add_filter( 'tc_post_tax_metas_html'       , array( $this, 'tc_set_tax_metas' ), 10, 2 );
          add_filter( 'tc_post_date_metas_html'      , array( $this, 'tc_set_date_metas' ), 10, 2 );
          add_filter( 'tc_post_author_metas_html'    , array( $this, 'tc_set_author_metas' ), 10 , 2 );
          add_filter( 'tc_set_metas_content'         , array( $this, 'tc_set_metas' ), 10, 2 );
        }


        /*****************
        * MODELS
        *****************/
        /**
        * Build the metas models
        * Render the view based on filters
        * hook : __after_content_title
        * @return void
        * @package Customizr
        * @since Customizr 3.2.2
        */
        function tc_set_post_metas_hooks() {
          if ( ! $this -> tc_show_post_metas() )
            return;
          global $post;
          $_model = array();
          //BUILD MODEL
          //Two cases : attachment and not attachment
          if ( 'attachment' == $post -> post_type ) {
            $_model = $this -> tc_build_attachment_post_metas_model();
          } else {
            $_model = $this -> tc_build_post_post_metas_model();
            //Set metas content based on customizer user options (@since 3.2.6)
            add_filter( 'tc_meta_utility_text'      , array( $this , 'tc_set_post_metas_elements'), 10 , 2 );
            //filter metas content with default theme settings
            add_filter( 'tc_meta_utility_text'      , array( $this , 'tc_add_link_to_post_after_metas'), 20 );
          }

          //RENDER VIEW
          $this -> tc_render_metas_view( $_model );
        }



        /**
        * Post metas model
        * @return model array
        * @package Customizr
        * @since Customizr 3.2.6
        */
        private function tc_build_post_post_metas_model() {
          $cat_list   = $this -> tc_meta_generate_tax_list( true );
          $tag_list   = $this -> tc_meta_generate_tax_list( false );
          $pub_date   = $this -> tc_get_meta_date( 'publication' );
          $auth       = $this -> tc_get_meta_author();
          $upd_date   = $this -> tc_get_meta_date( 'update' );

          $_args      = compact( 'cat_list' ,'tag_list', 'pub_date', 'auth', 'upd_date' );
          $_html      = sprintf( __( 'This entry was posted on %1$s<span class="by-author"> by %2$s</span>.' , 'customizr' ),
            $pub_date,
            $auth
          );
          return apply_filters( 'tc_post_metas_model' , compact( "_html" , "_args" ) );
        }



        /**
        * Attachment metas model
        * @return model array
        * @package Customizr
        * @since Customizr 3.3.2
        */
        private function tc_build_attachment_post_metas_model() {
          global $post;
          $metadata       = wp_get_attachment_metadata();
          $_html = sprintf( '%1$s <span class="entry-date"><time class="entry-date updated" datetime="%2$s">%3$s</time></span> %4$s %5$s',
              '<span class="meta-prep meta-prep-entry-date">'.__('Published' , 'customizr').'</span>',
              apply_filters('tc_use_the_post_modified_date' , false ) ? esc_attr( get_the_date( 'c' ) ) : esc_attr( get_the_modified_date('c') ),
              esc_html( get_the_date() ),
              ( isset($metadata['width']) && isset($metadata['height']) ) ? __('at dimensions' , 'customizr').'<a href="'.esc_url( wp_get_attachment_url() ).'" title="'.__('Link to full-size image' , 'customizr').'"> '.$metadata['width'].' &times; '.$metadata['height'].'</a>' : '',
              __('in' , 'customizr').'<a href="'.esc_url( get_permalink( $post->post_parent ) ).'" title="'.__('Return to ' , 'customizr').esc_attr( strip_tags( get_the_title( $post->post_parent ) ) ).'" rel="gallery"> '.get_the_title( $post->post_parent ).'</a>.'
          );
          return apply_filters( 'tc_attachment_metas_model' , compact( "_html" ) );
        }



        /*****************
        * VIEW
        *****************/
        /**
        * Customizr metas view
        * @return  html string
        * @package Customizr
        * @since Customizr 3.3.2
        */
        private function tc_render_metas_view( $_model ) {
          if ( empty($_model) )
            return;
          //extract $_html , $_args
          extract( $_model );
          $_html = isset($_html) ? $_html : '';
          $_args = isset($_args) ? $_args : array();
          //echoes all filtered metas components
          echo apply_filters(
            'tc_post_metas',
            sprintf( '<div class="entry-meta">%s</div>',
              apply_filters( 'tc_meta_utility_text', $_html , $_args )
            )
          );
        }




        /*****************
        * SETTERS / GETTERS / HELPERS
        *****************/
        /**
        * Set meta content based on user options
        * hook : tc_meta_utility_text
        * @return  html string as a wp filter
        * @package Customizr
        * @since Customizr 3.2.6
        */
        function tc_set_post_metas_elements( $_default , $_args = array() ) {
            $_show_cats         = 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_show_post_metas_categories' ) ) && false != $this -> tc_meta_generate_tax_list( true );
            $_show_tags         = 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_show_post_metas_tags' ) ) && false != $this -> tc_meta_generate_tax_list( false );
            $_show_pub_date     = 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_show_post_metas_publication_date' ) );
            $_show_upd_date     = 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_show_post_metas_update_date' ) ) && false !== TC_utils::$inst -> tc_post_has_update();
            $_show_upd_in_days  = 'days' == esc_attr( TC_utils::$inst->tc_opt( 'tc_post_metas_update_date_format' ) );
            $_show_date         = $_show_pub_date || $_show_upd_date;
            $_show_author       = 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_show_post_metas_author' ) );

            //extract cat_list, tag_list, pub_date, auth, upd_date from $args if not empty
            if ( empty($_args) )
              return $_default;
            extract($_args);

            //TAGS / CATS
            $_tax_text  = '';
            if ( $_show_cats && $_show_tags )
                $_tax_text   .= __( 'This entry was posted in %1$s and tagged %2$s' , 'customizr' );
            if ( $_show_cats && ! $_show_tags )
                $_tax_text   .= __( 'This entry was posted in %1$s' , 'customizr' );
            if ( ! $_show_cats && $_show_tags )
                $_tax_text   .= __( 'This entry was tagged %2$s' , 'customizr' );
            $_tax_text = apply_filters( 'tc_post_tax_metas_html' ,
              sprintf( $_tax_text , $cat_list, $tag_list ),
              compact( "_show_cats" , "_show_tags" , "cat_list", "tag_list" )
            );

            //PUBLICATION DATE
            $_date_text = '';
            if ( $_show_pub_date ) {
              $_date_text        = empty($_tax_text) ? __( 'This entry was posted on %1$s' , 'customizr' ) : $_date_text;
              if ( $_show_cats )
                $_date_text   .= __( 'on %1$s' , 'customizr' );
              if ( ! $_show_cats && $_show_tags )
                $_date_text   .= __( 'and posted on %1$s' , 'customizr' );
              $_date_text = apply_filters( 'tc_post_date_metas_html',
                sprintf( $_date_text, $pub_date ),
                $pub_date
              );
            }


            //AUTHOR
            $_author_text = '';
            if ( $_show_author ) {
              if ( empty($_tax_text) && empty($_date_text) ) {
                  $_author_text = sprintf( '%s <span class="by-author">%s</span>' , __( 'This entry was posted', 'customizr' ), __('by %1$s' , 'customizr') );
              } else {
                  $_author_text = sprintf( '<span class="by-author">%s</span>' , __('by %1$s' , 'customizr') );
              }
              $_author_text = apply_filters( 'tc_post_author_metas_html',
                sprintf( $_author_text, $auth ),
                $auth
              );
            }


            //UPDATE DATE
            $_update_text = '';
            if ( $_show_upd_date ) {
              if ( $_show_upd_in_days ) {
                $_update_days = TC_utils::$inst -> tc_post_has_update();
                $_update_text = ( 0 == $_update_days ) ? __( '(updated today)' , 'customizr' ) : sprintf( __( '(updated %s days ago)' , 'customizr' ), $_update_days );
                $_update_text = ( 1 == $_update_days ) ? __( '(updated 1 day ago)' , 'customizr' ) : $_update_text;
              }
              else {
                $_update_text = __( '(updated on %1$s)' , 'customizr' );
              }
              $_update_text = apply_filters( 'tc_post_update_metas_html',
                sprintf( $_update_text , $upd_date ),
                $upd_date
              );
            }

            return apply_filters ( 'tc_set_metas_content',
              sprintf( '%1$s %2$s %3$s %4$s' , $_tax_text , $_date_text, $_author_text, $_update_text ),
              compact( "_tax_text" , "_date_text", "_author_text", "_update_text" )
            );
        }



        /**
        * Helper
        * @return string of all the taxonomy terms (including the category list for posts)
        * @param  hierarchical tax boolean => true = categories like, false = tags like
        *
        * @package Customizr
        * @since Customizr 3.0
        */
        public function tc_meta_generate_tax_list( $hierarchical ) {
          $post_terms = $this -> tc_get_term_of_tax_type( $hierarchical );
          if ( ! $post_terms )
            return;

          $_terms_html_array  = array_map( array( $this , 'tc_meta_term_view' ), $post_terms );
          return apply_filters( 'tc_meta_generate_tax_list', implode( apply_filters( 'tc_meta_terms_glue' , '' ) , $_terms_html_array ) , $post_terms );
        }


        /**
        * Helper
        * @return string of the single term view
        * @param  $term object
        *
        * @package Customizr
        * @since Customizr 3.3.2
        */
        private function tc_meta_term_view( $term ) {
          $_classes         =  array( 'btn' , 'btn-mini' );
          $_is_hierarchical  =  is_taxonomy_hierarchical( $term -> taxonomy );
          if ( $_is_hierarchical ) //<= check if hierarchical (category) or not (tag)
            array_push( $_classes , 'btn-tag' );

          $_classes      = implode( ' ', apply_filters( 'tc_meta_tax_class', $_classes , $_is_hierarchical, $term ) );

          // (Rocco's PR Comment) : following to this https://wordpress.org/support/topic/empty-articles-when-upgrading-to-customizr-version-332
          // I found that at least wp 3.6.1  get_term_link($term->term_id, $term->taxonomy) returns a WP_Error
          // Looking at the codex, looks like we can just use get_term_link($term), when $term is a term object.
          // Just this change avoids the issue with 3.6.1, but I thought should be better make a check anyway on the return type of that function.
          $_term_link    = is_wp_error( get_term_link( $term ) ) ? '' : get_term_link( $term );

          $_to_return    = $_term_link ? '<a class="%1$s" href="%2$s" title="%3$s"> %4$s </a>' :  '<span class="%1$s"> %4$s </a>';

          return apply_filters( 'tc_meta_term_view' , sprintf($_to_return,
              $_classes,
              $_term_link,
              esc_attr( sprintf( __( "View all posts in %s", 'customizr' ), $term -> name ) ),
              $term -> name
            )
          );
        }



        /**
        * Helper to return the current post terms of specified taxonomy type : hierarchical or not
        *
        * @return boolean (false) or array
        * @param  boolean : hierarchical or not
        * @package Customizr
        * @since Customizr 3.1.20
        *
        */
        public function tc_get_term_of_tax_type( $hierarchical = true ) {
          //var declaration
          $post_type              = get_post_type( TC_utils::tc_id() );
          $tax_list               = get_object_taxonomies( $post_type, 'object' );
          $_tax_type_list         = array();
          $_tax_type_terms_list   = array();

          if ( empty($tax_list) )
              return false;

          //filter the post taxonomies
          while ( $_tax_object = current($tax_list) ) {
            // cast $_tax_object stdClass object in an array to access its property 'public'
            // fix for PHP version < 5.3 (?)
            $_tax_object = (array) $_tax_object;

            //Is the object well defined ?
            if ( ! isset($_tax_object['name']) ) {
              next($tax_list);
              continue;
            }

            $_tax_name = $_tax_object['name'];

            //skip the post format taxinomy
            if ( ! $this -> tc_is_tax_authorized( $_tax_object, $post_type ) ) {
              next($tax_list);
              continue;
            }

            if ( (bool) $hierarchical === (bool) $_tax_object['hierarchical'] )
                $_tax_type_list[$_tax_name] = $_tax_object;
            next($tax_list);
          }

          if ( empty($_tax_type_list) )
              return false;

          //fill the post terms array
          foreach ($_tax_type_list as $tax_name => $data ) {
              $_current_tax_terms = get_the_terms( TC_utils::tc_id() , $tax_name );

              //If current post support this tax but no terms has been assigned yet = continue
              if ( ! $_current_tax_terms )
                  continue;

              while( $term = current($_current_tax_terms) ) {
                  $_tax_type_terms_list[$term -> term_id] = $term;
                  next($_current_tax_terms);
              }
          }
          return empty($_tax_type_terms_list) ? false : apply_filters( "tc_tax_meta_list" , $_tax_type_terms_list , $hierarchical );
        }



        /**
        * Helper : check if a given tax is allowed in the post metas or not
        * A tax is authorized if :
        * 1) not in the exclude list
        * 2) AND not private
        *
        * @return boolean (false)
        * @param  $post_type, $_tax_object
        * @package Customizr
        * @since Customizr 3.3+
        *
        */
        public function tc_is_tax_authorized( $_tax_object , $post_type ) {
          $_in_exclude_list = in_array(
            $_tax_object['name'],
            apply_filters_ref_array ( 'tc_exclude_taxonomies_from_metas' , array( array('post_format') , $post_type , TC_utils::tc_id() ) )
          );

          $_is_private = false === (bool) $_tax_object['public'] && apply_filters_ref_array( 'tc_exclude_private_taxonomies', array( true, $_tax_object['public'], TC_utils::tc_id() ) );
          return ! $_in_exclude_list && ! $_is_private;
        }


        /**
        * Helper
        * Return the date post metas
        *
        * @package Customizr
        * @since Customizr 3.2.6
        */
        public function tc_get_meta_date( $pub_or_update = 'publication', $_format = '' ) {
            if ( 'short' == $_format )
              $_format = 'j M, Y';

            $_format = apply_filters( 'tc_meta_date_format' , $_format );
            $_use_post_mod_date = apply_filters( 'tc_use_the_post_modified_date' , 'publication' != $pub_or_update );
            return apply_filters(
                'tc_date_meta',
                sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date updated" datetime="%3$s">%4$s</time></a>' ,
                    esc_url( get_day_link( get_the_time( 'Y' ), get_the_time( 'm' ), get_the_time( 'd' ) ) ),
                    esc_attr( get_the_time() ),
                    $_use_post_mod_date ? esc_attr( get_the_modified_date('c') ) : esc_attr( get_the_date( 'c' ) ),
                    $_use_post_mod_date ? esc_html( get_the_modified_date( $_format ) ) : esc_html( get_the_date( $_format ) )
                ),
                $_use_post_mod_date,
                $_format
            );//end filter
        }


        /**
        * Helper
        * Return the post author metas
        *
        * @package Customizr
        * @since Customizr 3.2.6
        */
        private function tc_get_meta_author() {
            return apply_filters(
                'tc_author_meta',
                sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>' ,
                    esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
                    esc_attr( sprintf( __( 'View all posts by %s' , 'customizr' ), get_the_author() ) ),
                    get_the_author()
                )
            );//end filter
        }


        /**
        * @return  string
        * Return the filter post metas for specific post formats
        * hook tc_meta_utility_text
        * @package Customizr
        * @since Customizr 3.2.9
        */
        function tc_add_link_to_post_after_metas( $_metas_html ) {

          if ( apply_filters( 'tc_show_link_after_post_metas' , true )
            && in_array( get_post_format(), apply_filters( 'tc_post_formats_with_no_heading', TC_init::$instance -> post_formats_with_no_heading ) )
            && ! is_singular() ) {
            return apply_filters('tc_add_link_to_post_after_metas',
              sprintf('%1$s | <a href="%2$s" title="%3$s">%3$s &raquo;</a>', $_metas_html, get_permalink(), __('Open' , 'customizr') )
            );
          }
          return $_metas_html;
        }




        /**
        * hook body_class filter
        *
        * @package Customizr
        * @since Customizr 3.2.0
        */
        function tc_hide_all_post_metas( $_classes ) {
          return array_merge($_classes , array('hide-all-post-metas') );
        }


        /**
        * hook body_class filter
        *
        * @package Customizr
        * @since Customizr 3.2.0
        */
        function tc_hide_post_metas( $_classes ) {
          return array_merge($_classes , array('hide-post-metas') );
        }


                /**
        * hook : tc_meta_terms_glue
        * @return  string
        */
        public function tc_set_term_meta_glue() {
          return ' / ';
        }


        /**
        * hook : tc_post_tax_metas_html
        * @return  string
        */
        function tc_set_tax_metas( $_html , $_tax = array() ) {
          if ( empty($_tax) )
            return $_html;
          //extract "_show_cats" , "_show_tags" , "cat_list", "tag_list"
          extract($_tax);
          $cat_list = ! empty($cat_list) && $_show_cats ? sprintf( '&nbsp;%s %s' , __('in' , 'customizr') , $cat_list ) : '';
          $tag_list = ! empty($tag_list) && $_show_tags ? sprintf( '&nbsp;%s %s' , __('tagged' , 'customizr') , $tag_list ) : '';
          return sprintf( '%s%s' , $cat_list, $tag_list );
        }


        /**
        * hook : tc_post_date_metas_html
        * @return  string
        */
        function tc_set_date_metas( $_html, $_pubdate = '' ) {
          if ( empty($_pubdate))
            return $_html;
          return TC_post_metas::$instance -> tc_get_meta_date( 'publication' , 'short' );
        }

        /**
        * hook : tc_post_author_metas_html
        * @return  string
        */
        function tc_set_author_metas( $_html , $_auth = '' ) {
          if ( empty($_auth) )
            return $_html;

          return sprintf( '<span class="by-author"> %s %s</span>' , __('by' , 'customizr'), $_auth );
        }

        /**
        * hook : tc_set_metas_content
        * @return  string
        */
        function tc_set_metas( $_html, $_parts = array() ) {
          if ( empty($_parts) )
            return $_html;
          //extract $_tax_text , $_date_text, $_author_text, $_update_text
          extract($_parts);
          return sprintf( '%1$s %2$s %3$s %4$s' , $_date_text, $_tax_text , $_author_text, $_update_text );
        }
    }//end of class
endif;

//the only purpose of this function is to use the_tags() wp function in the theme...
function tc_get_the_tags() {
    return the_tags();
}

<?php
/**
* Displays useful informations for debugging
*
* 
* @package      Customizr
* @subpackage   classes
* @since        3.0.10
* @author       Nicolas GUILLAUME <nicolas@themesandco.com>
* @copyright    Copyright (c) 2013, Nicolas GUILLAUME
* @link         http://themesandco.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

class TC_debug {
    public $hooks;
    public $hook_list;
    public $hook_tree;
    public $class_list;
    public $apply_filters_list;

    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;

    function __construct () {

      self::$instance =& $this;

      add_action ( 'wp_enqueue_scripts'                 , array( $this , 'tc_debug_box_scripts' ));
      add_action ( 'wp_head'                            , array( $this , 'tc_debug_scripts' ));
      add_action ( '__after_footer'                     , array( $this , 'tc_display_debug_box' ));

      add_filter ( 'tip'                                , array( $this , 'tc_display_debug_tooltip'), 10, 4 );
      add_filter ( 'rec'                                , array( $this , 'tc_record_story'), 10, 3 );
      add_action ( 'display_story'                      , array( $this , 'tc_display_story'), 10, 1 );
      add_action ( 'display_hook_tree'                  , array( $this , 'tc_display_hook_tree'), 10, 1 );
      add_action ( 'display_class_tree'                 , array( $this , 'tc_display_class_tree'), 10, 2 );

      //get the raw hooks list
      $this -> hooks                = $this -> tc_get_hooks_list();
     
      //get the unique name list
      foreach ($this -> hooks as $key => $value) {
        $namelist[] = $value['name'];
      }
      $namelist = array_unique($namelist);

      //for later use
      $this -> hook_list            = $namelist;

      //create a clean and complete array of hooks
      foreach ($namelist as $key => $name) {
        foreach ($this -> hooks as $hook => $data) {
          if ($name == $data['name']) {
            $newhook[$name] = empty($newhook[$name]) ? array() : $newhook[$name];
            $newhook[$name] = wp_parse_args($newhook[$name],$data);
            //$newhook[$name] = array_unique($newhook[$name]);
            unset($newhook[$name]['name']);
            //array_push($newhook[$name],$data);
          }
        }
      }

      $this -> hooks                = $newhook;

      //hook tree var
      $this -> hook_tree            = $this -> tc_create_hook_tree();

      //apply filters list
      $this -> apply_filters_list   = $this -> tc_get_apply_filters_list();

    }//end of constructor






     /**
    * 
    * @package Customizr
    * @since Customizr 3.0.10
    *
    */
    function tc_debug_box_scripts () {
      if( !current_user_can( 'edit_theme_options' ))
        return;

      if( true == tc__f( '__get_option' , 'tc_debug_box' ) || true == tc__f( '__get_option' , 'tc_debug_tips' ) ) {
        wp_enqueue_script( 'jquery-ui-draggable' );
      }

      tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );

    }




    function tc_debug_scripts() {
      if( !current_user_can( 'edit_theme_options' ))
        return;

      if( true == tc__f( '__get_option' , 'tc_debug_box' ) || true == tc__f( '__get_option' , 'tc_debug_tips') ) {
          echo "<link href='http://fonts.googleapis.com/css?family=Spinnaker' rel='stylesheet' type='text/css'>";
        ?>
          <script>
          jQuery(document).ready(function( $) {
                !function ( $) {
                  $(window).on( 'load' , function () {
                    $(function() {

                      $('.debug-tip').live('click',function() {
                        $('.tooltip').draggable();
                      });


                      $( '.debug-tip' ).tooltip();
                      });
                    });
               }(window.jQuery);
            });
          </script>
        <?php
      }//endif

      if( true == tc__f( '__get_option' , 'tc_debug_box') ) {
        ?>
        <script>
          jQuery(document).ready(function( $) {
            !function ( $) {
              $(window).on( 'load' , function () {
                $(function() {
                  $('div.hook-tree div:has(div)').addClass('parent'); // Requires jQuery 1.2!

                  $('div.hook-tree div').click(function() {
                    var o = $(this);
                    o.children('div').toggle();
                    o.filter('.parent').toggleClass('expanded');
                    return false;
                  });

                  $('p.filter').hide();
                  $('p.hooks').hide();
                  $('p.action').show();
                  $('#story button').click(function() {
                    var currentId = $(this).attr('id')
                    var o = $(this);
                    $('#story p').hide();
                    $('p.'+currentId).show();
                    $('#story p.total').show();
                    return false;
                  });

                });
              })
             }(window.jQuery);
          });
        </script>
        <?php
      }//endif

      tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );

    }//end of function


    /**
    * 
    * @package Customizr
    * @since Customizr 3.0.10
    *
    */
    function tc_display_debug_box () {
      if( false == tc__f( '__get_option' , 'tc_debug_box') )
        return;
      if( !current_user_can( 'edit_theme_options' ))
        return;

      tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );

      $plugins    = get_option('active_plugins');

      ?>
        <script>
          jQuery(document).ready(function( $) {
            !function ( $) {
              $(window).on( 'load' , function () {
                    $(function() {
                      $('#debug').draggable();
                      $('#debugtab a').click(function (e) {
                        e.preventDefault();
                        $(this).tab('show');
                      })
                    });
              })
             }(window.jQuery);
          });
        </script>
        <div id="debug">
          <p class="general">
            <?php
              //CHECK IF WE ARE USING A CHILD THEME
              //get WP_Theme object of customizr
              $tc_theme                     = wp_get_theme();
              //define a boolean if using a child theme
              $is_child                     = $tc_theme -> parent();

              printf('WP v%1$s | %2$s | Lang : %3$s',
                get_bloginfo( $show = 'version' ),
                $is_child ? '<span class="info">Theme child of</span> Customizr v'.CUSTOMIZR_VER : 'Theme Customizr v'.CUSTOMIZR_VER,
                get_bloginfo( $show = 'language' )
                );
              ?>
          </p><br/>

          <!--TABS MENU-->
          <ul class="nav nav-tabs" id="debugtab">
            <li class="active"><a href="#context">Context</a></li>
            <li><a href="#story">Timeline</a></li>
            <li><a href="#dashboard">WP settings and plugins <span class="info">(<?php echo count($plugins) ?>)</span></a></li>
            <li><a href="#trees">Customizr options and hooks</a></li>
            <li><a href="#notes">Notes</a></li>
          </ul>

          <!--TABS-->
          <div class="tab-content">
            
            <div class="tab-pane active" id="context">
              <div class="row-fluid">
                <div class="span6"> 
                   <p class="title">Contextual infos</p>
                    <?php
                      global $wp_query;
                      echo 'ARCHIVE BOOLEANS<br/>';
                      echo 'is_author() : <span class="info">'.is_author().'</span><br/>';
                      echo 'is_archive() : <span class="info">'.is_archive().'</span><br/>';
                      echo 'is_tag() : <span class="info">'.is_tag().'</span><br/>';
                      echo 'is_category() : <span class="info">'.is_category().'</span><br/>';
                      echo 'is_tax() : <span class="info">'.is_tax().'</span><br/>';
                      echo 'is_year() : <span class="info">'.is_year().'</span><br/>';
                      echo 'is_day() : <span class="info">'.is_day().'</span><br/>';
                      echo 'is_month() : <span class="info">'.is_month().'</span><br/><br/>';
                      
                      echo 'POST_TYPES BOOLEANS<br/>';
                      echo 'is_page() : <span class="info">'.is_page().'</span><br/>';
                      echo 'is_attachment() : <span class="info">'.is_attachment().'</span><br/>';
                      echo 'is_post_type_archive() : <span class="info">'.$wp_query ->is_post_type_archive.'</span><br/><br/>';

                      echo 'SINGULARITY BOOLEANS<br/>';
                      echo 'is_singular() : <span class="info">'.is_singular().'</span><br/>';
                      echo 'is_single() : <span class="info">'.is_single().'</span><br/><br/>';

                      echo 'OTHER BOOLEANS<br/>';
                      echo 'is_search() : <span class="info">'.is_search().'</span><br/>';
                      echo 'is_404() : <span class="info">'.is_404().'</span><br/>';
                      echo 'is_home() : <span class="info">'.is_home().'</span><br/>';
                      echo 'is_front_page() : <span class="info">'.is_front_page().'</span><br/>';
                      echo 'is_posts_page() : <span class="info">'.$wp_query -> is_posts_page.'</span><br/>';
                      echo 'customizer on ? : <span class="info">'.isset( $_REQUEST['wp_customize'] ).'</span><br/>';
                  ?>
                </div>

                <div class="span6">
                  <p class="title">Query</p>
                  <div class="hook-tree">
                      <?php $this -> tc_display_query_tree(); ?>
                  </div>
                </div>
              </div>
            </div>


            <div class="tab-pane" id="story">
              <div><button id="action" class="btn btn-mini"> Show templates and actions (html rendering) </button> <button id="filter" class="btn btn-mini"> Show filters (getting values) </button> <button id="hooks" class="btn btn-mini"> Show all steps </button></div><br/>
              <p class="filter title">FILTERS</p>
              <p class="action title">TEMPLATES AND ACTIONS</p>
              <p class="hooks title">TEMPLATES, ACTIONS AND FILTERS</p><br/>
              <?php do_action( 'display_story') ?>
            </div>


            <div class="tab-pane" id="dashboard">
              <div class="row-fluid">
                <div class="span6">
                 <p class="title">Theme supports</p>
                      <?php
                        $features = array(
                          'post-thumbnails',
                          'post-formats',
                          'custom-header',
                          'custom-background',
                          'menus',
                          'automatic-feed-links',
                          'editor-style',
                          'widgets'
                          );

                        foreach ($features as $feat) {
                          if ( current_theme_supports( $feat ) ) {
                            echo '<p><span class="info"> &bull; '.$feat.'</span></p>';
                          }
                        }
                      ?>
                </div>
                <div class="span6">
                  <p class="title">Image sizes (in px)</p>
                    <?php
                      global $_wp_additional_image_sizes;
                      $built_in_sizes = array( 'thumbnail', 'medium', 'large' );
                      $sizes          = array();

                      foreach( get_intermediate_image_sizes() as $s ){
                        $sizes[ $s ] = array( 0, 0 );

                        if( in_array( $s, $built_in_sizes ) ){
                          $sizes[ $s ][0] = get_option( $s . '_size_w' );
                          $sizes[ $s ][1] = get_option( $s . '_size_h' );
                        }
                        else{

                          if( isset( $_wp_additional_image_sizes ) && isset( $_wp_additional_image_sizes[ $s ] ) ) {
                            $sizes[ $s ] = array( 'w:'.$_wp_additional_image_sizes[ $s ]['width'], 'h:'.$_wp_additional_image_sizes[ $s ]['height'], );
                          }
                        }
                      }//end foreach
                   
                      foreach( $sizes as $size => $atts ){
                        $info = in_array( $size, $built_in_sizes ) ? ' (WP)' : ' (theme)';
                        $crop = (isset( $_wp_additional_image_sizes ) && isset( $_wp_additional_image_sizes[ $size ] ) ) ? $_wp_additional_image_sizes[ $size ]['crop'] : false;
                        $crop = $crop ? '(cropped)' : '';
                        echo '<p><span class="info"> &bull; '.$size .$info. ' : ' . implode( ' x ', $atts ) . ' '.$crop.'</span><p>';
                      }
                    ?>
                </div>
              </div>
              <br/>
              <div class="row-fluid">
                <div class="span6">
                   <p class="title">Custom post type</p>
                  <?php

                    $args = array(
                       //'public'   => true,
                       '_builtin' => false
                    );

                    $output = 'names'; // names or objects, note names is the default
                    $operator = 'and'; // 'and' or 'or'

                    $post_types = get_post_types( $args, $output, $operator ); 

                    foreach ( $post_types  as $post_type ) {

                       echo '<p><span class="info"> &bull; ' . $post_type . '</span></p>';
                    }

                  ?>
                </div>
                <div class="span6">
                  <p class="title">Custom taxonomies</p>
                  <?php 
                    $args = array(
                      //'public'   => true,
                      '_builtin' => false
                      
                    ); 
                    $output = 'names'; // or objects
                    $operator = 'and'; // 'and' or 'or'
                    $taxonomies = get_taxonomies( $args, $output, $operator ); 
                    if ( $taxonomies ) {
                      foreach ( $taxonomies  as $taxonomy ) {
                        echo '<p><span class="info"> &bull; ' . $taxonomy . '</span></p>';
                      }
                    }
                  ?>
                </div>
              </div>
              <br/>
              <div class="row-fluid">
                <div class="span6">
                  <p class="title">Active plugins</p>
                    <?php
                      echo '<strong>'.count($plugins).' active plugin(s) :<br/></strong>';
                      foreach ($plugins as $key => $value) {
                        echo '<p><span class="info"> &bull; '.substr($value, 0, strpos($value, "/")).'</span></p>';
                      }
                    ?>
                </div>
                <div class="span6">
                  <?php
                      $plugin_list = array(

                        'Security' => array(
                            'about'  => '<i>The web is not a safe place, you MUST protect your website.</i><br/>',
                            'Better WP Security' => array(
                              'desc' => 'Downloaded more than 1 million times, and 5 stars rated. Helps protect your Wordpress installation from attackers. Hardens standard Wordpress security by hiding vital areas of your site, protecting access to important files via htaccess, preventing brute-force login attempts, detecting attack attempts, and more. MUST INSTALL!',
                              'link' => 'http://wordpress.org/plugins/better-wp-security/'),

                            'Akismet'  => array(
                              'desc' => 'Akismet is quite possibly the best way in the world to protect your blog from comment and trackback spam. It keeps your site protected from spam even while you sleep.',
                              'link' => 'http://wordpress.org/plugins/akismet/')
                          ),

                        'Performance' => array(
                            'about'  => '<i>Have you check if your website loads fast? Check your performance with those sites : <a href="http://gtmetrix.com/" target="_blank">GT Metrix</a>, <a href="http://developers.google.com/speed/pagespeed/insights/" target="_blank">Google Page Speed</a>, <a href="http://gtmetrix.com/" target="_blank">GT Metrix</a>, <a href="http://tools.pingdom.com/fpt/" target="_blank">Pingdom Tool</a></i><br/>',
                            'W3 Total Cache'  => array(
                              'desc' => 'The highest rated and most complete WordPress performance plugin. Dramatically improve the speed and user experience of your site. Add browser, page, object and database caching as well as minify and content delivery network (CDN) to WordPress.',
                              'link' => 'http://wordpress.org/plugins/w3-total-cache/')
                          ),


                        'Search Engine Optimization (SEO)' => array(
                            'about'  => '<i>Become Google best friend!</i><br/>',
                            'WordPress SEO'  => array(
                              'desc' => 'The first true all-in-one SEO solution for WordPress, including on-page content analysis, XML sitemaps and much more.',
                              'link' => 'http://wordpress.org/plugins/wordpress-seo/')
                          ),

                        'Images' => array(
                            'about'  => '<i>Make your images compatible with the Customizr Theme in one click : safe and fast plugin!</i><br/>',
                            'Regenerate Thumbnails'  => array(
                              'desc' => 'This plugin regenerate the thumbnails for your image attachments. This is very handy if you\'ve changed any of your thumbnail dimensions (via Settings -> Media) after previously uploading images or have changed to a theme with different featured post image dimensions.',
                              'link' => 'http://wordpress.org/plugins/regenerate-thumbnails/')
                        )
                      );
                  ?>
                  <p class="title">Recommended plugins</p>
                  <?php
                    foreach ($plugin_list as $cat => $plugs) {
                      echo '<p><span style="text-decoration: underline">'.$cat.'</span><br/>';
                      echo isset($plugs['about']) ? $plugs['about'] : '';
                      foreach ($plugs as $plug => $data) {
                        if (is_array($data)) {
                          ?>
                            <a style="padding-left: 10px;" href="<?php echo $data['link'] ?>" class="debug-tip" data-placement="top" data-html="true" data-trigger="hover" data-toggle="tooltip" title="<?php echo $data['desc'] ?>" target="_blank"><span class="info"> &bull; <?php echo $plug ?></span></a><br/>
                          <?php
                        }
                      }
                      echo '</p><br/>';
                    }
                  ?>
                </div>
              </div>
            </div>


            <div class="tab-pane" id="trees">
              <div class="row-fluid">
                <div class="span6">
                  <p class="title">Options map</p>
                  <div class="hook-tree">
                    <div>Customizer options (click to see details)
                        <?php
                          foreach ( tc__f( '__options' ) as $setting => $value) {
                            if('tc_sliders' == $setting && !empty($value)) {
                              echo '<div class="hook-func"><p>'.$setting.' : <br/>';
                              foreach ($value as $key => $slides) {
                                echo '---<span class="info">'.$key.' ('.count($slides).' slide(s)</span><br/>';
                              }
                            echo '</p></div>';
                            }

                            else {
                            echo '<div class="hook-func"><p>'.$setting.' : <span class="info">'.$value.'</span></p></div>';
                            }

                          }
                        ?>
                    </div>
                  </div>
                </div>
                <div class="span6">
                  <p class="title">Hooks map</p>
                  <p style="color:white">The tree of hook shows :</br>
                    Class group > hook (type : action or filter) > Class::Function</p></br>
                  <div class="hook-tree">

                    <?php do_action( 'display_hook_tree') ?>

                  </div>
                 </div>
              </div>
            </div>

            <div class="tab-pane" id="notes">
                <p class="title">Code logic</p>
                <p style="color:white">
                The Customizr theme is built on a classes framework. The classes are identified by their group and name like this : class-[group]-[name].php.<br/><br/>

                The function tc__( $group, $classname) :<br/>
                1) scans the theme folder to find the appropriate group / class <br/>
                2) and then instanciates the class(es) through a singleton factory.<br/><br/>

                <strong>A class typically includes a constructor which is mainly used to hook the methods to actions or filters.</strong><br/><br/>

                Classes are instanciated by group in inc/class-customizr-__.php.<br/><br/>

                Check the hooks map to understand how class groups organize the content.</p><br/>
          
                
                <p class="title">Actions</p>
                <p style="color:white">Actions are used to render HTML or execute some code in predefined WP actions.</br>
                The best and safest way to customize the Customizr theme, is to use those actions hooks. You can add actions <strong>( add_action( 'hook-hame','your-function-name', $priority, $arguments) )</strong> to existing hooks and order them with the priority parameter.
                </p><br/>
                
                <p class="title">Filters</p>
                <p style="color:white">Filters are used two ways in the Customizr theme:</br>1) In the classes, some filters are defined in the constructor, and then used anywhere to get values and avoid dependencies.<br/>2) Inside methods, they are used to modify the output if needed (with apply_filters).</p><br/>

                <a title="More about the template hierarchy" href="http://codex.wordpress.org/Plugin_API" target="_blank"><span class="info">More about WordPress hooks</span></a><br/><br/>

                <p class="title">Loop</p>
                <p style="color:white">Customizr uses one single loop for all kind of content. It is located in index.php. <br/><br/>The different kind of content (posts, pages, lists of posts, 404, no-result) are displayed by the /parts/* classes and filtered by the conditional tags : is_singular, is_404, is_search, is_archive, is_page...<br/><br/>
                There are no restrictions to use the WordPress built-in <a title="More about the template hierarchy" href="http://codex.wordpress.org/Template_Hierarchy" target="_blank"><span class="info">template hierarchy</span></a> by copying the index.php, modifying and renaming it (single.php for example).</p><br/>

                <p class="title">Templates</p>
                <p style="color:white">For simplification purposes, the theme uses few WP templates : header.php, index.php, footer.php, comments.php and sidebar(s).php. <br/>
                Those templates only includes some structural HTML markup, the rest is rendered with the action hooks defined in the classes of the parts/ folder.</p><br/>
                
              </p>
            </div>
          </div>

      </div><!--#debug-->
      <?php
    }

    function tc_get_func_hook($function) {

       //clean the function string
      $function           = trim(str_replace(array("(",")"), "", $function));

      //initialize the function hook array
      $function_hook      = array();

      //get the hook and its type : filter or action
      foreach ($this -> hooks as $hook => $hooked_func) {
        foreach ($hooked_func as $func) {
          //we avoid the type and group element of the array
          if( is_array($func) && in_array($function, $func) ) {
            $function_hook[$function] = $hook;
          }
         //$type = isset($this -> hooks[$hook]) ? $this -> hooks[$hook]['type'] : null;
        }//end foreach
      }//end foreach

      $hook = isset( $function_hook[$function] ) ? $function_hook[$function] : null ;

      return array('hook' => $hook, 'type' => $this -> hooks[$hook]['type']);
    }




    function tc_display_debug_tooltip( $function, $class, $file, $float = null ) {
      if( !current_user_can( 'edit_theme_options' ) )
        return;
      if( false == tc__f( '__get_option' , 'tc_debug_tips') )
        return;
      
      //get function hook infos
      $func_hook            = $this -> tc_get_func_hook($function);

       //isolate file name
      $end_car              = strlen($file) - (int)strpos($file, "/themes/");
      $located_in           = substr($file, (int)strpos($file, '/themes/' ) + 8 , $end_car);

      $comments             = $this -> tc_get_func_comments( $class , $function );
      
      $tc_debug_tips_color  = esc_html( tc__f( '__get_option' , 'tc_debug_tips_color') );

      $filtrable_by         = in_array ( $function, $this -> apply_filters_list ) ? $function : false;

      $title                = 'Displayed by function/method :  <span>'.$function.'()</span><br/>';
      $title                .= ( !empty($func_hook['type']) && !empty($func_hook['hook']) ) ? 'Hooked on '.$func_hook['type'].' : <span>'.$func_hook['hook'].'</span><br/>' : '';
      $title                .= 'Defined in class :  <span>'.$class.'</span><br/>';
      $title                .= 'Located in : <span>' .$located_in.'</span><br/>';
      $title                .= !empty($comments) ? 'Description : <span>'.$this -> tc_get_func_comments( $class , $function ).'</span><br/>' : '';
      $title                .= $filtrable_by ? "Filtrable with : <span>add_filter( '".$filtrable_by."' , '".str_replace('tc_', 'my_', $filtrable_by)."' )</span><br/>" : "";
      ?>
        <div class="tip-container" <?php echo isset($float)  ? 'style="float:'.$float.'"' : ''; ?> >
          <a style="color: <?php echo $tc_debug_tips_color ?>" class="debug-tip" data-placement="right" data-html="true" data-trigger="click" data-toggle="tooltip" title="<?php echo $title ?>"></a>
        </div>

      <?php

      tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
    }




    function tc_get_hooks_list( $path = null) {

       /* TC_BASE is the root server path */
      if ( ! defined( 'TC_BASE' ) )       { define( 'TC_BASE' , get_template_directory().'/' ); }

      $hooks    = array();

      $files      = scandir(TC_BASE.$path);
      
      foreach ( $files as $file) 
      {
          if ( $file[0] != '.' ) 
          {
              if ( is_dir(TC_BASE.$path.$file) ) 
              {
                  $hooks = array_merge( $hooks, $this -> tc_get_hooks_list( $path.$file.'/'));
              }

              else if ( substr( $file, -4) == '.php' ) {
                  
                //$hooks[] = $path.$file;
                $filelines = explode("\n", implode('', file(TC_BASE.$path.$file)));
                $read = false;
                $i = 0;

                foreach ($filelines as $line) {
                  $line_check = '$this';
                  //the check on $this avoid the selection of the lines below in hook list
                  if ( (strpos($line, 'add_action') || strpos($line, 'add_filter') ) && strpos($line, $line_check) ) {
                    //get action
                    $act        = substr($line, 0, strrpos($line, 'array'));
                    $to_delete  = array("add_action", "add_filter" , "/", " ", ",", "(", "'");
                    $act        = trim(str_replace($to_delete, "", $act));

                    //get hooked function
                    $func = '';
                    if (true == strpos($line, 'tc_' )) {
                      $end_car  = strlen($line) - (int)strpos($line, 'tc_' );
                      $func     = substr($line, (int)strpos($line, 'tc_' ) , $end_car);
                      $func     = substr($func, 0 , (int)strpos($func, ')'));
                      $to_delete  = array(")" , "," , "(", "'");
                      $func     = trim(str_replace($to_delete, "", $func));
                    }

                  $hooks[$act.'#'.$file]['type']    = strpos($line, 'add_action') ? 'action' : 'filter';
                  $hooks[$act.'#'.$file][]          = array(
                                                      'function'  => $func,
                                                      'class'     => tc_get_file_class($file)
                                                      );
                  $hooks[$act.'#'.$file]['name']    = $act;
                  $hooks[$act.'#'.$file]['group']   = tc_get_file_group($file);
                  //$hooks[$act.'#'.$file]['class']   = tc_get_file_class($file);
                  }//end if
                }//end foreach
              }//end if
          } //end if
      }//end for each

      return $hooks;
    }




    function tc_get_apply_filters_list( $path = null) {

       /* TC_BASE is the root server path */
      if ( ! defined( 'TC_BASE' ) )       { define( 'TC_BASE' , get_template_directory().'/' ); }

      $filters    = array();

      $files      = scandir(TC_BASE.$path);
      
      foreach ( $files as $file) 
      {
          if ( $file[0] != '.' ) 
          {
              if ( is_dir(TC_BASE.$path.$file) ) 
              {
                  $filters = array_merge( $filters, $this ->  tc_get_apply_filters_list( $path.$file.'/'));
              }

              else if ( substr( $file, -4) == '.php' ) {
                  
                //$hooks[] = $path.$file;
                $filelines = explode("\n", implode('', file(TC_BASE.$path.$file)));
                $read = false;
                $i = 0;

                foreach ($filelines as $line) {
                  //we don't want to list wrong filters!
                  $line_check_one = '$html';
                  $line_check_two = 'tc_';

                  //the check on $this avoid the selection of the lines below in hook list
                  if ( strpos($line, 'apply_filters')  && strpos($line, $line_check_one) && strpos($line, $line_check_two) ) {
                    //get filter
                    $filt           = substr($line, 0, strrpos($line, 'html'));
                    $to_delete      = array("apply_filters", "return" , "$", "echo" , ",",  " ", "(", "'");
                    $filt           = trim(str_replace($to_delete, "", $filt));

                    $filters[]      = $filt;
                    //$filters[$filt.'#'.$file]['group']   = tc_get_file_group($file);
                    //$hooks[$act.'#'.$file]['class']   = tc_get_file_class($file);
                  }//end if
                }//end foreach
              }//end if
          } //end if
      }//end for each

      return $filters;
    }




     /**
    * This function records the timeline
    *
    * @package Customizr
    * @since Customizr 3.0.10
    */
    function tc_record_story( $file, $function = null, $class = null ) {
      global $story;

       //isolate file name
      $end_car        = strlen($file) - (int)strpos($file, "/themes/");
      $file           = substr($file, (int)strpos($file, '/themes/' ) + 8 , $end_car);

      if ( isset($function) && !empty($function) ) {
        //get function hook infos
        $func_hook    = $this -> tc_get_func_hook($function);
      }

      $story[round( microtime(true)*1000)]  = array(
        'file'        => $file,
        'function'    => isset($function) ? $function : null,
        'class'       => isset($class) ? $class : null,
        'hook'        => isset($func_hook['hook']) ? $func_hook['hook'] : null,
        'type'        => isset($func_hook['type']) ? $func_hook['type'] : null,
        );
      return $story ;
    }





    /**
    * This function displays the timeline recorded in each function and exludes the debug function execution time
    *
    * @package Customizr
    * @since Customizr 3.0.10
    */
    function tc_display_story() {
      global $story;

      //sort by key
      ksort($story);

      $i = 1;
      foreach ($story as $key => $value) {
        
        $current                  = $key;
        $type                     = $story[$current]['type'];
        $hook                     = $story[$current]['hook'];
        $function                 = $story[$current]['function'];
        $class                    = $story[$current]['class'];
        $template                 = $story[$current]['file'];
        //$hook_type                = ( !is_null($type) ) ? $type : false;


        //set first loop data
        if (1 == $i) {
          $first_timestamp = $key;
          $time = 0;
          $debug_time = 0;
          $prev = 0;
        }
        else {
          $time = $current - $first_timestamp - $debug_time;
        }

        if ( 'TC_debug' != $class ) {
          //set content vars
          $comments     = $this -> tc_get_func_comments( $class , $function );

          $title        = '<u>Defined in class</u> :  '.$class.'<br/>';
          $title        .= '<u>Located in : </u>' .$template.'<br/>';
          $title        .= !empty($comments) ? '<u>Description : </u>'.$this -> tc_get_func_comments( $class , $function ) : '';

          printf('<p class="hooks %1$s">%2$s %3$s %4$s<p/>',
            (empty($function)) ? "action" : $type,
            ($i == 1) ? 'Sep #'.$i.' : <span class="info">'.$time.'ms</span>' : 'Step #'.$i.' : <span class="info">+'.$time.'ms</span>' ,
            !empty($function) ? ' | Function : <span class="info"><a class="debug-tip" data-html="true" data-placement="top" data-trigger="hover" data-toggle="tooltip" title="'.$title.'">'.$function.'()</a></span>' : ' | Template/file : <span class="info">'.$template.'</span>',
            (!empty($type) && !empty($hook)) ? '| Hooked on '.$type.' : <span class="info">'.$hook.'</span>' : ''
            );

          $debug_time = 0;
          $last = $time;
        }
        else {
          $debug_time = $time - $prev;
          $i = $i-1;
        }

        //record the time for next loop
        $prev = $time;

        $i++;
      }
      echo '<br/><p class="total">Total server execution time : <span class="info">'.$last.'ms</span></p>';
      tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
    }//end of function




    function tc_create_hook_tree() {

      global $groups;

      //create a clean and complete array of hooks
      foreach ($groups as $group) {
        foreach ($this -> hooks as $hook => $data) {
          if ($group == $data['group']) {
            unset($data['group']);
            $tree[$group][$hook] = empty($tree[$group][$hook]) ? array() : $tree[$group][$hook];
            $tree[$group][$hook] = wp_parse_args($tree[$group][$hook],$data);
            //unset($tree[$group][$hook]['type']);
            //$newhook[$name] = array_unique($newhook[$name]);
            //unset($newhook[$name]['name']);
            //array_push($newhook[$name],$data);
          }
        }
      }

     return $tree;
    }



    function tc_display_hook_tree($current = null) {
      $tree = $this -> hook_tree;

      $current_node = !empty($current) ? $current : $tree;

        foreach( $current_node as $key => $node )  {
          if (is_array($node) && !isset($node['function'])) {
            echo '<div>';
              if ( isset($node['type']) ) {
                echo $key.'('.$current_node[$key]['type'].')';
              }
              else {
                echo $key;
              }
              $this -> tc_display_hook_tree ($node);
            echo '</div>';
          }

          elseif ( is_array($node) && isset($node['function']) ) {
            $class      = 'TC_'.$node['class'];
            $function   = $node['function'];

            echo '<div class="hook-func">';
              echo '<span class="info"><a class="debug-tip" data-placement="top" data-trigger="hover" data-toggle="tooltip" title="'.$this -> tc_get_func_comments( $class , $function ).'">'.$class.'::'.$function.'()</a></span>';
            echo '</div>';
          }

      }//end foreach
      tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
    }//end function





    function tc_display_class_tree($current = null , $class = null ) {
      $tree = $this -> class_list;

      $current_node = !empty($current) ? $current : $tree;
        foreach( $current_node as $key => $node )  {
          if ( is_array($node) ) {
            echo '<div>';
              echo $key;
              //recursivity
              $this -> tc_display_class_tree ($node, $key);
            echo '</div>';
          }

          else {
            $function   = $node;
            echo '<div class="hook-func">';
              echo '<span class="info"><a class="debug-tip" data-placement="top" data-trigger="hover" data-toggle="tooltip" title="'.$this -> tc_get_func_comments( $class , $function ).'">'.$class.'::'.$function.'()</a></span>';
            echo '</div>';
          }
      }//end foreach
    }




    function tc_display_query_tree($current = null) {
      global $wp_query;

      $query_data           = array(
        "is_single",
        "is_preview",
        "is_page",
        "is_archive",
        "is_date",
        "is_year",
        "is_month",
        "is_day",
        "is_time",
        "is_author",
        "is_category",
        "is_tag",
        "is_tax",
        "is_search",
        "is_feed",
        "is_comment_feed",
        "is_trackback",
        "is_home",
        "is_404",
        "is_comments_popup",
        "is_paged",
        "is_admin",
        "is_attachment",
        "is_singular",
        "is_robots",
        "is_posts_page",
        "is_post_type_archive"
        );
                
      $tree = (array)$wp_query;
      
      $current_node = is_array($current) ? $current : $tree;

      foreach( $current_node as $key => $node )  {
        if ( is_object($node) ) {
          $current = (array)$node;
        }
        else {
          $current = $node;
        }
        //echo $key.' : '.$node.'<br/>';
        if ( !in_array($key , $query_data) ) {
          //$node = is_object($node) ? (array)$node : $node;
          if ( is_array($current) && !empty($current) ) {
           // $node = (array)$node;
            echo '<div>';
              echo $key;
              //recursivity
              $this -> tc_display_query_tree ($current);
            echo '</div>';
          }
          else {
            $current = is_string($current) ? strip_tags($current) : $current;
            $current = (is_string($current) && strlen($current) > 400) ? substr($current, 0, 400).'[...]' : $current;
            echo '<div class="hook-func">';
              echo $key.' => <span class="info">'.$current.'</span>';
            echo '</div>';
          }
        }
      }//end foreach
    }



    function tc_get_func_comments( $class , $function ) {
      tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
      $comments = '';
      if ( class_exists( $class ) ) {
        $rc         = new ReflectionClass($class);
        if ( method_exists( $class , $function ) ) {
          $method     = $rc -> getMethod($function);
          $comments   = $this -> tc_get_string( $method->getDocComment() , $start = '/**', $end = '@' );
        }
      }
      return str_replace( '*', '', $comments );
    }





    function tc_get_string($string, $start, $end){
       tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
       $string = " ".$string;
       $pos = strpos($string,$start);
       if ($pos == 0) return "";
       $pos += strlen($start);
       $len = strpos($string,$end,$pos) - $pos;

       return substr($string,$pos,$len);
    }

}//end of class
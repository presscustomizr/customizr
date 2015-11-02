<?php
/**
*
* This program is a free software; you can use it and/or modify it under the terms of the GNU
* General Public License as published by the Free Software Foundation; either version 2 of the License,
* or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
* even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*
* You should have received a copy of the GNU General Public License along with this program; if not, write
* to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*
* @package   	Customizr
* @subpackage 	functions
* @since     	1.0
* @author    	Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright 	Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link      	http://presscustomizr.com/customizr
* @license   	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/


/**
* This is where Customizr starts. This file defines and loads the theme's components :
* => Constants : CUSTOMIZR_VER, TC_BASE, TC_BASE_CHILD, TC_BASE_URL, TC_BASE_URL_CHILD, THEMENAME, TC_WEBSITE
* => Default filtered values : images sizes, skins, featured pages, social networks, widgets, post list layout
* => Text Domain
* => Theme supports : editor style, automatic-feed-links, post formats, navigation menu, post-thumbnails, retina support
* => Plugins compatibility : JetPack, bbPress, qTranslate, WooCommerce and more to come
* => Default filtered options for the customizer
* => Customizr theme's hooks API : front end components are rendered with action and filter hooks
*
* The method TC__::tc__() loads the php files and instanciates all theme's classes.
* All classes files (except the class__.php file which loads the other) are named with the following convention : class-[group]-[class_name].php
*
* The theme is entirely built on an extensible filter and action hooks API, which makes customizations easy and safe, without ever needing to modify the core structure.
* Customizr's code acts like a collection of plugins that can be enabled, disabled or extended.
*
* If you're not familiar with the WordPress hooks concept, you might want to read those guides :
* http://docs.presscustomizr.com/article/26-wordpress-actions-filters-and-hooks-a-guide-for-non-developers
* https://codex.wordpress.org/Plugin_API
*/


//Fire Customizr
require_once( get_template_directory() . '/inc/init.php' );

/**
* THE BEST AND SAFEST WAY TO EXTEND THE CUSTOMIZR THEME WITH YOUR OWN CUSTOM CODE IS TO CREATE A CHILD THEME.
* You can add code here but it will be lost on upgrade. If you use a child theme, you are safe!
*
* Don't know what a child theme is ? Then you really want to spend 5 minutes learning how to use child themes in WordPress, you won't regret it :) !
* https://codex.wordpress.org/Child_Themes
*
* More informations about how to create a child theme with Customizr : http://docs.presscustomizr.com/article/24-creating-a-child-theme-for-customizr/
* A good starting point to customize the Customizr theme : http://docs.presscustomizr.com/article/35-how-to-customize-the-customizr-wordpress-theme/
*/


/*if ( ! class_exists( 'TC_Controllers' ) ) :
  class TC_Controllers {
    static $instance;

  }
endif;//class_exists*/
//CZR() -> collection -> tc_change( 'joie', array('template' => '', 'html' => '<h1>Yo Man this is a changed view</h1>', 'view_class' => '') );
//CZR() -> collection -> tc_delete( 'joie');
//add_action('wp' , 'register_test_views');

function register_test_views() {

  CZR() -> collection -> tc_register(
    array(
      'hook'        => '__after_header',
      'id'          => 'joie',
      'template'    => 'custom',
      'view_class'  => 'TC_test_view_class',
      'early_setup' => 'TC_test_early_setup',
      'children' => array(
        'child1' => array(
            'hook'        => 'in_custom_template',
            'html'        => '<h2 style="color:green">I AM A CHID VIEW</h2>'
        ),
        'child2' => array(
            'hook'        => 'in_custom_template',
            'html'        => '<h2 style="color:purple">I AM ANOTHER CHID VIEW</h2>'
        )
      )
    )
  );

  CZR() -> collection -> tc_register(
    array( 'hook' => '__after_header', 'html' => '<h1>Yo Man this is some html to render</h1>' )
  );
  CZR() -> collection -> tc_register(
    array( 'hook' => '__after_header', 'callback' => array( 'TC_rendering', 'callback_met') )
  );
  CZR() -> collection -> tc_register(
    array( 'hook' => '__after_header', 'callback' => 'callback_fn', 'cb_params' => array('custom1', 'custom2') )
  );
}



// Fire Customizr
//CZR();

//Create a new test view
CZR() -> collection -> tc_register(
  array( 'hook' => '__after_header', 'template' => 'custom',  'html' => '<h1>Yo Man this some html to render 1</h1>' )
);


//CZR() -> collection -> tc_delete( 'joie');






function callback_fn( $text1 = "default1", $text2 = "default2"  ) {
  ?>
    <h1>THIS IS RENDERED BY A CALLBACK FUNCTION WITH 2 OPTIONAL PARAMS : <?php echo $text1; ?> and <?php echo $text2; ?></h1>
  <?php
}




add_action('__after_header' , function() {
  ?>
    <pre>
      <?php print_r( CZR() -> collection -> tc_get() ); ?>
    </pre>
  <?php
}, 100);



class TC_test_view_class extends TC_View {
  public $test_class_property = 'YOUPI';
  function __construct( $model = array() ) {
    $keys = array_keys( get_object_vars( parent::tc_get_instance() ) );
    foreach ( $keys as $key ) {
      if ( isset( $model[ $key ] ) ) {
        $this->$key = $model[ $key ];
      }
    }
  }

  /*public function tc_render() {
    ?>
      <h1>MY ID IS <span style="color:blue"><?php echo $this -> id ?></span>, AND I AM RENDERED BY THE VIEW CLASS</h1>
    <?php
  }*/
}





class TC_rendering {
  function callback_met( $text1 = "default1", $text2 = "default2"  ) {
    ?>
      <h1>THIS IS RENDERED BY A CALLBACK METHOD IN A CLASS, WITH 2 OPTIONAL PARAMS : <?php echo $text1; ?> and <?php echo $text2; ?></h1>
    <?php
  }
}



//@todo : children it would be good to add actions on pre_render_view, where we are in the parent's hook action.
//=> late check if new children have been registered
//=> if so, instanciate their views there
//
//@todo : the id could be based on the id, then template name, then hook_priority
//
//@todo : for logged in admin users, add a line of html comment before and after the view giving id, hook, priority
//
//@todo : move tc_apply_registered_changes_to_instance into the model ?
//
//@toco : pre_render_view stuffs














/*add_action('__after_footer' , function() {
  $args = array(
    'post_type' => array('post'),
    'post_status' => array('publish'),
    'posts_per_page'         => 10
  );

  tc_new(
    array('content' => array( array('inc/parts', 'loop_base') ) ),
    array(
      'name' => '',
      'query' => new WP_Query( $args )
    )
  );
});*/

/*add_action( 'parse_query' , function($query) {
  $test = array(
    'is_single' => $query -> is_single(),
    'is_home' => $query -> is_home()
  );

   if ( is_array() )
    array_walk_recursive(, function(&$v) { $v = htmlspecialchars($v); });
  ?>
    <pre>
      <br/><br/><br/><br/><br/><br/>
      <?php print_r($test); ?>
    </pre>
  <?php
  //wp_die();
});*/

// add_action('init' , function() {
//   $args = array(
//     'post_type' => array('post'),
//     'post_status' => array('publish'),
//     'posts_per_page'         => 3
//   );

//   tc_new(
//     array('content' => array( array('inc/parts', 'post_list') ) ),
//     array(
//       '_singleton' => false,
//       'loop_name' => 'custom-grid',
//       'query' => new WP_Query( $args ),
//       'render_on_hook' => '__after_header'
//     )
//   );
// });
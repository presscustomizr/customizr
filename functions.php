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
* @since     	1.0
* @author    	Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright 	Copyright (c) 2013-2016, Nicolas GUILLAUME
* @link      	http://presscustomizr.com/customizr
* @license   	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/


/**
* This is where Customizr starts. This file defines and loads the theme's components :
* => Constants : CUSTOMIZR_VER, TC_BASE, TC_BASE_CHILD, TC_BASE_URL, TC_BASE_URL_CHILD, THEMENAME, CZR_WEBSITE
* => Default filtered values : images sizes, skins, featured pages, social networks, widgets, post list layout
* => Text Domain
* => Theme supports : editor style, automatic-feed-links, post formats, navigation menu, post-thumbnails, retina support
* => Plugins compatibility : JetPack, bbPress, qTranslate, WooCommerce and more to come
* => Default filtered options for the customizer
* => Customizr theme's hooks API : front end components are rendered with action and filter hooks
*
* The method CZR__::czr_fn__() loads the php files and instantiates all theme's classes.
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
require_once( apply_filters( 'czr_init', get_template_directory() . '/inc/czr-init.php' ) );

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


/*

** Funding principle: **
We want to have a modular theme which allows us to print a template injected with a certain data model.


*** Models ***

Models are the core of the whole framewoks. They assolve different purposes as:
- setup the data needed to feed the template
- act as controllers (in an MVC pattern), as they're responsible of the View instantiation, whose purpose is mostly to just push the model's in the stack
of the current models (to make it accessible through the czr_fn_(get|echo) apis ) and render the model's template (a file, or a pure html code),

Model's nature is flexible enough to allow us to treat it as:
a) a singleton
b) a single instance

a) Singletons
We generally use the singleton's approach for those models which are used pagewide, which do not have to necessarily retain data
(e.g. post metas) or for performance reasons, like items (articles) and their components (image, text, post formats custom post metas...),

b) Single instance
This approach is used mostly for the grids. Each grid has its own life and defines its own 'environment' at instantiation time (constructor).

Nothing prevents the developer to use a model in both ways, so its up to the developer to prepare the stage before using them.

Due to this dual nature of the models, and to the fact that a template can be feed with a generic data model (see Templates section below), we need a way to setup the models
with a set of params both at instantiation time (single instance) and at rendering time (singletons, generally in a loop),
This purpose is accomplished by passing an array of arguments to the model with which we want to feed a template.

This array of args can be ( by default is ) merged to the model's $defaults array or properties, when updating a model.
This approach allows us, to reset to $defaults a singleton model just before its template's rendering (see Czr_Model:czr_fn_update() used in czr_fn_render_template).

Single instance kind of models can override a base class method to define a preset of properties that will be merged to the passed arguments at
instantiation phase. This "preset" is not persistent.
This approach is typically used for the grids.
Each grid defines a preset generally based on the user's options (display the thumbnail, center the images, excerpt length...).
This represents just the initial state of the grid.

By the nature of WordPress most of the actual data models properties need to be setup just before the rendering (e.g in the loop).
For this purpose each model can override a base method called czr_fn_setup_late_properties (action callback fired by the model's View just before rendering).


* Registration *
Each model needs to be registered in the collection of models, each model then can be re-used (see singletons above) by referring to its model id.
When rendering a template on the fly, if its model is not registered yet, a registration on the fly occurs.

Technically we should not need to register a model before its template rendering request, but in most of the cases we have to:
a) when a model has to enqueue assets or append specific css rules to the inline style (czr_user_options_style)
b) when a model X needs to know something about the model Y before both of them are actually rendered.
(e.g. when the alternate grid item needs to know whether or not the current post has a media to show, in order to set the text and media columns)
c) when a model X wants to alter some properties of Y before Y and X rendering.

Model's can be pre-registered (meaning registered before wp action hook) e.g. when they have to alter the main query (pre_get_posts).
We actually never do this atm.

The model's registration (as well as its view instantiation and its template rendering) can be subordered to various checks
e.g. on its integrity (is the model well formed?) and to what we call the model's controller, mostly consisting in a callback
which checks whether or not we are in the right context (post lists, singular, search ...) and/or user options allow it
(e.g. post metas/breadcrumbs/slider... visibility)



*** Templates ***

Templates mostly consist of html, and minimal php code to retrieve data from their own model.
They do not necessarily need to be a file, but in most of the cases they are, as we can instruct a model
to render pure html code (model's property $html )

A template can be:
a) rendered on fly ( core/functions.php czr_fn_render_template(...) ) directly from another template
b) rendered at a specific hook and priority

A template can access models data through two functions
1) czr_fn_get( property, model_id(optional, default is the current model), args(optional) ) - gets a model's property
2) czr_fn_echo( property, model_id(optional, default is the current model), args(optional) ) - echoes a model's property

Each model object can define the way a property is retrieved by defining a method with the following signature
czr_fn_get_{$property}( $args )
This is particularly useful for those properties which need to be computed on the fly (e.g. in a loop)

Even if we render model's templates, templates do not necessarily need a specific pre-associated model class. In case there's no corrispondent model class for a template,
the base Czr_Model object will be registered and instantiated, assigning the model's property "template" as the relative
file path of the template we're about to render.



*** Loading a page ***
In core/init.php at wp action hook we register the main set of models that we'll render afterwards from our main templates (main-templates/index.php|header.php etc. etc.)
This set of models consists of:
1) the header
2) the content (contains the logic to retrieve which model/template needs to be passed to the loop)
3) the footer

Each model then can register, at instantiation time, a set of children models.
For instance, in the content model we register the grid wrapper or the slider's model as they need to act on the user options style.

See main-templates/index.php for rendering flow.
*/
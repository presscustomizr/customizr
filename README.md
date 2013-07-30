![Customizr - Free Wordpress Theme](/screenshot.png)  

*Just enjoy designing your website live from the WP customizer screen. Choose your options  : skin, logo, social profiles, slider, layout, home featured blocks... you can even customize your css live. And this is it! The clean and fully responsive design can be used for any type of website: corporate, portfolio, business, blog, landing page, etc. The theme also includes a responsive slider generator (with call to action text and button) to make your pages or posts look beautiful. Customizr is built with HTML5 and CSS3 upon the Twitter Bootstrap framework. Customizr is translation ready and available in english, french, german, russian and spanish.*


# Installation
1. Upload the `customizr` folder to the `/wp-content/themes/` directory
Activation and Use
1. Activate the Theme through the 'Themes' menu in WordPress
2. See Appearance -> Customiz'it to change theme options

# User Guide and Main Features
## WP Version
Since this theme uses the WordPress customizer feature for most options, it requires at least version 3.4 to work properly.
Please make sure your WP version is at least 3.4.

## Choosing a skin 
The theme comes with seven skins. The default skin is the blue one. Open the customizer screen to select your prefered skin.

## Uploading a logo and a favicon 
Go to the customizer screen in the logo and favicon section and upload your images.
For best results, try uploading a logo with the following maximum dimensions => max-height :100px, max-width ; 250px

## Home page settings 
All settings for home page are available in the customizer screen. You can :
* Select a static page/ your lasts posts (the number of posts per page can be changed in the page and post layout section),
* Choose a layout for your sidebars : full-width, left, right, two sidebars,
* Select a slider among the ones you have created (see below for slider creation) and choose the slider options : full-width or boxed, delay between slides. To remove the demo slider : select "No slider" in the dropdown list.
* Set your home featured pages and text (you can also disable this feature). To remove the featured pages area : select "Hide" in the dropdown list.

## Selecting a menu 
Customizr comes with one menu location in the header. Select a menu in the customizr screen => menu section

## Pages and posts layout 
With Customizr, you can define your page and post layout at two levels :
1. In the main option screen (Customizer screen) : you can define the global default layout for all your website, for all your posts and for all your pages. Four choices are available for the sidebars location : full-width (no sidebars), left, right, two sidebars.  
2. In each post or page editing screen, you can define a particular layout in a dedicated box: full-width (no sidebars), left, right, two sidebars.

## Social links 
You can set up to nine social network profile in the customizer option screen. Write the url of your social profile in the fields.
The social icons can be displayed in four predefined location of your website : header, footer, top of right sidebar, top of left sidebar.

## Slider management
### Creation
1. Go to the edit screen of any image
2. Find the Slider options box
3. Check "Add to a slider"
4. Set the optional call to action fields : title, description, text of the button and link to page/post
5. Write a name and click on the "Add a slider" button : this will automatically add the current media to your slider (no need to refresh)

### Adding images to a slider 
1. Go to the edit screen of any image
2. Find the Slider options box
3. Check "Add to a slider"
4. Set the optional call to action fields : title, description, text of the button and link to page/post
5. Select a slider in the list

### Reordering the slides
The slides can be manually reorder with a drag and drop feature.

### Adding a slider to a post/page 
1. Go to the edit screen of any page/post
2. Find the slider options box
3. Check "Add a slider"
4. Select a slider
5. Change the delay and layout field if needed
6. Update the page

### Deleting a slider 
In the Slider options box, at the top of the slides table, click on "Delete this slider".
This action will delete the slider but not the images.

### Post Formats
You can choose up to 10 post formats for your posts.  
Posts with the aside, status, quote and link post formats are displayed with no title.  
Posts with the link post format will link out to the first a tag in the post.



# Notes for developers
## Code logic
The theme is built on a classes framework. The classes are identified by their group and name like this : class-[group]-[name].php.

The function tc__( $group, $classname), where the group parameter is required :
1) scans the theme folder to find the appropriate group / class 
2) and then instanciates the class(es) only once through a singleton factory.

A class typically includes a constructor which is mainly used to add the methods to WP actions and filters. 
Actions are used to render HTML or execute some code in predefined WP action, while filters are used to get values.

For simplification purposes, the theme uses few WP templates : index.php, header.php, footer.php, comments.php, sidebar(s).php. 
Those templates only includes some structural HTML markup, the rest is rendered with the actions defined in the classes of the parts/ folder.

Customizr uses one single loop for all kind of content. It is located in class-main-loop.php.


## Translation
The themes is ready for translation and already translated in french. Translation files are in /lang.



# License
Unless otherwise specified, all the theme files, scripts and images
are licensed under GNU General Public License version 2, see file license.txt.
The exceptions to this license are as follows:
* Bootstrap by Twitter and the Glyphicon set are licensed under the GPL-compatible [http://www.apache.org/licenses/LICENSE-2.0 Apache License v2.0]
* The script bootstrap-carousel.js v2.3.0 is licensed under the Apache License
* The script holder.js v1.9 is licensed under the Apache License
* The script modernizr.js is dual licensed under the BSD and MIT licenses
* The script jquery.iphonecheck.js is copyrighted by Thomas Reynolds, licensed GPL & MIT
* The script jquery.fancybox-1.3.4.js is dual licensed under the MIT and GPL licenses
* Icon Set:	Entypo is licensed under SIL Open-Font License
* The image phare.jpg is a free public picture from Wikimedia, copyright 2013 Alf van Beem (http://commons.wikimedia.org/wiki/File:Ca_1941_DAF_%27Rijdende_regenjas%27_pic7.JPG) , and distributed under the terms of the Creative Commons CC0 1.0 Universal Public Domain Dedication (http://creativecommons.org/publicdomain/zero/1.0/deed.en)
* The image chevrolet.jpg is a free public picture from Wikimedia, copyright 2013 Alf van Beem (http://commons.wikimedia.org/wiki/File:%2755_Chevrolet_ornament.JPG) , and distributed under the terms of the Creative Commons CC0 1.0 Universal Public Domain Dedication (http://creativecommons.org/publicdomain/zero/1.0/deed.en)
* The image ampoules.jpg is a free public picture from Wikimedia, copyright 2010 Medvedev (http://commons.wikimedia.org/wiki/File:E24_E14_E10.jpg) , and distributed under the terms of the Creative Commons CC0 1.0 Universal Public Domain Dedication (http://creativecommons.org/publicdomain/zero/1.0/deed.en)




# Changelog
= 3.0.5 =
* fixed : breadcrumb translation domain was not right
* fixed : domain translation for comment title was not set
* fixed : in v3.0.4, a slider could disappeared only if some slides had been inserted at one time and then deleted or disabled afterward. Thanks to Dave http://wordpress.org/support/profile/rdellconsulting!
* fixed : holder.js script bug in IE v8 and lower. Fixed by updating holder.js v1.9 to v2.0. Thanks to Joel (http://wordpress.org/support/profile/jrisberg) and Ivan (http://wordpress.org/support/profile/imsky).
* improved : better handling of comment number bubble everywhere : check if comments are opened AND if there are comments to display
* improved : welcome screen on update/activate : changelog automatic update, new tweet button
* improved : lightbox navigation is now enabled for galleries with media link option choosen (new filters on post gallery and attachment_link)
* improved : better code organization : split of content class in specific classes by content type
* added : customizr option for images : enable/disable autoscale on lightbox zoom
* added : jQuery fallback for CSS Transitions in carousel (ie. Internet Explorer) : https://github.com/twbs/bootstrap/pull/3052/files
* added : spanish translation. Thanks to Maria del Mar


= 3.0.4 =
* fixed : minor css correction on responsive thumbnail hover effect
* fixed : minor syntaxic issue on comment title (printf)
* fixed : translation domain was wrong for social networks
* fixed : slider arrows were still showing up if slides were deleted but not the slider itself. Added a routine to check if slides have attachment.
* improved : image galleries : if fancybox active, lightbox navigation is now enabled
* improved : better capability control of edit page button. Only appears if user_can edit_pages (like for posts)
* added : Activation welcome screen
* added : new action in admin_init hook to load the meta boxes class


= 3.0.3 =
* added : german translation. Thanks to Martin Bangemann <design@humane-wirtschaft.de> !
* changed : default option are now based on customizer settings
* fixed : reordering slides was deleting the slides


= 3.0.2 =
* fixed : problem fixed on theme zipping and upload in repository 


= 3.0.1 =
* fixed : "header already sent" error fixed (space before php opening markup in an admin class) was generating an error on log out  

= 3.0 =
* changed : global code structure has changed. Classes are instanciated by a singleton factory, html is rendered with actions, values are called through filters
* fixed : favicon rendering, $__options was not defined in head
* fixed : sidebars reordering on responsive display, tc_script.js file


= 2.1.8 =
* changed : activation options are disable for posts_per_page and show_on_front
* changed : redirect to customizr screen on first theme activation only


= 2.1.7 =
* fixed : home page slider was checking a $slider_active variable not correctly defined
* fixed : slider name for page and post was only ajax saved. Now also regular save on post update.


= 2.1.6 =
* improved : Menu title padding
* fixed : front office : page and post sliders could not be disable once created
* removed : some unnecessary favicon settings
* fixed : function wp_head() moved just before the closing <head> tag
* added : filter on wp_filter function
* added : russion translation, thanks to Evgeny Sudakov!
* improved : thumbnail and content layout for posts lists
* fixed : ajax saving was not working properly for page/page slider, a switch case was not breaked.



= 2.1.5 =
* fixed 	: When deleted from a slider, the first slide was not cleared out from option array
* added 	: Titles in customizer sections
* added 	: checkbox to enable/disable featured pages images
* added 	: Optional colored top border in customizer options
* added 	: new black skin
* removed 	: text-rendering: optimizelegibility for hx, in conflict with icon fonts in chrome version 27.0.1453.94
* improved 	: blockquote styling
* fixed 	: in tc_script.js insertbefore() target is more precise
* improved 	: font icons are now coded in CSS Value (Hex)
* added 	: add_action hooks in the templates index and sidebars


= 2.1.4 =
* fixed : in tc_meta_boxes.php, line 766, a check on the existence of $slide object has been added
* fixed : iframe content was dissapearing when reordering divs on resize. Now  handled properly in tc_scripts.js
* fixed : breadcrumb menu was getting covered (not clickable) in pages. fixed in css with z-index.
* fixed : thumbnails whith no-effect class are now having property min-height:initial => prevents stretching effect
* fixed : revelead images of featured page were stretched when displayed with @media (max-width: 979px) query
* fixed : better vertical alignment of the responsive menu
* changed : color of slider arrows on hover
* changed : text shadow of titles
* changed : color and shadow of site description

= 2.1.3 =
* fixed : in tc_voila_slider, jump to next loop if attachment has been deleted
* removed : title text in footer credit
* fixed : image in full width slider are displayed with CSS properties max-width: 100%, like before v2.0.9

= 2.1.2 =
* fixed : new screenshot.png

= 2.1.1 =
* fixed : new set of images licensed under Creative Commons CC0 1.0 Universal Public Domain Dedication (GPL Compatible)


= 2.1.0 =
* fixed : slide was still showing up when 'add to a slider' button was unchecked and a slider selected
* fixed : new set of images with compliant licenses


= 2.0.9 =
* replaced : jquery fancybox with a GPL compatible version
* removed : icon set non GPL compatible
* added : icon sets Genericons and Entypo GPL compatible
* fixed : image in full width slider are now displayed with CSS properties height:100% et width: auto
* added : function hooked on wp_head to render the custom CSS


= 2.0.8 =
* removed : minor issue, the function tc_write_custom_css() was written twice in header.php

= 2.0.7 =
* fixed : custom featured text (for featured pages) on front page was not updated when updated from customizer screen
* fixed : title of page was displayed when selected as static page for front page
* fixed : border-width of the status post-type box
* added : custom css field in customizer option screen
* added : lightbox checkbox option in customizer option screen

= 2.0.6 =
* added : new customizer option to enable/disable comments in page. Option is tested in index.php before rendering comment_templates for pages
* fixed : in the stylesheets, the right border of tables was unnecessary

= 2.0.5 =
* fixed : printf php syntax in footer.php

= 2.0.4 =
* fixed : test on current_user_can( 'edit_post' ) in template part content-page.php was generating a Notice: Undefined offset: 0 in ~/wp-includes/capabilities.php on line 1067
* added : copyright and license declaration in style.css

= 2.0.3 =
* fixed : same unique slug as prefix for all custom function names, classes, public/global variables, database entries.

= 2.0.2 =
* fixed : CSS image gallery navigation arrows
* removed : the_content() in attachment templates
* fixed : bullet list in content now visible
* added : hover effect on widget lists
* fixed : skin colors when hovering and focusing form fields
* fixed : skin colors when hovering social icons

= 2.0.1 =
* Removal of meta description (plugin territory)
* Page edit button is only visible for users logged in and with edit_post capabilities

= 2.0 =
* Replacement of the previous custom post type slider feature (was plugin territory) with a custom fields and options slider generator  
* Addition of ajax powered meta boxes in post/page/attachment for the sliders

= 1.1.7 =
* file structure simplification : one core loop in index.php

= 1.1.6 =
* Removal of add_editor_style()
* Addition of image.php and content-attachemnt.php for the images galleries and attachement rendering

= 1.1.5 =
* Sanitization of home_url() in some files (with esc_url)
* Clearing of warning message in slides list : check on the $_GET['action'] index
* Addition of some localized strings
* Removal of the optional WP footer credit links

= 1.1.4 =
* addition of selected() and checked() functions in metaboxes input
* better sanitization of WP customizer inputs : 3 sanitization callbacks added in tc_customizr_control_class for number, textarea and url

= 1.1 =
* Better stylesheets enqueuing
* Fix the quick mode edit for slide custom post : add a script to disable the clearing of metas fields on update
* Add a fallback screen on activation if WP version < 3.4 => WP Customizer not supported
* Fix the slide caption texts rendering change the conditions (&& => ||)

= 1.0 =
* Initial Release

# Copyright
**Customizr** is a free WordPress theme designed by Nicolas Guillaume in Nice, France. ([website : Themes and Co](http://www.themesandco.com>))  
Feel free to use, modify and redistribute this theme as you like.
You may remove any copyright references (unless required by third party components) and crediting is not necessary, but very appreciated... ;-D.  
Customizr is distributed under the terms of the GNU GPL.  

Enjoy it!
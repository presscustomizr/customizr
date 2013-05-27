###################### Copyright ######################
Customizr is a free WordPress theme designed by Nicolas Guillaume in Nice, France. (www.themesandco.com)
Feel free to use, modify and redistribute this theme as you like.
You may remove any copyright references (unless required by third party components) and crediting is not necessary, but very appreciated... ;-D.
Customizr is distributed under the terms of the GNU GPL
Enjoy it!



##################### Installation #####################
1. Upload the `customizr` folder to the `/wp-content/themes/` directory
Activation and Use
1. Activate the Theme through the 'Themes' menu in WordPress
2. See Appearance -> Customiz'it to change theme options



############# User Guide and Main Features #############
=== WP Version ===
As this theme uses the WordPress customizer feature for most options, it requires at least version 3.4 to work properly.
Please make sure your WP version is at least 3.4.

=== Choose a skin ===
The theme comes with seven skins. The default skin is the blue one. Open the customizer screen to select your prefered skin.

=== Upload a logo and a favicon ===
Go to the customizer screen in the logo and favicon section and upload your images.
For best results, try uploading a logo with the following maximum dimensions => max-height :100px, max-width ; 250px

=== Home page settings ===
All settings for home page are available in the customizer screen. You can :
- Select a static page/ your lasts posts (the number of posts per page can be changed in the page and post layout section),
- Choose a layout for your sidebars : full-width, left, right, two sidebars,
- Select a slider among the ones you have created (see below for slider creation) and choose the slider options : full-width or boxed, delay between slides. To remove the demo slider : select "No slider" in the dropdown list.
- Set your home featured pages and text (you can also disable this feature). To remove the featured pages area : select "Hide" in the dropdown list.

=== Select a menu ===
Customizr comes with one menu location in the header. Select a menu in the customizr screen => menu section

=== Pages and posts layout ===
With Customizr, you can define your page and post layout at two levels :
1- In the main option screen (Customizer screen) : you can define the global default layout for all your website, for all your posts and for all your pages. Four choices are available for the sidebars location : full-width (no sidebars), left, right, two sidebars.

2- In each post or page editing screen, you can define a particular layout in a dedicated box: full-width (no sidebars), left, right, two sidebars.

=== Social links ===
You can set up to nine social network profile in the customizer option screen. Write the url of your social profile in the fields.
The social icons can be displayed in four predefined location of your website : header, footer, top of right sidebar, top of left sidebar

=== Slider creation ===
1 - Go to the edit screen of any image
2 - Find the Slider options box
3 - Check "Add to a slider"
4 - Set the optional call to action fields : title, description, text of the button and link to page/post
5 - Write a name and click on the "Add a slider" button : this will automatically add the current media to your slider (no need to refresh)

=== Add images to a slider ===
1 - Go to the edit screen of any image
2 - Find the Slider options box
3 - Check "Add to a slider"
4 - Set the optional call to action fields : title, description, text of the button and link to page/post
5 - Select a slider in the list

=== Reorder the slides ===
The slides can be manually reorder with a drag and drop feature.

=== Add a slider to a post/page ===
1 - Go to the edit screen of any page/post
2 - Find the slider options box
3 - Check "Add a slider"
4 - Select a slider
5 - Change the delay and layout field if needed
6 - Update the page

=== Delete a slider ===
In the Slider options box, at the top of the slides table, click on "Delete this slider".
This action will delete the slider but not the images.

=== Post Formats ===
You can choose up to 10 post formats for your posts.
Posts with the aside, status, quote and link post formats are displayed with no title.
Posts with the link post format will link out to the first <a> tag in the post.



################## Notes for developers ################
=== Customizr loop ===
For the development of this theme, I created only one loop for all kind of content. It is located in index.php.
I also made the choice to use template parts instead of using the template hierarchy.

1 - The loop calls the main template : article-content.php
2 - The templates parts are then called in article-content.php, depending on the post type or context.

=== Translation ====
The themes is ready for translation and already translated in french. Translation files are in /lang.




####################### Licenses ########################
Unless otherwise specified, all the theme files, scripts and images
are licensed under GNU General Public License version 2, see file license.txt.
The exceptions to this license are as follows:
* Bootstrap by Twitter and the Glyphicon set are licensed under the GPL-compatible [http://www.apache.org/licenses/LICENSE-2.0 Apache License v2.0]
* The script bootstrap-carousel.js v2.3.0 is licensed under the Apache License
* The script holder.js v1.9 is licensed under the Apache License
* The script modernizr.js is dual licensed under the BSD and MIT licenses
* The script jquery.iphonecheck.js is copyrighted by Thomas Reynolds, licensed GPL & MIT
* The script jquery.fancybox-1.3.4.js is dual licensed under the MIT and GPL licenses
* Icon Set:	IcoMoon - Free -- http://keyamoon.com/icomoon/ License:	CC BY-SA 3.0 -- http://creativecommons.org/licenses/by-sa/3.0/
* Icon Set:	Broccolidry -- http://dribbble.com/shots/587469-Free-16px-Broccolidryiconsaniconsetitisfullof-icons License: Aribitrary -- http://licence.visualidiot.com/
* Icon Set:	Iconic -- http://somerandomdude.com/work/iconic/ License:	CC BY-SA 3.0 -- http://creativecommons.org/licenses/by-sa/3.0/us/


#####################  Image credits #####################
The image architecture.jpg is free for personal and commercial use. credits : Anita Berghoef, www.anitaberghoef.nl
The image columns.jpg is free for personal and commercial use. credits : Miguel Saavedra
The image laverie.jpg is free for personal and commercial use. credits : Philip MacKenzie, http://www.phillmackenzie.co.uk


#######################  Changelog ######################
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
* Replace the previous custom post type slider feature (was plugin territory) with a custom fields and options slider generator
* Addition of ajax powered meta boxes in post/page/attachment for the sliders

= 1.1.7 =
* file structure simplification : one core loop in index.php
* 

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


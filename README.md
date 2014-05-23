![Customizr - Free Wordpress Theme](/screenshot.png)  

*Enjoy designing your website live from the WP customizer : skin, logo, social profiles, slider, layout, home featured blocks, or even live css styling. The flat and fully responsive design can be used for small businesses, portfolios, blogs, corporate sites or landing pages. Engage your visitors with beautiful sliders and call to actions on any page or post. Built with valid HTML5 and CSS3 (from the Twitter Bootstrap), cross-browser tested, the theme is translation ready and available in 14 languages. Ready for WooCommerce, bbPress, qTranslate (and others), the code is easily extensible with a comprehensive API hooks.*

# Copyright
**Customizr** is a free WordPress theme designed by Nicolas Guillaume in Nice, France. ([website : Themes and Co](http://www.themesandco.com>))  
Feel free to use, modify and redistribute this theme as you like.
You may remove any copyright references (unless required by third party components) and crediting is not necessary, but very appreciated... ;-D.  
Customizr is distributed under the terms of the GNU GPL v2.0 or later


# Documentation and FAQs
* DOCUMENTATION : http://themesandco.com/customizr
* FAQs : http://themesandco.com/customizr/faq
* SNIPPETS : http://themesandco.com/code-snippets/
* HOOKS API : http://www.themesandco.com/customizr/hooks-api/


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
= 3.1.14 =
* added : (js : theme-customizer-control.js, css : theme-customizer-control.css, php : class-admin-customize.php) Donate block can now be disabled forever in admin.


= 3.1.13 =
* added : (lang) Danish translation. Thanks to <a href="http://teknikalt.dk">Peter Wiwe</a>
* added : (css, js) Donate link in admin


= 3.1.12 =
* fixed : (css) category archive icon now displayed again in chrome
* fixed : (php : TC_init::tc_add_retina_support) retina bug fixed by <a href="http://wordpress.org/support/profile/electricfeet" target="_blank">electricfeet</a>
* improved : (php : TC_breadcrumb ) breadcrumb trail for single posts, category and tag archive now includes the page_for_posts rewrited if defined.
* improved : (php) Better handling of the comment reply with the add_below parameter. Thanks to <a href="http://www.themesandco.com/author/eri_trabiccolo/">Rocco</a>.
* improved : (php) TC_Utils::tc_get_option() returns false if option not set
* removed : (php) Customiz'it button has been taken off


= 3.1.11 =
* added : (php , css) customizer : new option in the Skin Settings, enable/disable the minified version of skin
* added : (php) customizer : new option in the Responsive Settings, enable/disable the automatic centering of slides
* added : (js, php) automatic centering of the slider's slides on any devices. Thanks to <a href="http://www.themesandco.com/author/eri_trabiccolo/">Rocco</a>.
* improved : (css) skins have been minified to speed up load time (~ saved 80Ko)
* improved : (php) logo and favicon are now saved as relative path => avoid server change issues.
* improved : (php) better class loading. Check the context and loads only the necessary classes.
* improved : (php) customizer map has been moved into the class-fire-utils.php
* improved : (php) performance improvement for options. Default options are now generated once from the customizer map and saved into database as default_options
* improved : (js) block repositioning is only triggered on load for responsive devices
* updated : (translation) Slovak translation has been updated. Thanks to <a href="www.pcipservis.eu">Michal Hranicky</a>.


= 3.1.10 =
* fixed : (php : TC_init::tc_plugins_compatibility() , custom-page.php) WooCommerce compatibility issue fixed.
* added : (TC_customize::tc_customize_register() , TC_resources::tc_enqueue_customizr_scripts() , tc_script.js ) New option in customizer : Enable/Disable block reordering for smartphone viewport.


= 3.1.9 =
* fixed : (js  : tc_scripts.js , php : index.php ) responsive : dynamic content block position bug fixed in tc_script.js, the wrapper had to be more specific to avoid block duplication when inserting other .row inside main content. Thanks to <a href="http://www.themesandco.com/author/eri_trabiccolo/" target="_blank">Rocco Aliberti</a>.
* fixed : (php : TC_resources::tc_enqueue_customizr_scripts() ) comment : notice on empty archives due to the function comments_open(). A test on  0 != $wp_query -> post_count has been added in TC_resources::tc_enqueue_customizr_scripts(). Thanks to <a href="http://www.themesandco.com/author/eri_trabiccolo/" target="_blank">Rocco Aliberti</a>.
* improved : (js  : tc_scripts.js) responsive : the sidebar classes are set dynamically with a js localized var using the tc_{$position}_sidebar_class filter


= 3.1.8 =
* fixed : (js) responsive : dynamic content block position bug fixed in tc_script.js


= 3.1.7 =
* fixed : (css) : icons rendering for chrome
* improved : (css) : footer white icons also for black skin
* added : (php) utils : new filter with 2 parameters to tc_get_option
* added : (php) featured pages : new filter tc_fp_id for the featured pages
* added : (php) featured pages : new parameters added to the fp_img_src filter
* improved : (php) metaboxes : no metaboxes for acf post types
* improved : (js) responsive : dynamic content block position on resize hase been improved in tc_script.js
* fixed : (php) Image size : slider full size sets to 9999 instead of 99999 => was no compatible with Google App engine
* improved : (php) slider : make it easier to target individual slides with a unique class/or id
* added : (php) footer : dynamic actions added inside the widget wrapper
* improved : (php) footer : additional parameter for the tc_widgets_footer filter
* improved : (php)(js) comments : Comment reply link the whole button is now clickable
* fixed : (html) Google Structured Data : addition of the "updated" class in entry-date


= 3.1.6 =
* added : (php)(js) customizer controls : new filter for localized params
* added : (php) featured pages : new filters for title, excerpt and button blocks
* added : (php) search : form in the header if any results are found
* improved : (php) body tag : "itemscope itemtype="http://schema.org/WebPage" included in the 'tc_body_attributes' filter hook
* improved : (php) overall code : check added on ob_end_clean()
* improved : (php) headings : new filters by conditional tags
* improved : (php) comments : 'comment_text' WP built-in filter has been added in the comment callback function
* fixed : (js) submenu opening on click problem : [data-toggle="dropdown"] links are excluded from smoothscroll function
* fixed : (php) compatibility with NEXTGEN plugin : fixed ob_start() in class-content-headings::tc_content_headings()


= 3.1.5 =
* fixed : (php) child themes bug : child theme users can now override the Customizr files with same path/name.php.


= 3.1.4 =
* fixed : (css) featured pages : another responsive thumbnails alignment for max-width: 979px


= 3.1.3 =
* fixed : (css) featured pages : responsive thumbnails alignment


= 3.1.2 =
* improved : (php) minor file change : the class__.php content has been moved in functions.php


= 3.1.1 =
* added : (language) Turkish : thanks to <a href="http://www.ahmethakanergun.com/">Ahmet Hakan Ergün</a>
* added : (css) customizer : some styling
* fixed : (css) post thumbnails : minor alignment issues
* fixed : (php) translations in customizer for dropdown lists


= 3.1.0 =
* added : (language) Hungarian : thanks to Ferencz Székely
* added : (php) Site title : filter on the tag
* added : (php) archives (categories, tags, author, dates) and search results titles can be filtered
* added : (php) posts : 2 new hooks before and after post titles. Used for post metas.
* added : (php) logo and site title : new filter for link url (allowing to change the link on a per page basis)
* added : (php) featured pages : filters for page link url and text length
* added : (php) featured pages : new filter for the button text (allowing to change the title on a per page basis)
* added : (php) slider : new filters allowing a full control of img, title, text, link, button, color
* added : (php) slider : new function to easily get slides out of a slider
* added : (php) Slider : New edit link on each slides
* added : (php) comments : filter on the boolean controlling display
* added : (php) comments : direct link from post lists to single post comments section
* added : (php) comments : new filters allowing more control on the comment bubble
* added : (php) metaboxes : filter on display priority below WYSIWYG editor
* added : (php) footer : filters on widgets area : more controls on number of widgets and classes
* added : (php) sidebars : filters on column width classes
* added : (php) content : filters on the layout
* added : (php) page : support for excerpt
* added : (js)(php)(css) Retina : customizr now supports retina devices. Uses Ben Atkin's retina.js script.
* added : (js)(php)(css) New option : Optional smooth scroll effect on anchor links in the same page
* added : (js)(php) Slider : easier control of the stop on hover
* added : (php)(css) Menu : new option to select hover/click expansion mode of submenus
* added : (css) Bootstrap : Glyphicons are now available
* added : (php) Social Networks : possibility to easily add any social networks in option with a custom icon on front end
* added : (php) Social Networks : filter allowing additional link attributes like rel="publisher" for a specific social network
* added : (php) Posts/pages headings : new filters to enable/disable icons
* added : (php) Post lists : edit link in post titles for admin and edit_posts allowed users
* added : (php)(css) Intra post pagination : better styling with buttons
* added : (php) sidebars : two sidebar templates are back. needed by some plugins
* changed : (php) Featured page : name of the text filter is now 'fp_text'
* improved : (css) Menu : style has been improved
* improved : (php) slider : controls are not displayed if only on slide.
* improved : (php) fancy box : checks if isset $post before filtering content
* improved : (css) widgets : arrow next to widget's list is only displayed for default WP widgets
* fixed : (php) blog page layout : when blog was set to a page, the specific page layout was not active anymore
* fixed : (php) menu : the tc_menu_display filter was missing a parameter
* fixed : (php) comments : removed the useless permalink for the comments link in pages and posts


= 3.0.15 =
* added : (language) Catalan : thanks to <a href="https://twitter.com/jaume_albaiges" target="_blank">Jaume Albaig&egrave;s</a>
* fixed : (js) Slider : ie7/ie8/ie9 hack (had to be re-implemented) : thanks to @barryvdh (https://github.com/twbs/bootstrap/pull/3052)


= 3.0.14 =
* added : (language) Arabic : thanks to Ramez Bdiwi
* added : (language) RTL support : thanks to Ramez Bdiwi
* added : (language) Romanian : thanks to <a href="http://websiter.ro" target="_blank">Andrei Gheorghiu</a>
* added : (php) two hooks in index.php before article => allowing to add sections
* added : (php) new customizer option : select the length of posts in lists : excerpt of full length
* added : (php) add_size_images : new filters for image sizes
* added : (php) rtl : check on WPLANG to register the appropriate skin
* added : (php) featured pages : new filter for featured pages areas
* added : (php) featured pages : new filter for featured page text
* added : (php) slider : 3 filters have been added in class-admin-meta_boxes.php to modify the text, title and button length __slide_text_length, __slide_title_length, __slide_button_length
* added : (php) logo : 2 new filters to control max width and max height values (if logo resize options is enabled) : '__max_logo_width' , '__max_logo_height'
* added : (php) body tag : a new action hook '__body_attributes'
* added : (php) header tag : new '__header_classes' filter
* added : (php) #main-wrapper : new 'tc_main_wrapper_classes' filter
* added : (php) footer : new '__footer_classes' filter
* added : (js) scrollspy from Bootstrap
* added : (js) Scrollspy : updated version from Bootstrap v3.0. handles submenu spy.
* added : (css) back to top link colored with the skin color links
* added : (css) bootstrap : alerts, thumbnails, labels-badges, progress-bars, accordion stylesheets
* added : (css) Editor style support for skins, user style.css, specific post formats and rtl.
* improved : (css) performance : Avoid AlphaImageLoader filter for IE and css minified for fancybox stylesheet
* improved : (css) (php) logo : useless h1 tag has been removed if logo img. Better rendering function with printf. Better filters of logo function. 2 new actions have been added before and after logo : '__before_logo' , '__after_logo'
* removed : (php) Post list content : removed the useless buble $style var
* removed : (css) featured pages : useless p tag wrap for fp-button removed
* removed : (php) User experience : redirection to welcome screen on activation/update
* removed : (php) Security : Embedded video, Google+, and Twitter buttons
* fixed : (php) breadcrumb class : add a check isset on the $post_type_object->rewrite['with_front'] for CPT
* fixed : (php) a check on is_archive() has been added to tc_get_the_ID() function in class fire utils
* fixed : (php) we used tc__f('__ID') instead of get_the_ID() in class-header-slider
* fixed : (php) we add a hr separator after header only for search results and archives
* fixed : (php) comments : 'tc_comment_callback' filter hook was missing parameters
* fixed : (php) featured pages : filter  'tc_fp_single_display' was missing parameters
* fixed : (css) comments avatar more responsive
* fixed : (css) ie9 and less : hack to get rid of the gradient effect => was bugging the responsive menu.


= 3.0.13 =
* fixed : (php) Logo upload : we check if the getimagesize() function generates any warning (due to restrictions of use on some servers like 1&1) before using it. Thanks to <a href="http://wordpress.org/support/profile/svematec" target="_blank">svematec</a>, <a href="http://wordpress.org/support/profile/electricfeet" target="_blank">electricfeet</a> and <a href="http://wordpress.org/support/profile/heronswalk" target="_blank">heronswalk</a> for reporting this issue so quickly!


= 3.0.12 =
* fixed : (php) the slider is now also displayed on the blog page. Thanks to <a href="http://wordpress.org/support/profile/defttester" target="_blank">defttester</a> and <a href="http://wordpress.org/support/profile/rdellconsulting" target="_blank">rdellconsulting</a>

= 3.0.11 =
* added : (php) filter to the skin choices (in customizer options class), allowing to add new skins in the drop down list
* added : (php) filter for enqueuing the styles (in class ressources), allowing a better control for child theme
* added : (css) current menu item or current menu ancestor is colored with the skin color
* added : (php) function to check if we are using a child theme. Handles WP version <3.4.
* improved : (css) new conditional stylesheets ie8-hacks : icon sizes for IE8
* improved : (css) better table styling
* improved : (php) logo dimensions are beeing rendered in the img tag
* improved : (php) class group instanciation is faster, using the class group array instead of each singular group of class.
* improved : (php) the search and archive headers are easier to filter now with dedicated functions
* fixed : (css) archives and search icons color were all green for all skins
* fixed : (php) 404 content was displayed several times in a nested url rewrite context thanks to <a href="http://wordpress.org/support/profile/electricfeet" target="_blank">electricfeet</a>
* fixed : (php) attachment meta data dimensions : checks if are set $metadata['height'] && $metadata['width'] before rendering
* fixed : (php) attachment post type : checks if $post is set before getting the type
* fixed : (php) left and right sidebars are rendered even if they have no widgets hooked in thanks to <a href="http://wordpress.org/support/profile/pereznat" target="_blank">pereznat</a>.


= 3.0.10 =
* CHILD THEME USERS, templates have been modified : index.php, header.php, footer.php, comments.php *
* added : (php) (css) (html) New option : Draggable help box and clickable tooltips to easily display some contextual information and help for developers
* added : (php) support for custom post types for the slider meta boxes
* added : (php) new filter to get the post type
* added : polish translation. thanks to Marcin Sadowski from <a href="http://www.sadowski.edu.pl" target="_blank">http://www.sadowski.edu.pl</a>
* added : (php) (html) attachments are now listed in the search results with their thumbnails and descriptions, just like posts or pages
* added : (css) comment navigation styling, similar to post navigation
* added : (php) (css) author box styling (if bio field not empty)
* added : (css) comment bubble for pages
* added : (js) smooth transition for "back to top" link. Thanks to Nikolov : <a href="https://github.com/nikolov-tmw" target="_blank">https://github.com/nikolov-tmw</a>
* added : (js) smooth image loading on gallery attachment navigation
* added : (lang) Dutch translation. Thanks to Joris Dutmer.
* added : (css) icon to title of archive, search, 404
* improved : (php) attachment screen layout based on the parent
* improved : (php) simpler action hooks structure in the main templates : index, header, footer, comments, sidebars
* improved : (css) responsive behaviour : slider caption now visible for devices < 480px wide, thumbnail/content layout change for better display, body extra padding modified
* improved : (php) For better performances : options (single and full array) are now get from the TC_utils class instance instead of querying the database. (except for the customization context where they have to be retrieved dynamically from database on refresh)
* improved : (js) performance : tc_scripts and ajax_slider have been minified
* fixed : (css) IE fix : added z-index to active slide to fix slides falling below each other on transition. Thanks to PMStanley <a href="https://github.com/PMStanley">https://github.com/PMStanley</a>
* fixed : (css) IE fix : added 'top: 25%' to center align slide caption on older versions of IE. Thanks to PMStanley <a href="https://github.com/PMStanley" target="_blank">https://github.com/PMStanley</a>
* fixed : (php) empty reply button in comment threads : now checks if we reach the max level of threaded comment to render the reply button
* fixed : (php) empty nav buttons in single posts are not displayed anymore
* fixed : (css) font-icons compatibility with Safari is fixed for : page, formats (aside, link; image, video) and widgets (recent post, page menu, categories) thanks to <a href="http://wordpress.org/support/profile/electricfeet" target="_blank">electricfeet</a>
* fixed : (css) ordered list margin were not consistent in the theme thanks to <a href="http://wordpress.org/support/profile/electricfeet" target="_blank">electricfeet</a>
* fixed : (css) slider text overflow
* removed : sidebars templates. Sidebar content is now rendered with the class-content-sidebar.php


= 3.0.9 =
* ! SAFE UPGRADE FOR CHILD THEME USERS (v3.0.8 => v3.0.9) ! *
* fixed : function tc_is_home() was not checking the case where display nothing on home page. No impact for child theme users. Thanks to <a href="http://wordpress.org/support/profile/monten01">monten01</a>, <a href="http://wordpress.org/support/profile/rdellconsulting" target="_blank">rdellconsulting</a>
* fixed : When the permalink structure was not set to default, conditional tags is_page() and is_attachement() stopped working. They are now replaced by tests on $post -> post_type in class-main-content.php
* fixed : test if jet_pack is enabled before filtering post_gallery hook => avoid conflict
* fixed : @media print modified to remove links thanks to <a href="http://wordpress.org/support/profile/electricfeet" target="_blank">electricfeet</a>
* fixed : btn-info style is back to original Bootstrap style thanks to <a href="http://wordpress.org/support/profile/jo8192" target="_blank">jo8192</a>
* fixed : featured pages text => html tags are removed from page excerpt
* improved : custom css now allows special characters
* improved : better css structure, media queries are grouped at the end of the css files
* added : two new social networks in Customizer options : Instagram and WordPress
* added : help button and page in admin with links to FAQ, documentation and forum
* added : new constant TC_WEBSITE for author URI


= 3.0.8 =
* fixed : function tc_is_home() was missing a test. No impact for child theme users. Thanks to <a href="http://wordpress.org/support/profile/ldanielpour962gmailcom">http://wordpress.org/support/profile/ldanielpour962gmailcom</a>, <a href="http://wordpress.org/support/profile/rdellconsulting">http://wordpress.org/support/profile/rdellconsulting</a>, <a href="http://wordpress.org/support/profile/andyblackburn">http://wordpress.org/support/profile/andyblackburn</a>, <a href="http://wordpress.org/support/profile/chandlerleighcom">http://wordpress.org/support/profile/chandlerleighcom</a>


= 3.0.7 =
* fixed : the "force default layout" option was returning an array instead of a string. Thanks to http://wordpress.org/support/profile/edwardwilliamson and http://wordpress.org/support/profile/henry12345 for pointing this out!
* improved : get infos from parent theme if using a child theme in customizr-__ class constructor
* improved : enhanced filter for footer credit
* added : a notice about changelog if using a child theme
* improved : use esc_html tags in featured page text and slider captions


= 3.0.6 =
* fixed : Spanish translation has been fixed. Many thanks again to Maria del Mar for her great job!
* fixed : Pages protected with password will not display any thumbnail or excerpt when used in a featured page home block (thanks to rocketpopgames http://wordpress.org/support/profile/rocketpopgames)
* improved : performance : jquery.fancybox.1.3.4.js and modernizr have been minified
* added : footer credits can now be filtered with add_filter( 'footer_credits') and hooked with add_action ('__credits' )
* added : new customizer option to personnalize the featured page buttons


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
 

Enjoy it!
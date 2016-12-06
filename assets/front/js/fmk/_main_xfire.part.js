var czrapp = czrapp || {};

/************************************************
* LET'S DANCE
*************************************************/
jQuery(function ($) {
  var toLoad = {
    BrowserDetect : [],
    Czr_Dropdowns : [],
    Czr_MasonryGrid : ['masonryGridEventListener'],
    Czr_StickyHeader : [ 'stickyHeaderEventListener', 'triggerStickyHeaderLoad' ],
    Czr_UserExperience : [ 'variousHoverActions', 'smoothScroll', 'formFocusAction', 'variousHeaderActions', 'pluginsCompatibility', 'disableHoverOnScroll', 'headingsActions_test', 'featuredPages_test' ],
    Czr_Plugins : ['centerImagesWithDelay', 'parallax', 'lightbox', 'czr_slider' ]
//    Czr_Plugins : ['centerImagesWithDelay', 'imgSmartLoad' , 'dropCaps', 'extLinks' , 'fancyBox'],
//    Czr_Slider : ['fireSliders', 'manageHoverClass', 'centerSliderArrows', 'addSwipeSupport', 'sliderTriggerSimpleLoad'],
    //DropdownPlace is here to ensure is loaded before UserExperience's secondMenuRespActions
    //this will simplify the checks on whether or not move dropdowns at start
//    Czr_DropdownPlace : [],
//    Czr_UserExperience : [ 'dropdownMenuEventsHandler'/*, eventListener', 'outline','smoothScroll', 'anchorSmoothScroll', 'backToTop', 'widgetsHoverActions', 'attachmentsFadeEffect', 'clickableCommentButton', 'dynSidebarReorder', 'dropdownMenuEventsHandler', 'menuButtonHover', 'secondMenuRespActions'*/],
//    Czr_StickyHeader : [/*'stickyHeaderEventListener', 'triggerStickyHeaderLoad' */],
//    Czr_StickyFooter : ['stickyFooterEventListener'],
//    Czr_SideNav : []
  };
  czrapp.cacheProp().emitCustomEvents().loadCzr(toLoad);
});

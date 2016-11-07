var czrapp = czrapp || {};

/************************************************
* LET'S DANCE
*************************************************/
jQuery(function ($) {
  var toLoad = {
    BrowserDetect : [],
    Czr_Plugins : ['centerImagesWithDelay', 'imgSmartLoad' , 'dropCaps', 'extLinks' , 'fancyBox'],
    Czr_Slider : ['fireSliders', 'manageHoverClass', 'centerSliderArrows', 'addSwipeSupport', 'sliderTriggerSimpleLoad'],
    //DropdownPlace is here to ensure is loaded before UserExperience's secondMenuRespActions
    //this will simplify the checks on whether or not move dropdowns at start
    Czr_DropdownPlace : [],
    Czr_UserExperience : ['eventListener', 'outline','smoothScroll', 'anchorSmoothScroll', 'backToTop', 'widgetsHoverActions', 'attachmentsFadeEffect', 'clickableCommentButton', 'dynSidebarReorder', 'dropdownMenuEventsHandler', 'menuButtonHover', 'secondMenuRespActions'],
    Czr_StickyHeader : ['stickyHeaderEventListener', 'triggerStickyHeaderLoad' ],
    Czr_StickyFooter : ['stickyFooterEventListener'],
    Czr_SideNav : []
  };
  czrapp.cacheProp().emitCustomEvents().loadCzr(toLoad);
});

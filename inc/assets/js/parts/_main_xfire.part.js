var czrapp = czrapp || {};

/************************************************
* LET'S DANCE
*************************************************/
jQuery(function ($) {
  var toLoad = {
    BrowserDetect : [],
    Czr_Plugins : ['centerImagesWithDelay', 'imgSmartLoad' , 'dropCaps', 'extLinks' , 'fancyBox'],
    Czr_Slider : ['fireSliders', 'manageHoverClass', 'centerSliderArrows', 'addSwipeSupport', 'sliderTriggerSimpleLoad'],
    Czr_UserExperience : ['eventListener', 'smoothScroll', 'anchorSmoothScroll', 'backToTop', 'widgetsHoverActions', 'attachmentsFadeEffect', 'clickableCommentButton', 'dynSidebarReorder', 'dropdownMenuEventsHandler', 'menuButtonHover', 'secondMenuRespActions'],
    Czr_StickyHeader : ['stickyHeaderEventListener', 'triggerStickyHeaderLoad' ],
    Czr_StickyFooter : ['stickyFooterEventListener'],
    Czr_SideNav : []
  };
  czrapp.cacheProp().emitCustomEvents().loadCzr(toLoad);
});

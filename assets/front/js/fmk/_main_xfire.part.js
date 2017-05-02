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
         Czr_UserExperience : [
            'outline',
            'variousHoverActions',
            'smoothScroll',
            'formFocusAction',
            'variousHeaderActions',
            'pluginsCompatibility',
            'disableHoverOnScroll',
            'backToTop' ,
            'bttArrow',
            'headingsActions_test',
            'featuredPages_test'
         ],
         Czr_Plugins : [
            'centerImagesWithDelay',
            'imgSmartLoad',
            'parallax',
            'lightbox',
            'czrCarousels'
         ],
         Czr_StickyFooter : ['stickyFooterEventListener'],
         Czr_SideNav : []
   };
   czrapp.cacheProp().emitCustomEvents().loadCzr(toLoad);
});

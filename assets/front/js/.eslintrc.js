module.exports = {
    //https://eslint.org/docs/user-guide/configuring#configuration-cascading-and-hierarchy
    "globals": {
        // localized params
        "CZRParams":true,//modern
        "TCParams":true,//classical
        "frontHelpNoticeParams" : true,

        // front global czr object
        "czrapp" : true,

        // shared global functions
        "tcOutline" : true,
        "smoothScroll" : true,

        // external libs
        "Waypoint":true,
        "Flickity":true,
        "Vivus" : true
    }
};
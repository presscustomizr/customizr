//Falls back to default params
var TCParams = TCParams || {
	DisabledFeatures : {},
  centerAllImg: 1,
	FancyBoxAutoscale: 1,
	FancyBoxState: 1,
	HasComments: "",
	LeftSidebarClass: ".span3.left.tc-sidebar",
	LoadBootstrap: 1,
	LoadModernizr: 1,
	ReorderBlocks: 1,
	RightSidebarClass: ".span3.right.tc-sidebar",
	SliderDelay: +5000,
	SliderHover: 1,
	SliderName: "demo",
  centerSliderImg : 1,
	SmoothScroll: { Enabled : 1 , Options : {} },
	anchorSmoothScroll: "linear",
  anchorSmoothScrollExclude : {
      simple : ['[class*=edd]', '.tc-carousel-control', '.carousel-control', '[data-toggle="modal"]', '[data-toggle="dropdown"]', '[data-toggle="tooltip"]', '[data-toggle="popover"]', '[data-toggle="collapse"]', '[data-toggle="tab"]', '[class*=upme]', '[class*=um-]'],
      deep : { classes : [], ids : [] }
    },
	stickyCustomOffset: { _initial : 0, _scrolling : 0, options : { _static : true, _element : "" } },
	stickyHeader: 1,
	dropdowntoViewport: 1,
	timerOnScrollAllBrowsers:1,
  extLinksStyle :1,
  extLinksTargetExt:1,
  extLinksSkipSelectors: {
    classes : ['btn', 'button'],
    ids:[]
  },
  dropcapEnabled:1,
  dropcapWhere:{ post : 0, page : 1 },
  dropcapMinWords:50,
  dropcapSkipSelectors: {
    tags : ['IMG' , 'IFRAME', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6', 'BLOCKQUOTE', 'UL', 'OL'],
    classes : ['btn'],
    ids : []
  },
  imgSmartLoadEnabled:0,
  imgSmartLoadOpts: {
    parentSelectors: ['.article-container', '.__before_main_wrapper', '.widget-front'],
    opts : { excludeImg: ['.tc-holder-img'] }
  },
  goldenRatio : 1.618,
  gridGoldenRatioLimit : 350,
  isSecondMenuEnabled : 0,
  secondMenuRespSet : 'in-sn-before'
};

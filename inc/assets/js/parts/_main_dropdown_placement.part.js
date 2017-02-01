var czrapp = czrapp || {};
/************************************************
* DROPDOWN PLACEMENT SUB CLASS
*************************************************/
/*
* We need to compute the offset of dropdown and to do this the parents of the submenus
* have to be visible (visible for jQuery means display:block or similar).
* So we treat them case by case 'cause they might be already open (see resize when opened on click ).
* We cannot grab all the dropdowns and process them independentely from their parents.
*
* So what we do is:
* 1) grab all the first level dropdowns in the header
* 2) Cycle through them
* 3) make the single dropdown parent 'visible' and compute/set its new offset
* 4) if they have dropdowns children (1st level children), re-start from point 2) throughout them
* 5) reset the visibility manipulation
* Points from 3 to 5 are performed in _move_dropdown function
*/
(function($, czrapp) {
  var _methods =  {
    init : function() {
      this.$_sidenav                = $( '#tc-sn' );
      this._dd_first_selector       = '.menu-item-has-children.dropdown > .dropdown-menu' ;
      this.$_nav_collapse           = czrapp.$_tcHeader.length > 0 ? czrapp.$_tcHeader.find( '.navbar-wrapper .nav-collapse' ) : [];
      this.$_nav                    = this.$_nav_collapse.length ? this.$_nav_collapse.find( '.nav' ) : [];

      if ( ! this._has_dd_to_move() )
        return;

      //cache jQuery el
      this.$_navbar_wrapper         = this.$_nav_collapse.closest( '.navbar-wrapper' );
      this.$_nav                    = this.$_nav_collapse.find( '.nav' );
      this.$_head                   = $( 'head' );

      //other useful vars
      this._dyn_style_id            = 'tc-dropdown-dyn-style';
      this._prop                    = czrapp.$_body.hasClass('rtl') ? 'right' : 'left';

      //fire event listener
      this.dropdownPlaceEventListener();

      //place dropdowns on init
      this._place_dropdowns();
    },//init()


    dropdownPlaceCacheElements : function() {
      //cache jQuery el
      this.$_nav_collapse           = czrapp.$_tcHeader.length > 0 ? czrapp.$_tcHeader.find( '.navbar-wrapper .nav-collapse' ) : [];
      this.$_nav                    = this.$_nav_collapse.length ? this.$_nav_collapse.find( '.nav' ) : [];
      this.$_navbar_wrapper         = this.$_nav_collapse.length ? this.$_nav_collapse.closest( '.navbar-wrapper' ) : [];
    },

    /***********************************************
    * DOM EVENT LISTENERS AND HANDLERS
    ***********************************************/
    dropdownPlaceEventListener : function() {
      var self    = this,
          _events = 'tc-resize sn-open sn-close tc-sticky-enabled tc-place-dropdowns partialRefresh.czr';

      //Any event which may have resized the header
      czrapp.$_body.on( _events, function( evt, data ) {
        if ( 'partialRefresh' === evt.type && 'czr' === evt.namespace && data.container.hasClass('tc-header')  ) {
          self.dropdownPlaceCacheElements();
        }
        self.dropdownPlaceEventHandler( evt, 'resize' );
      });
    },


    dropdownPlaceEventHandler : function( evt, evt_name ) {
      var self = this;

      switch ( evt_name ) {
        case 'resize' :
          setTimeout( function(){
            self._place_dropdowns();
          }, 250);
        break;
      }
    },


    _place_dropdowns : function () {
      var _dd = this._get_dd_to_move();
      if ( ! _dd.length )
        return;

      this._staging();
      this._move_dropdown( _dd );
      this._write_dyn_style();
      this._unstaging();
    },



    /***********************************************
    * HELPERS
    ***********************************************/
    //DROPDOWN PLACE SUB CLASS HELPER (private like)
    //When checking if there's something to move does not make sense at the start
    //1) there's no navbar collapse in the header
    //2) there are no dropdowns to move in the header
    _has_dd_to_move : function() {
      if ( this.$_nav_collapse.length < 1 )
        return false;
      if ( this.$_nav.length && this.$_nav.find( this._dd_first_selector ) < 1 )
        return false;

      return true;
    },

    //DROPDOWN PLACE SUB CLASS HELPER (private like)
    //returns the dropdowns to move on resize?
    //a) when the nav-collapse is not absolute => we're not in mobile menu case => no dd to move
    //b) .tc-header .nav is hidden (case: second menu hidden in mobiles ) => no dd to move
    //c) return the .tc-header .nav dropdown children
    _get_dd_to_move : function() {
      if ( 'absolute' == this.$_nav_collapse.css('position') )
        return {};
      if ( ! this.$_nav.is(':visible') )
        return {};
      return this.$_nav.find( this._dd_first_selector );
    },

    //DROPDOWN PLACE SUB CLASS HELPER (private like)
    //Prepare the environment
    //What we do here:
    //1) we 'suspend' the transitions on submenus
    //2) we add a dynamic style which:
    // a) sets the max width of the dropdown to the window's width
    // b) allows braking words for submenus label
    _staging : function() {
      this._window_width = czrapp.$_window.width();
      //remove submenu fade, transitions corrupt the offset computing
      if ( this.$_navbar_wrapper.hasClass('tc-submenu-fade') )
        // tc-submenu-fade-susp(ended) is a dummy class we add for the future check in _unstaging
        this.$_navbar_wrapper.removeClass('tc-submenu-fade').addClass('tc-submenu-fade-susp');
      var _max_width            = this._window_width - 40,
          _dyn_style_css_prefix = '.tc-header .nav-collapse .dropdown-menu';

      //the max width of a drodpdown must be the window's width (- 40px aesthetical )
      this._dyn_style  = _dyn_style_css_prefix + ' {max-width: ' + _max_width + 'px;}';
      //following is to ensure that big labels are broken in more lines if they exceed the max width
      //probably due to a bug, white-space: pre; doesn't work fine in recent firefox.
      //Anyway this just means that the following rule (hence the prev) for them is useless => doesn't introduce a bug
      //p.s. this could be moved in our main CSS
      this._dyn_style += _dyn_style_css_prefix + ' > li > a { word-wrap: break-word; white-space: pre; }';
      this._write_dyn_style();
    },

    //DROPDOWN PLACE SUB CLASS HELPER (private like)
    //Reset temporary changes to the environment performed in the staging phase
    //What we do here:
    //1) Re-add the transitions on submenus if needed
    _unstaging : function() {
      //re-add submenu fade, transitions corrupt the offset computing
      if ( this.$_navbar_wrapper.hasClass('tc-submenu-fade-susp') )
        this.$_navbar_wrapper.removeClass('tc-submenu-fade-susp').addClass('tc-submenu-fade');
    },

    //DROPDOWN PLACE SUB CLASS HELPER (private like)
    //Write the dynamic style into the HEAD
    _write_dyn_style : function() {
      var $_dyn_style_el = this.$_head.find('#' + this._dyn_style_id);

      //there's already a _dyn_style_el, so remove it
      //I thought that remove/create a new element every time is worse than just have an empty style, but looks like that $_dyn_style_el.html( _dyn_style ) isnt' cross-browser, gives me errors in ie8
      if ( $_dyn_style_el.length > 0 )
        $_dyn_style_el.remove();
      if ( this._dyn_style )
        // I would have loved ot use getOverrideStyle, but couldn't get it to work -> Error: getOverrideStyle is not a function
        // I'm probabably missing something. Ref: http://www.w3.org/TR/DOM-Level-2-Style/css.html#CSS-CSSStyleDeclaration
        // probably not very supported by browsers?
        // getOverrideStyle($_dropdown[0], ':before');
        $("<style type='text/css' id='" + this._dyn_style_id +"'>" + this._dyn_style + "</style>")
          .appendTo( this.$_head );
    },

    //DROPDOWN PLACE SUB CLASS HELPER (private like)
    // Moving dropdown core
    _move_dropdown : function( $dropdown_menu ) {
      // does dropdown_menu element exists?
      if ( $dropdown_menu && $dropdown_menu.length ) {
        if ( $dropdown_menu.length > 1 ) {
          var self = this;
          // is $dropdown_menu an array of elements ? if yes call this function over them
          $.each( $dropdown_menu, function(){
            self._move_dropdown( $(this) );
          });
          return;
        }//end array of dropdown case
      }else //no dropdown
        return;
      // Moving core
      var _is_dropdown_visible = $dropdown_menu.is(':visible');
      if ( ! _is_dropdown_visible )
        $dropdown_menu.css('display', 'block').css('visibility', 'hidden');

      //first thing to do; reset all changes why?
      //example, say the last menu item has a submenu which has been moved when window's width == 1200px,
      //then the window is shrinked to 1000px and the last menu item drops on a new line. In this case :
      //a) the "moving" might not be needed anymore 'cause it might not overflows the window
      //b) even worse, the "moving" might have made it overflow on the opposite side.
      this._set_dropdown_offset( $dropdown_menu, '' );
      //get the current overflow
      var _overflow     = this._get_dropdown_overflow( $dropdown_menu );

      if ( _overflow )
        this._set_dropdown_offset( $dropdown_menu, _overflow );

      //move all the childrens (1st level of children ) which are dropdowns
      var $_children_dropdowns = $dropdown_menu.children('li.dropdown-submenu');
        if ( $_children_dropdowns.length )
          this._move_dropdown( $_children_dropdowns.children('ul.dropdown-menu') );

      //reset 'visibility-manipulation'
      if ( ! _is_dropdown_visible )
        $dropdown_menu.css('display', '').css('visibility', '');
    },

    //DROPDOWN PLACE SUB CLASS HELPER (private like)
    //Set dropdown offset + first dropdown level top arrow offset accordingly
    _set_dropdown_offset : function( $dropdown_menu, _dropdown_overflow ) {
      var _offset = '';

      if ( _dropdown_overflow ) {
        var $_parent_dropdown  = $dropdown_menu.parent('.menu-item-has-children'),
            _is_dropdown_submenu = $_parent_dropdown.hasClass('dropdown-submenu');

        //is submenu 2nd level?
        if ( _is_dropdown_submenu ) {
          _offset = parseFloat( $dropdown_menu.css( this._prop ) ) - _dropdown_overflow - 5;
          //does the parent menu item have "brothers" after it? in this case be sure the new position will
          //not make it completely overlap parent menu item sibling. We can left 30px of space so
          //the user can access the sibling menu item.
          //So the condition are:
          //1) the parent menu item has siblings
          //and
          //2) there's a space < 30px between the starting edges of the parent and child dropdown
          //or
          //2.1) there's a space < 30px between the ending edges of the parent and child dropdown
          if ( $_parent_dropdown.next('.menu-item').length ) {
            var _submenu_overflows_parent = this._get_element_overflow( $dropdown_menu, _offset, $_parent_dropdown );
            if ( _offset < 30  || _submenu_overflows_parent < 30 )
              //the new offset is then the old one minus the amount of overflow (ex. in ltr align parent and child right edge ) minus 30px
              _offset = _offset - _submenu_overflows_parent - 30;
          }
        } else {
          _offset = -20 - _dropdown_overflow; //add some space (20px) on the right(rtl-> left)
          // when is dropdown first level we need to move the top arrow
          // we need the menu-item-{id} class to build the css rule
          var _menu_id = $_parent_dropdown.attr('class').match(/(menu|page)-item-\d+/);
          _menu_id = _menu_id ? _menu_id[0] : null;
          if ( _menu_id )
            this._set_dropdown_arrow_style( _menu_id, _offset );
        }
      }
      //in any case write the dropdown offset css:
      //a dropdown which doesn't have to be moved will not be passed to this function, so no problem. The only case when this is needed is when we reset the dropdowns offset before checking whether or not we have to move it, Maybe we can fine tune this adding a css class to the moved dropdowns so we'll reset just them.
      $dropdown_menu.css( this._prop, _offset );
    },

    //DROPDOWN PLACE SUB CLASS HELPER (private like)
    //compute the dropdown overflow
    _get_dropdown_overflow : function ( $dropdown_menu ) {
      var overflow = null,
          _t_overflow;
       // how we compute the overflow
       // ltr
       if ( 'left' == this._prop ) {
         // the overlfow is: the absolute position left/right of the elemnt + its width - the window's width
         // so it represents the amount of "width" which overflows the window
         _t_overflow = this._get_element_overflow( $dropdown_menu, $dropdown_menu.offset().left, {}, this._window_width );
         // a positive overflow means that the dropdown goes off the window
         // anyways I decided to adjust its position even if the gap between the end of the dropdown
        // and the window's width is < 5 (6), just to avoid dropdown edges so close to the end of the window
        overflow = _t_overflow > -5 ? _t_overflow : overflow ;
      }else { // rtl
        //the overflow is: the left offset * -1 if less than 5px
        //note: jQuery.offset() gives just top and left properties.
        _t_overflow = $dropdown_menu.offset().left;
        overflow  = _t_overflow < 5 ? -1 * _t_overflow : overflow;
      }
        return overflow;
    },
    //DROPDOWN PLACE SUB CLASS HELPER (private like)
    //compute the overflow of an element given a parent an an initial left offset
    _get_element_overflow : function ( $_el, _offset, $_parent, _parent_width ) {
      _parent_width = $_parent.length ? $_parent.width() : _parent_width;
      return $_el.width() + _offset - _parent_width;
    },
    //DROPDOWN PLACE SUB CLASS HELPER (private like)
    //compute and set the dropdown first level top arrow offset
    //which is the original offset for the pseudo element before and after minus the
    //shift amount applied to the dropdown
    _set_dropdown_arrow_style : function( _menu_id, _offset ) {
      //9px is static to avoid using the following via javascript
      //window.getComputedStyle($_dropdown[0], ':before').left ;
      var _arrow_before_offset    = +9 - _offset,
          _arrow_after_offset     = _arrow_before_offset + 1,
          _arrow_css_rule_prefix  = '.tc-header .navbar .nav > .' + _menu_id + ' > .dropdown-menu',

         _arrow_before_css_rule  = _arrow_css_rule_prefix + ":before { " + this._prop + ": " + _arrow_before_offset + "px;}",
         _arrow_after_css_rule   = _arrow_css_rule_prefix + ":after { " + this._prop + ": " + _arrow_after_offset + "px;}";

      this._dyn_style += "\n" + _arrow_before_css_rule + "\n" + _arrow_after_css_rule;
    }
  };//_methods{}

  czrapp.methods.Czr_DropdownPlace = {};
  $.extend( czrapp.methods.Czr_DropdownPlace , _methods );

})(jQuery, czrapp);

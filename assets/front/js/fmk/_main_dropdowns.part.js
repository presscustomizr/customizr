var czrapp = czrapp || {};
/************************************************
* DROPDOWNS SUB CLASS
*************************************************/
(function($, czrapp) {
  var _methods =  {

    init : function() {
      this.namespace = 'czrDropdown';
      this.openClass = 'active';
      this.dropdownMenuOnClick();
      this.dropdownMenuOnHover();
    },


    dropdownTrigger : function( $_el, evt, data ) {
      $_el.trigger( evt+'.'+this.namespace, data );
    },

    //Handle dropdown on hover via js
    dropdownMenuOnHover : function() {
      var _dropdown_selector = '.tc-open-on-hover .menu-item-has-children, .primary-nav__woocart',
          self               = this;

      function _addOpenClass () {
        $_el = $(this);
        if ( ! $_el.hasClass(self.openClass) ) {
          self.dropdownTrigger( $_el, 'li-open' );
          $_el.addClass(self.openClass);
        }
      };

      //a little delay before closing to avoid closing a parent before accessing the child
      function _removeOpenClass () {

        var $_el = $(this)

        _debounced_removeOpenClass = _.debounce( function() {
          if ( $_el.find("ul li:hover").length < 1 && ! $_el.closest('ul').find('li:hover').is( $_el ) ) {
            //test
            self.dropdownTrigger( $_el, 'li-close' );
            $_el.removeClass(self.openClass);
          }

        }, 150);

        _debounced_removeOpenClass();
      };

      czrapp.$_tcHeader.on('mouseenter', _dropdown_selector, _addOpenClass );
      czrapp.$_tcHeader.on('mouseleave', _dropdown_selector , _removeOpenClass );
    },

    //Handle dropdown on click for multi-tier menus
    dropdownMenuOnClick : function() {
      var _dropdown_menu_container_selector  = '.tc-open-on-click',
          _dropdown_menu_selector            = '.dropdown-menu',
          _dropdown_submenu_selector         = '.dropdown-submenu',
          _dropdown_toggler_selector         = '[data-toggle="dropdown"]',
          _dropdown_submenu_toggler_selector = '.dropdown .dropdown-submenu > [data-toggle="dropdown"]',
          _open_class                        = this.openClass,
          self                               = this;

          //_dropdown_link_selector            = '.tc-open-on-click .menu-item.menu-item-has-children > a[href!="#"]'

      /* TODO: Better handling with bootstrap events */
      /* Test */
      $( _dropdown_menu_container_selector ).on( 'tap click', _dropdown_toggler_selector, function(evt) {
        var $_el = $(this).closest( 'li' );
        if ( $_el.hasClass( _open_class) ) {
          //we are about to close it
          self.dropdownTrigger( $_el, 'li-close' );
        }else
          self.dropdownTrigger( $_el, 'li-open' );
      });

      // make sub-submenus dropdown on click work
      $( _dropdown_menu_container_selector ).on('tap click', _dropdown_submenu_toggler_selector, function(){
        var _openthis          = false,
            $_el               = $(this);
            $_parent_submenu   = $_el.closest( _dropdown_submenu_selector );

        if ( ! $_parent_submenu.hasClass( _open_class ) ) {
          _openthis = true;
        }
        // close opened submenus
        $( $_parent_submenu.closest( _dropdown_menu_selector ) ).find( _dropdown_submenu_selector +'.'+ _open_class ).each(function() {
          var $_submenu_to_close = $( this ),
              $_toggler          = $_submenu_to_close.find( _dropdown_toggler_selector + '[aria-expanded="true"]' ),
              $_toggler_js_el    = $_toggler.length ? $_toggler[0] : null;

          $_submenu_to_close.removeClass( _open_class );
          $_toggler_js_el && $_toggler_js_el.setAttribute('aria-expanded', 'false');
        });

        if ( _openthis ) {
          $_parent_submenu.addClass( _open_class );
          $_el[0].setAttribute('aria-expanded', 'true');
        }

        return false;
      });//.on()
    }

  };//_methods{}

  czrapp.methods.Czr_Dropdowns = {};
  $.extend( czrapp.methods.Czr_Dropdowns , _methods );

})(jQuery, czrapp);
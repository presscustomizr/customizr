var czrapp = czrapp || {};
/************************************************
* DROPDOWNS SUB CLASS
*************************************************/
(function($, czrapp) {
  var _methods =  {

    init : function() {
        this.dropdownMenuOnClick();
        this.dropdownMenuOnHover();
    },
    
    //Handle dropdown on hover via js
    dropdownMenuOnHover : function() {
      var _dropdown_selector = '.tc-open-on-hover .menu-item-has-children, .primary-nav__woocart';
      function _addOpenClass (){ 
        if ( ! $(this).hasClass('open') )
          $(this).addClass('open');
      };
      
      //a little delay before closing to avoid closing a parent before accessing the child
      function _removeOpenClass (){  
      
        var $el = $(this);

        _debounced_removeOpenClass = _.debounce( function(){
          if ( $el.find("ul li:hover").length < 1 && ! $el.closest('ul').find('li:hover').is($el ) )
            $el.removeClass('open');    
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
          _dropdown_submenu_toggler_selector = '.dropdown .dropdown-submenu > [data-toggle="dropdown"]',
          _open_class                        = 'open';
      
          //_dropdown_link_selector            = '.tc-open-on-click .menu-item.menu-item-has-children > a[href!="#"]'
          
      //Not needed anymore!!
      //go to the link if submenu is already opened
      /*
      czrapp.$_tcHeader.on('tap click', _dropdown_link_selector, function(evt) {
        if ( $(this).closest('li').hasClass('open') ){
          evt.preventDefault;
          evt.stopPropagation();
        }

        var $_this_link     = $(this);
            $_dropdown_menu = $_this_link.next('.dropdown-menu'),
            $_li_parent     = $_this_link.parent();

        
        if ( ( $_dropdown_menu.css('visibility') != 'hidden' && $_dropdown_menu.is(':visible')  &&
                ! $_li_parent.hasClass('dropdown-submenu') ) ||
             ( $_dropdown_menu.is(':visible') && $_li_parent.hasClass('dropdown-submenu') ) )
          window.location = $(this).attr('href');

      });//.on()*/

      // make sub-submenus dropdown on click work
      $( _dropdown_menu_container_selector ).on('tap click', _dropdown_submenu_toggler_selector, function(){
        var _openthis          = false,
            $_parent_submenu   = $(this).closest( _dropdown_submenu_selector );

        if ( ! $_parent_submenu.hasClass( _open_class ) ) {
          _openthis = true;
        }
        // close opened submenus
        $( $_parent_submenu.closest( _dropdown_menu_selector ) ).children( _dropdown_submenu_selector ).each(function(){
          $(this).removeClass( _open_class );
        });

        if ( _openthis )
          $_parent_submenu.addClass( _open_class );

        return false;
      });//.on()

    }
    
  };//_methods{}

  czrapp.methods.Czr_Dropdowns = {};
  $.extend( czrapp.methods.Czr_Dropdowns , _methods );

})(jQuery, czrapp);
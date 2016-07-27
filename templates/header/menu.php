<?php
/**
 * The template for displaying a menu ( both main and secondary in navbar or/and the sidenav one)
 */
?>
      <div class="primary-nav__menu-wrapper">
        <ul class="nav tc-open-on-click nabvar-nav primary-nav__menu list-menu" id="main-menu_tp">
            <li class="nav-item menu-item active">
              <a class="nav-link" href="index.html"><span>Home</span><span class="sr-only">(current)</span></a>
            </li>
            <li class="dropdown nav-item btn-group menu-item menu-item-has-children">
              <a href="#" class="nav-link" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span>Skins</span><span class="caret__dropdown-toggler" ><span class="caret__dropdown-toggler__span"></span></span></a>
              <ul class="dropdown-menu">
                <li class="dropdown-item menu-item">
                  <a class="nav-link" href="skin-dark-example.html"><span>Dark Skin</span></a>
                </li>
                <li class="dropdown-item menu-item">
                  <a class="nav-link" href="skin-light-example.html"><span>Light Skin</span></a>
                </li>
              </ul>
            </li>
            <li class="dropdown nav-item btn-group menu-item menu-item-has-children">
              <a href="#" class="nav-link" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span>Headers / Menus</span><span class="caret__dropdown-toggler"><span class="caret__dropdown-toggler__span"></span></span></a>
              <ul class="dropdown-menu">
                <li class="dropdown-item menu-item">
                 <a class="nav-link" href="menu-dropdown-on-hover.html"><span>Menu dropdown on hover</span></a>
                </li>
                <li class="dropdown-item menu-item">
                  <a class="nav-link" href="sticky-nav-fixed.html"><span>Sticky Nav Fixed</span></a>
                </li>
                <li class="dropdown-item menu-item">
                  <a class="nav-link" href="sticky-nav-push.html"><span>Sticky Nav Push</span></a>
                </li>
                <li class="dropdown-item menu-item">
                  <a class="nav-link" href="ham-desk-example.html"><span>Desktop Hamburger</span></a>
                </li>
                <li class="dropdown-item dropdown-submenu nav-item btn-group menu-item menu-item-has-children">
                  <a href="#" class="nav-link" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span>Nested Dropdown Example</span><span class="caret__dropdown-toggler"><span class="caret__dropdown-toggler__span"></span></span></a>
                  <ul class="dropdown-menu">
                    <li class="dropdown-item dropdown-submenu nav-item btn-group menu-item menu-item-has-children">
                      <a href="http://www.google.it" class="nav-link"><span>Dropdown with Link</span></a><span class="caret__dropdown-toggler" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="caret__dropdown-toggler__span"></span></span>
                      <ul class="dropdown-menu">
                          <li class="dropdown-item menu-item"><a href="#"><span>Dropdown Sub Item One</span></a></li>
                          <li class="dropdown-item menu-item"><a href="#"><span>Dropdown Sub Item Two</span></a></li>
                          <li class="dropdown-item menu-item"><a href="#"><span>Dropdown Sub Item Three</span></a></li>
                          <li class="dropdown-item menu-item"><a href="#"><span>Dropdown Sub Item Four</span></a></li>
                      </ul>
                    </li>
                    <li class="dropdown-item menu-item"><a href="#"><span>Dropdown Item One</span></a></li>
                    <li class="dropdown-item menu-item"><a href="#"><span>Dropdown Item Two</span></a></li>
                    <li class="dropdown-item menu-item"><a href="#"><span>Dropdown Item Three</span></a></li>
                    <li class="dropdown-item menu-item"><a href="#"><span>Dropdown Item Four</span></a></li>
                  </ul>
                </li>
              </ul>
            </li>
            <li class="dropdown nav-item btn-group menu-item menu-item-has-children">
              <a href="#" class="nav-link" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span>Post lists</span><span class="caret__dropdown-toggler" ><span class="caret__dropdown-toggler__span"></span></span></a>
              <ul class="dropdown-menu">
                <li class="dropdown-item dropdown-submenu nav-item btn-group menu-item menu-item-has-children">
                  <a href="#" class="nav-link" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span>Alternates</span><span class="caret__dropdown-toggler" ><span class="caret__dropdown-toggler__span"></span></span></a>

                  <ul class="dropdown-menu">
                    <li class="dropdown-item menu-item">
                      <a class="nav-link" href="alternate-example.html"><span>Alternate Full Width</span></a>
                    </li>
                    <li class="dropdown-item menu-item">
                      <a class="nav-link" href="alternate-example-leftsidebar.html"><span>Alternate Left Sidebar</span></a>
                    </li>
                    <li class="dropdown-item menu-item">
                      <a class="nav-link" href="alternate-example-bothsidebars.html"><span>Alternate Both Sidebars</span></a>
                    </li>
                  </ul>
                </li>
                <li class="dropdown-item dropdown-submenu nav-item btn-group menu-item menu-item-has-children">
                  <a href="#" class="nav-link" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span>Grids</span><span class="caret__dropdown-toggler" ><span class="caret__dropdown-toggler__span"></span></span></a>
                  <ul class="dropdown-menu">
                    <li class="dropdown-item dropdown-submenu nav-item btn-group menu-item menu-item-has-children">
                      <a href="#" class="nav-link" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span>Classic Grids</span><span class="caret__dropdown-toggler" ><span class="caret__dropdown-toggler__span"></span></span></a>
                      <ul class="dropdown-menu">
                        <li class="dropdown-item menu-item">
                          <a class="nav-link" href="grid-classic-2c-2s.html"><span>Grid 2 cols - 2 SB</span></a>
                        </li>
                        <li class="dropdown-item menu-item">
                          <a class="nav-link" href="grid-classic-3c-1s.html"><span>Grid 3 cols - 1 SB</span></a>
                        </li>
                        <li class="dropdown-item menu-item">
                          <a class="nav-link" href="grid-classic-3c-full.html"><span>Grid 3 cols - Full</span></a>
                        </li>
                      </ul>
                    </li>
                    <li class="dropdown-item menu-item">
                      <a class="nav-link" href="grid-example.html"><span>Grid Example</span></a>
                    </li>
                    <li class="dropdown-item menu-item">
                      <a class="nav-link" href="grid-masonry-example.html"><span>Grid Masonry</span></a>
                    </li>
                  </ul>
                </li>
              </ul>
            </li>
            <li class="dropdown nav-item btn-group menu-item menu-item-has-children">
              <a href="#" class="nav-link" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span>Components</span><span class="caret__dropdown-toggler" ><span class="caret__dropdown-toggler__span"></span></span></a>
              <ul class="dropdown-menu">
                <li class="dropdown-item dropdown-submenu nav-item btn-group menu-item menu-item-has-children">
                  <a href="#" class="nav-link" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span>Parallax</span><span class="caret__dropdown-toggler" ><span class="caret__dropdown-toggler__span"></span></span></a>
                  <ul class="dropdown-menu">
                    <li class="dropdown-item menu-item">
                      <a class="nav-link" href="parallax.html"><span>Parallax</span></a>
                    </li>
                    <li class="dropdown-item menu-item">
                      <a class="nav-link" href="parallax-external.html"><span>External Parallax</span></a>
                    </li>
                  </ul>
                </li>
        </ul>
      </div>


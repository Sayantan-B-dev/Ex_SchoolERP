// Combined sidebar + mobile navbar logic
document.addEventListener('DOMContentLoaded', function () {
    /* =============================
       🖥️ Desktop Sidebar Collapse
    ============================== */
    var sidebar = document.getElementById('sideNavbar');
    var mainContent = document.getElementById('mainContent');
    var collapseBtn = document.getElementById('collapseSidebarBtn');
    var collapseBtnContainer = document.querySelector('.collapse-btn-container');

    // Persist state in localStorage
    var isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';

    function setSidebarState(collapsed) {
        if (collapsed) {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('collapsed');
            // Hide all children except collapse button container
            Array.from(sidebar.children).forEach(function (child) {
                if (!child.classList.contains('collapse-btn-container')) {
                    child.style.display = 'none';
                } else {
                    child.style.display = '';
                }
            });
        } else {
            sidebar.classList.remove('collapsed');
            mainContent.classList.remove('collapsed');
            // Show all children
            Array.from(sidebar.children).forEach(function (child) {
                child.style.display = '';
            });
        }
    }

    setSidebarState(isCollapsed);

    if (collapseBtn) {
        collapseBtn.addEventListener('click', function () {
            isCollapsed = !sidebar.classList.contains('collapsed');
            setSidebarState(isCollapsed);
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        });
    }

    /* =============================
       📱 Mobile Navbar Logic
    ============================== */
    var mobileToggler = document.querySelector('.mobile-navbar .navbar-toggler');
    var mobileNav = document.getElementById('mobileNavBar');

    // Mobile nav toggler open/close
    if (mobileToggler && mobileNav) {
        mobileToggler.addEventListener('click', function (e) {
            e.preventDefault();
            mobileNav.classList.toggle('show');
            mobileToggler.setAttribute(
                'aria-expanded',
                mobileNav.classList.contains('show') ? 'true' : 'false'
            );
        });
    }

    // Mobile dropdowns: open/close on tap, close others, close menu on link click
    var mobileDropdownToggles = document.querySelectorAll(
        '.mobile-navbar .nav-item.dropdown > .dropdown-toggle'
    );

    mobileDropdownToggles.forEach(function (toggle) {
        var parent = toggle.closest('.nav-item.dropdown');
        var menu = parent.querySelector('.dropdown-menu');

        toggle.addEventListener('click', function (e) {
            e.preventDefault();
            // Close all other dropdowns
            mobileDropdownToggles.forEach(function (otherToggle) {
                var otherParent = otherToggle.closest('.nav-item.dropdown');
                var otherMenu = otherParent.querySelector('.dropdown-menu');
                if (otherToggle !== toggle) {
                    otherParent.classList.remove('show');
                    otherMenu.style.display = 'none';
                    otherToggle.setAttribute('aria-expanded', 'false');
                }
            });
            // Toggle this dropdown
            var isOpen = parent.classList.toggle('show');
            menu.style.display = isOpen ? 'block' : 'none';
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    });

    // Single global listener to close dropdowns if clicked outside
    document.addEventListener('click', function (e) {
        mobileDropdownToggles.forEach(function (toggle) {
            var parent = toggle.closest('.nav-item.dropdown');
            var menu = parent.querySelector('.dropdown-menu');
            if (!parent.contains(e.target)) {
                parent.classList.remove('show');
                menu.style.display = 'none';
                toggle.setAttribute('aria-expanded', 'false');
            }
        });
    });

    // Close mobile menu after clicking any link (including dropdown items)
    var mobileNavLinks = document.querySelectorAll(
        '#mobileNavBar .nav-link, #mobileNavBar .dropdown-item, #mobileNavBar .btn'
    );

    mobileNavLinks.forEach(function (link) {
        link.addEventListener('click', function () {
            if (!link.classList.contains('dropdown-toggle')) {
                // Close main mobile menu
                if (mobileNav.classList.contains('show')) {
                    mobileNav.classList.remove('show');
                    if (mobileToggler)
                        mobileToggler.setAttribute('aria-expanded', 'false');
                }
                // Close all dropdowns
                mobileDropdownToggles.forEach(function (toggle) {
                    var parent = toggle.closest('.nav-item.dropdown');
                    var menu = parent.querySelector('.dropdown-menu');
                    parent.classList.remove('show');
                    menu.style.display = 'none';
                    toggle.setAttribute('aria-expanded', 'false');
                });
            }
        });
    });
});

"use strict";

// Mode Switcher
$("#modeSwitcher").on("click", function(e) {
    e.preventDefault();
    modeSwitch();
    location.reload();
});

// Sidebar Toggle Logic
$(".collapseSidebar").on("click", function(e) {
    e.preventDefault();
    
    // Check if we are on mobile (screen < 768px)
    if (window.innerWidth < 768) {
        $("body").toggleClass("show-sidebar");
    } else {
        // Desktop Collapse/Narrow logic
        if ($(".vertical").hasClass("narrow")) {
            $(".vertical").toggleClass("open");
        } else {
            $(".vertical").toggleClass("collapsed");
            if ($(".vertical").hasClass("hover")) {
                $(".vertical").removeClass("hover");
            }
        }
    }
});

// Close sidebar when clicking overlay on mobile
$(document).on("click", ".sidebar-overlay", function() {
    $("body").removeClass("show-sidebar");
});

// Close sidebar when clicking nav links on mobile
$(".sidebar-left .nav-link").on("click", function() {
    if (window.innerWidth < 768) {
        $("body").removeClass("show-sidebar");
    }
});

// Sidebar Hover Behavior (Desktop Only)
$(".sidebar-left").hover(function() {
    if (window.innerWidth >= 768) {
        if ($(".vertical").hasClass("collapsed") || !$(".narrow").hasClass("open")) {
            $(".vertical").addClass("hover");
        }
    }
}, function() {
    if (window.innerWidth >= 768) {
        $(".vertical").removeClass("hover");
    }
});

// Navbar slide toggle
$(".toggle-sidebar").on("click", function() {
    $(".navbar-slide").toggleClass("show");
});

// Dropdown support
(function(a) {
    a(".dropdown-menu a.dropdown-toggle").on("click", function(e) {
        if (!a(this).next().hasClass("show")) {
            a(this).parents(".dropdown-menu").first().find(".show").removeClass("show");
        }
        a(this).next(".dropdown-menu").toggleClass("show");
        a(this).parents("li.nav-item.dropdown.show").on("hidden.bs.dropdown", function(e) {
            a(".dropdown-submenu .show").removeClass("show");
        });
        return false;
    });
})(jQuery);

$(".navbar .dropdown").on("hidden.bs.dropdown", function() {
    $(this).find("li.dropdown").removeClass("show open");
    $(this).find("ul.dropdown-menu").removeClass("show open");
});
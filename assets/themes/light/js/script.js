// add bg to nav
window.addEventListener("scroll", function () {
    let scrollpos = window.scrollY;
    const header = document.querySelector("nav");
    const headerHeight = header.offsetHeight;

    if (scrollpos >= headerHeight) {
        header.classList.add("active");
    } else {
        header.classList.remove("active");
    }
});

// active nav item
const shortNavItem = document.getElementsByClassName("short-nav-item");
for (const element of shortNavItem) {
    element.addEventListener("click", () => {
        for (const ele of shortNavItem) {
            ele.classList.remove("active");
        }
        element.classList.add("active");
    });
}

const listingMapBox = document.getElementsByClassName("listing-map-box");
for (const element of listingMapBox) {
    element.addEventListener("click", () => {
        for (const ele of listingMapBox) {
            ele.classList.remove("active");
        }
        element.classList.add("active");
    });
}

$(document).ready(function () {
    $(".js-example-basic-single").select2({
        width: '100%'
    });
    $(".testimonials").owlCarousel({
        loop: true,
        margin: 25,
        nav: false,
        dots: true,
        autoplay : true,
        autoplayTimeout: 3000,
        responsive: {
            0: {
                items: 1,
            },
            768: {
                items: 2,
            },
            992: {
                items: 3,
            },
        },
    });
    // RANGE SLIDER
    $(".js-range-slider").ionRangeSlider({
        type: "double",
        min: 0,
        max: 1000,
        from: 200,
        to: 500,
        grid: true,
    });

    $("#shareBlock").socialSharingPlugin({
        urlShare: window.location.href,
        description: $("meta[name=description]").attr("content"),
        title: $("title").text(),
    });


});




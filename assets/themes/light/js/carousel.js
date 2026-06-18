$(document).ready(function () {
    $(".testimonials").owlCarousel({
        loop: true,
        margin: 25,
        nav: false,
        dots: true,
        autoplay: true,
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

    $(".categories-alphabet").owlCarousel({
        loop: true,
        margin: 0,
        nav: true,
        dots: false,
        autoplay: false,
        autoplayTimeout: 3000,
        responsive: {
            0: {
                items: 5,
            },
            768: {
                items: 10,
            },
            992: {
                items: 15,
            },
            1200: {
                items: 20,
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


if (document.querySelector("#mainCarousel") != null) {
    // Initialise Carousel
    const mainCarousel = new Carousel(document.querySelector("#mainCarousel"), {
        Dots: false,
    });

// Thumbnails
    const thumbCarousel = new Carousel(document.querySelector("#thumbCarousel"), {
        Sync: {
            target: mainCarousel,
            friction: 0,
        },
        Dots: false,
        Navigation: false,
        center: true,
        slidesPerPage: 1,
        infinite: true,
    });
}



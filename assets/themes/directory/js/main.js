"use strict";
// Preloader area
const preloader = document.getElementById("preloader");
const preloaderFunction = () => {
    preloader.style.display = "none";
};

// toggleSideMenu start
const toggleSideMenu = () => {
    document.body.classList.toggle("toggle-sidebar");
};
// toggleSideMenu end

// add bg to nav
const header = document.querySelector('nav');
window.addEventListener('scroll', () => {
    header.classList.toggle('active', window.scrollY >= 100);
});

$(document).ready(function () {
    // Testimonial section start
    // Owl carousel
    $('.testimonial-carousel').owlCarousel({
        loop: true,
        autoplay: true,
        margin: 20,
        navText: ["<i class='fa-regular fa-angle-left'></i>", "<i class='fa-regular fa-angle-right'></i>"],
        // rtl: true,
        responsive: {
            0: {
                items: 1,
                nav: false,
                dots: true,
                dotsEach: 3

            },
            600: {
                items: 1,
                nav: false,
                dots: true,
                dotsEach: 2

            },
            768: {
                items: 1,
                nav: true,
                dots: false,

            },
            1000: {
                items: 2,
                nav: true,
                dots: false,
            }
        }
    });
    // Category-slider carousel
    $('.category-slider').owlCarousel({
        loop: true,
        autoplay: false,
        margin: 20,
        responsiveClass: true,
        dots: false,
        nav: true,
        navText: ["<i class='fa-regular fa-angle-left'></i>", "<i class='fa-regular fa-angle-right'></i>"],
        // rtl: true,
        responsive: {
            0: {
                items: 1,
                nav: false,
                dots: true,
                dotsEach: 4
            },
            400: {
                items: 2,
                nav: false,
                dots: true,
                dotsEach: 4
            },

            768: {
                nav: true,
                dots: false
            },
            992: {
                items: 4,
                nav: true,
                dots: false
            },
            1200: {
                items: 5,
                nav: true,
                dots: false
            },

        }
    });
    // Category-slider carousel
    $('.listing-slider').owlCarousel({
        loop: true,
        autoplay: false,
        margin: 20,
        responsiveClass: true,
        dots: false,
        nav: true,
        navText: ["<i class='fa-regular fa-angle-left'></i>", "<i class='fa-regular fa-angle-right'></i>"],
        // rtl: true,
        responsive: {
            0: {
                items: 1,
                nav: false,
                dots: true,
                dotsEach: 3
            },
            600: {
                items: 2,
                nav: false,
                dots: true,
                dotsEach: 3
            },
            768: {
                nav: true,
                dots: false,
                items: 2,
            },
            992: {
                items: 3,
                nav: true,
                dots: false
            },

        }
    });

    // banner slider start
    $('.banner-slider').owlCarousel({
        loop: true,
        autoplay: true,
        responsiveClass: true,
        dots: false,
        nav: true,
        navText: ["<i class='fa-regular fa-angle-left'></i>", "<i class='fa-regular fa-angle-right'></i>"],
        // rtl: true,
        responsive: {
            0: {
                items: 1,
                nav: false,
            },
            600: {
                items: 2,
                nav: false,
            },
            768: {
                nav: true,
                dots: false,
                items: 3,
            },
            1200: {
                items: 4,
                nav: true,
                dots: false
            },

        }
    });
    // banner slider end


    // cmn select2 start
    $('.cmn-select2').select2();
    // cmn select2 end

    // cmn-select2 with image start
    $('.cmn-select2-image').select2({
        templateResult: formatState,
        templateSelection: formatState
    });
    // select2 function
    function formatState(state) {
        if (!state.id) {
            return state.text;
        }
        var baseUrl = "assets/img/mini-flag";
        var $state = $(
            '<span><img src="' + baseUrl + '/' + state.element.value.toLowerCase() + '.svg" class="img-flag" /> ' + state.text + '</span>'
        );
        return $state;
    };
    function formatState(state) {
        if (!state.id) {
            return state.text;
        }

        var baseUrl = "assets/img/mini-flag";
        var $state = $(
            '<span><img class="img-flag" /> <span></span></span>'
        );

        // Use .text() instead of HTML string concatenation to avoid script injection issues
        $state.find("span").text(state.text);
        $state.find("img").attr("src", baseUrl + "/" + state.element.value.toLowerCase() + ".svg");

        return $state;
    };
    // cmn-select2 with image end

    // Cmn select2 tags start
    $(".cmn-select2-tags").select2({
        tags: true
    });
    // Cmn select2 tags end



    // Payment method with image2 start
    $(document).ready(function () {
        $('.payment-method-select2-image').select2({
            templateResult: paymentMethod,
            templateSelection: paymentMethod
        });
    });
    // select2 function
    function paymentMethod(state) {
        if (!state.id) {
            return state.text;
        }
        var baseUrl = "assets/img/gateway";
        var $state = $(
            '<span><img src="' + baseUrl + '/' + state.element.value.toLowerCase() + '.jpg" class="img-flag" /> ' + state.text + '</span>'
        );
        return $state;
    };
    function paymentMethod(state) {
        if (!state.id) {
            return state.text;
        }

        var baseUrl = "assets/img/gateway";
        var $state = $(
            '<span><img class="img-flag" /> <span></span></span>'
        );

        // Use .text() instead of HTML string concatenation to avoid script injection issues
        $state.find("span").text(state.text);
        $state.find("img").attr("src", baseUrl + "/" + state.element.value.toLowerCase() + ".jpg");

        return $state;
    };
    // Payment method with image2 start

    // cmn select2 modal start
    $(".modal-select").select2({
        dropdownParent: $("#formModal"),
        placeholder: "Select option",
    });
    $(".modal-select2").select2({
        dropdownParent: $("#formModal2"),
    });
    // cmn select2 modal start


    // Fancybox carousel section start
    // Initialise Carousel
    if ($("#mainCarousel").length) {
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

        // Customize Fancybox
        Fancybox.bind('[data-fancybox="gallery"]', {
            Carousel: {
                on: {
                    change: (that) => {
                        mainCarousel.slideTo(mainCarousel.findPageForSlide(that.page), {
                            friction: 0,
                        });
                    },
                },
            },
        });
    }
    // Fancybox carousel section end

    // magnificPopup start
    if ($('.magnific-popup').length) {
        $('.magnific-popup').magnificPopup({
            type: 'image',
            delegate: 'a',
            gallery: {
                enabled: true
            }
        });
    }
    // magnificPopup end


});


// input file preview
const previewImage = (id) => {
    document.getElementById(id).src = URL.createObjectURL(event.target.files[0]);
};

// Tooltip
const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

// Copy text start
function copyTextFunc() {
    // get the container
    const element = document.querySelector('.docs-code');
    // Create a fake `textarea` and set the contents to the text
    // you want to copy
    const storage = document.createElement('textarea');
    storage.value = element.innerHTML;
    element.appendChild(storage);

    // Copy the text in the fake `textarea` and remove the `textarea`
    storage.select();
    storage.setSelectionRange(0, 99999);
    document.execCommand('copy');
    element.removeChild(storage);
}
// Copy text end

// Social share start
if ($("#shareBlock").length) {
    $("#shareBlock").socialSharingPlugin({
        urlShare: window.location.href,
        description: $("meta[name=description]").attr("content"),
        title: $("title").text(),
    });
}
// Social share end




// Nice select start
if ($(".nice-select").length) {
    $('.nice-select').niceSelect();
}
// Nice select end

// Range area start
if ($(".js-range-slider").length) {
    $(".js-range-slider").ionRangeSlider({
        type: "double",
        min: 0,
        max: 100,
        from: 800,
        to: 500,
        grid: true
    });
}
// Range area end
// International Telephone Input start
if ($("#telephone").length) {
    const input = document.querySelector("#telephone");
    window.intlTelInput(input, {
        initialCountry: "bd",
        separateDialCode: true,
    });
}
// International Telephone Input end
// Dropdown select with Filter end
if ($(".multiple-search-box").length) {
    function handleSelect(inputBox, searchInput, searchItem, inputField) {
        searchInput.addEventListener('click', function (event) {
            inputBox.classList.add('active');
            event.stopPropagation();
        });

        window.addEventListener('click', function () {
            inputBox.classList.remove('active');
        });

        searchItem.forEach(function (searchItemSingle) {
            searchItemSingle.addEventListener('click', function () {
                const text = searchItemSingle.querySelector(".title");
                const textContent = text.textContent;
                const thisDataId = searchItemSingle.getAttribute('data-id');
                searchInput.value = textContent;
                inputField.value = thisDataId;
                inputBox.classList.remove('active');
            });
        });
    }

    const inputBox = document.querySelector('#input-box');
    const searchInput = document.querySelector('#search-input');
    const searchItem = document.querySelectorAll('#search-result .search-item');
    const inputField = document.querySelector('#search-input-value');
    handleSelect(inputBox, searchInput, searchItem, inputField);

    const inputBox2 = document.querySelector('#input-box2');
    const searchInput2 = document.querySelector('#search-input2');
    const searchItem2 = document.querySelectorAll('#search-result2 .search-item');
    const inputField2 = document.querySelector('#search-input2-value');
    handleSelect(inputBox2, searchInput2, searchItem2,inputField2);

    const inputBox3 = document.querySelector('#input-box3');
    const searchInput3 = document.querySelector('#search-input3');
    const searchItem3 = document.querySelectorAll('#search-result3 .search-item');
    const inputField3 = document.querySelector('#search-input3-value');
    handleSelect(inputBox3, searchInput3, searchItem3,inputField3);

    const inputBox4 = document.querySelector('#input-box4');
    const searchInput4 = document.querySelector('#search-input4');
    const searchItem4 = document.querySelectorAll('#search-result4 .search-item');
    const inputField4 = document.querySelector('#search-input4-value');
    handleSelect(inputBox4, searchInput4, searchItem4, inputField4);


    // filter start
    function filterItems(inputId, items) {
        const input = document.getElementById(inputId);
        const filter = input.value.toUpperCase();

        items.forEach((item) => {
            const title = item.querySelector(".title");
            const txtValue = title.textContent || title.innerText;

            if (txtValue.toUpperCase().includes(filter)) {
                item.style.display = "";
            } else {
                item.style.display = "none";
            }
        });
    }
    const filterSearchInputId = "search-input";
    const filterSearchInput = document.getElementById(filterSearchInputId);
    const items = document.querySelectorAll("#search-result .search-item");
    filterSearchInput.addEventListener("keyup", function () {
        filterItems(filterSearchInputId, items);
    })

    const filterSearchInputId2 = "search-input2";
    const filterSearchInput2 = document.getElementById(filterSearchInputId2);
    const items2 = document.querySelectorAll("#search-result2 .search-item");
    filterSearchInput2.addEventListener("keyup", function () {
        filterItems(filterSearchInputId2, items2);
    })

    const filterSearchInputId3 = "search-input3";
    const filterSearchInput3 = document.getElementById(filterSearchInputId3);
    const items3 = document.querySelectorAll("#search-result3 .search-item");
    filterSearchInput3.addEventListener("keyup", function () {
        filterItems(filterSearchInputId3, items3);
    })
}
// Dropdown select with Filter end



@if(basicControl()->analytic_status)
    <!--Start of Google analytic Script-->
	<script async src="https://www.googletagmanager.com/gtag/js?id={{trim(basicControl()->MEASUREMENT_ID)}}"></script>
	<script>
		"use strict";
		$(document).ready(function () {
			var MEASUREMENT_ID = "{{ basicControl()->MEASUREMENT_ID }}";
			window.dataLayer = window.dataLayer || [];

			function gtag() {
				dataLayer.push(arguments);
			}

			gtag('js', new Date());
			gtag('config', MEASUREMENT_ID);
		});
	</script>
    <!--End of Google analytic Script-->
@endif


@if(basicControl()->tawk_status)

    <!--Start of Tawk.to Script-->
    <script type="text/javascript">
        $(document).ready(function () {
             var Tawk_SRC = 'https://embed.tawk.to/' + "{{ trim(basicControl()->tawk_id)}}";
            var Tawk_API = Tawk_API || {}, Tawk_LoadStart = new Date();
            (function () {
                var s1 = document.createElement("script"), s0 = document.getElementsByTagName("script")[0];
                s1.async = true;
                s1.src = Tawk_SRC;
                s1.charset = 'UTF-8';
                s1.setAttribute('crossorigin', '*');
                s0.parentNode.insertBefore(s1, s0);
            })();
        });
    </script>
    <!--End of Tawk.to Script-->
@endif


@if(basicControl()->fb_messenger_status)
    <!--start of Facebook Messenger Script-->
	<div id="fb-root"></div>
	<script>
		"use strict";
		$(document).ready(function () {
			var fb_app_id = "{{ basicControl()->fb_app_id }}";
			window.fbAsyncInit = function () {
				FB.init({
					appId: fb_app_id,
					autoLogAppEvents: true,
					xfbml: true,
					version: 'v10.0'
				});
			};
			(function (d, s, id) {
				var js, fjs = d.getElementsByTagName(s)[0];
				if (d.getElementById(id)) return;
				js = d.createElement(s);
				js.id = id;
				js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
				fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));
		});
	</script>
	<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js"></script>
	<div class="fb-customerchat" page_id="{{ basicControl()->fb_page_id }}"></div>
    <!--End of Facebook Messenger Script-->
@endif


<script>
    let root = document.querySelector(':root');
{{--    @if(getTheme() == 'light' && isset($fromUser))--}}
    @if(getTheme() == 'light')
    root.style.setProperty('--primary', '{{basicControl()->primary_color ?? '#ff5c14' }}');
{{--    @elseif(getTheme() == 'directory' && isset($fromUser))--}}
    @elseif(getTheme() == 'directory')
    root.style.setProperty('--base', hexToHSL('{{basicControl()->secondary_color ?? '#f0002a' }}'));
    root.style.setProperty('--primary-color', '{{basicControl()->secondary_color ?? '#f0002a' }}');
    @endif


    function hexToHSL(hex) {
        // Convert hex to RGB first
        let r = 0, g = 0, b = 0;
        if (hex.length == 4) {
            r = "0x" + hex[1] + hex[1];
            g = "0x" + hex[2] + hex[2];
            b = "0x" + hex[3] + hex[3];
        } else if (hex.length == 7) {
            r = "0x" + hex[1] + hex[2];
            g = "0x" + hex[3] + hex[4];
            b = "0x" + hex[5] + hex[6];
        }

        // Then to HSL
        r /= 255;
        g /= 255;
        b /= 255;
        let max = Math.max(r, g, b), min = Math.min(r, g, b);
        let h, s, l = (max + min) / 2;
        if (max == min) {
            h = s = 0; // achromatic
        } else {
            let d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            switch (max) {
                case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                case g: h = (b - r) / d + 2; break;
                case b: h = (r - g) / d + 4; break;
            }
            h /= 6;
        }
        return `${Math.round(h * 360)} ${Math.round(s * 100)}% ${Math.round(l * 100)}%`;
    }

</script>

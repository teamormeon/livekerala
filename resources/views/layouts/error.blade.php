<!DOCTYPE html>
<html lang="en" @if(session()->get('rtl') == 1) dir="rtl" @endif >

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link href="{{ getFile(basicControl()->favicon_driver, basicControl()->favicon) }}" rel="icon">
    <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrap5.min.css') }}">

    <style>
        @import url("https://fonts.googleapis.com/css2?family=Asap:ital,wght@0,100..900;1,100..900&family=Montserrat:ital,wght@0,100..900&display=swap");
        :root {
            --primary: #ff5c14;
            --primary-rgb: 255, 92, 20;
            --secondary: #e85f4c;
            --white: #fff;
            --black: #141720;
            --bgLight: #f7f7f8;
            --textColor: #353535;
            --shadow: 0px 2px 16.8px 3.2px rgba(0, 38, 66, 0.08);
            --shadow2: 0 0.375rem 0.75rem rgba(140, 152, 164, .075);
            --heading-font: "Montserrat", sans-serif;
        }
        .error-section {
            background: rgba(255, 92, 20, 0.1);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }
        .error-content {
            color: #2e0307;
        }

        .error-title {
            font-size: 150px;
            font-family: "DM Sans", sans-serif;
            font-weight: 600;
            line-height: 1;
        }

        .error-info {
            font-size: 40px;
            line-height: 1.3;
        }

        .btn-area {
            margin-top: 30px;
        }

        .error-btn {
            background: var(--primary);
            padding: 10px 20px;
            border-radius: 5px;
            transition: 0.5s;
            font-size: 16px;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 500;
            font-family: var(--heading-font), serif;
            text-transform: capitalize;
            gap: 5px;
            text-decoration: none;
        }

        .error-btn:hover {
            color: #fff;
        }

        .error-img {
            width: 100%;
            height: 600px;
        }

        @media (max-width: 991px) {
            .error-section {
                height: auto;
            }

            .error-title {
                font-size: 100px;
            }

            .error-info {
                font-size: 30px;
            }
        }

        @media (max-width: 575px) {
            .error-img {
                height: 350px;
            }
        }
    </style>
</head>

<body class="pb-0">

<section class="error-section">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-sm-6">
                <div class="error-thum">
                    @hasSection('error_image')
                        @yield('error_image')
                    @else
                        <img class="error-img" src="{{ asset(config('filelocation.error2')) }}" alt="...">
                    @endif
                </div>

            </div>
            <div class="col-sm-6">
                <div class="error-content">
                    <div class="error-title">@yield('error_code')</div>
                    <div class="error-info">@yield('error_message')</div>
                    <div class="btn-area">
                        <a href="{{ url('/') }}" class="error-btn ">
                            <img src="{{ asset('assets/global/images/icon/w-left-arrow.png') }}"
                                 height="30" width="30" alt="..."> {{ trans('Back to Homepage') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<script src="{{ asset('assets/global/js/bootstrap.bundle.min.js') }}"></script>

<script>
    let root = document.querySelector(':root');
    @if(getTheme() == 'light')
    root.style.setProperty('--primary', '{{basicControl()->primary_color}}' ?? '#ff5c14');
    @else
    root.style.setProperty('--primary', '{{basicControl()->secondary_color}}' ?? '#f0002c');
    @endif
</script>

</body>
</html>

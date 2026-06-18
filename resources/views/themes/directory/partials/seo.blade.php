
@if(isset($pageSeo) && isset($single_listing_details->listingSeo))
    <meta name="theme-color" :content="{{ basicControl()->primary_color }}">
    <meta name="author" :content="{{basicControl()->site_title}}">
    <meta name="title" :content="{{ optional($single_listing_details->listingSeo)->meta_title . ' | ' . basicControl()->site_title }}">
    <meta name="description" :content="{{ optional($single_listing_details->listingSeo)->meta_description ? Illuminate\Support\Str::limit(optional($single_listing_details->listingSeo)->meta_description, 500) : '' }}">
    <meta name="keywords" :content="{{ optional($single_listing_details->listingSeo)->meta_keywords ?? '' }}">
    <meta name="robots" :content="{{ optional($single_listing_details->listingSeo)->meta_robots ?? '' }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ basicControl()->site_title ?? '' }}">
    <meta property="og:title" :content="{{ optional($single_listing_details->listingSeo)->meta_title ?? '' }}">
    <meta property="og:description" :content="{{ optional($single_listing_details->listingSeo)->og_description ? Illuminate\Support\Str::limit(optional($single_listing_details->listingSeo)->og_description, 500) : '' }}">
    <meta property="og:image" :content="{{ optional(@$single_listing_details->listingSeo)->seo_image ? getFile(optional(@$single_listing_details->listingSeo)->driver, optional(@$single_listing_details->listingSeo)->seo_image) : '' }}">
    <meta property="og:url" :content="{{ url()->current() }}">
    <meta name="twitter:card" :content="{{ optional($single_listing_details->listingSeo)->meta_title ?? '' }}">
    <meta name="twitter:title" :content="{{ optional($single_listing_details->listingSeo)->meta_title ?? '' }}">
    <meta property="twitter:description" :content="{{ optional($single_listing_details->listingSeo)->meta_description ? Illuminate\Support\Str::limit(optional($single_listing_details->listingSeo)->meta_description, 500) : '' }}">
    <meta property="twitter:image" :content="{{ optional($single_listing_details->listingSeo)->seo_image ? getFile(optional(@$single_listing_details->listingSeo)->driver, optional(@$single_listing_details->listingSeo)->seo_image) : '' }}">
@elseif(isset($pageSeo))
    <meta name="theme-color" :content="{{ basicControl()->primary_color }}">
    <meta name="author" :content="{{basicControl()->site_title}}">
    <meta name="title" :content="{{ $pageSeo['meta_title'] . ' | ' . basicControl()->site_title }}">
    <meta name="description" :content="{{ isset($pageSeo['meta_description']) ? Illuminate\Support\Str::limit($pageSeo['meta_description'], 500) : '' }}">
    <meta name="keywords" :content="{{ is_array(@$pageSeo['meta_keywords']) ? implode(', ', @$pageSeo['meta_keywords']) : '' }}">
    <meta name="robots" :content="{{ $pageSeo['meta_robots'] ?? '' }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ basicControl()->site_title ?? '' }}">
    <meta property="og:title" :content="{{ $pageSeo['meta_title'] ?? '' }}">
    <meta property="og:description" :content="{{ isset($pageSeo['og_description']) ? Illuminate\Support\Str::limit($pageSeo['og_description'], 500) : '' }}">
    <meta property="og:image" :content="{{ @$pageSeo['meta_image'] ? getFile(@$pageSeo['meta_image_driver'], @$pageSeo['meta_image']) : '' }}">
    <meta property="og:url" :content="{{ url()->current() }}">
    <meta name="twitter:card" :content="{{ $pageSeo['meta_title'] ?? '' }}">
    <meta name="twitter:title" :content="{{ $pageSeo['meta_title'] ?? '' }}">
    <meta property="twitter:description" :content="{{ isset($pageSeo['meta_description']) ? Illuminate\Support\Str::limit($pageSeo['meta_description'], 500) : '' }}">
    <meta property="twitter:image" :content="{{ $pageSeo['meta_image'] ? getFile(@$pageSeo['meta_image_driver'], @$pageSeo['meta_image']) : '' }}">
@endif

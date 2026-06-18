@extends('user_panel.layouts.user')
@section('title')
    @lang("Reviews")
@endsection
@push('style')
    <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrap-datepicker.css') }}"/>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="main row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0">@lang('Reviews')</h3>
                </div>

                <!-- table -->
                <div class="table-parent table-responsive">
                    <div class="table-heading py-3 d-flex justify-content-between align-items-center">
                        <h4>@lang('Reviews of ') (@lang($listing->title))</h4>
                        <button type="button" class="cmn-btn customButton" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">@lang('Filter')<i class="fal fa-filter"></i>
                        </button>
                    </div>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>@lang('SL')</th>
                            <th>@lang('User')</th>
                            <th>@lang('Rating')</th>
                            <th>@lang('Review')</th>
                            <th>@lang('Date-Time')</th>
                        </tr>
                        </thead>
                        <tbody>

                        @forelse($allReviews as $key => $review)
                            <tr>
                                <td>{{++$key}}</td>
                                <td class="company-logo p-0" data-label="@lang('User')">
                                    <div>
                                        <a href="{{ route('profile', optional($review->review_user_info)->username) }}"
                                           target="_blank">
                                            <img src="{{ getFile(optional($review->review_user_info)->image_driver, optional($review->review_user_info)->image) }}">
                                        </a>
                                    </div>
                                    <div>
                                        @lang(optional($review->review_user_info)->firstname.' '.optional($review->review_user_info)->lastname) <br>
                                        @lang(optional($review->review_user_info)->email)
                                    </div>
                                </td>
                                <td data-label="@lang('Rating')">
                                    @php
                                        $j = 0;
                                    @endphp
                                    @for ($i = $review->rating; $i > 0; $i--)
                                        <i class="fas fa-star rating__gold"></i>
                                        @php
                                            $j = $j + 1;
                                        @endphp
                                    @endfor

                                    @for($j; $j < 5; $j++)
                                        <i class="far fa-star rating__gold"></i>
                                    @endfor
                                </td>
                                <td data-label="@lang('Review')">
                                    @lang(Str::limit($review->review, 100))
                                </td>

                                <td data-label="@lang('Date-Time')">
                                    {{ dateTime($review->created_at) }}
                                </td>
                        @empty
                            <td class="text-center" colspan="100%">
                                <img class="noDataImg" src="{{ asset('assets/admin/img/oc-error.svg') }}" alt="image">
                                <p class="mt-3">@lang('No data available')</p>
                            </td>
                        @endforelse
                        </tbody>
                    </table>
                    {{ $allReviews->appends($_GET)->links('user_panel.partials.pagination') }}
                </div>
            </div>
        </div>
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
        <div class="offcanvas-header">
            <h4 class="offcanvas-title" id="offcanvasExampleLabel">@lang('Filter Review')</h4>
            <button type="button" class="cmn-btn-close" data-bs-dismiss="offcanvas" aria-label="Close">
                <i class="fal fa-arrow-right"></i>
            </button>
        </div>
        <div class="offcanvas-body">
            <form action="" method="get" enctype="multipart/form-data">
                <div class="row g-4">
                    <div class="input-box">
                        <label class="form-label">@lang('User')</label>
                        <select name="user" id="user" class="form-control js-example-basic-single">
                            <option selected disabled>@lang('Select User')</option>
                            @foreach($allReviews as $reviewUser)
                                <option value="{{ optional($reviewUser->review_user_info)->id }}" {{  request()->user == optional($reviewUser->review_user_info)->id ? 'selected' : '' }}>
                                    @lang(optional($reviewUser->review_user_info)->firstname.' '.optional($reviewUser->review_user_info)->lastname)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="input-box">
                        <label class="form-label">@lang('Rating')</label>
                        <select name="rating[]" class="form-control js-example-basic-single">
                            <option disabled selected> @lang('Select Rating')</option>
                            <option value="5" @if(isset(request()->rating)) @foreach(request()->rating as $data) @if($data == 5) selected @endif @endforeach @endif>
                                @lang('5 Star')
                            </option>

                            <option value="4" @if(isset(request()->rating)) @foreach(request()->rating as $data) @if($data == 4) selected @endif @endforeach @endif>
                                @lang('4 Star')
                            </option>

                            <option value="3" @if(isset(request()->rating)) @foreach(request()->rating as $data) @if($data == 3) selected @endif @endforeach @endif>
                                @lang('3 Star')
                            </option>

                            <option value="2" @if(isset(request()->rating)) @foreach(request()->rating as $data) @if($data == 2) selected @endif @endforeach @endif>
                                @lang('2 Star')
                            </option>

                            <option value="1" @if(isset(request()->rating)) @foreach(request()->rating as $data) @if($data == 1) selected @endif @endforeach @endif>
                                @lang('1 Star')
                            </option>
                        </select>
                    </div>
                    <div class="input-box">
                        <label for="search" class="form-label">@lang('From Date')</label>
                        <input type="text" class="form-control datepicker from_date" name="from_date" autofocus="off" readonly placeholder="@lang('From Date')" value="{{ old('from_date',request()->from_date) }}">
                    </div>
                    <div class="input-box">
                        <label for="search" class="form-label">@lang('To Date')</label>
                        <input type="text" class="form-control datepicker to_date" name="to_date" autofocus="off" readonly placeholder="@lang('To Date')" value="{{ old('to_date',request()->to_date) }}" disabled="true">
                    </div>
                    <div class="btn-area">
                        <button type="submit" class="cmn-btn w-100">@lang('Filter')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('assets/global/js/bootstrap-datepicker.js') }}"></script>
    <script>
        'use strict'
        $(document).ready(function () {
            $(".datepicker").datepicker({
                autoclose: true,
                clearBtn: true
            });

            $('.from_date').on('change', function () {
                $('.to_date').removeAttr('disabled');
            });
        });
    </script>
@endpush


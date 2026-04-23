@extends('user_panel.layouts.user')
@section('title',trans('Dashboard'))
@section('content')
    <div class="container-fluid">
        <div class="main row">
            <div class="col-12">
                <div class="row g-4 mb-4">
                    <div class="col-xl-3 col-md-6">
                        <div class="dashboard-box">
                            <h5>@lang('Total Listings')</h5>
                            <h3>
                                <a href="{{ route('user.listings') }}">
                                    {{ number_format($listings['listing_infos']) }}
                                </a>
                            </h3>
                            <i class="fal fa-box-open"></i>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="dashboard-box">
                            <h5>@lang('Active Listings')</h5>
                            <h3>
                                <a href="{{ route('user.listings', 'approved') }}">
                                    {{ number_format($listings['activeListing']) }}
                                </a>
                            </h3>
                            <i class="fal fa-box-open"></i>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="dashboard-box">
                            <h5>@lang('Pending Listings')</h5>
                            <h3>
                                <a href="{{ route('user.listings', 'pending') }}">
                                    {{ number_format($listings['pendingListing']) }}
                                </a>
                            </h3>
                            <i class="fal fa-box-open"></i>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="dashboard-box">
                            <h5>@lang('Views')</h5>
                            <h3>
                                <a href="javascript:void(0)">{{ $all_viewers_count }}</a>

                            </h3>
                            <i class="fal fa-street-view"></i>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="dashboard-box box-3">
                            <h5>@lang('Products')</h5>
                            <h3>
                                <a href="javascript:void(0)">{{ $totalProduct }}</a>
                            </h3>
                            <i class="fal fa-shopping-basket"></i>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="dashboard-box box-3">
                            <h5>@lang('Unseen Enquiries')</h5>
                            <h3>
                                <a href="{{ route('user.product.queries', 'customer-enquiry') }}">{{ $productUnseenQuires }}</a>
                            </h3>
                            <i class="fal fa-envelope-open-text"></i>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="dashboard-box box-4">
                            <h5>@lang('Active Package')</h5>
                            <h3>
                                <a href="{{ route('user.myPackages') }}">
                                    {{ $activePackage }}
                                </a>
                            </h3>
                            <i class="fal fa-box-full" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="dashboard-box box-4">
                            <h5>@lang('Pending Package')</h5>
                            <h3>
                                <a href="{{ route('user.myPackages') }}">
                                    {{ $pendingPackage }}
                                </a>
                            </h3>
                            <i class="fal fa-box-full" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>

                <!-- charts -->
                <section class="chart-information">
                    <div class="row">
                        <div class="col-lg-6 mb-4 mb-lg-0">
                            <div class="progress-wrapper p-3">
                                <h3 class="card-title">@lang("Upcoming Expired Packages")</h3>
                                <div id="calendar"></div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="progress-wrapper">
                                <h3 class="my__package__heading">@lang('My Packages')</h3>
                                <div
                                    class="progress-container d-flex flex-column flex-sm-row justify-content-around mt-3 mt-sm-5 pb-3 pb-sm-5">
                                    @forelse($userPurchasePackage as $key => $package)
                                        @if($package->no_of_listing != null)
                                            @php
                                                $total_listing = optional($package->get_package)->no_of_listing;
                                                $remaining_listing = $package->no_of_listing;
                                                $expence_listing = $total_listing - $remaining_listing;
                                                $total_used_listing_percent = $total_listing > 0 ? ($expence_listing * 100 / $total_listing) : 0;
                                            @endphp
                                        @else
                                            @php
                                                $total_used_listing_percent = 'Unlimited';
                                            @endphp
                                        @endif

                                        @for($i = $key; $i <= $key; $i++)
                                            @if($package->no_of_listing != null)
                                                <div class="circular-progress cp_1 mt-sm-5 mt-3 pb-sm-5 pb-3">
                                                    <svg class="radial-progress"
                                                         data-percentage="{{ $total_used_listing_percent }}"
                                                         viewBox="0 0 80 80">
                                                        <circle class="incomplete plan-stroke{{ $i }}" cx="40" cy="40"
                                                                r="35"></circle>
                                                        <circle class="complete same-cricle plan-stroke-percent{{ $i }}"
                                                                cx="40" cy="40" r="35"></circle>
                                                        <text class="percentage" x="50%" y="53%"
                                                              transform="matrix(0, 1, -1, 0, 80, 0)">
                                                            {{ (int)$total_used_listing_percent }} %
                                                        </text>
                                                        <i class="fal fa-box-open mt-2"></i> @lang('Used')
                                                    </svg>
                                                    <h4 class="golden-text mt-4 text-center">
                                                        <a href="{{ route('user.myPackages', $package->id) }}">
                                                            @lang(optional(optional($package->get_package)->details)->title)
                                                        </a>
                                                    </h4>
                                                </div>
                                            @else
                                                <div
                                                    class="circular-progress cp_1 circular-progress cp_1 circular-progress cp_1 circular-progress cp_1 mt-5 pb-5">
                                                    <svg class="radial-progress" data-percentage="0"
                                                         viewBox="0 0 80 80">
                                                        <circle class="incomplete plan-stroke{{ $i }}" cx="40" cy="40"
                                                                r="35"></circle>
                                                        <circle class="complete same-cricle plan-stroke-percent{{ $i }}"
                                                                cx="40" cy="40" r="35"></circle>
                                                        <text class="percentage" x="50%" y="53%"
                                                              transform="matrix(0, 1, -1, 0, 80, 0)">
                                                            @lang('Unlimited')
                                                        </text>
                                                        <i class="fal fa-box-open mt-2"></i>
                                                        <i class="far fa-infinity mt-2"></i>
                                                    </svg>
                                                    <h4
                                                        class="golden-text mt-4 text-center">
                                                        <a href="{{ route('user.myPackages', $package->id) }}">
                                                            @lang(optional(optional($package->get_package)->details)->title)
                                                        </a>
                                                    </h4>
                                                </div>
                                            @endif
                                        @endfor
                                    @empty
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- table -->

                <div class="table-parent table-responsive">
                    <div class="py-3 d-flex justify-content-between align-items-center table-heading">
                        <h4>@lang('Latest Listings')</h4>
                        <button type="button" class="cmn-btn customButton" data-bs-toggle="offcanvas"
                                data-bs-target="#offcanvasExample"
                                aria-controls="offcanvasExample">@lang('Filter')<i class="fal fa-filter"></i>
                        </button>
                    </div>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th scope="col">@lang('Package')</th>
                            <th scope="col">@lang('Category')</th>
                            <th scope="col">@lang('Listing')</th>
                            <th scope="col">@lang('Address')</th>
                            <th scope="col">@lang('Status')</th>
                            <th scope="col" class="text-end">@lang('Action')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($user_listings as $key => $item)
                            <tr>
                                <td data-label="Package">
                                    @lang(optional(optional(optional($item->get_package)->get_package)->details)->title)
                                </td>

                                <td data-label="Category">
                                    @lang($item->getCategoriesName())
                                </td>

                                <td data-label="Listing">
                                    <a href="{{ route('listing.details',$item->slug) }}"
                                       target="_blank">@lang($item->title)</a>
                                </td>

                                <td data-label="Address">@lang($item->address)</td>

                                <td data-label="Status">
                                    @if($item->status == 1)
                                        <span class="badge  bg-success">@lang('Approved')</span>
                                    @elseif($item->status == 2)
                                        <span class="badge  bg-danger">@lang('Rejected')</span>
                                    @else
                                        <span class="badge  bg-info">@lang('Pending')</span>
                                    @endif
                                </td>

                                <td data-label="@lang('Action')">
                                    <div class="dropdown-btns">
                                        <button type="button" class="dropdown-toggle" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                            <i class="far fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a href="{{ route('user.analytics', $item->id) }}"
                                                   class="btn currentColor dropdown-item">
                                                    <i class="fal fa-analytics me-2"></i> @lang('Analytics')
                                                </a>
                                            </li>

                                            <li>
                                                <a href="{{ route('user.reviews', $item->id) }}"
                                                   class="btn currentColor dropdown-item">
                                                    <i class="far fa-star me-2"></i> @lang('Reviews')
                                                </a>
                                            </li>

                                            <li>
                                                <a href="{{ route('user.editListing', $item->id) }}"
                                                   class="btn currentColor dropdown-item">
                                                    <i class="far fa-edit me-2"></i> @lang('Edit')
                                                </a>
                                            </li>

                                            <li>
                                                <a href="javascript:void(0)" data-bs-toggle="modal"
                                                   data-bs-target="#delete-modal"
                                                   class="btn currentColor notiflix-confirm dropdown-item"
                                                   data-route="{{ route('user.deleteListing', $item->id) }}">
                                                    <i class="far fa-trash me-2"></i> @lang('Delete')
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <td class="text-center" colspan="100%"> @lang('No Data Found')</td>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('loadModal')
        <!-- Delete Modal -->
        <div class="modal fade" id="delete-modal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-top modal-md">
                <div class="modal-content">
                    <div class="modal-header modal-primary modal-header-custom">
                        <h4 class="modal-title" id="editModalLabel">@lang('Delete Confirmation')</h4>
                        <button type="button" class="close-btn" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fal fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        @lang('Are you sure want to delete this Listing?')
                    </div>
                    <hr>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')</button>
                        <form action="" method="post" class="deleteRoute">
                            @csrf
                            @method('delete')
                            <button type="submit"
                                    class="rounded-3 text-capitalize btn-custom addCreateListingRoute">@lang('Confirm')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endpush

    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
        <div class="offcanvas-header">
            <h4 class="offcanvas-title" id="offcanvasExampleLabel">@lang('Filter Listing')</h4>
            <button type="button" class="cmn-btn-close" data-bs-dismiss="offcanvas" aria-label="Close">
                <i class="fal fa-arrow-right"></i>
            </button>
        </div>
        <div class="offcanvas-body">
            <form action="" method="get" enctype="multipart/form-data">
                <div class="row g-4">
                    <div class="input-box">
                        <label for="search" class="form-label">@lang('Search')</label>
                        <input type="text" name="listing_search_name" class="form-control" id="search"
                               placeholder="@lang('Listing')" value="{{ request()->listing_search_name }}"/>
                    </div>
                    <div class="input-box">
                        <label class="form-label">@lang('Location')</label>
                        <select class="js-example-basic-single form-control" name="listing_location_name">
                            <option value="" selected disabled>@lang('Enter Location')</option>
                            @foreach($all_listing_addresses as $address)
                                <option
                                    value="{{ $address }}" {{  request()->listing_location_name == $address ? 'selected' : '' }}>@lang($address)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="btn-area">
                        <button type="submit" class="cmn-btn w-100">@lang('Filter')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
@push('css-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/fullcalendar.min.css') }}"/>
@endpush
@push('extra-js')
    <script src='https://fullcalendar.io/js/fullcalendar-2.1.1/lib/moment.min.js'></script>
    <script src='https://fullcalendar.io/js/fullcalendar-2.1.1/fullcalendar.min.js'></script>
@endpush

@push('script')
    <script>
        "use strict";
        $(document).ready(function () {
            $('.notiflix-confirm').on('click', function () {
                var route = $(this).data('route');
                $('.deleteRoute').attr('action', route)
            })
        });

        $('#calendar').fullCalendar({
            themeSystem: 'bootstrap5',
            header: {
                left: 'today',
                center: 'prev title next',
                right: 'month,basicWeek,basicDay'
            },
            defaultDate: "{{$handover}}",
            editable: false,
            eventLimit: true,
            events: "{{ route('user.calender') }}",
            eventColor: "#1c2d41",
            height: 500
        });
    </script>
@endpush

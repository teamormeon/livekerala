@extends('user_panel.layouts.user')
@section('title',trans('All Listing'))

@section('content')
    <div class="container-fluid">
        <div class="main row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">@lang('Listings')</h3>
                </div>

                <div class="switcher">
                    <a href="{{ route('user.listings') }}">
                        <button class="@if(lastUriSegment() == 'listings') active @endif">@lang('Listings')</button>
                    </a>
                    <a href="{{ route('user.listings', 'pending') }}">
                        <button class="{{(lastUriSegment() == 'pending') ? 'active' : ''}}">@lang('Pending')</button>
                    </a>
                    <a href="{{ route('user.listings', 'approved') }}">
                        <button class="{{(lastUriSegment() == 'approved') ? 'active' : ''}}"> @lang('Approved')</button>
                    </a>
                    <a href="{{ route('user.listings', 'rejected') }}">
                        <button class="{{(lastUriSegment() == 'rejected') ? 'active' : ''}}">@lang('Rejected')</button>
                    </a>
                </div>


                <!-- table -->
                <div class="table-parent table-responsive listing-table-parent">
                    <div class="table-heading py-3 d-flex justify-content-between align-items-center">
                        <h4>@lang('Latest Listings')</h4>
                        <div class="d-flex align-items-center">
                            <a href="{{ route('user.listing.import.csv') }}" class="cmn-btn customButton me-2">
                                <i class="fal fa-upload" aria-hidden="true"></i> @lang('Import')
                            </a>
                            <button type="button" class="cmn-btn customButton notiflix-confirm me-2" data-bs-toggle="modal" data-bs-target="#addListingmodal">
                                <i class="fal fa-plus" aria-hidden="true"></i> @lang('Add Listing')
                            </button>
                            <button type="button" class="cmn-btn customButton" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">@lang('Filter')<i class="fal fa-filter"></i>
                            </button>
                        </div>
                    </div>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th scope="col">@lang('Package')</th>
                            <th scope="col">@lang('Category')</th>
                            <th scope="col">@lang('Listing')</th>
                            <th scope="col">@lang('Location')</th>
                            <th scope="col">@lang('Stage')</th>
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
                                    <a class="text-info" href="{{ route('listing.details', $item->slug) }}"
                                       target="_blank">
                                        @lang($item->title)
                                    </a>
                                </td>

                                <td data-label="Location">
                                    @lang($item->address)
                                </td>

                                <td data-label="Stage">
                                    @if($item->status == 1)
                                        <span class="badge rounded-pill bg-primary">@lang('Approved')</span>
                                    @elseif($item->status == 2)
                                        <span class="badge rounded-pill bg-secondary">@lang('Rejected')</span>
                                    @else
                                        <span class="badge rounded-pill bg-warning">@lang('Pending')</span>
                                    @endif
                                </td>

                                <td data-label="Status">
                                    @if($item->is_active == 0)
                                        <span class="badge rounded-pill bg-danger">@lang('Deactive')</span>
                                    @else
                                        <span class="badge rounded-pill bg-success">@lang('Active')</span>
                                    @endif
                                </td>


                                @if($item->status == 2)
                                    <td data-label="@lang('Action')">
                                        <div class="dropdown-btns">
                                            <button type="button" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="far fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a href="javascript:void(0)" data-bs-toggle="modal"
                                                       data-bs-target="#delete-modal" class="btn currentColor notiflix-confirm dropdown-item"
                                                       data-route="{{ route('user.deleteListing', $item->id) }}">
                                                        <i class="far fa-trash"></i> @lang('Delete')
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                @else
                                    <td data-label="@lang('Action')">
                                        <div class="dropdown-btns">
                                            <button type="button" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="far fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a href="{{ route('user.analytics', $item->id) }}" class="btn currentColor dropdown-item">
                                                        <i class="fal fa-analytics"></i> @lang('Analytics')
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('user.reviews', $item->id) }}" class="btn currentColor dropdown-item">
                                                        <i class="far fa-star"></i> @lang('Reviews')
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('user.dynamic.form.data', $item->id) }}" class="btn currentColor dropdown-item">
                                                        <i class="far fa-album-collection"></i> @lang('Form Data')
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('user.editListing', $item->id) }}" class="btn currentColor dropdown-item">
                                                        <i class="far fa-edit"></i> @lang('Edit')
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#delete-modal" class="btn currentColor notiflix-confirm dropdown-item"
                                                       data-route="{{ route('user.deleteListing', $item->id) }}">
                                                        <i class="far fa-trash"></i> @lang('Delete')
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <td class="text-center" colspan="100%">
                                <img class="noDataImg" src="{{ asset('assets/admin/img/oc-error.svg') }}" alt="image">
                                <p class="mt-3">@lang('No data available')</p>
                            </td>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $user_listings->appends($_GET)->links('user_panel.partials.pagination') }}
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
                        @lang('Are you sure want to delete this?')
                    </div>
                    <hr>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')</button>
                        <form action="" method="post" class="deleteRoute">
                            @csrf
                            @method('delete')
                            <button type="submit" class="rounded-3 text-capitalize btn-custom addCreateListingRoute">@lang('Confirm')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="addListingmodal" tabindex="-1" aria-labelledby="addListingmodal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header modal-header-custom">
                        <h4 class="modal-title text-white" id="editModalLabel">@lang('Create Listing')</h4>
                        <button type="button" class="close-btn" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fal fa-times" aria-hidden="true"></i>
                        </button>
                    </div>
                    <form>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="package" class="col-form-label">@lang('Package')</label>
                                <select name="package" id="package" class="form-control">
                                    <option selected disabled>@lang('Select Package')</option>
                                    @foreach($my_packages as $key => $package)
                                        @if(($package->no_of_listing > 0 || $package->no_of_listing == null) && ($package->expire_date == null ||  \Carbon\Carbon::now() <= \Carbon\Carbon::parse($package->expire_date)) && ($package->status == 1))
                                            <option value="{{ $package->id }}" data-listing="{{ $package->no_of_listing }}"
                                                    data-route="{{ route('user.addListing', $package->id) }}"
                                                    class="total_listing{{$package->id}}">@lang(optional(optional($package->get_package)->details)->title)
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    @error('category_id') @lang($message) @enderror
                                </div>
                            </div>
                            <div class="mb-3 d-none" id="noOfListing">
                                <label for="message-text" class="col-form-label">@lang('No. of available listing')</label>
                                <input type="text" class="form-control total_no_of_listing_field bg-white" readonly>
                            </div>
                        </div>
                        <hr>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">@lang('Close')</button>
                            <a href="javascript:void(0)"  class="btn customButton addCreateListingRoute">@lang('Create')</a>
                        </div>
                    </form>
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
                        <input type="text" name="name" class="form-control" placeholder="@lang('Search')" value="{{ old('name',request()->name) }}"/>
                    </div>
                    <div class="input-box">
                        <label class="form-label">@lang('Package')</label>
                        <select class="js-example-basic-single form-control" name="package">
                            <option value="" selected disabled>@lang('Select Package')</option>
                            @foreach($packages as $package)
                                <option value="{{ $package->id }}" {{  request()->package == $package->id ? 'selected' : '' }}>@lang(optional($package->details)->title)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="input-box listingCategory filterListing">
                        <label class="form-label">@lang('Category')</label>
                        <select class="listing__category__select2 form-control filterListing" name="category[]" multiple>
                            <option value="all"
                                    @if(request()->category && in_array('all', request()->category))
                                        selected
                                @endif>@lang('All Category')</option>
                            @foreach($listingCategories as $category)
                                <option value="{{ $category->id }}"
                                        @if(request()->category && in_array($category->id, request()->category))
                                            selected
                                    @endif>@lang(optional($category->details)->name)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="input-box">
                        <label class="form-label">@lang('Location')</label>
                        <select class="js-example-basic-single form-control" name="location" style="background: #f1f2f6">
                            <option value="" selected disabled>@lang('Enter Location')</option>
                            @foreach($allAddresses as $address)
                                <option value="{{ $address->id }}" {{  request()->location == $address->id ? 'selected' : '' }}>@lang($address->name)</option>
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

@push('script')

    <script>
        'use strict'
        $(document).ready(function () {
            $(".listing__category__select2").select2({
                width: '100%',
                placeholder: '@lang("Select Categories")',
            });

            $('.notiflix-confirm').on('click', function () {
                var route = $(this).data('route');
                $('.deleteRoute').attr('action', route)
            })

            $('#package').on('change', function () {
                $('#noOfListing').removeClass('d-none');
                let package_id = $(this).val();
                let no_of_listing = $('.total_listing' + package_id).data('listing');
                if (no_of_listing) {
                    $('.total_no_of_listing_field').val(no_of_listing);
                } else {
                    $('.total_no_of_listing_field').val('Unlimited');
                }

                let route = $('.total_listing' + package_id).data('route');
                $('.addCreateListingRoute').attr('href', route)
            });
        });
    </script>
@endpush

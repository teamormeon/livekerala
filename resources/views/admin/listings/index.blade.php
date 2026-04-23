@extends('admin.layouts.app')
@section('page_title',__('User Listings'))
@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">@lang('User Listings')</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-no-gutter">
                            <li class="breadcrumb-item">
                                <a class="breadcrumb-link" href="{{ route('admin.dashboard') }}">
                                    @lang('Dashboard')
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">@lang('Listings')</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-sm-6 col-lg-2 mb-3 mb-lg-5">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2">@lang("Total Listing")</h6>
                        <div class="row align-items-center gx-2">
                            <div class="col">
                                <span class="js-counter display-4 text-dark">{{ $totalListing ?? 0 }}</span>
                                <span class="text-body fs-5 ms-1">@lang("From") {{ $totalListing ?? 0 }}</span>
                            </div>
                            <div class="col-auto">
                              <span class="badge bg-soft-info text-info p-1">
                                <i class="bi-graph-up"></i> {{ fractionNumber($totalListing) }}%
                              </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-2 mb-3 mb-lg-5">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2">@lang("Total Active Listing")</h6>
                        <div class="row align-items-center gx-2">
                            <div class="col">
                                <span class="js-counter display-4 text-dark">{{ $totalActiveListing }}</span>
                                <span class="text-body fs-5 ms-1">@lang("From") {{ $totalListing ?? 0 }}</span>
                            </div>
                            <div class="col-auto">
                                @if($growthPercentageActive > 0)
                                    <span class="badge bg-soft-success text-success p-1">
                                        <i class="bi-graph-up"></i> {{ number_format($growthPercentageActive , 2) }}%
                                    </span>
                                @else
                                    <span class="badge bg-soft-danger text-danger p-1">
                                        <i class="bi-graph-down"></i> {{ number_format($growthPercentageActive , 2) }}%
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-2 mb-3 mb-lg-5">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2">@lang("Total Inactive Listing")</h6>
                        <div class="row align-items-center gx-2">
                            <div class="col">
                                <span class="js-counter display-4 text-dark">{{ $totalInactiveListing }}</span>
                                <span class="text-body fs-5 ms-1">@lang("From") {{ $totalListing ?? 0 }}</span>
                            </div>
                            <div class="col-auto">
                                @if($growthPercentageInactive > 0)
                                    <span class="badge bg-soft-success text-success p-1">
                                        <i class="bi-graph-up"></i> {{ number_format($growthPercentageInactive , 2) }}%
                                    </span>
                                @else
                                    <span class="badge bg-soft-danger text-danger p-1">
                                        <i class="bi-graph-down"></i> {{ number_format($growthPercentageInactive , 2) }}%
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-2 mb-3 mb-lg-5">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2">@lang("Total Pending Listing")</h6>
                        <div class="row align-items-center gx-2">
                            <div class="col">
                                <span class="js-counter display-4 text-dark">{{ $totalPending ?? 0 }}</span>
                                <span class="text-body fs-5 ms-1">@lang("From") {{ $totalListing ?? 0 }}</span>
                            </div>
                            <div class="col-auto">
                                @if($growthPercentagePending > 0)
                                    <span class="badge bg-soft-success text-success p-1">
                                        <i class="bi-graph-up"></i> {{ number_format($growthPercentagePending , 2) }}%
                                    </span>
                                @else
                                    <span class="badge bg-soft-danger text-danger p-1">
                                        <i class="bi-graph-down"></i> {{ number_format($growthPercentagePending , 2) }}%
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-2 mb-3 mb-lg-5">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2">@lang("Total Approved Listing")</h6>
                        <div class="row align-items-center gx-2">
                            <div class="col">
                                <span class="js-counter display-4 text-dark">{{ $totalApproved ?? 0 }}</span>
                                <span class="text-body fs-5 ms-1">@lang("From") {{ $totalListing ?? 0 }}</span>
                            </div>
                            <div class="col-auto">
                                @if($growthPercentageApproved > 0)
                                    <span class="badge bg-soft-success text-success p-1">
                                        <i class="bi-graph-up"></i> {{ number_format($growthPercentageApproved , 2) }}%
                                    </span>
                                @else
                                    <span class="badge bg-soft-danger text-danger p-1">
                                        <i class="bi-graph-down"></i> {{ number_format($growthPercentageApproved , 2) }}%
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-2 mb-3 mb-lg-5">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2">@lang("Total Rejected Listing")</h6>
                        <div class="row align-items-center gx-2">
                            <div class="col">
                                <span class="js-counter display-4 text-dark">{{ $totalRejected ?? 0 }}</span>
                                <span class="text-body fs-5 ms-1">@lang("From") {{ $totalListing ?? 0 }}</span>
                            </div>
                            <div class="col-auto">
                                @if($growthPercentageRejected > 0)
                                    <span class="badge bg-soft-success text-success p-1">
                                        <i class="bi-graph-up"></i> {{ number_format($growthPercentageRejected , 2) }}%
                                    </span>
                                @else
                                    <span class="badge bg-soft-danger text-danger p-1">
                                        <i class="bi-graph-down"></i> {{ number_format($growthPercentageRejected , 2) }}%
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="card">
                <div class="card-header card-header-content-md-between">
                    <div class="mb-2 mb-md-0">
                        <div class="input-group input-group-merge input-group-flush">
                            <div class="input-group-prepend input-group-text">
                                <i class="bi-search"></i>
                            </div>
                            <input id="datatableSearch" type="search" class="form-control" placeholder="Search"
                                   aria-label="Search Listing" autocomplete="off">
                        </div>
                    </div>

                    <div class="d-grid d-sm-flex justify-content-md-end align-items-sm-center gap-2">
                        @if(adminAccessRoute(config('role.manage_listing.access.delete')))
                            <div id="datatableCounterInfo">
                                <div class="d-flex align-items-center">
                                <span class="fs-5 me-3">
                                  <span id="datatableCounter">0</span>
                                  @lang('Selected')
                                </span>
                                    <a class="btn btn-outline-danger btn-sm" href="javascript:void(0)"
                                       data-bs-toggle="modal"
                                       data-bs-target="#soldPlanDeleteMultipleModal">
                                        <i class="bi-trash"></i> @lang('Delete')
                                    </a>
                                </div>
                            </div>
                        @endif

                        @if(adminAccessRoute(config('role.manage_listing.access.edit')))
                            <div class="btn-group" role="group">
                                <a href="javascript:void(0)" class="btn btn-white btn-sm">
                                    @lang('Action')
                                </a>
                                <div class="btn-group">
                                    <button type="button"
                                            class="btn btn-white btn-icon btn-sm dropdown-toggle dropdown-toggle-empty"
                                            id="userEditDropdown" data-bs-toggle="dropdown"
                                            aria-expanded="false"></button>
                                    <div class="dropdown-menu dropdown-menu-end mt-1"
                                         aria-labelledby="userEditDropdown">
                                        <button class="dropdown-item text-success" type="button" data-bs-toggle="modal"
                                                data-bs-target="#multiApprovedModal"><i
                                                class="fas fa-check pr-2"></i> @lang('Approved')</button>
                                        <button class="dropdown-item text-danger" type="button" data-bs-toggle="modal"
                                                data-bs-target="#multiRejectedModal"><i
                                                class="fas fa-times pr-2"></i> @lang('Rejected')</button>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="dropdown">
                            <button type="button" class="btn btn-white btn-sm w-100"
                                    id="dropdownMenuClickable" data-bs-auto-close="false"
                                    id="usersFilterDropdown"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                <i class="bi-filter me-1"></i> @lang('Filter')
                            </button>

                            <div
                                class="dropdown-menu dropdown-menu-sm-end dropdown-card card-dropdown-filter-centered filter_dropdown"
                                aria-labelledby="dropdownMenuClickable">
                                <div class="card">
                                    <div class="card-header card-header-content-between">
                                        <h5 class="card-header-title">@lang('Filter Listing')</h5>
                                        <button type="button" class="btn btn-ghost-secondary btn-icon btn-sm ms-2"
                                                id="filter_close_btn">
                                            <i class="bi-x-lg"></i>
                                        </button>
                                    </div>

                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-12 mb-4">
                                                <small class="text-cap text-body">@lang("Listing Stage")</small>
                                                <div class="tom-select-custom">
                                                    <select
                                                        class="js-select js-datatable-filter form-select form-select-sm"
                                                        id="select_status"
                                                        data-target-column-index="4" data-hs-tom-select-options='{
                                                              "placeholder": "Any status",
                                                              "searchInDropdown": false,
                                                              "hideSearch": true,
                                                              "dropdownWidth": "10rem"
                                                            }'>
                                                        <option value="all"
                                                                data-option-template='<span class="d-flex align-items-center"><span class="legend-indicator bg-secondary"></span>@lang("All Status")</span>'>
                                                            @lang("All Status")
                                                        </option>
                                                        <option value="0"
                                                                data-option-template='<span class="d-flex align-items-center"><span class="legend-indicator bg-warning"></span>Pending</span>'>
                                                            @lang("Pending")
                                                        </option>
                                                        <option value="1"
                                                                data-option-template='<span class="d-flex align-items-center"><span class="legend-indicator bg-success"></span>Approved</span>'>
                                                            @lang("Approved")
                                                        </option>
                                                        <option value="2"
                                                                data-option-template='<span class="d-flex align-items-center"><span class="legend-indicator bg-danger"></span>Rejected</span>'>
                                                            @lang("Rejected")
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-sm-12 mb-4">
                                                <small class="text-cap text-body">@lang("Status")</small>
                                                <div class="tom-select-custom">
                                                    <select
                                                        class="js-select js-datatable-filter form-select form-select-sm"
                                                        id="active_status"
                                                        data-target-column-index="4" data-hs-tom-select-options='{
                                                              "placeholder": "Any status",
                                                              "searchInDropdown": false,
                                                              "hideSearch": true,
                                                              "dropdownWidth": "10rem"
                                                            }'>
                                                        <option value="all"
                                                                data-option-template='<span class="d-flex align-items-center"><span class="legend-indicator bg-secondary"></span>@lang("All Status")</span>'>
                                                            @lang("All Status")
                                                        </option>
                                                        <option value="1"
                                                                data-option-template='<span class="d-flex align-items-center"><span class="legend-indicator bg-success"></span>Activated</span>'>
                                                            @lang("Activated")
                                                        </option>
                                                        <option value="0"
                                                                data-option-template='<span class="d-flex align-items-center"><span class="legend-indicator bg-danger"></span>Deactivated</span>'>
                                                            @lang("Deactivated")
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12 mb-4">
                                                <span class="text-cap text-body">@lang('Date Range')</span>
                                                <div class="input-group mb-3 custom">
                                                    <input type="text" id="filter_date_range"
                                                           class="js-flatpickr form-control"
                                                           placeholder="Select dates"
                                                           data-hs-flatpickr-options='{
                                                                 "dateFormat": "d/m/Y",
                                                                 "mode": "range"
                                                               }' aria-describedby="flatpickr_filter_date_range">
                                                    <span class="input-group-text" id="flatpickr_filter_date_range">
                                                        <i class="bi bi-arrow-counterclockwise"></i>
                                                    </span>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="d-grid">
                                            <button type="button" id="filter_button"
                                                    class="btn btn-primary">@lang('Apply')</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class=" table-responsive datatable-custom">
                    <table id="datatable"
                           class="js-datatable table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                           data-hs-datatables-options='{
                           "columnDefs": [{
                              "targets": [0, 7],
                              "orderable": false
                            }],
                           "order": [],
                           "info": {
                             "totalQty": "#datatableWithPaginationInfoTotalQty"
                           },
                           "search": "#datatableSearch",
                           "entries": "#datatableEntries",
                           "pageLength": 15,
                           "isResponsive": false,
                           "isShowPaging": false,
                           "pagination": "datatablePagination"
                         }'>
                        <thead class="thead-light">
                        <tr>
                            <th class="table-column-pe-0">
                                <div class="form-check">
                                    <input class="form-check-input check-all tic-check" type="checkbox" name="check-all"
                                           id="datatableCheckAll">
                                    <label class="form-check-label" for="datatableCheckAll"></label>
                                </div>
                            </th>
                            <th>@lang('User')</th>
                            <th>@lang('Package')</th>
                            <th>@lang('Category')</th>
                            <th>@lang('Listing Title')</th>
                            <th>@lang('Stage')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Created Date')</th>
                            <th>@lang('Action')</th>
                        </tr>
                        </thead>

                        <tbody>

                        </tbody>
                    </table>
                </div>


                <div class="card-footer">
                    <div class="row justify-content-center justify-content-sm-between align-items-sm-center">
                        <div class="col-sm mb-2 mb-sm-0">
                            <div class="d-flex justify-content-center justify-content-sm-start align-items-center">
                                <span class="me-2">@lang('Showing:')</span>
                                <div class="tom-select-custom">
                                    <select id="datatableEntries"
                                            class="js-select form-select form-select-borderless w-auto"
                                            autocomplete="off"
                                            data-hs-tom-select-options='{
                                                "searchInDropdown": false,
                                                "hideSearch": true
                                              }'>
                                        <option value="10">10</option>
                                        <option value="15" selected>15</option>
                                        <option value="20">20</option>
                                    </select>
                                </div>
                                <span class="text-secondary me-2">of</span>
                                <span id="datatableWithPaginationInfoTotalQty"></span>
                            </div>
                        </div>
                        <div class="col-sm-auto">
                            <div class="d-flex  justify-content-center justify-content-sm-end">
                                <nav id="datatablePagination" aria-label="Activity pagination"></nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="soldPlanDeleteMultipleModal" tabindex="-1" role="dialog"
         aria-labelledby="soldPlanDeleteMultipleModalLabel" data-bs-backdrop="static"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="soldPlanDeleteMultipleLabel"><i
                            class="fa-light fa-square-check"></i> @lang('Confirmation')</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post">
                    @csrf
                    <div class="modal-body">
                        @lang('Do you want to delete all selected Listing data?')
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn-primary delete-multiple">@lang('Confirm')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{--delete listing modal--}}
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
         data-bs-backdrop="static"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="deleteModalLabel"><i
                            class="bi bi-check2-square"></i> @lang("Confirmation")</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>@lang("Do you want to delete this Listing")</p>
                </div>
                <form id="deleteForm" action="" method="post" class="setRoute">
                    @csrf
                    @method("DELETE")
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn-primary">@lang('Confirm')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{--multi aproved modal--}}
    <div class="modal fade" id="multiApprovedModal" tabindex="-1" role="dialog"
         aria-labelledby="multiApprovedModalLabel"
         data-bs-backdrop="static"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="multiApprovedModalLabel"><i
                            class="bi bi-check2-square"></i> @lang("Approved Listing Confirmation")</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>@lang('Are you really want to approved the Listing?')</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-bs-dismiss="modal">@lang('No')</button>
                    <a href="javascript:void(0)" class="btn btn-primary multiApprovedYesBtn">@lang('Yes')</a>
                </div>
            </div>
        </div>
    </div>

    {{--multi rejected modal--}}
    <div class="modal fade" id="multiRejectedModal" tabindex="-1" role="dialog"
         aria-labelledby="multiRejectedModalLabel"
         data-bs-backdrop="static"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="multiRejectedModalLabel"><i
                            class="bi bi-check2-square"></i> @lang("Rejected Listing Confirmation")</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>@lang("Are you really want to rejected the Listing?")</p>
                    <div class="form-group">
                        <label for="">@lang('Write you reason')</label> <span class="text-danger">*</span>
                        <textarea name="reject_reason" id="multi_reject_reason" rows="4" class="form-control"
                                  placeholder="@lang('type here...')"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-bs-dismiss="modal">@lang('No')</button>
                    <a href="javascript:void(0)" class="btn btn-primary multiRejectedBtn">@lang('Yes')</a>
                </div>
            </div>
        </div>
    </div>

    {{--single aproved modal--}}
    <div class="modal fade" id="singleApprovedModal" tabindex="-1" role="dialog"
         aria-labelledby="singleApprovedModalLabel"
         data-bs-backdrop="static"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="singleApprovedModalLabel"><i
                            class="bi bi-check2-square"></i> @lang("Approved Listing Confirmation")</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>@lang('Are you really want to approved the Listing?')</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-bs-dismiss="modal">@lang('No')</button>
                    <a href="javascript:void(0)" class="btn btn-primary singleApprovedRoute">@lang('Yes')</a>
                </div>
            </div>
        </div>
    </div>

    {{--single rejected modal--}}
    <div class="modal fade" id="singleRejectedModal" tabindex="-1" role="dialog"
         aria-labelledby="singleRejectedModalLabel"
         data-bs-backdrop="static"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="singleRejectedModalLabel"><i
                            class="bi bi-check2-square"></i> @lang("Rejected Listing Confirmation")</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>@lang("Are you really want to rejected the Listing?")</p>
                    <div class="form-group">
                        <label for="">@lang('Write you reason')</label> <span class="text-danger">*</span>
                        <textarea name="reject_reason" id="single_reject_reason" rows="4" class="form-control"
                                  placeholder="@lang('type here...')"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-bs-dismiss="modal">@lang('No')</button>
                    <a href="javascript:void(0)" class="btn btn-primary singleRejectedRoute">@lang('Yes')</a>
                </div>
            </div>
        </div>
    </div>

    {{--single rejected info modal--}}
    <div class="modal fade" id="singleRejectedInfoModal" tabindex="-1" role="dialog"
         aria-labelledby="singleRejectedInfoModalLabel"
         data-bs-backdrop="static"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"
                        id="singleRejectedInfoModalLabel">@lang("Rejected Listing Information")</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group">
                        <li class="list-group-item listingOwner"></li>
                        <li class="list-group-item listingTitle"></li>
                        <li class="list-group-item rejectedReason"></li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-bs-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>


    {{--active modal--}}
    <div class="modal fade" id="activeModal" tabindex="-1" role="dialog" aria-labelledby="activeModalLabel"
         data-bs-backdrop="static"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="activeModalLabel"><i
                            class="bi bi-check2-square"></i> @lang("Listing Active Confirmation")</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="showListingTitle">@lang('Are you really want to active the Listing?')</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-bs-dismiss="modal">@lang('No')</button>
                    <a href="javascript:void(0)" class="btn btn-primary activeRoute">@lang('Yes')</a>
                </div>
            </div>
        </div>
    </div>

    {{--deactive modal--}}
    <div class="modal fade" id="deactiveModal" tabindex="-1" role="dialog" aria-labelledby="deactiveModalLabel"
         data-bs-backdrop="static"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="deactiveModalLabel"><i
                            class="bi bi-check2-square"></i> @lang("Listing Deactive Confirmation")</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="showListingTitle">@lang('Are you really want to deactive the Listing?')</p>
                    <div class="form-group">
                        <label for="">@lang('Write you reason')</label> <span class="text-danger">*</span>
                        <textarea name="deactive_reason" id="deactive_reason" rows="4" class="form-control"
                                  placeholder="@lang('type here...')"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-bs-dismiss="modal">@lang('No')</button>
                    <a href="javascript:void(0)" class="btn btn-primary deactiveRoute">@lang('Yes')</a>
                </div>
            </div>
        </div>
    </div>

    {{--deactive info modal--}}
    <div class="modal fade" id="deactiveInfoModal" tabindex="-1" role="dialog"
         aria-labelledby="deactiveInfoModalLabel"
         data-bs-backdrop="static"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="deactiveInfoModalLabel"> @lang("Listing Deactive Information")</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group">
                        <li class="list-group-item listingOwner"></li>
                        <li class="list-group-item listingTitle"></li>
                        <li class="list-group-item deactiveReason"></li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-bs-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>

@endsection


@push('css-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/tom-select.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/flatpickr.min.css') }}">
@endpush


@push('js-lib')
    <script src="{{ asset('assets/admin/js/hs-file-attach.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/tom-select.complete.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/select.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/appear.min.js') }}"></script>
    <script src="{{ asset("assets/admin/js/hs-counter.min.js") }}"></script>
    <script src="{{ asset('assets/admin/js/flatpickr.min.js') }}"></script>
@endpush


@push('script')
    <script>
        $(document).on('ready', function () {
            $('#deleteModal').on('show.bs.modal', function (event) {
                let button = $(event.relatedTarget);
                let route = button.data('route');
                let form = $(this).find('form#deleteForm');
                form.attr('action', route);
            });

            $('#addListingModal').on('show.bs.modal', function (event) {
                let button = $(event.relatedTarget);
                let route = button.data('route');
                let form = $(this).find('form#addListingForm');
                form.attr('action', route);
            });

            new HSCounter('.js-counter')
            new HSFileAttach('.js-file-attach')
            HSCore.components.HSFlatpickr.init('.js-flatpickr')
            HSCore.components.HSTomSelect.init('.js-select', {
                maxOptions: 250,
            })

            HSCore.components.HSDatatables.init($('#datatable'), {
                processing: true,
                serverSide: true,

                ajax: {
                    url: "{{ route("admin.listing.search") }}",

                },
                columns: [
                    {data: 'checkbox', name: 'checkbox'},
                    {data: 'user', name: 'user'},
                    {data: 'package', name: 'package'},
                    {data: 'category', name: 'category'},
                    {data: 'listing-title', name: 'listing-title'},
                    {data: 'stage', name: 'stage'},
                    {data: 'status', name: 'status'},
                    {data: 'created-date', name: 'created-date'},
                    {data: 'action', name: 'action'},
                ],
                select: {
                    style: 'multi',
                    selector: 'td:first-child input[type="checkbox"]',
                    classMap: {
                        checkAll: '#datatableCheckAll',
                        counter: '#datatableCounter',
                        counterInfo: '#datatableCounterInfo'
                    }
                },
                language: {
                    zeroRecords: `<div class="text-center p-4">
                    <img class="dataTables-image mb-3" src="{{ asset('assets/admin/img/oc-error.svg') }}" alt="Image Description" data-hs-theme-appearance="default">
                    <img class="dataTables-image mb-3" src="{{ asset('assets/admin/img/oc-error-light.svg') }}" alt="Image Description" data-hs-theme-appearance="dark">
                    <p class="mb-0">No data to show</p>
                    </div>`,
                    processing: `<div><div></div><div></div><div></div><div></div></div>`
                },
            })


            $(document).on('click', '#filter_button', function () {
                let filterSelectedStatus = $('#select_status').val();
                let filterSelectedActiveStatus = $('#active_status').val();
                let filterDate = $('#filter_date_range').val();
                const datatable = HSCore.components.HSDatatables.getItem(0);
                datatable.ajax.url("{{ route('admin.listing.search') }}" + "?filterStatus=" + filterSelectedStatus + "&filterActiveStatus=" + filterSelectedActiveStatus + "&filterDate=" + filterDate).load();
            });

            $.fn.dataTable.ext.errMode = 'throw';


            $(document).on('click', '#datatableCheckAll', function () {
                $('input:checkbox').not(this).prop('checked', this.checked);
            });

            $(document).on('change', ".row-tic", function () {
                let length = $(".row-tic").length;
                let checkedLength = $(".row-tic:checked").length;
                if (length == checkedLength) {
                    $('#check-all').prop('checked', true);
                } else {
                    $('#check-all').prop('checked', false);
                }
            });

            $(document).on('click', '.delete-multiple', function (e) {
                e.preventDefault();
                let all_value = [];
                $(".row-tic:checked").each(function () {
                    all_value.push($(this).attr('data-id'));
                });
                let strIds = all_value;
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('admin.listing.delete.multiple') }}",
                    data: {strIds: strIds},
                    dataType: 'json',
                    type: "post",
                    success: function (data) {
                        location.reload();
                    },
                });
            });


            //singleApproved
            $('#singleApprovedModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var route = button.data('route');
                var listingId = button.data('listingid');
                var modal = $(this);
                modal.find('.singleApprovedRoute').attr('data-route', route);
                modal.find('.singleApprovedRoute').attr('data-listingid', listingId);
            });
            $('.singleApprovedRoute').click(function () {
                var route = $(this).data('route');
                var listingId = $(this).data('listingid');
                $.ajax({
                    url: route,
                    method: 'POST',
                    data: {
                        listingId: listingId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        location.reload();
                    },
                });
                $('#singleApprovedModal').modal('hide');
            });


            //singleRejected
            $('#singleRejectedModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var route = button.data('route');
                var listingId = button.data('listingid');
                var modal = $(this);
                modal.find('.singleRejectedRoute').attr('data-route', route);
                modal.find('.singleRejectedRoute').attr('data-listingid', listingId);
            });
            $('.singleRejectedRoute').click(function () {
                var route = $(this).data('route');
                var listingId = $(this).data('listingid');
                var rejectReason = $('#single_reject_reason').val();
                $.ajax({
                    url: route,
                    method: 'POST',
                    data: {
                        listingId: listingId,
                        rejectReason: rejectReason,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        location.reload();
                    },
                });
                $('#singleRejectedModal').modal('hide');
            });

            //singleRejectedInfo
            $('#singleRejectedInfoModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var owner = button.data('owner');
                var title = button.data('title');
                var rejectedreason = button.data('rejectedreason');
                var modal = $(this);
                modal.find('.listingOwner').text(`@lang('Owner: ') ${owner}`);
                modal.find('.listingTitle').text(`@lang('Listing Title: ') ${title}`);
                modal.find('.rejectedReason').text(`@lang('Rejected Reason: ') ${rejectedreason}`);
            });


            //Active Listing
            $('#activeModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var route = button.data('route');
                var listingId = button.data('listingid');
                var listingTitle = button.data('title');
                var modal = $(this);
                modal.find('.activeRoute').attr('data-route', route);
                modal.find('.activeRoute').attr('data-listingid', listingId);
                modal.find('.activeRoute').attr('data-title', listingTitle);
                modal.find('.showListingTitle').text(`@lang('Are you sure to active ') ${listingTitle} @lang(' Listing?')`);
            });
            $('.activeRoute').click(function () {
                var route = $(this).data('route');
                var listingId = $(this).data('listingid');
                $.ajax({
                    url: route,
                    method: 'POST',
                    data: {
                        listingId: listingId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        location.reload();
                    },
                });
                $('#activeModal').modal('hide');
            });

            //Deactive Listing
            $('#deactiveModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var route = button.data('route');
                var listingId = button.data('listingid');
                var listingTitle = button.data('title');
                var modal = $(this);
                modal.find('.deactiveRoute').attr('data-route', route);
                modal.find('.deactiveRoute').attr('data-listingid', listingId);
                modal.find('.deactiveRoute').attr('data-title', listingTitle);
                modal.find('.showListingTitle').text(`@lang('Are you sure to deactive ') ${listingTitle} @lang(' Listing?')`);
            });

            $('.deactiveRoute').click(function () {
                var route = $(this).data('route');
                var listingId = $(this).data('listingid');
                var deactiveReason = $('#deactive_reason').val();
                $.ajax({
                    url: route,
                    method: 'POST',
                    data: {
                        listingId: listingId,
                        deactiveReason: deactiveReason,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        location.reload();
                    },
                });
                $('#deactiveModal').modal('hide');
            });

            //DeactivatedInfo
            $('#deactiveInfoModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var owner = button.data('owner');
                var title = button.data('title');
                var deactivatedreason = button.data('deactivatedreason');
                var modal = $(this);
                modal.find('.listingOwner').text(`@lang('Owner: ') ${owner}`);
                modal.find('.listingTitle').text(`@lang('Listing Title: ') ${title}`);
                modal.find('.deactiveReason').text(`@lang('Deactivated Reason: ') ${deactivatedreason}`);
            });

        });


        //multiple Approved
        $(document).on('click', '.multiApprovedYesBtn', function (e) {
            e.preventDefault();
            var listingIds = [];
            $(".row-tic:checked").each(function () {
                listingIds.push($(this).attr('data-id'));
            });

            $.ajax({
                url: "{{ route('admin.multi.listing.approved') }}",
                data: {
                    listingIds: listingIds,
                    _token: '{{ csrf_token() }}'
                },
                datatType: 'json',
                type: "POST",
                success: function (data) {
                    location.reload();
                },
            });
        });

        //multiple Rejected
        $(document).on('click', '.multiRejectedBtn', function (e) {
            e.preventDefault();
            var listingIds = [];
            var rejectReason = $('#multi_reject_reason').val();
            $(".row-tic:checked").each(function () {
                listingIds.push($(this).attr('data-id'));
            });

            $.ajax({
                url: "{{ route('admin.multi.listing.rejected') }}",
                data: {
                    listingIds: listingIds,
                    rejectReason: rejectReason,
                    _token: '{{ csrf_token() }}'
                },
                datatType: 'json',
                type: "POST",
                success: function (data) {
                    location.reload();
                },
            });
        });

    </script>

@endpush





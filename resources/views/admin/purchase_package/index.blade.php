@extends('admin.layouts.app')
@section('page_title',__('Purchase Package List'))
@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-sm mb-2 mb-sm-0">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-no-gutter">
                            <li class="breadcrumb-item"><a class="breadcrumb-link"
                                                           href="javascript:void(0);">@lang('Dashboard')</a></li>
                            <li class="breadcrumb-item active" aria-current="page">@lang('Manage Package')</li>
                        </ol>
                    </nav>
                    <h1 class="page-header-title">@lang('Purchase History')</h1>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header card-header-content-md-between">
                <div class="mb-2 mb-md-0">
                        <div class="input-group input-group-merge navbar-input-group">
                            <div class="input-group-prepend input-group-text">
                                <i class="bi-search"></i>
                            </div>
                            <input type="search" id="datatableSearch"
                                   class="search form-control form-control-sm"
                                   placeholder="@lang('Search Purchase Package')"
                                   aria-label="@lang('Search Purchase Package')"
                                   autocomplete="off">
                            <a class="input-group-append input-group-text display-none" href="javascript:void(0)">
                                <i id="clearSearchResultsIcon" class="bi-x"></i>
                            </a>
                        </div>
                </div>
                <div class="d-grid d-sm-flex justify-content-md-end align-items-sm-center gap-2">

                    <div id="datatableCounterInfo">
                        <div class="d-flex align-items-center">
                                <span class="fs-5 me-3">
                                  <span id="datatableCounter">0</span>
                                  @lang('Selected')
                                </span>
                            <form action="{{ route('admin.purchase.package.export.excel') }}" method="POST" enctype="multipart/form-data" class="d-inline">
                                @csrf
                                <input type="hidden" name="package_id" class="export_selected_packages">
                                <button type="submit" class="btn btn-outline-info btn-rounded btn-sm me-2">{{ __('Export Excel') }}</button>
                            </form>

                            <form action="{{ route('admin.purchase.package.export.csv') }}" method="POST" enctype="multipart/form-data" class="d-inline">
                                @csrf
                                <input type="hidden" name="package_id" class="export_selected_packages">
                                <button type="submit" class="btn btn-outline-success btn-rounded btn-sm me-2">{{ __('Export Csv') }}</button>
                            </form>
                            @if(adminAccessRoute(config('role.purchase_package.access.delete')))
                                <a class="btn btn-outline-danger btn-sm" href="javascript:void(0)" data-bs-toggle="modal"
                                   data-bs-target="#deleteMultipleModal">
                                    <i class="bi-trash"></i> @lang('Delete')
                                </a>
                            @endif
                        </div>
                    </div>

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
                                    <h5 class="card-header-title">@lang('Filter')</h5>
                                    <button type="button"
                                            class="btn btn-ghost-secondary btn-icon btn-sm ms-2"
                                            id="filter_close_btn">
                                        <i class="bi-x-lg"></i>
                                    </button>
                                </div>

                                <div class="card-body">
                                    <form id="filter_form">
                                        <div class="row">
                                            <div class="col-sm-12 mb-4">
                                                <small class="text-cap text-body">@lang("User")</small>
                                                <input type="text" class="form-control" id="filter_user" autocomplete="off">
                                            </div>

                                            <div class="col-sm-12 mb-4">
                                                <small class="text-cap text-body">@lang("package")</small>
                                                <div class="tom-select-custom">
                                                    <select
                                                        class="js-select js-datatable-filter form-select form-select-sm"
                                                        id="select_package"
                                                        data-target-column-index="4" data-hs-tom-select-options='{
                                                          "placeholder": "Any Package",
                                                          "searchInDropdown": false,
                                                          "hideSearch": true,
                                                          "dropdownWidth": "10rem"
                                                        }'>
                                                        <option value="all"
                                                                data-option-template='<span class="d-flex align-items-center">@lang("All Package")</span>'>
                                                            @lang("All Package")
                                                        </option>
                                                        @foreach($packages as $package)
                                                            <option value="{{ $package->id }}"
                                                                    data-option-template='<span class="d-flex align-items-center">{{ optional($package->details)->title }}</span>'>
                                                                @lang(optional($package->details)->title)
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-sm-12 mb-4">
                                                <small class="text-cap text-body">@lang("Validity")</small>
                                                <div class="tom-select-custom">
                                                    <select
                                                        class="js-select js-datatable-filter form-select form-select-sm"
                                                        id="select_validity"
                                                        data-target-column-index="4" data-hs-tom-select-options='{
                                                          "placeholder": "Any validity",
                                                          "searchInDropdown": false,
                                                          "hideSearch": true,
                                                          "dropdownWidth": "10rem"
                                                        }'>
                                                        <option value="all"
                                                                data-option-template='<span class="d-flex align-items-center"><span class="legend-indicator bg-secondary"></span>@lang("All Validity")</span>'>
                                                            @lang("All Validity")
                                                        </option>
                                                        <option value="active"
                                                                data-option-template='<span class="d-flex align-items-center"><span class="legend-indicator bg-success"></span>Active</span>'>
                                                            @lang("Active")
                                                        </option>
                                                        <option value="expired"
                                                                data-option-template='<span class="d-flex align-items-center"><span class="legend-indicator bg-danger"></span>Expired</span>'>
                                                            @lang("Expired")
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 mb-4">
                                                <small class="text-cap text-body">@lang("Status")</small>
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
                                                                data-option-template='<span class="d-flex align-items-center"><span class="legend-indicator bg-success"></span>Running</span>'>
                                                            @lang("Running")
                                                        </option>
                                                        <option value="2"
                                                                data-option-template='<span class="d-flex align-items-center"><span class="legend-indicator bg-danger"></span>Canceled</span>'>
                                                            @lang("Canceled")
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
                                        <div class="row gx-2">
                                            <div class="col">
                                                <div class="d-grid">
                                                    <button type="button" class="btn btn-primary" id="filter_button"><i class="bi-search"></i> @lang('Apply')</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class=" table-responsive datatable-custom  ">
                <table id="datatable"
                       class="js-datatable table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                       data-hs-datatables-options='{
                       "columnDefs": [{
                          "targets": [0, 8],
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
                        <th>@lang('Package Name')</th>
                        <th>@lang('Validity')</th>
                        <th>@lang('Subscription Type')</th>
                        <th>@lang('Status')</th>
                        <th>@lang('Purchased Date')</th>
                        <th>@lang('Expired Date')</th>
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
                            <!-- Select -->
                            <div class="tom-select-custom">
                                <select id="datatableEntries"
                                        class="js-select form-select form-select-borderless w-auto" autocomplete="off"
                                        data-hs-tom-select-options='{
                                            "searchInDropdown": false,
                                            "hideSearch": true
                                          }'>
                                    <option value="10">10</option>
                                    <option value="15" selected>15</option>
                                    <option value="20">20</option>
                                </select>
                            </div>
                            <span class="text-secondary me-2">@lang('of')</span>
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

    <div class="modal fade" id="deleteMultipleModal" tabindex="-1" role="dialog" aria-labelledby="deleteMultipleModalLabel" data-bs-backdrop="static"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="deleteMultipleModalLabel"><i
                            class="fa-light fa-square-check"></i> @lang('Confirmation')</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post">
                    @csrf
                    <div class="modal-body">
                        @lang('Do you want to delete all selected data?')
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn-primary delete-multiple">@lang('Confirm')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="delete" data-bs-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalHeader">@lang('Delete Confirmation!')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="deleteModalBody">@lang('Are you sure to delete this?')</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-bs-dismiss="modal">@lang('Close')</button>
                    <form action="" method="post" class="deleteModalRoute">
                        @csrf
                        @method('delete')
                        <button type="submit" class="btn btn-soft-success">@lang('Yes')</button>
                    </form>
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
    <script src="{{ asset('assets/admin/js/tom-select.complete.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/select.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/appear.min.js') }}"></script>
    <script src="{{ asset("assets/admin/js/hs-counter.min.js") }}"></script>
@endpush


@push('script')
    <script>
        $(document).on('ready', function () {
            new HSCounter('.js-counter')
            HSCore.components.HSFlatpickr.init('.js-flatpickr')
            HSCore.components.HSTomSelect.init('.js-select', {
                maxOptions: 250,
            })
            HSCore.components.HSDatatables.init($('#datatable'), {
                processing: true,
                serverSide: true,
                ordering: false,
                ajax: {
                    url: "{{ route("admin.purchase.package.search") }}",
                },
                columns: [
                    {data: 'checkbox', name: 'checkbox'},
                    {data: 'user', name: 'user'},
                    {data: 'package-name', name: 'package-name'},
                    {data: 'validity', name: 'validity'},
                    {data: 'subscription-type', name: 'subscription-type'},
                    {data: 'status', name: 'status'},
                    {data: 'purchased-date', name: 'purchased-date'},
                    {data: 'expired-date', name: 'expired-date'},
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
            });
            document.getElementById("filter_button").addEventListener("click", function () {
                let filter_user = $('#filter_user').val();
                let filterPackageId = $('#select_package').val();
                let filterValidity = $('#select_validity').val();
                let filterStatus = $('#select_status').val();
                let filterDate = $('#filter_date_range').val();
                const datatable = HSCore.components.HSDatatables.getItem(0);
                datatable.ajax.url("{{ route('admin.purchase.package.search') }}" + "?filter_user=" + filter_user + "&filterPackageId=" + filterPackageId
                    + "&filterValidity=" + filterValidity + "&filterStatus=" + filterStatus +
                    "&filterDate=" + filterDate).load();
            });
            $.fn.dataTable.ext.errMode = 'throw';

            var selectedValues = [];
            $(document).on('change', ".row-tic", function () {
                let dataId = $(this).attr('data-id');
                if ($(this).is(':checked')) {
                    selectedValues.push(dataId);
                    $('.export_selected_packages').val(selectedValues);
                } else {
                    selectedValues = selectedValues.filter(value => value !== dataId);
                    $('.export_selected_packages').val(selectedValues);
                }
            });


            $(document).on('click', '#datatableCheckAll', function () {
                let isChecked = $(this).is(':checked');

                $('input.row-tic').prop('checked', isChecked).each(function () {
                    let dataId = $(this).attr('data-id');
                    if (isChecked) {
                        if (!selectedValues.includes(dataId)) {
                            selectedValues.push(dataId);
                            $('.export_selected_packages').val(selectedValues);
                        }
                    } else {
                        selectedValues = [];
                    }
                });
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
                    url: "{{ route('admin.purchase.package.delete.multiple') }}",
                    data: {strIds: strIds},
                    datatType: 'json',
                    type: "post",
                    success: function (data) {
                        location.reload();
                    },
                });
            });

            $(document).on('click', '.delete_btn', function () {
                let route = $(this).data('route');
                $('#deleteModalBody').text('Are you sure you want to cancel this subscription?');
                $('.deleteModalRoute').attr('action', route);
            });

        });

    </script>
@endpush





@extends('admin.layouts.app')
@section('page_title',__('Amenities'))
@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-sm mb-2 mb-sm-0">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-no-gutter">
                            <li class="breadcrumb-item"><a class="breadcrumb-link"
                                                           href="javascript:void(0);">@lang('Dashboard')</a></li>
                            <li class="breadcrumb-item active" aria-current="page">@lang('Manage Amenities')</li>
                        </ol>
                    </nav>
                    <h1 class="page-header-title">@lang('Amenities')</h1>
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
                                   placeholder="@lang('Search Amenities')"
                                   aria-label="@lang('Search Amenities')"
                                   autocomplete="off">
                            <a class="input-group-append input-group-text display-none" href="javascript:void(0)">
                                <i id="clearSearchResultsIcon" class="bi-x"></i>
                            </a>
                        </div>
                </div>
                <div class="d-grid d-sm-flex justify-content-md-end align-items-sm-center gap-2">

                    @if(adminAccessRoute(config('role.amenities.access.delete')))
                        <div id="datatableCounterInfo">
                            <div class="d-flex align-items-center">
                                <span class="fs-5 me-3">
                                  <span id="datatableCounter">0</span>
                                  @lang('Selected')
                                </span>
                                <a class="btn btn-outline-danger btn-sm" href="javascript:void(0)" data-bs-toggle="modal"
                                   data-bs-target="#deleteMultipleModal">
                                    <i class="bi-trash"></i> @lang('Delete')
                                </a>
                            </div>
                        </div>
                    @endif

                    @if(adminAccessRoute(config('role.amenities.access.add')))
                        <a href="{{ route('admin.amenities.create') }}" class="btn btn-sm btn-primary">@lang('Add Amenities')</a>
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
                                                        <option value="1"
                                                                data-option-template='<span class="d-flex align-items-center"><span class="legend-indicator bg-success"></span>Active</span>'>
                                                            @lang("Active")
                                                        </option>
                                                        <option value="0"
                                                                data-option-template='<span class="d-flex align-items-center"><span class="legend-indicator bg-danger"></span>Deactive</span>'>
                                                            @lang("Deactive")
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
                                                    <button type="button" id="clear_filter" class="btn btn-white">@lang('Clear Filters')</button>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="d-grid">
                                                    <button type="button" class="btn btn-primary" id="filter_button">     <i class="bi-search"></i> @lang('Apply')</button>
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
                          "targets": [0, 3],
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
                        <th>@lang('Amenities')</th>
                        <th>@lang('Status')</th>
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


    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" data-bs-backdrop="static"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="deleteModalLabel"><i
                            class="fa-light fa-square-check"></i> @lang('Confirmation')</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post" class="deleteRoute">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        @lang('Do you want to delete `')<span class="deletedTitle fw-bold"></span>@lang('` Amenities?')
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn-primary">@lang('Confirm')</button>
                    </div>
                </form>
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
                    url: "{{ route("admin.amenities.search") }}",
                },
                columns: [
                    {data: 'checkbox', name: 'checkbox'},
                    {data: 'amenities', name: 'amenities'},
                    {data: 'status', name: 'status'},
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
                let filterStatus = $('#select_status').val();
                let filterDate = $('#filter_date_range').val();
                const datatable = HSCore.components.HSDatatables.getItem(0);
                datatable.ajax.url("{{ route('admin.amenities.search') }}"  + "?filterStatus=" + filterStatus +
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
                    url: "{{ route('admin.amenities.delete.multiple') }}",
                    data: {strIds: strIds},
                    datatType: 'json',
                    type: "post",
                    success: function (data) {
                        location.reload();
                    },
                });
            });
        });

        $(document).on('click','.deleteBtn', function () {
            var route = $(this).data('route');
            var name = $(this).data('amenities-name');
            $('.deletedTitle').text(name);
            $('.deleteRoute').attr('action', route)
        })

    </script>
@endpush





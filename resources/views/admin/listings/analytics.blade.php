@extends('admin.layouts.app')
@section('page_title',__('Listing Analytics'))
@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">@lang('Analytics')</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-no-gutter">
                            <li class="breadcrumb-item">
                                <a class="breadcrumb-link" href="{{ route('admin.dashboard') }}">
                                    @lang('Dashboard')
                                </a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">@lang('Listing')</li>
                            <li class="breadcrumb-item active" aria-current="page">@lang('Analytics')</li>
                        </ol>
                    </nav>
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
                                   aria-label="Search" autocomplete="off">
                        </div>
                    </div>

                    <div class="d-grid d-sm-flex justify-content-md-end align-items-sm-center gap-2">
                        @if(adminAccessRoute(config('role.listing_analytics.access.delete')))
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
                                        <h5 class="card-header-title">@lang('Filter Analytics')</h5>
                                        <button type="button" class="btn btn-ghost-secondary btn-icon btn-sm ms-2"
                                                id="filter_close_btn">
                                            <i class="bi-x-lg"></i>
                                        </button>
                                    </div>

                                    <div class="card-body">
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
                                            <button type="button" id="filter_button" class="btn btn-primary">@lang('Apply')</button>
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
                              "targets": [0, 5],
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
                            <th>@lang('Listing')</th>
                            <th>@lang('Country')</th>
                            <th>@lang('Total Visit')</th>
                            <th>@lang('Last Visited At')</th>
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

        <div class="modal fade" id="deleteMultipleModal" tabindex="-1" role="dialog" aria-labelledby="deleteMultipleModalLabel" data-bs-backdrop="static"
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
                            @lang('Do you want to delete all selected Reviews data?')
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-white" data-bs-dismiss="modal">@lang('Close')</button>
                            <button type="submit" class="btn btn-primary delete-multiple">@lang('Confirm')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

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
                        <p>@lang("Do you want to delete this Reviews")</p>
                    </div>
                    <form id="deleteForm" action="" method="post" class="setRoute">
                        @csrf
                        @method("put")
                        <div class="modal-footer">
                            <button type="button" class="btn btn-white" data-bs-dismiss="modal">@lang('Close')</button>
                            <button type="submit" class="btn btn-primary">@lang('Confirm')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <div id="analyticDetailsModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="analyticDetailsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-top-cover bg-dark text-center">
                        <figure class="position-absolute end-0 bottom-0 start-0">
                            <svg preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 1920 100.1">
                                <path fill="#fff" d="M0,0c0,0,934.4,93.4,1920,0v100.1H0L0,0z"/>
                            </svg>
                        </figure>
                        <div class="modal-close">
                            <button type="button" class="btn-close btn-close-light" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    </div>
                    <div class="modal-top-cover-icon">
                        <span class="icon icon-lg icon-light icon-circle icon-centered shadow-sm">
                          <i class="bi-receipt fs-2"></i>
                        </span>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3 align-items-end">
                            <div class=" col-12">
                                <div class="row my-2 border-bottom py-2">
                                    <div class="col-6">@lang('Visitor IP')</div>
                                    <div class="col-6 text-end" id="visitor_ip"></div>
                                </div>
                                <div class="row my-2 border-bottom py-2">
                                    <div class="col-6">@lang('Country')</div>
                                    <div class="col-6 text-end" id="country"></div>
                                </div>
                                <div class="row my-2 border-bottom py-2">
                                    <div class="col-6">@lang('City')</div>
                                    <div class="col-6 text-end" id="city"></div>
                                </div>
                                <div class="row my-2 border-bottom py-2">
                                    <div class="col-6">@lang('Code')</div>
                                    <div class="col-6 text-end" id="code"></div>
                                </div>
                                <div class="row my-2 border-bottom py-2">
                                    <div class="col-6">@lang('Latitude')</div>
                                    <div class="col-6 text-end" id="latitude"></div>
                                </div>
                                <div class="row my-2 border-bottom py-2">
                                    <div class="col-6">@lang('Longitude')</div>
                                    <div class="col-6 text-end" id="longitude"></div>
                                </div>
                                <div class="row my-2 border-bottom py-2">
                                    <div class="col-6">@lang('OS Platform')</div>
                                    <div class="col-6 text-end" id="os_platform">

                                    </div>
                                </div>
                                <div class="row my-2 py-2">
                                    <div class="col-6">@lang('Browser')</div>
                                    <div class="col-6 text-end" id="browser">
                                    </div>
                                </div>
                            </div>
                        </div>
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
            <script src="{{ asset('assets/admin/js/flatpickr.min.js') }}"></script>
            <script src="{{ asset('assets/admin/js/jquery.dataTables.min.js') }}"></script>
            <script src="{{ asset('assets/admin/js/select.min.js') }}"></script>
            <script src="{{ asset('assets/admin/js/appear.min.js') }}"></script>
            <script src="{{ asset("assets/admin/js/hs-counter.min.js") }}"></script>
        @endpush


        @push('script')
            <script>
                $(document).on('ready', function () {
                    var id = {{ $id }};
                    HSCore.components.HSFlatpickr.init('.js-flatpickr')
                    new HSCounter('.js-counter')
                    new HSFileAttach('.js-file-attach')
                    HSCore.components.HSTomSelect.init('.js-select', {
                        maxOptions: 250,
                    })

                    HSCore.components.HSDatatables.init($('#datatable'), {
                        processing: true,
                        serverSide: true,

                        ajax: {
                            url: "{{ route('admin.listing.single.analytics.search') }}" + '/' + id,
                        },
                        columns: [
                            {data: 'checkbox', name: 'checkbox'},
                            {data: 'listing', name: 'listing'},
                            {data: 'country', name: 'country'},
                            {data: 'total-visit', name: 'total-visit'},
                            {data: 'last-visited-at', name: 'last-visited-at'},
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
                        let filterDate = $('#filter_date_range').val();
                        const datatable = HSCore.components.HSDatatables.getItem(0);
                        datatable.ajax.url("{{ route('admin.listing.single.analytics.search') }}" + '/' + id + "?filterDate=" + filterDate).load();
                    });
                    $.fn.dataTable.ext.errMode = 'throw';


                    $('#deleteModal').on('show.bs.modal', function (event) {
                        let button = $(event.relatedTarget);
                        let route = button.data('route');
                        let form = $(this).find('form#deleteForm');
                        form.attr('action', route);
                    });

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
                            url: "{{ route('admin.listing.analytics.delete.multiple') }}",
                            data: {strIds: strIds},
                            dataType: 'json',
                            type: "post",
                            success: function (data) {
                                location.reload();
                            },
                        });
                    });

                });


                function analyticsDetails(id){
                    $.ajax({
                        url: "{{ route('admin.listing.analytics.show') }}",
                        method: 'POST',
                        data: {
                            id: id,
                            _token: '{{ csrf_token() }}',
                        },
                        success: function (response) {
                            $('#visitor_ip').text(response.visitor_ip ?? '-');
                            $('#country').text(response.country ?? '-');
                            $('#city').text(response.city ?? '-');
                            $('#code').text(response.code ?? '-');
                            $('#latitude').text(response.lat ?? '-');
                            $('#longitude').text(response.long ?? '-');
                            $('#os_platform').text(response.os_platform ?? '-');
                            $('#browser').text(response.browser ?? '-');
                            $('#analyticDetailsModal').modal('show');
                        }
                    });
                }

            </script>

    @endpush





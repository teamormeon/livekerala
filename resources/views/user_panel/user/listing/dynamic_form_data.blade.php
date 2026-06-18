@extends('user_panel.layouts.user')
@section('title')
    @lang("Dynamic Form Data")
@endsection
@push('style')
    <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrap-datepicker.css') }}"/>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="main row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0">@lang('Dynamic Form Data')</h3>
                </div>

                <!-- table -->
                <div class="table-parent table-responsive">
                    <div class="table-heading py-3 d-flex justify-content-between align-items-center">
                        <h4>@lang('Data of ') (@lang($listing->title))</h4>
                        <button type="button" class="cmn-btn customButton" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">@lang('Filter')<i class="fal fa-filter"></i>
                        </button>
                    </div>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>@lang('SL')</th>
                                <th>@lang('Form Name')</th>
                                <th>@lang('Date-Time')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dynamicFOrmData as $key => $data)
                                <tr>
                                    <td>{{++$key}}</td>
                                    <td data-label="@lang('form-name')">
                                        @lang($data->form_name ?? 'N/A')
                                    </td>
                                    <td data-label="@lang('Date-Time')">
                                        {{ dateTime($data->created_at) }}
                                    </td>
                                    <td data-label="@lang('Action')">
                                        <div class="dropdown-btns">
                                            <button type="button" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="far fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#details-modal" class="btn currentColor dropdown-item show-details-btn"
                                                       data-id="{{ $data->id }}"
                                                       data-input-form="{{ json_encode($data->input_form) }}">
                                                        <i class="fal fa-info"></i>@lang('Details')
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <td class="text-center" colspan="100%">
                                    <img class="noDataImg" src="{{ asset('assets/admin/img/oc-error.svg') }}" alt="image">
                                    <p class="mt-3">@lang('No data available')</p>
                                </td>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $dynamicFOrmData->appends($_GET)->links('user_panel.partials.pagination') }}
                </div>
            </div>
        </div>
    </div>

    @push('loadModal')
        <!-- Details Modal -->
        <div class="modal fade" id="details-modal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-top modal-md">
                <div class="modal-content">
                    <div class="modal-header modal-primary modal-header-custom">
                        <h4 class="modal-title" id="editModalLabel">@lang('Details Information')</h4>
                        <button type="button" class="close-btn" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fal fa-times"></i>
                        </button>
                    </div>

                    <div class="modal-body">
                    </div>
                    <hr>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">@lang('Close')</button>
                    </div>
                </div>
            </div>
        </div>
    @endpush

    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
        <div class="offcanvas-header">
            <h4 class="offcanvas-title" id="offcanvasExampleLabel">@lang('Filter Data')</h4>
            <button type="button" class="cmn-btn-close" data-bs-dismiss="offcanvas" aria-label="Close">
                <i class="fal fa-arrow-right"></i>
            </button>
        </div>
        <div class="offcanvas-body">
            <form action="" method="get" enctype="multipart/form-data">
                <div class="row g-4">
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

        document.querySelectorAll('.show-details-btn').forEach(button => {
            button.addEventListener('click', function () {
                var inputForm = JSON.parse(this.getAttribute('data-input-form'));
                var modalBody = document.querySelector('#details-modal .modal-body');

                modalBody.innerHTML = '';
                for (var key in inputForm) {
                    if (inputForm.hasOwnProperty(key)) {
                        var item = inputForm[key];

                        var div = document.createElement('div');
                        div.classList.add('d-flex', 'align-items-center', 'p-3', 'border', 'border-light', 'mb-1');
                        if (item.type == 'file' && item.field_value) {
                            div.innerHTML = `
                        <strong class="pe-3">${item.field_name}:</strong>
                        <a href="#" data-driver='${item.field_driver}' data-path='${item.field_value}' class="downloadBtn">Download File</a>
                    `;
                        } else {
                            div.innerHTML = `
                        <strong class="pe-3">${item.field_name}:</strong>
                        <span>${item.field_value}</span>
                    `;
                        }
                        modalBody.appendChild(div);
                    }
                }
            });
        });

        $(document).on('click', '.downloadBtn', function (e) {
            e.preventDefault();

            let driver = $(this).data('driver');
            let path = $(this).data('path');
            let downloadLink = $(this);

            $.ajax({
                url: "{{ route('getFilePath') }}",
                type: "post",
                data: {
                    driver: driver,
                    path: path,
                    _token: `{{ csrf_token() }}`
                },
                success: function (res) {
                    let fileUrl = res.fileUrl;
                    downloadLink.attr('href', fileUrl);

                    const tempLink = document.createElement('a');
                    tempLink.href = fileUrl;
                    tempLink.download = path.split('/').pop();
                    document.body.appendChild(tempLink);
                    tempLink.click();
                    document.body.removeChild(tempLink);
                }
            });
        });
    </script>
@endpush


@extends('user_panel.layouts.user')
@section('title',trans('Import Listing CSV File'))

@section('content')
    <div class="container-fluid">
        <div class="main row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">@lang('Import Listing CSV File')</h3>
                </div>

                <!-- table -->
                <div class="table-parent table-responsive listing-table-parent">
                    <div class="table-heading py-3 d-flex justify-content-between align-items-center">
                        <h4>@lang('Import CSV File')</h4>
                        <div class="d-flex align-items-center">
                            <a href="{{ route('user.listing.import.csv.sample.download') }}" class="cmn-btn customButton me-2">
                                <i class="fal fa-download" aria-hidden="true"></i> @lang('Sample')
                            </a>
                        </div>
                    </div>

                    <form action="{{ route('user.listing.import.csv') }}" method="post" id="csv-upload-form" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="add-listing-form">
                                    <div class="input-box mb-3">
                                        <label class="form-label mt-2">@lang('Package')</label>
                                        <select
                                            class="js-example-basic-single form-control @error('package') is-invalid @enderror"
                                            name="package">
                                            <option value="" selected disabled>@lang('Select Package')</option>
                                            @foreach($packages as $package)
                                                @if(($package->no_of_listing > 0 || $package->no_of_listing == null) && ($package->expire_date == null ||  \Carbon\Carbon::now() <= \Carbon\Carbon::parse($package->expire_date)) && ($package->status == 1))
                                                    <option
                                                        value="{{ $package->id }}" {{ request()->package == $package->id ? 'selected' : '' }}>@lang(optional(optional($package->get_package)->details)->title)</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback d-block packageError">

                                        </div>
                                    </div>
                                    <div class="upload-img thumbnail">
                                        <div class="form">
                                            <div class="img-box">
                                                <input class="@error('file') is-invalid @enderror" type="file"
                                                       name="file" accept=".csv" onchange="showFileName(this)">
                                                <span class="select-file" id="file-name">@lang('Select File')</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback d-block fileError">

                                    </div>
                                    <button type="submit" class="cmn-btn customButton mt-3 w-100">
                                        <i class="fal fa-upload me-1"></i> @lang('Upload')
                                    </button>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="add-listing-form">
                                    <h4 class="p-2  text-center ">@lang('Sample File Fields')</h4>
                                    <div class="sampleFileTable">
                                        <table class="table table-striped">
                                            <thead class="sampleFileThead">
                                            <tr>
                                                <th scope="col">@lang('SL')</th>
                                                <th scope="col">@lang('Field Name')</th>
                                                <th scope="col">@lang('Field Action')</th>
                                            </tr>
                                            </thead>
                                            <tbody class="">
                                            @foreach($fileFields as $key => $value)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>@lang($key)</td>
                                                    <td class="{{ $value == 'Required' ? 'text-danger' : '' }}">@lang($value)</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('script')

    <script>
        'use strict'

        function showFileName(input) {
            var fileName = input.files[0] ? input.files[0].name : '';
            var fileNameDisplay = document.getElementById('file-name');
            if (fileName) {
                fileNameDisplay.textContent = fileName;
            } else {
                fileNameDisplay.textContent = 'Select File';
            }
        }


        $(document).ready(function() {
            $('#csv-upload-form').on('submit', function(e) {
                e.preventDefault();
                Notiflix.Block.circle('#csv-upload-form');
                var submitButton = $(this).find('button[type="submit"]');
                submitButton.prop('disabled', true);
                var formData = new FormData(this);

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Notiflix.Block.remove('#csv-upload-form');
                        submitButton.prop('disabled', false);
                        if(response.errors){
                            $('.packageError').text(response.errors.package[0]);
                            $('.fileError').text(response.errors.file[0]);
                        }
                        if (response.success) {
                            Notiflix.Notify.success(response.message);
                        }
                    }
                });
            });
        });

    </script>
@endpush

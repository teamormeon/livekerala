<div id="tab1" class="card add-listing-form">
    <div class="card-body">
        <h3>@lang('Basic Info')</h3>
        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label">@lang('Brand Category')</label>
                <select class="js-select form-control" name="brand_category_id" id="brand_category_id"
                        data-categories="{{ $purchase_package_infos->number_of_listing }}">
                    <option selected disabled>@lang('Select Brand Category')</option>
                    @foreach ($all_listings_category as $item)
                        <option value="{{ $item->id }}" {{ old('brand_category_id') == $item->id ? 'selected' : '' }}>@lang($item->name)</option>
                    @endforeach
                </select>
                <div class="invalid-feedback">
                    @error('brand_category_id') @lang($message) @enderror
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">@lang('Brand Model')</label>
                <select class="js-select form-control" name="brand_model_id" id="brand_model_id">
                </select>
                <div class="invalid-feedback">
                    @error('brand_model_id') @lang($message) @enderror
                </div>
            </div>
            <div class="col-md-6">
                <label for="Listing-Name" class="form-label">@lang('Listing Title') <span class="highlight">*</span></label>
                <input type="text" class="form-control @error('title') is-invalid @enderror"
                       name="title" value="{{ old('title') }}" placeholder="@lang('Enter Listing Title')">
                <div class="invalid-feedback">
                    @error('title') @lang($message) @enderror
                </div>
            </div>
            <div class="col-md-6">
                <label for="Listing-Name" class="form-label">@lang('Listing Sub Title') <span class="highlight">*</span></label>
                <input type="text" class="form-control @error('sub_title') is-invalid @enderror"
                       name="sub_title" value="{{ old('sub_title') }}" placeholder="@lang('Enter Listing Sub Title')">
                <div class="invalid-feedback">
                    @error('sub_title') @lang($message) @enderror
                </div>
            </div>
            <div class="col-md-4">
                <label for="Listing-Name" class="form-label">@lang('Email') <span class="highlight">*</span></label>
                <input type="text" class="form-control @error('email') is-invalid @enderror"
                       name="email" value="{{ old('email') }}" placeholder="@lang('Enter Email')">
                <div class="invalid-feedback">
                    @error('email') @lang($message) @enderror
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label">@lang('Phone') <span class="highlight">*</span></label>
                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                       name="phone" value="{{ old('phone') }}" placeholder="@lang('Enter Phone')">
                <div class="invalid-feedback">
                    @error('phone') @lang($message) @enderror
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label">@lang('Price') <span class="highlight">*</span></label>
                <input type="text" class="form-control @error('price') is-invalid @enderror"
                       name="price" value="{{ old('price') }}" placeholder="@lang('Enter Price')">
                <div class="invalid-feedback">
                    @error('price') @lang($message) @enderror
                </div>
            </div>
            <div class="col-md-12 bg-white">
                <label class="form-label">@lang('Description') <span class="highlight">*</span></label>
                <textarea class="form-control summernote @error('description') is-invalid @enderror"
                          name="description" id="description" rows="10"
                          placeholder="@lang('description')">{{ old('description') }}</textarea>
                <div class="invalid-feedback">
                    @error('description') @lang($message) @enderror
                </div>
            </div>
        </div>

        <h3 class="mt-4 mb-2">@lang('Location')</h3>
        <div class="row g-4">
            <div class="col-md-6">
                <select class="js-select form-control" name="country_id" id="country_id"
                        data-categories="{{ $purchase_package_infos->no_of_categories_per_listing }}">
                    <option selected disabled> @lang('Select Country')</option>
                    @foreach ($all_places as $item)
                        <option value="{{ $item->id }}"
                                data-name="{{ $item->name }}"
                            {{ old('country_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback">
                    @error('country_id') @lang($message) @enderror
                </div>
            </div>
            <div class="col-md-6">
                <select class="js-select form-control" name="state_id" id="state_id">
                    <option selected disabled> @lang('state')</option>
                </select>
                <div class="invalid-feedback">
                    @error('state_id') @lang($message) @enderror
                </div>
            </div>
            <div class="col-md-6">
                <select class="js-select form-control" name="city_id" id="city_id">
                    <option selected disabled> @lang('City')</option>
                </select>
                <div class="invalid-feedback">
                    @error('city_id') @lang($message) @enderror
                </div>
            </div>

            <div class="col-md-6">
                <input type="text" class="form-control @error('address') is-invalid @enderror"
                       name="address" value="{{ old('address') }}" placeholder="@lang('Enter address')">
                <div class="invalid-feedback">
                    @error('address') @lang($message) @enderror
                </div>
            </div>

            <div class="col-xl-6">
                <h3 class="mb-3">@lang('Business Hours')</h3>
                <div class="form business-hour">
                    <div class="d-sm-flex justify-content-between delete_this @error('working_day.0') is-invalid @enderror">
                        <div class="input-box w-100 my-1 mx-sm-1">
                            <select class="js-example-basic-single form-control" name="working_day[]">
                                <option selected disabled>@lang('Select Day')</option>
                                <option
                                    value="Monday" {{ old('working_day.0') == 'Monday' ? 'selected' : '' }}>@lang('Monday')</option>
                                <option
                                    value="Tuesday" {{ old('working_day.0') == 'Tuesday' ? 'selected' : '' }}>@lang('Tuesday')</option>
                                <option
                                    value="Wednesday" {{ old('working_day.0') == 'Wednesday' ? 'selected' : '' }}>@lang('Wednesday')</option>
                                <option
                                    value="Thursday" {{ old('working_day.0') == 'Thursday' ? 'selected' : '' }}>@lang('Thursday')</option>
                                <option
                                    value="Friday" {{ old('working_day.0') == 'Friday' ? 'selected' : '' }}>@lang('Friday')</option>
                                <option
                                    value="Saturday" {{ old('working_day.0') == 'Saturday' ? 'selected' : '' }}>@lang('Saturday')</option>
                                <option
                                    value="Sunday" {{ old('working_day.0') == 'Sunday' ? 'selected' : '' }}>@lang('Sunday')</option>
                            </select>
                            <div class="invalid-feedback">
                                @error('working_day.0') @lang($message) @enderror
                            </div>
                        </div>

                        <div class="d-flex">
                            <div class="input-box w-100 my-1 me-1">
                                <input type="time" name="start_time[]" value="{{ old('start_time.0') }}"
                                       class="form-control @error('start_time.0') is-invalid @enderror"
                                       placeholder="@lang('Start Hour')"/>
                                <div class="invalid-feedback">
                                    @error('start_time.0') @lang($message) @enderror
                                </div>
                            </div>

                            <div class="input-box w-100 my-1 me-1">
                                <input type="time" name="end_time[]" value="{{ old('end_time.0') }}"
                                       class="form-control @error('end_time.0') is-invalid @enderror"
                                       placeholder="@lang('End Hour')"/>
                                <div class="invalid-feedback">
                                    @error('end_time.0') @lang($message) @enderror
                                </div>
                            </div>

                            <div class="input-box my-1 me-1">
                                <button class="btn btn-success add-new" type="button" id="add_business_hour">
                                    <i class="fal fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="new_business_hour_form">
                        @php
                            $oldWorkingDaysCount = old('working_day') ? count(old('working_day')) : 0;
                        @endphp
                        @if($oldWorkingDaysCount > 1)
                            @for($i = 1; $i < $oldWorkingDaysCount; $i++)
                                <div
                                    class="d-sm-flex justify-content-between delete_this removeBusinessHourInputField @error("working_day.$i") is-invalid @enderror">
                                    <div class="input-box w-100 my-1 mx-sm-1">
                                        <select class="js-example-basic-single form-control"
                                                name="working_day[]">
                                            <option
                                                value="Monday" {{ old("working_day.$i") == 'Monday' ? 'selected' : '' }}>@lang('Monday')</option>
                                            <option
                                                value="Tuesday" {{ old("working_day.$i") == 'Tuesday' ? 'selected' : '' }}>@lang('Tuesday')</option>
                                            <option
                                                value="Wednesday" {{ old("working_day.$i") == 'Wednesday' ? 'selected' : '' }}>@lang('Wednesday')</option>
                                            <option
                                                value="Thursday" {{ old("working_day.$i") == 'Thursday' ? 'selected' : '' }}>@lang('Thursday')</option>
                                            <option
                                                value="Friday" {{ old("working_day.$i") == 'Friday' ? 'selected' : '' }}>@lang('Friday')</option>
                                            <option
                                                value="Saturday" {{ old("working_day.$i") == 'Saturday' ? 'selected' : '' }}>@lang('Saturday')</option>
                                            <option
                                                value="Sunday" {{ old("working_day.$i") == 'Sunday' ? 'selected' : '' }}>@lang('Sunday')</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            @error("working_day.$i") @lang($message) @enderror
                                        </div>
                                    </div>

                                    <div class="d-flex">
                                        <div class="input-box w-100 my-1 me-1">
                                            <input type="time" name="start_time[]"
                                                   value="{{ old("start_time.$i") }}"
                                                   class="form-control @error("start_time.$i") is-invalid @enderror"
                                                   placeholder="@lang('Start Hour')"/>
                                            <div class="invalid-feedback">
                                                @error("start_time.$i") @lang($message) @enderror
                                            </div>
                                        </div>

                                        <div class="input-box w-100 my-1 me-1">
                                            <input type="time" name="end_time[]"
                                                   value="{{ old("end_time.$i") }}"
                                                   class="form-control @error("end_time.$i") is-invalid @enderror"
                                                   placeholder="@lang('End Hour')"/>
                                            <div class="invalid-feedback">
                                                @error("end_time.$i") @lang($message) @enderror
                                            </div>
                                        </div>

                                        <div class="input-box my-1 me-1">
                                            <button
                                                class="btn btn-danger add-new btn-custom-danger remove_business_hour_input_field_block"
                                                type="button">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <h3 class="mb-3">@lang('Websites And Social Links')</h3>
                <div class="form website_social_links">
                    <div class="d-flex justify-content-between">
                        <div class="input-group mt-1">
                            <input type="text" name="social_icon[]"
                                   class="form-control demo__icon__picker iconpicker1 @error('social_icon.0') is-invalid @enderror"
                                   placeholder="Pick a icon" aria-label="Pick a icon"
                                   aria-describedby="basic-addon1" readonly>
                            <div class="invalid-feedback">
                                @error('social_icon.0') @lang($message) @enderror
                            </div>
                        </div>

                        <div class="input-box w-100 my-1 me-1">
                            <input type="url" name="social_url[]" value="{{ old('social_url.0') }}"
                                   class="form-control @error('social_url.0') is-invalid @enderror"
                                   placeholder="@lang('URL')"/>
                            @error('social_url.0')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="my-1 me-1">
                            <button class="btn btn-success add-new" type="button" id="add_social_links">
                                <i class="fal fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="new_social_links_form">
                        @php
                            $oldSocialCounts = old('social_icon') ? count(old('social_icon')) : 0;
                        @endphp
                        @if($oldSocialCounts > 1)
                            @for($i = 1; $i < $oldSocialCounts; $i++)
                                <div
                                    class="d-flex justify-content-between append_new_social_form removeSocialLinksInput">
                                    <div class="input-group mt-1">
                                        <input type="text" name="social_icon[]"
                                               value="{{ old("social_icon.$i") }}"
                                               class="form-control demo__icon__picker iconpicker{{$i}} iconpicker @error("social_icon.$i") is-invalid @enderror"
                                               placeholder="Pick a icon" aria-label="Pick a icon"
                                               aria-describedby="basic-addon1" readonly>
                                        <div class="invalid-feedback">
                                            @error("social_icon.$i") @lang($message) @enderror
                                        </div>
                                    </div>

                                    <div class="input-box w-100 my-1 me-1">
                                        <input type="url" name="social_url[]" value="{{ old("social_url.$i") }}"
                                               class="form-control @error("social_url.$i") is-invalid @enderror"
                                               placeholder="@lang('URL')"/>
                                        @error("social_url.$i")
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="my-1 me-1">
                                        <button
                                            class="btn btn-danger add-new btn-custom-danger remove_social_link_input_field"
                                            type="button">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            @endfor
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <h3 class="mb-3">@lang('Key Information')</h3>
                <div class="form key_info">
                    <div class="d-flex justify-content-between">
                        <div class="input-group mt-1">
                            <input type="text" name="key_info_icon[]"
                                   class="form-control demo__icon__picker keyInfoIconPicker1 @error('key_info_icon.0') is-invalid @enderror"
                                   placeholder="Pick a icon" aria-label="Pick a icon"
                                   aria-describedby="basic-addon1" readonly>
                            <div class="invalid-feedback">
                                @error('key_info_icon.0') @lang($message) @enderror
                            </div>
                        </div>

                        <div class="input-box w-100 my-1 me-1">
                            <input type="text" name="key_info_name[]" value="{{ old('key_info_name.0') }}"
                                   class="form-control @error('key_info_name.0') is-invalid @enderror"
                                   placeholder="@lang('Key Info Name')"/>
                            @error('key_info_name.0')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="input-box w-100 my-1 me-1">
                            <input type="text" name="key_info_value[]" value="{{ old('key_info_value.0') }}"
                                   class="form-control @error('key_info_value.0') is-invalid @enderror"
                                   placeholder="@lang('Key Info Value')"/>
                            @error('key_info_value.0')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="my-1 me-1">
                            <button class="btn btn-success add-new" type="button" id="add_key_info">
                                <i class="fal fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="new_key_info_form">
                        @php
                            $oldKeyInfoCounts = old('key_info_icon') ? count(old('key_info_icon')) : 0;
                        @endphp
                        @if($oldKeyInfoCounts > 1)
                            @for($i = 1; $i < $oldKeyInfoCounts; $i++)
                                <div
                                    class="d-flex justify-content-between append_new_key_info_form removeKeyInfoInput">
                                    <div class="input-group mt-1">
                                        <input type="text" name="key_info_icon[]"
                                               value="{{ old("key_info_icon.$i") }}"
                                               class="form-control demo__icon__picker keyInfoIconPicker{{$i}} iconpicker @error("key_info_icon.$i") is-invalid @enderror"
                                               placeholder="Pick a icon" aria-label="Pick a icon"
                                               aria-describedby="basic-addon1" readonly>
                                        <div class="invalid-feedback">
                                            @error("key_info_icon.$i") @lang($message) @enderror
                                        </div>
                                    </div>

                                    <div class="input-box w-100 my-1 me-1">
                                        <input type="text" name="key_info_name[]" value="{{ old("key_info_name.$i") }}"
                                               class="form-control @error("key_info_name.$i") is-invalid @enderror"
                                               placeholder="@lang('Key Info Name')"/>
                                        @error("key_info_name.$i")
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="input-box w-100 my-1 me-1">
                                        <input type="text" name="key_info_value[]" value="{{ old("key_info_value.$i") }}"
                                               class="form-control @error("key_info_value.$i") is-invalid @enderror"
                                               placeholder="@lang('Key Info Value')"/>
                                        @error("key_info_value.$i")
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="my-1 me-1">
                                        <button
                                            class="btn btn-danger add-new btn-custom-danger remove_key_info_input_field"
                                            type="button">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            @endfor
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@push('script')
    <script>

        $(document).ready(function () {
            //for Business hour
            $("#add_business_hour").on('click', function () {
                var form = `<div class="d-sm-flex justify-content-between removeBusinessHourInputField">
                                <div class="input-box w-100 my-1 mx-sm-1">
                                    <select class="js-example-basic-single form-control" name="working_day[]">
                                        <option value="Monday">@lang('Monday')</option>
                                        <option value="Tuesday">@lang('Tuesday')</option>
                                        <option value="Wednesday">@lang('Wednesday')</option>
                                        <option value="Thursday">@lang('Thursday')</option>
                                        <option value="Friday">@lang('Friday')</option>
                                        <option value="Saturday">@lang('Saturday')</option>
                                        <option value="Sunday">@lang('Sunday')</option>
                                    </select>
                                </div>
                                <div class="d-flex input-box-two">
                                    <div class="input-box w-100 my-1 me-1">
                                        <input type="time" name="start_time[]" class="form-control" placeholder="@lang('Start Hour')" />
                                    </div>
                                    <div class="input-box w-100 my-1 me-1">
                                        <input type="time" name="end_time[]" class="form-control" placeholder="@lang('End Hour')" />
                                    </div>
                                    <div class="input-box my-1 me-1">
                                        <button class="btn btn-danger add-new btn-custom-danger remove_business_hour_input_field_block" type="button">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>`;

                $('.new_business_hour_form').append(form)
            });

            $(document).on('click', '.remove_business_hour_input_field_block', function () {
                $(this).parents('.removeBusinessHourInputField').remove();
            });

            // for key info icon pick
            keyInfoIconPicker('.keyInfoIconPicker1');

            // for social icon pick
            setIconpicker('.iconpicker1');

            let newSocialForm = $('.append_new_social_form').length + 1;
            for (let i = 2; i <= newSocialForm; i++) {
                setIconpicker(`#iconpicker${i}`);
            }

            $("#add_social_links").on('click', function () {
                let newSocialForm = $('.append_new_social_form').length + 2;

                var form = `<div class="d-flex justify-content-between append_new_social_form removeSocialLinksInput">
                                <div class="input-group mt-1">
                                    <input type="text" name="social_icon[]" class="form-control demo__icon__picker iconpicker${newSocialForm}" placeholder="Pick a icon" aria-label="Pick a icon"
                                           aria-describedby="basic-addon1" readonly>
                                </div>

                                <div class="input-box w-100 my-1 me-1">
                                    <input type="url" name="social_url[]" class="form-control" placeholder="@lang('URL')"/>
                                </div>
                                <div class="my-1 me-1">
                                    <button class="btn btn-danger add-new btn-custom-danger remove_social_link_input_field" type="button">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                            </div>`;

                $('.new_social_links_form').append(form)
                setIconpicker(`.iconpicker${newSocialForm}`);
            });

            $(document).on('click', '.remove_social_link_input_field', function () {
                $(this).parents('.removeSocialLinksInput').remove();
            });

            let newKeyInfoForm = $('.append_new_key_info_form').length + 1;
            for (let i = 2; i <= newKeyInfoForm; i++) {
                keyInfoIconPicker(`#keyInfoIconPicker${i}`);
            }

            $("#add_key_info").on('click', function () {
                let newKeyInfoForm = $('.append_new_key_info_form').length + 2;

                var form = `<div class="d-flex justify-content-between append_new_key_info_form removeKeyInfoInput">
                                <div class="input-group mt-1">
                                    <input type="text" name="key_info_icon[]" class="form-control demo__icon__picker keyInfoIconPicker${newKeyInfoForm}" placeholder="Pick a icon" aria-label="Pick a icon"
                                           aria-describedby="basic-addon1" readonly>
                                </div>

                                <div class="input-box w-100 my-1 me-1">
                                    <input type="text" name="key_info_name[]" class="form-control" placeholder="@lang('Key Info Name')"/>
                                </div>

                                <div class="input-box w-100 my-1 me-1">
                                    <input type="text" name="key_info_value[]" class="form-control" placeholder="@lang('Key Info Value')"/>
                                </div>
                                <div class="my-1 me-1">
                                    <button class="btn btn-danger add-new btn-custom-danger remove_key_info_input_field" type="button">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                            </div>`;

                $('.new_key_info_form').append(form)
                keyInfoIconPicker(`.keyInfoIconPicker${newKeyInfoForm}`);
            });

            $(document).on('click', '.remove_key_info_input_field', function () {
                $(this).parents('.removeKeyInfoInput').remove();
            });

        })


        //get state of selected Country
        $(document).on('change', '#country_id', function (){
            let countryId = this.value;

            $("#state_id").html('');
            $.ajax({
                url: "{{route('get.states')}}",
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "country_id": countryId,
                },
                dataType: 'json',
                success: function (result) {
                    $('#state_id').html('<option value="">-- Select State --</option>');
                    $.each(result.states, function (key, value) {
                        $("#state_id").append('<option value="' + value
                            .id + '">' + value.name + '</option>');
                    });
                    $('#city_id').html('<option value="">-- Select City --</option>');
                }
            });
        });

        //get city of selected state
        $(document).on('change', '#state_id', function (){
            let stateId = this.value;
            $("#city_id").html('');
            $.ajax({
                url: "{{route('get.cities')}}",
                type: "POST",
                data: {
                    state_id: stateId,
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function (res) {
                    $('#city_id').html('<option value="">-- Select City --</option>');
                    $.each(res.cities, function (key, value) {
                        $("#city_id").append('<option value="' + value
                            .id + '">' + value.name + '</option>');
                    });
                }
            });
        });

        //get brand model of selected brand category
        $(document).on('change', '#brand_category_id', function (){
            let brandCategoryId = this.value;
            $("#brand_model_id").html('');
            $.ajax({
                url: "{{route('get.brandModel')}}",
                type: "POST",
                data: {
                    brand_category_id: brandCategoryId,
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function (res) {
                    $('#brand_model_id').html('<option value="">-- Select City --</option>');
                    $.each(res.models, function (key, value) {
                        $("#brand_model_id").append('<option value="' + value
                            .id + '">' + value.name + '</option>');
                    });
                }
            });
        });





        function setIconpicker(selector = '.iconpicker1') {
            $(selector).iconpicker({
                title: 'Search Social Icons',
                selected: false,
                defaultValue: false,
                placement: "top",
                collision: "none",
                animation: true,
                hideOnSelect: true,
                showFooter: false,
                searchInFooter: false,
                mustAccept: false,
                icons: [{
                    title: "bi bi-facebook",
                    searchTerms: ["facebook", "text"]
                }, {
                    title: "bi bi-twitter",
                    searchTerms: ["twitter", "text"]
                }, {
                    title: "bi bi-linkedin",
                    searchTerms: ["linkedin", "text"]
                }, {
                    title: "bi bi-youtube",
                    searchTerms: ["youtube", "text"]
                }, {
                    title: "bi bi-instagram",
                    searchTerms: ["instagram", "text"]
                }, {
                    title: "bi bi-whatsapp",
                    searchTerms: ["whatsapp", "text"]
                }, {
                    title: "bi bi-discord",
                    searchTerms: ["discord", "text"]
                }, {
                    title: "bi bi-globe",
                    searchTerms: ["website", "text"]
                }, {
                    title: "bi bi-google",
                    searchTerms: ["google", "text"]
                }, {
                    title: "bi bi-camera-video",
                    searchTerms: ["vimeo", "text"]
                }, {
                    title: "bi bi-skype",
                    searchTerms: ["skype", "text"]
                }, {
                    title: "bi bi-camera-video-fill",
                    searchTerms: ["tiktalk", "text"]
                }, {
                    title: "bi bi-badge-tm-fill",
                    searchTerms: ["tumbler", "text"]
                }, {
                    title: "bi bi-blockquote-left",
                    searchTerms: ["blogger", "text"]
                }, {
                    title: "bi bi-file-word-fill",
                    searchTerms: ["wordpress", "text"]
                }, {
                    title: "bi bi-badge-wc",
                    searchTerms: ["weixin", "text"]
                }, {
                    title: "bi bi-telegram",
                    searchTerms: ["telegram", "text"]
                }, {
                    title: "bi bi-bell-fill",
                    searchTerms: ["snapchat", "text"]
                }, {
                    title: "bi bi-three-dots",
                    searchTerms: ["flickr", "text"]
                }, {
                    title: "bi bi-file-ppt",
                    searchTerms: ["pinterest", "text"]
                }],
                selectedCustomClass: "bg-primary",
                fullClassFormatter: function (e) {
                    return e;
                },
                input: "input,.iconpicker-input",
                inputSearch: false,
                container: false,
                component: ".input-group-addon,.iconpicker-component",
            })
        }

        function keyInfoIconPicker(selector = '.keyInfoIconPicker1') {
            $(selector).iconpicker({
                title: 'Search Key Info Icons',
                selected: false,
                defaultValue: false,
                placement: "top",
                collision: "none",
                animation: true,
                hideOnSelect: true,
                showFooter: false,
                searchInFooter: false,
                mustAccept: false,
                icons: [{
                    title: "bi bi-car-front",
                    searchTerms: ["car", "text"]
                }, {
                    title: "bi bi-speedometer",
                    searchTerms: ["tachometer", "text"]
                },{
                    title: "bi bi-fuel-pump",
                    searchTerms: ["fuel", "text"]
                },{
                    title: "bi bi-gear",
                    searchTerms: ["gear", "text"]
                },{
                    title: "bi bi-wrench",
                    searchTerms: ["wrench", "text"]
                },{
                    title: "bi bi-patch-check",
                    searchTerms: ["check", "text"]
                },{
                    title: "bi bi-door-open",
                    searchTerms: ["door", "text"]
                },{
                    title: "bi bi-bucket",
                    searchTerms: ["fill", "text"]
                },{
                    title: "bi bi-patch-check",
                    searchTerms: ["check", "text"]
                }],
                selectedCustomClass: "bg-primary",
                fullClassFormatter: function (e) {
                    return e;
                },
                input: "input,.iconpicker-input",
                inputSearch: false,
                container: false,
                component: ".input-group-addon,.iconpicker-component",
            })
        }

    </script>
@endpush

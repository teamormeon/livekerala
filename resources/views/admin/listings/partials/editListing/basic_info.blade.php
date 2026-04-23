<div id="tab1" class="card add-listing-form">
    <div class="card-body">
        <h3 class="mb-3">@lang('Basic Info')</h3>
        <div class="row g-4">
            <div class="col-md-6">
                <label for="Listing-Name" class="form-label">@lang('Listing Title') <span
                        class="highlight">*</span></label>
                <input type="text" class="form-control @error('title') is-invalid @enderror"
                       name="title" value="{!! old('title', $single_listing_infos->title) !!}"
                       placeholder="@lang('Enter Listing Title')">
                <div class="invalid-feedback">
                    @error('title') @lang($message) @enderror
                </div>
            </div>

            <div class="col-md-6">
                @php
                    $categoryIds = $single_listing_infos->getCategories();
                @endphp
                <label class="form-label">@lang('Listing Category')</label>
                <select class="form-control listingCategory" name="category_id[]" multiple
                        data-categories="{{ $single_package_infos->no_of_categories_per_listing }}">
                    @foreach ($all_listings_category as $item)
                        <option value="{{ $item->id }}"
                                @foreach($categoryIds as $category) @if($category->id == $item->id) selected @endif @endforeach>
                            @lang(optional($item->details)->name)
                        </option>
                    @endforeach
                </select>
                <div class="invalid-feedback">
                    @error('category_id') @lang($message) @enderror
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label">@lang('Email') <span class="highlight">*</span></label>
                <input type="text" class="form-control @error('email') is-invalid @enderror"
                       name="email" value="{{ old('email', $single_listing_infos->email) }}"
                       placeholder="@lang('Enter Email')">
                <div class="invalid-feedback">
                    @error('email') @lang($message) @enderror
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">@lang('Phone') <span class="highlight">*</span></label>
                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                       name="phone" value="{{ old('phone', $single_listing_infos->phone) }}"
                       placeholder="@lang('Enter Phone')">
                <div class="invalid-feedback">
                    @error('phone') @lang($message) @enderror
                </div>
            </div>
            <div class="col-md-12 bg-white">
                <label class="form-label">@lang('Description') <span class="highlight">*</span></label>
                <textarea class="form-control @error('description') is-invalid @enderror"
                          name="description" id="description" rows="10"
                          placeholder="@lang('description')">{{ old('description', $single_listing_infos->description) }}</textarea>
                <div class="invalid-feedback">
                    @error('description') @lang($message) @enderror
                </div>
            </div>
        </div>


        <div class="row g-4">
            <div class="col-xl-12">
                <h3 class="mt-4 mb-3">@lang('Location')</h3>
                <div class="map-box">
                    <div class="row g-4">
                        <div class="col-xl-6">
                            <div class="form">
                                <div class="row g-3 location-form">
                                    <div class="input-box col-md-6">
                                        <div class="">
                                            <select
                                                class="js-select place_id form-select @error('country_id') is-invalid @enderror"
                                                autocomplete="off" name="country_id" id="country_id">
                                                <option selected disabled>@lang('Select Country')</option>
                                                @foreach($all_places as $item)
                                                    <option value="{{ $item->id }}" data-name="{{ $item->name }}"
                                                            data-code="{{ $item->iso2 }}"
                                                        {{ old('country_id', $item->id) == $single_listing_infos->country_id ? 'selected' : '' }}>
                                                        @lang($item->name)
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('country_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="input-box col-md-6">
                                        <div class="">
                                            <select
                                                class="js-select place_id form-select @error('state_id') is-invalid @enderror"
                                                autocomplete="off" name="state_id" id="state_id">
                                                <option selected disabled>@lang('Select State')</option>
                                            </select>
                                            @error('state_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="input-box col-md-6">
                                        <div class="">
                                            <select
                                                class="js-select place_id form-select @error('city_id') is-invalid @enderror"
                                                autocomplete="off" name="city_id" id="city_id">
                                                <option selected disabled>@lang('Select City')</option>
                                            </select>
                                            @error('city_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="input-box col-md-6">
                                        <input id="address-search"
                                               class="form-control @error('address') is-invalid @enderror"
                                               name="address"
                                               value="{{ old('address', $single_listing_infos->address) }}"
                                               placeholder="@lang('address')" type="text"
                                               autocomplete="off" data-lat="33.93911" data-long="67.709953"
                                               data-code="AF"/>
                                        <div class="invalid-feedback">
                                            @error('address') @lang($message) @enderror
                                        </div>
                                    </div>
                                    <div class="input-box col-md-6">
                                        <input class="form-control @error('lat') is-invalid @enderror"
                                               id="lat" placeholder="@lang('lat')" name="lat"
                                               value="{{ old('lat', $single_listing_infos->lat) }}"/>
                                        <div class="invalid-feedback">
                                            @error('lat') @lang($message) @enderror
                                        </div>
                                    </div>
                                    <div class="input-box col-md-6">
                                        <input class="form-control @error('long') is-invalid @enderror"
                                               placeholder="@lang('long')" id="lng" name="long"
                                               value="{{ old('long', $single_listing_infos->long) }}"
                                               type="text"/>
                                        <div class="invalid-feedback">
                                            @error('long') @lang($message) @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            @if(basicControl()->is_google_map == 1)
                                <div id="map"></div>
                                <div id="infowindow-content">
                                    <img id="place-image" src="#" alt="" style="display: none;">
                                    <p id="place-name" class="title"></p>
                                    <span id="place-address"></span>
                                </div>
                            @else
                                <div id="map">
                                    <p>
                                        @lang('You can also set location moving marker')
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <h3 class="mb-3">@lang('Business Hours')</h3>
                <div class="form business-hour">
                    @if($single_listing_infos->get_business_hour->isEmpty())
                        <div
                            class="d-sm-flex justify-content-between delete_this @error('working_day.0') is-invalid @enderror">
                            <div class="input-box w-100 my-1 mx-sm-1">
                                <div class="tom-select-custom">
                                    <select class="js-tom-select form-select" name="working_day[]">
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
                                    <button class="btn btn-soft-primary add-new" type="button" id="add_business_hour">
                                        <i class="fal fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @else
                        @for($i = 0; $i < count($single_listing_infos->get_business_hour); $i++)
                            <div
                                class="d-sm-flex justify-content-between delete_this removeBusinessHourInputField @error("working_day.$i") is-invalid @enderror">
                                <div class="input-box w-100 my-1 mx-sm-1">
                                    <div class="tom-select-custom">
                                        <select class="js-tom-select form-select" name="working_day[]">
                                            <option
                                                value="Monday" {{ old("working_day.$i", $business_hours[$i]->working_day ?? '') == 'Monday' ? 'selected' : '' }}>@lang('Monday')</option>
                                            <option
                                                value="Tuesday" {{ old("working_day.$i", $business_hours[$i]->working_day ?? '') == 'Tuesday' ? 'selected' : '' }}>@lang('Tuesday')</option>
                                            <option
                                                value="Wednesday" {{ old("working_day.$i", $business_hours[$i]->working_day ?? '') == 'Wednesday' ? 'selected' : '' }}>@lang('Wednesday')</option>
                                            <option
                                                value="Thursday" {{ old("working_day.$i", $business_hours[$i]->working_day ?? '') == 'Thursday' ? 'selected' : '' }}>@lang('Thursday')</option>
                                            <option
                                                value="Friday" {{ old("working_day.$i", $business_hours[$i]->working_day ?? '') == 'Friday' ? 'selected' : '' }}>@lang('Friday')</option>
                                            <option
                                                value="Saturday" {{ old("working_day.$i", $business_hours[$i]->working_day ?? '') == 'Saturday' ? 'selected' : '' }}>@lang('Saturday')</option>
                                            <option
                                                value="Sunday" {{ old("working_day.$i", $business_hours[$i]->working_day ?? '') == 'Sunday' ? 'selected' : '' }}>@lang('Sunday')</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            @error("working_day.$i") @lang($message) @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex">
                                    <div class="input-box w-100 my-1 me-1">
                                        <input type="time" name="start_time[]"
                                               value="{{ old("start_time.$i", $business_hours[$i]->start_time) }}"
                                               class="form-control @error("start_time.$i") is-invalid @enderror"
                                               placeholder="@lang('Start Hour')"/>
                                        <div class="invalid-feedback">
                                            @error("start_time.$i") @lang($message) @enderror
                                        </div>
                                    </div>

                                    <div class="input-box w-100 my-1 me-1">
                                        <input type="time" name="end_time[]"
                                               value="{{ old("end_time.$i", $business_hours[$i]->end_time) }}"
                                               class="form-control @error("end_time.$i") is-invalid @enderror"
                                               placeholder="@lang('End Hour')"/>
                                        <div class="invalid-feedback">
                                            @error("end_time.$i") @lang($message) @enderror
                                        </div>
                                    </div>

                                    <div class="input-box my-1 me-1">
                                        @if($i == 0)
                                            <button class="btn btn-soft-primary add-new" type="button"
                                                    id="add_business_hour">
                                                <i class="fal fa-plus"></i>
                                            </button>
                                        @else
                                            <button class="btn btn-white remove_business_hour_old_input_field_block"
                                                    type="button">
                                                <i class="bi-trash"></i>
                                            </button>
                                        @endif
                                    </div>

                                </div>
                            </div>
                        @endfor

                    @endif
                    <div class="new_business_hour_form">

                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <h3 class="mb-3">@lang('Websites And Social Links')</h3>
                <div class="form website_social_links">
                    @if($single_listing_infos->get_social_info->isEmpty())
                        <div class="d-flex justify-content-between">
                            <div class="input-group my-1 me-1">
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
                                <button class="btn btn-soft-primary add-new" type="button" id="add_social_links">
                                    <i class="fal fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    @else
                        @for($i = 0; $i < count($single_listing_infos->get_social_info); $i++)
                            <div class="d-flex justify-content-between removeSocialLinksInput">
                                <div class="input-group my-1 me-1">
                                    <input type="text" name="social_icon[]"
                                           value="{{ old("social_icon.$i", $social_links[$i]->social_icon) }}"
                                           class="form-control demo__icon__picker iconpicker1 @error("social_icon.$i") is-invalid @enderror"
                                           placeholder="Pick a icon" aria-label="Pick a icon"
                                           aria-describedby="basic-addon1" readonly>
                                    <div class="invalid-feedback">
                                        @error("social_icon.$i") @lang($message) @enderror
                                    </div>
                                </div>

                                <div class="input-box w-100 my-1 me-1">
                                    <input type="url" name="social_url[]"
                                           value="{{ old("social_url.$i", $social_links[$i]->social_url) }}"
                                           class="form-control @error("social_url.$i") is-invalid @enderror"
                                           placeholder="@lang('URL')"/>
                                    @error("social_url.$i")
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="my-1 me-1">
                                    @if($i == 0)
                                        <button class="btn btn-soft-primary add-new" type="button" id="add_social_links">
                                            <i class="fal fa-plus"></i>
                                        </button>
                                    @else
                                        <button class="btn btn-white remove_social_link_old_input_field" type="button">
                                            <i class="bi-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endfor
                    @endif

                    <div class="new_social_links_form append_new_social_form">

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@push('script')
    <script>

        HSCore.components.HSTomSelect.init('.js-tom-select')
        $(document).ready(function () {
            //for Business hour
            $("#add_business_hour").on('click', function () {
                var form = `<div class="d-sm-flex justify-content-between removeBusinessHourInputField">
                                <div class="input-box w-100 my-1 mx-sm-1">
                                    <div class="tom-select-custom">
                                        <select class="js-tom-select form-select" name="working_day[]">
                                            <option value="Monday">@lang('Monday')</option>
                                            <option value="Tuesday">@lang('Tuesday')</option>
                                            <option value="Wednesday">@lang('Wednesday')</option>
                                            <option value="Thursday">@lang('Thursday')</option>
                                            <option value="Friday">@lang('Friday')</option>
                                            <option value="Saturday">@lang('Saturday')</option>
                                            <option value="Sunday">@lang('Sunday')</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="d-flex input-box-two">
                                    <div class="input-box w-100 my-1 me-1">
                                        <input type="time" name="start_time[]" class="form-control" placeholder="@lang('Start Hour')" />
                                    </div>
                                    <div class="input-box w-100 my-1 me-1">
                                        <input type="time" name="end_time[]" class="form-control" placeholder="@lang('End Hour')" />
                                    </div>
                                    <div class="input-box my-1 me-1">
                                        <button class="btn btn-white add-new btn-custom-danger remove_business_hour_input_field_block" type="button">
                                            <i class="bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>`;

                $('.new_business_hour_form').append(form)
            });

            $(document).on('click', '.remove_business_hour_input_field_block', function () {
                $(this).closest('.removeBusinessHourInputField').remove();
            });

            $(document).on('click', '.remove_business_hour_old_input_field_block', function () {
                $(this).closest('.removeBusinessHourInputField').remove();
            });


            //for social icon
            setIconpicker('.iconpicker1');

            let newSocialForm = $('.append_new_social_form').length + 1;
            for (let i = 2; i <= newSocialForm; i++) {
                setIconpicker(`#iconpicker${i}`);
            }

            $("#add_social_links").on('click', function () {
                let newSocialForm = $('.append_new_social_form').length + 2;

                var form = `<div class="d-flex justify-content-between append_new_social_form removeSocialLinksInput">
                                <div class="input-group my-1 me-1">
                                    <input type="text" name="social_icon[]" class="form-control demo__icon__picker iconpicker${newSocialForm}" placeholder="Pick a icon" aria-label="Pick a icon"
                                           aria-describedby="basic-addon1" readonly>
                                </div>

                                <div class="input-box w-100 my-1 me-1">
                                    <input type="url" name="social_url[]" class="form-control" placeholder="@lang('URL')"/>
                                </div>
                                <div class="my-1 me-1">
                                    <button class="btn btn-white add-new btn-custom-danger remove_social_link_input_field" type="button">
                                        <i class="bi-trash"></i>
                                    </button>
                                </div>
                            </div>`;

                $('.new_social_links_form').append(form)
                setIconpicker(`.iconpicker${newSocialForm}`);
            });

            $(document).on('click', '.remove_social_link_input_field', function () {
                $(this).closest('.removeSocialLinksInput').remove();
            });
            $(document).on('click', '.remove_social_link_old_input_field', function () {
                $(this).closest('.removeSocialLinksInput').remove();
            });

            let maxSelectCategories = $('.listingCategory').data('categories');
            $(".listingCategory").select2({
                width: '100%',
                placeholder: '@lang("Select Categories")',
                maximumSelectionLength: maxSelectCategories,
            });
        })

        $(document).on('change', '#city_id', function () {
            let value = $("#city_id").find('option:selected').data("name");
            let lat = $("#city_id").find('option:selected').data("lat");
            let long = $("#city_id").find('option:selected').data("long");
            $('#lat').val(lat);
            $('#lng').val(long);
            $('#address-search').attr('data-lat', lat);
            $('#address-search').attr('data-long', long);
            $('#address-search').val(value);
            @if(basicControl()->is_google_map == 1)
            initMap();
            @else
            $('#address-search').val($("#city_id").find('option:selected').data("name"));
            @endif
        });


        let selectedCountryId = {{ $single_listing_infos->country_id ?? 'null' }};
        let selectedStateId = {{ $single_listing_infos->state_id ?? 'null' }};
        let selectedCityId = {{ $single_listing_infos->city_id ?? 'null' }};
        if (selectedCountryId) {
            fetchStates(selectedCountryId, selectedStateId);
            fetchCities(selectedStateId, selectedCityId);
        }


        //get state of selected Country
        $(document).on('change', '#country_id', function () {
            let countryId = this.value;
            let countryCode = $("#country_id").find('option:selected').data("code");
            $('#address-search').attr('data-code', countryCode);
            @if(basicControl()->is_google_map == 1)
            initMap();
            @endif
            console.log('hello');
            fetchStates(countryId);
        });

        //get city of selected state
        $(document).on('change', '#state_id', function () {
            let stateId = this.value;
            fetchCities(stateId);
        });

        // Function to fetch states
        function fetchStates(countryId, selectedStateId = null) {
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
                    $('#state_id').html('<option value="">Select State</option>');
                    $.each(result.states, function (key, value) {
                        const selected = selectedStateId == value.id ? 'selected' : '';
                        $("#state_id").append('<option value="' + value.id + '" ' + selected + '>' + value.name + '</option>');
                    });
                }
            });
        }

        // Function to fetch cities
        function fetchCities(stateId, selectedCityId = null) {
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
                    $('#city_id').html('<option value="">Select City</option>');
                    $.each(res.cities, function (key, value) {
                        $('#address-search').attr('data-lat', value.latitude);
                        $('#address-search').attr('data-long', value.longitude);
                        $('#address-search').attr('data-code', value.country_code);
                        @if(basicControl()->is_google_map == 1)
                        initMap();
                        @endif

                        const selected = selectedCityId == value.id ? 'selected' : '';
                        $("#city_id").append('<option value="' + value.id + '" ' +
                            'data-name="' + value.name + '" ' +
                            'data-lat="' + value.latitude + '" ' +
                            'data-long="' + value.longitude + '" ' +
                            'data-code="' + value.country_code + '" ' + selected + '>' +
                            value.name + '</option>');
                    });
                }
            });
        }


        //function for social icon pick
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
    </script>
@endpush

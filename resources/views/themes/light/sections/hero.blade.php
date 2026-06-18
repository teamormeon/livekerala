
@if(isset($hero))
    <section class="home-section"
             style="background-image: url({{ getFile($hero['single']['media']->image->driver, $hero['single']['media']->image->path) }})">
        <div class="overlay h-100">
            <div class="container h-100">
                <div class="row h-100 align-items-center">
                    <div class="col-lg-12">
                        <div class="text-box text-center">
                            <h1>@lang($hero['single']['title'])</h1>
                            <h5 class="text-white">
                                @lang($hero['single']['sub_title'])
                            </h5>
                            <div class="search-bar mx-0">
                                <form action="{{ route('listings') }}" method="get">
                                    <div class="row g-0">
                                        <div class="input-box col-lg-3 col-md-6">
                                            <div class="input-group">
                                                 <span class="input-group-prepend">
                                                    <i class="fal fa-search"></i>
                                                 </span>
                                                <input type="text" name="name"
                                                       {{ old('name', request()->name) }}class="form-control"
                                                       placeholder="@lang('What are you looking for')?"/>
                                            </div>
                                        </div>

                                        <div class="input-box col-lg-3 col-md-6">
                                            <div class="input-group">
                                                 <span class="input-group-prepend">
                                                  <i class="far fa-chart-scatter"></i>
                                                 </span>
                                                <select class="listing__category__select2 form-control"
                                                        name="category[]">
                                                    <option value="all"
                                                            @if(request()->category && in_array('all', request()->category))
                                                                selected
                                                        @endif>@lang('All Category')</option>
                                                    @foreach($hero['all_categories'] as $category)
                                                        @if($category!=null)
                                                            <option value="{{ $category->id }}"
                                                                    @if(request()->category && in_array($category->id, request()->category))
                                                                        selected
                                                                @endif> @lang(optional($category->details)->name)
                                                                @endif
                                                            </option>
                                                            @endforeach
                                                </select>


                                            </div>
                                        </div>

                                        <div class="input-box col-lg-2 col-md-6">
                                            <div class="input-group">
                                                 <span class="input-group-prepend">
                                                    <i class="fal fa-map-marker-alt"></i>
                                                 </span>
                                                <select class="js-example-basic-single form-control" name="location" id="district-select" autocomplete="off">
                                                    <option value="all">@lang('All Districts')</option>
                                                    @foreach($hero['all_places'] as $place)
                                                        @if($place!=null)
                                                            <option value="{{ $place->id }}"
                                                                    @if(request()->location == $place->id) selected @endif>@lang($place->name)</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="input-box col-lg-2 col-md-6">
                                            <div class="input-group">
                                                 <span class="input-group-prepend">
                                                    <i class="fal fa-map-marker-alt"></i>
                                                 </span>
                                                <select class="js-example-basic-single form-control" name="city" id="city-select" autocomplete="off">
                                                    <option value="all">@lang('All Cities')</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="input-box col-lg-2 col-md-6">
                                            <button class="btn-custom w-100 h-100">
                                                <i class="fal fa-search"></i> @lang('search')
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif

<script src="{{asset('assets/global/js/jquery.min.js') }}"></script>
<script>
    'use strict'
    $(document).ready(function () {
        $(".listing__category__select2").select2({
            width: '100%',
            placeholder: '@lang("Select Category")',
        });

        // Load cities for pre-selected district on page load
        var selectedDistrict = $('#district-select').val();
        var selectedCity = '{{ request()->city }}';
        if (selectedDistrict && selectedDistrict !== 'all') {
            loadCities(selectedDistrict, selectedCity);
        }

        // On district change, load cities
        $('#district-select').on('change', function () {
            var stateId = $(this).val();
            $('#city-select').html('<option value="all">@lang("All Cities")</option>');
            if (stateId && stateId !== 'all') {
                loadCities(stateId, null);
            }
        });

        function loadCities(stateId, selectedCity) {
            $.ajax({
                url: '/get-cities/' + stateId,
                type: 'GET',
                success: function (cities) {
                    var options = '<option value="all">@lang("All Cities")</option>';
                    $.each(cities, function (i, city) {
                        var selected = (selectedCity && selectedCity == city.id) ? 'selected' : '';
                        options += '<option value="' + city.id + '" ' + selected + '>' + city.name + '</option>';
                    });
                    $('#city-select').html(options);
                }
            });
        }
    })
</script>

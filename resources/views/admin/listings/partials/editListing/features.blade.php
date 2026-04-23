<div id="tab11" class="card add-listing-form">
    <div class="card-body">
        <div class="row g-4">
            <div class="col-xl-6">
                <h3 class="mb-3">@lang('Features')</h3>
                <div class="form mx-3">
                    <div class="row g-3">
                        @foreach ($featureCategories as $category)
                            <div class="input-box col-md-12">
                                <label class="me-3" for="features_{{ $category->id }}">{{ $category->name }}</label>
                                <select
                                    id="features_{{ $category->id }}"
                                    class="features_select2 form-control @error('features.' . $category->id) is-invalid @enderror"
                                    name="features[{{ $category->id }}][]" multiple>
                                    @foreach ($category->features as $feature)
                                        <option  value="{{ $feature->id }}" {{ in_array($feature->id, $listing_features) ? 'selected' : '' }}>{{ $feature->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    @error('features.' . $category->id . '.0') {{ $message }} @enderror
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@push('script')
    <script>
        $(document).ready(function () {
            $(".features_select2").select2({
                width: '100%',
                placeholder: '@lang("Select Feature")',
            });
        })
    </script>
@endpush

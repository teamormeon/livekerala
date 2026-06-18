<div id="tab4" class="card add-listing-form">
    <div class="card-body">
        <div class="row g-4">
            <div class="col-xl-6">
                <h3 class="mb-3">@lang('Amenities')</h3>
                <div class="form">
                    <div class="row g-3">
                        <div class="input-box col-md-12 amenitySelect">
                            <select
                                class="amenities_select2 form-control @error('amenity_id') is-invalid @enderror"
                                name="amenity_id[]" multiple
                                data-amenities="{{ $single_package_infos->no_of_amenities_per_listing }}">
                                @foreach ($all_amenities as $item)
                                    <option
                                        value="{{ $item->id }}"
                                        {{ in_array($item->id, $listing_amenities->pluck('amenity_id')->toArray()) ? 'selected' : '' }}>{{ $item->details->title }}</option>
                                    @endforeach
                            </select>
                        </div>
                        <div class="invalid-feedback">
                            @error('amenity_id') {{ $message }} @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@push('script')
    <script>
        $(document).ready(function () {
            let maxSelectAmenities = $('.amenities_select2').data('amenities');
            $(".amenities_select2").select2({
                width: '100%',
                placeholder: '@lang("Select amenities")',
                maximumSelectionLength: maxSelectAmenities,
            });
        })
    </script>
@endpush

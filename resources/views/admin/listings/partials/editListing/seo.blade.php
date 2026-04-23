<div id="tab6" class="card add-listing-form">
    <div class="card-body">
        <div class="row g-4">
            <div class="col-xl-12">
                <h3 class="mb-3">@lang('SEO & META Keywords')</h3>
                <div class="form">
                    <div class="basic-form p-4">
                        <div class="row g-3">
                            <div class="input-box col-md-4">
                                <input
                                    class="form-control @error('meta_title') is-invalid @enderror"
                                    type="text" name="meta_title"
                                    value="{{ old('meta_title', @$listing_seo->meta_title) }}" placeholder="@lang('Title')"/>
                                <div class="invalid-feedback">
                                    @error('meta_title') @lang($message) @enderror
                                </div>
                            </div>
                            <div class="input-box col-md-4">
                                <input
                                    class="form-control @error('meta_keywords') is-invalid @enderror"
                                    type="text" name="meta_keywords"
                                    value="{{ old('meta_keywords', @$listing_seo->meta_keywords) }}" placeholder="@lang('Meta Keywords')"/>
                                <div class="invalid-feedback">
                                    @error('meta_keywords') @lang($message) @enderror
                                </div>
                            </div>

                            <div class="input-box col-md-4">
                                <input
                                    class="form-control @error('meta_robots') is-invalid @enderror"
                                    type="text" name="meta_robots"
                                    value="{{ old('meta_robots', @$listing_seo->meta_robots) }}" placeholder="@lang('Meta Robots')"/>
                                <div class="invalid-feedback">
                                    @error('meta_robots') @lang($message) @enderror
                                </div>
                            </div>

                            <div class="input-box col-md-6">
                                <textarea class="form-control" cols="30" rows="3" name="meta_description"
                                          value="{{ old('meta_description') }}"
                                          placeholder="@lang('Description')">{{ old('meta_description', @$listing_seo->meta_description) }}</textarea>
                                <div class="invalid-feedback">
                                    @error('meta_description') @lang($message) @enderror
                                </div>
                            </div>

                            <div class="input-box col-md-6">
                                <textarea class="form-control" cols="30" rows="3" name="og_description"
                                          value="{{ old('og_description') }}"
                                          placeholder="@lang('OG Description')">{{ old('og_description', @$listing_seo->og_description) }}</textarea>
                                <div class="invalid-feedback">
                                    @error('og_description') @lang($message) @enderror
                                </div>
                            </div>

                            <div class="col-md-6 custom-margin">
                                <div class="thumbnailImage">
                                    <div class="form-group">
                                        <input type="file" id="seoImageUpload" class="form-control-file" name="seo_image">
                                        <span>@lang('Select Image')</span>
                                    </div>
                                    <img id="seo" class="thumbnail" src="{{ getFile(@$listing_seo->driver, @$listing_seo->seo_image) }}" alt="SEO Image">
                                </div>
                            </div>
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
            //for thumbnail image upload
            $('#seoImageUpload').on('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        $('#seo').attr('src', event.target.result);
                        $('#seo').removeClass('d-none');
                    }
                    reader.readAsDataURL(file);
                }
            });
        })
    </script>
@endpush




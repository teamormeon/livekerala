<div id="tab6" class="card add-listing-form">
    <div class="card-body">
        <div class="row g-4">
            <div class="col-xl-12">
                <h3 class="mb-3">@lang('SEO & META Keywords')</h3>
                <div class="form">
                    <div class="basic-form p-4">
                        <div class="row g-3">
                            <div class="input-box col-md-6">
                                <input
                                    class="form-control @error('page_title') is-invalid @enderror"
                                    type="text" name="page_title"
                                    value="{{ old('page_title') }}" placeholder="@lang('Page Title')"/>
                                <div class="invalid-feedback">
                                    @error('page_title') @lang($message) @enderror
                                </div>
                            </div>
                            <div class="input-box col-md-6">
                                <input
                                    class="form-control @error('meta_title') is-invalid @enderror"
                                    type="text" name="meta_title"
                                    value="{{ old('meta_title') }}" placeholder="@lang('Meta Title')"/>
                                <div class="invalid-feedback">
                                    @error('meta_title') @lang($message) @enderror
                                </div>
                            </div>
                            <div class="input-box col-md-12">
                                <input
                                    class="form-control @error('meta_keywords') is-invalid @enderror"
                                    type="text" name="meta_keywords"
                                    value="{{ old('meta_keywords') }}" placeholder="@lang('Meta Keywords')"/>
                                <div class="invalid-feedback">
                                    @error('meta_keywords') @lang($message) @enderror
                                </div>
                            </div>
                            <div class="input-box col-md-12">
                                <textarea class="form-control" cols="30" rows="3" name="meta_description"
                                          value="{{ old('meta_description') }}"
                                          placeholder="@lang('Meta Description')">{{ old('meta_description') }}</textarea>
                                <div class="invalid-feedback">
                                    @error('meta_description') @lang($message) @enderror
                                </div>
                            </div>

                            <div class="col-md-6 custom-margin">
                                <div class="thumbnailImage">
                                    <div class="form-group">
                                        <input type="file" id="seoImageUpload" class="form-control-file" name="seo_image">
                                        <span>@lang('Select Image')</span>
                                    </div>
                                    <img id="seo" class="thumbnail d-none" src="" alt="SEO Image">
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




<div id="tab2" class="card add-listing-form">
    <div class="card-body">
        <div class="row g-4">
            <div class="col-xl-6">
                <h3 class="mb-3">
                    @lang('Video') <span class="optional">(@lang('Youtube Video ID'))</span>
                </h3>
                <div class="form">
                    <div class="row g-3">
                        <div class="input-box col-md-12">
                            <input class="form-control @error('youtube_video_id') is-invalid @enderror"
                                   type="text" placeholder="@lang('Enter Video ID')"
                                   value="{{ old('youtube_video_id', $single_listing_infos->youtube_video_id) }}" name="youtube_video_id" id="youtube_video_id"/>
                        </div>
                        <span class="text-danger" id="error-message"></span>
                        <div class="col-12">
                            <div class="youtube nk-plain-video">
                                <iframe src="" id="youtube_iframe" title="YouTube video" allowfullscreen></iframe>
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
            updateYouTubeIframe();
        })

        function updateYouTubeIframe() {
            var videoId = document.getElementById("youtube_video_id").value;
            var iframe = document.getElementById("youtube_iframe");
            var youtubeUrl = "https://www.youtube.com/embed/" + videoId;

            if(videoId) {
                iframe.src = youtubeUrl;
            } else {
                iframe.src = "";
            }
        }
        document.getElementById("youtube_video_id").addEventListener("input", updateYouTubeIframe);
    </script>
@endpush

<div id="tab2" class="card add-listing-form">
    <div class="card-body">
        <div class="row g-4">
            <div class="col-xl-6">
                <h3 class="mb-3">
                    @lang('Video') <span class="optional">(@lang('Youtube Video Url'))</span>
                </h3>
                <div class="form">
                    <div class="row g-3">
                        <div class="input-box col-md-12">
                            <input class="form-control @error('youtube_video_id') is-invalid @enderror"
                                   type="text" placeholder="@lang('Enter URL')"
                                   value="{{ old('youtube_video_id') }}" name="youtube_video_id" id="youtube_video_id"/>
                            @error('youtube_video_id')
                            <span class="text-danger d-block" id="error-message">{{ $message }}</span>
                            @enderror
                        </div>

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
        $(document).ready(function(){
            $('#youtube_video_id').on('keyup', function() {
                let url = $(this).val();
                let videoId = extractVideoId(url);
                if(videoId) {
                    let embedUrl = 'https://www.youtube.com/embed/' + videoId;
                    $('#youtube_iframe').attr('src', embedUrl);
                    $('#error-message').text('');
                } else {
                    $('#error-message').text('Invalid YouTube URL');
                }
            });

            function extractVideoId(url) {
                const regex = /(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/;
                const match = url.match(regex);
                return match ? match[1] : null;
            }
        });

    </script>
@endpush

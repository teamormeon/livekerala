@extends('admin.layouts.app')
@section('page_title', __('Manage Theme'))
@section('content')
    <div class="content container-fluid" id="manageTheme">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-sm">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-no-gutter">
                            <li class="breadcrumb-item"><a class="breadcrumb-link"
                                                           href="javascript:void(0)">@lang('Dashboard')</a></li>
                            <li class="breadcrumb-item active"
                                aria-current="page">@lang("Manage Theme")</li>
                        </ol>
                    </nav>
                    <h1 class="page-header-title">@lang("Manage Theme")</h1>
                </div>
            </div>
        </div>
        <div class="row d-flex justify-content-center">
            <div class="col-12">
                <div class="row d-flex">
                    @foreach($themes as $key => $item)
                        <div class="col-sm-6 col-lg-4 mb-3 mb-lg-5 themeDiv">
                            <input class="themeName" type="hidden" value="{{ $item }}">
                            <div class="card">
                                <div class="card-header bg-dark text-white font-weight-bold p-3">
                                    @lang(ucwords($item))
                                </div>
                                <div class="card-body m-0 p-0">
                                    <img class="w-100" src="{{asset('assets/global/images/themes/'.$item.'.png')}}" alt="{{ $item }} Theme Image" style="height: 400px">
                                </div>
                                <div class="card-footer">
                                    @if(basicControl()->theme == $item)
                                        <button type="button" class="btn waves-effect waves-light btn-rounded rounded-5 btn-success btn-block mt-3 w-100" disabled>
                                            <span><i class="fas fa-check-circle pr-2"></i> @lang('Selected')</span>
                                        </button>
                                    @else
                                        <button type="button" class="btn waves-effect waves-light btn-rounded rounded-5 btn-primary btn-block mt-3 w-100 switchThemeBtn"
                                                data-name="{{ $item }}">
                                            <span><i class="fas fa-save pr-2"></i> @lang('Select As Active')</span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection


@push('script')
    <script>
        $(document).on('click', '.switchThemeBtn', function() {
            Notiflix.Block.standard('#manageTheme');
            var selectedTheme = $(this).data('name');
            var parentDiv = $(this).closest('.themeDiv');
            selectThemeStyle(selectedTheme, parentDiv);
        });

        async function selectThemeStyle(theme, parentDiv) {
            let url = "{{ route('admin.manage.theme.select', ['val' => ':val']) }}";
            url = url.replace(':val', theme);

            let demo = "{{config('demo.IS_DEMO')}}"
            if (demo) {
                Notiflix.Notify.info("This is a demo version, you can't change theme.");
                return 0;
            }


            await axios.get(url)
                .then(function(res) {
                    Notiflix.Block.remove('#manageTheme');
                    if (res.data == 'success'){
                        // location.reload()
                        $('.themeDiv').each(function() {
                            $(this).find('.card-footer').html(`
                            <button type="button" class="btn waves-effect waves-light btn-rounded rounded-5 btn-primary btn-block mt-3 w-100 switchThemeBtn"
                                data-name="${$(this).find('.themeName').val()}">
                                <span><i class="fas fa-save pr-2"></i> @lang('Select As Active')</span>
                            </button>`);
                        });

                        parentDiv.find('.card-footer').html(`
                        <button type="button" class="btn waves-effect waves-light btn-rounded rounded-5 btn-success btn-block mt-3 w-100" disabled>
                            <span><i class="fas fa-check-circle pr-2"></i> @lang('Selected')</span>
                        </button>`);
                        Notiflix.Notify.success(`${theme.charAt(0).toUpperCase() + theme.slice(1)} Theme Activated`);
                    }else {
                        Notiflix.Notify.failure('Error selecting theme');
                    }
                })
                .catch(function(error) {
                    Notiflix.Notify.failure('Error selecting theme');
                });
        }
    </script>
@endpush

@extends('user_panel.layouts.user')
@section('title',trans('Profile Settings'))

@push('css-lib')
    <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrapicons-iconpicker.css') }}">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="main row">
            <div class="row mt-2">
                <div class="col">
                    <div class="header-text-full">
                        <h3 class="dashboard_breadcurmb_heading mb-1">@lang('Profile Settings')</h3>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <section class="edit-profile-section">
                    <div class="row g-4">
                        <div class="col-xl-12">
                            <div class="sidebar-wrapper">
                                <div class="cover">
                                    <div class="img">
                                        <img id="cover profile-cover-preview" class="profile-cover-preview"
                                             src="{{getFile($user->cover_image_driver, $user->cover_image)}}"
                                             alt="image" class="img-fluid"/>
                                        <button class="upload-img">
                                            <i class="fal fa-camera" aria-hidden="true"></i>
                                            <input class="form-control" id="userCoverPhoto" accept="image/*"
                                                   name="cover_photo" type="file"/>
                                        </button>
                                    </div>
                                </div>

                                <div class="profile">
                                    <div class="img">
                                        <img id="profile profile-image-preview" class="profile-image-preview img-fluid"
                                             src="{{getFile($user->image_driver, $user->image)}}"
                                             alt="image" />
                                        <button class="upload-img">
                                            <i class="fal fa-camera" aria-hidden="true"></i>
                                            <input class="form-control" id="userPorfileImage" name="image"
                                                   accept="image/*" type="file"/>
                                        </button>
                                    </div>
                                    <div>
                                        <h4 class="name">
                                            @lang($user->firstname) @lang($user->lastname)
                                            @if($user_information->identity_verify ==  2 && $user_information->address_verify ==  2)
                                                <i class="fas fa-check-circle" aria-hidden="true"></i>
                                            @endif
                                        </h4>
                                        <span>@lang($user->email)</span>
                                    </div>
                                </div>
                                <div class="about mt-4">
                                    <div>
                                        <p class="bio">
                                            @lang($user->bio)
                                        </p>
                                        <div class="links">
                                            @if($user->website)
                                                <a href="javascript:void(0)" >
                                                    <i class="fas fa-globe" aria-hidden="true"></i>@lang($user->website)
                                                </a>
                                            @endif
                                            @if($user->fullAddress)
                                                <a href="javascript:void(0)" >
                                                    <i class="fas fa-location-arrow"
                                                       aria-hidden="true"></i>@lang($user->fullAddress)
                                                </a>
                                            @endif

                                            <a href="javascript:void(0)" >
                                                <i class="fas fa-calendar-alt" aria-hidden="true"></i>@lang('Joined') {{ dateTime($user->created_at) }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="counts mt-4">
                                    <div class="count">
                                        @lang('Listing')
                                        <span class="badge rounded-pill bg-light text-dark">{{ count($listing_infos) }}</span>
                                    </div>
                                    <div class="count">
                                        @lang('Views')
                                        <span class="badge rounded-pill bg-light text-dark">{{ $all_viewers_count }}</span>
                                    </div>
                                    <div class="count">
                                        @lang('Follower')
                                        <span class="badge rounded-pill bg-light text-dark">{{ count($user_information->follower) }}</span>
                                    </div>
                                    <div class="count">
                                        @lang('Following')
                                        <span class="badge rounded-pill bg-light text-dark">{{ count($user_information->following) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-12">
                            <section class="profile-setting">
                                <div class="row g-lg-5">
                                    <div class="col-lg-4">
                                        <div class="sidebar-wrapper mb-3">
                                            <h3>@lang('Update Information')</h3>
                                            <div class="profile-navigator">
                                                <button tab-id="tab1"
                                                        class="tab {{ $errors->has('profile') ? 'active' : (($errors->has('password') || $errors->has('identity') || $errors->has('addressVerification')) ? '' : ' active') }}">
                                                    <i class="fal fa-user"></i> @lang('Profile information')
                                                </button>
                                                <button tab-id="tab2" class="tab {{ $errors->has('password') ? 'active' : '' }}">
                                                    <i class="fal fa-lock"></i> @lang('Password setting')
                                                </button>
                                                <button tab-id="tab3" class="tab {{ $errors->has('identity') ? 'active' : '' }}">
                                                    <i class="fal fa-id-card"></i> @lang('identity verification')
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-8">
                                        <div id="tab1"
                                             class="content {{ $errors->has('profile') ? ' active' : (($errors->has('password') || $errors->has('identity') || $errors->has('addressVerification')) ? '' :  ' active') }}">
                                            <form action="{{ route('user.profile.update')}}" method="post">
                                                @method('put')
                                                @csrf
                                                <div class="row g-4">
                                                    <div class="input-box col-md-6">
                                                        <label for="firstname">@lang('First Name')</label>
                                                        <input type="text"
                                                               class="form-control"
                                                               name="firstname"
                                                               id="firstname"
                                                               value="{{old('firstname')?: $user->firstname }}"/>
                                                        @if($errors->has('firstname'))
                                                            <div
                                                                class="error text-danger">@lang($errors->first('firstname'))
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="input-box col-md-6">
                                                        <label for="lastname">@lang('Last Name')</label>
                                                        <input type="text"
                                                               id="lastname"
                                                               name="lastname"
                                                               class="form-control"
                                                               value="{{old('lastname')?: $user->lastname }}"/>
                                                        @if($errors->has('lastname'))
                                                            <div
                                                                class="error text-danger">@lang($errors->first('lastname'))
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="input-box col-md-6">
                                                        <label for="username">@lang('Username')</label>
                                                        <input type="text"
                                                               id="username"
                                                               name="username"
                                                               value="{{old('username')?: $user->username }}"
                                                               class="form-control"/>
                                                        @if($errors->has('username'))
                                                            <div
                                                                class="error text-danger">@lang($errors->first('username'))
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="input-box col-md-6">
                                                        <label for="email">@lang('Email Address')</label>
                                                        <input type="email"
                                                               id="email"
                                                               name="email"
                                                               value="{{ $user->email }}"
                                                               readonly
                                                               class="form-control"/>
                                                        @if($errors->has('email'))
                                                            <div
                                                                class="error text-danger">@lang($errors->first('email'))
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="input-box col-md-6">
                                                        <label for="phone">@lang('Phone Number')</label>
                                                        <input type="text"
                                                               id="phone"
                                                               name="phone"
                                                               class="form-control"
                                                               value="{{$user->phone}}"/>
                                                        @if($errors->has('phone'))
                                                            <div
                                                                class="error text-danger">@lang($errors->first('phone'))
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="input-box col-md-6">
                                                        <label for="">@lang('Website')</label>
                                                        <input
                                                            class="form-control @error('website') is-invalid @enderror"
                                                            name="website"
                                                            type="text"
                                                            value="@lang($user->website)"
                                                        />
                                                        <div class="invalid-feedback">
                                                            @error('website') @lang($message) @enderror
                                                        </div>
                                                    </div>

                                                    <div class="input-box col-md-6">
                                                        <label for="language_id">@lang('Preferred language')</label>
                                                        <select class="form-select"
                                                                name="language_id"
                                                                id="language_id"
                                                                aria-label="Default select example">
                                                            <option value="" disabled>@lang('Select Language')</option>
                                                            @foreach($languages as $la)
                                                                <option
                                                                    value="{{$la->id}}" {{ old('language_id', $user->language_id) == $la->id ? 'selected' : '' }}> @lang($la->name)</option>
                                                            @endforeach
                                                        </select>
                                                        @if($errors->has('language_id'))
                                                            <div
                                                                class="error text-danger">@lang($errors->first('language_id'))
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="input-box col-md-6 col-xl-6 col-12">
                                                        <label for="">@lang('Address One')</label>
                                                        <input
                                                            class="form-control @error('address_one') is-invalid @enderror"
                                                            id="address_one"
                                                            name="address_one"
                                                            type="text"
                                                            value="@lang(@$user->address_one)"
                                                        />
                                                        @if($errors->has('address_one'))
                                                            <div
                                                                class="error text-danger">@lang($errors->first('address_one'))
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="input-box col-12">
                                                        <label for="">@lang('Address Two')</label>
                                                        <input
                                                            class="form-control @error('address_two') is-invalid @enderror"
                                                            id="address_two"
                                                            name="address_two"
                                                            type="text"
                                                            value="@lang(@$user->address_two)"
                                                        />
                                                        @if($errors->has('address_two'))
                                                            <div
                                                                class="error text-danger">@lang($errors->first('address_two'))
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="input-box col-12">
                                                        <label for="">@lang('Bio')</label>
                                                        <textarea
                                                            class="form-control @error('Bio') is-invalid @enderror"
                                                            cols="30"
                                                            rows="3"
                                                            name="bio"
                                                        >@lang(@$user->bio)</textarea>
                                                        @if($errors->has('bio'))
                                                            <div
                                                                class="error text-danger">@lang($errors->first('bio'))
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="input-box col-12">
                                                        <label for="">@lang('Social Links')</label>
                                                        <div class="form website_social_links">
                                                            @php
                                                                $oldSocialCounts = max(old('social_icon', $social_links) ? count(old('social_icon', $social_links)) : 1, 1);
                                                            @endphp

                                                            @if($oldSocialCounts > 0)
                                                                @for($i = 0; $i < $oldSocialCounts; $i++)
                                                                    <div
                                                                        class="d-flex justify-content-between append_new_social_form removeSocialLinksInput">
                                                                        <div class="input-group mt-1">
                                                                            <input type="text" name="social_icon[]" value="{{ old("social_icon.$i", $social_links[$i]->social_icon ?? '') }}" class="form-control demo__icon__picker iconpicker1 @error("social_icon.$i") is-invalid @enderror" placeholder="Pick a icon" aria-label="Pick a icon"
                                                                                   aria-describedby="basic-addon1" readonly>

                                                                            <div class="invalid-feedback">
                                                                                @error("social_icon.$i") @lang($message) @enderror
                                                                            </div>
                                                                        </div>

                                                                        <div class="input-box w-100 my-1 me-1">
                                                                            <input type="url" name="social_url[]"
                                                                                   value="{{ old("social_url.$i", $social_links[$i]->social_url ?? '') }}"
                                                                                   class="form-control @error("social_url.$i") is-invalid @enderror"
                                                                                   placeholder="@lang('URL')"/>
                                                                            @error("social_url.$i")
                                                                            <span class="text-danger">{{ $message }}</span>
                                                                            @enderror

                                                                        </div>
                                                                        <div class="my-1 me-1">
                                                                            @if($i == 0)
                                                                                <button class="btn-custom customButton add-new" type="button"
                                                                                        id="add_social_links">
                                                                                    <i class="fal fa-plus"></i>
                                                                                </button>
                                                                            @else
                                                                                <button
                                                                                    class="btn btn-outline-danger h-100 add-new remove_social_link_input_field"
                                                                                    type="button">
                                                                                    <i class="fa fa-times"></i>
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

                                                    <div class="input-box col-12">
                                                        <button class="btn-custom customButton" type="submit">@lang('Update Profile')</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                        <div id="tab2" class="content {{ $errors->has('password') ? 'active' : '' }}">
                                            <form method="post" action="{{ route('user.updatePassword') }}">
                                                @csrf
                                                <div class="row g-4">
                                                    <div class="input-box col-md-4">
                                                        <label for="">@lang('Current Password')</label>
                                                        <input type="password"
                                                               id="current_password"
                                                               name="current_password"
                                                               autocomplete="off"
                                                               class="form-control"
                                                               placeholder="@lang('Enter Current Password')"/>
                                                        @if($errors->has('current_password'))
                                                            <div
                                                                class="error text-danger">@lang($errors->first('current_password'))</div>
                                                        @endif
                                                    </div>
                                                    <div class="input-box col-md-4">
                                                        <label for="">@lang('New Password')</label>
                                                        <input type="password"
                                                               id="password"
                                                               name="password"
                                                               autocomplete="off"
                                                               class="form-control"
                                                               placeholder="@lang('Enter New Password')"/>
                                                        @if($errors->has('password'))
                                                            <div class="error text-danger">@lang($errors->first('password'))</div>
                                                        @endif
                                                    </div>

                                                    <div class="input-box col-md-4">
                                                        <label for="password_confirmation">@lang('Confirm Password')</label>
                                                        <input type="password"
                                                               id="password_confirmation"
                                                               name="password_confirmation"
                                                               autocomplete="off"
                                                               class="form-control"
                                                               placeholder="@lang('Confirm Password')"/>
                                                        @if($errors->has('password_confirmation'))
                                                            <div
                                                                class="error text-danger">@lang($errors->first('password_confirmation'))</div>
                                                        @endif
                                                    </div>

                                                    <div class="input-box col-12">
                                                        <button class="btn-custom customButton" type="submit">@lang('Update Password')</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                        <div id="tab3" class="content {{ $errors->has('identity') ? 'active' : '' }}">
                                            @if(!empty($identityFormList) && count($identityFormList) > 0)
                                                <form method="post" action="{{route('user.kyc.verification.submit')}}" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="col-md-12 mb-3">
                                                        <div class="input-box col-md-12">
                                                            <label for="identity_type">@lang('Identity Type')</label>
                                                            <select class="form-select"
                                                                    name="type" id="identity_type"
                                                                    aria-label="Default select example">
                                                                <option value="" disabled selected>@lang('Select Identity Type')</option>
                                                                @foreach($identityFormList as $sForm)
                                                                    <option value="{{$sForm->slug}}" {{ old('identity_type', @$identity_type) == $sForm->slug ? 'selected' : '' }}> @lang($sForm->name) </option>
                                                                @endforeach
                                                            </select>
                                                            @error('identity_type')
                                                            <div class="error text-danger">@lang($message) </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    @if(isset($identityForm))
                                                        @foreach($identityForm->input_form as $k => $v)
                                                            @if($v->type == "text")
                                                                <div class="input-box col-md-12 mt-2">
                                                                    <label for="{{$k}}">
                                                                        {{trans($v->field_label)}}
                                                                        @if($v->validation == 'required')
                                                                            <span class="text-danger">*</span>
                                                                        @endif
                                                                    </label>
                                                                    <input type="text" name="{{$k}}"
                                                                           class="form-control "
                                                                           value="{{old($k)}}" id="{{$k}}"
                                                                           @if($v->validation == 'required') required @endif/>
                                                                    @if($errors->has($k))
                                                                        <div
                                                                            class="error text-danger">@lang($errors->first($k))</div>
                                                                    @endif
                                                                </div>

                                                            @elseif($v->type == "textarea")
                                                                <div class="input-box col-12 mt-2">
                                                                    <label for="{{$k}}">
                                                                        {{trans($v->field_label)}}
                                                                        @if($v->validation == 'required')
                                                                            <span class="text-danger">*</span>
                                                                        @endif
                                                                    </label>
                                                                    <textarea
                                                                        name="{{$k}}"
                                                                        id="{{$k}}"
                                                                        class="form-control"
                                                                        cols="30"
                                                                        rows="3"
                                                                        placeholder="{{trans('Type Here')}}"
                                                        @if($v->validation == 'required')@endif>{{old($k)}}</textarea>
                                                                    @error($k)
                                                                    <div class="error text-danger">
                                                                        {{trans($message)}}
                                                                    </div>
                                                                    @enderror
                                                                </div>
                                                            @elseif($v->type == "file")
                                                                <div class="col-md-12 mt-2">
                                                                    <div class="form-group">
                                                                        <label class="golden-text">
                                                                            {{trans($v->field_label)}}
                                                                            @if($v->validation == 'required')
                                                                                <span class="text-danger">*</span>
                                                                            @endif
                                                                        </label>

                                                                        <br>
                                                                        <div class="fileinput fileinput-new "
                                                                             data-provides="fileinput">
                                                                            <div class="fileinput-new thumbnail "
                                                                                 data-trigger="fileinput">
                                                                                <img class="w-150px custom-verification-img"
                                                                                     src="{{ getFile(config('location.default')) }}"
                                                                                     alt="image">
                                                                            </div>
                                                                            <div
                                                                                class="fileinput-preview fileinput-exists thumbnail wh-200-150 ">
                                                                            </div>

                                                                            <div class="img-input-div">
                                                                <span class="btn bg-custom-primary text-white btn-file">
                                                                    <span
                                                                        class="fileinput-new"> @lang('Select') {{$v->field_label}}</span>
                                                                    <span
                                                                        class="fileinput-exists"> @lang('Change')</span>
                                                                    <input type="file" name="{{$k}}"
                                                                           value="{{ old($k) }}"
                                                                           accept="image/*" @if($v->validation == "required")@endif>
                                                                </span>
                                                                                <a href="#" class="btn btn-danger fileinput-exists"
                                                                                   data-dismiss="fileinput"> @lang('Remove')</a>
                                                                            </div>
                                                                        </div>

                                                                        @error($k)
                                                                        <div class="error text-danger">
                                                                            {{trans($message)}}
                                                                        </div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                        <button type="submit" class="gold-btn mt-2 btn-custom customButton">
                                                            @lang('Submit')
                                                        </button>
                                                    @endif
                                                </form>
                                            @else
                                                <div class="alert mb-0 py-3 text-center">
                                                    <span> @lang('Identity verification not required')</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <!-- Modal for profile image-->
    <div class="modal fade" id="profileImage" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">@lang('Confirmation')</h4>
                    <button type="button" class="close-btn" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fal fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="imageChangeText"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">
                        @lang('Close')
                    </button>
                    <button type="button" class="btn customButton profile-image-save">@lang('Save')</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for cover photo-->
    <div class="modal fade" id="coverPhotoModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">@lang('Confirmation')</h4>
                    <button type="button" class="close-btn" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fal fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="coverChangeText"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">
                        @lang('Close')
                    </button>
                    <button type="button" class="btn customButton cover-photo-save">@lang('Save')</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css-lib')
    <link rel="stylesheet" href="{{asset('assets/global/css/bootstrap-fileinput.css')}}">
@endpush

@push('extra-js')
    <script src="{{asset('assets/global/js/bootstrap-fileinput.js')}}"></script>
    <script src="{{ asset('assets/global/js/bootstrapicon-iconpicker.js') }}"></script>
@endpush

@push('script')

    <script>
        'use strict'
        $(document).ready(function () {
            let curIconFirst = $($(`#iconpicker1`)).data('icon');

            setIconpicker('.iconpicker1');
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
                        title: "bi bi-instagram",
                        searchTerms: ["instagram", "text"]
                    }, {
                        title: "bi bi-linkedin",
                        searchTerms: ["linkedin", "text"]
                    }, {
                        title: "bi bi-discord",
                        searchTerms: ["discord", "text"]
                    }, {
                        title: "bi bi-youtube",
                        searchTerms: ["youtube", "text"]
                    }, {
                        title: "bi bi-whatsapp",
                        searchTerms: ["whatsapp", "text"]
                    }, {
                        title: "bi bi-twitter",
                        searchTerms: ["twitter", "text"]
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
                                    <button class="btn btn-outline-danger h-100 add-new remove_social_link_input_field" type="button">
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

            // User profile image change
            $('#userPorfileImage').on('change', function () {
                $('#imageChangeText').text(`@lang('Do you want to change your profile image?')`)
                $('#profileImage').modal('show');
            });

            $(document).on('click', '.profile-image-save', function () {
                $('#profileImage').modal('hide');
                let formData = new FormData();
                formData.append('profile_image', document.getElementById('userPorfileImage').files[0]);

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "{{ route('user.profile.image.update') }}",
                    type: "post",
                    data: formData,
                    async: false,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (data) {
                        if (data.src) {
                            $('.profile-image-preview').attr('src', data.src);
                            Notiflix.Notify.success(data.message);
                        }
                    }
                });
            })

            // User cover photo change
            $('#userCoverPhoto').on('change', function () {
                $('#coverChangeText').text(`@lang('Do you want to change your cover photo?')`)
                $('#coverPhotoModal').modal('show');
            });

            $(document).on('click', '.cover-photo-save', function () {
                $('#coverPhotoModal').modal('hide');
                let formData = new FormData();
                formData.append('user_cover_photo', document.getElementById('userCoverPhoto').files[0]);

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "{{ route('user.profile.cover.image.update') }}",
                    type: "post",
                    data: formData,
                    async: false,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (data) {
                        if (data.src) {
                            $('.profile-cover-preview').attr('src', data.src);
                            Notiflix.Notify.success(data.message);
                        }
                    }
                });
            })


            $(document).on('change', "#identity_type", function () {
                let value = $(this).find('option:selected').val();
                window.location.href = "{{route('user.profile')}}/?identity_type=" + value
            });

        });
    </script>
@endpush

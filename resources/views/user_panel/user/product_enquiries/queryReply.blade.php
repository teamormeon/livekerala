@extends('user_panel.layouts.user')
@section('title',trans('Product query reply'))

@section('content')
    <div class="container-fluid p-5" id="messenger" v-cloak>
        <div class="main row">
            <div class="col-xl-12 col-md-12 col-12">
                <div class="search-bar my-search-bar">
                    <section class="conversation-section pt-3 pb-3">
                        <div class="container-fluid">
                            <div class="row g-4">
                                <div class="col-lg-7">
                                    <div class="inbox_right_side__profile__info__phone d-flex">
                                        <i class="far fa-question custom--mar"></i>
                                        <p class="ms-2"> @lang($singleProductQuery->message)</p>
                                    </div>

                                    <div class="inbox-wrapper" id="productQueryChat">
                                        <!-- top bar -->
                                        <div class="top-bar">
                                            <div>
                                                @if($singleProductQuery->get_listing->get_user->id == Auth::id())
                                                    <div class="massenger_active">
                                                        <img class="user img-fluid" src="{{getFile(optional($singleProductQuery->get_client)->image_driver, optional($singleProductQuery->get_client)->image)}}"
                                                             alt="image"/>
                                                        <p class="{{optional($singleProductQuery->get_client)->lastSeen == 'true' ? 'active-icon-messenger':'deActive-icon-messenger' }}"></p>
                                                        <span class="name text-white">@lang(optional($singleProductQuery->get_client)->firstname) @lang(optional($singleProductQuery->get_client)->lastname)</span>
                                                    </div>

                                                @else
                                                    <div class="massenger_active">
                                                        <img class="user img-fluid" src="{{getFile(optional($singleProductQuery->get_user)->image_driver, optional($singleProductQuery->get_user)->image)}}"/>
                                                        <p class="{{optional($singleProductQuery->get_user)->lastSeen == 'true' ? 'active-icon-messenger':'deActive-icon-messenger' }}"></p>
                                                        <span class="name text-white">@lang(optional($singleProductQuery->get_user)->firstname) @lang(optional($singleProductQuery->get_user)->lastname)</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- chats -->
                                        <div class="chats">
                                            <div v-for="message in messages">
                                                <div v-if="message.client_id != authUser" class="chat-box this-side">
                                                    <div class="text-wrapper">
                                                        <div class="text">
                                                            <p>@{{ message.reply }}</p>
                                                        </div>
                                                        <div class="fileimg" v-if="message.fileImage">
                                                            <a :href="message.fileImage" data-fancybox="gallery">
                                                                <img :src="message.fileImage" width="50px" height="50px">
                                                            </a>
                                                        </div>
                                                        <span class="time" v-cloak>@{{ message.sent_at }}</span>
                                                    </div>
                                                    <div class="img">
                                                        <img class="img-fluid" :src="message.sender_image"/>
                                                    </div>
                                                </div>

                                                <div v-else class="chat-box opposite-side">
                                                    <div class="img">
                                                        <img class="img-fluid" :src="message.sender_image"/>
                                                    </div>
                                                    <div class="text-wrapper">
                                                        <div class="text">
                                                            <p>@{{ message.reply }}</p>
                                                        </div>
                                                        <div class="fileimg" v-if="message.fileImage">
                                                            <a :href="message.fileImage" data-fancybox="gallery">
                                                                <img :src="message.fileImage" width="50px" height="50px">
                                                            </a>
                                                        </div>
                                                        <span class="time" v-cloak>@{{ message.sent_at }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!------------------------------------- typing area ---------------------------------------------->
                                        <div class="typing-area">
                                            <div class="img-preview" v-if="file.name">
                                                <button class="delete" @click="removeImage">
                                                    <i class="fal fa-times"></i>
                                                </button>
                                                <img id="attachment" :src="photo" class="img-fluid"/>
                                            </div>

                                            <small v-if="typingFriend.firstname" v-cloak>@{{ typingFriend.firstname }} @lang('is typing...')</small>

                                            <div class="input-group">
                                                <div>
                                                    <button class="upload-img send-file-btn pt-0">
                                                        <i class="fal fa-paperclip" aria-hidden="true"></i>
                                                        <input class="form-control" id="upload" accept="image/*" type="file" @change="handleFileUpload( $event )"/>
                                                    </button>
                                                    <span class="text-danger file"></span>
                                                </div>

                                                <input type="hidden" name="product_query_id" value="{{ $id }}" class="form-control product_query_id">

                                                <textarea v-model="message" @keydown.enter.prevent="sendMessage" @keydown="onTyping" cols="30" rows="10" class="form-control type-message"
                                                          placeholder="@lang('Type your message...')"></textarea>

                                                <button @click.prevent="sendMessage" class="submit-btn pt-0">
                                                    <i class="fal fa-paper-plane reply-submit-btn"
                                                       aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-5">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="search-bar my-search-bar">
                                                <form action="" method="get" enctype="multipart/form-data">
                                                    <div class="row g-3">
                                                        <div class="inbox_right_side bg-white rounded">
                                                            <div class="d-flex justify-content-center">
                                                                <h5>@lang('Product Information')</h5>
                                                            </div>
                                                            <div class="inbox_right_side__profile  p-3">
                                                                <div
                                                                    class="inbox_right_side__profile__header text-center mb-4">
                                                                    <img src="{{ getFile(optional($singleProductQuery->get_product)->driver, optional($singleProductQuery->get_product)->product_thumbnail) }}"
                                                                         class="productInfoThumbnail">
                                                                </div>

                                                                <div class="inbox_right_side__profile__info">
                                                                    <div
                                                                        class="inbox_right_side__profile__info__phone d-flex justify-content-between">
                                                                        <p>{{ __('Listing') }} : </p>
                                                                        <p>@if(optional($singleProductQuery->get_listing)->title)
                                                                                <a href="{{ route('listing.details',optional($singleProductQuery->get_listing)->slug) }}" target="_blank" class="text-secondary">
                                                                                    @lang(optional($singleProductQuery->get_listing)->title)
                                                                                </a>
                                                                            @else
                                                                                @lang('N/A')
                                                                            @endif</p>
                                                                    </div>
                                                                    <div
                                                                        class="inbox_right_side__profile__info__phone d-flex justify-content-between">
                                                                        <p>{{ __('Product') }} : </p>
                                                                        <p>@if(optional($singleProductQuery->get_product)->product_title)
                                                                                @lang(optional($singleProductQuery->get_product)->product_title)
                                                                            @else
                                                                                @lang('N/A')
                                                                            @endif</p>
                                                                    </div>
                                                                    <div
                                                                        class="inbox_right_side__profile__info__phone d-flex justify-content-between">
                                                                        <p>{{ __('Price') }} :</p>
                                                                        <p>@if(optional($singleProductQuery->get_product)->product_price)
                                                                            {{ currencyPosition(optional($singleProductQuery->get_product)->product_price) }}
                                                                            @else
                                                                                @lang('N/A')
                                                                            @endif
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="search-bar my-search-bar">
                                                <form action="" method="get" enctype="multipart/form-data">
                                                    <div class="row g-3">
                                                        <div class="d-flex justify-content-center">
                                                            <h5>@lang('Customer Information')</h5>
                                                        </div>
                                                        <div class="inbox_right_side bg-white rounded m-0">
                                                            <div class="inbox_right_side__profile  p-3">
                                                                <div class="inbox_right_side__profile__header text-center mb-4">
                                                                    <img src="{{ getFile(optional($singleProductQuery->get_client)->image_driver, optional($singleProductQuery->get_client)->image) }}"
                                                                         class="productClientImage">
                                                                    <h6 class="mt-2 mb-0">
                                                                        <b>@lang(optional($singleProductQuery->get_client)->firstname) @lang(optional($singleProductQuery->get_client)->lastname)</b>
                                                                    </h6>
                                                                </div>

                                                                <div class="inbox_right_side__profile__info">
                                                                    <div
                                                                        class="inbox_right_side__profile__info__phone d-flex justify-content-between">
                                                                        <p>{{ __('Email') }}:</p>
                                                                        <p>@if(optional($singleProductQuery->get_client)->email)
                                                                                {{ optional($singleProductQuery->get_client)->email }}
                                                                            @else
                                                                                @lang('N/A')
                                                                            @endif</p>
                                                                    </div>

                                                                    <div
                                                                        class="inbox_right_side__profile__info__phone d-flex justify-content-between">
                                                                        <p>{{ __('Website') }}: </p>
                                                                        <p>
                                                                            @if(optional($singleProductQuery->get_client)->website)
                                                                                <a href="{{ optional($singleProductQuery->get_client)->website }}" target="_blank" class="text-secondary">
                                                                                    {{ optional($singleProductQuery->get_client)->website }}
                                                                                </a>
                                                                            @else
                                                                                @lang('N/A')
                                                                            @endif</p>
                                                                    </div>

                                                                    <div
                                                                        class="inbox_right_side__profile__info__phone d-flex justify-content-between">
                                                                        <p>{{ __('Address') }}:</p>
                                                                        <p>@if(optional($singleProductQuery->get_client)->fullAddress)
                                                                                @lang(optional($singleProductQuery->get_client)->fullAddress)
                                                                            @else
                                                                                @lang('N/A')
                                                                            @endif</p>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="right_side_bottom p-3">
                                                                <a href="{{ route('profile', $singleProductQuery->get_client->username) }}" target="_blank"
                                                                   class="btn customButton btn-custom__product__reply d-flex justify-content-center">@lang('Visit Profile')</a>
                                                            </div>
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
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{asset('assets/global/js/laravel-echo.common.min.js')}}"></script>

    <script>
        "use strict";
        let messenger = new Vue({
            el: "#messenger",
            data: {
                item: {},
                authUser: '',
                id: '',
                selectedContactId: 0,
                selectedContact: null,
                messages: [],
                message: '',
                file: '',
                photo: '',
                myProfile: [],  //<!-- typing show -->
                typingFriend: {},   //<!-- typing show -->
                typingClock: null,  //<!-- typing show -->
                errors: {},
            },
            mounted() {
                this.authUser = "{{auth()->user()->id}}";
                this.allMessages();
                this.wsConnection();
                this.listenUser();

            },
            watch: {
                messages(messages) {
                    this.scrollToBottom();
                }
            },
            methods: {
                handleFileUpload(event) {
                    if (event.target.files[0].size > 3145728) {  //made condition: file will less than 3MB(3*1024*1024=1048576 byte)
                        Notiflix.Notify.Failure("@lang('Image should be less than 3MB!')");
                    } else {
                        this.file = event.target.files[0];
                        this.photo = URL.createObjectURL(event.target.files[0]);
                    }
                },
                removeImage() {
                    this.file = '';
                    this.photo = '';
                },
                scrollToBottom() {
                    setTimeout(() => {
                        let messagesContainer = this.$el.querySelector(".chats");
                        messagesContainer.scrollTop = messagesContainer.scrollHeight;
                    }, 50);
                },
                allMessages() {
                    let item = this.item;
                    var product_query_id = $('.product_query_id').val();
                    var client_id = {{optional($singleProductQuery->get_user)->id}};
                    item.productId = product_query_id;
                    this.selectedContactId = client_id;
                    axios.post("{{ route('user.product.query.reply.message.render') }}", this.item)
                        .then(response => {
                            this.myProfile = response.data[response.data.length - 1];
                            this.messages = response.data.filter(ownProfile => ownProfile.id !== this.myProfile.id);
                        });
                },
                sendMessage() {
                    var _this = this;
                    if (this.message === '' && this.file === '') {
                        Notiflix.Notify.Failure("@lang('Can\'t send empty message')");
                        return;
                    }
                    let formData = new FormData();
                    formData.append('file', this.file);
                    formData.append('reply', this.message);
                    formData.append('product_query_id', $('.product_query_id').val());
                    var check = "{{ $singleProductQuery->user_id }}";
                    if (this.authUser != check) {
                        var client_id = {{ $singleProductQuery->user_id }};
                    } else {
                        var client_id = {{ $singleProductQuery->client_id }};
                    }

                    formData.append('client_id', client_id);

                    const headers = {'Content-Type': 'multipart/form-data'};
                    axios.post("{{route('user.product.query.reply.message')}}", formData, {headers})
                        .then(function (res) {
                            _this.message = '';
                            _this.file = '';
                            _this.messages.push(res.data);
                        })
                        .catch(error => this.errors = error.response.data.errors);
                },
                wsConnection() {
                    window.Echo = new Echo({
                        broadcaster: 'pusher',
                        key: '{{ config("broadcasting.connections.pusher.key") }}',
                        cluster: '{{ config("broadcasting.connections.pusher.options.cluster") }}',
                        forceTLS: true,
                        authEndpoint: '{{ url('/') }}/broadcasting/auth'
                    });
                },
                listenUser() {
                    let _this = this;
                    window.Echo.private('user.chat.{{ auth()->id() }}')
                        .listen('ChatEvent', (e) => {
                            _this.messages.push(e.message);
                        })
                        .listenForWhisper('typing', (e) => {
                            console.log('test');
                            _this.typingFriend = e.user;
                        });
                },
                onTyping() {
                    var check = "{{ $singleProductQuery->user_id }}";
                    if (this.authUser != check) {
                        var client_id = {{ $singleProductQuery->user_id }};
                    } else {
                        var client_id = {{ $singleProductQuery->client_id }};
                    }
                    Echo.private('user.chat.' + client_id).whisper('typing', {
                        user: this.myProfile
                    });
                },
            }
        });
    </script>

@endpush


@extends('user_panel.layouts.user')
@section('title',trans('Product query reply'))

@section('content')
    <div class="container-fluid p-5" id="messenger">
        <div class="main row">
            <div class="col-xl-12 col-md-12 col-12">
                <div class="search-bar my-search-bar">
                    <section class="conversation-section pt-3 pb-3">
                        <div class="container-fluid">
                            <div class="row g-4">
                                <div class="col-lg-7">
                                    <div class="inbox-wrapper" id="pushChatArea">
                                        <!-- top bar -->
                                        <div class="top-bar">
                                            <div class="d-flex">
                                                @if(!empty($persons))
                                                    @forelse($persons as $person)
                                                        <div class="massenger_active">
                                                            <img class="user img-fluid"
                                                                 title="{{'@'.$person->username}}"
                                                                 src="{{$person->imgPath}}"/>
                                                            <p class="{{optional($claimRequest->get_user)->lastSeen == 'true' ? 'active-icon-messenger':'deActive-icon-messenger' }}"></p>
                                                            <span class="name text-white">@lang(optional($claimRequest->get_user)->fullName)</span>
                                                        </div>
                                                    @empty
                                                    @endforelse
                                                @endif
                                            </div>
                                        </div>

                                        <!-- chats -->
                                        <div class="chats" ref="chatArea">
                                            <div v-for="item in items">
                                                <div v-if="item.userable_id == auth_id" class="chat-box this-side" :title="item.userable.username">
                                                    <div class="text-wrapper">
                                                        <div class="text">
                                                            <p>@{{ item.description }}</p>
                                                        </div>
                                                        <div class="fileimg" v-if="item.fileImage">
                                                            <a :href="item.fileImage" data-fancybox="gallery">
                                                                <img :src="item.fileImage" width="50px" height="50px">
                                                            </a>
                                                        </div>
                                                        <span class="time" v-cloak>@{{ item.formatted_date }}</span>
                                                    </div>
                                                    <div class="img">
                                                        <img class="img-fluid" :src="item.userable.imgPath"/>
                                                    </div>
                                                </div>

                                                <div v-else class="chat-box opposite-side">
                                                    <div class="img">
                                                        <img class="img-fluid" :src="item.userable.imgPath"/>
                                                    </div>
                                                    <div class="text-wrapper">
                                                        <div class="text">
                                                            <p>@{{ item.description }}</p>
                                                        </div>
                                                        <div class="fileimg" v-if="item.fileImage">
                                                            <a :href="item.fileImage" data-fancybox="gallery">
                                                                <img :src="item.fileImage" width="50px" height="50px">
                                                            </a>
                                                        </div>
                                                        <span class="time" v-cloak>@{{ item.formatted_date }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="chatDisableContent" v-if="chatType == 0">
                                                <div class="text">
                                                    <span>The chat option has been disabled by the Admin</span>
                                                </div>


                                            </div>
                                        </div>

                                        <!------------------------------------- typing area ---------------------------------------------->
                                        <div class="typing-area" v-if="chatType == 1">
                                            {{--                                            <div class="img-preview" v-if="file.name">--}}
                                            {{--                                                <button class="delete" @click="removeImage">--}}
                                            {{--                                                    <i class="fal fa-times"></i>--}}
                                            {{--                                                </button>--}}
                                            {{--                                                <img id="attachment" :src="photo" class="img-fluid"/>--}}
                                            {{--                                            </div>--}}

                                            {{--                                            <small v-if="typingFriend.firstname" v-cloak>@{{ typingFriend.firstname }} @lang('is typing...')</small>--}}

                                            <div class="input-group">
                                                <div>
                                                    <button class="upload-img send-file-btn pt-0">
                                                        <i class="fal fa-paperclip" aria-hidden="true"></i>
                                                        <input class="form-control" id="upload" accept="image/*"
                                                               type="file" @change="handleFileUpload( $event )"/>
                                                    </button>
                                                    <span class="text-danger file"></span>
                                                </div>

                                                <input type="hidden" name="product_query_id" value=""
                                                       class="form-control product_query_id">

                                                <textarea v-model.trim="message" cols="30" rows="10"
                                                          class="form-control type-message"
                                                          placeholder="@lang('Type your message...')"></textarea>

                                                <button @click="send" class="submit-btn pt-0">
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
                                                                <h5>@lang('Listing Information')</h5>
                                                            </div>
                                                            <div class="inbox_right_side__profile  p-3">
                                                                <div
                                                                        class="inbox_right_side__profile__header text-center mb-2">
                                                                    <img src="{{ getFile(optional($claimRequest->get_listing)->thumbnail_driver, optional($claimRequest->get_listing)->thumbnail) }}"
                                                                         class="productInfoThumbnail">
                                                                </div>

                                                                <div class="inbox_right_side__profile__info">
                                                                    <div
                                                                            class="inbox_right_side__profile__info__phone d-flex justify-content-between">
                                                                        <p>{{ __('Listing Title') }} : </p>
                                                                        <p>@if(optional($claimRequest->get_listing)->title)
                                                                                <a href="{{ route('listing.details',optional($claimRequest->get_listing)->slug) }}" class="text-secondary"
                                                                                   target="_blank">
                                                                                    @lang(optional($claimRequest->get_listing)->title)
                                                                                </a>
                                                                            @else
                                                                                @lang('N/A')
                                                                            @endif</p>
                                                                    </div>
                                                                    <div
                                                                            class="inbox_right_side__profile__info__phone d-flex align-items-center justify-content-between">
                                                                        <p class="mb-0">{{ __('Status') }} :</p>
                                                                        <p class="mb-0 badge bg-{{ $claimRequest->get_listing->status == 1 ? 'success' :($claimRequest->get_listing->status == 2 ? 'danger' : 'warning')}}">@if(optional($claimRequest->get_listing)->status)
                                                                                @lang(optional($claimRequest->get_listing)->status == 0 ? 'Pending' : (optional($claimRequest->get_listing)->status == 1 ? 'Approved' : 'Rejected'))
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

                                        @if($isAuthor == true)
                                            <div class="col-lg-12">
                                                <div class="search-bar my-search-bar">
                                                    <form action="" method="get" enctype="multipart/form-data">
                                                        <div class="row g-3">
                                                            <div class="d-flex justify-content-center">
                                                                <h5>@lang('Claimer Information')</h5>
                                                            </div>
                                                            <div class="inbox_right_side bg-white rounded m-0">
                                                                <div class="inbox_right_side__profile  p-3">
                                                                    <div class="inbox_right_side__profile__header text-center mb-2">
                                                                        <img src="{{ getFile(optional($claimRequest->get_client)->image_driver, optional($claimRequest->get_client)->image) }}"
                                                                             class="productClientImage">
                                                                        <h6 class="mt-2 mb-0">
                                                                            <b>@lang(optional($claimRequest->get_client)->fullName)</b>
                                                                        </h6>
                                                                    </div>

                                                                    <div class="inbox_right_side__profile__info">
                                                                        <div
                                                                                class="inbox_right_side__profile__info__phone d-flex justify-content-between">
                                                                            <p>{{ __('Email') }}:</p>
                                                                            <p>@if(optional($claimRequest->get_client)->email)
                                                                                    {{ optional($claimRequest->get_client)->email }}
                                                                                @else
                                                                                    @lang('N/A')
                                                                                @endif</p>
                                                                        </div>

                                                                        <div class="inbox_right_side__profile__info__phone d-flex justify-content-between">
                                                                            <p>{{ __('Website') }}: </p>
                                                                            <p>
                                                                                @if(optional($claimRequest->get_client)->website)
                                                                                    <a href="{{ optional($claimRequest->get_client)->website }}" class="text-secondary"
                                                                                       target="_blank">
                                                                                        {{ optional($claimRequest->get_client)->website }}
                                                                                    </a>
                                                                                @else
                                                                                    @lang('N/A')
                                                                                @endif</p>
                                                                        </div>

                                                                        <div
                                                                                class="inbox_right_side__profile__info__phone d-flex justify-content-between">
                                                                            <p class="mb-0">{{ __('Address') }}:</p>
                                                                            <p class="mb-0">@if(optional($claimRequest->get_client)->fullAddress)
                                                                                    @lang(optional($claimRequest->get_client)->fullAddress)
                                                                                @else
                                                                                    @lang('N/A')
                                                                                @endif</p>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="right_side_bottom p-3">
                                                                    <a href="{{ route('profile', $claimRequest->get_client->username) }}"
                                                                       target="_blank"
                                                                       class="btn customButton btn-custom__product__reply d-flex justify-content-center">@lang('Visit Profile')</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        @else
                                            <div class="col-lg-12">
                                                <div class="search-bar my-search-bar">
                                                    <form action="" method="get" enctype="multipart/form-data">
                                                        <div class="row g-3">
                                                            <div class="d-flex justify-content-center">
                                                                <h5>@lang('Listing Owner Information')</h5>
                                                            </div>
                                                            <div class="inbox_right_side bg-white rounded m-0">
                                                                <div class="inbox_right_side__profile  p-3">
                                                                    <div class="inbox_right_side__profile__header text-center mb-4">
                                                                        <img src="{{ getFile(optional($claimRequest->get_listing_owner)->image_driver, optional($claimRequest->get_listing_owner)->image) }}"
                                                                             class="productClientImage">
                                                                        <h6 class="mt-2 mb-0">
                                                                            <b>@lang(optional($claimRequest->get_listing_owner)->fullName)</b>
                                                                        </h6>
                                                                    </div>

                                                                    <div class="inbox_right_side__profile__info">
                                                                        <div
                                                                                class="inbox_right_side__profile__info__phone d-flex justify-content-between">
                                                                            <p>{{ __('Email') }}:</p>
                                                                            <p>@if(optional($claimRequest->get_listing_owner)->email)
                                                                                    {{ optional($claimRequest->get_listing_owner)->email }}
                                                                                @else
                                                                                    @lang('N/A')
                                                                                @endif</p>
                                                                        </div>

                                                                        <div
                                                                                class="inbox_right_side__profile__info__phone d-flex justify-content-between">
                                                                            <p>{{ __('Website') }}: </p>
                                                                            <p>
                                                                                @if(optional($claimRequest->get_listing_owner)->website)
                                                                                    <a href="{{ optional($claimRequest->get_listing_owner)->website }}" class="text-secondary"
                                                                                       target="_blank">
                                                                                        {{ optional($claimRequest->get_listing_owner)->website }}
                                                                                    </a>
                                                                                @else
                                                                                    @lang('N/A')
                                                                                @endif</p>
                                                                        </div>

                                                                        <div
                                                                                class="inbox_right_side__profile__info__phone d-flex justify-content-between">
                                                                            <p>{{ __('Address') }}:</p>
                                                                            <p>@if(optional($claimRequest->get_listing_owner)->fullAddress)
                                                                                    @lang(optional($claimRequest->get_listing_owner)->fullAddress)
                                                                                @else
                                                                                    @lang('N/A')
                                                                                @endif</p>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="right_side_bottom p-3">
                                                                    <a href="{{ route('profile', $claimRequest->get_listing_owner->username) }}"
                                                                       target="_blank"
                                                                       class="btn customButton btn-custom__product__reply d-flex justify-content-center">@lang('Visit Profile')</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        @endif
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

    <script>
        'use strict';
        let pushChatArea = new Vue({
            el: "#pushChatArea",
            data: {
                chatType: 0,
                items: [],
                auth_id: "{{auth()->id()}}",
                auth_model: "App\\Models\\User",
                admin_model: "App\\Models\\Admin",
                message: ''
            },
            beforeMount() {
                this.chatType = "{{$claimRequest->is_chat_enable}}";
                this.getNotifications();
                this.pushNewItem();
            },
            methods: {
                scrollToBottom() {
                    let messageDisplay = this.$refs.chatArea;
                    messageDisplay.scrollTop = messageDisplay.scrollHeight;
                },

                getNotifications() {
                    let app = this;
                    axios.get("{{ route('user.claim.business.push.chat.show',$claimRequest->uuid) }}")
                        .then(function (res) {
                            app.items = res.data;
                            Vue.nextTick(() => {
                                app.scrollToBottom();
                            });
                        })
                },

                pushNewItem() {
                    let app = this;
                    // Pusher.logToConsole = true;
                    let pusher = new Pusher("{{ env('PUSHER_APP_KEY') }}", {
                        encrypted: true,
                        cluster: "{{ env('PUSHER_APP_CLUSTER') }}"
                    });

                    let channel = pusher.subscribe('offer-chat-notification.' + "{{ $claimRequest->uuid }}");

                    channel.bind('App\\Events\\OfferChatNotification', function (data) {
                        app.items.push(data.message);
                        var x = document.getElementById("myAudio");
                        x.play();
                        Vue.nextTick(() => {
                            app.scrollToBottom();
                        });

                    });
                    channel.bind('App\\Events\\UpdateOfferChatNotification', function (data) {
                        app.getNotifications();
                        console.log('update')
                    });

                    let channelStage = pusher.subscribe('chat.stage.change.' + "{{ $claimRequest->uuid }}");
                    channelStage.bind('App\\Events\\ChatStageChangeEvent', function (data) {
                        if (data.message == 'enable') {
                            app.chatType = 1;
                        } else {
                            app.chatType = 0;
                        }
                    });
                },

                send() {
                    let app = this;
                    if (app.message.length == 0) {
                        Notiflix.Notify.failure(`{{trans('Type your message two')}}`);
                        return 0;
                    }

                    axios.post("{{ route('user.claim.business.push.chat.new.message')}}", {
                        listing_id: "{{$claimRequest->listing_id}}",
                        claim_business_id: "{{$claimRequest->id}}",
                        message: app.message
                    }).then(function (res) {
                        if (res.data.errors) {
                            var err = res.data.errors;
                            for (const property in err) {
                                // Notiflix.Notify.failure(`${err[property]}`);
                            }
                            return 0;
                        }

                        app.message = '';

                        if (res.data.success == true) {
                            Vue.nextTick(() => {
                                app.scrollToBottom();
                            });
                        }
                    }).catch(function (error) {
                        console.log(error)
                    });

                }
            }
        });
    </script>

@endpush


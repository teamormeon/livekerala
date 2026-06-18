@extends('admin.layouts.app')
@section('page_title', __('Claim Business Conversation'))
@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-sm">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-no-gutter">
                            <li class="breadcrumb-item"><a class="breadcrumb-link"
                                                           href="javascript:void(0)">@lang('Dashboard')</a></li>
                            <li class="breadcrumb-item"><a class="breadcrumb-link"
                                                           href="javascript:void(0)">@lang('Claim List')</a></li>
                            <li class="breadcrumb-item active" aria-current="page">@lang('Conversation')</li>
                        </ol>
                    </nav>
                    <h1 class="page-header-title">@lang('Conversation')</h1>
                </div>
            </div>
        </div>
        <div class="card-body d-flex g-2">
            <div class="col-lg-8  col-xl-8">
                    @if(!empty($persons))
                        <div class="">
                            <div class="card-body">
                                <div class="report  justify-content-center " id="pushChatArea">
                                    <audio id="myAudio">
                                        <source src="{{asset('assets/admin/css/sound.mp3')}}" type="audio/mpeg">
                                    </audio>
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="adiv justify-content-between align-items-center p-2 d-flex">
                                                <div class="d-flex align-items-center">
                                                    <h4><i class="fas fa-users"></i> {{trans('Conversation')}}</h4>
                                                    <div class="d-flex user-chatlist ms-2">
                                                        @if(!empty($persons))
                                                            @forelse($persons as $person)
                                                                @if($person)
                                                                    <div class="d-flex no-block align-items-center">
                                                                        <a href="javascript:void(0)"
                                                                           data-bs-toggle="tooltip" data-bs-html="true"
                                                                           title="{{$person->username}}"
                                                                           class="me-1 position-relative">

                                                                            <i class="batti position-absolute fa fa-circle activeIcon text-{{($person->LastSeenActivity == true) ?'success':'warning' }} font-12"
                                                                               title="{{($person->LastSeenActivity == true) ?'Online':'Away' }}"></i>
                                                                            <img src="{{$person->imgPath}}"
                                                                                 alt="user" class="rounded-circle "
                                                                                 width="30"
                                                                                 height="30">
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                            @empty
                                                            @endforelse
                                                        @endif
                                                    </div>
                                                </div>
                                                @if(adminAccessRoute(config('role.claim_business_conversation.access.edit')))
                                                    <button type="button" v-if="chatType == 0" @click.prevent="chatStageChange('enable')" class="btn btn-sm btn-success ms-2">Enable Chat</button>
                                                    <button type="button" v-if="chatType == 1" @click.prevent="chatStageChange('disable')" class="btn btn-sm btn-danger ms-2">Disable Chat</button>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="card-body bg-light adminChatBox">
                                            <div class="chat-length" ref="chatArea">
                                                <div v-for="item in items" :key="item.id">
                                                    <div
                                                            v-if=" item.userable_type == auth_model"
                                                            class="d-flex flex-row justify-content-end align-items-center p-3 "
                                                            :title="item.userable.username">
                                                        <div
                                                                class="me-2 ps-2 pe-2 position-relative mw-130 messagebox">
                                                            <span class="fw-bold">@{{item.description}}</span> <br>
                                                            <span class="timmer fs-6 float-end">@{{item.formatted_date}}</span>

                                                        </div>
                                                        <img
                                                                :src="item.userable.imgPath"
                                                                width="40" height="40" class="rounded-circle">
                                                    </div>

                                                    <div v-else class="d-flex flex-row justify-content-start align-items-center p-3"
                                                         :title="item.userable.username">
                                                        <img :src="item.userable.imgPath" class="rounded-circle"
                                                             width="40" height="40">
                                                        <div class="chat ms-2 ps-2 pe-5 position-relative mw-130 messagebox">
                                                            <span class="fw-bold">@{{item.description}}</span> <br>
                                                            <span class="timmer fs-6 float-end">@{{item.formatted_date}}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            @if(adminAccessRoute(config('role.claim_business_conversation.access.edit')))
                                                <form @submit.prevent="send" enctype="multipart/form-data" method="post">
                                                    <div class="writing-box d-flex justify-content-between align-items-center">
                                                        <div class="input-group form-group">
                                                            <input class="form-control w-100 type_msg"
                                                                   v-model.trim="message"
                                                                   placeholder="{{trans('Type your message')}}"/>
                                                        </div>
                                                        <div class="send text-center">
                                                            <button type="button" class="btn btn-success btn--success"
                                                                    @click="send">
                                                                <i class="fas fa-paper-plane"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            <div class="col-lg-4 col-xl-4 ms-2">
                <div class="card">
                    <div class="card-header">
                        <h5>@lang('Listing Information')</h5>
                    </div>
                    <div class="card-body">
                        <div class="listingImgBox">
                            <img src="{{ getFile(optional($claimBusiness->get_listing)->thumbnail_driver, optional($claimBusiness->get_listing)->thumbnail) }}" alt="image">
                        </div>
                        <div class="mt-2">
                            <div class="d-flex justify-content-between">
                                <p>@lang('Listing Title') : </p>
                                <p>
                                    <a href="{{ route('listing.details',optional($claimBusiness->get_listing)->slug) }}" target="_blank">@lang(optional($claimBusiness->get_listing)->title)</a>
                                </p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p>@lang('Listing Status') : </p>
                                <p>
                                    {{ optional($claimBusiness->get_listing)->status == 0 ? 'Pending' : (optional($claimBusiness->get_listing)->status == 1 ? 'Approved' : 'Rejected') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-2">
                    <div class="card-header">
                        <h5>@lang('Listing Owner Information')</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5 listingOwnerImgBox">
                                <a href="{{ route('admin.user.view.profile',optional($claimBusiness->get_listing_owner)->id) }}"><img src="{{ optional($claimBusiness->get_listing_owner)->imgPath }}" alt="image"></a>
                            </div>
                            <div class="col-7">
                                <p class="mb-1">
                                    @lang(optional($claimBusiness->get_listing_owner)->email)
                                </p>
                                <p class="mb-1">
                                    <a href="{{ optional($claimBusiness->get_listing_owner)->website }}" target="_blank">{{ optional($claimBusiness->get_listing_owner)->website }}</a>
                                </p>
                                <p class="mb-1">
                                    {{ optional($claimBusiness->get_listing_owner)->fullAddress }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-2">
                    <div class="card-header">
                        <h5>@lang('Listing Claimer Information')</h5>
                    </div>
                    <div class="card-body">
                        <div class="row col-12">
                            <div class="col-5 listingOwnerImgBox">
                                <a href="{{ route('admin.user.view.profile',optional($claimBusiness->get_client)->id) }}"><img src="{{ optional($claimBusiness->get_client)->imgPath }}" alt="image"></a>
                            </div>
                            <div class="col-7">
                                <p class="mb-1">
                                    @lang(optional($claimBusiness->get_client)->email)
                                </p>
                                <p class="mb-1">
                                    <a href="{{ optional($claimBusiness->get_client)->website }}" target="_blank">{{ optional($claimBusiness->get_client)->website }}</a>
                                </p>
                                <p class="mb-1">
                                    {{ optional($claimBusiness->get_client)->fullAddress }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection
@push('script')
    <script>
        'use strict';
        @if($claimBusiness)
            let pushChatArea = new Vue({
                el: "#pushChatArea",
                data: {
                    chatType: 0,
                    items: [],
                    auth_id: "{{auth()->guard('admin')->id()}}",
                    auth_model: "App\\Models\\Admin",
                    message: ''
                },
                beforeMount() {
                    this.chatType = "{{$claimBusiness->is_chat_enable}}";
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
                        axios.get("{{ route('admin.claim.business.conversation.push.chat.show',$claimBusiness->uuid) }}")
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

                        let channel = pusher.subscribe('offer-chat-notification.' + "{{ $claimBusiness->uuid }}");
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

                        let channelStage = pusher.subscribe('chat.stage.change.' + "{{ $claimBusiness->uuid }}");
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
                            Notiflix.Notify.failure(`{{trans('Type your message')}}`);
                            return 0;
                        }


                        axios.post("{{ route('admin.claim.business.conversation.push.chat.new.message')}}", {
                            listing_id: "{{$claimBusiness->listing_id}}",
                            claim_business_id: "{{$claimBusiness->id}}",
                            message: app.message
                        }).then(function (res) {
                            if (res.data.errors) {
                                var err = res.data.errors;
                                for (const property in err) {
                                    Notiflix.Notify.failure(`${err[property]}`);
                                }
                            }

                            if (res.data.success == true) {
                                app.message = '';
                                Vue.nextTick(() => {
                                    app.scrollToBottom();
                                });
                            }
                        }).catch(function (error) {

                        });

                    },
                    chatStageChange(type){
                        let app = this;
                        axios.post("{{ route('admin.claim.chat.stage.change')}}", {
                            claim_id: "{{$claimBusiness->id}}",
                            type: type
                        }).then(function (res) {
                            if (res.data.errors) {
                                var err = res.data.errors;
                                for (const property in err) {
                                    Notiflix.Notify.failure(`${err[property]}`);
                                }
                            }
                        }).catch(function (error) {

                        });
                    }
                }
            });
        @endif
    </script>

@endpush

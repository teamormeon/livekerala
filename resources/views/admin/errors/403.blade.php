
@extends('admin.layouts.app')
@section('page_title', __('403'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <p class="text-center times-403"><i class="fa fa-user-times"></i></p>
                        <h4 class="card-title mb-3 text-center text-primary"> @lang("You don't have permission to access that link")</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

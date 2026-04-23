@extends(template().'layouts.app')
@section('title',trans('Category'))

@section('content')
    @if (count($categories) > 0)
       <section class="brands-section">
           <div class="container">
               @if(isset($categorySingle))
                   <div class="row">
                       <div class="section-header text-center">
                           <div class="section-subtitle">@lang(json_decode($categorySingle->description)->title)</div>
                           <h2>@lang(json_decode($categorySingle->description)->sub_title)</h2>
                           <p class="cmn-para-text mx-auto">@lang(json_decode($categorySingle->description)->description)</p>
                       </div>
                   </div>
               @endif
               <div class="row">
                   <div class="col-lg-8 mx-auto ">
                       <div class="search-box2 mb-50">
                           <input type="search" class="form-control searchInput" placeholder="@lang('Search here')">
                           <button type="submit" class="search-btn2 searchBtn"><i class="far fa-search"></i>@lang('Serach')</button>
                       </div>
                   </div>
               </div>
               <div class="row g-4" id="renderCategory">
                   @include(template().'frontend.category.partials.renderCategory')
               </div>
           </div>
       </section>
    @else
        <div class="custom-not-found">
            <img src="{{ asset(template(true).'img/error/error.png') }}" alt="image" class="img-fluid">
        </div>
    @endif

@endsection

@push('script')
      <script>
         'use strict'
         $(document).ready(function(){
            $('.searchBtn').on('click', function(){
               let character = $('.searchInput').val();

               $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "{{ route('category.search') }}",
                    type: "post",
                    data:{
                        character:character,
                    },
                    success: function(response)
                    {
                        if ((response.count)*1 <  1) {
                                $('#renderCategory').html(`<div class="custom-not-found">
                                                <img src="{{ asset(template(true).'img/error/error.png') }}" alt="{{ basicControl()->site_title }}"
                                                     class="img-fluid">
                                            </div>`);
                        } else {
                           $('#renderCategory').html(response.data);
                        }
                    }
                });
            });
         });
      </script>
@endpush



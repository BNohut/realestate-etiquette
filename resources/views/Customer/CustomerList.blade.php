@php
  use App\Models\Setting;
  $jsonData = json_decode(Setting::first()->config, true)
@endphp
<div class="card main-card" style="@if($customers->count() == 0) border: 1px solid rgb(110, 126, 199)@endif">
    <div class="row px-3">
        @if($customers->count() > 0)
        @foreach($customers as $customer)
            @if($loop->iteration <= 2)
                <div class="col-md-6 col-sm-12 mt-3">
            @else
                <div class="col-md-6 col-sm-12 mt-3">
            @endif

            <div class="card consultant-card mb-3">
                <div class="row g-0">
                    <div class="col-md-4 col-sm-12">
                        @if ($customer->contactS->attachment()->first()?->url)
                        <img src="{{$customer->contactS->attachment()->first()->url}}" class="img-fluid card-image-customer" alt="Profile Picture">
                        @else
                        <img src="{{asset('images/user.png')}}" class="img-fluid card-image-customer" alt="Profile Picture">
                        @endif
                    </div>
                    <div class="col-md-8 col-sm-12">
                        <div class="card-body pt-3 pb-0">
                            @if($customer->contact_id)
                            <p class="card-title card-content"><span class="card-sub-title">{{__('Contact')}}:</span> {{$customer->contactS->name}}</p>
                            @endif
                            @if($customer->user_id)
                            <p class="card-title card-content"><span class="card-sub-title">{{__('Consultant')}}:</span> {{$customer->presenter()->fullName}}</p>
                            @endif
                            <p class="card-title card-content"><span class="card-sub-title">{{__('Budget')}}:</span> {{number_format($customer->budget, 0, ',', '.')}} ₺</p>
                            <p class="card-title card-content"><span class="card-sub-title">{{__('Location')}}:</span> {{$customer->presenter()->fullAddress}}</p>
                            <p class="card-title card-content"><span class="card-sub-title">{{__('Resource')}}:</span> {{$jsonData['contact_resources'][$customer->contact_resource]}}</p>
                        </div>
                        {{-- <div class="row">
                            <div class="col text-end">
                                <p class="text-muted">
                                <x-orchid-icon path='calendar' style="font-size: .7rem; margin-top: -0.2rem;"></x-orchid-icon>
                                    {{changeDateFormat($customer->record_date, 1)}}</p>
                            </div>
                        </div> --}}
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-2 pl-2 d-flex gap-3 justify-content-center">
                        @checkAccess('platform.records.detail', $customer->user_id)
                        <a class="btn gap-2 card-detail-button card-button" 
                        href="{{route('platform.customer.detail', $customer)}}"
                        >
                            <x-orchid-icon path='eye'></x-orchid-icon>
                            <strong>
                                {{mb_strtoupper(__('Detail'))}}
                            </strong>
                        </a>
                        @endcheckAccess
                        @checkAccess('platform.records.edit', $customer->user_id)
                        <a class="btn gap-2 card-edit-button card-button" href="{{route('platform.customer.edit', $customer->id)}}">
                            <x-orchid-icon path='pencil-square'></x-orchid-icon>
                            <strong>
                                {{mb_strtoupper(__('Edit'))}}
                            </strong>
                        </a>
                        @endcheckAccess
                        @checkAccess('platform.records.remove', $customer->user_id)
                        <a class="btn gap-2 btn-danger card-delete-button" record-id={{$customer->id}}>
                            <x-orchid-icon path='trash'></x-orchid-icon>
                            <strong>
                                {{__('DELETE')}}
                            </strong>
                        </a>
                        @endcheckAccess
                        <a class="btn card-call-button gap-2" href="tel:{{$customer->contactS->phone}}">
                            <x-orchid-icon path='telephone'></x-orchid-icon>
                            <strong>
                                {{$customer->contactS->phone}}
                            </strong>
                        </a>
                    </div>
                </div>
            </div>
            
            </div>

            @if($loop->iteration % 2 === 0 || $loop->last)
                </div>
                @if(!$loop->last)
                <div class="row px-3">
                @endif
            @endif
        @endforeach
        @else
        <div class="col-md-12 col-sm-12 text-center" style="height: 60px">
            <br>
            <span>{{__('No Registered Customer Records')}}</span>
            <br>
        </div>
        @endif
    </div>
</div>

<script>
    function loadScript(src, callback) {
        var s,
            r,
            t;
        r = false;
        s = document.createElement('script');
        s.type = 'text/javascript';
        s.src = src;
        s.onload = s.onreadystatechange = function() {
            //console.log( this.readyState ); //uncomment this line to see which ready states are called.
            if (!r && (!this.readyState || this.readyState == 'complete')) {
                r = true;
                callback();
            }
        };
        t = document.getElementsByTagName('script')[0];
        t.parentNode.insertBefore(s, t);
    }

    loadScript('https://code.jquery.com/jquery-3.7.0.js', function(){
        $('.card-delete-button').on('click', function(e){
                recordId = $(this).attr('record-id');
                deleteCustomer(recordId);
        })
        function deleteCustomer(recordId){
            try {
                if (confirm('Bu alıcı müşteri kaydını silmek istediğinize emin misiniz?')){
                    $.get(
                        "/admin/ajax/delete-record", 
                        {recordId: recordId},
                        function(result) {
                            if (result.status == true) {
                                window.location.reload();
                            }else{
                                console.log(result);
                            }
                        }
                    )
                }
            } catch (error) {
                console.log(error);
            }
        }
    })

</script>
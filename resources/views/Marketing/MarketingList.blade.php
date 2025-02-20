@php
  use App\Models\Setting;
  $jsonData = json_decode(Setting::first()->config, true)
@endphp
<div class="card main-card" style="@if($marketings->count() == 0) border: 1px solid rgb(110, 126, 199)@endif">
    <div class="row px-3">
        @if($marketings->count() > 0)
        @foreach($marketings as $marketing)
            @if($loop->iteration <= 2)
                <div class="col-md-6 col-sm-12 mt-3">
            @else
                <div class="col-md-6 col-sm-12 mt-3">
            @endif

            <div class="card consultant-card mb-3">
                <div class="row g-0">
                    <div class="col-md-4 col-sm-12">
                        @if ($marketing->portfolioS->attachment()->count() > 0)
                        <img src="{{$marketing->portfolioS->attachment()->first()->url}}" class="img-fluid card-image-marketing" alt="Profile Picture">
                        @else
                        <img src="{{asset('images/house.png')}}" class="img-fluid card-image-marketing" alt="Profile Picture">
                        @endif
                    </div>
                    <div class="col-md-8 col-sm-12">
                        <div class="card-body pt-3 pb-0">
                            @if($marketing->user_id)
                            <p class="card-title card-content"><span class="card-sub-title">{{__('Consultant')}}:</span> {{$marketing->presenter()->fullName}}</p>
                            @endif
                            <p class="card-title card-content"><span class="card-sub-title">{{__('Portfolio')}}:</span> {{$marketing->portfolioS->title}}</p>
                            <p class="card-title card-content"><span class="card-sub-title">{{__('Activity Type')}}:</span> {{$jsonData['activity_types'][$marketing->activity_type]}}</p>
                        </div>
                        {{-- <div class="row">
                            <div class="col text-end">
                                <p class="text-muted">
                                <x-orchid-icon path='calendar' style="font-size: .7rem; margin-top: -0.2rem;"></x-orchid-icon>
                                    {{changeDateFormat($marketing->record_date, 1)}}</p>
                            </div>
                        </div> --}}
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-1 pl-2 d-flex gap-3 justify-content-center">
                        @checkAccess('platform.records.detail', $marketing->user_id)
                        <a class="btn gap-2 card-detail-button card-button" 
                        href="{{route('platform.marketing.detail', $marketing)}}"
                        >
                            <x-orchid-icon path='eye'></x-orchid-icon>
                            <strong>
                                {{mb_strtoupper(__('Detail'))}}
                            </strong>
                        </a>
                        @endcheckAccess
                        @checkAccess('platform.records.edit', $marketing->user_id)
                        <a class="btn gap-2 card-edit-button card-button" href="{{route('platform.marketing.edit', $marketing->id)}}">
                            <x-orchid-icon path='pencil-square'></x-orchid-icon>
                            <strong>
                                {{mb_strtoupper(__('Edit'))}}
                            </strong>
                        </a>
                        @endcheckAccess
                        @checkAccess('platform.records.remove', $marketing->user_id)
                        <a class="btn gap-2 btn-danger card-delete-button" record-id={{$marketing->id}}>
                            <x-orchid-icon path='trash'></x-orchid-icon>
                            <strong>
                                {{__('DELETE')}}
                            </strong>
                        </a>
                        @endcheckAccess
                        <a class="btn card-go-button gap-2" href="{{$marketing->link}}">
                            <x-orchid-icon path='arrow-up-right-square'></x-orchid-icon>
                            <strong>
                                {{__('Go to Activity')}}
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
            <span>{{__('No Registered Marketing Records')}}</span>
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
                deleteMarketing(recordId);
        })
        function deleteMarketing(recordId){
            try {
                if (confirm('Bu pazarlama kaydını silmek istediğinize emin misiniz?')){
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
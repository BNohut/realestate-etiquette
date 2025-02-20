<div class="card main-card" style="@if($sales->count() == 0) border: 1px solid rgb(110, 126, 199)@endif">
    <div class="row px-3">
        @if($sales->count() > 0)
        @foreach($sales as $sale)
            @if($loop->iteration <= 2)
                <div class="col-md-6 col-sm-12 mt-3">
            @else
                <div class="col-md-6 col-sm-12 mt-3">
            @endif

            <div class="card consultant-card mb-3">
                <div class="row g-0">
                    <div class="col-md-4 col-sm-12">
                        @if ($sale->portfolioS->attachment()->count() > 0)
                        <img src="{{$sale->portfolioS->attachment()->first()->url}}" class="img-fluid card-image-sale" alt="Profile Picture">
                        @else
                        <img src="{{asset('images/house.png')}}" class="img-fluid card-image-sale" alt="Profile Picture">
                        @endif
                    </div>
                    <div class="col-md-8 col-sm-12">
                        <div class="card-body pt-3 pb-0">
                            @if($sale->contact_id)
                            <p class="card-title card-content"><span class="card-sub-title">{{__('Contact')}}:</span> {{$sale->contactS->name}}</p>
                            @endif
                            @if($sale->user_id)
                            <p class="card-title card-content"><span class="card-sub-title">{{__('Consultant')}}:</span> {{$sale->presenter()->fullName}}</p>
                            @endif
                            <p class="card-title card-content"><span class="card-sub-title">{{__('Portfolio')}}:</span> {{$sale->portfolioS->title}}</p>
                            <p class="card-title card-content"><span class="card-sub-title">{{__('Taken Deposit')}}:</span> {{number_format($sale->prepayment, 0, ',', '.')}} ₺</p>
                            <p class="card-title card-content"><span class="card-sub-title">{{__('Total Sale Price')}}:</span> {{number_format($sale->sales_price, 0, ',', '.')}} ₺</p>
                        </div>
                        {{-- <div class="row">
                            <div class="col text-end">
                                <p class="text-muted">
                                <x-orchid-icon path='calendar' style="font-size: .7rem; margin-top: -0.2rem;"></x-orchid-icon>
                                    {{changeDateFormat($sale->record_date, 1)}}</p>
                            </div>
                        </div> --}}
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-2 pl-2 d-flex gap-3 justify-content-center">
                        @checkAccess('platform.records.detail', $sale->user_id)
                        <a class="btn gap-2 card-detail-button" 
                        href="{{route('platform.sale.detail', $sale)}}"
                        >
                            <x-orchid-icon path='eye'></x-orchid-icon>
                            <strong>
                                {{mb_strtoupper(__('Detail'))}}
                            </strong>
                        </a>
                        @endcheckAccess
                        @checkAccess('platform.records.edit', $sale->user_id)
                        <a class="btn gap-2 card-edit-button" href="{{route('platform.sale.edit', $sale->id)}}">
                            <x-orchid-icon path='pencil-square'></x-orchid-icon>
                            <strong>
                                {{mb_strtoupper(__('Edit'))}}
                            </strong>
                        </a>
                        @endcheckAccess
                        @checkAccess('platform.records.remove', $sale->user_id)
                        <a class="btn gap-2 btn-danger card-delete-button" record-id={{$sale->id}}>
                            <x-orchid-icon path='trash'></x-orchid-icon>
                            <strong>
                                {{__('DELETE')}}
                            </strong>
                        </a>
                        @endcheckAccess
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
            <span>{{__('No Registered Sale Closing Records')}}</span>
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
                deleteSale(recordId);
        })
        function deleteSale(recordId){
            try {
                if (confirm('Bu yer gösterme kaydını silmek istediğinize emin misiniz?')){
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
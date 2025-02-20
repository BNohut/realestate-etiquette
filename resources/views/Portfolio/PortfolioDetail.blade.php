<link rel='stylesheet' href={{asset('css/lc_lightbox.css')}} />
<link rel='stylesheet' href={{asset('css/minimal.css')}} />

@php
    use App\Models\Setting;

    $jsonData = json_decode(Setting::first()->config, true);
    $groupList = $jsonData['portfolio_groups'];
    $newList = [];
    $variationList = [];
    foreach ($groupList as $key => $value) {
        $newList[$key] = $key;
    }
    $variationList = $jsonData['portfolio_groups'][$portfolio->portfolio_group];
@endphp

<div class="card main-card">
    <div class="row">
        <div class="col-md-4 col-sm-12 text-center">
            <div class="row">
                <div class="col">
                    @if($portfolio->attachment()->get()->count() > 0)
                    <img class="image-card picture" src="{{$portfolio->attachment()->get()[0]->url}}" id="image" alt="Porfolio First Image" width="305px" height="320px">
                    @else
                    <img class="image-card picture" src="{{asset('images/house.png')}}" id="image" alt="Avatar" width="200px" height="220px">
                    @endif
                </div>
            </div>
            <div class="row m-4">
                    @foreach($portfolio->attachment as $index => $attachment)
                    <div class="col-md-3 col-sm-12 mt-2 text-center">
                        <a href="{{$attachment->url}}" class='rests'>
                            <img src="{{$attachment->url}}" class="rest-of-images {{$loop->iteration}}" alt="Photo">
                        </a>
                    </div>
                    @endforeach
            </div>
        </div>
        <div class="col-md-8 col-sm-12 mt-md-4">
            <h5 class="title text-center text-md-start">{{$portfolio->title}}</h5>
            <h6 class="mt-3 text-center text-md-start">
                <x-orchid-icon path="geo-alt" style="color:blueviolet"></x-orchid-icon>
                {{findProvinceName($portfolio->province_id)}} / 
                {{findStateName($portfolio->state_id)}} / 
                {{$portfolio->neighborhood}}
                @if($portfolio->street)
                 - {{$portfolio->street}} {{__('Street')}}
                @endif
            </h6>
            @if($portfolio->link)
                <a href="{{$portfolio->link}}" class="btn card-join-button card-button justify-content-center " style="width: 30%">{{__('Go To Portfolio')}}</a>
            @endif
            <table class="table mt-3">
                    <tbody class="text-white">
                        <tr>
                            <td class="card-sub-title">{{__('Contact')}}</td>
                            <td>{{$portfolio->contactS->name}}</td>
                            <td class="card-sub-title">{{__('Consultant')}}</td>
                            <td>{{$portfolio->userS->name . " " . $portfolio->userS->last_name}}</td>
                        </tr>
                        <tr>
                            <td class="card-sub-title">{{__('Gross Square Meters')}}</td>
                            <td>{{$portfolio->square_total}}</td>
                            <td class="card-sub-title">{{__('Net Square Meters')}}</td>
                            <td>{{$portfolio->square_net}}</td>
                        </tr>
                        <tr>
                            <td class="card-sub-title">{{__('Portfolio Type')}}</td>
                            <td>{{$jsonData['portfolio_types'][$portfolio->portfolio_type]}}</td>
                            <td class="card-sub-title">{{__('Portfolio Group')}}</td>
                            <td>{{$newList[$portfolio->portfolio_group]}}</td>
                        </tr>
                        <tr>
                            <td class="card-sub-title">{{__('Portfolio Variation')}}</td>
                            <td>{{$variationList[$portfolio->portfolio_variation]}}</td>
                            <td class="card-sub-title">{{__('Portfolio Resource')}}</td>
                            <td>{{$jsonData['portfolio_resources'][$portfolio->portfolio_resource]}}</td>
                        </tr>
                        <tr>
                            <td class="card-sub-title">{{__('Ada No')}}</td>
                            <td>{{$portfolio->ada_no}}</td>
                            <td class="card-sub-title">{{__('Parsel No')}}</td>
                            <td>{{$portfolio->parsel_no}}</td>
                        </tr>
                        <tr>
                            <td class="card-sub-title">{{__('Dış Kapı No')}}</td>
                            <td>{{$portfolio->building_no ? $portfolio->building_no : "-"}}</td>
                            <td class="card-sub-title">{{__('İç Kapı No')}}</td>
                            <td>{{$portfolio->apartment_no ? $portfolio->apartment_no : "-"}}</td>
                        </tr>
                        <tr>
                            <td class="card-sub-title">{{__('List Price')}}</td>
                            <td>{{number_format($portfolio->list_price, 0, ',', '.')}} ₺</td>
                            <td class="card-sub-title">{{__('Minimum Price')}}</td>
                            <td>{{number_format($portfolio->minimum_price, 0, ',', '.')}} ₺</td>
                        </tr>
                        <tr>
                            <td class="card-sub-title">{{__('Sell Price')}}</td>
                            <td>{{$portfolio->sale_price ? number_format($portfolio->sale_price, 0, ',', '.') . " ₺" : "-"}}</td>
                            <td class="card-sub-title">{{__('Contract Date')}}</td>
                            <td>{{changeDateFormat($portfolio->contract_date, 1)}}</td>
                        </tr>
                        <tr>
                            <td class="card-sub-title">{{__('Deed Status')}}</td>
                            <td>{{$jsonData['deed_statuses'][$portfolio->deed_status]}}</td>
                        </tr>
                    </tbody>
                </table>
        </div>
    </div>
</div>
<div class="row justify-content-spacebetween">
    <div class="col-md-6 col-sm-12 m-0 p-3">
        <div class="card main-card">
            <div class="card-title mx-3 my-3">
                <h5>{{mb_strtoupper(__('Description'),'UTF-8')}}</h5>
            </div>
            <div class="card-body mt-0 description">
                {!! $description !!}
            </div>
        </div>
    </div>
    <div class="col-md-6 col-sm-12 m-0 pr-0">
        @include('Portfolio/PortfolioLocation')
    </div>
</div>

<style>
    #map{
        border-radius: 1rem;
        width: 520px;
        height: 400px;
    }
</style>

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

    loadScript("{{asset('/js/lc_lightbox.lite.js')}}", function(){
        var obj = lc_lightbox('div a.rests');
    });
</script>
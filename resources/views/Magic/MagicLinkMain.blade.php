<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel='stylesheet' href={{asset('css/lc_lightbox.css')}} />
<link rel='stylesheet' href={{asset('css/minimal.css')}} />
<link href="{{ asset('images/favicon.ico') }}" id="favicon" rel="icon">


<style>
    body{
        align-items: center !important;
        justify-content: center;
        font-family: 'Poppins', sans-serif;
    }

    body::before {
        content: "";
        background: url('../images/signup.png') no-repeat center fixed;
        background-size: cover;
        height: 100%;
        width: 100vw;
        margin: 0;
        position: fixed;
        z-index: -1;   
    }

    .logo {
        width: 230;
        margin-bottom: 1.2rem;
    }

    .navbar-row {
        align-items: center;
    }
    .social-media-buttons {
        text-decoration: none;
        color: #fff;
    }
    h1 {
        font-size: 3rem;
    }
    .main-card {
        background-color: #ffffffa4;
        border-radius: 1rem;
        border: 2px solid #fff;
        margin-top: 3rem;
    }

    .client-record-card {
        border:none;
        background-color: rgba(255, 255, 255, 0.779);
        border-radius: 1.1rem;
        box-shadow: 2px 2px rgba(0, 0, 0, 0.2);
        transition: transform 0.2s;
        min-height: 117px;
    }
    .client-record-card:hover {
        box-shadow: 3px 3px #8e9aef59;
        transition: transform 0.2s;
    }
    .card-image {
        border-radius: 1rem 0 0 1rem;
    }
    .card-content {
        color: #000;
        margin-bottom: 3px;
    }
    /* If the screen size is 1200px wide or more, set the font-size to 80px */
    @media (min-width: 1200px) {
        .card-content, .activity-button {
            font-size: .9rem;
        }
        .card-content span {
            font-size: 1rem;
        }
        #map {
            border: 1px solid #fff;
            border-radius: 1rem;
            box-shadow: 2px 2px rgba(0, 0, 0, 0.2);
            height: 160px !important;
            width: 370px !important;
            margin: 0 !important;
        }
        .first-image {
            height: 200px;
            width: 250px;
            border-radius: 1rem 0 0 1rem;
            border: 1px solid #fff;
            box-shadow: 2px 2px rgba(0, 0, 0, 0.2);
        }
        .rest-of-images {
            border: 1px solid #fff;
            width: 100px;
            height: 98px;
            box-shadow: 2px 2px rgba(0, 0, 0, 0.2);
        }
        .even-image {
            border-radius: 0 0 1rem 0;
        }
        .odd-image {
            border-radius: 0 1rem 0 0;
        }
        .rest-column{
            max-height: 204px;
            overflow:hidden;
        }
        .content {
            max-height: 100%;
            overflow-y:scroll;
        }
        .content::-webkit-scrollbar {
            display: none;
        }
        .detail-button{
            position: absolute;
            top:90;
            left:320;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 55%;
        }
    }
    /* If the screen size is smaller than 1200px, set the font-size to 80px */
    @media (max-width: 1199.98px) {
        .card-content, .activity-button {
            font-size: .7rem;
        }
        .card-content span {
            font-size: .75rem;
        }
        #map {
            border: 1px solid #fff;
            border-radius: 1rem;
            box-shadow: 2px 2px rgba(0, 0, 0, 0.2);
            height: 150px !important;
            width: 330px !important;
            margin: 0 !important;
        }
        .first-image {
            height: 203px;
            width: 330px;
            border-radius: 1rem 1rem 1rem 1rem;
            border: 1px solid #fff;
        }
        .rest-of-images {
            border: 1px solid #fff;
            width: 100px;
            height: 100px;
            box-shadow: 2px 2px rgba(0, 0, 0, 0.2);
        }
        .even-image {
            border-radius: 1rem 1rem;
        }
        .odd-image {
            border-radius: 1rem 1rem;
        }
        .rest-column{
            max-width: 330px;
            overflow-x:auto;
            white-space: nowrap;
            margin-top: 1rem !important;
        }
        .detail-button {
            position: absolute;
            top:60;
            left:250;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 48%;
        }
    }
    .card-label {
        color:rgb(74, 13, 204);
        font-weight: bold;
    }
    .footer {
        position: fixed;
        bottom: 0;
        left: 48%;
    }
    .card-header {
        background-color: transparent;
        border: none;
        padding: 0;
    }

    span.badge {
        min-width: 125px;
        background-color: #0a27e5b3;
        color: #fff;
        border-radius: 0 1rem 0 1rem;
        font-weight: bold;
    }
    .detail-button{
        color: #0a27e5b3;
        border-radius: .8rem !important;
        font-size: .8rem;
        font-weight: 1000;
        text-decoration: none;
        padding: 4px 10px;
    }

    .detail-button:hover{
        background-color: #0a27e5b3;
        color: #fff;
    }
    .card-col {
        padding-top: 1rem;
        padding-bottom: .7rem;
    }
    h5, p {
        margin-bottom: 0;
    }
    .activity-button {
        color: rgb(47, 135, 82);;
        border: none;
        font-weight: 1000;
        text-decoration: none;
        padding-left: 0;
        padding-top: 0;
    }

    .colored-title {
        color:rgb(60, 38, 107)
    }
    .input-card{
        box-shadow: 0px 2px rgba(0, 0, 0, 0.2);
        border-radius: .7rem;
        white-space: nowrap;
        overflow: hidden;
    }
    a {
        text-decoration: none;
    }

</style>
@php
    use App\Models\Setting;
    $jsonData = json_decode(Setting::first()->config, true);
    $groupList = $jsonData['portfolio_groups'];
    $newList = [];
    $variationList = [];
    foreach ($groupList as $key => $value) {
        $newList[$key] = $key;
    }
    $variationList = $groupList[$portfolio->portfolio_group];
@endphp
{{-- NAV BAR --}}
<div class="row justify-content-center mx-1">
    <div class="col-12 col-sm-9 navbar-col">
        <div class="row mt-3 navbar-row">
            <div class="col-12 col-md-6 col-sm-12 text-center text-md-start">
                <img class="logo" src="{{asset('images/etiquette_white.png')}}" alt="White Logo">
            </div>
            <div class="col-12 col-md-6 col-sm-12 text-white">
                <div class="row navbar-row">
                    <div class="col-12 col-md-9 col-sm-12 d-flex gap-3 gap-md-5 justify-content-center justify-content-md-end align-items-center"> <!-- col-12 ve text-center ekledik -->
                        <h6>{{__('Home')}}</h6>
                        <h6>İletişim</h6>
                    </div>
                    <div class="col-12 col-md-3 col-sm-12 d-flex gap-3 gap-md-4 justify-content-center justify-content-md-end" style="margin-top: -5px;"> <!-- col-12 ve text-center ekledik -->
                        <a href="" class="social-media-buttons"><x-orchid-icon path="linkedin"></x-orchid-icon></a>
                        <a href="" class="social-media-buttons"><x-orchid-icon path="instagram"></x-orchid-icon></a>
                        <a href="" class="social-media-buttons"><x-orchid-icon path="twitter"></x-orchid-icon></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<br>

{{-- Title (Name) --}}
<div class="row justify-content-center mt-4 mx-2">
    <div class="col-12 text-center text-white">
        <h1 class="d-none d-sm-block">{{$portfolio->userS->officeS->name}} | {{$portfolio->userS->getFullNameAttribute()}}</h1>
        <h4 class="d-block d-sm-none">{{$portfolio->userS->officeS->name}} | {{$portfolio->userS->getFullNameAttribute()}}</h4>
        <h3 class="d-none d-sm-block">{{__('Our Work On Your Portfolio')}}</h3>
        <h6 class="d-block d-sm-none">{{__('Our Work On Your Portfolio')}}</h6>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-md-8 col-sm-12">
            <div class="card main-card">
                <div class="row mx-1 mt-3">
                    <div class="card-header col-12 text-start ps-4">
                        <div class="row me-4 align-items-center">
                            <div class="col text-start">
                                <h3 class="d-none d-sm-block colored-title">{{__('Records')}}</h3>
                                <h4 class="d-block d-sm-none colored-title">{{__('Records')}}</h4>
                            </div>
                            <div class="col text-end">
                                <h6 class="mb-0 colored-title">{{$records->filter(function ($record) {
                                    return $record->recordTypeS->name != 'Portföy';
                                })->count()}} {{__('Record')}}</h6>
                            </div>
                        </div>
                    </div>
                    @foreach($records as $record)
                    @if($record->recordTypeS->name != "Portföy")
                        <div class="col-md-6 col-sm-12 card-col">
                            <div class="card client-record-card">
                                <div class="row">
                                    <div class="col-4">
                                        @if ($record->contact_id)
                                            @if($record->contactS->attachment()->count() > 0)
                                                <img class="card-image img-fluid" src="{{$record->contactS->attachment()->first()?->url}}" alt="">
                                            @else
                                                <img class="card-image img-fluid" src="{{asset('../images/user.png')}}" alt="">
                                            @endif
                                        @elseif($record->portfolio_id)
                                            @if($record->recordTypeS->name == "Pazarlama")
                                                @if($record->userS->attachment()->count() > 0)
                                                    <img class="card-image img-fluid" src="{{$record->userS->attachment()->first()->url}}" alt="">
                                                @else
                                                    <img class="card-image img-fluid" src="{{asset('../images/house.png')}}" alt="">
                                                @endif
                                            @else
                                                @if($record->portfolioS->contactS->attachment()->count() > 0)
                                                    <img class="card-image img-fluid" src="{{$record->portfolioS->contactS->attachment()->first()->url}}" alt="">
                                                @else
                                                    <img class="card-image img-fluid" src="{{asset('../images/house.png')}}" alt="">
                                                @endif
                                            @endif
                                        @endif
                                    </div>
                                    <div class="col-8 ps-0">
                                        <div class="row justify-content-between">
                                            <div class="col pt-1">
                                                <h5 class="card-title card-content"><span class="card-label"><x-orchid-icon path="calendar" style="margin-top: -.2rem !important;"></x-orchid-icon></span> {{changeDateFormat($record->record_date, 1)}}</h5>
                                            </div>
                                            <div class="col">
                                                <div class="card-header text-end">
                                                    <h5 class="card-content"><span class="badge">{{$record->recordTypeS->name == "Tapu Satış-Kiralama İşlemleri" ? 'Satış-Kiralama' : $record->recordTypeS->name}}</span></h5>
                                                </div>
                                            </div>
                                        </div>
                                
                                        @if($record->contact_id)
                                        <p class="card-title card-content"><span class="card-label">{{__('Contact')}}:</span> {{$record->contactS->name}}</p>
                                        @elseif($record->portfolio_id)
                                        <p class="card-title card-content"><span class="card-label">{{__('Contact')}}:</span> {{$record->portfolioS->userS->getFullNameAttribute()}}</p>
                                        @endif
                                        @if($record->recordTypeS->name == "Çağrı")
                                        <p class="card-title card-content"><span class="card-label">{{__('Severity Level')}}:</span> {{$jsonData['record_levels'][$record->record_level]}}</p>
                                        <p class="card-title card-content"><span class="card-label">{{__('Offer Price')}}:</span> {{number_format($record->price_offer, 0, ",", ".")}} ₺</p>
                                        @elseif($record->recordTypeS->name == "Yer Gösterme")
                                        <p class="card-title card-content"><span class="card-label">{{__('Severity Level')}}:</span> {{$jsonData['record_levels'][$record->record_level]}}</p>
                                        <p class="card-title card-content"><span class="card-label">{{__('Offer Price')}}:</span> {{number_format($record->price_offer, 0, ",", ".")}} ₺</p>
                                        @elseif($record->recordTypeS->name == "Pazarlama")
                                        <p class="card-title card-content"><span class="card-label">{{__('Activity Type')}}:</span> {{$jsonData['activity_types'][$record->activity_type]}}</p>
                                        <p class="card-title card-content"><a class="btn btn-sm activity-button" href="{{$record->link}}">{{__('Go to Activity')}}</a></p>
                                        @elseif($record->recordTypeS->name == "Tapu Satış-Kiralama İşlemleri")
                                        <p class="card-title card-content"><span class="card-label">{{__('Sale Price')}}:</span> {{number_format($record->sales_price, 0, ",", ".")}} ₺</p>
                                        <p class="card-title card-content"><span class="card-label">{{__('Operation Type')}}:</span> {{$record->activity_type}}</p>
                                        @elseif($record->recordTypeS->name == "Satış Kapama")
                                        <p class="card-title card-content"><span class="card-label">{{__('Taken Deposit')}}:</span> {{number_format($record->prepayment, 0, ",", ".")}}</p>
                                        <p class="card-title card-content"><span class="card-label">{{__('Sale Price')}}:</span> {{number_format($record->sales_price, 0, ",", ".")}} ₺</p>
                                        @endif
                                        <a href="{{route('magic.detail', 
                                                ['portfolio' => $portfolio->slug, 
                                                'recordId' => $record->id])}}" class="detail-button">
                                            <x-orchid-icon path="eye" ></x-orchid-icon>
                                            {{__('Detail')}}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-12">
            <div class="card main-card">
                <div class="row mt-4 ms-3">
                    <div class="card-header col-12 text-start ps-2">
                        <div class="row me-4 align-items-center">
                            <div class="col text-start">
                                <h3 class="d-none d-sm-block mb-0 colored-title">{{__('Your Portfolio')}}</h3>
                                <h4 class="d-block d-sm-none mb-0 colored-title">{{__('Your Portfolio')}}</h4>
                            </div>
                            <div class="col text-end">
                                <h6 class="mb-0 colored-title"><x-orchid-icon path="calendar" style="margin-top: -.2rem;"></x-orchid-icon>{{changeDateFormat($portfolio->created_at,1)}}</h6>
                            </div>
                        </div>
                    </div>
                </div>
                @if($portfolio->attachment()->count() > 0)
                    <div class="row justify-content-center mt-3 mx-3" id="image-container">
                        <div class="col-12 col-md-8 col-sm-12 p-0">
                            <a href="{{$portfolio->attachment->first()->url}}"><img src="{{$portfolio->attachment->first()->url}}" class="first-image mt-1" alt="Portfolio's First Image"></a>
                        </div>
                        <div class="col-12 col-md-4 col-sm-12 text-center px-0 mt-1 rest-column">
                            <div class="content">
                                @foreach ($portfolio->attachment()->get() as $image)
                                    @if (!$loop->first) 
                                        <a href="{{$image->url}}"><img src="{{$image->url}}" class="rest-of-images mb-1 {{$loop->iteration % 2 == 0 ? 'odd-image' : 'even-image'}}" alt="Portfolio Images"></a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
                <div class="row ms-2 mt-3">
                    <div class="card-body py-0 ps-2">
                        @include('Portfolio/PortfolioLocation', ['portfolio' => $portfolio])
                    </div>
                    <div class="card-header col-12 text-center ps-2 pt-2 pb-1">
                        <h6 class="colored-title"><x-orchid-icon path="geo-alt" style="margin-top: -.2rem;"></x-orchid-icon>{{$portfolio->presenter()->fullAddress()}}</h6>
                    </div>
                </div>
                    <div class="row justify-content-center space-between my-2 mx-1">
                        <div class="col-md-6 col-sm-12 text-start mt-2">
                            <div class="card-header colored-title">
                                {{__('Portfolio Type')}}
                            </div>
                            <div class="card input-card text-center">
                                <div class="card-body py-2">
                                    {{$jsonData['portfolio_types'][$portfolio->portfolio_type]}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12 text-start mt-2">
                            <div class="card-header colored-title">
                                {{__('Portfolio Group')}}
                            </div>
                            <div class="card input-card text-center">
                                <div class="card-body py-2">
                                    {{$newList[$portfolio->portfolio_group]}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12 text-start mt-2">
                            <div class="card-header text-start colored-title">
                                {{__('Portfolio Variation')}}
                            </div>
                            <div class="card input-card text-center">
                                <div class="card-body py-2">
                                    {{$variationList[$portfolio->portfolio_variation]}} 
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12 text-start mt-2">
                            <div class="card-header text-start colored-title">
                                {{__('Contact Resource')}}
                            </div>
                            <div class="card input-card text-center">
                                <div class="card-body py-2">
                                    {{$jsonData['contact_resources'][$portfolio->portfolio_resource]}}
                                </div>
                            </div>
                        </div>
                        @if($portfolio->square_total)
                        <div class="col-md-6 col-sm-12 text-start mt-2">
                            <div class="card-header text-start colored-title">
                                {{__('Gross Square Meters')}}
                            </div>
                            <div class="card input-card text-center">
                                <div class="card-body py-2">
                                    {{$portfolio->square_total}}
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($portfolio->square_net)
                        <div class="col-md-6 col-sm-12 text-start mt-2">
                            <div class="card-header text-start colored-title">
                                {{__('Net Square Meters')}}
                            </div>
                            <div class="card input-card text-center">
                                <div class="card-body py-2">
                                    {{$portfolio->square_net}}
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($portfolio->ada_no)
                        <div class="col-md-6 col-sm-12 text-start mt-2">
                            <div class="card-header text-start colored-title">
                                Ada No
                            </div>
                            <div class="card input-card text-center">
                                <div class="card-body py-2">
                                    {{$portfolio->ada_no}}
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($portfolio->parsel_no)
                        <div class="col-md-6 col-sm-12 text-start mt-2">
                            <div class="card-header text-start colored-title">
                                Parsel No
                            </div>
                            <div class="card input-card text-center">
                                <div class="card-body py-2">
                                    {{$portfolio->parsel_no}}
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($portfolio->building_no)
                        <div class="col-md-6 col-sm-12 text-start mt-2">
                            <div class="card-header text-start colored-title">
                                Dış Kapı No
                            </div>
                            <div class="card input-card text-center">
                                <div class="card-body py-2">
                                    {{$portfolio->building_no}}
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($portfolio->apartment_no)
                        <div class="col-md-6 col-sm-12 text-start mt-2">
                            <div class="card-header text-start colored-title">
                                İç Kapı No
                            </div>
                            <div class="card input-card text-center">
                                <div class="card-body py-2">
                                    {{$portfolio->apartment_no}}
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="col-md-6 col-sm-12 text-start mt-2">
                            <div class="card-header text-start colored-title">
                                {{__('List Price')}}
                            </div>
                            <div class="card input-card text-center">
                                <div class="card-body py-2">
                                    {{number_format($portfolio->list_price, 0, ",", ".")}} ₺
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12 text-start mt-2">
                            <div class="card-header text-start colored-title">
                                {{__('Minimum Price')}}
                            </div>
                            <div class="card input-card text-center mt-2">
                                <div class="card-body py-2">
                                    {{number_format($portfolio->minimum_price, 0, ",", ".")}} ₺
                                </div>
                            </div>
                        </div>
                        @if($portfolio->sales_price)
                        <div class="col-md-6 col-sm-12 text-start mt-2">
                            <div class="card-header text-start colored-title">
                                {{__('Sale Price')}}
                            </div>
                            <div class="card input-card text-center">
                                <div class="card-body py-2">
                                    {{number_format($portfolio->sales_price, 0, ",", ".")}} ₺
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($portfolio->deed_status)
                        <div class="col-md-6 col-sm-12 text-start mt-2">
                            <div class="card-header text-start colored-title">
                                {{__('Deed Status')}}
                            </div>
                            <div class="card input-card text-center">
                                <div class="card-body py-2">
                                    {{$jsonData['deed_statuses'][$portfolio->deed_status]}}
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($portfolio->contract_date)
                        <div class="col-md-6 col-sm-12 text-start mt-2">
                            <div class="card-header text-start colored-title">
                                {{__('Contract Date')}}
                            </div>
                            <div class="card input-card text-center">
                                <div class="card-body py-2">
                                    {{changeDateFormat($portfolio->contract_date, 1)}}
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
            </div>
        </div>
    </div>
</div>
{{-- FOOTER --}}
<div class="row footer">
    <div class="col-12 mt-4 text-white text-center">
        <span class="footer">© {{date('Y')}} KodGaraj</span>
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

    loadScript("{{asset('/js/lc_lightbox.lite.js')}}", function(){
        var obj = lc_lightbox('#image-container a');
    });
</script>
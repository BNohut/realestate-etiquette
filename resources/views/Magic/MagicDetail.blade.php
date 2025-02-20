<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="{{ asset('images/favicon.ico') }}" id="favicon" rel="icon">

<style>
    body{
        align-items: center !important;
        justify-content: center;
        font-family: 'Poppins', sans-serif;
        overflow:-moz-hidden-unscrollable;
    }

    body::before {
        content: "";
        background: url('/images/signup.png') no-repeat center fixed;
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
    .input-card {
        box-shadow: 0px 2px rgba(0, 0, 0, 0.2);
        border-radius: .7rem;
        white-space: nowrap;
        overflow: hidden;
    }
    .client-record-card {
        border:none;
        background-color: rgba(255, 255, 255, 0.779);
        border-radius: 1rem;
        box-shadow: 2px 2px rgba(0, 0, 0, 0.2);
    }
    .card-header {
        background: none;
        color: #3015df;
        border: none;
        padding-bottom: 0;
        padding-top:0;
        font-weight: bold;
    }

    .card-body {
        white-space: nowrap;
        overflow: auto;
        text-overflow: inherit;
    }

    #map {
        border: 1px solid #fff;
        border-radius: .5rem;
        box-shadow: 2px 2px rgba(0, 0, 0, 0.2);
        height: 300px !important;
        width: 440px !important;
        margin: 0 !important;
    }

    .rest-of-images {
        border: 1px solid #fff;
        width: 130px;
        height: 100px;
        border-radius: .5rem;
        box-shadow: 2px 2px rgba(0, 0, 0, 0.2);

    }
    .btn-success {
        border-radius: .5rem;
        background-color: #ffffff;
        color: #198754;
        border: 1px solid #198754;
        box-shadow: 2px 2px rgba(0, 0, 0, 0.2);
    }
    .btn-success:hover {
        border-radius: .5rem;
        background-color: #198754;
        color: #fff;
    }
     @media (min-width: 1200px) {
        .footer {
            position: fixed;
            bottom: 0;
            left: 47%;
        }
     }
    @media (max-width: 1199.98px) {
        .footer {
            position: fixed;
            bottom: 0;
            left: 34%;
        }
    }

</style>
@php
    use App\Models\Setting;
    $jsonData = json_decode(Setting::first()->config, true);
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
        <h1 class="d-none d-sm-block">{{$record->recordTypeS->name}} Kaydı</h1>
        <h4 class="d-block d-sm-none">{{$record->recordTypeS->name}} Kaydı</h4>
    </div>
</div>

{{-- MAIN CARD --}}
<div class="container">
    <div class="row justify-content-center mt-1">
        <div class="col-md-6 col-sm-12 text-center ">
            <div class="card main-card">
                <div class="row m-3 mb-1 align-content-center">
                    <div class="card-header col-6 text-start ps-4">
                        {{__('Record Detail')}}
                    </div>
                    <div class="card-header col-6 text-md-end pe-3">
                        <x-orchid-icon path="calendar" style="margin-top: -.2rem;"></x-orchid-icon>
                        {{changeDateFormat($record->record_date, 1)}}
                    </div>
                </div>
                <div class="row justify-content-center space-between m-3">
                    {{-- If it is a call record --}}
                    @if($record->recordTypeS->name == "Çağrı" || $record->recordTypeS->name == "Yer Gösterme")
                        <div class="col-md-6 col-sm-12 p-4 pt-0 text-start">
                            <div class="card-header">
                                {{__('Contact')}}
                            </div>
                            <div class="card input-card text-center">
                                <div class="card-body py-2">
                                    {{$record->contactS->name}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12 p-4 pt-0 text-start">
                            <div class="card-header">
                                {{__('Resource')}}
                            </div>
                            <div class="card input-card text-center">
                                <div class="card-body py-2">
                                    {{$jsonData['contact_resources'][$record->contact_resource]}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12 p-4 pt-0 text-start">
                            <div class="card-header text-start">
                                {{__('Budget')}}
                            </div>
                            <div class="card input-card text-center">
                                <div class="card-body py-2">
                                    {{number_format($record->budget, 0, ',', '.')}} ₺
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12 p-4 pt-0 text-start">
                            <div class="card-header text-start">
                                {{__('Offer Price')}}
                            </div>
                            <div class="card input-card text-center">
                                <div class="card-body py-2">
                                    {{number_format($record->price_offer, 0, ',', '.')}} ₺
                                </div>
                            </div>
                        </div>
                    @endif
                    {{-- If it is a marketing record --}}
                    @if($record->recordTypeS->name == "Pazarlama")
                        <div class="col-md-6 col-sm-12 p-4 pt-0 text-start">
                            <div class="card-header">
                                {{__('Consultant')}}
                            </div>
                            <div class="card input-card text-center">
                                <div class="card-body py-2">
                                    {{$record->presenter()->fullName}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12 p-4 pt-0 text-start">
                            <div class="card-header">
                                {{__('Activity Type')}}
                            </div>
                            <div class="card input-card text-center">
                                <div class="card-body py-2">
                                    {{$jsonData['activity_types'][$record->activity_type]}}
                                </div>
                            </div>
                        </div>
                        <div class="col-12 p-4 pt-0 text-start d-grid">
                            <a class="btn btn-success block" href="{{$record->link}}">{{__('Go to Activity')}}</a>
                        </div>
                    @endif
                    {{-- If it is a deed process record --}}
                    @if($record->recordTypeS->name == "Tapu Satış-Kiralama İşlemleri")
                        <div class="col-md-6 col-sm-12 p-4 pt-0 text-start">
                            <div class="card-header">
                                {{__('Consultant')}}
                            </div>
                            <div class="card input-card text-center">
                                <div class="card-body py-2">
                                    {{$record->presenter()->fullName}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12 p-4 pt-0 text-start">
                            <div class="card-header">
                                {{__('Sale Price')}}
                            </div>
                            <div class="card input-card text-center">
                                <div class="card-body py-2">
                                    {{number_format($record->sales_price, 0, ',', '.')}} ₺
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12 p-4 pt-0 text-start">
                            <div class="card-header">
                                {{__('Operation Type')}}
                            </div>
                            <div class="card input-card text-center">
                                <div class="card-body py-2">
                                    {{$record->activity_type}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12 p-4 pt-0 text-start">
                            <div class="card-header">
                                {{__('Operation Date')}}
                            </div>
                            <div class="card input-card text-center">
                                <div class="card-body py-2">
                                    {{changeDateFormat($record->record_date, 1)}}
                                </div>
                            </div>
                        </div>
                    @endif
                    {{-- If it is a sale closing record --}}
                    @if($record->recordTypeS->name == "Satış Kapama")
                        <div class="col-md-6 col-sm-12 p-4 pt-0 text-start">
                            <div class="card-header">
                                {{__('Consultant')}}
                            </div>
                            <div class="card input-card text-center">
                                <div class="card-body py-2">
                                    {{$record->presenter()->fullName}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12 p-4 pt-0 text-start">
                            <div class="card-header">
                                {{__('Taken Deposit')}}
                            </div>
                            <div class="card input-card text-center">
                                <div class="card-body py-2">
                                    {{number_format($record->prepayment, 0, ',', '.')}} ₺
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12 p-4 pt-0 text-start">
                            <div class="card-header">
                                {{__('Total Sale Price')}}
                            </div>
                            <div class="card input-card text-center">
                                <div class="card-body py-2">
                                    {{number_format($record->sales_price, 0, ',', '.')}} ₺
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
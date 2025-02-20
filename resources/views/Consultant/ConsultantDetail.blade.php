@php
    function createButton(string $buttonName, string $icon, string $class, string $action, int $consultantId){
        $mergedClass = $class . " btn card-button";
        $button = Orchid\Screen\Actions\Button::make(__($buttonName))
                                            ->icon($icon)
                                            ->class($mergedClass)
                                            ->action(route('platform.consultant', [
                                                'method' => $action,
                                                'consultant_id'=>$consultantId,
                                            ]));
        return $button;
    }
@endphp

<div class="card main-card">
    <div class="row px-5 py-4">
        <div class="col-sm-12 col-md-2 text-center">
            <div class="row">
                <div class="col p-0 pt-1">
                    @if($consultant->attachment()->get()->count() > 0)
                    <img class="consultant-image" src="{{$consultant->attachment()->get()[0]->url}}" id="image" alt="Avatar">
                    @else
                    <img class="consultant-image" src="{{asset('images/user.png')}}" id="image" alt="Avatar">
                    @endif
                </div>
            </div>
            @if($consultant->json)
            <div class="row">
                <div class="col d-flex gap-4 justify-content-center mt-3">
                    @foreach ($consultant->json as $socialMedia)
                        @if ($socialMedia["Platform"] == "Instagram")
                            <a href="{{$socialMedia['Link']}}"><x-orchid-icon path='instagram' class="social-icons"></x-orchid-icon></a>
                        @elseif ($socialMedia["Platform"] == "Facebook")
                            <a href="{{$socialMedia['Link']}}"><x-orchid-icon path='facebook' class="social-icons"></x-orchid-icon></a>
                        @elseif ($socialMedia["Platform"] == "Twitter") 
                            <a href="{{$socialMedia['Link']}}"><x-orchid-icon path='twitter' class="social-icons"></x-orchid-icon></a>
                        @elseif ($socialMedia["Platform"] == "Linked-In")
                            <a href="{{$socialMedia['Link']}}"><x-orchid-icon path='linkedin' class="social-icons"></x-orchid-icon></a>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        <div class="col-sm-12 col-md-4 text-center">
            <h5 class="pt-3">{{mb_strtoupper($consultant->name) . " " . mb_strtoupper($consultant->last_name)}}</h5>
            <h6 class="card-sub-title pt-1">{{mb_strtoupper(__('Contact Information Of Consultant'), 'UTF-8')}}</h6>
            @if ($consultant->province_id)
            <p class="pt-1">
                <x-orchid-icon class="contact-icons me-2" path="geo-alt"></x-orchid-icon>
                {{findStateName($consultant->state_id) . ", " . findProvinceName($consultant->province_id)}}
            </p>
            @endif
            @if ($consultant->phone)
            <p><x-orchid-icon class="contact-icons me-2" path="telephone"></x-orchid-icon>{{$consultant->phone}}</p>
            @endif
            <p><x-orchid-icon class="contact-icons me-2" path="envelope-paper"></x-orchid-icon>{{$consultant->email}}</p>
            @if(!authUserInRole(['super-yonetici', 'yonetici', 'ofis-yoneticisi']) && auth()->user()->id != $consultant->id && $consultant->visibility)
                @if(doesUserFollow(auth()->user()->id, $consultant->id) == "follow")
                    {{createButton('Follow', 'person-plus', 'card-follow-button', 'follow', $consultant->id)}}
                @elseif(doesUserFollow(auth()->user()->id, $consultant->id) == "waiting")
                    {{createButton('Waiting', 'clock', 'card-waiting-button', 'cancelRequest', $consultant->id)}}
                @elseif(doesUserFollow(auth()->user()->id, $consultant->id) == "approve")
                    {{createButton('Approve', 'person-fill-check', 'card-approve-button', 'approve', $consultant->id)}}
                    {{createButton('Reject', 'person-fill-x', 'card-reject-button', 'reject', $consultant->id)}}
                @elseif(doesUserFollow(auth()->user()->id, $consultant->id) == "follow-back")
                    {{createButton('Follow Back', 'person-plus', 'card-follow-back-button', 'followBack', $consultant->id)}}
                @elseif(doesUserFollow(auth()->user()->id, $consultant->id) == "unfollow")
                    {{createButton('Unfollow', 'person-dash', 'card-unfollow-button', 'unfollow', $consultant->id)}}
                @elseif(doesUserFollow(auth()->user()->id, $consultant->id) == "approve-back")
                    {{createButton('Approve', 'person-fill-check', 'card-approve-button', 'approve', $consultant->id)}}
                    {{createButton('Reject', 'person-fill-x', 'card-reject-button', 'reject', $consultant->id)}}
                @elseif(doesUserFollow(auth()->user()->id, $consultant->id) == "waiting-back")
                    {{createButton('Waiting', 'clock', 'card-waiting-back-button', 'cancelRequest', $consultant->id)}}
                @endif
            @else
                @if(!authUserInRole(['super-yonetici', 'yonetici', 'ofis-yoneticisi']) && !$consultant->visibility)
                    {{createButton('', 'incognito', 'disabled', '', $consultant->id)}}
                @endif
            @endif

        </div>
        <div class="col-sm-12 col-md-6">
            <div class="card consultant-card">
                <div class="m-0 text-center" style="padding: 0.7rem 1rem;">
                    <span class="card-sub-title">{{mb_strtoupper(__('About Me'))}}</span>
                </div>
                <div class="card-body pt-0 pe-0">
                    <div class="form-floating about-me">
                        {!!$aboutMe!!}
                    </div>
                </div>
            </div>
           
        </div>      
    </div>

    <div class="row pt-3">
        <div class="col px-5">
            <h4 class="card-sub-title">{{mb_strtoupper(__('My Portfolios')) . " (" .$portfolios->count(). ")"}}</h4>
        </div>
    </div>

    @php
        use App\Models\Setting;
        $jsonData = json_decode(Setting::first()->config, true);
        $groupList = $jsonData['portfolio_groups'];
        $newList = [];
        $variationList = [];
        foreach ($groupList as $key => $value) {
            $newList[$key] = $key;
        }
    @endphp

    <div class="row" style="padding: 0 2.4rem;">
        @foreach($portfolios as $portfolio)
            @if($loop->iteration <= 2)
                <div class="col-md-6 col-sm-12 mt-3">
            @else
                <div class="col-md-6 col-sm-12 mt-3">
            @endif
            <div class="card consultant-card mb-3">
                <div class="row">
                    <div class="col-md-4 col-sm-12 pt-2 text-center">
                        @if($portfolio->attachment()->first())
                        <img src="{{$portfolio->attachment()->first()->url}}" class="img-fluid card-image-consultant-portfolio" alt="Profile Picture">
                        @else
                        <img src="{{asset('images/house.png')}}" class="img-fluid card-image-consultant-portfolio" alt="Profile Picture">
                        @endif
                    </div>
                    <div class="col-md-8 col-sm-12 text-start px-0 pt-2">
                        <div class="card-body">
                            <h6 class="card-title">
                                @if ($portfolio->province_id)
                                {{findProvinceName($portfolio->province_id) . " / " . findStateName($portfolio->state_id) . " / " . $portfolio->neighborhood}}
                                @endif
                            </h6>
                            <p>
                                {{$jsonData['portfolio_types'][$portfolio->portfolio_type]}}
                                /
                                {{$newList[$portfolio->portfolio_group]}}
                                /
                                {{$jsonData['portfolio_groups'][$portfolio->portfolio_group][$portfolio->portfolio_variation]}}
                            </p>
                            <div class="col text-start">
                                <span>{{number_format($portfolio->list_price, 2, ',', '.')}} ₺</span>
                            </div>
                            <br>
                            <p class="m-0">
                                {{$jsonData['portfolio_groups'][$portfolio->portfolio_group][$portfolio->portfolio_variation]}}
                                -
                                {{$portfolio->square_net}} m<sup>2</sup>
                                -
                                {{$jsonData['deed_statuses'][$portfolio->deed_status]}}
                            </p>

                        </div>
                    </div>
                </div>
            </div>
            
            </div>

            @if($loop->iteration % 2 === 0 || $loop->last)
                </div>
                @if(!$loop->last)
                <div class="row" style="padding: 0 2.4rem;">
                @endif
            @endif
        @endforeach
    </div>
</div>
<script>
    var waitingBackButtons = document.querySelectorAll('.card-waiting-back-button');
    waitingBackButtons.forEach(button => {
        button.addEventListener('mouseover', function(){
            let spanEl = this.querySelector('span');
            spanEl.innerText = "İptal Et";
        });
        button.addEventListener('mouseout', function(){
            let spanEl = this.querySelector('span');
            spanEl.innerText = "Beklemede";
        });
    });
    
    var waitingButtons = document.querySelectorAll('.card-waiting-button');
    waitingButtons.forEach(button => {
        button.addEventListener('mouseover', function(){
            let spanEl = this.querySelector('span');
            spanEl.innerText = "İptal Et";
        });
        button.addEventListener('mouseout', function(){
            let spanEl = this.querySelector('span');
            spanEl.innerText = "Beklemede";
        });
    });
</script>

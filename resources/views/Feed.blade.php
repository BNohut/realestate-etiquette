@php
    use App\Models\Record;
    function createButton(string $icon, string $action, int $consultantId, bool $disabled){
        $button = Orchid\Screen\Actions\Button::make()
                                            ->icon($icon)
                                            ->class("btn btn-follow text-white")
                                            ->disabled($disabled)
                                            ->action(route('platform.consultant', [
                                                'method' => $action,
                                                'consultant_id'=>$consultantId,
                                            ]));
        return $button;
    }
    function createHeartButton(int $recordId){
        $userLikedBefore = userLikedBefore($recordId);
        $icon = $userLikedBefore ? 'heart-fill' : 'heart';
        $class = $userLikedBefore ? 'btn heart-filled text-danger like-button' : 'btn heart-not-filled text-white like-button';
        $action = $userLikedBefore ? 'unlike' : 'like';
        $button = Orchid\Screen\Actions\Button::make()
                                            ->icon($icon)
                                            ->class($class)
                                            ->action(route('platform.main', [
                                                'method' => $action,
                                                'recordId'=>$recordId,
                                            ]));
        return $button;
    }

    function userLikedBefore(int $recordId){
        $record = Record::find($recordId);
        if($record->likes){
            $userId = auth()->user()->id;
            $recordLikes = json_decode($record->likes, true);
            return in_array($userId, $recordLikes);
        }
        return false;
    }
    use App\Models\User;
    use App\Models\Follower;
                            
    $consultantLinkArray = [];
    if(authUserInRole(['super-yonetici', 'yonetici'])){
        $users = User::join('role_users', 'users.id', '=', 'role_users.user_id')
            ->join('roles', 'role_users.role_id', '=', 'roles.id')
            ->whereIn('roles.slug', ['ofis-danismani','bireysel-danisman'])
            ->select('users.*')->get();
        foreach($users as $user){
            $consultantLinkArray[$user->id] = Orchid\Screen\Actions\Link::make($user->name . " " . $user->last_name)
                ->route('platform.main', ['consultant' => $user->id] + Request::query())
                ->class('consultant-dropdown-item text-center');
        };
    }
    if(authUserInRole('ofis-yoneticisi')){
        $users = User::join('role_users', 'users.id', '=', 'role_users.user_id')
                ->join('roles', 'role_users.role_id', '=', 'roles.id')
                ->where('roles.slug', 'ofis-danismani')
                ->where('users.office_id', auth()->user()->office_id)->select('users.*')->get();
        foreach($users as $user){
            $consultantLinkArray[$user->id] = Orchid\Screen\Actions\Link::make($user->name . " " . $user->last_name)
                ->route('platform.main', ['consultant' => $user->id] + Request::query())
                ->class('consultant-dropdown-item text-center');
        };
    }
    if(authUserInRole('ofis-danismani')){
        $officeConsultantIds = User::join('role_users', 'users.id', '=', 'role_users.user_id')
                ->join('roles', 'role_users.role_id', '=', 'roles.id')
                ->where('roles.slug', 'ofis-danismani')
                ->where('office_id', auth()->user()->office_id)
                ->where('users.id', '!=', auth()->user()->id)
                ->select('users.id')
                ->get()
                ->pluck('id')
                ->toArray();
        $followingsIds = Follower::where('from', auth()->user()->id)
            ->whereNotNull('approved')
            ->groupBy('followers.from', 'followers.to')
            ->pluck('to')
            ->toArray();
        $users = User::whereIn('id', array_unique(array_merge($followingsIds, $officeConsultantIds)))
            ->orderBy('name', 'asc')
            ->get();
        foreach($users as $user){
            $consultantLinkArray[$user->id] = Orchid\Screen\Actions\Link::make($user->name . " " . $user->last_name)
                ->route('platform.main', ['consultant' => $user->id] + Request::query())
                ->class('consultant-dropdown-item text-center');
        };
    }
    if(authUserInRole('bireysel-danisman')){
        foreach($followingsIds as $followingId){
            $currentUser = User::find($followingId);
            $consultantLinkArray[$currentUser->id] = Orchid\Screen\Actions\Link::make($currentUser->name . " " . $currentUser->last_name)
                                ->route('platform.main', ['consultant' => $followingId] + Request::query())
                                ->class('consultant-dropdown-item text-center');
    }
    }

    $filterTitle = request()->consultant ? User::find(request()->consultant)->getFullNameAttribute() : __('Select Consultant')
@endphp

@include('AlgoliaSearch')

<div class="row">
    <div class="col-md-8 col-sm-12 left-col">
        @if(count($records) > 0)
            @foreach ($records as $record)
                <div class="card main-card text-center">
                    <div class="row align-items-center justify-content-center">
                        <div class="col px-5 py-3">
                            <div class="row align-items-center">
                                <div class="col-3">
                                    @if($record->userS->attachment()->count() > 0)
                                        <img src="{{$record->userS->attachment()->first()->url}}" class="rounded-circle" alt="Avatar" width="50" height="50">
                                    @else
                                        <img src="{{asset('images/user.png')}}" class="rounded-circle" alt="Avatar" width="50" height="50">
                                    @endif
                                </div>
                                <div class="col-9">
                                    <p class="mb-0 ps-2 text-start"><strong>{{$record->getConsultantNameAttribute()}}</strong></p>
                                </div>
                            </div>
                        </div>
                        <div class="col px-5 py-3">
                            <div class="row align-items-center">
                                <div class="col-12 pe-0 justify-content-end d-flex gap-3">
                                    <h5 class="mb-0"><p class="badge type mb-0">{{$record->recordTypeS->name}}</p></h5>
                                    {{createHeartButton($record->id)}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col px-5 text-start feed-message-field">
                            <p>{{$record->feed_message}}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            @if($record->portfolio_id)
                                @if($record->portfolioS->attachment()->count() > 0)
                                    <div class="card image-card" style="background: url({{$record->portfolioS->attachment()->first()->url}})">
                                @else
                                    <div class="card image-card" style="background: url('../images/realestate.jpg');">
                                @endif
                            @else
                                <div class="card image-card" style="background: url('../images/realestate.jpg');">
                            @endif
                                <div class="row h-100 align-items-top">
                                    <div class="col text-start mt-3 ms-4">
                                        @if($record->portfolio_id)
                                        <h4><p class="badge title">{{$record->portfolioS->title}}</p></h4>
                                        @else
                                        <h4><p class="badge title">{{findProvinceName($record->province_id) . " / " . findStateName($record->state_id)}}</p></h4>
                                        @endif
                                    </div>
                                </div>
                                <div class="row h-100 align-items-center">
                                    <div class="col text-end me-4 mt-3">
                                        @if($record->portfolio_id)
                                        <h3><p class="badge title">{{
                                        __('List Price') . ": " . 
                                        number_format($record->portfolioS->list_price, 0, ",", ".")}} ₺</p></h3>
                                        @elseif($record->sales_price)
                                        <h3><p class="badge title">{{
                                        __('Sale Price') . ": " . 
                                        number_format($record->sales_price, 0, ",", ".")}} ₺</p></h3>
                                        @else
                                        <h3><p class="badge title">{{
                                        __('Budget') . ": " . 
                                        number_format($record->budget, 0, ",", ".")}} ₺</p></h3>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-between ms-4 me-3">
                        <div class="col-3 text-start">
                            @if($record->portfolio_id)
                            <p class="text-muted">{{$record->portfolioS->neighborhood}}</p>
                            @else
                            <p class="text-muted">{{$record->neighborhood}}</p>
                            @endif
                        </div>
                        <div class="col-4 justify-content-end d-flex gap-4 me-3">
                            <p class="text-muted">{{changeDateFormat($record->record_date)}}</p>
                            @if($record->likes != null)
                                <p class="text-muted">{{__('Likes')}}: {{count(json_decode($record->likes,true))}}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="card main-card text-center">
                <div class="row align-items-center">
                    <div class="col p-3 pt-4">
                        <p>{{__('No action yet')}}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <div class="col-md-4 col-sm-12 right-col">
        <div class="card main-card">
            <div class="row mt-3">
                <div class="col-7 text-start ms-4">
                    <h5>Çağrılar</h5>
                </div>
                <div class="col-3 text-end me-2 pe-1">
                    <h5><p class="badge title">{{$counts['call']}}</p></h5>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-7 text-start ms-4">
                    <h5>FSBO'lar</h5>
                </div>
                <div class="col-3 text-end me-2 pe-1">
                    <h5><p class="badge title">{{$counts['fsbo']}}</p></h5>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-7 text-start ms-4">
                    <h5>Yer Göstermeler</h5>
                </div>
                <div class="col-3 text-end me-2 pe-1">
                    <h5><p class="badge title">{{$counts['viewing']}}</p></h5>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-7 text-start ms-4">
                    <h5>Alıcı Müşteriler</h5>
                </div>
                <div class="col-3 text-end me-2 pe-1">
                    <h5><p class="badge title">{{$counts['customer']}}</p></h5>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-7 text-start ms-4">
                    <h5>Pazarlama</h5>
                </div>
                <div class="col-3 text-end me-2 pe-1">
                    <h5><p class="badge title">{{$counts['marketing']}}</p></h5>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-7 text-start ms-4">
                    <h5>Satış Kapama</h5>
                </div>
                <div class="col-3 text-end me-2 pe-1">
                    <h5><p class="badge title">{{$counts['sale']}}</p></h5>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-7 text-start ms-4">
                    <h5>Tapu Satış-Kiralama</h5>
                </div>
                <div class="col-3 text-end me-2 pe-1">
                    <h5><p class="badge title">{{$counts['deed']}}</p></h5>
                </div>
            </div>
            @if(authUserInRole(['super-yonetici', 'yonetici', 'ofis-yoneticisi', 'ofis-asistani']) || count($followingsIds) > 0)
                <div class="row justify-content-end">
                    <div class="col-10">
                        <div class="row">
                            <div class="col pe-0">
                                {!!
                                    Orchid\Screen\Actions\DropDown::make($filterTitle)
                                        ->style(request()->consultant ? "display: inline-block; color:#8b10dc;" : "display: inline-block; color:#a99f9f;")
                                        ->icon('filter')
                                        ->id('consultant-filter')
                                        ->list($consultantLinkArray)
                                        ->hidden($consultantLinkArray == [])
                                        !!}
                            </div>
                            @if(request()->consultant)
                                <div class="col">
                                    <a href="{{route('platform.main', collect(Request::query())->except('consultant')->toArray())}}"
                                        class="text-danger ps-0"><x-orchid-icon path="x" id="clear-filter-button"></x-orchid-icon></a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
            <div class="row justify-content-center mb-2">
                <div class="col text-center">
                    <div class="btn-group gap-2" role="group" aria-label="Basic radio toggle button group">
                        <input type="radio" class="btn-check" name="btnradio" id="btn-radio1" autocomplete="off">
                        <a href="{{route('platform.main', ['counts' => 'today'] + Request::query())}}"><label for="btn-radio1" id="counts-day">{{__('Last 24h')}}</label></a>
                        | 
                        <input type="radio" class="btn-check" name="btnradio" id="btn-radio2" autocomplete="off">
                        <a href="{{route('platform.main', ['counts' => 'week'] + Request::query())}}"><label for="btn-radio2" id="counts-week">{{__('Last Week')}}</label></a>
                        | 
                        <input type="radio" class="btn-check" name="btnradio" id="btn-radio3" autocomplete="off">
                        <a href="{{route('platform.main', ['counts' => 'month'] + Request::query())}}"><label for="btn-radio3" id="counts-month">{{__('Last Month')}}</label></a>
                        | 
                        <input type="radio" class="btn-check" name="btnradio" id="btn-radio4" autocomplete="off">
                        <a href="{{route('platform.main', collect(Request::query())->except('counts')->toArray())}}"><label for="btn-radio4" id="counts-all">{{__('All')}}</label></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="main-card">
            <div class="row">
                <div class="col mt-3 text-center">
                    <h5>EN ÇOK ÇALIŞANLAR</h5>
                </div>
            </div>
            @foreach ($champions as $user)
                <div class="row mt-4 ms-1 me-4 pe-3 pb-4 align-items-center">
                    <div class="col-3 text-center">
                        @if($user->attachment()->count() > 0)
                        <img src="{{$user->attachment()->first()->url}}" class="rounded-circle" alt="Cinque Terre" width="40" height="40">
                        @else
                        <img src="{{asset('images/user.png')}}" class="rounded-circle" alt="Cinque Terre" width="40" height="40">
                        @endif
                    </div>
                    <div class="col-7 text-start"><a class="champ-name" href="{{$user->visibility ? route('platform.consultant.detail', $user->id) : route('platform.consultant')}}">{{$user->name . " " . $user->last_name}}</a></div>
                    <div class="col-2 text-center">
                        @if(authUserInRole(['ofis-danismani', 'bireysel-danisman']) && auth()->user()->id != $user->id && $user->visibility)
                            @if(doesUserFollow(auth()->user()->id, $user->id) == "follow")
                                {{createButton('plus', 'follow', $user->id, false)}}
                            @elseif(doesUserFollow(auth()->user()->id, $user->id) == "waiting")
                                {{createButton('clock', '', 0, true)}}
                            @elseif(doesUserFollow(auth()->user()->id, $user->id) == "approve")
                                {{createButton('clock', '', 0, true)}}
                            @elseif(doesUserFollow(auth()->user()->id, $user->id) == "follow-back")
                                {{createButton('plus', 'followBack', $user->id, false)}}
                            @elseif(doesUserFollow(auth()->user()->id, $user->id) == "unfollow")
                                {{createButton('check', 'unfollow', $user->id, true)}}
                            @elseif(doesUserFollow(auth()->user()->id, $user->id) == "approve-back")
                                {{createButton('clock', '', 0, true)}}
                            @elseif(doesUserFollow(auth()->user()->id, $user->id) == "waiting-back")
                                {{createButton('clock', '', 0, true)}}
                            @endif
                        @else
                            @if(authUserInRole(['ofis-danismani', 'bireysel-danisman']) && !$user->visibility)
                                {{createButton('dash', '', 0, true)}}
                            @endif
                        @endif
                    </div>
                </div>
            @endforeach
            <div class="row justify-content-center">
                <div class="col text-center mb-2">
                    <div class="btn-group gap-2" role="group" aria-label="Basic radio toggle button group">
                        <input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off">
                        <a href="{{route('platform.main', ['champions' => 'today'] + Request::query())}}"><label for="btnradio1" id="champs-day">{{__('Last 24h')}}</label></a>
                        | 
                        <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off">
                        <a href="{{route('platform.main', ['champions' => 'week'] + Request::query())}}"><label for="btnradio2" id="champs-week">{{__('Last Week')}}</label></a>
                        | 
                        <input type="radio" class="btn-check" name="btnradio" id="btnradio3" autocomplete="off">
                        <a href="{{route('platform.main', collect(Request::query())->except('champions')->toArray())}}"><label for="btnradio3" id="champs-all">{{__('All')}}</label></a>
                    </div>
                </div>
            </div>
        </div>
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
        $(document).ready(function(){
            // Scroll yapılan element
            var leftCol = document.querySelector('.left-col');

            // COUNTS FILTER
            var countsFilter = @json($countsFilter);
            var countsDayLabelEl = $('#counts-day');
            var countsWeekLabelEl = $('#counts-week');
            var countsMonthLabelEl = $('#counts-month');
            var countsAllLabelEl = $('#counts-all');
            if(countsFilter == "countsToday"){
                countsDayLabelEl.addClass("counts-checked");
            }else if(countsFilter == "countsWeek"){
                countsWeekLabelEl.addClass("counts-checked");
            }else if(countsFilter == "countsMonth"){
                countsMonthLabelEl.addClass("counts-checked");
            }else{
                countsAllLabelEl.addClass("counts-checked");
            };
            // CHAMPIONS FILTER
            var champsionsFilter = @json($championsFilter);
            var champsDayLabelEl = $('#champs-day');
            var champsWeekLabelEl = $('#champs-week');
            var champsAllLabelEl = $('#champs-all');

            if(champsionsFilter == "champsToday"){
                champsDayLabelEl.addClass("champs-checked");
            }else if(champsionsFilter == "champsWeek"){
                champsWeekLabelEl.addClass("champs-checked");
            }else{
                champsAllLabelEl.addClass("champs-checked");
            };

            champsDayLabelEl.click(function(){
                // champsWeekLabelEl.removeClass("champs-checked");
                $(this).addClass("champs-checked");
            });
            champsWeekLabelEl.click(function(){
                // champsDayLabelEl.removeClass("champs-checked");
                $(this).addClass("champs-checked");
            });
            var scrollPosition = sessionStorage.getItem('scrollPosition');
            if (scrollPosition) {
                leftCol.scrollTop = scrollPosition;
                sessionStorage.removeItem('scrollPosition');
            }

            // Like butonu tıklandığında kaydırma pozisyonunu kaydet
            var likeButtons = document.querySelectorAll('.like-button');
            likeButtons.forEach(button => {
                button.addEventListener('click', function(){
                    sessionStorage.setItem('scrollPosition', leftCol.scrollTop);
                });
            });
        });
    })
    
</script>
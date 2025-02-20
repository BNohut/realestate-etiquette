@php
    function createButton(string $buttonName, string $icon, string $class, string $action, int $consultantId){
        $mergedClass = $class . " btn card-button pb-2";
        $button = Orchid\Screen\Actions\Button::make(__($buttonName))
                                            ->icon($icon)
                                            ->class($mergedClass)
                                            ->style('width: 100%; justify-content: center;')
                                            ->action(route('platform.consultant', [
                                                'method' => $action,
                                                'consultant_id'=>$consultantId,
                                            ]));
        return $button;
    }
@endphp

<div class="card main-card" style="@if($consultants->count() == 0) border: 1px solid rgb(110, 126, 199)@endif">
    <div class="row px-3">
        @if($consultants->count() > 0)
        @foreach($consultants as $consultant)
            @if($loop->iteration <= 3)
                <div class="col-md-4 col-sm-12 mt-3">
            @else
                <div class="col-md-4 col-sm-12 mt-3">
            @endif

            <div class="card consultant-card mb-3">
                <div class="row g-0 justify-content-center">
                    <div class="col-md-4 col-sm-12 text-center">
                        @if($consultant->attachment()->first())
                        <img src="{{$consultant->attachment()->first()->url}}" class="img-fluid card-image-consultant" alt="Profile Picture">
                        @else
                        <img src="{{asset('images/user.png')}}" class="img-fluid card-image-consultant" alt="Profile Picture">
                        @endif
                    </div>
                    <div class="col-md-8 col-sm-12 text-center">
                        <div class="card-body pb-0">
                            <h5 class="card-title">{{$consultant->name . " " . $consultant->last_name}}</h5>
                            <p class="card-text mb-1"><span class="card-sub-title">TEL: </span>{{$consultant->phone ? $consultant->phone : "-"}}</p>
                            @if(authUserInRole('super-yonetici') || $consultant->visibility)
                            <div class="row justify-content-center">
                                <div class="col-6 p-0 m-0 ms-2 mb-1">
                                        <a class="btn gap-2 card-detail-button justify-content-center" 
                                        href="{{route('platform.consultant.detail', $consultant)}}"
                                        >
                                            <x-orchid-icon path='eye'></x-orchid-icon>
                                            {{__('Detail')}}
                                        </a>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="row justify-content-center">
                                <div class="col-6 p-0 m-0 ms-2 mb-1">
                                    @checkAccess('platform.consultants.edit', $consultant->id)
                                    <a class="btn gap-2 card-edit-button justify-content-center " href="{{route('platform.consultant.edit', $consultant)}}">
                                        <x-orchid-icon path='pencil-square'></x-orchid-icon>
                                        {{__('Edit')}}
                                    </a>
                                    @endcheckAccess
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-6 p-0 m-0 ms-2">
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
                                            {{createButton('Gizli', 'incognito', 'disabled', '', $consultant->id)}}
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            </div>

            @if($loop->iteration % 3 === 0 || $loop->last)
                </div>
                @if(!$loop->last)
                <div class="row px-3">
                @endif
            @endif
        @endforeach
         @else
        <div class="col-md-12 col-sm-12 text-center" style="height: 60px">
            <br>
            <span>{{__('No Registered Consultants')}}</span>
            <br>
        </div>
        @endif
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
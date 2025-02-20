<div class="card main-card" style="@if($contacts->count() == 0) border: 1px solid rgb(110, 126, 199)@endif">
    <div class="row px-2 pt-2">
        @if ($contacts->count() > 0)
            @foreach($contacts as $contact)
            @if($loop->iteration <= 3)
                <div class="col-md-4 col-sm-12 mt-3">
            @else
                <div class="col-md-4 col-sm-12 mt-3">
            @endif

            <div class="card contact-card mb-3">
                <div class="row g-0">
                    <div class="col-md-4 col-sm-12 text-center pt-2 pt-md-0">
                        @if($contact->attachment()->first())
                        <img src="{{$contact->attachment()->first()->url}}" class="img-fluid card-image-contact" alt="Profile Picture">
                        @else
                        <img src="{{asset('images/user.png')}}" class="img-fluid card-image-contact" alt="Profile Picture">
                        @endif
                    </div>
                    <div class="col-md-8 col-sm-12 text-center">
                        <div class="card-body">
                            <h5 class="card-title">{{$contact->name}}</h5>
                            <p class="card-text"><span class="card-sub-title">TEL: </span>{{$contact->phone}}</p>
                            <div class="row">
                                <div class="col-6 px-2">
                                    @checkAccess('platform.contacts.detail', $contact->user_id)
                                    <a class="btn card-detail-button card-button justify-content-center" href="{{route('platform.contact.detail', $contact)}}">
                                        <span>
                                            <x-orchid-icon path='eye'></x-orchid-icon>
                                            {{mb_strtoupper(__('Detail'))}}
                                        </span>
                                    </a>
                                    @endcheckAccess
                                </div>
                                <div class="col-6 px-1">
                                     @checkAccess('platform.contacts.edit', $contact->user_id)
                                    <a class="btn card-edit-button justify-content-center" href="{{route('platform.contact.edit', $contact)}}">
                                        <span>
                                            <x-orchid-icon path='pencil-square'></x-orchid-icon>
                                            {{mb_strtoupper(__('Edit'))}}
                                        </span>
                                    </a>
                                    @endcheckAccess
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
                <div class="row px-2 pt-2">
                @endif
            @endif
            @endforeach
        @else
        <div class="col-md-12 col-sm-12 text-center" style="height: 60px">
            <br>
            <span>{{__('No Registered Contacts')}}</span>
            <br>
        </div>
        @endif
    </div>
</div>
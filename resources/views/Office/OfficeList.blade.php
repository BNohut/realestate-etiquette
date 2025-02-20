<div class="card main-card">
    <div class="row mb-4 mx-2">
        @if ($offices->count() == 0)
            <div class="col-12 text-center pt-3 pb-0">
                    {{__('There is no office yet')}}
            </div>
        @endif
        @foreach($offices as $office)
            <div class="col-md-6 col-sm-12 mt-4">
                <div class="card office-card">
                    <div class="row">
                        <div class="col-md-4 col-sm-12 text-center">
                            <img src="{{$office->attachment()->first()->url}}" class="img-fluid card-image-office" alt="Profile Picture">
                        </div>
                        <div class="col-md-8 col-sm-12 mt-3 ps-3">
                            <h4 class="office-name">{{$office->name}}</h4>
                            <div class="row">
                                <div class="col d-flex gap-2">
                                    <x-orchid-icon path="geo-alt" style="color: #8b10dc; margin-top:.3rem;"></x-orchid-icon> 
                                    <p class="full-address mb-2 me-4">
                                        {{$office->neighborhood}}
                                        @if($office->street)
                                          {{$office->street}} {{__('Street')}}
                                        @endif
                                        @if($office->building_no)
                                          No: {{$office->building_no}}
                                        @endif
                                        @if($office->apartment_no)
                                        / {{$office->apartment_no}}
                                        @endif
                                         {{findStateName($office->state_id)}} /
                                        {{findProvinceName($office->province_id)}}
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col d-flex gap-2">
                                    <x-orchid-icon path="telephone" style="color: #8b10dc; margin-top:.3rem;"></x-orchid-icon>
                                    <p class="mb-2">{{$office->phone}}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col d-flex gap-2 pt-0">
                                    <x-orchid-icon path="envelope-at" style="color: #8b10dc; margin-top:.3rem;"></x-orchid-icon>
                                    <p class="mb-2">{{$office->email}}</p>
                                </div>
                            </div>
                            @if ($office->website)
                            <div class="row">
                                <div class="col d-flex gap-2 pt-0">
                                    <x-orchid-icon path="globe-americas" style="color: #8b10dc; margin-top:.3rem;"></x-orchid-icon>
                                    <a href="{{$office->website}}"><p class="mb-2 text-white">{{__('Website')}}</p></a>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col pl-2 me-5 d-flex gap-3 justify-content-end">
                            @if(!authUserInRole(['super-yonetici', 'yonetici', 'ofis-yoneticisi', ['ofis-asistani']]))
                                @if (!auth()->user()->office_id)
                                    {!!
                                    Orchid\Screen\Actions\Button::make(__('Join'))
                                        ->icon('send-plus')
                                        ->class("btn gap-1 card-join-button card-button")
                                        ->action(route('platform.office', [
                                            'method' => 'join',
                                            'officeId'=>$office->id,
                                        ]));
                                    !!}
                                @endif
                                @if (auth()->user()->office_id && !auth()->user()->office_approved_at && auth()->user()->office_id == $office->id)
                                    {!!
                                    Orchid\Screen\Actions\Button::make(__('Cancel'))
                                        ->icon('clock')
                                        ->class("btn gap-1 card-clock-button card-button")
                                        ->action(route('platform.office', [
                                            'method' => 'cancelRequest',
                                            'officeId'=>$office->id,
                                        ]));
                                    !!}
                                @endif
                                @if (auth()->user()->office_id && auth()->user()->office_approved_at && auth()->user()->office_id == $office->id)
                                    {!!
                                    Orchid\Screen\Actions\Button::make(__('Leave'))
                                        ->icon('box-arrow-left')
                                        ->class("btn gap-1 card-leave-button card-button")
                                        ->action(route('platform.office', [
                                            'method' => 'leaveTheOffice',
                                            'officeId'=>$office->id,
                                        ]));
                                    !!}
                                @endif
                            @endif
                            @if (authUserInRole(['super-yonetici', 'yonetici']) || (authUserInRole('ofis-yoneticisi') && auth()->user()->id == $office->user_id))
                            <a class="btn gap-2 card-edit-button card-button" href="{{route('platform.office.edit', $office->id)}}">
                                <x-orchid-icon path='pencil-square'></x-orchid-icon>
                                <span>
                                    {{__('Edit')}}
                                </span>
                            </a>
                            @endif
                            @if (authUserInRole(['super-yonetici', 'yonetici']))
                            {!!
                            Orchid\Screen\Actions\Button::make(__('Delete'))
                                ->icon('trash')
                                ->class("btn gap-1 btn-danger card-delete-button")
                                ->confirm(__('Are you sure you want to delete this office?') . 
                                '<br><br>' . 
                                __('If you delete this office, all users in this office will be effected.') . 
                                '<br><br><strong>* </strong>' .
                                __('The manager and assistants of office will be soft deleted') . 
                                '<br><strong>* </strong>' .
                                __('Consultants role will be changed to individual consultant'))
                                ->action(route('platform.office', [
                                    'method' => 'remove',
                                    'officeId'=>$office->id,
                                ]));
                            !!}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
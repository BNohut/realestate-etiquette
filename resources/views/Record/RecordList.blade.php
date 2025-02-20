@php
    use App\Models\Setting;
    use App\Models\RecordType;
    $jsonData = json_decode(Setting::first()->config, true);
    $recordTypes = RecordType::all();
    $groups = $jsonData['portfolio_groups'];
    $groupList = [];
    $variationList = [];
    foreach ($groups as $key => $value) {
        $groupList[$key] = $key;
    }

    function findRecordTypeName($id, $recordTypes) {
        foreach($recordTypes as $recordType){
            if($recordType->id == $id){
                return $recordType->name;
            }
        }
    }

    function findRecordTypeRoutePreffix($id, $recordTypes) {
        $typeMapping = [
            'F.S.B.O.' => 'fsbo',
            'Çağrı' => 'call',
            'Yer Gösterme' => 'viewing',
            'Alıcı Müşteri' => 'customer',
            'Pazarlama' => 'marketing',
            'Tapu Satış-Kiralama İşlemleri' => 'deed',
            'Satış Kapama' => 'sale',
        ];

        foreach ($recordTypes as $recordType) {
            if ($recordType->id == $id && isset($typeMapping[$recordType->name])) {
                return $typeMapping[$recordType->name];
            }
        }
    }
@endphp
<div class="container">
    <div class="row">
        <div class="col">
            <div class="card main-card m-1" style="@if($records->count() == 0) border: 1px solid rgb(110, 126, 199)@endif">
                @if(count($records) > 0)
                @foreach ($records as $record)
                @if($record->recordTypeS->name != "Portföy")
                <div class="card record-card mt-3">
                    <div class="row">
                        <div class="col-2">
                            @if ($record->portfolio_id)
                                @if($record->portfolioS->attachment()->count() > 0)
                                    <img src="{{$record->portfolioS->attachment()->first()->url}}" class="img-fluid card-image-records" alt="Portfolio Image">
                                @else
                                    <img src="{{ asset('images/house.png') }}" class="img-fluid card-image-records" alt="Portfolio Image">
                                @endif
                            @else
                                @if($record->contactS->attachment()->count() > 0)
                                    <img src="{{$record->contactS->attachment()->first()->url}}" class="img-fluid card-image-records" alt="Contact Image">
                                @else
                                    <img src="{{ asset('images/user.png') }}" class="img-fluid card-image-records" alt="Contact Image">
                                @endif
                            @endif
                        </div>
                        <div class="col-10">
                            <div class="row">
                                <div class="col mt-4 d-flex justify-content-between align-items-center">
                                    @if($record->contact_id)
                                        <h6 class="card-title card-content mb-0">
                                             <span class="card-sub-title">{{__('Contact') . ": "}}</span>{{$record->contactS->name}}
                                        </h6>   
                                    @elseif($record->user_id)
                                        <h6 class="card-title card-content mb-0">
                                            <span class="card-sub-title">{{__('Consultant') . ": "}}</span>{{$record->presenter()->fullName}}
                                        </h6>
                                    @endif
                                    <h5 class="mb-0"><span class="badge bg-primary record-badge">{{$record->recordTypeS->name}}</span></h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    @if(
                                        findRecordTypeName($record->record_type_id, $recordTypes) == 'F.S.B.O.' || 
                                        findRecordTypeName($record->record_type_id, $recordTypes) == 'Alıcı Müşteri'
                                        )
                                        <h6 class="card-title card-content">
                                            <span class="card-sub-title">{{__('Portfolio Information') . ": "}}</span>
                                            {{$jsonData['portfolio_groups'][$record->portfolio_group][$record->portfolio_variation]}} - 
                                            {{findStateName($record->state_id)}} / {{findProvinceName($record->province_id)}} 
                                            
                                        </h6>
                                        @else
                                        <h6 class="card-title card-content">
                                            <span class="card-sub-title">{{__('Portfolio Information') . ": "}}</span>
                                            {{$jsonData['portfolio_groups'][$record->portfolioS->portfolio_group][$record->portfolioS->portfolio_variation]}} - 
                                            {{findStateName($record->portfolioS->state_id)}} / {{findProvinceName($record->portfolioS->province_id)}} 
                                        </h6>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    @php
                                        $typeName = findRecordTypeName($record->record_type_id, $recordTypes);
                                        $resultKey = null;
                                        $content = '';
                                        
                                        switch ($typeName) {
                                            case 'F.S.B.O.':
                                                $resultKey = 'fsbo_results';
                                                break;
                                            case 'Çağrı':
                                                $resultKey = 'interview_results';
                                                break;
                                            case 'Yer Gösterme':
                                                $resultKey = 'demonstration_results';
                                                break;
                                            case 'Alıcı Müşteri':
                                                $content = $record->property_features;
                                                break;
                                            case 'Pazarlama':
                                                $content = $jsonData['activity_types'][$record->activity_type];
                                                break;
                                            case 'Tapu Satış-Kiralama İşlemleri':
                                                $content = $record->activity_type;
                                                break;
                                            case 'Satış Kapama':
                                                $content = number_format($record->sales_price, 0, ",", ".") . ' ₺';
                                                break;
                                        }
                                    @endphp

                                    @if ($resultKey)
                                        <h6 class="card-title card-content">
                                            <span class="card-sub-title">{{__('Result')}}: </span>
                                            {{$jsonData[$resultKey][$record->record_result]}}
                                        </h6>
                                    @elseif ($content)
                                        <h6 class="card-title card-content">
                                            <span class="card-sub-title">
                                                @if (in_array($typeName, ['Alıcı Müşteri', 'Pazarlama', 'Tapu Satış-Kiralama İşlemleri']))
                                                    {{__('Activity Type')}}:
                                                @elseif ($typeName === 'Satış Kapama')
                                                    {{__('Sale Price')}}:
                                                @endif
                                            </span>
                                            {{$content}}
                                        </h6>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col d-flex justify-content-between">
                                    @php
                                        $typeName = findRecordTypeName($record->record_type_id, $recordTypes);
                                        $content = '';
                                        $title = '';
                                        $color = 'rgb(74, 13, 204)';

                                        switch ($typeName) {
                                            case 'F.S.B.O.':
                                            case 'Çağrı':
                                            case 'Yer Gösterme':
                                            case 'Alıcı Müşteri':
                                                $title = __('Resource');
                                                $content = $jsonData['contact_resources'][$record->contact_resource];
                                                break;
                                            case 'Pazarlama':
                                                $title = __('Portfolio');
                                                $content = $record->portfolioS->title;
                                                break;
                                            case 'Tapu Satış-Kiralama İşlemleri':
                                                $title = __('Sale Price');
                                                $content = number_format($record->sales_price, 0, ",", ".") . ' ₺';
                                                break;
                                            case 'Satış Kapama':
                                                $title = __('Taken Deposit');
                                                $content = number_format($record->prepayment, 0, ",", ".") . ' ₺';
                                                break;
                                        }
                                    @endphp
                                    @if ($content)
                                        <h6 class="card-title card-content">
                                            <span class="card-sub-title">{{$title}}: </span>
                                            {{$content}}
                                        </h6>
                                    @endif
                                </div>
                                <div class="row mt-4">
                                    <div class="col d-flex gap-2 justify-content-end">
                                        @checkAccess('platform.records.edit', $record->user_id)
                                        <a class="btn card-edit-button d-flex gap-1" href="{{route("platform." . findRecordTypeRoutePreffix($record->record_type_id, $recordTypes). ".edit", $record->id)}}">
                                            <x-orchid-icon path='pencil-square' style="font-size: .8rem;"></x-orchid-icon>
                                            {{mb_strtoupper(__('Edit'))}}
                                        </a>
                                        @endcheckAccess
                                        @checkAccess('platform.records.remove', $record->user_id)
                                        <a class="btn card-delete-button btn-danger d-flex gap-1" record-id={{$record->id}} style="background-color:rgb(183, 68, 68); color:#fff">
                                            <x-orchid-icon path='trash' style="font-size: .8rem; "></x-orchid-icon>
                                            {{__('DELETE')}}
                                        </a>
                                        @endcheckAccess
                                        @if(findRecordTypeName($record->record_type_id, $recordTypes) == "Pazarlama")
                                            <a class="btn card-go-button gap-2" href="{{$record->link}}">
                                                <x-orchid-icon path='arrow-up-right-square' style="font-size: .8rem; "></x-orchid-icon>
                                                <strong>
                                                    {{__('Go to Activity')}}
                                                </strong>
                                            </a>
                                        @else
                                            <a class="btn card-go-button gap-2" href="tel:{{$record->contactS->phone}}">
                                                <x-orchid-icon path='telephone' style="font-size: .8rem; "></x-orchid-icon>
                                                <strong>
                                                    {{$record->contactS->phone}}
                                                </strong>
                                            </a>
                                        @endif
                                        <p class="text-muted" style="margin-top: -0.2rem;">
                                            <x-orchid-icon path='calendar' style="font-size: .7rem; margin-top: -0.2rem;"></x-orchid-icon>
                                                {{changeDateFormat($record->record_date, 1)}}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                   </div>
                </div>
                @endif
                @endforeach
            @else
            <div class="col-md-12 col-sm-12 text-center" style="height: 60px">
                <br>
                <span>{{__('There is not any record')}}</span>
                <br>
            </div>
            @endif
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
        $('.delete').on('click', function(e){
                recordId = $(this).attr('record-id');
                deleteCall(recordId);
        })
        function deleteCall(recordId){
            try {
                if (confirm('Bu kaydı silmek istediğinize emin misiniz?')){
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
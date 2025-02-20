@php
    use App\Models\Record;
    use App\Models\RecordType;

    function findCountOfCallRecordsOfSpecificPortfolio($portfolioId){
        $callRecordTypeId = RecordType::where('name', 'Çağrı')->first()->id;
        return Record::where('portfolio_id', $portfolioId)->where('record_type_id', $callRecordTypeId)->get()->count();
    }
    function findCountOfViewingRecordsOfSpecificPortfolio($portfolioId){
        $viewingRecordTypeId = RecordType::where('name', 'Yer Gösterme')->first()->id;
        return Record::where('portfolio_id', $portfolioId)->where('record_type_id', $viewingRecordTypeId)->get()->count();
    }
    function findCountOfMarketingRecordsOfSpecificPortfolio($portfolioId){
        $marketingRecordTypeId = RecordType::where('name', 'Pazarlama')->first()->id;
        return Record::where('portfolio_id', $portfolioId)->where('record_type_id', $marketingRecordTypeId)->get()->count();
    }
@endphp

<div class="card main-card" style="@if($portfolios->count() == 0) border: 1px solid rgb(110, 126, 199)@endif">
    <div class="row px-3">
        @if($portfolios->count() > 0)
        @foreach($portfolios as $index => $portfolio)
            @if($loop->iteration <= 2)
                <div class="col-md-6 col-sm-12 mt-3">
            @else
                <div class="col-md-6 col-sm-12 mt-3">
            @endif

            <div class="card portfolio-card mb-md-3 mx-md-0">
                <div class="row px-1 px-md-0">
                    <div class="col-md-4 col-sm-12 text-center">
                        @if($portfolio->attachment()->first())
                        <a href="{{route('platform.portfolio.detail', $portfolio->id)}}"><img src="{{$portfolio->attachment()->first()->url}}" class="img-fluid card-image-portfolio" alt="Portfolio Picture"></a>
                        @else
                        <a href="{{route('platform.portfolio.detail', $portfolio->id)}}"><img src="{{asset('images/house.png')}}" class="img-fluid card-image-portfolio" alt="Portfolio Picture"></a>
                        @endif
                    </div>
                    <div class="col-md-8 col-sm-12">
                        <div class="card-body" style="padding-left: 0 !important;">
                            <div class="row">
                                <div class="col-10 text-start title-col">
                                    <a href="{{route('platform.portfolio.detail', $portfolio->id)}}"><h6 class="card-title">{{$portfolio->title}}</h6></a>
                                </div>
                                @if($portfolio->userS->getRoles()[0]->slug != "bireysel-danisman" && authUserInRole(['super-yonetici', 'yonetici']) || 
                                (authUserInRole('ofis-danismani') && $portfolio->user_id == auth()->user()->id)  ||
                                (authUserInRole('ofis-yoneticisi') && $portfolio->userS->office_id == auth()->user()->office_id))
                                    <div class="col-2 text-end magic-link-col-{{$portfolio->id}}">
                                        <p role="button" portfolio="{{$portfolio->id}}" url="{{route('magic', $portfolio->slug)}}" class="magic m-0 magic-link-stars">
                                            <x-orchid-icon style="margin-top: -.5rem !important" path="stars"></x-orchid-icon>
                                        </p>
                                        <span class="copied-message-{{$portfolio->id}}" style="display: none; color:rgb(245, 243, 146)">{{__('Copied!')}}</span>
                                    </div>
                                @endif
                            </div>
                            <h6 style="color:#fff"><span class="card-sub-title">{{__('List Price') . ": "}}</span> {{number_format($portfolio->list_price, 2, ',', '.')}} ₺</h6>
                            <div class="row">
                                <div class="col mb-4 mt-1 d-flex gap-2" style="padding-left: 10px">
                                    <a class="btn record-button call {{canAuthUserClickPortfolioRecordCounts($portfolio->id) != true ? 'disabled' : ''}}" href="{{route('platform.call', $portfolio->id)}}">
                                        {{mb_strtoupper(__('Çağrı')) . ' (' . findCountOfCallRecordsOfSpecificPortfolio($portfolio->id) . ')'}}
                                    </a>
                                     <a class="btn record-button land {{canAuthUserClickPortfolioRecordCounts($portfolio->id) != true ? 'disabled' : ''}}" href="{{route('platform.viewing', $portfolio->id)}}">
                                        {{mb_strtoupper(__('Yer Gösterme')) . ' (' . findCountOfViewingRecordsOfSpecificPortfolio($portfolio->id) . ')'}}
                                    </a>
                                     <a class="btn record-button sell {{canAuthUserClickPortfolioRecordCounts($portfolio->id) != true ? 'disabled' : ''}}" href="{{route('platform.marketing', $portfolio->id)}}">
                                        {{mb_strtoupper(__('Pazarlama')) . ' (' . findCountOfMarketingRecordsOfSpecificPortfolio($portfolio->id) . ')'}}
                                    </a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-md-5 card-location-col">
                                    <span class="card-content">
                                        {{findProvinceName($portfolio->province_id)}} / {{findStateName($portfolio->state_id)}}
                                    </span>
                                </div>
                                <div class="col-sm-12 col-md-7 p-0 m-0 d-flex gap-2 justify-content-end" style="padding-right: 10px !important">
                                    @checkAccess('platform.portfolios.edit', $portfolio->user_id)
                                    <a class="btn gap-2 card-edit-button" href="{{route('platform.portfolio.edit', $portfolio)}}">
                                        <x-orchid-icon path='pencil-square'></x-orchid-icon>
                                            {{mb_strtoupper(__('Edit'))}}
                                    </a>
                                    @endcheckAccess
                                    @checkAccess('platform.portfolios.remove', $portfolio->user_id)
                                    <a class="btn gap-2 btn-danger card-delete-button" portfolio-id={{$portfolio->id}}>
                                        @csrf
                                        <x-orchid-icon path='trash'></x-orchid-icon>
                                            {{mb_strtoupper(__('DELETE'), 'UTF-8')}}
                                    </a>
                                    @endcheckAccess
                                </div>
                            </div>
                        </div>
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
            <span>{{__('No Registered Porfolios')}}</span>
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

        $('.btn-danger').on('click', function(e){
            portfolioId = $(this).attr('portfolio-id');
            deletePortfolio(portfolioId);
        })

        function deletePortfolio(portfolioId){
            try {
                if (confirm('Bu portföyü silmek istediğinize emin misiniz?')){
                    $.get(
                        "/admin/ajax/delete-portfolio", 
                        {portfolioId: portfolioId},
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

        function showCopiedMessage(portfolioId) {
            var copiedMessageEl = $('.copied-message-' + portfolioId);
            var colEl = $('.magic-link-col-' + portfolioId);
            if (copiedMessageEl) {
                colEl.addClass('d-flex');
                copiedMessageEl.css('display', 'block');
                setTimeout(function() {
                    colEl.removeClass('d-flex');
                    copiedMessageEl.css('display', 'none');
                }, 1000); // 2 saniye sonra mesajı kaldır
            }
        };

        function copyMagicLink(url, portfolioId){
            // Geçici bir textarea oluşturun
            var textarea = document.createElement("textarea");
            textarea.value = url;

            // Textareayı sayfada görünmez bir şekilde ekleyin
            textarea.style.position = "fixed";
            textarea.style.opacity = 0;

            // Textareayı sayfaya ekleyin
            document.body.appendChild(textarea);

            // Textareayı seçin ve kopyalama işlemini gerçekleştirin
            textarea.select();
            document.execCommand("copy");

            // Textareayı ve kopyalama işlemi için eklenen gereksiz elementi kaldırın
            document.body.removeChild(textarea);

            // Kopyalama işlemi tamamlandı mesajı gönderin
            showCopiedMessage(portfolioId);
        }

        $(".magic-link-stars").on("click", function(){
            var url = $(this).attr('url');
            var portfolioId = $(this).attr('portfolio');
            copyMagicLink(url, portfolioId);
        });
    });


</script>
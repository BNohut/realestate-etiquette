<link rel='stylesheet' href={{asset('css/lc_lightbox.css')}} />
<link rel='stylesheet' href={{asset('css/minimal.css')}} />


@if (count($urls) != 0)

<div class="row my-3">
    
    <div class="col-12 px-4">
        <div class="row">
    <div class="col my-2 px-4">
        <span class="form-label mb-0" style="font-size: .875rem; font-weight: 500; padding-left: 10px !important">
            {{__('Uploaded Images')}}
        </span>
    </div>
</div>
        <div id="lcl_elems_wrapper" class="bg-white rounded shadow-sm p-4 d-flex gap-3">
            @foreach ($urls as $index => $url)
                <div class="card align-items-center flex-column" style="border: 1.5px solid rgb(9, 10, 11); background-color:rgb(193, 193, 193); border-radius: 0.5rem;" >
                    <a href={{$url}}>
                        <img class="p-1" style="border-radius: 10%" src="{{$url}}" alt="Deneme" width="90" height="90">
                    </a>
                    <button onClick="deleteImage({{$index}}, {{$portfolio->id}})" class="delete-button btn pt-0" style="color:red;">
                        <x-orchid-icon width="1rem" 
                        height="1rem" path="trash3" />
                        <span class="visually-hidden"></span>
                    </button>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<script>
    function deleteImage(index, orderId){
        try {
            if (confirm('Bu resmi silmek istediğinize emin misiniz?')){
                $.get("/admin/ajax/delete-image", 
                {   
                    portfolioId: orderId,
                    index: index,
                }, function(result) {
                        if (result.status == true) {
                            window.location.reload();
                        }else{
                            console.log(result);
                        }
                    })
            }
            } catch (error) {
                console.log(error);
            }
    }
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
        var obj = lc_lightbox('#lcl_elems_wrapper div a');
    });
</script>

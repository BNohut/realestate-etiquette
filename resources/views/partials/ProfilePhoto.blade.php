<style>
    [data-controller="listener"]>fieldset {
        margin-bottom: 0 !important;
    }
    .bg-white{
        padding-top: .5rem !important;
    }
</style>
<input class="form-input" id="{{$contactModal ? 'contactImageInput' : 'consultantImageInput'}}" type="file" name="avatar" hidden>
<div class="row mt-3">
    <div class="d-flex gap-3 {{$edit ? 'col-6' : 'col justify-content-center'}}">
        <label for="{{$contactModal ? 'contactImageInput' : 'consultantImageInput'}}">
            <a style="{{$edit ? 'margin-left: 2.3rem' : ''}}">
                <img class="uploaded-image picture" src="{{asset('images/plus.svg')}}" id="{{$contactModal ? 'contact' : 'consultant'}}" alt="Avatar">
            </a>
        </label>
    </div>
</div>
@php
    $url = "";
        if($contact){
            if($contact->attachment()->get()->count() > 0 ){
                $url = $contact->attachment()->first()->url;
            };
        };
@endphp

@if($firstCall)
    <script>
        var contactAvatarUrl = @json($url);
        if (contactAvatarUrl.length > 0){
            var imageFirst = document.getElementById('contact');
            imageFirst.src = contactAvatarUrl;
        }
        //Catch input HTML element
        var contactInput = document.getElementById('contactImageInput');
        //If EndUser Upload ProfilePhoto
        contactInput.addEventListener('input', function(e){
            //Check for any modal shown or not
            //If there is a shown modal
            if(document.querySelector('.modal.show')){
                //Check the modal id
                //If it includes 'Contact' loadFile to contact modal
                if(document.querySelector('.modal.show').getAttribute('id').includes('Contact')){
                    loadFileContact(e);
                }
            }else{
                loadFileContact(e);
            }
        });
        function loadFileContact(event){
            //Catch the image HTML element
            const imageSecond = document.getElementById('contact');
            //CreateObjectURL for uploaded image and set it to rc prop of image element with event file
            imageSecond.src = URL.createObjectURL(event.target.files[0]);
        };
    </script>
@else
    <script>
        var consultantAvatarUrl = @json($url);
        if (consultantAvatarUrl.length >0){
            var imageThird = document.getElementById('consultant');
            imageThird.src = consultantAvatarUrl;
        }

        //Catch input HTML element
        var consultantInput = document.getElementById('consultantImageInput');
        //If EndUser Upload ProfilePhoto
        consultantInput.addEventListener('input', function(e){
            //Check for any modal shown or not
            //If there is a shown modal
            if(document.querySelector('.modal.show')){
                //Check the modal id
                //If it includes 'Contact' loadFile to contact modal
                if(document.querySelector('.modal.show').getAttribute('id').includes('Consultant')){
                    loadFileConsultant(e);
                }
            }else{
                loadFileConsultant(e);
            }
        })

        function loadFileConsultant(event, id){
            //Catch the image HTML element
            const imageFourth = document.getElementById('consultant');
            //CreateObjectURL for uploaded image and set it to rc prop of image element with event file
            imageFourth.src = URL.createObjectURL(event.target.files[0]);
        }
    </script>
@endif

<style>
    .picture {
        border: 0.5px solid rgb(231, 136, 76);
        vertical-align: middle;
        width: 100px;
        height: 100px;
        max-width: 100px;
        max-height: 100px;
        border-radius: 20%;
        padding: 0.6rem;
    }
</style>

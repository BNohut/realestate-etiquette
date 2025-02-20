<link href="{{ asset('css/app.css') }}" rel="stylesheet">
@if(!Auth::user())
    <div class="text-center">
        <p class="small m-2">
            <a href="/sign-up" class="text-secondary">Üye Ol</a>
        </p>
    </div>
@endif
<div class="text-center user-select-none text-black">
    <p class="small m-0">
        Etiquette @ {{ date('Y') }} <a href="https://www.kodgaraj.com" target="_blank"
            title="Hayal Edin, Kodlayalım!" class="text-secondary">KodGaraj</a>
    </p>
</div>

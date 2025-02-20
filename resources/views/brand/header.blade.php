@push('stylesheets')
    <link href="{{ asset('images/favicon.ico') }}" id="favicon" rel="icon">
@endpush

<p class="h2 n-m font-thin text-center">
    <img src="{{ asset('images/etiquette_white.png') }}" alt="Etiquette Logo" id="header-logo" />
</p>

@if($errors->has('verified'))
    <p class="error-alert text-center n-m" id="password-error">{{$errors->first('verified')}}</p>
@endif

<script>
    // var aTagEl = document.querySelector('a.d-flex');
    // aTagEl.classList.remove("d-flex");
    // aTagEl.classList.remove("mb-4");

    // Hata mesajlarını 10 saniye sonra gizleme işlemi
    setTimeout(function() {
        var errorAlert = document.querySelector('.error-alert');
        if (errorAlert) {
            errorAlert.style.display = 'none';
        }
    }, 5000);
</script>

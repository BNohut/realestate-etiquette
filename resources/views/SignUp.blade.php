<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Marcellus&family=Montserrat:wght@300&display=swap" rel="stylesheet">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Etiquette - Üye Ol</title>
<link href="{{ asset('./images/favicon.ico') }}" id="favicon" rel="icon">

<style>
    body{
        align-items: center !important;
        display: flex;
        justify-content: center;
        font-family: 'Marcellus', serif;
        font-family: 'Montserrat', sans-serif;
        background: url('images/signup.png') no-repeat center fixed;
        background-size: cover;
        margin: 0;
        padding: 0;
    }

    /* body::before {
        content: "";
        background: url('images/signup.png') no-repeat center fixed;
        background-size: contain;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        height: 100vh;
        margin: 0;
        position: absolute;
        z-index: -1;   
    } */

    .logo {
        width: 250px;
        margin-bottom: 1.2rem;
    }

    @media (max-width: 578px) {
        .card {
            border-radius: 2rem;
            box-shadow: 0 5px rgba(0, 0, 0, 0.2);
            opacity: .85;
        } 
    }
    @media (min-width: 578px) {
        .card {
            border-radius: 2rem;
            box-shadow: 0 5px rgba(0, 0, 0, 0.2);
            opacity: .85;
        } 
    }

    input, select {
        border-radius: 2rem !important;
        text-align: center !important;
    }

    input:focus, select:focus{
        border: 1px solid #6544f9 !important;
        box-shadow: 0 0 0 0.25rem rgb(100 68 249 / 25%) !important;
    }
    label {
        color: #6544f9;
        display: flex;
        margin-bottom: 0 !important;
    }

    sub {
        color: red;
    }

    h2 {
        color: #6544f9;
        font-weight: bold !important;
        font-size: 1.5rem !important;
    }
    
    button {
        background-color: #6544f9 !important;
        border-radius: 2rem !important;
        color: #fff !important;
    }

    button:hover {
        background-color: #ffffff !important;
        color: #6544f9 !important;
    }

    a {
        color: #6544f9 !important;
        margin: .4rem;
        margin-bottom: 0;
    }

    p {
        margin: .45rem;
        margin-bottom: 0;
    }

    span .footer{
        color: #fff;
        font-weight: 500;
        text-shadow: 0 2px rgba(0, 0, 0, 0.2);
    }

    #email-error, #password-error {
        margin-left: 20px !important;
        color: red;
        font-size: 13px;
        margin-top: 4;
    }

    #error-alert {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        margin: auto;
    }

    #alert-box {
        background: #ffffff3b;
        border: 1px solid #9d1111;
        color: #9d1111;
        border-radius: .6rem;
        padding: 10px;
        height: 30px;
        
    }

    #alert-box p {
        margin-top: -10px !important;
    }

</style>

<div class="container">
    <div class="row justify-content-center">
        <div class="col text-center">
            <img class="logo" src="{{asset('images/etiquette_white.png')}}" alt="White Logo">
            <div class="row justify-content-center">
                <div class="col-sm-12 col-md-4 text-center">
                    <div class="card text-center">
                        <div class="card-body">
                            <div class="card-title mt-2">
                                <h2>Üye Ol</h2>
                            </div>
                            <form action="{{route('user.register')}}" method="POST" autocomplete="off">
                                @csrf
                                <div class="mx-4 mt-2">
                                    <label for="name" class="form-label">İsim<sub> *</sub></label>
                                    <input type="text" class="form-control" required id="name" name="name" value="{{old('name')}}">
                                </div>
                                <div class="mx-4 mt-2">
                                    <label for="last_name" class="form-label">Soyisim<sub> *</sub></label>
                                    <input type="text" class="form-control" required id="last_name" name="last_name" value="{{old('last_name')}}">
                                </div>
                                <div class="mx-4 mt-2">
                                    <label for="province" class="form-label">İl<sub> *</sub></label>
                                    <select class="form-select" id="province" name="province_id">
                                        <option selected>{{__('Select Province')}}</option>
                                    </select>
                                </div>
                                <div class="mx-4 mt-2">
                                    <label for="state" class="form-label">İlçe<sub> *</sub></label>
                                    <select class="form-select" id="state" name="state_id">
                                        <option selected>{{__('Select State')}}</option>
                                    </select>
                                </div>
                                <div class="mx-4 mt-2">
                                    <label for="email" class="form-label">E-mail<sub> *</sub>
                                    @if($errors->has('email'))
                                        <span class="error-alert" id="email-error">{{$errors->first('email')}}</span>
                                    @endif
                                    </label>
                                    <input type="email" class="form-control" required id="email" name="email" value="{{old('email')}}">
                                </div>
                                <div class="mx-4 mt-2">
                                    <label for="password" class="form-label">Şifre<sub> *</sub>
                                    @if($errors->has('password'))
                                        <span class="error-alert" id="password-error">{{$errors->first('password')}}</span>
                                    @endif
                                    </label>
                                    <input type="password" class="form-control" required id="password" name="password">
                                </div>
                                <div class="mx-4 mt-2">
                                    <label for="password_confirmation" class="form-label">Şifre Tekrar<sub> *</sub></label>
                                    <input type="password" class="form-control" required id="password_confirmation" name="password_confirmation">
                                </div>
                                <div class="row justify-content-center">
                                    <div class="col mt-2">
                                            <input class="form-check-input mt-2" type="checkbox" value="" id="agreement">
                                            <a type="button" id="agreementLink">
                                             Kullanıcı Sözleşmesi
                                            </a>'ni okudum ve kabul ediyorum.
                                    </div>
                                </div>
                                <div class="d-grid d-block mx-4 mt-4">
                                    <button type="submit" class="btn btn-primary" disabled>Kaydet</button>
                                </div>
                                <div class="row">
                                    <div class="col d-flex justify-content-center mt-4">
                                        <p>Zaten kayıtlı mısın? </p>
                                        <a href="/"> Giriş yap <x-orchid-icon path="box-arrow-in-right"></x-orchid-icon></a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-12 text-center mt-4">
                    <span class="footer text-white">© {{date('Y')}} KodGaraj</span>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content" style="border-radius: 1rem;">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">KULLANICI LİSANS SÖZLEŞMESİ (EULA)</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p style="text-align:center"><span style="font-size:11pt"><span style="font-family:Arial,sans-serif"><span style="color:#000000"><strong>ETIQUETTE WEB ve MOBİL UYGULAMA</strong></span></span></span></p>

                <p style="text-align:center"><span style="font-size:11pt"><span style="font-family:Arial,sans-serif"><span style="color:#000000"><strong>KULLANICI LİSANS S&Ouml;ZLEŞMESİ (EULA)</strong></span></span></span></p>

                <p>&nbsp;</p>

                <p><span style="font-size:11pt"><span style="font-family:Arial,sans-serif"><span style="color:#000000">L&uuml;tfen Etiquette mobil uygulamasını kullanmadan &ouml;nce bu Kullanıcı Lisans S&ouml;zleşmesi&#39;ni dikkatlice okuyun. Etiquette uygulamasını kullanarak aşağıdaki koşulları kabul etmiş olursunuz.</span></span></span></p>

                <p>&nbsp;</p>

                <p><span style="font-size:11pt"><span style="font-family:Arial,sans-serif"><span style="color:#000000"><u>Kabul Edilen Şartlar:</u></span></span></span></p>

                <p><span style="font-size:11pt"><span style="font-family:Arial,sans-serif"><span style="color:#000000">Etiquette uygulamasını kullanırken herhangi bir t&uuml;rde hakaret i&ccedil;eren, taciz edici, tehditkar veya ayrımcı i&ccedil;erik oluşturamaz, paylaşamaz veya yayınlayamazsınız.</span></span></span></p>

                <p><span style="font-size:11pt"><span style="font-family:Arial,sans-serif"><span style="color:#000000">Etiquette uygulamasında başkalarının gizliliğini ihlal eden veya kişisel bilgilerini paylaşan i&ccedil;erikler oluşturamazsınız veya paylaşamazsınız.</span></span></span></p>

                <p><span style="font-size:11pt"><span style="font-family:Arial,sans-serif"><span style="color:#000000">Etiquette uygulamasında yasa dışı faaliyetlerde bulunamazsınız veya teşvik edemezsiniz.</span></span></span></p>

                <p>&nbsp;</p>

                <p><span style="font-size:11pt"><span style="font-family:Arial,sans-serif"><span style="color:#000000"><u>İ&ccedil;erik Filtreleme:</u></span></span></span></p>

                <p><span style="font-size:11pt"><span style="font-family:Arial,sans-serif"><span style="color:#000000">Etiquette, uygunsuz i&ccedil;eriği filtrelemek i&ccedil;in &ccedil;eşitli teknik &ouml;nlemler alabilir. Ancak, kullanıcılar da uygunsuz i&ccedil;erikleri bildirmekten sorumludur. İ&ccedil;erikler g&uuml;nl&uuml;k kontrol edilir ve uygunsuz g&ouml;r&uuml;len i&ccedil;erik 24 saat i&ccedil;inde silinir.</span></span></span></p>

                <p>&nbsp;</p>

                <p><span style="font-size:11pt"><span style="font-family:Arial,sans-serif"><span style="color:#000000"><u>Kullanıcıların İtiraz Mekanizması:</u></span></span></span></p>

                <p><span style="font-size:11pt"><span style="font-family:Arial,sans-serif"><span style="color:#000000">Kullanıcılar, Etiquette uygulamasında g&ouml;rd&uuml;kleri uygunsuz i&ccedil;erikleri bildirebilirler. Etiquette ekibi, bu t&uuml;r raporları değerlendirecek ve gerekli &ouml;nlemleri alacaktır.</span></span></span></p>

                <p>&nbsp;</p>

                <p><span style="font-size:11pt"><span style="font-family:Arial,sans-serif"><span style="color:#000000"><u>K&ouml;t&uuml;ye Kullanıcıları Engelleme:</u></span></span></span></p>

                <p><span style="font-size:11pt"><span style="font-family:Arial,sans-serif"><span style="color:#000000">Etiquette kullanıcıları, rahatsız edici veya istenmeyen davranışlar sergileyen diğer kullanıcıları uygulama i&ccedil;erisinde engelleyebilirler.</span></span></span></p>

                <p>&nbsp;</p>

                <p><span style="font-size:11pt"><span style="font-family:Arial,sans-serif"><span style="color:#000000"><u>İ&ccedil;eriğe Y&ouml;nelik Hızlı Eylem:</u></span></span></span></p>

                <p><span style="font-size:11pt"><span style="font-family:Arial,sans-serif"><span style="color:#000000">Etiquette ekibi, kullanıcılar tarafından bildirilen uygunsuz i&ccedil;erikleri 24 saat i&ccedil;inde inceleyecek ve gerektiğinde i&ccedil;eriği kaldıracak ve ihlal eden kullanıcıyı uygulamadan &ccedil;ıkaracaktır.</span></span></span></p>

                <p>&nbsp;</p>

                <p><span style="font-size:11pt"><span style="font-family:Arial,sans-serif"><span style="color:#000000"><u>Kabul Edilme Tarihi:</u></span></span></span><span style="font-size:11pt"><span style="font-family:Arial,sans-serif"><span style="color:#000000"> &Uuml;yelik Oluşturulan Tarih</span></span></span></p>

                <p>&nbsp;</p>

                <p><span style="font-size:11pt"><span style="font-family:Arial,sans-serif"><span style="color:#000000">Bu Kullanıcı Lisans S&ouml;zleşmesi Etiquette mobil uygulamasını kullanmanızı d&uuml;zenler. Bu s&ouml;zleşmeyi kabul etmiyorsanız, Etiquette uygulamasını kullanmayınız. Etiquette ile ilgili gizlilik s&ouml;zleşmesini şu linkte g&ouml;rebilirsiniz:&nbsp;</span></span></span></p>


<p><a href="https://panel.etiquette.biz/gizlilik.html" rel="noreferrer noopener" tabindex="0" target="_blank" title="https://panel.etiquette.biz/gizlilik.html">https://panel.etiquette.biz/gizlilik.html</a></p>
                <p><br />
                &nbsp;</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tamam</button>
            </div>
        </div>
    </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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

    loadScript('https://code.jquery.com/jquery-3.7.0.min.js', function() {
        let provinceSelectEl = $('#province');
        $.get('/provinces', function(data){
            let provinces = data.data;
            // Add option html to province select el
            html = '';
            provinces.forEach(province => {
                html += '<option value=' + province.id + '>' + province.name + '</option>'
            });
            provinceSelectEl.append(html);
        })
        
        provinceSelectEl.on('change', function() {
            let provinceId = $(this).val();
            let stateSelectEl = $('#state');
            $.get('/states/' + provinceId, function(data){
                let states = data.data;
                // Add option html to province select el
                html = '';
                states.forEach(state => {
                    html += '<option value=' + state.id + '>' + state.name + '</option>'
                });
                stateSelectEl.html(html);    
            });
        });
        let agreement = $('#agreementLink');
        agreement.on('click', function() {
            const agreementModal = new bootstrap.Modal('#staticBackdrop');
            agreementModal.show();
        });
        let agreementCheckbox = $('#agreement');
        let submitButton = $('button[type="submit"]');
        agreementCheckbox.on('change', function() {
            if (this.checked) {
                submitButton.removeAttr('disabled');
            } else {
                submitButton.attr('disabled', 'disabled');
            }
        });
    });
    // Hata mesajlarını 2.4 saniye sonra gizleme işlemi
    setTimeout(function() {
        var errorAlert = document.querySelector('.error-alert');
        if (errorAlert) {
            errorAlert.style.display = 'none';
        }
    }, 1800);
</script>
@php
    use Firebase\JWT\JWT;
    
    function jwtCreate($user)
    {
        $jwt = JWT::encode([
            ...(is_array($user) ? $user : $user->toArray()),
            'exp' => time() + (60 * 60 * 24 * 30),
            'iat' => time()
        ], config('app.jwt.secret'), 'HS256');

        return $jwt;
    }
    $token = jwtCreate(collect(auth()->user()->toArray())->except(['role', 'role_permissions']));
@endphp
<script>
        document.addEventListener("turbo:load", function () {
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
                    //Get Consultants Depends On Auth User Role From PHP
                    let consultantSelectEl = $('#selectConsultant');
                    let contactSelectEl = $('#selectContact');
                    let portfolioSelectEl = $('#selectPortfolio');

                    consultantSelectEl.on('input', function(){
                        let contactSelectElement = document.getElementById('selectContact');
                        let portfolioSelectElement = document.getElementById('selectPortfolio');
                        let selectedConsultantId = $(this).find('option:selected').val();

                        if(contactSelectElement && contactSelectEl){
                            // Clear Variation SelectBox TomSelect Items
                            contactSelectElement.tomselect.clear();
                            contactSelectElement.tomselect.clearOptions();
    
                            //Clear Variation SelectBox Options
                            while (contactSelectElement.firstChild) {
                                contactSelectElement.removeChild(contactSelectElement.firstChild);
                            }
                            
                            $.ajax({
                            url: "/api/contact/all",
                            type: 'POST',
                            headers: {"Authorization": "Bearer " + @json($token)},
                            data: {
                                consultantId: selectedConsultantId
                                }
                            }).done(function(data) {
                                let contacts = data.data;
                                let contactList = [];
                                for (let i = 0; i < contacts.length; i++) {
                                    contactList[contacts[i].id] = contacts[i].name;
                                }
                                contactList.forEach((contact, key) => {
                                    contactSelectEl.append($('<option></option>').attr('value', key).text(contact));
                                })
                                contactSelectElement.tomselect.refreshItems();
                            }).fail(function(jqXHR, textStatus, errorThrown) {
                                console.log(jqXHR);
                                console.log(textStatus);
                                console.log(errorThrown);
                            });
                        }
                        if(portfolioSelectElement && portfolioSelectEl){
                            portfolioSelectElement.tomselect.clear();
                            portfolioSelectElement.tomselect.clearOptions();
                            while (portfolioSelectElement.firstChild) {
                                portfolioSelectElement.removeChild(portfolioSelectElement.firstChild);
                            }
                            $.ajax({
                            url: "/api/portfolio/all",
                            type: 'POST',
                            headers: {"Authorization": "Bearer " + @json($token)},
                            data: {
                                consultantId: selectedConsultantId
                                }
                            }).done(function(data) {
                                let portfolios = data.data;
                                let portfolioList = [];
                                for (let i = 0; i < portfolios.length; i++) {
                                    portfolioList[portfolios[i].id] = portfolios[i].title;
                                }
                                portfolioList.forEach((contact, key) => {
                                    portfolioSelectEl.append($('<option></option>').attr('value', key).text(contact));
                                })
                                portfolioSelectElement.tomselect.refreshItems();
                            }).fail(function(jqXHR, textStatus, errorThrown) {
                                console.log(jqXHR);
                                console.log(textStatus);
                                console.log(errorThrown);
                            });
                        }
                    });
                })

        });
</script>
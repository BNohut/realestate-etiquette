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
        document.addEventListener('turbo:load', function(){
            //Change Add Contact Modal Close Button CSS
            var closeButton = $('.btn-close');
            closeButton.removeClass('btn-close');
            closeButton.addClass('btn btn-outline-danger');
            closeButton.text('X');
            closeButton.css('font-size', '1rem');
            closeButton.css('border', 'none');
            closeButton.css('color', '#fff');
            closeButton.hover(
                function () {
                    $(this).css('color', 'red');
                    $(this).css('background', 'transparent');
                }, 
                function () {
                    $(this).css('color','#fff');
                }
                );})
    });
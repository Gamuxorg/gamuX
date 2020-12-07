function auth_rest_post(url) {
    var ajaxreq=new XMLHttpRequest();
    ajaxreq.open(method = 'POST', url = wpApiSettings.root + url); 
    ajaxreq.setRequestHeader( 'X-WP-Nonce', wpApiSettings.nonce );
    ajaxreq.onreadystatechange = function() {
        if(ajaxreq.readyState == 4) {
            if(ajaxreq.status == 200) {
                console.log(ajaxreq.response);
                ajaxreq.onreadystatechange = '';
            }
        }
    }
    ajaxreq.send();
}
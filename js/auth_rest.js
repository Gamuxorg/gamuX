var cp6;
var cp5;    //for debug
function auth_rest_post(url, method = 'POST') {
    var ajaxreq=new XMLHttpRequest();
    ajaxreq.open(method, url = wpApiSettings.root + url); 
    ajaxreq.setRequestHeader( 'X-WP-Nonce', wpApiSettings.nonce );
    ajaxreq.onreadystatechange = function() {
        if(ajaxreq.readyState == 4) {
            if(ajaxreq.status == 200) {
                // console.log(ajaxreq.response);
                cp5 = ajaxreq.response;
                cp6 = JSON.parse(ajaxreq.response);
                ajaxreq.onreadystatechange = function(){};
            }
        }
    }
    ajaxreq.send();
}
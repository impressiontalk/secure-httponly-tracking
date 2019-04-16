setTimeout(function(){
  if (document.cookie.indexOf('secure_cookies_set') == -1) {
    const Http = new XMLHttpRequest();
    const url = '/wp-json/secure_httponly_tracking/v1/secure-cookies';
    Http.open("POST", url);
    Http.send();
    Http.onreadystatechange=(e)=>{
      console.log(Http.responseText)
    }
  }
}, 3000);

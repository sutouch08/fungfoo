function setCookie(cname, cvalue, exdays) {
  var d = new Date();
  d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
  var expires = "expires="+d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
  var name = cname + "=";
  var ca = document.cookie.split(';');
  for(var i = 0; i < ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}

function deleteCookie( name ) {
  document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}
function changeLanguage(current_lang)
{
  let lang = 'thai';
  if(current_lang == 'thai'){
    lang = 'english';
  }

  $.ajax({
    url:BASE_URL + 'tools/change_language/'+lang,
    type:'POST',
    cache:false,
    success:function(){
      window.location.reload();
    }
  });
}

function label(thaiText, EnglishText){
  return language === 'thai' ? thaiText : EnglishText;
}

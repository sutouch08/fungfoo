var HOME = BASE_URL + 'orders/quotation/';

function goBack(){
  window.location.href = HOME;
}


function addNew(){
  window.location.href = HOME + 'add_new';
}


function goEdit(code){
  window.location.href = HOME + 'edit/'+code;
}

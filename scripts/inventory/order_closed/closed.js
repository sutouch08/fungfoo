var HOME = BASE_URL + 'inventory/invoice/';

function goBack(){
  window.location.href = HOME;
}


function viewDetail(code){
  window.location.href = HOME + 'view_detail/'+ code;
}

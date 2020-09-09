var HOME = BASE_URL + 'inventory/adjust/';
//--- กลับหน้าหลัก
function goBack(){
  window.location.href = HOME;
}


function goAdd(){
  window.location.href = HOME + 'add_new';
}


function goEdit(code){
  window.location.href = HOME + 'edit/'+code;
}

//--- ไปหน้ารายละเอียดออเดอร์
function goDetail(code){
  window.location.href = HOME + 'view_detail/'+ code;
}

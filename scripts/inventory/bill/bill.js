var HOME = BASE_URL + 'inventory/delivery_order/';
//--- กลับหน้าหลัก
function goBack(){
  window.location.href = HOME;
}



//--- ไปหน้ารายละเอียดออเดอร์
function goDetail(code){
  window.location.href = HOME + 'view_detail/'+ code;
}

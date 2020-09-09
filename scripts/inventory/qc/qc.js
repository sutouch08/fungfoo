var HOME = BASE_URL + 'inventory/qc/';

function goBack(){
  window.location.href = HOME;
}




//--- ต้องการจัดสินค้า
function goQc(code){
  window.location.href = HOME + 'process/'+code;
}



function viewProcess(){
  window.location.href = HOME + 'view_process';
}



//---- กำหนดค่าการแสดงผลที่เก็บสินค้า เมื่อมีการคลิกปุ่มที่เก็บ
$(function () {
  $('.btn-pop').popover({html:true});
});

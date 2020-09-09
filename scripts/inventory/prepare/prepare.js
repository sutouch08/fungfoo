var HOME = BASE_URL + 'inventory/prepare';


function goBack(){
    window.location.href = HOME;
}




//---- ไปหน้าจัดสินค้า
function goPrepare(code){
    window.location.href = HOME + '/process/'+code;
}


function goProcess(){
  window.location.href = HOME + '/view_process';
}


function pullBack(code){
  $.ajax({
    url:HOME + '/pull_order_back',
    type:'POST',
    cache:'false',
    data:{
      'order_code' : code
    },
    success:function(rs){
      var rs = $.trim(rs);
      if(rs == 'success'){
        $('#row-'+code).remove();
        reIndex();
      }else{
        swal('Error', rs, 'error');
      }
    }
  });
}




//--- ไปหน้ารายการที่กำลังจัดสินค้าอยู่
function viewProcess(){
  window.location.href = HOME + '/view_process';
}

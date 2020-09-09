
var chk = setInterval(function () { checkState(); }, 10000);



function checkState(){
  var order_code = $("#order_code").val();
  $.ajax({
    url: HOME + 'get_state',
    type: 'GET',
    data: {
      'order_code' : order_code
    },
    success: function(rs){
      var rs = parseDefault(parseInt($.trim(rs)), 0);
      if( rs > 7){
        $("#btn-confirm-order").remove();
        clearInterval(chk);
      }
    }
  });
}



function confirmOrder(){
  var order_code = $("#order_code").val();
  load_in();
  $.ajax({
    url: HOME + 'confirm_order',
    type:'POST',
    cache:'false',
    data:{
      'order_code' : order_code
    },
    success:function(rs){
      load_out();
      var rs = $.trim(rs);
      if( rs == 'success'){
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          window.location.reload();
        },1200);

      }else {
        swal('Error!', rs, 'error');
      }
    }
  });
}

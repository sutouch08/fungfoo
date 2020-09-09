function getOrderCreditList(){
  var customer_code = $('#customer_code').val();
  var repay_code = $('#repay_code').val();
  $.ajax({
    url:HOME + 'get_credit_list/'+customer_code+'/'+repay_code,
    type:'GET',
    cache:false,
    success:function(rs){
      if(isJson(rs)){
        var source = $('#check-list-template').html();
        var data = $.parseJSON(rs);
        var output = $('#check-list-table');
        render(source, data, output);
        $('#check-list-modal').modal('show');
      }else{
        swal('Error', rs, 'error');
      }
    }
  })
}


function addToList(){
  $('#check-list-modal').modal('hide');
  var repay_code = $('#repay_code').val();
  var customer_code = $('#customer_code').val();
  var data = [];
  $('.chk-order').each(function(){
    if($(this).is(':checked')){
      let id = $(this).val();
      data.push({"name" : "credit["+id+"]", "value" : id});
    }
  })

  if(data.length > 0){
    $.ajax({
      url:HOME + 'add_detail/'+repay_code,
      type:'POST',
      cache:false,
      data: data,
      success:function(rs){
        if(rs === 'success'){
          reloadList();
        }else{
          swal({
            title:'Error!',
            text:rs,
            type:'error'
          });

          reloadList();
        }
      }
    })
  }
}



function reloadList(){
  var code = $('#repay_code').val();
  $.ajax({
    url:HOME + 'get_details_talbe/'+code,
    type:'GET',
    cache:false,
    success:function(rs){
      if(isJson(rs)){
        var source = $('#detail-template').html();
        var data = $.parseJSON(rs);
        var output = $('#details');

        render(source, data, output);
      }else{
        swal(rs);
      }
    }
  })
}


function getDelete(order_code, id){
  swal({
    title:'คุณแน่ใจ ?',
    text: 'ต้องการลบ '+order_code+' หรือไม่ ?',
    type:'warning',
    showCancelButton:true,
    confirmButtonColor: "#FA5858",
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
      deleteDetail(id);
  })
}


function deleteDetail(id){
  $.ajax({
    url: HOME + 'delete_detail/'+id,
    type:'POST',
    cache:'false',
    success:function(rs){
      var rs = $.trim(rs);
      if(rs == 'success'){
        swal({
          title:'Deleted',
          type:'success',
          timer:1000
        });

        reloadList();
      }
    }
  });
}

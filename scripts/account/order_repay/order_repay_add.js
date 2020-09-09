function save(){
  var error = 0;
  var count = $('.input-amount').length;
  if(count == 0){
    return false;
  }

  
  $('.input-amount').each(function(index){
    let amount = $(this).val();
    if(amount.length === 0){
      $(this).addClass('has-error');
      error++;
    }else{
      $(this).removeClass('has-error');
    }
  });

  if(error === 0){
    $('#updateForm').submit();
  }else{
    swal("กรุณาระบุยอดชำระ");
  }
}


$('#date').datepicker({
  dateFormat:'dd-mm-yy'
});





$("#customer").autocomplete({
	source: BASE_URL + 'auto_complete/get_customer_code_and_name',
	autoFocus: true,
	close: function(){
		var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			var code = arr[0];
			var name = arr[1];
			$("#customerCode").val(code);
			$("#customer").val(name);
      $('#remark').focus();
		}else{
			$("#customerCode").val('');
			$(this).val('');
		}
	}
});



function add(){
  var customer_code = $('#customerCode').val();
  var customer_name = $('#customer').val();
  var date_add = $('#date').val();

  if(customer_code.length == 0 || customer_name.length == 0){
    swal('ชื่อลูกค้าไม่ถูกต้อง');
    return false;
  }

  if(!isDate(date_add))
  {
    swal('วันที่ไม่ถูกต้อง');
    return false;
  }

  $('#addForm').submit();
}


function getEdit(){
  $('.edit').removeAttr('disabled');
  $('#btn-edit').addClass('hide');
  $('#btn-update').removeClass('hide');
}


function update(){
  var code = $('#repay_code').val();
  var customer_code = $('#customer_code').val();
  var customer_name = $('#customer').val();
  var pay_type = $('#pay_type').val();
  var date_add = $('#date').val();
  var remark = $('#remark').val();

  if(customer_code.length == 0 || customer_name.length == 0){
    swal('ชื่อลูกค้าไม่ถูกต้อง');
    return false;
  }

  if(!isDate(date_add))
  {
    swal('วันที่ไม่ถูกต้อง');
    return false;
  }

  if(pay_type == ''){
    swal('กรุณาเลือกวิธีการชำระ');
    return false;
  }

  $.ajax({
    url:HOME + 'update/'+ code,
    type:'POST',
    cache:false,
    data:{
      'customer_code' : customer_code,
      'pay_type' : pay_type,
      'date_add' : date_add,
      'remark' : remark
    },
    success:function(rs){
      if(rs === 'success'){
        $('.edit').attr('disabled', 'disabled');
        $('#btn-update').addClass('hide');
        $('#btn-edit').removeClass('hide');

        swal({
          title:'Updated',
          text:'ปรับปรุงเอกสารเรียบร้อยแล้ว',
          type:'success',
          timer:1000
        });
      }else{
        swal(rs);
      }
    }
  })
}

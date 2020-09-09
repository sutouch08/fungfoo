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
		}else{
			$("#customerCode").val('');
			$(this).val('');
		}
	}
});


$('#is_term').change(function(){
  if($(this).val() == 1){
    $('#credit_term').removeAttr('readonly').focus();
  }else{
    $('#credit_term').val(0).attr('readonly', 'readonly');
  }
})



$('#date_add').datepicker({
  dateFormat:'dd-mm-yy'
})

function get_edit(){
  $('.edit').removeAttr('disabled');
  $('#btn-edit').addClass('hide');
  $('#btn-update').removeClass('hide');
}

function update(){
  let code = $('#code').val();
  let date = $('#date_add').val();
  let customer_code = $('#customerCode').val();
  let contact = $('#contact').val();
  let is_term = $('#is_term').val();
  let credit_term = $('#credit_term').val();
  let remark = $('#remark').val();

  if(!isDate(date)){
    swal("วันที่ไม่ถูกต้อง");
    return false;
  }

  if(customer_code.length == 0){
    swal("รหัสลูกค้าไม่ถูกต้อง");
    return false;
  }

  load_in();
  $.ajax({
    url:HOME + 'update',
    type:'POST',
    cache:false,
    data:{
      'code' : code,
      'date_add' : date,
      'customer_code' : customer_code,
      'contact' : contact,
      'is_term' : is_term,
      'credit_term' : credit_term,
      'remark' : remark
    },
    success:function(rs){
      load_out();
      rs = $.trim(rs);
      if(rs === 'success'){
        $('.edit').attr('disabled', 'disabled');
        $('#btn-update').addClass('hide');
        $('#btn-edit').removeClass('hide');

        swal({
          title:'Updated',
          type:'success',
          timer: 1000
        });
      }else{
        swal({
          title:'Error!',
          text: rs,
          type:'error'
        });
      }
    }

  })
}



$("#pd-box").autocomplete({
	source: BASE_URL + 'auto_complete/get_style_code',
	autoFocus: true
});




$('#pd-box').keyup(function(event) {
	if(event.keyCode == 13){
		var code = $(this).val();
		if(code.length > 0){
			setTimeout(function(){
				getProductGrid();
			}, 300);

		}
	}

});




function getOrderGrid(pdCode){
	if( pdCode.length > 0  ){
		load_in();
		$.ajax({
			url: BASE_URL + 'orders/orders/get_product_grid',
			type:"GET",
			cache:"false",
			data:{
				"style_code" : pdCode
			},
			success: function(rs){
				load_out();
				var rs = rs.split(' | ');
				if( rs.length == 4 ){
					var grid = rs[0];
					var width = rs[1];
					var pdCode = rs[2];
					var style = rs[3];
					$("#modal").css("width", width +"px");
					$("#modalTitle").html(pdCode);
					$("#id_style").val(style);
					$("#modalBody").html(grid);
					$("#orderGrid").modal('show');
				}else{
					swal(rs[0]);
				}
			}
		});
	}
}


function getProductGrid(){
	var pdCode = $('#pd-box').val();
	if( pdCode.length > 0  ){
		load_in();
		$.ajax({
			url: BASE_URL + 'orders/orders/get_product_grid',
			type:"GET",
			cache:"false",
			data:{
				"style_code" : pdCode
			},
			success: function(rs){
				load_out();
				var rs = rs.split(' | ');
				if( rs.length == 4 ){
					var grid = rs[0];
					var width = rs[1];
					var pdCode = rs[2];
					var style = rs[3];
					$("#modal").css("width", width +"px");
					$("#modalTitle").html(pdCode);
					$("#id_style").val(style);
					$("#modalBody").html(grid);
					$("#orderGrid").modal('show');
				}else{
					swal(rs[0]);
				}
			}
		});
	}
}


function valid_qty(){
  return true;
}


function insert_item()
{
	$('#orderGrid').modal('hide');
	var code = $('#code').val();
	var discLabel = $('#discountLabel').val();

	var items = [];

  $('.input-qty').each(function(){
    let pdCode = $(this).attr('id');
    var qty = parseDefault(parseInt($(this).val()), 0);

    if(qty > 0){
      var item = {
        'product_code' : pdCode,
        'qty' : qty
      }

      items.push(item);
    }
  });

  if(items.length == 0){
    swal('กรุณาระบุจำนวนอย่างน้อย 1 รายการ');
    return false;
  }

  var data = JSON.stringify(items);

	load_in();

  $.ajax({
    url:HOME + 'add_details',
		type:'POST',
		cache:false,
		data:{
			'code' : code,
			'data' : data,
			'discountLabel' : discLabel
		},
		success:function(rs){
			load_out();
			if(rs == 'success'){
				swal({
					title:'Success',
					text:'เพิ่ม '+items.length+' รายการ เรียบร้อยแล้ว',
					type:'success',
					timer:1000
				});

				setTimeout(function(){
					window.location.reload();
				},1500);
			}else{
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				}, function(){
					$('#orderGrid').modal('show');
				});
			}
		}
  });
}

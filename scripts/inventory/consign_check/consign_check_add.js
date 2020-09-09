function unsave()
{
	var code = $('#return_code').val();
	swal({
		title:'คุณแน่ใจ ?',
		text:'โปรดทราบ คุณต้องลบเอกสารใน SAP ด้วย ต้องการดำเนินการต่อหรือไม่ ?',
		type:'warning',
		showCancelButton:true,
		confirmButtonText:'ดำเนินการต่อ',
		confirmButtonColor:'#DD6B55',
		cancelButtonText:'ยกเลิก',
		closeOnConfirm:false
	}, function(){
		$.ajax({
			url:HOME + 'unsave/'+code,
			type:'POST',
			cache:false,
			success:function(rs){
				if(rs == 'success'){
					swal({
						title:'Success',
						text:'ยกเลิกการบันทึกเรียบร้อยแล้ว',
						type:'success',
						timer:1000
					});

					setTimeout(function(){
						goEdit(code);
					}, 1500);
				}else{
					swal({
						title:'Error',
						text:rs,
						type:'error'
					});
				}
			}
		})
	});
}


function getEdit(){
  $('#dateAdd').removeAttr('disabled');
  $('#remark').removeAttr('disabled');
  $('#btn-edit').addClass('hide');
  $('#btn-update').removeClass('hide');
}



function update(){
  var code = $('#check_code').val();
  var date_add = $('#dateAdd').val();
  var remark   = $('#remark').val();

  if(! isDate(date_add)){
    swal('วันที่ไม่ถูกต้อง');
    return false;
  }


  load_in();
  $.ajax({
    url: HOME + 'update_header/'+code,
    type:'POST',
    cache:'false',
    data:{
      'date_add' : date_add,
      'remark' : remark
    },
    success:function(rs){
      load_out();
      var rs = $.trim(rs);
      if(rs == 'success'){
        swal({
          title: 'Updated',
          type:'success',
          timer: 1000
        });

        $('#dateAdd').attr('disabled', 'disabled');
        $('#remark').attr('disabled', 'disabled');
        $('#btn-update').addClass('hide');
        $('#btn-edit').removeClass('hide');
      }
    }
  });
}



$('#dateAdd').datepicker({
	dateFormat:'dd-mm-yy'
});



$("#customer").autocomplete({
	source: BASE_URL + 'auto_complete/get_customer_code_and_name',
	autoFocus: true,
	close: function () {
		var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
		if (arr.length == 2) {
			var code = arr[0];
			var name = arr[1];
			$("#customer_code").val(code);
			$("#customer").val(name);
			zoneInit(code, true);
		} else {
			$("#customer_code").val('');
			$(this).val('');
			zoneInit('', true);
		}
	}
});



function zoneInit(customer_code, edit) {
	if (edit) {
		$('#zone_code').val('');
		$('#zone').val('');
	}

	$('#zone').autocomplete({
		source: BASE_URL + 'auto_complete/get_consign_zone/' + customer_code,
		autoFocus: true,
		close: function () {
			var rs = $.trim($(this).val());
			var arr = rs.split(' | ');
			if (arr.length == 2) {
				var code = arr[0];
				var name = arr[1];
				$('#zone_code').val(code);
				$('#zone').val(name);
			} else {
				$('#zone_code').val('');
				$('#zone').val('');
			}
		}
	})
}


function add(){
	let date = $('#dateAdd').val();
	let customer = $('#customer').val();
	let customer_code = $('#customer_code').val();
	let zone = $('#zone').val();
	let zone_code = $('#zone_code').val();
	let remark = $('#remark').val();

	if(! isDate(date)){
		swal("วันที่ไม่ถูกต้อง");
		return false;
	}

	if(customer_code.length == 0 || customer.length == 0){
		swal("กรุณาระบุลูกค้า");
		return false;
	}

	if(zone.length == 0 || zone_code.length == 0){
		swal("โซนไม่ถูกต้อง");
		return false;
	}

	$('#addForm').submit();
}


function recalTotal(){
	var totalQty = 0;
	$('.qty').each(function(){
		let qty = $(this).val();
		qty = parseDefault(parseFloat(qty),0);
		totalQty += qty;
	});

	$('#totalQty').text(addCommas(totalQty));
}


$(document).ready(function(){
	let customer_code = $('#customer_code').val();
	zoneInit(customer_code, false);
})

function unsave(){
	var code = $('#return_code').val();
	$.ajax({
		url:HOME + 'unsave/'+code,
		type:'POST',
		cache:false,
		success:function(rs){
			if(rs === 'success'){
				swal({
					title:'Success',
					text:'ยกเลิกการบันทึกเรียบร้อยแล้ว',
					type:'success',
					time:1000
				});

				setTimeout(function(){
					goEdit(code);
				}, 1500);
			}
		}
	})
}


function save()
{
	var error = 0;
	$('.input-price').each(function(){
		let price = parseFloat($(this).val());
		if(isNaN(price)){
			error++;
			swal('กรุณาใสราคาให้ครบถ้วน');
			$(this).addClass('has-error');
			return false;
		}else{
			$(this).removeClass('has-error');
		}
	});

	$('.input-qty').each(function(){
		let qty = parseFloat($(this).val());
		if(isNaN(qty) || qty == 0){
			error++;
			swal('กรุณาใส่จำนวนให้ครบถ้วน');
			$(this).addClass('has-error');
			return false;
		}
	});

	if(error == 0){
		$('#detailsForm').submit();
	}
}



function approve(){
	var code = $('#return_code').val();
	$.get(HOME+'approve/'+code, function(rs){
		if(rs === 'success'){
			swal({
				title:'Success',
				type:'success',
				timer: 1000
			});

			$('#btn-approve').remove();
		}else{
			swal({
				title:'Error',
				text:rs,
				type:'error'
			})
		}
	});
}



function doExport(){
	var code = $('#return_code').val();
	$.get(HOME + 'export_return/'+code, function(rs){
		if(rs === 'success'){
			swal({
				title:'Success',
				text:'ส่งข้อมูลไป SAP สำเร็จ',
				type:'success',
				timer:1000
			});
			setTimeout(function(){
				viewDetail(code);
			}, 1500);
		}
	});
}



function editHeader(){
	$('.edit').removeAttr('disabled');
	$('#btn-edit').addClass('hide');
	$('#btn-update').removeClass('hide');
}


function updateHeader(){
	var code = $('#return_code').val();
	var date_add = $('#dateAdd').val();
	var invoice = $('#invoice').val();
	var customer_code = $('#customer_code').val();
	var warehouse_code = $('#warehouse_code').val();
	var zone_code = $('#zone_code').val();
  var remark = $('#remark').val();

	if(!isDate(date_add)){
    swal('วันที่ไม่ถูกต้อง');
    return false;
  }

	if(invoice.length == 0){
		swal('กรุณาอ้างอิงเลขที่บิล');
		return false;
	}

	if(customer_code.length == 0){
		swal('กรุณาอ้างอิงลูกค้า');
		return false;
	}

	if(warehouse_code.length == 0){
		swal('กรุณาระบุคลังสินค้า');
		return false;
	}

	if(zone_code.length == 0){
		swal('กรุณาระบุโซนรับสินค้า');
		return false;
	}

  load_in();
	$.ajax({
		url:HOME + 'update',
		type:'POST',
		cache:false,
		data:{
			'return_code' : code,
			'date_add' : date_add,
			'invoice' : invoice,
			'customer_code' : customer_code,
			'warehouse_code' : warehouse_code,
			'zone_code' : zone_code,
			'remark' : remark
		},
		success:function(rs){
			load_out();
			if(rs == 'success'){
				$('.edit').attr('disabled', 'disabled');
				$('#btn-update').addClass('hide');
				$('#btn-edit').removeClass('hide');

				swal({
					title:'Success',
					text:'ต้องการโหลดข้อมูลรายการสินค้าใหม่หรือไม่ ?',
					type: 'success',
					showCancelButton: true,
					cancelButtonText: 'No',
					confirmButtonText: 'Yes',
					closeOnConfirm: false
				}, function(){
					window.location.reload();
				});
			}
			else
			{
				swal({
					title:'Error!!',
					text:rs,
					type:'error'
				});
			}
		}
	})
}



$('#dateAdd').datepicker({
	dateFormat:'dd-mm-yy'
});



function addNew()
{
  var date_add = $('#dateAdd').val();
	var invoice = $('#invoice').val();
	var customer_code = $('#customer_code').val();
	//var warehouse_code = $('#warehouse_code').val();
	var zone_code = $('#zone_code').val();

  if(!isDate(date_add)){
    swal('วันที่ไม่ถูกต้อง');
    return false;
  }

	if(invoice.length == 0){
		swal('กรุณาอ้างอิงเลขที่บิล');
		return false;
	}

	if(customer_code.length == 0){
		swal('กรุณาอ้างอิงลูกค้า');
		return false;
	}

	// if(warehouse_code.length == 0){
	// 	swal('กรุณาระบุคลังสินค้า');
	// 	return false;
	// }

	if(zone_code.length == 0){
		swal('กรุณาระบุโซนรับสินค้า');
		return false;
	}

  $('#addForm').submit();
}



$('#warehouse').autocomplete({
	source:BASE_URL + 'auto_complete/get_warehouse_code_and_name',
	autoFocus:true,
	close:function(){
		var arr = $(this).val().split(' | ');
		if(arr.length == 2){
			$('#warehouse_code').val(arr[0]);
			$('#warehouse').val(arr[1]);
			zoneInit();
		}else{
			$('#warehouse_code').val('');
			$('#warehouse').val('');
		}
	}
});


$('#customer').autocomplete({
	source:BASE_URL + 'auto_complete/get_customer_code_and_name',
	autoFocus:true,
	close:function(){
		var arr = $(this).val().split(' | ');
		if(arr.length == 2){
			$('#customer_code').val(arr[0]);
			$('#customer').val(arr[1]);
		}else{
			$('#customer_code').val('');
			$('#customer').val('');
		}
	}
});


// function zoneInit(){
// 	$('#zone_code').val('');
// 	$('#zone').val('');
// 	var warehouse = $('#warehouse_code').val();
// 	if(warehouse.length > 0){
// 		$('#zone').autocomplete({
// 			source : BASE_URL + 'auto_complete/get_zone_code_and_name/'+warehouse,
// 			autoFocus:true,
// 			close:function(){
// 				var arr = $(this).val().split(' | ');
// 				if(arr.length == 2){
// 					$(this).val(arr[1]);
// 					$('#zone_code').val(arr[0]);
// 				}else{
// 					$(this).val('');
// 					$('#zone_code').val('');
// 				}
// 			}
// 		})
// 	}
// }

$('#zone').autocomplete({
	source : BASE_URL + 'auto_complete/get_zone_code_and_name',
	autoFocus:true,
	close:function(){
		var arr = $(this).val().split(' | ');
		if(arr.length == 2){
			$(this).val(arr[1]);
			$('#zone_code').val(arr[0]);
		}else{
			$(this).val('');
			$('#zone_code').val('');
		}
	}
})


function inputQtyInit(){
	$('.input-qty').keyup(function(index) {
		var arr = $(this).attr('id').split('_');
		var code = arr[1];
		var inv = arr[2];
		var price = parseFloat($('#price_'+code+'_'+inv).val());
		var qty = parseFloat($(this).val());
		var discount = parseFloat($('#discount_'+code+'_'+inv).val()) * 0.01;
		price = isNaN(price) ? 0 : price;
		qty = isNaN(qty) ? 0 : qty;
		discount = qty * (price * discount);
		var amount = (qty * price) - discount;
		amount = amount.toFixed(2);
		$('#amount_'+code+'_'+inv).text(addCommas(amount));
		recalTotal();
	});
}

//
// function inputPriceInit(){
// 	$('.input-price').keyup(function(index) {
// 		var arr = $(this).attr('id').split('_');
// 		var code = arr[1];
// 		var inv = arr[2];
// 		var price = parseFloat($('#qty_'+code+'_'+inv).val());
// 		var qty = parseFloat($(this).val());
// 		price = isNaN(price) ? 0 : price;
// 		qty = isNaN(qty) ? 0 : qty;
// 		var amount = (qty * price).toFixed(2);
// 		$('#amount_'+code+'_'+inv).text(addCommas(amount));
// 		recalTotal();
// 	});
// }



$(document).ready(function(){
	inputQtyInit();
	//inputPriceInit();
});

function recalTotal(){
	var totalAmount = 0;
	var totalQty = 0;
	$('.amount-label').each(function(){
		let amount = removeCommas($(this).text());
		amount = parseFloat(amount);
		totalAmount += amount;
	});

	$('.input-qty').each(function(){
		let qty = $(this).val();
		qty = parseFloat(qty);
		totalQty += qty;
	});

	$('#total-qty').text(addCommas(totalQty));
	$('#total-amount').text(addCommas(totalAmount));
}



function removeRow(rowCode, id){
	if(id != ''){
		$.ajax({
			url:HOME + 'delete_detail/'+id,
			type:'GET',
			cache:false,
			success:function(rs){
				if(rs == 'success'){
					$('#row_' + rowCode).remove();
					reIndex();
					recalTotal();
				}
				else
				{
					swal(rs);
					return false;
				}
			}
		});
	}
	else
	{
		$('#row_'+rowCode).remove();
		reIndex();
		recalTotal();
	}
}

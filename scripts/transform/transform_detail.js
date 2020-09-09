//---	ลบสินค้าที่เชื่อมโยงแล้วออกจากรายการ
function removeTransformProduct(id_order_detail, product_code){
	swal({
		title:'คุณแน่ใจ ?',
		text: 'ต้องการลบ '+ product_code + ' หรือไม่ ?',
		type: 'warning',
		showCancelButton:true,
		cancelButtonText:'ยกเลิก',
		confirmButtonText: 'ลบรายการ',
		confirmButtonColor:'#FA5858',
		closeOnConfirm:false
	}, function(){
		$.ajax({
			url:HOME + 'remove_transform_product',
			type:'POST',
			cache:'false',
			data:{
				'id_order_detail' : id_order_detail,
				'product_code' : product_code
			},
			success:function(rs){
				var rs = $.trim(rs);
				if( isJson(rs)){
					var ds = $.parseJSON(rs);
					$('#transform-box-'+id_order_detail).html(ds.data);
					swal({
						title:'success',
						type:'success',
						timer:1000
					});

				}else{
					swal('Error !', rs, 'error');
				}
			}
		});
	});
}





//---	ตรวจสอบสินค้าที่เชื่อมโยงว่าครบหรือไม่ก่อนบันทึกออเดอร์
function validateTransformProducts(){
	var sc = true;
	$('.connect').each(function(index, el) {
		var arr = $(this).attr('id').split('-');
		var id = arr[2];
		var qty = parseInt( removeCommas($('#qty-'+id).text() ) );

		var trans_qty = parseInt($('#transform-qty-'+id).val());


		if( qty != trans_qty){
			sc = false;
		}
	});

	return sc;
}





function addToTransform(){
	var order_code = $('#order_code').val();
	var id_order_detail = $('#id_order_detail').val();
	var transform_product = $('#transform_product').val();
	var original_product = $('#original_product').val();
	var product_code = $('#trans-product').val();
	var qty = parseInt($('#trans-qty').val());
	var limit = parseInt($('#detail-qty').val());

	if( id_order_detail == ''){
		$('#transform-modal').modal('hide');
		swal('ไม่พบตัวแปร ID ORDER DETAIL');
		return false;
	}

	if( isNaN(qty) || qty < 1 || qty > limit){
		$('#qty-error').text('จำนวนไม่ถูกต้อง');
		$('#qty-error').removeClass('not-show');
		$('#trans-qty').focus();
		return false;
	}else{
		$('#qty-error').addClass('not-show');
	}

	if( transform_product == '' || product_code == ''){
		$('#product-error').text('สินค้าไม่ถูกต้อง');
		$('#product-error').removeClass('not-show');
		$('#trans-product').focus();
		return false;
	}else{
		$('#product-error').addClass('not-show');
	}

	$.ajax({
		url:HOME + 'add_transform_product',
		type:'POST',
		cache:'false',
		data:{
			'order_code' : order_code,
			'id_order_detail' : id_order_detail,
			'original_product' : original_product,
			'transform_product' : transform_product,
			'qty' : qty
		},
		success:function(rs){
			//--- ถ้าสำเร็จได้ JSON มาทำการ Update ตารางเลย
			var rs = $.trim(rs);
			if( isJson(rs)){
				var ds = $.parseJSON(rs);
				$('#transform-box-'+id_order_detail).html(ds.data);
				clearFields();
				$('#transform-modal').modal('hide');
			}else{
				swal('Error !', rs, 'error');
			}
		}
	});
}




function clearFields(){
	$('#id_order_detail').val('');
	$('#transform_product').val('');
	$('#detail-qty').val('');
	$('#trans-qty').val('');
	$('#trans-product').val('');
}


//----- แก้ไขรายการเชื่อมโยงสินค้า
function editTransformProduct(id_order_detail, transform_product, limit){

}





//---- 	เปิดกล่องเชื่อมโยงสินค้า
function addTransformProduct(id, original_product){
	//---	id = id_order_detail
	//---	จำนวนที่สั่ง
	var qty = parseInt(removeCommas($('#qty-'+id).text()));

	//---	จำนวนที่เชื่อมโยงแล้ว
	var trans_qty = isNaN(parseInt($('#transform-qty-'+id).val())) ? 0 : parseInt($('#transform-qty-'+id).val());

	//---	จำนวนคงเหลือที่จะเชื่อมโยงได้
	var available_qty = qty - trans_qty;

	$('#id_order_detail').val(id);

	$('#detail-qty').val(available_qty);

	$('#transform_product').val('');

	$('#original_product').val(original_product);

	$('#trans-qty').val(available_qty);

	$('#transform-modal').modal('show');
}


$('#transform-modal').on('shown.bs.modal', function(){
	$('#trans-product').focus();
});



$('#trans-product').autocomplete({
	source:BASE_URL + 'auto_complete/get_product_code',
	autoFocus:true,
	close:function(){
		var rs = $(this).val();
	   if(rs === 'no item found')
     {
       $('#transform_product').val('');
       $(this).val('');
     }
     else
     {
       $('#transform_product').val(rs);
     }
	}
});



//---	หากมีการติ๊กถูกตรงช่องไม่คืน (สินค้าใช้แปรสภาพแล้วหมดไป)
//---	สินค้านี้จะไม่ต้องเชื่อมโยงว่าจะแปรเป็นอะไร
//---	แต่หากมีการเชื่อมโยงไว้แล้ว ต้องแจ้งเตือนว่าจะต้องเอาออก
function isConnected(id){
	//---	ตรวจสอบว่ามีการเชื่อมโยงบ้างหรือไม่
	$.ajax({
		url:HOME + 'is_exists_connected',//'controller/transformController.php?isExistsConnected',
		type:'GET',
		cache:'false',
		data:{
      'id_order_detail' : id
    },
		success:function(rs){
			var rs = $.trim(rs);
			//---	ถ้ามีการเชื่อมโยงอยู่ แจ้งเตือนการลบ
			if(rs == 'exists'){
				swal({
					title:'รายการที่เชื่อมโยงไว้จะถูกลบ',
					text: 'ต้องการดำเนินการต่อหรือไม่ ?',
					type:'warning',
					showCancelButton:true,
					confirmButtonText:'ดำเนินการ',
					closeOnConfirm:true
				},
				//---	หากยืนยันการลบ
				function(isConfirm){

          if(isConfirm){
            //---	ลบรายการเชื่อมโยง
  					$.ajax({
  						url: HOME + 'remove_transform_detail', //'controller/transformController.php?removeTransformDetail',
  						type:'POST',
  						cache:'false',
  						data:{
  							'id_order_detail' : id,
  						},
  						success:function(sc){
  							var sc = $.trim(sc);
  							if( sc == 'success'){
  								//---	ลบสำเร็จ
  								//---	ลบรายการเชื่อมโยงหน้าเว็บออก
  								$('#transform-box-'+id).html('');

  								//---	เอาปุ่มเชื่อมโยงออก
  								removeButton(id);
  							}else{
  								//---	แจ้งข้อผิดพลาด
  								swal('Error !', rs, 'error');
  							}
  						}
  					});
          }
          else
          {
            $('#chk-'+id).prop('checked', false);
          }

				});

			}else{
				//---	หากไม่มีการเชื่อมโยงไว้
				//---	เอาปุ่มเชื่อมโยงออกได้เลย
				removeButton(id);
			}
		}
	})
}


//---	เมื่อติ๊กถูกหรือติ๊กออก ช่องไม่คืนสินค้า
function toggleReturn(id, productCode){

	var chk = $('#chk-'+id);

	//---	ถ้าติ๊กถูก
	if( chk.is(':checked')){
		isConnected(id);

	}else{
	//---	ถ้าติ๊กออก
	addButton(id, productCode);

	}
}



function removeButton(id){
	$('#btn-connect-'+id).remove();
}


function addButton(id, productCode){
	if($('#btn-connect-'+id).length == 0){
		$('#connect-box-'+id).html('<button type="button" class="btn btn-xs btn-success btn-block connect" id="btn-connect-'+id+'" onclick="addTransformProduct('+id+', \''+productCode+'\')"><i class="fa fa-plus"></i> เชื่อมโยง</button>')
	}
}


$('#trans-qty').focusout(function(){
	var input_qty = parseInt($(this).val());
	var limit = parseInt($('#detail-qty').val());
	if( isNaN(input_qty) || input_qty < 1 || input_qty > limit){
		$('#qty-error').text('ได้ไม่เกิน '+limit+' หน่วย');
		$('#qty-error').removeClass('not-show');
		$(this).val(limit);
	}else{
		$('#qty-error').addClass('not-show');
	}
});

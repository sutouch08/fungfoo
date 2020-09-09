function removeAddress(id)
{
	swal({
		title: 'ต้องการลบที่อยู่ ?',
		text: 'คุณแน่ใจว่าต้องการลบที่อยู่นี้ โปรดจำไว้ว่าการกระทำนี้ไม่สามารถกู้คืนได้',
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#DD6855',
		confirmButtonText: 'ใช่ ลบเลย',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
			$.ajax({
				url:BASE_URL + 'orders/orders/delete_shipping_address',
				type:"POST",
				cache:"false",
				data:{
					"id_address" : id
				},
				success: function(rs){
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({ title : "สำเร็จ", text: "ลบรายการเรียบร้อยแล้ว", timer: 1000, type: "success" });
						reloadAddressTable();
					}else{
						swal("ข้อผิดพลาด!!", "ลบรายการไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");
					}
				}
			});
		});
}





//----------  edit address  -----------//
function editAddress(id)
{
	$.ajax({
		url:BASE_URL + 'orders/orders/get_shipping_address',
		type:"POST",
		cache:"false",
		data:{
			"id_address" : id
		},
		success: function(rs){
			var rs = $.trim(rs);
			if( isJson(rs) ){
				var ds = $.parseJSON(rs);
				$("#id_address").val(ds.id);
				$("#Fname").val(ds.name);
				$("#address1").val(ds.address);
				$("#sub_district").val(ds.sub_district);
				$('#district').val(ds.district);
				$("#province").val(ds.province);
				$("#postcode").val(ds.postcode);
				$("#phone").val(ds.phone);
				$("#email").val(ds.email);
				$("#alias").val(ds.alias);
				$("#addressModal").modal('show');
			}else{
				swal("ข้อผิดพลาด!", "ไม่พบข้อมูลที่อยู่", "error");
			}
		}
	});
}





//--------- set address as default address  ------------------//
function setDefault(id)
{
	var customer_ref = $('#customers_code').val();
	$.ajax({
		url:BASE_URL + 'orders/orders/set_default_address',
		type:"POST",
		cache:"false",
		data:{
			"id_address" : id,
			"customer_ref" : customer_ref
		},
		success: function(rs){
			$(".btn-address").removeClass('btn-success');
			$("#btn-"+id).addClass('btn-success');
		}
	});
}





function reloadAddressTable()
{
	var order_code = $("#order_code").val();
	var customer_ref = $('#customers_code').val();
	$.ajax({
		url:BASE_URL + 'orders/orders/get_address_table',
		type:"POST",
		cache:"false",
		data:{
			'customer_ref' : customer_ref
		},
		success: function(rs){
			var rs = $.trim(rs);
			if(isJson(rs)){
				var source 	= $("#addressTableTemplate").html();
				var data 		= $.parseJSON(rs);
				var output 	= $("#adrs");
				render(source, data, output);
			}else{
				$("#adrs").html('<tr><td colspan="7" align="center">ไม่พบที่อยู่</td></tr>');
			}
		}
	});
}






function saveAddress()
{
	var code 			= $('#customers_code').val();
	var name			= $("#Fname").val();
	var addr			= $("#address1").val();
	var subdistrict = $('#sub_district').val();
	var district  = $('#district').val();
	var province  = $('#province').val();
	var email			= $("#email").val();
	var alias 		= $("#alias").val();

	if(code == ''){
		swal('กรุณาระบุชื่อลูกค้า[ออนไลน์]');
		return false;
	}


	if( name == '' ){
		swal('กรุณาระบุชื่อผู้รับ');
		return false;
	}

	if( addr.length == 0 ){
		swal('กรุณาระบุที่อยู่');
		return false;
	}

	if(subdistrict.length == 0){
		swal('กรุณาระบุตำบล');
		return false;
	}


	if(district.length == 0){
		swal('กรุณาระบุอำเภอ');
		return false;
	}

	if(province.length == 0){
		swal('กรุณาระบุจังหวัด');
		return false;
	}


	if( alias == '' ){
		swal('กรุณาตั้งชื่อให้ที่อยู่');
		return false;
	}

	if( email != '' && ! validEmail(email) ){
		swal("อีเมล์ไม่ถูกต้องกรุณาตรวจสอบ");
		return false;
	}

	var ds = [];

	ds.push( {"name" : "id_address", "value" : $("#id_address").val() } );
	ds.push( {"name" : "customer_ref", "value" : code } );
	ds.push( {"name" : "name", "value" : name } );
	ds.push( {"name" : "address", "value" : $("#address1").val() } );
	ds.push( {"name" : "sub_district", "value" : $("#sub_district").val() } );
	ds.push( {"name" : "district", "value" : $("#district").val() } );
	ds.push( {"name" : "province", "value" : $("#province").val() } );
	ds.push( {"name" : "postcode", "value" : $("#postcode").val() } );
	ds.push( {"name" : "phone", "value" : $("#phone").val() } );
	ds.push( {"name" : "email", "value" : $("#email").val() } );
	ds.push( {"name" : "alias", "value" : $("#alias").val() } );

	$("#addressModal").modal('hide');

	load_in();
	$.ajax({
		url:BASE_URL + 'orders/orders/save_address',
		type:"POST",
		cache:"false",
		data: ds,
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if(rs === 'success'){
				reloadAddressTable();
				clearAddressField();
			}else{
				swal({
					title:'ข้อผิดพลาด',
					text:rs,
					type:'error'
				});
				$("#addressModal").modal('show');
			}
		}
	});
}





function addNewAddress()
{
	clearAddressField();
	$("#addressModal").modal('show');
}



$('#sub_district').autocomplete({
	source:BASE_URL + 'auto_complete/sub_district',
	autoFocus:true,
	open:function(event){
		var $ul = $(this).autocomplete('widget');
		$ul.css('width', 'auto');
	},
	close:function(){
		var rs = $.trim($(this).val());
		var adr = rs.split('>>');
		if(adr.length == 4){
			$('#sub_district').val(adr[0]);
			$('#district').val(adr[1]);
			$('#province').val(adr[2]);
			$('#postcode').val(adr[3]);
		}
	}
});


$('#district').autocomplete({
	source:BASE_URL + 'auto_complete/district',
	autoFocus:true,
	open:function(event){
		var $ul = $(this).autocomplete('widget');
		$ul.css('width', 'auto');
	},
	close:function(){
		var rs = $.trim($(this).val());
		var adr = rs.split('>>');
		if(adr.length == 3){
			$('#district').val(adr[0]);
			$('#province').val(adr[1]);
			$('#postcode').val(adr[2]);
		}
	}
});


$('#province').autocomplete({
	source:BASE_URL + 'auto_complete/district',
	autoFocus:true,
	open:function(event){
		var $ul = $(this).autocomplete('widget');
		$ul.css('width', 'auto');
	},
	close:function(){
		var rs = $.trim($(this).val());
		var adr = rs.split('>>');
		if(adr.length == 2){
			$('#province').val(adr[0]);
			$('#postcode').val(adr[1]);
		}
	}
})



function clearAddressField()
{
	$("#id_address").val('');
	$("#Fname").val('');
	$("#address1").val('');
	$('#sub_district').val('');
	$('#district').val('');
	$("#province").val('');
	$("#postcode").val('');
	$("#phone").val('');
	$("#email").val('');
	$("#alias").val('');
}



//-------  ดึงรายการสินค้าในโซน
function getProductInZone(){
	var zone_code  = $("#from_zone_code").val();
	var move_code = $('#move_code').val();
	if( zone_code.length > 0 ){
		$.ajax({
			url: HOME + 'get_product_in_zone',
			type:"GET",
      cache:"false",
      data:{
				'move_code' : move_code,
        'zone_code' : zone_code
      },
			success: function(rs){
				var rs = 	$.trim(rs);
				if( isJson(rs) ){
					var source = $("#zoneTemplate").html();
					var data		= $.parseJSON(rs);
					var output	= $("#zone-list");
					render(source, data, output);
					$("#move-table").addClass('hide');
					$("#zone-table").removeClass('hide');
					inputQtyInit();
				}
			}
		});
	}
}


$(document).ready(function() {
	from_zone_init();
	to_zone_init();
});



function from_zone_init(){
	var code = $('#from_warehouse_code').val();
	$("#from-zone").autocomplete({
		source: HOME + 'get_move_zone/'+ code,
		autoFocus: true,
		close: function(){
			var rs = $(this).val();
			var rs = rs.split(' | ');
			if( rs.length == 2 ){
				$("#from_zone_code").val(rs[0]);
	      //--- แสดงชื่อโซนใน text box
				$(this).val(rs[1]);
				//---	แสดงชื่อโซนที่ หัวตาราง
				$('#zoneName').text(rs[1]);
			}else{

				$("#from_zone_code").val('');
				//---	ชื่อโซนที่ หัวตาราง
				$('#zoneName').text('');
				$(this).val('');
			}
		}
	});
}



$("#from-zone").keyup(function(e) {
    if( e.keyCode == 13 ){
		setTimeout(function(){
			getProductInZone();
		}, 100);
	}
});



function to_zone_init(){
	var code = $('#to_warehouse_code').val();
	$("#to-zone").autocomplete({
		source: HOME + 'get_move_zone/' + code,
		autoFocus: true,
		close: function(){
			var rs = $(this).val();
			var rs = rs.split(' | ');
			if( rs.length == 2 ){
				$("#to_zone_code").val(rs[0]);
				$(this).val(rs[1]);
			}else{
				$("#to_zone_code").val('');
				$(this).val('');
			}
		}
	});

}



//------- สลับไปแสดงหน้า move_detail
function showMoveTable(){
	getMoveTable();
	hideZoneTable();
	hideTempTable();
	showControl();
	hideMoveIn();
	hideMoveOut();
	$("#move-table").removeClass('hide');
}



function hideMoveTable(){
	$("#move-table").addClass('hide');
}


function showMoveIn(){
	$(".moveIn-zone").removeClass('hide');
}


function hideMoveIn(){
	$(".moveIn-zone").addClass('hide');
}


function showMoveOut(){
	$(".moveOut-zone").removeClass('hide');
}



function hideMoveOut(){
	$(".moveOut-zone").addClass('hide');
}



function showControl(){
	$(".control-btn").removeClass('hide');
}


function hideControl(){
	$(".control-btn").addClass('hide');
}


function showTempTable(){
	getTempTable();
	hideMoveTable();
	hideZoneTable();
	$("#temp-table").removeClass('hide');
}



function hideTempTable(){
	$("#temp-table").addClass('hide');
}



function showZoneTable(){
	$("#zone-table").removeClass('hide');
}



function hideZoneTable(){
	$("#zone-table").addClass('hide');
}



function inputQtyInit(){
	$('.input-qty').keyup(function(){
		var qty = parseInt($(this).val());
		var limit = parseInt($(this).attr('max'));
		qty = isNaN(qty) ? 0 : qty;
		limit = isNaN(limit) ? 0 : limit;

		if(qty > limit)
		{
			swal('โอนได้ไม่เกิน ' + limit);
			$(this).val(limit);
		}
	})
}





function getMoveIn(){
	$(".moveIn-zone").removeClass('hide');
	$('#barcode-hr').removeClass('hide');

	$(".moveOut-zone").addClass('hide');
	$(".control-btn").addClass('hide');

	hideMoveTable();
	getTempTable();
	showTempTable();
	$("#toZone-barcode").focus();
}



//---	เปลี่ยนโซนปลายทาง
function newToZone(){
	$('#toZone-barcode').removeAttr('disabled');
	$('#btn-new-to-zone').attr('disabled','disabled');
	$('#zoneName-label').text('');
	$("#to_zone_code").val("");
	$("#toZone-barcode").val("");
	$("#zone-table").addClass('hide');
	$("#toZone-barcode").focus();
}





//---	ดึงข้อมูลสินค้าในโซนต้นทาง
function getZoneTo(){

	var barcode = $("#toZone-barcode").val();

	if( barcode.length > 0 ){
		//---	คลังปลายทาง
		var warehouse_code = $("#to_warehouse_code").val();

		$.ajax({
			url: BASE_URL + 'masters/zone/get_warehouse_zone',
			type:"GET",
			cache:"false",
			data:{
				"barcode" : barcode,
				"warehouse_code" : warehouse_code
			},
			success: function(rs){

				var rs = $.trim(rs);

				if( isJson(rs) ){

					//---	รับข้อมูลแล้วแปลงจาก json
					var ds = $.parseJSON(rs);

					//---	update id โซนปลายทาง
					$("#to_zone_code").val(ds.code);

					//---	update ชื่อโซน
					$("#zoneName-label").val(ds.name);

					//---	disabled ช่องยิงบาร์โค้ดโซน
					$("#toZone-barcode").attr('disabled', 'disabled');

					//--- active new zone button
					$('#btn-new-to-zone').removeAttr('disabled');

					$('#qty-to').removeAttr('disabled');

					$('#barcode-item-to').removeAttr('disabled');

					$('#barcode-item-to').focus();

					showTempTable();

				}else{

					swal("ข้อผิดพลาด", rs, "error");

					//---	ลบไอดีโซนปลายทาง
					$("#to_zone_code").val("");

					//---	ไม่แสดงชื่อโซน
					$('#zoneName-label').val('');

					//--- disabled new zone buton
					$('#btn-new-to-zone').attr('disabled');

					//--- ซ่อนตารางสินค้าในโซน
					$("#zone-table").addClass('hide');

					beep();
				}
			}
		});
	}
}




$("#toZone-barcode").keyup(function(e) {
    if( e.keyCode == 13 ){
		getZoneTo();
		setTimeout(function(){ $("#barcode-item-to").focus(); }, 500);
	}
});



$("#barcode-item-to").keyup(function(e) {
    if( e.keyCode == 13 ){

		//---	บาร์โค้ดสินค้าที่ยิงมา
		var barcode = $(this).val();

		//---	ไอดีโซนปลายทาง
		var zone_code	= $("#to_zone_code").val();

		//---	ไอดีเอกสาร
		var move_code = $("#move_code").val();

		if( zone_code.length == 0 ){
			swal("กรุณาระบุโซนปลายทาง");
			return false;
		}

		var qty = parseInt($("#qty-to").val());

		var curQty	= parseInt($("#qty-"+barcode).val());

		$(this).val('');

		if( isNaN(curQty) ){
			swal("สินค้าไม่ถูกต้อง");
			return false;
		}



		if( qty != '' && qty != 0 ){
			if( qty <= curQty ){
				$.ajax({
					url: HOME + 'move_to_zone', //"controller/moveController.php?moveBarcodeToZone",
					type:"POST",
					cache:"false",
					data:{
						"move_code" : move_code,
						"zone_code" : zone_code,
						"qty" : qty,
						"barcode" : barcode
					},
					success: function(rs){
						var rs = $.trim(rs);
						if( rs == 'success'){
							curQty = curQty - qty;
							if(curQty == 0 ){
								getTempTable();
							}else{
								$("#qty-label-"+barcode).text(curQty);
								$("#qty-"+barcode).val(curQty);
							}
							$("#qty-to").val(1);
							$("#barcode-item-to").focus();
						}else{
							swal("ข้อผิดพลาด", rs, "error");
						}
					}
				});
			}else{
				swal("จำนวนในโซนไม่เพียงพอ");
			}
		}
	}
});






//-------	เปิดกล่องควบคุมสำหรับยิงบาร์โค้ดโซนต้นทาง
function getMoveOut(){

	$(".moveIn-zone").addClass('hide');
	$(".control-btn").addClass('hide');
	$("#moveIn-input").addClass('hide');
	$("#move-table").addClass('hide');

	$('#barcode-hr').removeClass('hide');
	$(".moveOut-zone").removeClass('hide');
	$("#zone-table").removeClass('hide');
	$("#fromZone-barcode").focus();
}



//---	เปลี่ยนโซนต้นทาง
function newFromZone(){
	$("#from_zone_code").val("");
	$("#fromZone-barcode").val("");
	$("#zone-table").addClass('hide');
	$('#fromZone-barcode').removeAttr('disabled');
	$('#btn-new-zone').attr('disabled', 'disabled');
	$('#qty-from').attr('disabled', 'disabled');
	$('#barcode-item-from').attr('disabled', 'disabled');
	$("#fromZone-barcode").focus();
}




//---	ดึงข้อมูลสินค้าในโซนต้นทาง
function getZoneFrom(){

	var barcode = $("#fromZone-barcode").val();

	if( barcode.length > 0 ){
		//---	คลังต้นทาง
		var warehouse_code = $("#from_warehouse_code").val();

		$.ajax({
			url:BASE_URL + 'masters/zone/get_warehouse_zone',
			type:"GET",
			cache:"false",
			data:{
				"barcode" : barcode,
				"warehouse_code" : warehouse_code
			},
			success: function(rs){

				var rs = $.trim(rs);

				if( isJson(rs) ){

					//---	รับข้อมูลแล้วแปลงจาก json
					var ds = $.parseJSON(rs);

					//---	update id โซนต้นทาง
					$("#from_zone_code").val(ds.code);

					//---	update ชื่อโซน
					$("#zoneName").text(ds.name);


					$("#fromZone-barcode").attr('disabled', 'disabled');
					$('#btn-new-zone').removeAttr('disabled');
					$('#qty-from').removeAttr('disabled');
					$('#barcode-item-from').removeAttr('disabled');
					$('#barcode-item-from').focus();

					//---	แสดงรายการสินค้าในโซน
					getProductInZone();

				}else{
					swal("ข้อผิดพลาด", rs, "error");

					//---	ลบไอดีโซนต้นทาง
					$("#from_zone_code").val("");

					//---	ไม่แสดงชื่อโซน
					$('#zoneName').val('');

					$("#zone-table").addClass('hide');

					beep();
				}
			}
		});
	}
}



$("#fromZone-barcode").keyup(function(e) {
    if( e.keyCode == 13 ){
		getZoneFrom();
		setTimeout(function(){ $("#barcode-item-from").focus(); }, 500);
	}
});


//------------------------------------- ยิงบาร์โค้ดสินค้า

$("#barcode-item-from").keyup(function(e) {
  if( e.keyCode == 13 ){
		//---	โซนต้นทาง
		var zone_code	= $("#from_zone_code").val();

		//---	ID เอกสาร
		var move_code = $("#move_code").val();

		//---	ตรวจสอบว่ายิงบาร์โค้ดโซนมาแล้วหรือยัง
		if( zone_code.length == 0 ){
			swal("กรุณาระบุโซนปลายทาง");
			return false;
		}

		//---	จำนวนที่ป้อนมา
		var qty = parseInt($("#qty-from").val());

		//---	บาร์โค้ดสินค้า
		var barcode = $(this).val();

		//---	จำนวนที่เพิ่มไปแล้ว
		var curQty	= parseInt($("#qty_"+barcode).val());

		//---	เคลียร์ช่องให้พร้อมยิงตัวต่อไป
		$(this).val('');

		//---	เมื่อมีการใส่จำนวนมาตามปกติ
		if( qty != '' && qty != 0 ){

			//---	ถ้าจำนวนที่ใส่มา น้อยกว่าหรือเท่ากับ จำนวนที่มีอยู่
			//---	หรือ โซนนี้สามารถติดลบได้และติ๊กว่าให้ติดลบได้
			//---	หากโซนนี้ไม่สามารถติดลบได้ ถึงจะติ๊กให้ติดลบได้ก็ไม่สามารถให้ติดลบได้
			if( qty <= curQty ){
				//---	เพิ่มรายการเข้า temp
				$.ajax({
					url: HOME + 'add_to_temp',
					type:"POST",
					cache:"false",
					data:{
						"move_code" : move_code,
						"from_zone" : zone_code,
						"qty" : qty,
						"barcode" : barcode,
					},
					success: function(rs){

						var rs = $.trim(rs);

						if( rs == 'success'){

							//--- ลดยอดสินค้าคงเหลือในโซนบนหน้าเว็บ (ในฐานข้อมูลถูกลดแล้ว)
							curQty = curQty - qty;

							//---	แสดงผลยอดสินค้าคงเหลือในโซน
							$("#qty-label_"+barcode).text(curQty);

							//---	ปรับยอดคงเหลือในโซน สำหรับใช้ตรวจสอบการยิงครั้งต่อไป
							$("#qty_"+barcode).val(curQty);

							//---	reset จำนวนเป็น 1
							$("#qty-from").val(1);

							//---	focus ที่ช่องยิงบาร์โค้ด รอการยิงต่อไป
							$("#barcode-item-from").focus();

						}else{

							swal("ข้อผิดพลาด", rs, "error");
						}
					}
				});
			}else{
				swal("จำนวนในโซนไม่เพียงพอ");
			}
		}
	}
});

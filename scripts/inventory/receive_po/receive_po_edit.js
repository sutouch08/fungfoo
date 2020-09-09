// JavaScript Document

function save(){
	var code = $('#code').val();
	load_in();
	$.ajax({
		url:HOME + 'save/'+code,
		type:'POST',
		cache:false,
		success:function(rs){
			load_out();
			if(rs === 'success'){
				swal({
					title:'Saved',
					text:'Save success',
					type:'success',
					timer:1000
				});

				setTimeout(function(){
					window.location.reload();
				}, 1500);
			}else{
				swal({
					title:'Error!',
					text: rs,
					type:'error'
				});
			}
		}
	});
}


function unSave(){
	var code = $('#code').val();

	swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการยกเลิกการบันทึก '" + code + "' หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'ใช่, ฉันต้อง',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
			$.ajax({
				url: HOME + 'unsave/'+ code,
				type:"POST",
        cache:"false",
				success: function(rs){
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({
              title: 'Unsaved',
              type: 'success',
              timer: 1000 });
						goEdit(code);
					}else{
						swal("Error !", rs , "error");
					}
				}
			});
	});
}



function editHeader(){
	$('.edit').removeAttr('disabled');
	$('#btn-edit').addClass('hide');
	$('#btn-update').removeClass('hide');
}

function updated(){
	$('.edit').attr('disabled', 'disabled');
	$('#btn-update').addClass('hide');
	$('#btn-edit').removeClass('hide');
}

function updateHeader(){
	var code = $('#code').val();
	var date_add = $('#dateAdd').val();
  var venderCode = $('#venderCode').val();
  var poCode = $('#poCode').val();
  var invoice = $('#invoice').val();
  var zoneCode = $('#zoneCode').val();
  var remark = $('#remark').val();

  if(!isDate(date_add)){
    swal('วันที่ไม่ถูกต้อง');
    return false;
  }

  if(venderCode.length == 0){
    swal('กรุณาระบุผู้ขาย');
    return false;
  }

  if(invoice.length == 0){
    swal('กรุณาระบุใบส่งสินค้า');
    return false;
  }

  if(zoneCode.length == 0){
    swal('กรุณาระบุโซนรับสินค้า');
    return false;
  }

  load_in();
  $.ajax({
    url:HOME + 'update',
    type:'POST',
    cache:false,
    data:{
			'code' : code,
      'date_add' : date_add,
      'venderCode' : venderCode,
      'poCode' : poCode,
      'invoice' : invoice,
      'zoneCode' : zoneCode,
      'remark' : remark
    },
    success:function(rs){
      load_out();
      var arr = $.parseJSON(rs);
      if(arr.status == 'error'){
        swal({
          title:'Error!!',
          text:arr.message,
          type:'error'
        });
      }else{
        updated();
      }
    }
  })
}


$('#poCode').keyup(function(e) {
	if(e.keyCode == 13){
		$('#invoice').focus();
	}
});


function removeRow(id, code){
	swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการลบ '" + code + "' หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
			$.ajax({
				url: HOME + 'delete_detail/'+ id,
				type:"POST",
        cache:"false",
				success: function(rs){
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({
              title: 'Deleted',
              type: 'success',
              timer: 1000 });
						window.location.reload();
					}else{
						swal("Error !", rs , "error");
					}
				}
			});
	});
}


function clearAll(){
	var code = $('#code').val();
	swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการลบรายการทั้งหมดหรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
			$.ajax({
				url: HOME + 'delete_details/'+ code,
				type:"POST",
        cache:"false",
				success: function(rs){
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({
              title: 'Deleted',
              type: 'success',
              timer: 1000
						 });
						setTimeout(function(){
							window.location.reload();
						}, 1500);
					}else{
						swal("Error !", rs , "error");
					}
				}
			});
	});
}

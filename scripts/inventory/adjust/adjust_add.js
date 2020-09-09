$('#date_add').datepicker({
  dateFormat:'dd-mm-yy'
});



function saveAdjust(){
  let code = $('#code').val();
  load_in();
  $.ajax({
    url:HOME + 'save',
    type:'POST',
    cache:false,
    data:{
      'code' : code
    },
    success:function(rs){
      load_out();
      if(rs === 'success'){
        swal({
          title:'Saved',
          text:'บันทึกรายการเรียบร้อยแล้ว',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          goDetail(code);
        }, 1200);
      }else{
        swal("Error", rs, 'error');
      }
    }
  })
}



function getEdit(){
  $('#date_add').removeAttr('disabled');
  $('#reference').removeAttr('disabled');
  $('#remark').removeAttr('disabled');
  $('#btn-edit').addClass('hide');
  $('#btn-update').removeClass('hide');
}



function updateHeader(){
  let code = $('#code').val();
  let date_add = $('#date_add').val();
  let reference = $('#reference').val();
  let remark = $('#remark').val();

  $.ajax({
    url:HOME + 'update',
    type:'POST',
    cache:false,
    data:{
      'code' : code,
      'date_add' : date_add,
      'reference' : reference,
      'remark' : remark
    },
    success:function(rs){
      if(rs == 'success'){
        swal({
          title:'Updated',
          text:'ปรับปรุงข้อมูลเรียบร้อยแล้ว',
          type:'success',
          timer:1000
        });

        $('#date_add').attr('disabled', 'disabled');
        $('#reference').attr('disabled', 'disabled');
        $('#remark').attr('disabled', 'disabled');
        $('#btn-edit').removeClass('hide');
        $('#btn-update').addClass('hide');
      }else{
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        });
      }
    }
  })
}



function add(){
  var date_add = $('#date_add').val();
  var reference = $('#reference').val();
  var remark = $('#remark').val();

  if(!isDate(date_add)){
    swal("วันที่ไม่ถูกต้อง");
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + 'add',
    type:'POST',
    cache:false,
    data:{
      'date_add' : date_add,
      'reference' : reference,
      'remark' : remark
    },
    success:function(rs){
      load_out();
      var rs = $.trim(rs);
      var arr = rs.split('|');
      if(arr.length === 2){
        goEdit(arr[1]);
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


$('#zone').autocomplete({
  source: BASE_URL + 'auto_complete/get_zone_code_and_name',
  autoFocus:true,
  close:function(){
    let rs = $(this).val();
    let arr = rs.split(' | ');
    if(arr.length == 2){
      let code = arr[0];
      let name = arr[1];
      $('#zone').val(name);
      $('#zone_code').val(code);
    }else{
      $('#zone').val('');
      $('#zone_code').val('');
    }
  }
})


$('#zone').keyup(function(e){
  if(e.keyCode === 13){
    set_zone();
  }
})


function set_zone(){
  let zone = $('#zone_code').val();
  if(zone.length > 0){
    $('#zone').attr('disabled', 'disabled');
    $('#btn-set-zone').addClass('hide');
    $('#btn-change-zone').removeClass('hide');

    $('#pd-code').removeAttr('disabled');
    $('#qty-up').removeAttr('disabled');
    $('#qty-down').removeAttr('disabled');
    $('#btn-add').removeAttr('disabled');
    $('#pd-code').focus();
  }
}


$('#pd-code').autocomplete({
  source: BASE_URL + 'auto_complete/get_item_code',
  autoFocus:true,
  close:function(){
    let code = $(this).val();
    if(code.length === 0 || code === 'not found'){
      $(this).val('');
    }
  }
});

$('#pd-code').keyup(function(e){
  if(e.keyCode === 13){
    let code = $(this).val();
    if(code.length === 0 || code === 'not found'){
      $(this).val('');
      return false;
    }

    $('#qty-up').focus();
  }
})


$('#qty-up').keyup(function(e){
  let down_qty = parseFloat($('#qty-down').val());
  let up_qty = parseFloat($(this).val());

  if(isNaN(up_qty) || up_qty < 0){
    $(this).val(0);
  }else{
    $(this).val(up_qty);
  }

  if(up_qty > 0 && down_qty != 0){
    $('#qty-down').val(0);
  }

  if(e.keyCode === 13){
    $('#qty-down').focus();
  }

});


$('#qty-down').keyup(function(e){
  let down_qty = parseFloat($(this).val());
  let up_qty = parseFloat($('#qty-up').val());

  if(isNaN(down_qty) || down_qty < 0){
    $(this).val(0);
  }else{
    $(this).val(down_qty);
  }

  if(down_qty > 0 && up_qty != 0){
    $('#qty-up').val(0);
  }

  if(e.keyCode === 13){
    add_detail();
  }
})


function add_detail(){
  let code = $('#code').val();
  let pd_code = $('#pd-code').val();
  let zone_code = $('#zone_code').val();
  let qty_up = $('#qty-up').val();
  let qty_down = $('#qty-down').val();

  if(code.length == 0){
    swal('ไม่พบเลขที่เอกสาร');
    return false;
  }

  if(pd_code.length == 0){
    swal('กรุณาระบุรหัสสินค้า');
    return false;
  }

  if(zone_code.length == 0){
    swal('กรุณาระบุโซน');
    return false;
  }

  if(qty_up == 0 && qty_down == 0){
    swal('กรุณาระบุจำนวนที่จะปรับยอด');
    return false;
  }

  $('#btn-add').attr('disabled');

  load_in();
  $.ajax({
    url:HOME + 'add_detail',
    type:'POST',
    cache:false,
    data:{
      'code' : code,
      'zone_code' : zone_code,
      'pd_code' : pd_code,
      'qty_up' : qty_up,
      'qty_down' : qty_down
    },
    success:function(rs){
      load_out();
      if(isJson(rs)){
        //--- แปลง json ให้เป็น object
        var ds = $.parseJSON(rs);

        //--  ตรวจสอบว่ามีรายการปรับยอดอยู่แล้วหรือไม่
        //--- ถ้ามีจะ update ยอด
        if( $('#row-' + ds.id ).length == 1){
          //--- update ยอดในรายการ
          $('#qty-up-'+ ds.id).text(ds.up);
          $('#qty-down-'+ ds.id).text(ds.down);

          //--- เติมสีน้ำเงินในแถวที่มีการเปลี่ยนแปลง
          setColor(ds.id);

          //--- Reset Input control พร้อมสำหรับรายการต่อไป
          getReady();

        }else{
          //--- ถ้ายังไม่มีรายการในตารางดำเนินการเพิ่มใหม่
          //--- ลำดับล่าสุด
          var no = getMaxNo() + 1;

          //--- เพิ่มจำนวนล่าสุดเข้าไปเพื่อใช้ render แถวใหม่
          ds.no = no;

          var source = $('#detail-template').html();
          var output = $('#detail-table');

          //--- เพิ่มแถวใหม่ต่อท้ายตาราง
          render_append(source, ds, output);

          //--- เติมสีน้ำเงินในแถวที่มีการเปลี่ยนแปลง
          setColor(ds.id);

          //--- Reset Input control พร้อมสำหรับรายการต่อไป
          getReady();
        }
      }else{
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        });
      }
    }
  })
}




//--- Reset Input control พร้อมสำหรับรายการต่อไป
function getReady(){
  $('#pd-code').val('');
  $('#qty-up').val('');
  $('#qty-down').val('');
  $('#pd-code').focus();
}



//--- ไอไลท์แถวที่มีการเปลี่ยนแปลงล่าสุด
function setColor(id){
  //--- เอาสีน้ำเงินออกจากทุกรายการก่อน
  $('.rox').removeClass('blue');

  //--- เติมสีน้ำเงินในแถวที่มีการเปลี่ยนแปลง
  $('#row-' + id).addClass('blue');
}


//--- หาลำดับสูงสุดเพื่อเพิ่มแถวต่อไป
function getMaxNo(){
  var no = 0;
  $('.no').each(function(index, el) {
    var cno = parseInt($(this).text());
    if( cno > no ){
      no = cno;
    }
  });

  return no;
}




//--- เปลียนโซนใหม่
function changeZone(){
  //--- clear ค่าต่างๆ
  $('#zone_code').val('');
  $('#qty-up').val('').attr('disabled','disabled');
  $('#qty-down').val('').attr('disabled','disabled');
  $('#pd-code').val('').attr('disabled','disabled');
  $('#zone').val('').removeAttr('disabled');
  $('#btn-change-zone').addClass('hide');
  $('#btn-set-zone').removeClass('hide');
  $('#btn-add').attr('disabled', 'disabled');
}


//--- ลบรายการ 1 บรรทัด
function deleteDetail(id, pdCode){
  swal({
		title: 'คุณแน่ใจ ?',
		text: 'ต้องการลบ '+ pdCode +' หรือไม่ ?',
		type: 'warning',
		showCancelButton: true,
		comfirmButtonColor: '#DD6855',
		confirmButtonText: 'ใช่ ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
	}, function(){
		$.ajax({
			url: HOME + "delete_detail",
			type:"POST",
			cache:"false",
			data:{
				"id" : id
			},
			success: function(rs){
				var rs = $.trim(rs);
				if( rs == 'success' ){
					swal({
						title:'Deleted',
						text: 'ลบรายการเรียบร้อยแล้ว',
						type: 'success',
						timer: 1000
					});

					$("#row-"+id).remove();
          reIndex();

				}else{

					swal("ลบรายการไม่สำเร็จ", rs, "error");
				}
			}
		});
	});
}

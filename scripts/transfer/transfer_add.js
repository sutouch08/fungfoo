
//--- เพิ่มเอกสารโอนคลังใหม่
function add(){

  //--- วันที่เอกสาร
  var date_add = $('#date').val();

  //--- คลังต้นทาง
  var from_warehouse = $('#from_warehouse').val();
  var from_warehouse_code = $('#from_warehouse_code').val();

  //--- คลังปลายทาง
  var to_warehouse = $('#to_warehouse').val();
  var to_warehouse_code = $('#to_warehouse_code').val();

  //--- หมายเหตุ
  var remark = $('#remark').val();

  //--- ตรวจสอบวันที่
  if( ! isDate(date_add))
  {
    swal('วันที่ไม่ถูกต้อง');
    return false;
  }

  //--- ตรวจสอบคลังต้นทาง
  if(from_warehouse.length == 0 || from_warehouse_code == ''){
    swal('คลังต้นทางไม่ถูกต้อง');
    return false;
  }

  //--- ตรวจสอบคลังปลายทาง
  if(to_warehouse_code == '' || to_warehouse.length == 0){
    swal('คลังปลายทางไม่ถูกต้อง');
    return false;
  }

  //--- ตรวจสอบว่าเป็นคนละคลังกันหรือไม่ (ต้องเป็นคนละคลังกัน)
  if( from_warehouse_code == to_warehouse_code){
    swal('คลังต้นทางต้องไม่ตรงกับคลังปลายทาง');
    return false;
  }

  //--- ถ้าไม่มีข้อผิดพลาด
  $('#addForm').submit();
}




//--- update เอกสาร
function update(){
  //--- ไอดีเอกสาร สำหรับส่งไปอ้างอิงการแก้ไข
  var code = $('#transfer_code').val();

  //--- คลังต้นทาง
  var from_warehouse = $('#from_warehouse_code').val();
  var old_from_wh = $('#old_from_warehouse_code').val();
  //--- คลังปลายทาง
  var to_warehouse = $('#to_warehouse_code').val();
  var old_to_wh = $('#old_to_warehouse_code').val();
  //--  วันที่เอกสาร
  var date_add = $('#date').val();

  //--- หมายเหตุ
  var remark = $('#remark').val();

  //--- ตรวจสอบไอดี
  if(code == ''){
    swal('Error !', 'ไม่พบเลขที่เอกสาร', 'error');
    return false;
  }

  //--- ตรวจสอบวันที่
  if( ! isDate(date_add)){
    swal('วันที่ไม่ถูกต้อง');
    return false;
  }

  //--- ตรวจสอบคลังต้นทาง
  if(from_warehouse == ''){
    swal('กรุณาเลือกคลังต้นทาง');
    return false;
  }

  //--- ตรวจสอบคลังปลายทาง
  if(to_warehouse == ''){
    swal('กรุณาเลือกคลังปลายทาง');
    return false;
  }

  //--- ตรวจสอบว่าเป็นคนละคลังกันหรือไม่ (ต้องเป็นคนละคลังกัน)
  if( from_warehouse == to_warehouse){
    swal('คลังต้นทางต้องไม่ตรงกับคลังปลายทาง');
    return false;
  }

  console.log('from:'+from_warehouse);
  console.log('old : '+old_from_wh);
  console.log('to :' + to_warehouse);
  console.log('old :' + old_to_wh);
  //--- ตรวจสอบหากมีการเปลี่ยนคลัง ต้องเช็คก่อนว่ามีการทำรายการไปแล้วหรือยัง
  if(from_warehouse != old_from_wh || to_warehouse != old_to_wh)
  {
    $.ajax({
      url:HOME + 'is_exists_detail/'+code,
      type:'POST',
      cache:false,
      success:function(rs)
      {
        if(rs === 'exists')
        {
          swal({
            title:'Warning !',
            text:'มีการทำรายการแล้วไม่สามารถเปลี่ยนคลังได้',
            type:'warning'
          });

          return false;
        }
        else
        {
          do_update(code, date_add, from_warehouse, to_warehouse, remark);
        }
      }
    })
  }
  else
  {
    do_update(code, date_add, from_warehouse, to_warehouse, remark);
  }
}



function do_update(code, date_add, from_warehouse, to_warehouse, remark)
{
  load_in();
  //--- ถ้าไม่มีอะไรผิดพลาด ส่งข้อมูไป update
  $.ajax({
    url: HOME + 'update/'+code,
    type:'POST',
    cache:'false',
    data:{
      'date_add'    : date_add,
      'from_warehouse' : from_warehouse,
      'to_warehouse' : to_warehouse,
      'remark'      : remark
    },
    success:function(rs){
      load_out();

      var rs = $.trim(rs)
      if( rs == 'success'){
        swal({
          title:'Success',
          type:'success',
          timer: 1000
        });

        setTimeout(function(){
          window.location.reload();
        }, 1200);

      }else{
        swal('Error', rs, 'error');
      }
    }
  });
}



//--- แก้ไขหัวเอกสาร
function getEdit(){
  $('.edit').removeAttr('disabled');
  $('#btn-edit').addClass('hide');
  $('#btn-update').removeClass('hide');
}



//---  บันทึกเอกสาร
function save(){
  var code = $('#transfer_code').val();

  //--- check temp
  $.ajax({
    url:HOME + 'check_temp_exists/'+code,
    type:'POST',
    cache:'false',
    success:function(rs){
      var rs = $.trim(rs);
      //--- ถ้าไม่มียอดค้างใน temp
      if( rs == 'not_exists'){
        //--- ส่งข้อมูลไป formula
        saveTransfer(code);
      }else{
        swal({
          title:'ข้อผิดพลาด !',
          text:'พบรายการที่ยังไม่โอนเข้าปลายทาง กรุณาตรวจสอบ',
          type:'error'
        });
      }
    }
  });
}



function saveTransfer(code)
{
  load_in();
  $.ajax({
    url:HOME + 'save_transfer/'+code,
    type:'POST',
    cache:false,
    success:function(rs){
      load_out();
      if(rs == 'success'){
        swal({
          title:'Saved',
          text: 'บันทึกเอกสารเรียบร้อยแล้ว',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          window.location.reload();
        }, 1200);
      }else{
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        });
      }
    }
  });
}



function unSave(){
  var code = $('#transfer_code').val();
  swal({
		title: 'คำเตือน !!',
		text: 'หากต้องการยกเลิกการบันทึก คุณต้องยกเลิกเอกสารนี้ใน SAP ก่อน ต้องการยกเลิกการบันทึก '+ code +' หรือไม่ ?',
		type: 'warning',
		showCancelButton: true,
		comfirmButtonColor: '#DD6855',
		confirmButtonText: 'ใช่ ฉันต้องการ',
		cancelButtonText: 'ไม่ใช่',
		closeOnConfirm: false
	}, function(){
		$.ajax({
			url:HOME + 'unsave_transfer/'+ code,
			type:"POST",
      cache:"false",
			success: function(rs){
				var rs = $.trim(rs);
				if( rs == 'success' ){
					swal({
						title:'Success',
						text: 'ดำเนินการเรียบร้อยแล้ว',
						type: 'success',
						timer: 1000
					});

					setTimeout(function(){
						window.location.reload();
					}, 1200);

				}else{
					swal("ข้อผิดพลาด", rs, "error");
				}
			}
		});
	});
}




$('#from_warehouse').autocomplete({
  source:BASE_URL + 'auto_complete/get_warehouse_code_and_name',
  autoFocus:true,
  close:function(){
    var rs = $(this).val();
    var arr = rs.split(' | ');
    if(arr.length == 2)
    {
      code = arr[0];
      name = arr[1];
      $('#from_warehouse_code').val(code);
      $(this).val(name);
    }
    else
    {
      $('#from_warehouse_code').val('');
      $(this).val('');
    }
  }
});


$('#to_warehouse').autocomplete({
  source:BASE_URL + 'auto_complete/get_warehouse_code_and_name',
  autoFocus:true,
  close:function(){
    var rs = $(this).val();
    var arr = rs.split(' | ');
    if(arr.length == 2)
    {
      code = arr[0];
      name = arr[1];
      $('#to_warehouse_code').val(code);
      $(this).val(name);
    }
    else
    {
      $('#to_warehouse_code').val('');
      $(this).val('');
    }
  }
});

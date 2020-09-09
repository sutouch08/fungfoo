$('#date_add').datepicker({
  dateFormat: 'dd-mm-yy'
});

$('#require_date').datepicker({
  dateFormat:'dd-mm-yy'
});

$('#vender_code').autocomplete({
  source: BASE_URL + 'auto_complete/get_vender_code_and_name',
  autoFocus:true,
  open:function(event, ui){
    $(this).autocomplete("widget").css({
      'width' : 'auto',
      'min-width' : $(this).width() + 'px'
    })
  },
  close:function(){
    var arr = $(this).val().split(' | ');
    if(arr.length == 2){
      $('#vender_code').val(arr[0]);
      $('#vender_name').val(arr[1]);
    }else{
      $('#vender_code').val('');
      $('#vender_name').val('');
    }
  }
});


$('#vender_name').autocomplete({
  source: BASE_URL + 'auto_complete/get_vender_code_and_name',
  autoFocus:true,
  open:function(event, ui){
    $(this).autocomplete('widget').css({
      'width' : 'auto',
      'min-width' : $(this).width() + 'px'
    })
  },
  close:function(){
    var arr = $(this).val().split(' | ');
    if(arr.length == 2){
      $('#vender_code').val(arr[0]);
      $('#vender_name').val(arr[1]);
    }else{
      $('#vender_code').val('');
      $('#vender_name').val('');
    }
  }
});


function getEdit(){
  $('.edit').removeAttr('disabled');
  $('#btn-edit').addClass('hide');
  $('#btn-update').removeClass('hide');
}



function save(){
  var code = $('#code').val();
  $.ajax({
    url:HOME + 'save_po',
    type:'POST',
    cache:false,
    data:{
      'po_code' : code
    },
    success:function(rs){
      if(rs === 'success'){
        swal({
          title:'Success',
          text:'',
          type:'success',
          timer:1000
        });
        setTimeout(function(){
          window.location.reload();
        }, 2000);

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


function unsave(){
  var code = $('#code').val();
  $.ajax({
    url:HOME + 'unsave_po',
    type:'POST',
    cache:false,
    data:{
      'po_code' : code
    },
    success:function(rs){
      if(rs === 'success'){
        swal({
          title:'Success',
          text:'',
          type:'success',
          timer:1000
        });
        setTimeout(function(){
          window.location.reload();
        }, 2000);

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


function update(){
  var code = $('#code').val();
  var vender_code = $('#vender_code').val();
  var date_add = $('#date_add').val();
  var require_date = $('#require_date').val();
  var remark = $('#remark').val();

  if(vender_code.length == 0){
    swal('กรุณาระบุผู้ขาย');
    return false;
  }

  $.ajax({
    url:HOME + 'update',
    type:'POST',
    cache:false,
    data:{
      'po_code' : code,
      'vender_code' : vender_code,
      'date_add' : date_add,
      'require_date' : require_date,
      'remark' : remark
    },
    success:function(rs){
      if(rs === 'success'){
        $('.edit').attr('disabled', 'disabled');
        $('#btn-update').addClass('hide');
        $('#btn-edit').removeClass('hide');
        swal({
          title:'Updated',
          text:'Upate successfully',
          type:'success',
          timer:1000
        });
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


function closePO(){
  let code = $('#code').val();
  swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการปิด '" + code + "' หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'ใช่',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
      $.ajax({
        url:HOME + 'close_po/'+code,
        type:'POST',
        cache:false,
        success:function(rs){
          load_out();
          if(rs === 'success'){
            swal({
              title:'Closed',
              text:'Close PO successfull',
              type:'success',
              timer:1000
            });

            setTimeout(function(){
              goEdit(code);
            }, 1500);
          }else{
            swal({
              title:'Error!',
              text:rs,
              type:'error'
            });
          }
        }
      })
	});
}


function unClosePO(){
  let code = $('#code').val();
  swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการเปิด '" + code + "' อีกครั้งหรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'ใช่',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
      $.ajax({
        url:HOME + 'un_close_po/'+code,
        type:'POST',
        cache:false,
        success:function(rs){
          load_out();
          if(rs === 'success'){
            swal({
              title:'Closed',
              text:'Close PO successfull',
              type:'success',
              timer:1000
            });

            setTimeout(function(){
              goEdit(code);
            }, 1500);
          }else{
            swal({
              title:'Error!',
              text:rs,
              type:'error'
            });
          }
        }
      })
	});
}

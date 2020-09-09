function addNew(){
  var ruleName = $('#name').val();
  if(ruleName.length < 4){
    swal('Error!', 'ชื่อเงื่อนไขต้องมากกว่า 4 ตัวอักษร', 'error');
    return false;
  }

  $('#addForm').submit();
}



function getEdit(){
  $('#txt-rule-name').removeAttr('disabled');
  $('#btn-active-rule').removeAttr('disabled');
  $('#btn-dis-rule').removeAttr('disabled');
  $('#btn-edit').addClass('hide');
  $('#btn-update').removeClass('hide');

}


function activeRule(){
  $('#isActive').val(1);
  $('#btn-active-rule').addClass('btn-success');
  $('#btn-dis-rule').removeClass('btn-danger');
}


function disActiveRule(){
  $('#isActive').val(0);
  $('#btn-active-rule').removeClass('btn-success');
  $('#btn-dis-rule').addClass('btn-danger');
}

function updateRule(){
  var id = $('#id_rule').val();
  var isActive = $('#isActive').val();
  var name = $('#txt-rule-name').val();
  if(isNaN(parseInt(id))){
    swal('ไม่พบ ID Rule');
    return false;
  }

  if(name.length < 4){
    swal('ข้อผิดพลาด!', 'ชื่อเงื่อนไขต้องมากกว่า 4 ตัวอักษร', 'error');
    return false;
  }

  load_in();

  $.ajax({
    url: BASE_URL + 'discount/discount_rule/update_rule/'+id,
    type:'POST',
    cache:'false',
    data:{
      'name' : name,
      'active' : isActive
    },
    success:function(rs){
      load_out();
      var rs = $.trim(rs);
      if(rs == 'success'){
        swal({
          title:'Updated',
          type:'success',
          timer:1000
        });

        $('#txt-rule-name').attr('disabled','disabled');
        $('#btn-active-rule').attr('disabled', 'disabled');
        $('#btn-dis-rule').attr('disabled', 'disabled');
        $('#btn-update').addClass('hide');
        $('#btn-edit').removeClass('hide');
      }
    }
  });
}

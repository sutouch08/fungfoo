function addNew(){
  var name = $('#policy_name').val();
  var fromDate = $('#fromDate').val();
  var toDate = $('#toDate').val();

  if( name.length == 0){
    swal('ชื่อนโยบายไม่ถูกต้อง');
    return false;
  }

  if( !isDate(fromDate) || !isDate(toDate)){
    swal('วันที่ไม่ถูกต้อง');
    return false;
  }

  $('#addForm').submit();
}


function toggleButton(){
  if($('.chk-rule:checked').size() > 0){
    $('#btn-add-rule').removeAttr('disabled');
  }else{
    $('#btn-add-rule').attr('disabled', 'disabled');
  }
}

function addRule(){
  id = $('#id_policy').val();
  count = parseInt($('.chk-rule:checked').size());

  if(count == 0){
    return false;
  }

  data = [
    {'name':'id_policy', 'value' : id}
  ];

  i = 0;
  $('.chk-rule').each(function(index, el) {
    if($(this).is(':checked')){
      name = 'rule['+i+']';
      data.push({'name' : name, 'value':$(this).val()});
      i++;
    }
  });

  $('#rule-modal').modal('hide');

  load_in();

  $.ajax({
    url: BASE_URL + 'discount/discount_rule/add_policy_rule',
    type:'POST',
    cache:'false',
    data: data,
    success:function(rs){
      load_out();
      rs = $.trim(rs);
      if(rs == 'success'){
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          window.location.reload();
        }, 1500);

      }else{
        swal(rs);
      }
    }

  });
}




function showRuleList(){
  $('#rule-modal').modal('show');
}


function getActiveRuleList(){
  load_in();
  $.ajax({
    url: BASE_URL + 'discount/discount_policy/get_active_rule',
    type:'GET',
    cache:'false',
    success:function(rs){
      load_out();
      if(isJson(rs)){
        source = $('#rule-template').html();
        data = $.parseJSON(rs);
        output = $('#result');
        render(source, data, output);
        showRuleList();
      }
    }
  });
}



function getEdit(){
  $('.header-box').removeAttr('disabled');
  $('#btn-edit').addClass('hide');
  $('#btn-update').removeClass('hide');
}



function update(){
  var id_policy = $('#id_policy').val();
  var pName = $('#policy_name').val();
  var fromDate = $('#fromDate').val();
  var toDate = $('#toDate').val();
  var active = $('#isActive').val();

  if(isNaN(parseInt(id_policy))){
    swal('Error!', 'ไม่พบ ID Policy', 'error');
    return false;
  }

  if(pName.length < 4 || pName.length > 150){
    swal('ข้อมูลไม่ถูกต้อง','กรุณากำหนดชื่อนโยบายอย่างน้อย 4 ตัวอักษร สูงสุด 150 ตัวอักษร', 'warning');
    return false;
  }

  if(!isDate(fromDate) || ! isDate(toDate)){
    swal('วันที่ไม่ถูกต้อง', 'กรุณากำหนดวันที่ให้ถูกต้อง', 'error');
    return false;
  }

  load_in();

  $('#editForm').submit();
}



function setActive(option){
  $('#isActive').val(option);
  if(option == 1){
    $('#btn-active').addClass('btn-success');
    $('#btn-disactive').removeClass('btn-danger');
    return;
  }

  if(option == 0){
    $('#btn-active').removeClass('btn-success');
    $('#btn-disactive').addClass('btn-danger');
  }
}

function viewRuleDetail(id_rule){
  var target = BASE_URL + 'discount/discount_rule/view_rule_detail/' + id_rule;
  var wid = $(document).width();
	var left = (wid - 900) /2;
	window.open(target, "_blank", "width=900, height=1000, left="+left+", location=no, scrollbars=yes");
}


function unlinkRule(id, name){
  swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการลบ '"+name+"' ออกจากนโยบายหรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#FA5858",
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
      load_in();
			$.ajax({
				url: BASE_URL + 'discount/discount_rule/unlink_rule',
				type:"POST",
        cache:"false",
        data:{
          "id_rule" : id
        },
				success: function(rs){
          load_out();
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({
              title: 'Success',
              type: 'success',
              timer: 1000
            });

						$("#row_"+id).remove();
					}else{
						swal('Error !', rs, 'error');
					}
				}
			});
	});
}

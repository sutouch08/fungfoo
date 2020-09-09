var HOME = BASE_URL + 'masters/warehouse';

function goBack(){
  window.location.href = HOME;
}

function getSearch(){
  $('#searchForm').submit();
}


function clearFilter(){
  $.get(HOME +'/clear_filter', function(){
    goBack();
  });
}

function addNew(){
  window.location.href = HOME + '/add_new';
}

function getEdit(code){
  window.location.href = HOME + '/edit/'+code;
}



function getDelete(code){
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ ' + code + ' หรือไม่ ?',
    type:'warning',
    showCancelButton: true,
		confirmButtonColor: '#FA5858',
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
  },function(){
    $.ajax({
      url: HOME + '/delete/' + code,
      type:'GET',
      cache:false,
      success:function(rs){
        if(rs === 'success'){
          swal({
            title:'Deleted',
            text:'ลบคลัง '+code+' เรียบร้อยแล้ว',
            type:'success',
            timer:1000
          });
          $('#row-'+code).remove();
          reIndex();
        }else{
          swal({
            title:'Error!',
            text:rs,
            type:'error'
          });
        }
      }
    })

  })
}


function toggleSell(option)
{
  $('#sell').val(option);
  if(option == 1){
    $('#btn-sell-yes').addClass('btn-success');
    $('#btn-sell-no').removeClass('btn-danger');
  }
  else
  {
    $('#btn-sell-yes').removeClass('btn-success');
    $('#btn-sell-no').addClass('btn-danger');
  }
}


function togglePrepare(option)
{
  $('#prepare').val(option);
  if(option == 1){
    $('#btn-prepare-yes').addClass('btn-success');
    $('#btn-prepare-no').removeClass('btn-danger');
  }
  else
  {
    $('#btn-prepare-yes').removeClass('btn-success');
    $('#btn-prepare-no').addClass('btn-danger');
  }
}


function toggleAuz(option)
{
  $('#auz').val(option);
  if(option == 1){
    $('#btn-auz-yes').addClass('btn-success');
    $('#btn-auz-no').removeClass('btn-danger');
  }
  else
  {
    $('#btn-auz-yes').removeClass('btn-success');
    $('#btn-auz-no').addClass('btn-danger');
  }
}


function toggleActive(option)
{
  $('#active').val(option);
  if(option == 1){
    $('#btn-active-yes').addClass('btn-success');
    $('#btn-active-no').removeClass('btn-danger');
  }
  else
  {
    $('#btn-active-yes').removeClass('btn-success');
    $('#btn-active-no').addClass('btn-danger');
  }
}


function checkAdd(){
  var code = $.trim($('#code').val());
  var name = $.trim($('#name').val());
  var type = $('#role').val();

  if(code.length == 0){
    set_error($('#code'), $('#code-error'), 'Required');
    return false;
  }else{
    clear_error($('#code'), $('#code-error'));
  }

  if(name.length == 0){
    set_error($('#name'), $('#name-error'), 'Required');
    return false;
  }else{
    clear_error($('#name'), $('#name-error'));
  }

  //---- check exists code
  $.ajax({
    url:HOME + '/is_exists_code/'+code,
    type:'GET',
    cache:false,
    success:function(rs){
      if(rs != 'ok'){
        set_error($('#code'), $('#code-error'), rs);
        return false;
      }else{
        clear_error($('#code'), $('#code-error'));
        //----- check exists name
        $.ajax({
          url: HOME + '/is_exists_name/'+name,
          type:'GET',
          cache:false,
          success:function(rs){
            if(rs != 'ok'){
              set_error($('#name'), $('#name-error'), rs);
              return false;
            }else{
              clear_error($('#name'), $('#name-error'));

              if(type == ""){
                set_error($('#role'), $('#role-error'), 'Required');
                return false;
              }else{
                $('#addForm').submit();
              }
            }
          }
        });
      }
    }
  });
}



function checkUpdate(){
  var code = $.trim($('#code').val());
  var old_code = $('#old_code').val();
  var name = $.trim($('#name').val());
  var old_name = $('#old_name').val();
  var type = $('#role').val();

  if(code.length == 0){
    set_error($('#code'), $('#code-error'), 'Required');
    return false;
  }else{
    clear_error($('#code'), $('#code-error'));
  }

  if(name.length == 0){
    set_error($('#name'), $('#name-error'), 'Required');
    return false;
  }else{
    clear_error($('#name'), $('#name-error'));
  }

  //---- check exists code
  $.ajax({
    url:HOME + '/is_exists_code/'+code+'/'+old_code,
    type:'GET',
    cache:false,
    success:function(rs){
      if(rs != 'ok'){
        set_error($('#code'), $('#code-error'), rs);
        return false;
      }else{
        clear_error($('#code'), $('#code-error'));
        //----- check exists name
        $.ajax({
          url: HOME + '/is_exists_name/'+name+'/'+old_name,
          type:'GET',
          cache:false,
          success:function(rs){
            if(rs != 'ok'){
              set_error($('#name'), $('#name-error'), rs);
              return false;
            }else{
              clear_error($('#name'), $('#name-error'));

              if(type == ""){
                set_error($('#role'), $('#role-error'), 'Required');
                return false;
              }else{
                $('#addForm').submit();
              }
            }
          }
        });
      }
    }
  });
}

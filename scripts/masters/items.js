var HOME = BASE_URL + 'masters/items/';

function addNew(){
  window.location.href = HOME + 'add_new';
}



function goBack(){
  window.location.href = HOME;
}


function getEdit(code){
  window.location.href = HOME + 'edit/'+code;
}


function duplicate(code){
  window.location.href = HOME + 'duplicate/'+code;
}



$('#style').autocomplete({
  source: BASE_URL + 'auto_complete/get_style_code',
  autoFocus:true
});

$('#color').autocomplete({
  source: BASE_URL + 'auto_complete/get_color_code_and_name',
  autoFocus:true,
  close:function(){
    var rs = $(this).val();
    var err = rs.split(' | ');
    if(err.length == 2){
      $(this).val(err[0]);
    }else{
      $(this).val('');
    }
  }
});


$('#size').autocomplete({
  source:BASE_URL + 'auto_complete/get_size_code_and_name',
  autoFocus:true,
  close:function(){
    var rs = $(this).val();
    var err = rs.split(' | ');
    if(err.length == 2){
      $(this).val(err[0]);
    }else{
      $(this).val('');
    }
  }
});


function checkAdd(){
  var code = $('#code').val();
  if(code.length > 0){
    $.ajax({
      url:HOME + 'is_exists_code/'+code,
      type:'GET',
      cache:false,
      success:function(rs){
        if(rs != 'ok'){
          set_error($('#code'), $('#code-error'), rs);
          return false;
        }else{
          clear_error($('#code'), $('#code-error'));
          $('#btn-submit').click();
        }
      }
    })
  }
}


function checkUpdate(){
  var code = $('#code').val();
  var old_code = $('#old_code').val();
  if(code.length > 0){
    $.ajax({
      url:HOME + 'is_exists_code/' + code + '/' + old_code,
      type:'GET',
      cache:false,
      success:function(rs){
        if(rs != 'ok'){
          set_error($('#code'), $('#code-error'), rs);
          return false;
        }else{
          clear_error($('#code'), $('#code-error'));
          $('#btn-submit').click();
        }
      }
    })
  }
}



function clearFilter(){
  var url = HOME + 'clear_filter';
  var page = BASE_URL + 'masters/products';
  $.get(url, function(){
    goBack();
  });
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
      url: BASE_URL + 'masters/products/delete_style/' + code,
      type:'GET',
      cache:false,
      success:function(rs){
        if(rs === 'success'){
          swal({
            title:'Deleted',
            text:'ลบรุ่นสินค้าเรียบร้อยแล้ว',
            type:'success',
            timer:1000
          });

          $('#row-'+code).remove();
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



function getSearch(){
  $('#searchForm').submit();
}

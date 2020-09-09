function addNew(){
  window.location.href = BASE_URL + 'masters/product_color/add_new';
}



function goBack(){
  window.location.href = BASE_URL + 'masters/product_color';
}


function getEdit(code){
  window.location.href = BASE_URL + 'masters/product_color/edit/'+code;
}


function clearFilter(){
  var url = BASE_URL + 'masters/product_color/clear_filter';
  var page = BASE_URL + 'masters/product_color';
  $.get(url, function(rs){
    window.location.href = page;
  });
}


function getDelete(code, name){
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ ' + name + ' หรือไม่ ?',
    type:'warning',
    showCancelButton: true,
		confirmButtonColor: '#FA5858',
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
  },function(){
    window.location.href = BASE_URL + 'masters/product_color/delete/' + code;
  })
}


function toggleActive(option, code)
{
  $.ajax({
    url:BASE_URL + 'masters/product_color/set_active',
    type:'POST',
    cache:'false',
    data:{
      'code' : code,
      'active' : option
    },
    success:function(rs){
      if(rs != ''){
        $('#'+code).html(rs);
      }
    }
  });
}


function getSearch(){
  $('#searchForm').submit();
}

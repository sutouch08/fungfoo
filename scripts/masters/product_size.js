function addNew(){
  window.location.href = BASE_URL + 'masters/product_size/add_new';
}



function goBack(){
  window.location.href = BASE_URL + 'masters/product_size';
}


function getEdit(code){
  window.location.href = BASE_URL + 'masters/product_size/edit/'+code;
}


function clearFilter(){
  var url = BASE_URL + 'masters/product_size/clear_filter';
  var page = BASE_URL + 'masters/product_size';
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
    window.location.href = BASE_URL + 'masters/product_size/delete/' + code;
  })
}



function getSearch(){
  $('#searchForm').submit();
}

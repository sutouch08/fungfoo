function addNew(){
  window.location.href = BASE_URL + 'masters/payment_methods/add_new';
}



function goBack(){
  window.location.href = BASE_URL + 'masters/payment_methods';
}


function getEdit(code){
  window.location.href = BASE_URL + 'masters/payment_methods/edit/'+code;
}


function clearFilter(){
  var url = BASE_URL + 'masters/payment_methods/clear_filter';
  var page = BASE_URL + 'masters/payment_methods';
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
    window.location.href = BASE_URL + 'masters/payment_methods/delete/' + code;
  })
}



function getSearch(){
  $('#searchForm').submit();
}



function check(){
  if($('#term-check').is(":checked")){
    $('#term').val(1);
  }else{
    $('#term').val(0);
  }

  //console.log($('#term').val());
  getSearch();
}

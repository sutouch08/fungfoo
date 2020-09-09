var HOME = BASE_URL + 'masters/employee/';

function goBack(){
  window.location.href = HOME;
}

function addNew(){
  window.location.href = HOME + 'add_new';
}



function getEdit(code){
  window.location.href = HOME + 'edit/'+code;
}



function toggleActive(option){
  $('#active').val(option);
  if(option == 1){
    $('#active-on').addClass('btn-success');
    $('#active-off').removeClass('btn-danger');
  }
  else
  {
    $('#active-on').removeClass('btn-success');
    $('#active-off').addClass('btn-danger');
  }
}


function clearFilter(){
  var url = HOME + 'clear_filter';
  $.get(url, function(rs){
    goBack();
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
    window.location.href = HOME + 'delete/' + code;
  })
}



$('.filter').change(function(){
  getSearch();
});


$('.filter').keyup(function(e){
  if(e.keyCode == 13){
    getSearch();
  }
});


function getSearch(){
  $('#searchForm').submit();
}

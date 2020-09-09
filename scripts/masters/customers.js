function addNew(){
  window.location.href = BASE_URL + 'masters/customers/add_new';
}



function goBack(){
  window.location.href = BASE_URL + 'masters/customers';
}


function getEdit(code){
  window.location.href = BASE_URL + 'masters/customers/edit/'+code;
}


function viewDetail(code){
  window.location.href = BASE_URL + 'masters/customers/view_detail/'+code;
}


function changeURL(code, tab)
{

	var url = BASE_URL + 'masters/customers/edit/' + code + '/' + tab;
	var stObj = { stage: 'stage' };
	window.history.pushState(stObj, 'customers', url);
}


function changeView(code, tab)
{

	var url = BASE_URL + 'masters/customers/view_detail/' + code + '/' + tab;
	var stObj = { stage: 'stage' };
	window.history.pushState(stObj, 'customers', url);
}



function clearFilter(){
  var url = BASE_URL + 'masters/customers/clear_filter';
  var page = BASE_URL + 'masters/customers';
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
    window.location.href = BASE_URL + 'masters/customers/delete/' + code;
  })
}



$('.filter').change(function(){
  getSearch();
});


$('#date').datepicker();


function getSearch(){
  $('#searchForm').submit();
}

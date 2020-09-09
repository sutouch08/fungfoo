var HOME = BASE_URL + 'masters/transport/';

function getSearch(){
  $('#searchForm').submit();
}


$('.search-box').keyup(function(e){
  if(e.keyCode == 13){
    getSearch();
  }
});


function clearFilter(){
  $.get(HOME + 'clear_filter', function(){
    goBack();
  });
}

function goBack(){
  window.location.href = HOME;
}

function addNew(){
  window.location.href = HOME + 'add_new';
}


function getEdit(id){
  window.location.href = HOME + 'edit/'+id;
}


function getDelete(id, name){
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
    window.location.href = HOME +'delete/' + id;
  })
}


$('#customer_name').autocomplete({
  source: BASE_URL + 'auto_complete/get_customer_code_and_name',
  autoFocus: true,
	close: function(){
		var rs = $(this).val();
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			$(this).val(arr[1]);
			$("#customer_code").val(arr[0]);
			$('#customer_name').focus();
		}else{
			$(this).val('');
			$("#customer_code").val('');
		}
	}
})


$('#main_sender').autocomplete({
  source: BASE_URL + 'auto_complete/get_sender',
  autoFocus: true,
	close: function(){
		var rs = $(this).val();
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			$(this).val(arr[1]);
			$("#main_sender_id").val(arr[0]);
			$('#main_sender').focus();
		}else{
			$(this).val('');
			$("#main_sender_id").val('');
		}
	}
})

$('#second_sender').autocomplete({
  source: BASE_URL + 'auto_complete/get_sender',
  autoFocus: true,
	close: function(){
		var rs = $(this).val();
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			$(this).val(arr[1]);
			$("#second_sender_id").val(arr[0]);
			$('#second_sender').focus();
		}else{
			$(this).val('');
			$("#second_sender_id").val('');
		}
	}
})

$('#third_sender').autocomplete({
  source: BASE_URL + 'auto_complete/get_sender',
  autoFocus: true,
	close: function(){
		var rs = $(this).val();
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			$(this).val(arr[1]);
			$("#third_sender_id").val(arr[0]);
			$('#third_sender').focus();
		}else{
			$(this).val('');
			$("#third_sender_id").val('');
		}
	}
})

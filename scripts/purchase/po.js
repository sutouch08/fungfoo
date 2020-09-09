var HOME = BASE_URL + 'purchase/po/';


function addNew(){
  window.location.href = HOME + 'add_new';
}



function goBack(){
  window.location.href = HOME;
}


function goEdit(code)
{
  window.location.href = HOME + 'edit/'+code;
}



function viewDetail(code)
{
  window.location.href = HOME + 'view_detail/'+code;
}




function getDelete(code){
	swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการลบ '" + code + "' หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
			$.ajax({
				url: HOME + 'delete_po/'+ code,
				type:"POST",
        cache:"false",
				success: function(rs){
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({
              title: 'Deleted',
              type: 'success',
              timer: 1000 });
						$('#row-'+code).remove();
            reIndex();
					}else{
						swal("Error !", rs , "error");
					}
				}
			});
	});
}

function printPO()
{
  var code = $('#code').val();
  var url = HOME + 'print_po/'+code;
  var center = ($(document).width() - 800) /2;
	window.open(url, "_blank", "width=800, height=900. left="+center+", scrollbars=yes");
}

function clearFilter(){
  var url = HOME + 'clear_filter';
  $.get(url, function(rs){ goBack(); });
}



function getSearch(){
  $('#searchForm').submit();
}



$('.search').keyup(function(e){
  if(e.keyCode == 13){
    getSearch();
  }
});


$("#fromDate").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(ds){
		$("#toDate").datepicker("option", "minDate", ds);
	}
});

$("#toDate").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(ds){
		$("#fromDate").datepicker("option", "maxDate", ds);
	}
});

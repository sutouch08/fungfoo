var HOME = BASE_URL + 'account/consign_order/';

function goBack(){
  window.location.href = HOME;
}


function goAdd(){
  window.location.href = HOME + 'add_new';
}


function viewDetail(code){
  window.location.href = HOME + 'view_detail/'+code;
}


function goEdit(code)
{
  window.location.href = HOME + 'edit/'+code;
}


//--- delete all data and cancle document
function getDelete(code){
	swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการยกเลิก '"+code+"' หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'ใช่, ฉันต้องการ',
		cancelButtonText: 'ไม่ใช่',
		closeOnConfirm: false
		}, function(){
			$.ajax({
				url: HOME + 'cancle/'+code,
				type:"POST",
				cache:"false",
				success: function(rs){
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({
							title: 'Cancled',
							type: 'success',
							timer: 1000
						});

						setTimeout(function(){
							window.location.reload();
						}, 1200);

					}else{
						swal("Error !", rs, "error");
					}
				}
			});
	});
}


function exportConsignSold(code){
  load_in();
  $.ajax({
    url: HOME + 'export_consign_order/'+code,
    type:'POST',
    cache:'false',
    success:function(rs){
      load_out();
      var rs = $.trim(rs);
      if(rs == 'success'){
        swal({
          title:'success',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          window.location.reload();
        }, 1200);
      }
    }
  });
}


function getSearch(){
	$("#searchForm").submit();
}


$(".search").keyup(function(e){
	if( e.keyCode == 13 ){
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



// JavaScript Document
function printConsignOrder(){
  var code = $('#consign_code').val();
	var center = ($(document).width() - 800) /2;
  var target = HOME + 'print_consign/'+ code;
  window.open(target, "_blank", "width=800, height=900, left="+center+", scrollbars=yes");
}



function clearFilter(){
  var url = HOME + 'clear_filter';
  $.get(url, function(rs){
    goBack();
  });
}

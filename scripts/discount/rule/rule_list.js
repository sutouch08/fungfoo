function getSearch(){
  $('#searchForm').submit();
}



$('.search-box').keyup(function(e){
  if(e.keyCode == 13){
    getSearch();
  }
});


function clearFilter(){
  $.get(BASE_URL + 'discount/discount_rule/clear_filter', function(){
    goBack();
  });
}


function getDelete(id, name){
  swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการลบ '"+name+"' หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#FA5858",
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
			$.ajax({
				url:BASE_URL + "discount/discount_rule/delete_rule",
				type:"POST",
        cache:"false",
        data:{
          "id_rule" : id
        },
				success: function(rs){
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({
              title: 'Deleted',
              type: 'success',
              timer: 1000 });
						$("#row-"+id).remove();
            reIndex();
					}else{
						swal("Error !", rs, "error");
					}
				}
			});
	});
}

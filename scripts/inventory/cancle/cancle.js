function goBack(){
  window.location.href = BASE_URL + 'inventory/cancle';
}

function getSearch(){
  $('#searchForm').submit();
}


$('.search').keyup(function(e){
  if(e.keyCode == 13){
    getSearch();
  }
})


function clearFilter(){
  $.get(BASE_URL + 'inventory/cancle/clear_filter', function(){
    goBack();
  })
}


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


function move_back(id){
  $.ajax({
    url:BASE_URL + 'inventory/cancle/move_back/'+id,
    type:'POST',
    cache:false,
    success:function(rs){
      if(rs === 'success'){
        $('#row-'+id).remove();
        reIndex();
      }
    }
  })
}

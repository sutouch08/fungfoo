function goBack(){
  window.location.href = BASE_URL + 'inventory/movement';
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
  $.get(BASE_URL + 'inventory/movement/clear_filter', function(){
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

var HOME = BASE_URL + 'account/order_credit/';

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



$("#dueFromDate").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(ds){
		$("#dueToDate").datepicker("option", "minDate", ds);
	}
});



$("#dueToDate").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(ds){
		$("#dueFromDate").datepicker("option", "maxDate", ds);
	}
});




function clearFilter(){
  var url = HOME + 'clear_filter';
  $.get(url, function(rs){
    window.location.href = HOME;
  });
}

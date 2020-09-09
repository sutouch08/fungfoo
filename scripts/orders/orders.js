function addNew(){
  window.location.href = BASE_URL + 'orders/orders/add_new';
}



function goBack(){
  window.location.href = BASE_URL + 'orders/orders';
}



function editDetail(){
  var code = $('#order_code').val();
  window.location.href = BASE_URL + 'orders/orders/edit_detail/'+ code;
}


function editOrder(code){
  window.location.href = BASE_URL + 'orders/orders/edit_order/'+ code;
}



function clearFilter(){
  var url = BASE_URL + 'orders/orders/clear_filter';
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


function sort(field){
  var sort_by = "";
  if(field === 'date_add'){
    el = $('#sort_date_add');
    sort_by = el.hasClass('sorting_desc') ? 'ASC' : 'DESC';
    sort_class = el.hasClass('sorting_desc') ? 'sorting_asc' : 'sorting_desc';
  }else{
    el = $('#sort_code');
    sort_by = el.hasClass('sorting_desc') ? 'ASC' : 'DESC';
    sort_class = el.hasClass('sorting_desc') ? 'sorting_asc' : 'sorting_desc';
  }

  $('.sorting').removeClass('sorting_desc');
  $('.sorting').removeClass('sorting_asc');

  el.addClass(sort_class);
  $('#sort_by').val(sort_by);
  $('#order_by').val(field);

  getSearch();
}

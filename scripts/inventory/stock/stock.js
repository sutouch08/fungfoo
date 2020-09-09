function getSearch(){
  $('#searchForm').submit();
}

$(".search").keyup(function(e){
	if( e.keyCode == 13 ){
		getSearch();
	}
});


function clearFilter(){
  $.get(BASE_URL + 'inventory/stock/clear_filter', function(){
    window.location.href = BASE_URL + 'inventory/stock';
  })
}

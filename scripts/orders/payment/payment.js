// JavaScript Document
function viewImage(imageUrl)
{
	var image = '<img src="'+imageUrl+'" width="100%" />';
	$("#imageBody").html(image);
	$("#imageModal").modal('show');
}



function goBack(){
	window.location.href = BASE_URL + 'orders/order_payment';
}




function getSearch(){
	$("#searchForm").submit();
}



$(".search-box").keyup(function(e){
	if( e.keyCode == 13 ){
		getSearch();
	}
});



function clearFilter(){
	$.get(BASE_URL + 'orders/order_payment/clear_filter', function(){
		goBack();
	});
}

function showBoxList(){
  $("#boxListModal").modal('show');
}


function printBox(id){
  var code = $("#order_code").val();
  var center = ($(document).width() - 800) /2;
  var target = BASE_URL + 'inventory/qc/print_box/'+code+'/'+id;
	window.open(target, "_blank", "width=800, height=900. left="+center+", scrollbars=yes");
}

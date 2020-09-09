$("#chk-force-close").change(function(){
  if( $("#chk-force-close").prop('checked') == true){
    $("#btn-force-close").removeClass('not-show');
  }else{
    $("#btn-force-close").addClass('not-show');
  }
});


function printBox(id){
  var code = $("#order_code").val();
  var center = ($(document).width() - 800) /2;
  var target = HOME + 'print_box/'+code+'/'+id;
	window.open(target, "_blank", "width=800, height=900. left="+center+", scrollbars=yes");
}

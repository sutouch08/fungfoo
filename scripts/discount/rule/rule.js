var HOME = BASE_URL + 'discount/discount_rule/';
function goBack(){
  window.location.href = HOME;
}


function goAdd(){
  window.location.href = HOME + 'add_new/';
}


function goEdit(id){
  window.location.href = HOME + 'edit_rule/'+id;
}


function viewDetail(id){
  var target = BASE_URL + 'discount/discount_rule/view_rule_detail/' + id;
  var wid = $(document).width();
	var left = (wid - 900) /2;
	window.open(target, "_blank", "width=900, height=1000, left="+left+", location=no, scrollbars=yes");
}

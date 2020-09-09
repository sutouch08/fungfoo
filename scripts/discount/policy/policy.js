var HOME = BASE_URL + 'discount/discount_policy';

function goBack(){
  window.location.href = BASE_URL + 'discount/discount_policy';
}


function goAdd(){
  window.location.href = BASE_URL + 'discount/discount_policy/add_new';
}



function goEdit(code){
  window.location.href = BASE_URL + 'discount/discount_policy/edit_policy/' + code;
}



function viewDetail(code){
  window.location.href = BASE_URL + 'discount/discount_policy/view_detail/'+code;
}





$('#fromDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd){
    $('#toDate').datepicker('option', 'minDate', sd);
  }
});

$('#toDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd){
    $('#fromDate').datepicker('option', 'maxDate', sd);
  }
});

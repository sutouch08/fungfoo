var HOME = BASE_URL + 'report/sales/order_sold_by_customer_and_product/';

function toggleAllCustomer(option){
  $('#allCustomer').val(option);
  if(option == 1){
    $('#btn-cus-all').addClass('btn-primary');
    $('#btn-cus-range').removeClass('btn-primary');
    $('#cusFrom').val('');
    $('#cusFrom').attr('disabled', 'disabled');
    $('#cusTo').val('');
    $('#cusTo').attr('disabled', 'disabled');
    return
  }

  if(option == 0){
    $('#btn-cus-all').removeClass('btn-primary');
    $('#btn-cus-range').addClass('btn-primary');
    $('#cusFrom').removeAttr('disabled');
    $('#cusTo').removeAttr('disabled');
    $('#cusFrom').focus();
  }
}


$('#cusFrom').autocomplete({
  source : BASE_URL + 'auto_complete/get_customer_code_and_name',
  autoFocus:true,
  close:function(){
    var arr = $(this).val().split(' | ');
    if(arr.length === 2){
      var cusFrom = arr[0];
      $(this).val(cusFrom);
      var cusTo = $('#cusTo').val();
      if(cusTo.length > 0){
        if(cusFrom > cusTo){
          $('#cusTo').val(cusFrom);
          $('#cusFrom').val(cusTo);
        }
      }else{
        $('#cusTo').focus();
      }
    }else{
      $(this).val('');
    }
  }
});


$('#cusTo').autocomplete({
  source:BASE_URL + 'auto_complete/get_customer_code_and_name',
  autoFocus:true,
  close:function(){
    var arr = $(this).val().split(' | ');
    if(arr.length === 2){
      var cusTo = arr[0];
      $(this).val(cusTo);
      var cusFrom = $('#cusFrom').val();
      if(cusFrom.length > 0){
        if(cusFrom > cusTo){
          $('#cusTo').val(cusFrom);
          $('#cusFrom').val(cusTo);
        }
      }else{
        $('#cusFrom').focus();
      }
    }else{
      $(this).val('');
    }
  }
})


function toggleAllProduct(option){
  $('#allProduct').val(option);
  if(option == 1){
    $('#btn-pd-all').addClass('btn-primary');
    $('#btn-pd-range').removeClass('btn-primary');
    $('#pdFrom').val('');
    $('#pdFrom').attr('disabled', 'disabled');
    $('#pdTo').val('');
    $('#pdTo').attr('disabled', 'disabled');
    return
  }

  if(option == 0){
    $('#btn-pd-all').removeClass('btn-primary');
    $('#btn-pd-range').addClass('btn-primary');
    $('#pdFrom').removeAttr('disabled');
    $('#pdTo').removeAttr('disabled');
    $('#pdFrom').focus();
  }
}


$('#pdFrom').autocomplete({
  source : BASE_URL + 'auto_complete/get_style_code',
  autoFocus:true,
  close:function(){
    var pdFrom = $(this).val();
    var pdTo = $('#pdTo').val();
    if(pdTo.length > 0 && pdFrom.length > 0){
      if(pdFrom > pdTo){
        $('#pdTo').val(pdFrom);
        $('#pdFrom').val(pdTo);
      }
    }
  }
});


$('#pdTo').autocomplete({
  source:BASE_URL + 'auto_complete/get_style_code',
  autoFocus:true,
  close:function(){
    var pdTo = $(this).val();
    var pdFrom = $('#pdFrom').val();
    if(pdTo.length > 0 && pdFrom.length > 0){
      if(pdFrom > pdTo){
        $('#pdTo').val(pdFrom);
        $('#pdFrom').val(pdTo);
      }
    }
  }
})


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



function getReport(){
  var allCustomer = $('#allCustomer').val();
  var cusFrom = $('#cusFrom').val();
  var cusTo = $('#cusTo').val();
  var fromDate = $('#fromDate').val();
  var toDate = $('#toDate').val();

  if(allCustomer == 0){
    if(cusFrom.length == 0){
      $('#cusFrom').addClass('has-error');
      return false;
    }else{
      $('#cusFrom').removeClass('has-error');
    }

    if(cusTo.length == 0){
      $('#cusTo').addClass('has-error');
      return false;
    }else{
      $('#cusTo').removeClass('has-error');
    }
  }else{
    $('#cusFrom').removeClass('has-error');
    $('#cusTo').removeClass('has-error');
  }


  if(! isDate(fromDate) || ! isDate(toDate)){
    swal('วันที่ไม่ถูกต้อง');
    $('#fromDate').addClass('has-error');
    $('#toDate').addClass('has-error');
    return false;
  }else{
    $('#fromDate').removeClass('has-error');
    $('#toDate').removeClass('has-error');
  }

  var data = [
    {'name' : 'allCustomer' , 'value' : allCustomer},
    {'name' : 'fromDate' , 'value' : fromDate},
    {'name' : 'toDate', 'value' : toDate},
    {'name' : 'cusFrom', 'value' : cusFrom},
    {'name' : 'cusTo', 'value' : cusTo}
  ];

  load_in();

  $.ajax({
    url:HOME + 'get_report',
    type:'GET',
    cache:'false',
    data:data,
    success:function(rs){
      load_out();
      var rs = $.trim(rs);
      if(isJson(rs)){
        var source = $('#template').html();
        var data = $.parseJSON(rs);
        var output = $('#rs');
        render(source,  data, output);
      }
    }
  });

}


function doExport(){
  var allCustomer = $('#allCustomer').val();
  var cusFrom = $('#cusFrom').val();
  var cusTo = $('#cusTo').val();
  var fromDate = $('#fromDate').val();
  var toDate = $('#toDate').val();

  if(allCustomer == 0){
    if(cusFrom.length == 0){
      $('#cusFrom').addClass('has-error');
      return false;
    }else{
      $('#cusFrom').removeClass('has-error');
    }

    if(cusTo.length == 0){
      $('#cusTo').addClass('has-error');
      return false;
    }else{
      $('#cusTo').removeClass('has-error');
    }
  }else{
    $('#cusFrom').removeClass('has-error');
    $('#cusTo').removeClass('has-error');
  }


  if(! isDate(fromDate) || ! isDate(toDate)){
    swal('วันที่ไม่ถูกต้อง');
    $('#fromDate').addClass('has-error');
    $('#toDate').addClass('has-error');
    return false;
  }else{
    $('#fromDate').removeClass('has-error');
    $('#toDate').removeClass('has-error');
  }

  $('#reportForm').submit();

}

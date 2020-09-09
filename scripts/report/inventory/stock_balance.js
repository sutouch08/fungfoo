var HOME = BASE_URL + 'report/inventory/stock_balance/';

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

function toggleAllWarehouse(option){
  $('#allWarehouse').val(option);
  if(option == 1){
    $('#btn-wh-all').addClass('btn-primary');
    $('#btn-wh-range').removeClass('btn-primary');
    return
  }

  if(option == 0){
    $('#btn-wh-all').removeClass('btn-primary');
    $('#btn-wh-range').addClass('btn-primary');
    $('#wh-modal').modal('show');
  }
}


function toggleDate(option){
  $('#currentDate').val(option);
  if(option == 1){
    $('#btn-date-now').addClass('btn-primary');
    $('#btn-date-range').removeClass('btn-primary');
    $('#date').val('');
    $('#date').attr('disabled', 'disabled');
    return
  }

  if(option == 0){
    $('#btn-date-now').removeClass('btn-primary');
    $('#btn-date-range').addClass('btn-primary');
    $('#date').removeAttr('disabled');
  }
}

$('#date').datepicker({
  dateFormat:'dd-mm-yy'
});


function getReport(){
  var allProduct = $('#allProduct').val();
  var allWhouse = $('#allWarehouse').val();
  var currentDate = $('#currentDate').val();
  var pdFrom = $('#pdFrom').val();
  var pdTo = $('#pdTo').val();
  var date = $('#date').val();

  if(allProduct == 0){
    if(pdFrom.length == 0){
      $('#pdFrom').addClass('has-error');
      return false;
    }else{
      $('#pdFrom').removeClass('has-error');
    }

    if(pdTo.length == 0){
      $('#pdTo').addClass('has-error');
      return false;
    }else{
      $('#pdTo').removeClass('has-error');
    }
  }else{
    $('#pdFrom').removeClass('has-error');
    $('#pdTo').removeClass('has-error');
  }


  if(allWhouse == 0){
    var count = $('.chk:checked').length;
    console.log(count);
    if(count == 0){
      $('#wh-modal').modal('show');
      return false;
    }
  }

  if(currentDate == 0){
    if(date == ''){
      $('#date').addClass('has-error');
      return false;
    }else{
      $('#date').removeClass('has-error');
    }
  }
  else
  {
    $('#date').removeClass('has-error');
  }

  var data = [
    {'name' : 'allProduct', 'value' : allProduct},
    {'name' : 'allWhouse' , 'value' : allWhouse},
    {'name' : 'currentDate' , 'value' : currentDate},
    {'name' : 'pdFrom', 'value' : pdFrom},
    {'name' : 'pdTo', 'value' : pdTo},
    {'name' : 'date', 'value' : date}
  ];

  if(allWhouse == 0){
    $('.chk').each(function(index, el) {
      if($(this).is(':checked')){
        let names = 'warehouse['+$(this).val()+']';
        data.push({'name' : names, 'value' : $(this).val() });
      }
    });
  }

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
  var allProduct = $('#allProduct').val();
  var allWhouse = $('#allWarehouse').val();
  var currentDate = $('#currentDate').val();
  var pdFrom = $('#pdFrom').val();
  var pdTo = $('#pdTo').val();
  var date = $('#date').val();

  if(allProduct == 0){
    if(pdFrom.length == 0){
      $('#pdFrom').addClass('has-error');
      return false;
    }else{
      $('#pdFrom').removeClass('has-error');
    }

    if(pdTo.length == 0){
      $('#pdTo').addClass('has-error');
      return false;
    }else{
      $('#pdTo').removeClass('has-error');
    }
  }else{
    $('#pdFrom').removeClass('has-error');
    $('#pdTo').removeClass('has-error');
  }


  if(allWhouse == 0){
    var count = $('.chk:checked').length;
    console.log(count);
    if(count == 0){
      $('#wh-modal').modal('show');
      return false;
    }
  }

  if(currentDate == 0){
    if(date == ''){
      $('#date').addClass('has-error');
      return false;
    }else{
      $('#date').removeClass('has-error');
    }
  }
  else
  {
    $('#date').removeClass('has-error');
  }

  var data = [
    {'name' : 'allProduct', 'value' : allProduct},
    {'name' : 'allWhouse' , 'value' : allWhouse},
    {'name' : 'currentDate' , 'value' : currentDate},
    {'name' : 'pdFrom', 'value' : pdFrom},
    {'name' : 'pdTo', 'value' : pdTo},
    {'name' : 'date', 'value' : date}
  ];

  if(allWhouse == 0){
    $('.chk').each(function(index, el) {
      if($(this).is(':checked')){
        let names = 'warehouse['+$(this).val()+']';
        data.push({'name' : names, 'value' : $(this).val() });
      }
    });
  }

  $('#reportForm').submit();
  //
  // data = $.param(data);
  //
  // var token = new Date().getTime();
  // var target = HOME + 'do_export';
  // target += '&'+data;
  // target += '&token='+token;
  // get_download(token);
  // window.location.href = target;

}

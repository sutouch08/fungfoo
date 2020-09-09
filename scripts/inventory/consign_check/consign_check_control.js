//---- When User scan barcode box
$('#box-code').keyup(function(e){
  if(e.keyCode == 13){
    var barcode = $.trim($(this).val());
    if(barcode.length > 0){
      getBox(barcode);
    }
  }
});

//----- get box id to start check in box
function getBox(barcode){
  var code = $('#check_code').val();
  $.ajax({
    url: HOME + 'get_box/' + code +'/'+barcode,
    type:'GET',
    cache:'false',
    success:function(rs){
      if(isJson(rs))
      {
        box = $.parseJSON(rs);
        $('#id_box').val(box.id_box);
        $('#box-qty').text(box.qty);
        $('#box-label').text('กล่องที่ ' + box.box_no);
        activeControl();
      }
    }
  });
}


//---- When User scan barcode item
$('#barcode').keyup(function(e){
  if(e.keyCode == 13){
    var barcode = $.trim($(this).val());
    var qty = $('#qty-box').val();
    if(barcode.length > 0 && qty != 0){
      checkItem(barcode, qty);
    }
  }
});




function checkItem(barcode, qty){
  var code = $('#check_code').val();
  var id_box = $('#id_box').val();
  var qty = parseDefault(parseInt(qty), 1);
  if(id_box != ''){
    $.ajax({
      url: HOME + 'check_item/'+ code,
      type:'POST',
      cache:'false',
      data:{
        'id_box' : id_box,
        'barcode' : barcode,
        'qty' : qty
      },
      success:function(rs){
        var rs = $.trim(rs);
        if(rs == 'success'){
          let stock = parseInt($('#stock_qty_' + barcode).text());
          let checked = parseInt($('#check_qty_' + barcode).text());
          let box_qty = parseInt($('#box-qty').text());
          checked += qty;
          let diff = stock - checked;
          box_qty += qty;

          $('#check_qty_' + barcode).text(checked);
          $('#diff_qty_' + barcode).text(diff);
          $('#box-qty').text(box_qty);
          $('#qty-box').val(1);
          $('#barcode').val('');
          if(checked != 0){
            $('#btn-'+ barcode).removeClass('hide');
          }else{
            $('#btn-'+ barcode).addClass('hide');
          }

          $('#detail-table').prepend($('#row-'+ barcode));

          //updateTotalStockQty();
          updateTotalCheckedQty();
          updateTotalDiffQty();
          reIndex();

          $('#barcode').focus();

        }else{
          swal('Error!', rs, 'error');
          $('#qty-box').val(1);
          $('#barcode').val('');
        }
      }
    });
  }else{
    swal('Error!', 'กรุณายิงบาร์โค้ดกล่อง', 'error');
  }

}








function changeBox(){
  $('#id_box').val('');
  $('#box-code').val('');
  $('#barcode').val('');
  $('#box-qty').text('0');
  $('#box-label').text('จำนวนในกล่อง');
  $('.item').attr('disabled', 'disabled');
  $('.box').removeAttr('disabled');
  $('#box-code').focus();
}



function activeControl(){
  var id_box = $('#id_box').val();
  if(id_box != ''){
    $('.box').attr('disabled', 'disabled');
    $('.item').removeAttr('disabled');
    $('#barcode').focus();

  }else{
    swal('โซนไม่ถูกต้อง');
  }
}



$(document).ready(function() {
  var sumStock = $('#sumStock').val();
  var sumCheck = $('#sumCount').val();
  var sumDiff  = $('#sumDiff').val();
  $('#total-zone').text(sumStock);
  $('#total-checked').text(sumCheck);
  $('#total-diff').text(sumDiff);
});



function updateTotalStockQty(){
  var qty = 0;
  $('.stock-qty').each(function(index, el) {
    qty += parseInt($(this).text());
  });

  $('#total-zone').text(qty);
  $('#sumStock').val(qty);
}


function updateTotalCheckedQty(){
  var qty = 0;
  $('.checked-qty').each(function(index, el) {
    qty += parseInt($(this).text());
  });

  $('#total-checked').text(qty);
  $('#sumCount').val(qty);
}


function updateTotalDiffQty(){
  var qty = 0;
  $('.diff-qty').each(function(index, el) {
    qty += parseInt($(this).text());
  });

  $('#total-diff').text(qty);
  $('#sumDiff').val(qty);
}



function buildDetails(){
  var id_consign_check = $('#check_code').val();
  load_in();
  $.ajax({
    url:'controller/consignCheckController.php?buildDetails',
    type:'GET',
    cache:'false',
    data:{
      'id_consign_check' : id_consign_check
    },
    success:function(rs){
      load_out();
      var rs = $.trim(rs);
      if(rs == 'success'){
        swal({
          title:'Completed',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          window.location.reload();
        }, 1500);
      }else{
        swal('Error!!', rs, 'error');
      }
    }
  });
}


function getBoxList(){
  var code = $('#check_code').val();
  $.ajax({
    url: HOME + 'get_box_list/'+code,
    type:'GET',
    cache:'false',
    success:function(rs){
      if(isJson(rs)){
        var source = $('#box-list-template').html();
        var data = $.parseJSON(rs);
        var output = $('#box-list-body');
        render(source, data, output);
        $('#box-list-modal').modal('show');
      }
    }
  });
}

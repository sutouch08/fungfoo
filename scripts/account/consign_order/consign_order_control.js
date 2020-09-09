$('#barcode-item').keyup(function(e){
  if(e.keyCode == 13){
    if($(this).val() != ''){
      getItemByBarcode();
    }
  }
});

$('#barcode-item').focusout(function(e){
    if($(this).val() != ''){
      getItemByBarcode();
    }
});


$('#item-code').keyup(function(e) {
  if(e.keyCode == 13){
    getItemByCode();
  }
});


$('#item-code').focusout(function(e){
  getItemByCode();
});



$('#item-code').autocomplete({
  source: BASE_URL + 'auto_complete/get_product_code',
  autoFocus:true,
  close:function(){
    var rs = $(this).val();
    if( rs == 'no item found'){
      $(this).val('');
    }
  }
});



function getItemByCode(){
  var code = $.trim($('#item-code').val());
  var zone_code = $('#zone_code').val();
  if(code.length > 0)
  {
    $.ajax({
      url: HOME + 'get_item_by_code',
      type:'GET',
      cache:'false',
      data:{
        'code' : code,
        'zone_code' : zone_code
      },
      success:function(rs){
        var rs = $.trim(rs);
        if( isJson(rs) ){
          var ds = $.parseJSON(rs);
          $('#product_code').val(ds.pdCode);
          $('#barcode-item').val(ds.barcode);
          $('#txt-price').val(ds.price);
          $('#txt-disc').val(ds.disc);
          $('#stock-qty').text(ds.stock);
          $('#count_stock').val(ds.count_stock);
          $('#txt-price').focus();
          $('#txt-price').select();
        }else{
          swal('Error', rs, 'error');
          $('#product_code').val('');
          $('#barcode-item').val('');
          $('#txt-price').val('');
          $('#txt-disc').val('');
          $('#stock-qty').text(0);
          $('#count_stock').val(1);
        }
      }
    });
  }

}



function getItemByBarcode(){
  var barcode = $.trim($('#barcode-item').val());
  var zone_code = $('#zone_code').val();
  if(barcode.length > 0)
  {
    $.ajax({
      url: HOME + 'get_item_by_barcode',
      type:'GET',
      cache:'false',
      data:{
        'barcode' : barcode,
        'zone_code' : zone_code
      },
      success:function(rs){
        var rs = $.trim(rs);
        if( isJson(rs) ){
          var ds = $.parseJSON(rs);
          $('#product_code').val(ds.pdCode);
          $('#item-code').val(ds.product);
          $('#txt-price').val(ds.price);
          $('#txt-disc').val(ds.disc);
          $('#stock-qty').text(ds.stock);
          $('#count_stock').val(ds.count_stock);
          $('#txt-price').focus();
          $('#txt-price').select();
        }else{
          swal('Error', rs, 'error');
          $('#product_code').val('');
          $('#item-code').val('');
          $('#txt-price').val('');
          $('#txt-disc').val('');
          $('#stock-qty').text(0);
          $('#count_stock').val(1);
        }
      }
    });
  }

}


$('#txt-price').keydown(function(e) {

    //--- skip to qty if space bar key press
    if(e.keyCode == 32){
      e.preventDefault();
      $('#txt-qty').focus();
    }
});



$('#txt-price').keyup(function(e){
  if(e.keyCode == 13 && $(this).val() != ''){
    $('#txt-disc').focus();
    $('#txt-disc').select();
  }

  calAmount();
});



$('#txt-price').focusout(function(event) {
  var amount = parseFloat($(this).val());
  if(amount <= 0){
    $('#txt-disc').val(0);
  }

  if(amount < 0 ){
    $(this).val(0);
  }
});






$('#txt-disc').keyup(function(e){

  if(e.keyCode == 13){
    $('#txt-qty').focus();
    $('#txt-qty').select();
  }

  calAmount();
});


$('#txt-qty').keyup(function(e){
  if(e.keyCode == 13){
    var qty = parseInt($(this).val());
    if(qty > 0){
      addToDetail();
      return;
    }
  }

  calAmount();

});


function calAmount(){
  qty = parseDefault(parseInt($('#txt-qty').val()),0);
  price = parseDefault(parseFloat($('#txt-price').val()), 0);
  disc = parseDiscount($('#txt-disc').val(), price);
  discount = disc.discountAmount * qty;
  amount = (price * qty) - discount;
  $('#txt-amount').text(addCommas(amount.toFixed(2)));
}




function addToDetail(){
  var code = $('#consign_code').val();
  var qty = parseInt($('#txt-qty').val());
  var stock = parseInt($('#stock-qty').text());
  var product_code = $('#product_code').val();
  var price = $('#txt-price').val();
  var disc = $('#txt-disc').val();
  var auz = $('#auz').val();
  var count_stock = $('#count_stock').val();

  if(qty <= 0){
    swal('จำนวนไม่ถูกต้อง');
    return false;
  }

  if(qty > stock && auz == 0 && count_stock == 1){
    swal('ยอดในโซนไม่พอตัด');
    return false;
  }

  if(product_code == ''){
    swal('สินค้าไม่ถูกต้อง');
    return false;
  }

  load_in();
  $.ajax({
    url: HOME + 'add_detail/' + code,
    type:'POST',
    cache:'false',
    data:{
      'product_code' : product_code,
      'qty' : qty,
      'price' : price,
      'disc' : disc
    },
    success:function(rs){
      load_out();
      if(isJson(rs)){
        var data = $.parseJSON(rs);
        var id = data.id;
        if($('#row-'+id).length == 1)
        {
          $('#input-qty-'+id).val(data.qty);
          $('#qty-'+id).text(addCommas(data.qty));
        }
        else
        {
          var source = $('#new-row-template').html();
          var output = $('#detail-table');
          render_prepend(source, data, output);
        }
        reIndex();
        reCalAll();
        clearFields();
      }else{
        swal('Error!', rs, 'error');
      }
    }
  })
}


function clearFields(){
  $('#barcode-item').val('');
  $('#item-code').val('');
  $('#txt-price').val('');
  $('#txt-disc').val('');
  $('#stock-qty').text(0);
  $('#txt-qty').val('');
  $('#txt-amount').text('');
  $('#product_code').val('');
  $('#barcode-item').focus();
}




function focusRow(id){
  $('.rox').removeClass('blue');
  $('#row-'+id).addClass('blue');
}


function reCal(id){
  var price = parseDefault(parseFloat(removeCommas($('#input-price-'+id).val())), 0);
  var disc = parseDiscount($('#input-disc-'+id).val(), price);
  var qty  = parseDefault(parseFloat(removeCommas($('#qty-'+id).text())),1);
  var amount = qty * (price - disc.discountAmount);
  $('#amount-'+id).text(addCommas(amount.toFixed(2)));
  updateTotalAmount();
}



function reCalAll(){
  $('.rox').each(function(index, el) {
    var ids = $(this).attr('id').split('-');
    var id = ids[1];
    reCal(id);
  });

  updateTotalQty();
  updateTotalAmount();
}



function updateTotalAmount(){
  var total = 0;
  $('.amount').each(function(index, el) {
    var amount = parseDefault(parseFloat(removeCommas($(this).text())), 0);
    total += amount;
  });

  total = parseFloat(total).toFixed(2);
  $('#total-amount').text(addCommas(total));
}





function updateTotalQty(){
  var total = 0;
  $('.qty').each(function(index, el) {
    var qty = parseInt(removeCommas($(this).text()));
    total += qty;
  });

  $('#total-qty').text(addCommas(total));
}



function getEditDiscount(){
  $('.disc').addClass('hide');
  $('.input-disc').removeClass('hide');
  $('#btn-edit-disc').addClass('hide');
  $('#btn-update-disc').removeClass('hide');
}


function getEditPrice()
{
  $('.price').addClass('hide');
  $('.input-price').removeClass('hide');
  $('#btn-edit-price').addClass('hide');
  $('#btn-update-price').removeClass('hide');
}

function nextFocus(el, className){
  var cl = $('.'+className);
  var idx = cl.index(el);
  cl.eq(idx+1).focus();
}


$('.input-price').keyup(function(e){
  var ids = $(this).attr('id').split('-');
  var id = ids[2];
  var price = parseDefault(parseFloat($(this).val()), 0);
  if(price < 0){
    swal('ราคาน้อยกว่า 0');
    $(this).val(0);
  }

  reCal(id);

  if(e.keyCode == 13){
    nextFocus($(this), 'input-price');
  }
});


$('.input-disc').keyup(function(e){
  var ids = $(this).attr('id').split('-');
  var id = ids[2];
  var price = parseDefault(parseFloat($('#input-price-'+id).val()), 0);
  var disc = parseDiscount($(this).val(), price);

  if(disc.discountAmount > price){
    swal('ส่วนลดเกินราคา');
    $(this).val('');
  }

  if(disc.discountAmount < 0 ){
    swal('ส่วนลดน้อยกว่า 0');
    $(this).val('');
  }

  reCal(id);

  if(e.keyCode == 13){
    nextFocus($(this), 'input-disc');
  }
});




function updatePrice(){
  var code = $('#consign_code').val();
  var empty_qty = 0;
  var ds = [];

  $('.input-price').each(function(index, el){
    var ids = $(this).attr('id').split('-');
    var id = ids[2];
    if($(this).val() == ''){
      empty_qty++;
      $(this).addClass('has-error');
    }else{
      var pName  = 'price['+id+']';
      var price  = $(this).val();
      var qty    = removeCommas($('#qty-'+id).text());
      ds.push(
        {'name' : pName, 'value' : price},
      );

      $(this).removeClass('has-error');
    }

  });

  if(empty_qty > 0){
    swal('พบรายการที่ไม่ถูกต้อง '+ empty_qty+' รายการ');
    return false;
  }


  if(ds.length == 0){
    swal('ไม่พบรายการ');
    return false;
  }

  load_in();
  $.ajax({
    url: HOME + 'update_price/'+code,
    type:'POST',
    cache:'false',
    data: ds,
    success:function(rs){
      load_out();
      var rs = $.trim(rs);
      if(rs == 'success'){
        swal({
          title:'Updated',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          window.location.reload();
        }, 1200);
      }else{
        swal('Error!', rs, 'error');
      }
    }
  });
}




function updateDiscount(){
  var code = $('#consign_code').val();
  var ds = [];

  $('.input-disc').each(function(index, el){
    var ids = $(this).attr('id').split('-');
    var id = ids[2];
    var disc = $('#input-disc-'+id).val();

    var name = 'disc['+id+']';

    ds.push(
      {'name' : name, 'value' : disc}
    );
  });

  load_in();
  $.ajax({
    url:HOME + 'update_discount/'+code,
    type:'POST',
    cache:'false',
    data: ds,
    success:function(rs){
      load_out();
      var rs = $.trim(rs);
      if(rs == 'success'){
        swal({
          title:'Updated',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          window.location.reload();
        }, 1200);
      }else{
        swal('Error!', rs, 'error');
      }
    }
  });
}

$('#barcode').keyup(function(e){
  if(e.keyCode == 13){
    var barcode = $.trim($(this).val());
    var qty = $('#qty').val();
    doReceive();
  }
});


$('#invoice-box').keyup(function(e){
  if(e.keyCode === 13){
    load_invoice();
  }
})

function doReceive(){
  var barcode = $('#barcode').val();
  var qty = parseFloat($('#qty').val());
  $('#barcode').val('');
  $('#qty').val(1);
  $('#barcode').focus();
  $.ajax({
    url:HOME + 'get_item',
    type:'POST',
    cache:false,
    data:{
      'barcode' : barcode
    },
    success:function(rs){
      if(isJson(rs)){
        var pd = $.parseJSON(rs);
        var code = pd.code;
        if(code.length)
        {
          $('.invoice').each(function(){
            if(qty > 0)
            {
              var inv = $.trim($(this).text());

              if($('#qty_'+ code + '_'+ inv).length)
              {
                let c_qty = parseFloat($('#qty_'+ code + '_'+ inv).val());
                let limit = parseFloat($('#inv_qty_'+code+'_'+inv).val());
                diff = limit - c_qty;
                discount = parseFloat($('#discount_'+code+'_'+inv).val()) * 0.01;
                console.log(discount);
                if(diff > 0){
                  u_qty = diff <= qty ? diff : qty;
                  sum_qty = u_qty + c_qty;
                  price = parseFloat($('#price_'+code+'_'+inv).val());
                  discount = sum_qty * (price * discount);
                  amount = (sum_qty * price) - discount;
                  amount = amount.toFixed(2);
                  $('#qty_'+code+'_'+inv).val(sum_qty);
                  $('#amount_'+code+'_'+inv).text(addCommas(amount));
                  qty -= u_qty;
                }
              }
            }
          }); //-- each function

          reIndex();
          inputQtyInit();
          //inputPriceInit();
          recalTotal();

          if(qty > 0){
            swal({
              title:'สินค้าเกิน',
              text: code + ' เกินใบสั่งซื้อ ' + qty + 'ชิ้น',
              type:'warning'
            });
          }
        } //-- .code.length
        else
        {
          var source = $('#row-template').html();
          var data = {
            'barcode' : pd.barcode,
            'code' : pd.code,
            'name' : pd.name,
            'qty' : qty,
            'price' : (parseFloat(pd.price)).toFixed(2),
            'invoice' : $('#invoice_code').val(),
            'amount' : addCommas((parseFloat(pd.price) * parseFloat(qty)).toFixed(2))
          }
          var output = $('#detail-table');

          render_prepend(source, data, output);
          reIndex();
          inputQtyInit();
          //inputPriceInit();
          recalTotal();

        }


      }
      else
      {
        swal('ไม่พบสินค้า');
      }
    }
  })
}


function load_invoice(){
  var code = $('#return_code').val();
  var invoice = $('#invoice-box').val();
  if(invoice.length == 0){
    return false;
  }


  load_in();
  if($('.'+invoice).length > 0){
    load_out();
    return false;
  }

  $.ajax({
    url:HOME + 'get_invoice/' + invoice,
    type:'GET',
    cache:false,
    success:function(rs){
      load_out();
      if(isJson(rs))
      {
        var source = $('#row-template').html();
        var data = $.parseJSON(rs);
        var output = $('#detail-table');
        render_append(source, data, output);
        reIndex();
        inputQtyInit();
        //inputPriceInit();
        recalTotal();
        $('#invoice-box').val('');
      }
      else
      {
        swal(rs);
      }
    }
  })
}

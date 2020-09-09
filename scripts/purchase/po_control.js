$('#pd-code').autocomplete({
  source:BASE_URL + 'auto_complete/get_style_code',
  autoFocus:true
});

$('#pd-code').keyup(function(e){
  if(e.keyCode == 13){
    getProductGrid();
  }
});



$('#item-code').autocomplete({
  source:BASE_URL + 'auto_complete/get_item_code',
  autoFocus:true,
  close:function(){
    $('#item-qty').focus();
  }
});


function getProductGrid(){
	var pdCode 	= $("#pd-code").val();
	if( pdCode.length > 0  ){
		load_in();
		$.ajax({
			url: BASE_URL + 'orders/orders/get_product_grid',
			type:"GET",
			cache:"false",
			data:{
				"style_code" : pdCode
			},
			success: function(rs){
				load_out();
				var rs = rs.split(' | ');
				if( rs.length == 4 ){
					var grid = rs[0];
					var width = rs[1];
					var pdCode = rs[2];
					var style = rs[3];
					$("#modal").css("width", width +"px");
					$("#modalTitle").html(pdCode);
					$("#id_style").val(style);
					$("#modalBody").html(grid);
					$("#orderGrid").modal('show');
				}else{
					swal(rs[0]);
				}
			}
		});
	}
}



//---- เพิ่มรายการสินค้าเช้าใบสั่งซื้อ
function addToPo(){
  var code = $('#code').val();
	//var count = countInput();
  var data = [];
  $(".input-qty").each(function(index, element){
    if($(this).val() != ''){
      var item = $(this).attr('id');
      data.push({'code' : item, 'qty' : $(this).val()});
    }
  });

	if(data.length > 0 ){
		$("#orderGrid").modal('hide');
		$.ajax({
			url: HOME + 'add_details/'+code,
			type:"POST",
      cache:"false",
      data: {
        'data' : data
      },
			success: function(rs){
				load_out();
				var rs = $.trim(rs);
				if( rs == 'success' ){
					swal({
            title: 'success',
            type: 'success',
            timer: 1000
          });
					$("#btn-save-order").removeClass('hide');
					updateDetailTable(); //--- update list of order detail
				}else{
					swal("Error", rs, "error");
				}
			}
		});
	}
}


function addRow(){
  var code = $('#code').val();
  var itemCode = $('#item-code').val();
  var itemQty = $('#item-qty').val();

  if(itemQty.length == 0 || itemQty == 0){
    itemQty = 1;
  }

  if(itemCode.length == 0){
    return false;
  }

  $.ajax({
    url:HOME + 'add_detail/'+code,
    type:'POST',
    cache:false,
    data:{
      'product_code' : itemCode,
      'qty' : itemQty
    },
    success:function(rs){
      if(rs === 'success'){
        updateDetailTable();
      }else{
        swal(rs);
      }
    }
  })
}


function updateDetailTable(){
  var code = $('#code').val();
  $.ajax({
    url:HOME + 'get_details_table/'+code,
    type:'GET',
    cache:false,
    success:function(rs){
      if(isJson(rs)){
        var source = $('#detail-template').html();
        var data = $.parseJSON(rs);
        var output = $('#detail-table');
        render(source, data, output);
      }else if(rs == 'no_content'){
        $('#detail-table').html('');
      }
    }
  })
}

function removeDetail(id, name){
	swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการลบ '" + name + "' หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
			$.ajax({
				url: HOME + 'remove_detail/'+ id,
				type:"POST",
        cache:"false",
				success: function(rs){
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({
              title: 'Deleted',
              type: 'success',
              timer: 1000 });
						updateDetailTable();
					}else{
						swal("Error !", rs , "error");
					}
				}
			});
	});
}



function clearAll(){
  swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการลบรายการทั้งหมดหรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'ใช่, ลบทั้งหมด',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
      var code = $('#code').val();
			$.ajax({
				url: HOME + 'remove_all_details',
				type:"POST",
        cache:"false",
        data:{
          'po_code' : code
        },
				success: function(rs){
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({
              title: 'Deleted',
              type: 'success',
              timer: 1000 });
						updateDetailTable();
					}else{
						swal("Error !", rs , "error");
					}
				}
			});
	});
}



function valid_qty(){
  return true;
}

$('#item-qty').keyup(function(e){
  if(e.keyCode == 13){
    addRow();
  }
});


function get_id(attr){
  var arr = attr.split('-');
  return arr[1];
}

$('.price').keyup(function(){
  var id = get_id($(this).attr('id'));
  var price = parseDefault(parseFloat($(this).val()), 0);
  var qty = parseDefault(parseInt($('#qty-'+id).val()), 0);

  var amount = addCommas((qty * price).toFixed(2));
  $('#amount-'+id).text(amount);

  recalTotal();
});


$('.qty').keyup(function(){
  var id = get_id($(this).attr('id'));
  var price = parseDefault(parseFloat($('#price-'+id).val()), 0);
  var qty = parseDefault(parseInt($(this).val()), 0);

  var amount = addCommas((qty * price).toFixed(2));
  $('#amount-'+id).text(amount);

  recalTotal();
});



function recalTotal(){
  var total_amount = 0;
  var total_qty = 0;
  $('.amount').each(function(){
    let amount = $(this).text();
    amount = parseFloat(removeCommas(amount));
    total_amount += amount;
  });

  $('.qty').each(function(){
    let qty = parseDefault(parseInt($(this).val()), 0);
    total_qty += qty;
  });

  $('#total-qty').text(addCommas(total_qty));
  $('#total-amount').text(addCommas(total_amount.toFixed(2)));
}

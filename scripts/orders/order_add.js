$('#date').datepicker({
  dateFormat:'dd-mm-yy'
});




//---- เปลี่ยนสถานะออเดอร์  เป็นบันทึกแล้ว
function saveOrder(){
  var order_code = $('#order_code').val();
	$.ajax({
		url: BASE_URL + 'orders/orders/save/'+ order_code,
		type:"POST",
    cache:false,
		success:function(rs){
			var rs = $.trim(rs);
			if( rs == 'success' ){
				swal({
          title: 'Saved',
          type: 'success',
          timer: 1000
        });
				setTimeout(function(){ editOrder(order_code) }, 1200);
			}else{
				swal("Error ! ", rs , "error");
			}
		}
	});
}




$("#customer").autocomplete({
	source: BASE_URL + 'auto_complete/get_customer_code_and_name',
	autoFocus: true,
	close: function(){
		var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			var code = arr[0];
			var name = arr[1];
			$("#customerCode").val(code);
			$("#customer").val(name);
		}else{
			$("#customerCode").val('');
			$(this).val('');
		}
	}
});


var customer;
var channels;
var payment;
var date;


function getEdit(){
  $('.edit').removeAttr('disabled');
  $('#btn-edit').addClass('hide');
  $('#btn-update').removeClass('hide');
  customer = $("#customerCode").val();
	channels = $("#channels").val();
	payment  = $("#payment").val();
	date = $("#date").val();
}


//---- เพิ่มรายการสินค้าเช้าออเดอร์
function addToOrder(){
  var order_code = $('#order_code').val();
	//var count = countInput();
  var data = [];
  $(".order-grid").each(function(index, element){
    if($(this).val() != ''){
      var code = $(this).attr('id');
      data.push({'code' : code, 'qty' : $(this).val()});
    }
  });

	if(data.length > 0 ){
		$("#orderGrid").modal('hide');
		$.ajax({
			url: BASE_URL + 'orders/orders/add_detail/'+order_code,
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





//---- เพิ่มรายการสินค้าเช้าออเดอร์
function addItemToOrder(){
	var orderCode = $('#order_code').val();
	var qty = parseDefault(parseInt($('#input-qty').val()), 0);
	var limit = parseDefault(parseInt($('#stock-qty').val()), 0);
	var itemCode = $('#item-code').val();
  var data = [{'code':itemCode, 'qty' : qty}];
  var auz = $('#auz').val();

	if(qty > 0 && (qty <= limit || auz > 0)){
		load_in();
		$.ajax({
			url:BASE_URL + 'orders/orders/add_detail/'+orderCode,
			type:"POST",
			cache:"false",
			data:{
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

					setTimeout(function(){
						$('#item-code').val('');
						$('#stock-qty').val('');
						$('#input-qty').val('');
						$('#item-code').focus();
					},1200);


				}else{
					swal("Error", rs, "error");
				}
			}
		});
	}
}


// JavaScript Document
function updateDetailTable(){
	var order_code = $("#order_code").val();
	$.ajax({
		url: BASE_URL + 'orders/orders/get_detail_table/'+order_code,
		type:"GET",
    cache:"false",
		success: function(rs){
			if( isJson(rs) ){
				var source = $("#detail-table-template").html();
				var data = $.parseJSON(rs);
				var output = $("#detail-table");
				render(source, data, output);
			}
			else
			{
				var source = $("#nodata-template").html();
				var data = [];
				var output = $("#detail-table");
				render(source, data, output);
			}
		}
	});
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
				url: BASE_URL + 'orders/orders/remove_detail/'+ id,
				type:"POST",
        cache:"false",
				success: function(rs){
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({ title: 'Deleted', type: 'success', timer: 1000 });
						updateDetailTable();
					}else{
						swal("Error !", rs , "error");
					}
				}
			});
	});
}




$("#pd-box").autocomplete({
	source: BASE_URL + 'auto_complete/get_style_code',
	autoFocus: true
});




$('#pd-box').keyup(function(event) {
	if(event.keyCode == 13){
		var code = $(this).val();
		if(code.length > 0){
			setTimeout(function(){
				getProductGrid();
			}, 300);

		}
	}

});



$('#item-code').autocomplete({
	source:BASE_URL + 'auto_complete/get_product_code',
	minLength: 4,
	autoFocus:true,
	close:function(){
		setTimeout(function(){
			getItemGrid();
		}, 200);
	}
});

$('#item-code').keyup(function(e){
	if(e.keyCode == 13){
		var code = $(this).val();
		if(code.length > 4){
			setTimeout(function(){
				getItemGrid();
			}, 200);
		}
	}
});


$('#input-qty').keyup(function(e){
	if(e.keyCode == 13){
		addItemToOrder();
	}
});


//--- ตรวจสอบจำนวนที่คีย์สั่งใน order grid
function countInput(){
	var qty = 0;
	$(".order-grid").each(function(index, element) {
        if( $(this).val() != '' ){
			qty++;
		}
    });
	return qty;
}



function validUpdate(){
	var date_add = $("#date").val();
	var customer_code = $("#customerCode").val();
  var customer_name = $('#customer').val();
	var channels_code = $("#channels").val();
	var payment_code = $("#payment").val();
  var recal = 0;
	//---- ตรวจสอบวันที่
	if( ! isDate(date_add) ){
		swal("วันที่ไม่ถูกต้อง");
		return false;
	}

	//--- ตรวจสอบลูกค้า
	if( customer_code.length == 0 || customer_name == "" ){
		swal("ชื่อลูกค้าไม่ถูกต้อง");
		return false;
	}

  if(channels_code == ""){
    swal('กรุณาเลือกช่องทางขาย');
    return false;
  }


  if(payment_code == ""){
    swal('กรุณาเลือกช่องทางการชำระเงิน');
    return false;
  }

	//--- ตรวจสอบความเปลี่ยนแปลงที่สำคัญ
	if( (date_add != date) || ( customer_code != customer ) || ( channels_code != channels ) || ( payment_code != payment ) )
  {
		recal = 1; //--- ระบุว่าต้องคำนวณส่วนลดใหม่
	}

  updateOrder(recal);
}





function updateOrder(recal){
	var order_code = $("#order_code").val();
	var date_add = $("#date").val();
	var customer_code = $("#customerCode").val();
  var customer_name = $("#customer").val();
  var customer_ref = $('#customer_ref').val();
	var channels_code = $("#channels").val();
	var payment_code = $("#payment").val();
	var reference = $('#reference').val();
  var sender_id = $('#sender_id').val();
	var remark = $("#remark").val();

	load_in();

	$.ajax({
		url:BASE_URL + 'orders/orders/update_order',
		type:"POST",
		cache:"false",
		data:{
      "order_code" : order_code,
  		"date_add"	: date_add,
  		"customer_code" : customer_code,
      "customer_ref" : customer_ref,
  		"channels_code" : channels_code,
  		"payment_code" : payment_code,
  		"reference" : reference,
      "sender_id" : sender_id,
  		"remark" : remark,
      "recal" : recal
    },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'success' ){
				swal({
          title: 'Done !',
          type: 'success',
          timer: 1000
        });

				setTimeout(function(){
          window.location.reload();
        }, 1200);

			}else{
				swal({
          title: "Error!",
          text: rs,
          type: 'error'
        });
			}
		}
	});
}



function recalDiscount(){
	updateOrder(1);
}



// JavaScript Document
function changeState(){
    var order_code = $("#order_code").val();
    var state = $("#stateList").val();
    if( state != 0){
      load_in();
        $.ajax({
            url:BASE_URL + 'orders/orders/order_state_change',
            type:"POST",
            cache:"false",
            data:{
              "order_code" : order_code,
              "state" : state
            },
            success:function(rs){
              load_out();
                var rs = $.trim(rs);
                if(rs == 'success'){
                    swal({
                      title:'success',
                      text:'status updated',
                      type:'success',
                      timer: 1000
                    });

                    setTimeout(function(){
                      window.location.reload();
                    }, 1500);

                }else{
                    swal("Error !", rs, "error");
                }
            }
        });
    }
}



function setNotExpire(option){
  var order_code = $('#order_code').val();
  load_in();
  $.ajax({
    url:BASE_URL + 'orders/orders/set_never_expire',
    type:'POST',
    cache:'false',
    data:{
      'order_code' : order_code,
      'option' : option
    },
    success:function(rs){
      load_out();
      var rs = $.trim(rs);
      if(rs == 'success'){
        swal({
          title:'Success',
          type:'success',
          timer: 1000
        });

        setTimeout(function(){
          window.location.reload();
        },1500);
      }else{
        swal('Error', rs, 'error');
      }
    }
  });
}

function unExpired(){
  var order_code = $('#order_code').val();
  load_in();
  $.ajax({
    url:BASE_URL + 'orders/orders/un_expired',
    type:'GET',
    cache:'false',
    data:{
      'order_code' : order_code
    },
    success:function(rs){
      load_out();
      var rs = $.trim(rs);
      if(rs == 'success'){
        swal({
          title:'Success',
          type:'success',
          timer: 1000
        });

        setTimeout(function(){
          window.location.reload();
        },1500);
      }else{
        swal('Error', rs, 'error');
      }
    }
  });
}


function paid_order(){
  var code = $('#order_code').val();
  load_in();
  $.ajax({
    url:BASE_URL + 'orders/orders/paid_order/'+code,
    type:'POST',
    cache:false,
    success:function(rs){
      load_out();
      if(rs === 'success'){
        swal({
          title:'Paid',
          text:'ได้รับเงินเรียบร้อยแล้ว',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          window.location.reload();
        }, 1500);
      }else{
        swal({
          title:'Error',
          text:rs,
          type:'error'
        });
      }
    }
  })
}


function unpaid_order(){
  var code = $('#order_code').val();
  load_in();
  $.ajax({
    url:BASE_URL + 'orders/orders/unpaid_order/'+code,
    type:'POST',
    cache:false,
    success:function(rs){
      load_out();
      if(rs === 'success'){
        swal({
          title:'Success',
          text:'ยกเลิกการชำระเงินเรียบร้อยแล้ว',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          window.location.reload();
        }, 1500);
      }else{
        swal({
          title:'Error',
          text:rs,
          type:'error'
        });
      }
    }
  })
}

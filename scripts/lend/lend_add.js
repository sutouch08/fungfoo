$('#date').datepicker({
  dateFormat:'dd-mm-yy'
});


//---- เปลี่ยนสถานะออเดอร์  เป็นบันทึกแล้ว
function saveOrder(){
  var order_code = $('#order_code').val();
	$.ajax({
		url: HOME + 'save/'+ order_code,
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

				setTimeout(function(){
          editOrder(order_code)
        }, 1200);

			}else{
				swal("Error ! ", rs , "error");
			}
		}
	});
}




$("#customer").autocomplete({
	source: BASE_URL + 'auto_complete/get_employee',
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
    zoneInit();
	}
});



$('#customer').focusout(function(){
  var code = $(this).val();
  if(code.length == 0)
  {
    $('#customerCode').val('');
    zoneInit();
  }
});


function zoneInit(){
  let customerCode = $('#customerCode').val();
  $('#zone_code').val('');
  $('#zone').val('');
  
  $('#zone').autocomplete({
    source:BASE_URL + 'auto_complete/get_lend_zone/'+customerCode,
    autoFocus: true,
    close:function(){
      var rs = $.trim($(this).val());
      var arr = rs.split(' | ');
      if(arr.length == 2)
      {
        var code = arr[0];
        var name = arr[1];
        $('#zone_code').val(code);
        $('#zone').val(name);
      }else{
        $('#zone_code').val('');
        $('#zone').val('');
      }
    }
  });
}


$(document).ready(function() {
  zoneInit();
})

function add(){
  var customer_code = $('#customerCode').val();
  var customer_name = $('#customer').val();
  var date_add = $('#date').val();
  var empName = $('#empName').val();

  if(customer_code.length == 0 || customer_name.length == 0){
    swal('ชื่อผู้รับไม่ถูกต้อง');
    return false;
  }

  if(!isDate(date_add))
  {
    swal('วันที่ไม่ถูกต้อง');
    console.log('date error');
    return false;
  }

  if(empName.length == 0)
  {
    swal('ชื่อผู้เบิกไม่ถูกต้อง');
    return false;
  }

  $('#addForm').submit();
}


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
      var arr = code.split('qty_');
      data.push({'code' : arr[1], 'qty' : $(this).val()});
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
	var user_ref = $("#user_ref").val();
  var zone_code = $('#zone_code').val();
  var zone_name = $('#zone').val();

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

  if(user_ref == ""){
    swal('กรุณาระบุผู้เบิก[ผู้สั่งงาน]');
    return false;
  }

  if(zone_code == '' || zone_name.length == 0)
  {
    swal('โซนไม่ถูกต้อง');
    return false;
  }

  updateOrder();
}





function updateOrder(){
	var order_code = $("#order_code").val();
	var date_add = $("#date").val();
	var customer_code = $("#customerCode").val();
  var customer_name = $("#customer").val();
	var user_ref = $('#user_ref').val();
	var remark = $("#remark").val();
  var zone_code = $('#zone_code').val();

	load_in();

	$.ajax({
		url:HOME + 'update_order',
		type:"POST",
		cache:"false",
		data:{
      "order_code" : order_code,
  		"date_add"	: date_add,
  		"customer_code" : customer_code,
      "user_ref" : user_ref,
  		"remark" : remark,
      "zone_code" : zone_code
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


// JavaScript Document
function changeState(){
    var order_code = $("#order_code").val();
    var state = $("#stateList").val();
    if( state != 0){
        $.ajax({
            url:BASE_URL + 'orders/orders/order_state_change',
            type:"POST",
            cache:"false",
            data:{
              "order_code" : order_code,
              "state" : state
            },
            success:function(rs){
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

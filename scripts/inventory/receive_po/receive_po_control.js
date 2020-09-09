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

function valid_qty(){
  return true;
}


function insert_item()
{
	$('#orderGrid').modal('hide');
	var code = $('#code').val();

	var items = [];

  $('.input-qty').each(function(){
    let pdCode = $(this).attr('id');
    var qty = parseDefault(parseInt($(this).val()), 0);

    if(qty > 0){
      var item = {
        'product_code' : pdCode,
        'qty' : qty
      }

      items.push(item);
    }
  });

  if(items.length == 0){
    swal('กรุณาระบุจำนวนอย่างน้อย 1 รายการ');
    return false;
  }

  var data = JSON.stringify(items);

	load_in();

  $.ajax({
    url:HOME + 'add_details/' + code,
		type:'POST',
		cache:false,
		data:{
			'details' : data
		},
		success:function(rs){
			load_out();
			if(rs == 'success'){
				swal({
					title:'Success',
					text:'เพิ่ม '+items.length+' รายการ เรียบร้อยแล้ว',
					type:'success',
					timer:1000
				});

				setTimeout(function(){
					window.location.reload();
				},1500);
			}else{
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				}, function(){
					$('#orderGrid').modal('show');
				});
			}
		}
  });
}



function getData(){
	var po = $("#poCode").val();
	if(po.length > 0){
		load_in();
		$.ajax({
			url: HOME + 'get_po_details',
			type:"GET",
			cache:"false",
			data:{
				"po_code" : po
			},
			success: function(rs){
				load_out();
				var rs = $.trim(rs);
				if( isJson(rs) ){
					data = $.parseJSON(rs);
					$('#pre_label').remove();
	        $('#po-title').text(po);
					var source = $('#po-template').html();
	        var data = $.parseJSON(rs);
	        var output = $('#po-body');
	        render(source, data, output);
	        $('#poGrid').modal('show');
				}else{
					swal("ข้อผิดพลาด !", rs, "error");
				}
			}
		});
	}
}



function insertPoItems()
{
	$('#poGrid').modal('hide');

	var code = $("#code").val();
	var items = [];

  $('.receive_qty').each(function(){
    let pdCode = $(this).attr('id');
    var qty = parseDefault(parseInt($(this).val()),0);

    if(qty > 0){
      var item = {
        'product_code' : pdCode,
        'qty' : qty
      }

      items.push(item);
    }
  });

  if(items.length == 0){
    swal({
      title:'Error!',
      text:'กรุณาใส่จำนวนอย่างน้อย 1 รายการ',
      type:'error'
    });

    return false;
  }
  var data = JSON.stringify(items);

	load_in();

  $.ajax({
    url:HOME + 'add_details/'+code,
		type:'POST',
		cache:false,
		data:{
			'details' : data
		},
		success:function(rs){
			load_out();
			if(rs == 'success'){
				swal({
					title:'Success',
					text:'เพิ่ม '+items.length+' รายการ เรียบร้อยแล้ว',
					type:'success',
					timer:1000
				});

				setTimeout(function(){
					window.location.reload();
				},1500);
			}else{
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				},function(){
					$('#poGrid').modal('show');
				});
			}
		}
  });
}

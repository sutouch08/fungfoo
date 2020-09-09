// JavaScript Document

var data = [];
var poError = 0;
var invError = 0;
var zoneError = 0;
var lang = language;


$('#venderCode').autocomplete({
  source: BASE_URL + 'auto_complete/get_vender_code_and_name',
  autoFocus:true,
  open:function(event, ui){
    $(this).autocomplete("widget").css({
      'width' : 'auto',
      'min-width' : $(this).width() + 'px'
    })
  },
  close:function(){
    var arr = $(this).val().split(' | ');
    if(arr.length == 2){
      $('#venderCode').val(arr[0]);
      $('#venderName').val(arr[1]);
    }else{
      $('#venderCode').val('');
      $('#venderName').val('');
    }
  }
});


$('#venderName').autocomplete({
  source: BASE_URL + 'auto_complete/get_vender_code_and_name',
  autoFocus:true,
  open:function(event, ui){
    $(this).autocomplete("widget").css({
      'width' : 'auto',
      'min-width' : $(this).width() + 'px'
    })
  },
  close:function(){
    var arr = $(this).val().split(' | ');
    if(arr.length == 2){
      $('#venderCode').val(arr[0]);
      $('#venderName').val(arr[1]);
    }else{
      $('#venderCode').val('');
      $('#venderName').val('');
    }
  }
});

$('#venderName').focusout(function(event) {
	if($(this).val() == ''){
		$('#venderCode').val('');
	}
	poInit();
});

$('#venderCode').focusout(function(event) {
	if($(this).val() == ''){
		$('#venderName').val('');
	}
	poInit();
});



$('#venderCode').keyup(function(e) {
	if(e.keyCode == 13){
		if($(this).val().length > 0){
			$('#poCode').focus();
		}
	}
});



function addNew()
{
  var date_add = $('#dateAdd').val();
  var venderCode = $('#venderCode').val();
  var poCode = $('#poCode').val();
  var invoice = $('#invoice').val();
  var zoneCode = $('#zoneCode').val();
  var remark = $('#remark').val();

  if(!isDate(date_add)){
    swal('วันที่ไม่ถูกต้อง');
    return false;
  }

  if(venderCode.length == 0){
    swal('กรุณาระบุผู้ขาย');
    return false;
  }

  if(invoice.length == 0){
    swal('กรุณาระบุใบส่งสินค้า');
    return false;
  }

  if(zoneCode.length == 0){
    swal('กรุณาระบุโซนรับสินค้า');
    return false;
  }

  load_in();
  $.ajax({
    url:HOME + 'add',
    type:'POST',
    cache:false,
    data:{
      'date_add' : date_add,
      'venderCode' : venderCode,
      'poCode' : poCode,
      'invoice' : invoice,
      'zoneCode' : zoneCode,
      'remark' : remark
    },
    success:function(rs){
      load_out();
      var arr = $.parseJSON(rs);
      if(arr.status == 'error'){
        swal({
          title:'Error!!',
          text:arr.message,
          type:'error'
        });
      }else{
        goEdit(arr.message);
      }
    }
  })
}



$(document).ready(function() {
	poInit();
});


function poInit(){
	var vender_code = $('#venderCode').val();
	if(vender_code == ''){
		$("#poCode").autocomplete({
			source: BASE_URL + 'auto_complete/get_po_code_and_vender_name',
			autoFocus: true,
			open: function(event, ui) {
				$(this).autocomplete("widget").css({
		            "width": "auto",
								"min-width" : ($(this).width() + "px")
		        });
		    },
			close:function(){
				var code = $(this).val();
				var arr = code.split(' | ');
				if(arr.length == 2){
					$(this).val(arr[0]);
          updateVender(arr[0]);
				}else{
					$(this).val('');
				}
			}
		});
	}else{
		$("#poCode").autocomplete({
			source: BASE_URL + 'auto_complete/get_po_code_and_vender_name/'+vender_code,
			autoFocus: true,
			open: function(event, ui) {
				$(this).autocomplete("widget").css({
		            "width": "auto",
								"min-width" : ($(this).width() + "px")
		        });
		    },
			close:function(){
				var code = $(this).val();
				var arr = code.split(' | ');
				if(arr.length == 2){
					$(this).val(arr[0]);
          updateVender(arr[0]);
				}else{
					$(this).val('');
				}
			}
		});
	}
}


$('#poCode').keyup(function(e) {
	if(e.keyCode == 13){
		$('#invoice').focus();
	}
});


//---- ดึงรหัสและชื่อผู้ขายจากเลขที่ PO
function updateVender(poCode){
  $.get(HOME+'get_vender_by_po/'+poCode, function(rs){
    var arr = rs.split(' | ');
    if(arr.length == 2){
      $('#venderCode').val(arr[0]);
      $('#venderName').val(arr[1]);
    }
    poInit();
  });
}


$("#zoneCode").autocomplete({
	source: BASE_URL + 'auto_complete/get_zone_code_and_name', //"controller/receiveProductController.php?search_zone",
	autoFocus: true,
	open: function(event, ui) {
		$(this).autocomplete("widget").css({
						"width": "auto",
						"min-width" : ($(this).width() + "px")
				});
		},
	close: function(){
		var rs = $(this).val();
		var arr = rs.split(' | ');
		if(arr.length == 2){
			$('#zoneCode').val(arr[0]);
			$('#zoneName').val(arr[1]);
			$('#remark').focus();
		}else{
			$('#zoneCode').val('');
			$('#zoneName').val('');
		}
	}
});


$("#zoneName").autocomplete({
	source: BASE_URL + 'auto_complete/get_zone_code_and_name', //"controller/receiveProductController.php?search_zone",
	autoFocus: true,
	open: function(event, ui) {
		$(this).autocomplete("widget").css({
						"width": "auto",
						"min-width" : ($(this).width() + "px")
				});
		},
	close: function(){
		var rs = $(this).val();
		var arr = rs.split(' | ');
		if(arr.length == 2){
			$('#zoneCode').val(arr[0]);
			$('#zoneName').val(arr[1]);
			$('#remark').focus();
		}else{
			$('#zoneCode').val('');
			$('#zoneName').val('');
		}
	}
});


$("#dateAdd").datepicker({ dateFormat: 'dd-mm-yy'});

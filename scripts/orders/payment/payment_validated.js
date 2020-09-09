// JavaScript Document
function viewValidDetail(id_order)
{
	load_in();
	$.ajax({
		url:"controller/paymentController.php?getValidPaymentDetail",
		type:"POST", cache:"false", data:{ "id_order" : id_order },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'fail' ){
				swal('ข้อผิดพลาด', 'ไม่พบข้อมูล', 'error');
			}else{
				var source 	= $("#detailTemplate").html();
				var data		= $.parseJSON(rs);
				var output	= $("#detailBody");
				render(source, data, output);
				$("#confirmModal").modal('show');
			}
		}
	});
}





function removeValidPayment(id_order, name)
{
	swal({
		title: 'คุณแน่ใจ ?',
		text: 'ต้องการลบการแจ้งชำระของ '+ name + ' หรือไม่?',
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#DD6855',
		confirmButtonText: 'ใช่ ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
			$.ajax({
				url: "controller/paymentController.php?removeValidPayment",
				type:"POST", cache:"false", data:{ "id_order" : id_order, "reference" : name },
				success: function(rs){
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({ title : "สำเร็จ", text: "ลบรายการเรียบร้อยแล้ว", timer: 1000, type: "success" });	
						$("#"+id_order).remove();
					}else{
						swal("ข้อผิดพลาด!!", "ลบรายการไม่สำเร็จ", "error");	
					}
				}
			});
		});	
}



$("#fromDate").datepicker({
	dateFormat:'dd-mm-yy',
	onClose: function(sd){
		$("#toDate").datepicker("option", "minDate", sd);
	}
});



$("#toDate").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(sd){
		$("#fromDate").datepicker("option", "maxDate", sd);
	}
});


$("#sAcc").autocomplete({
	source:"controller/paymentController.php?searchBankAccount",
	autoFocus:true,
	close: function(){
		var rs = $(this).val();
		//----		bank_name | acc_no | branch | acc_name;
		var arr = rs.split(' | ');
		if( arr.length > 1 ){
			$(this).val(arr[1]);
		}
	}
});
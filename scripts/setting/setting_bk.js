
function updateConfig(formName)
{
	load_in();
	var formData = $("#"+formName).serialize();
	$.ajax({
		url: BASE_URL + "setting/configs/update_config",
		type:"POST",
    cache:"false",
    data: formData,
		success: function(rs){
			load_out();
      rs = $.trim(rs);
      if(rs == 'success'){
        swal({
          title:'Updated',
          type:'success',
          timer:1000
        });
      }else{
        swal('Error!', rs, 'error');
      }
		}
	});
}



function openSystem()
{
	$("#closed").val(0);
	$("#btn-close").removeClass('btn-danger');
	$("#btn-open").addClass('btn-success');
}



function closeSystem()
{
	$("#closed").val(1);
	$("#btn-open").removeClass('btn-success');
	$("#btn-close").addClass('btn-danger');
}


function toggleQC(option)
{
	$('#use_qc').val(option);
	if(option == 1)
	{
		$('#btn-qc-yes').addClass('btn-success');
		$('#btn-qc-no').removeClass('btn-danger')
		return;
	}

	if(option == 0)
	{
		$('#btn-qc-yes').removeClass('btn-success');
		$('#btn-qc-no').addClass('btn-danger');
		return;
	}
}

function toggleManualCode(option)
{
	$('#manual-doc-code').val(option);
	if(option == 1){
		$('#btn-manual-yes').addClass('btn-success');
		$('#btn-manual-no').removeClass('btn-danger');
		return;
	}
	if(option == 0){
		$('#btn-manual-yes').removeClass('btn-success');
		$('#btn-manual-no').addClass('btn-danger');
		return;
	}
}


//---- ไม่ขายสินค้าให้ลูกค้าที่มียอดค้างเกินกำหนด
function toggleStrictDue(option)
{
	$('#strict-over-due').val(option);
	if(option == 1){
		$('#btn-strict-yes').addClass('btn-success');
		$('#btn-strict-no').removeClass('btn-danger');
		return;
	}
	if(option == 0){
		$('#btn-strict-yes').removeClass('btn-success');
		$('#btn-strict-no').addClass('btn-danger');
		return;
	}
}



//---- ไม่ขายสินค้าให้ลูกค้าที่มียอดค้างเกินกำหนด
function toggleAuz(option)
{
	$('#allow-under-zero').val(option);
	if(option == 1){
		$('#btn-auz-yes').addClass('btn-danger');
		$('#btn-auz-no').removeClass('btn-success');
		return;
	}
	if(option == 0){
		$('#btn-auz-yes').removeClass('btn-danger');
		$('#btn-auz-no').addClass('btn-success');
		return;
	}
}


function toggleControlCredit(option)
{
	$('#control-credit').val(option);
	if(option == 1){
		$('#btn-credit-yes').addClass('btn-success');
		$('#btn-credit-no').removeClass('btn-danger');
		return;
	}
	if(option == 0){
		$('#btn-credit-yes').removeClass('btn-success');
		$('#btn-credit-no').addClass('btn-danger');
		return;
	}
}



function toggleReceiveDue(option)
{
	$('#receive-over-due').val(option);
	if(option == 1){
		$('#btn-receive-yes').addClass('btn-success');
		$('#btn-receive-no').removeClass('btn-danger');
		return;
	}
	if(option == 0){
		$('#btn-receive-yes').removeClass('btn-success');
		$('#btn-receive-no').addClass('btn-danger');
		return;
	}
}

function toggleEditDiscount(option)
{
	$('#allow-edit-discount').val(option);
	if(option == 1){
		$('#btn-disc-yes').addClass('btn-success');
		$('#btn-disc-no').removeClass('btn-danger');
		return;
	}

	if(option == 0){
		$('#btn-disc-yes').removeClass('btn-success');
		$('#btn-disc-no').addClass('btn-danger');
		return;
	}
}


function toggleEditPrice(option){
	$('#allow-edit-price').val(option);

	if(option == 1){
		$('#btn-price-yes').addClass('btn-success');
		$('#btn-price-no').removeClass('btn-danger');
		return;
	}

	if(option == 0){
		$('#btn-price-yes').removeClass('btn-success');
		$('#btn-price-no').addClass('btn-danger');
		return;
	}
}


function toggleEditCost(option){
	$('#allow-edit-cost').val(option);

	if(option == 1){
		$('#btn-cost-yes').addClass('btn-success');
		$('#btn-cost-no').removeClass('btn-danger');
		return;
	}

	if(option == 0){
		$('#btn-cost-yes').removeClass('btn-success');
		$('#btn-cost-no').addClass('btn-danger');
		return;
	}
}



function toggleAutoClose(option){
	$('#po-auto-close').val(option);

	if(option == 1){
		$('#btn-po-yes').addClass('btn-success');
		$('#btn-po-no').removeClass('btn-danger');
		return;
	}

	if(option == 0){
		$('#btn-po-yes').removeClass('btn-success');
		$('#btn-po-no').addClass('btn-danger');
		return;
	}
}


function checkCompanySetting(){
	vat = parseFloat($('#VAT').val());
	year = parseInt($('#startYear').val());

	if(isNaN(year)){
		swal('ปีที่เริ่มต้นกิจการไม่ถูกต้อง');
		return false;
	}

	if(year < 1970){
		swal('ปีที่เริ่มต้นกิจการไม่ถูกต้อง');
		return false;
	}

	if(year > 2100){
		year = year - 543;
		$('#startYear').val(year);
	}


	updateConfig('companyForm');
}

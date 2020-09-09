function saveDiscount(){
  id = $('#id_rule').val();

  if(id == ''){
    swal('Error !', 'RULE ID Not found', 'error');
    return false;
  }

  //--- กำหนดราคาขาย
  setPrice = $('#set_price').val();
  price = parseFloat($('#txt-price').val());
  price = isNaN(price) ? 0 : price;

  //--- กำหนดส่วนลด
  discUnit = $('#disc_unit').val();
  disc = parseFloat($('#txt-discount').val());
  disc = isNaN(disc) ? 0 : disc;

  //--- กำหนดส่วนลด 2
  discUnit2 = $('#disc_unit2').val();
  disc2 = parseFloat($('#txt-discount2').val());
  disc2 = isNaN(disc2) ? 0 : disc2;

  //--- กำหนดส่วนลด 3
  discUnit3 = $('#disc_unit3').val();
  disc3 = parseFloat($('#txt-discount3').val());
  disc3 = isNaN(disc3) ? 0 : disc3;

  //--- กำหนดจำนวนขั้นต่ำ
  minQty = parseInt($('#txt-qty').val());
  minQty = isNaN(minQty) ? 0 : minQty;

  //--- กำหนดมูลค่าขั้นต่ำ
  minAmount = parseFloat($('#txt-amount').val());
  minAmount = isNaN(minAmount) ? 0 : minAmount;

  //--- สามารถรวมยอดได้หรือไม่
  canGroup = $('#can_group').val();


  if(setPrice == 'Y' && price <= 0){
    swal('ข้อผิดพลาด', 'ราคาขายต้องมากกว่า 0', 'error');
    return false;
  }

  if(setPrice == 'N' && disc <= 0){
    swal('ข้อผิดพลาด', 'ส่วนลดต้องมากกว่า 0', 'error');
    return false;
  }

  if(setPrice == 'N' && discUnit == 'P' && disc > 100){
    swal('ข้อผิดพลาด', 'ส่วนลดต้องไม่เกิน 100%', 'error');
    return false;
  }

  if(minQty < 0){
    swal('ข้อผิดพลาด', 'จำนวนขั้นต่ำต้องไม่น้อยกว่า 0', 'error');
    return false;
  }

  if(minAmount < 0){
    swal('ข้อผิดพลาด', 'มูลค่าขั้นต่ำต้องไม่น้อยกว่า 0', 'error');
    return false;
  }

  load_in();
  $.ajax({
    url:BASE_URL + 'discount/discount_rule/set_discount',
    type:'POST',
    cache:'false',
    data:{
      'id_rule' : id,
      'set_price' : setPrice,
      'price' : price,
      'disc' : disc,
      'disc_unit' : discUnit,
      'disc2' : disc2,
      'disc_unit2' : discUnit2,
      'disc3' : disc3,
      'disc_unit3' : discUnit3,
      'min_qty' : minQty,
      'min_amount' : minAmount,
      'can_group' : canGroup
    },
    success:function(rs){
      load_out();
      var rs = $.trim(rs);
      if(rs == 'success'){
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });
      }else{
        swal('Error', rs, 'error');
      }
    }
  });

}


function toggleSetPrice(option){
  $('#set_price').val(option);
  if(option == 'Y'){
    $('#btn-set-price-yes').addClass('btn-primary');
    $('#btn-set-price-no').removeClass('btn-primary');
    $('#txt-price').removeAttr('disabled');
    $('#txt-discount').attr('disabled', 'disabled');
    $('#btn-aUnit').attr('disabled', 'disabled');
    $('#btn-pUnit').attr('disabled', 'disabled');
    $('#txt-discount').val(0);

    $('#txt-discount2').attr('disabled', 'disabled');
    $('#btn-aUnit2').attr('disabled', 'disabled');
    $('#btn-pUnit2').attr('disabled', 'disabled');
    $('#txt-discount2').val(0);

    $('#txt-discount3').attr('disabled', 'disabled');
    $('#btn-aUnit3').attr('disabled', 'disabled');
    $('#btn-pUnit3').attr('disabled', 'disabled');
    $('#txt-discount3').val(0);

    toggleUnit('P');
    $('#txt-price').focus();
    return;
  }

  if(option == 'N'){
    $('#btn-set-price-no').addClass('btn-primary');
    $('#btn-set-price-yes').removeClass('btn-primary');
    $('#txt-price').attr('disabled', 'disabled');
    $('#txt-discount').removeAttr('disabled');
    $('#btn-aUnit').removeAttr('disabled');
    $('#btn-pUnit').removeAttr('disabled');

    $('#txt-discount2').removeAttr('disabled');
    $('#btn-aUnit2').removeAttr('disabled');
    $('#btn-pUnit2').removeAttr('disabled');

    $('#txt-discount3').removeAttr('disabled');
    $('#btn-aUnit3').removeAttr('disabled');
    $('#btn-pUnit3').removeAttr('disabled');

    $('#txt-price').val(0);
    $('#txt-discount').focus();
    return;
  }
}


function toggleUnit(option){
  $('#disc_unit').val(option);
  if(option == 'P'){
    disc = isNaN(parseFloat($('#txt-discount').val())) ? 0 : parseFloat($('#txt-discount').val());

    if(disc > 100){
      swal('ส่วนลดต้องไม่เกิน 100%');
      $('#txt-discount').val(0);
      return false;
    }

    $('#btn-pUnit').addClass('btn-primary');
    $('#btn-aUnit').removeClass('btn-primary');
    return;
  }

  if(option == 'A'){
    $('#btn-aUnit').addClass('btn-primary');
    $('#btn-pUnit').removeClass('btn-primary');
    return;
  }
}



function toggleUnit2(option){
  $('#disc_unit2').val(option);
  if(option == 'P'){
    disc = isNaN(parseFloat($('#txt-discount2').val())) ? 0 : parseFloat($('#txt-discount2').val());

    if(disc > 100){
      swal('ส่วนลดต้องไม่เกิน 100%');
      $('#txt-discount2').val(0);
      return false;
    }

    $('#btn-pUnit2').addClass('btn-primary');
    $('#btn-aUnit2').removeClass('btn-primary');
    return;
  }

  if(option == 'A'){
    $('#btn-aUnit2').addClass('btn-primary');
    $('#btn-pUnit2').removeClass('btn-primary');
    return;
  }
}



function toggleUnit3(option){
  $('#disc_unit3').val(option);
  if(option == 'P'){
    disc = isNaN(parseFloat($('#txt-discount3').val())) ? 0 : parseFloat($('#txt-discount3').val());

    if(disc > 100){
      swal('ส่วนลดต้องไม่เกิน 100%');
      $('#txt-discount').val(0);
      return false;
    }

    $('#btn-pUnit3').addClass('btn-primary');
    $('#btn-aUnit3').removeClass('btn-primary');
    return;
  }

  if(option == 'A'){
    $('#btn-aUnit3').addClass('btn-primary');
    $('#btn-pUnit3').removeClass('btn-primary');
    return;
  }
}




function toggleCanGroup(option){
  $('#can_group').val(option);
  if(option == 'Y'){
    $('#btn-cangroup-yes').addClass('btn-primary');
    $('#btn-cangroup-no').removeClass('btn-primary');
    return;
  }

  if(option == 'N'){
    $('#btn-cangroup-no').addClass('btn-primary');
    $('#btn-cangroup-yes').removeClass('btn-primary');
    return;
  }
}

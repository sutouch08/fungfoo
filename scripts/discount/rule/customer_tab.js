function saveCustomer(){
  id_rule = $('#id_rule').val();
  all_customer = $('#all_customer').val();
  customer_id = $('#customer_id').val();
  customer_group = $('#customer_group').val();
  customer_type = $('#customer_type').val();
  customer_kind = $('#customer_kind').val();
  customer_area = $('#customer_area').val();
  customer_class = $('#customer_class').val();

  countId = $('.custId').length;

  //--- ถ้าเลือกลูกค้าทั้งหมดจะไม่สนใจเงื่อนไขอื่นๆ
  if(all_customer == 'N'){

    //--- ถ้าเป็นการระบุชื่อลูกค้ารายคนแล้วยังไม่ได้ระบุ
    if(customer_id == 'Y' && countId == 0){
      swal('กรุณาระบุลูกค้าอย่างน้อย 1 ราย');
      return false;
    }

    if(customer_id == 'N'){
      count_group = parseInt($('.chk-group:checked').size());
      count_type  = parseInt($('.chk-type:checked').size());
      count_kind  = parseInt($('.chk-kind:checked').size());
      count_area  = parseInt($('.chk-area:checked').size());
      count_class = parseInt($('.chk-class:checked').size());
      sum_count = count_group + count_type + count_kind + count_area + count_class;


      //---- กรณีลือกลูกค้าแบบเป็นกลุ่มแล้วไม่ได้เลือก
      if(customer_group == 'Y' && count_group == 0 ){
        swal('กรุณาเลือกกลุ่มลูกค้าอย่างน้อย 1 รายการ');
        return false;
      }

      //---- กรณีลือกลูกค้าแบบเป็นชนิดแล้วไม่ได้เลือก
      if(customer_type == 'Y' && count_type == 0 ){
        swal('กรุณาเลือกชนิดลูกค้าอย่างน้อย 1 รายการ');
        return false;
      }

      //---- กรณีลือกลูกค้าแบบเป็นประเภทแล้วไม่ได้เลือก
      if(customer_kind == 'Y' && count_kind == 0 ){
        swal('กรุณาเลือกประเภทลูกค้าอย่างน้อย 1 รายการ');
        return false;
      }

      //---- กรณีลือกลูกค้าแบบเป็นเขตแล้วไม่ได้เลือก
      if(customer_area == 'Y' && count_area == 0 ){
        swal('กรุณาเลือกเขตลูกค้าอย่างน้อย 1 รายการ');
        return false;
      }

      //---- กรณีลือกลูกค้าแบบเป็นเกรดแล้วไม่ได้เลือก
      if(customer_class == 'Y' && count_class == 0 ){
        swal('กรุณาเลือกเกรดลูกค้าอย่างน้อย 1 รายการ');
        return false;
      }

      if(sum_count == 0){
        swal('กรุณาระบุอย่างน้อย 1 เงื่อนไข');
        return false;
      }

    } //-- end if customer_id == 'N'

  } //--- end if all_customer

  ds = [
    {'name':'id_rule', 'value':id_rule},
    {'name':'all_customer', 'value':all_customer},
    {'name':'customer_id', 'value':customer_id},
    {'name':'customer_group', 'value':customer_group},
    {'name':'customer_type', 'value':customer_type},
    {'name':'customer_kind', 'value':customer_kind},
    {'name':'customer_area', 'value':customer_area},
    {'name':'customer_class', 'value':customer_class}
  ];

  //--- เก็บข้อมูลชื่อลูกค้า
  if(customer_id == 'Y'){
    $('.custId').each(function(index, el) {
      ds.push({'name':$(this).attr('name'), 'value':$(this).val()});
    });
  }

  //--- เก็บข้อมูลกลุ่มลูกค้า
  if(customer_id == 'N' && customer_group == 'Y'){
    i = 0;
    $('.chk-group').each(function(index, el) {
      if($(this).is(':checked')){
        name = 'customerGroup['+i+']';
        ds.push({'name':name, 'value':$(this).val()});
        i++;
      }
    });
  }

  //--- เก็บข้อมูลชนิดลูกค้า
  if(customer_id == 'N' && customer_type == 'Y'){
    i = 0;
    $('.chk-type').each(function(index, el) {
      if($(this).is(':checked')){
        name = 'customerType['+i+']';
        ds.push({'name':name, 'value':$(this).val()});
        i++;
      }
    });
  }

  //--- เก็บข้อมูเลือกประเภทลูกค้า
  if(customer_id == 'N' && customer_kind == 'Y'){
    i = 0;
    $('.chk-kind').each(function(index, el){
      if($(this).is(':checked')){
        name = 'customerKind['+i+']';
        ds.push({'name':name, 'value':$(this).val()});
        i++;
      }
    });
  }

  //--- เก็บข้อมูลเขตลูกค้า
  if(customer_id == 'N' && customer_area == 'Y'){
    i = 0;
    $('.chk-area').each(function(index, el){
      if($(this).is(':checked')){
        name = 'customerArea['+i+']';
        ds.push({'name':name, 'value':$(this).val()});
        i++;
      }
    });
  }


  //--- เก็บข้อมูลเกรดลูกค้า
  if(customer_id == 'N' && customer_class == 'Y'){
    i = 0;
    $('.chk-class').each(function(index, el){
      if($(this).is(':checked')){
        name = 'customerClass['+i+']';
        ds.push({'name':name, 'value':$(this).val()});
        i++;
      }
    });
  }

  load_in();
  $.ajax({
    url:BASE_URL + 'discount/discount_rule/set_customer_rule',
    type:'POST',
    cache:'false',
    data:ds,
    success:function(rs){
      load_out();
      if(rs == 'success'){
        swal({
          title:'Saved',
          type:'success',
          timer:1000
        });
      }else{
        swal('Error!', rs, 'error');
      }
    }
  });
} //--- end function





function showCustomerGroup(){
  $('#cust-group-modal').modal('show');
}


function showCustomerList(){
  $('#cust-name-modal').modal('show');
}

function showCustomerClass(){
  $('#cust-class-modal').modal('show');
}

function showCustomerType(){
  $('#cust-type-modal').modal('show');
}

function showCustomerKind(){
  $('#cust-kind-modal').modal('show');
}

function showCustomerArea(){
  $('#cust-area-modal').modal('show');
}


$('#txt-cust-id-box').keyup(function(e){
  if(e.keyCode == 13){
    if($(this).val() != ''){
      addCustId();
    }
  }
});


$('.chk-group').change(function(e){
  count = 0;
  $('.chk-group').each(function(index, el) {
    if($(this).is(':checked')){
      count++;
    }
  });
  $('#badge-group').text(count);
});


$('.chk-type').change(function(e){
  count = 0;
  $('.chk-type').each(function(index, el) {
    if($(this).is(':checked')){
      count++;
    }
  });
  $('#badge-type').text(count);
});


$('.chk-kind').change(function(e){
  count = 0;
  $('.chk-kind').each(function(index, el) {
    if($(this).is(':checked')){
      count++;
    }
  });
  $('#badge-kind').text(count);
});


$('.chk-area').change(function(e){
  count = 0;
  $('.chk-area').each(function(index, el) {
    if($(this).is(':checked')){
      count++;
    }
  });
  $('#badge-area').text(count);
});


$('.chk-class').change(function(e){
  count = 0;
  $('.chk-class').each(function(index, el) {
    if($(this).is(':checked')){
      count++;
    }
  });
  $('#badge-class').text(count);
});


$('#txt-cust-id-box').autocomplete({
  source: BASE_URL + 'auto_complete/get_customer_code_and_name',
  autoFocus:true,
  close:function(){
    arr = $(this).val().split(' | ');
    if(arr.length == 2){
      code = arr[0];
      name = arr[1];
      $('#id_customer').val(code);
      $(this).val(code+' : '+name);
    }else{
      $(this).val('');
      $('#id_customer').val('');
    }
  }
});



function addCustId(){
  id = $('#id_customer').val();
  custName = $('#txt-cust-id-box').val();
  if(custName.length > 0){
    count = parseInt($('#count').text());
    count++;
    list  = '<li style="min-height:15px; padding:5px;" id="cust-id-'+id+'">';
    list += '<a href="#" class="paddint-5" onclick="removeCustId(\''+id+'\')"><i class="fa fa-times red"></i></a>';
    list += '<span style="margin-left:10px;">'+custName+'</span>';
    list += '</li>';

    input = '<input type="hidden" name="custId['+id+']" id="custId-'+id+'" class="custId" value="'+id+'" />';
    $('#cust-list').append(list);
    $('#cust-list').append(input);
    $('#count').text(count);

    $('#txt-cust-id-box').val('');
    $('#id_customer').val('');
    $('#txt-cust-id-box').focus();
  }

}



function removeCustId(id){
  count = parseInt($('#count').text());
  $('#cust-id-'+id).remove();
  $('#custId-'+id).remove();
  count--;
  $('#count').text(count);
}


//--- เลือกลูกค้าทั้งหมด
function toggleAllCustomer(option){
  $('#all_customer').val(option);
  if(option == 'Y'){
    $('#btn-cust-all-yes').addClass('btn-primary');
    $('#btn-cust-all-no').removeClass('btn-primary');
    disActiveCustomerControl();
  }else if(option == 'N'){
    $('#btn-cust-all-no').addClass('btn-primary');
    $('#btn-cust-all-yes').removeClass('btn-primary');
    $('.not-all').removeAttr('disabled');
    activeCustomerControl();
  }
}



function disActiveCustomerControl(){
  toggleCustomerGroup();
  toggleCustomerType();
  toggleCustomerKind();
  toggleCustomerArea();
  toggleCustomerClass();
  $('.not-all').attr('disabled', 'disabled');
}




function activeCustomerControl(){
  customer_id = $('#customer_id').val();
  if(customer_id == 'Y'){
    toggleCustomerGroup();
    toggleCustomerType();
    toggleCustomerKind();
    toggleCustomerArea();
    toggleCustomerClass();
    return;
  }

  toggleCustomerGroup($('#customer_group').val());
  toggleCustomerType($('#customer_type').val());
  toggleCustomerKind($('#customer_kind').val());
  toggleCustomerArea($('#customer_area').val());
  toggleCustomerClass($('#customer_class').val());
}






function toggleCustomerId(option){
  if(option == '' || option == undefined){
    option = $('#customer_id').val();
  }

  $('#customer_id').val(option);
  if(option == 'Y'){
    $('#btn-cust-id-yes').addClass('btn-primary');
    $('#btn-cust-id-no').removeClass('btn-primary');
    $('#txt-cust-id-box').removeAttr('disabled');
    $('#btn-cust-id-add').removeAttr('disabled');

  }else if(option == 'N'){
    $('#btn-cust-id-no').addClass('btn-primary');
    $('#btn-cust-id-yes').removeClass('btn-primary');
    $('#txt-cust-id-box').attr('disabled', 'disabled');
    $('#btn-cust-id-add').attr('disabled', 'disabled');
  }

  activeCustomerControl();
}


function toggleCustomerGroup(option){
  if(option == '' || option == undefined){
    option = $('#customer_group').val();
  }

  $('#customer_group').val(option);
  all = $('#all_customer').val();
  sc = $('#customer_id').val();
  if(option == 'Y' && sc == 'N' && all == 'N'){
    $('#btn-cust-group-no').removeClass('btn-primary');
    $('#btn-cust-group-yes').addClass('btn-primary');
    $('#btn-cust-group-no').removeAttr('disabled');
    $('#btn-cust-group-yes').removeAttr('disabled');
    $('#btn-select-cust-group').removeAttr('disabled');

    return;
  }

  if(option == 'N' && sc == 'N' && all == 'N'){
    $('#btn-cust-group-yes').removeClass('btn-primary');
    $('#btn-cust-group-no').addClass('btn-primary');
    $('#btn-cust-group-no').removeAttr('disabled');
    $('#btn-cust-group-yes').removeAttr('disabled');
    $('#btn-select-cust-group').attr('disabled', 'disabled');

    return;
  }

  if(all == 'Y' || sc == 'Y'){
    $('#btn-cust-group-yes').attr('disabled', 'disabled');
    $('#btn-cust-group-no').attr('disabled', 'disabled');
    $('#btn-select-cust-group').attr('disabled', 'disabled');
    return;
  }
}



function toggleCustomerType(option){
  if(option == '' || option == undefined){
    option = $('#customer_type').val();
  }

  $('#customer_type').val(option);
  sc = $('#customer_id').val();
  all = $('#all_customer').val();
  if(option == 'Y' && all == 'N' && sc == 'N'){
    $('#btn-cust-type-no').removeClass('btn-primary');
    $('#btn-cust-type-yes').addClass('btn-primary');
    $('#btn-cust-type-no').removeAttr('disabled');
    $('#btn-cust-type-yes').removeAttr('disabled');
    $('#btn-select-cust-type').removeAttr('disabled');
    return;
  }

  if(option == 'N' && sc == 'N' && all == 'N'){
    $('#btn-cust-type-yes').removeClass('btn-primary');
    $('#btn-cust-type-no').addClass('btn-primary');
    $('#btn-cust-type-yes').removeAttr('disabled');
    $('#btn-cust-type-no').removeAttr('disabled');
    $('#btn-select-cust-type').attr('disabled', 'disabled');

    return;
  }

  if(all == 'Y' || sc == 'Y'){
    $('#btn-cust-type-yes').attr('disabled', 'disabled');
    $('#btn-cust-type-no').attr('disabled', 'disabled');
    $('#btn-select-cust-type').attr('disabled', 'disabled');
  }
}



function toggleCustomerKind(option){
  if(option == '' || option == undefined){
    option = $('#customer_kind').val();
  }


  $('#customer_kind').val(option);
  sc = $('#customer_id').val();
  all = $('#all_customer').val();

  if(option == 'Y' && all == 'N' && sc == 'N'){
    $('#btn-cust-kind-no').removeClass('btn-primary');
    $('#btn-cust-kind-yes').addClass('btn-primary');
    $('#btn-cust-kind-no').removeAttr('disabled');
    $('#btn-cust-kind-yes').removeAttr('disabled');
    $('#btn-select-cust-kind').removeAttr('disabled');
    return;
  }

  if(option == 'N' && sc == 'N' && all == 'N'){
    $('#btn-cust-kind-yes').removeClass('btn-primary');
    $('#btn-cust-kind-no').addClass('btn-primary');
    $('#btn-cust-kind-no').removeAttr('disabled');
    $('#btn-cust-kind-yes').removeAttr('disabled');
    $('#btn-select-cust-kind').attr('disabled', 'disabled');
    return;
  }

  if(all == 'Y' || sc == 'Y'){
    $('#btn-cust-kind-yes').attr('disabled', 'disabled');
    $('#btn-cust-kind-no').attr('disabled', 'disabled');
    $('#btn-select-cust-kind').attr('disabled', 'disabled');
  }
}



function toggleCustomerArea(option){
  if(option == '' || option == undefined){
    option = $('#customer_area').val();
  }

  $('#customer_area').val(option);
  sc = $('#customer_id').val();
  all = $('#all_customer').val();
  if(option == 'Y' && all == 'N' && sc == 'N'){
    $('#btn-cust-area-no').removeClass('btn-primary');
    $('#btn-cust-area-yes').addClass('btn-primary');
    $('#btn-cust-area-no').removeAttr('disabled');
    $('#btn-cust-area-yes').removeAttr('disabled');
    $('#btn-select-cust-area').removeAttr('disabled');
    return;
  }

  if(option == 'N' && sc == 'N' && all == 'N'){
    $('#btn-cust-area-yes').removeClass('btn-primary');
    $('#btn-cust-area-no').addClass('btn-primary');
    $('#btn-cust-area-yes').removeAttr('disabled');
    $('#btn-cust-area-no').removeAttr('disabled');
    $('#btn-select-cust-area').attr('disabled', 'disabled');
    return;
  }

  if(all == 'Y' || sc == 'Y'){
    $('#btn-cust-area-yes').attr('disabled', 'disabled');
    $('#btn-cust-area-no').attr('disabled', 'disabled');
    $('#btn-select-cust-area').attr('disabled', 'disabled');
  }
}



function toggleCustomerClass(option){
  if(option == '' || option == undefined){
    option = $('#customer_class').val();
  }

  $('#customer_class').val(option);
  sc = $('#customer_id').val();
  all = $('#all_customer').val();
  if(option == 'Y' && all == 'N' && sc == 'N'){
    $('#btn-cust-class-no').removeClass('btn-primary');
    $('#btn-cust-class-yes').addClass('btn-primary');
    $('#btn-cust-class-no').removeAttr('disabled');
    $('#btn-cust-class-yes').removeAttr('disabled');
    $('#btn-select-cust-class').removeAttr('disabled');
    return;
  }

  if(option == 'N' && sc == 'N' && all == 'N'){
    $('#btn-cust-class-yes').removeClass('btn-primary');
    $('#btn-cust-class-no').addClass('btn-primary');
    $('#btn-cust-class-no').removeAttr('disabled');
    $('#btn-cust-class-yes').removeAttr('disabled');
    $('#btn-select-cust-class').attr('disabled', 'disabled');
    return;
  }

  if(all == 'Y' || sc == 'Y'){
    $('#btn-cust-class-no').attr('disabled', 'disabled');
    $('#btn-cust-class-yes').attr('disabled', 'disabled');
    $('#btn-select-cust-class').attr('disabled', 'disabled');
  }
}


$(document).ready(function() {
  var all = $('#all_customer').val();
  var custId = $('#customer_id').val();
  toggleAllCustomer(all);
  toggleCustomerId(custId);
});

//--- ปิดออเดอร์ (ตรวจเสร็จแล้วจ้า) เปลี่ยนสถานะ
function closeOrder(){
  var order_code = $("#order_code").val();

  var notsave = 0;
  //--- ตรวจสอบก่อนว่ามีรายการที่ยังไม่บันทึกค้างอยู่หรือไม่
  $(".hidden-qc").each(function(index, element){
    if( $(this).val() > 0){
      notsave++;
    }
  });

  //--- ถ้ายังมีรายการที่ยังไม่บันทึก ให้บันทึกก่อน
  if(notsave > 0){
    saveQc(2);
  }else{
    //--- close order
    $.ajax({
      url: HOME +'close_order',
      type:'POST',
      cache:'false',
      data:{
        "order_code": order_code
      },
      success:function(rs){
        var rs = $.trim(rs);
        if(rs == 'success'){
          swal({title:'Success', type:'success', timer:1000});
          $('#btn-close').attr('disabled', 'disabled');
          $(".zone").attr('disabled', 'disabled');
          $(".item").attr('disabled', 'disabled');
          $(".close").attr('disabled', 'disabled');
          $('#btn-print-address').removeClass('hide');
        }else{
          swal("Error!", rs, "error");
        }
      }
    });
  }

}





function forceClose(){
  swal({
    title: "คุณแน่ใจ ?",
    text: "ต้องการบังคับจบออเดอร์นี้หรือไม่ ?",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#FA5858",
    confirmButtonText: 'บังคับจบ',
    cancelButtonText: 'ยกเลิก',
    closeOnConfirm: false
    }, function(){
      closeOrder();
  });
}

//--- บันทึกยอดตรวจนับที่ยังไม่ได้บันทึก
function saveQc(option){
  //--- Option 0 = just save, 1 = change box after saved, 2 = close order after Saved
  var order_code = $("#order_code").val();
  var id_box = $("#id_box").val();

  if(id_box == '' || order_code == ''){
    return false;
  }

  var ds = [];
  ds.push({"name" : "order_code", "value" : order_code});
  ds.push({"name" : "id_box", "value" : id_box});
  $(".hidden-qc").each(function(index, element){
    var id = $(this).attr('id');
    var name = "rows["+id+"]";
    var value = $(this).val();
    ds.push( {"name" : name, "value" : value });
  });

  if(ds.length > 2){
    load_in();
    $.ajax({
      url: HOME + 'save_qc',
      type:"POST",
      cache:"false",
      data: ds,
      success:function(rs){
        load_out();
        var rs = $.trim(rs);
        if( rs == 'success'){

          //--- เอาสีน้ำเงินออกเพื่อให้รู้ว่าบันทึกแล้ว
          $(".blue").removeClass('blue');

          //---
          if(option == 0){

            swal({
              title:'Saved',
              type:'success',
              timer:1000
            });

            setTimeout(function(){ $("#barcode-item").focus();}, 2000);

          }

          //--- รีเซ็ตจำนวนที่ยังไม่ได้บันทึก
          $('.hidden-qc').each(function(index, element){
            $(this).val(0);
          });


          //--- ถ้ามาจากการเปลี่ยนกล่อง
          if( option == 1){

            swal({
              title:'Saved',
              type:'success',
              timer:1000
            } );

            setTimeout(function(){ changeBox(); }, 1200);

          }

          //--- ถ้ามาจากการกดปุ่ม ตรวจเสร็จแล้ว หรือ ปุ่มบังคับจบ
          if( option == 2){
            closeOrder();
          }

        }else {
          //--- ถ้าผิดพลาด
          swal("Error!", rs, "error");
        }

      }
    });
  }
}





//--- เมื่อยิงบาร์โค้ด
$("#barcode-item").keyup(function(e){
  if( e.keyCode == 13 && $(this).val() != "" ){
    qcProduct();
  }
});



function qcProduct(){
  var barcode = $("#barcode-item").val();
  $("#barcode-item").val('');

  if($("."+barcode).length == 1 ){

      var id = $("."+barcode).attr('id');
      var qty = parseInt($("."+barcode).val());

      //--- จำนวนที่จัดมา
      var prepared = parseInt( removeCommas( $("#prepared-"+id).text() ) );

      //--- จำนวนที่ตรวจไปแล้วยังไม่บันทึก
      var notsave = parseInt( removeCommas( $("#"+id).val() ) ) + qty;

      //--- จำนวนที่ตรวจแล้วทั้งหมด (รวมที่ยังไม่บันทึก) ของสินค้านี้
      var qc_qty = parseInt( removeCommas( $("#qc-"+id).text() ) ) + qty;

      //--- จำนวนสินค้าที่ตรวจแล้วทั้งออเดอร์ (รวมที่ยังไม่บันทึกด้วย)
      var all_qty = parseInt( removeCommas( $("#all_qty").text() ) ) + qty;

      //--- ถ้าจำนวนที่ตรวจแล้ว
      if(qc_qty <= prepared){

        $("#"+id).val(notsave);

        $("#qc-"+id).text(addCommas(qc_qty));

        //--- อัพเดตจำนวนในกล่อง
        updateBox(qc_qty);

        //--- อัพเดตยอดตรวจรวมทั้งออเดอร์
        $("#all_qty").text( addCommas(all_qty));

        //--- เปลียนสีแถวที่ถูกตรวจแล้ว
        $("#row-"+id).addClass('blue');


        //--- ย้ายรายการที่กำลังตรวจขึ้นมาบรรทัดบนสุด
        $("#incomplete-table").prepend($("#row-"+id));


        //--- ถ้ายอดตรวจครบตามยอดจัดมา
        if( qc_qty == prepared ){

          //--- ย้ายบรรทัดนี้ลงข้างล่าง(รายการที่ครบแล้ว)
          $("#complete-table").append($("#row-"+id));
          $("#row-"+id).removeClass('incomplete');
        }


        if($(".incomplete").length == 0 ){
          showCloseButton();
        }

      }else{
        beep();
        swal("สินค้าเกิน!");
      }

  }else{
    beep();
    swal("สินค้าไม่ถูกต้อง");
  }

}



function updateBox(){
  var id_box = $("#id_box").val();
  var qty = parseInt( removeCommas( $("#"+id_box).text() ) ) +1 ;
  $("#"+id_box).text(addCommas(qty));
}



function updateBoxList(){
  var id_box = $("#id_box").val();
  var order_code = $("#order_code").val();

  $.ajax({
    url: HOME + 'get_box_list',
    type:"GET",
    cache: "false",
    data:{
      "order_code" : order_code,
      "id_box" : id_box
    },
    success:function(rs){
      var rs = $.trim(rs);
      if(isJson(rs)){
        var source = $("#box-template").html();
        var data = $.parseJSON(rs);
        var output = $("#box-row");
        render(source, data, output);
      }else if(rs == "no box"){
        $("#box-row").html('<span id="no-box-label">ยังไม่มีการตรวจสินค้า</span>');
      }else{
        swal("Error!", rs, "error");
      }
    }
  });
}



//---
$("#barcode-box").keyup(function(e){
  if(e.keyCode == 13){
    if( $(this).val() != ""){
      getBox();
    }
  }
});



//--- ดึงไอดีกล่อง
function getBox(){
  var barcode = $("#barcode-box").val();
  var order_code = $("#order_code").val();
  if( barcode.length > 0){
    $.ajax({
      url: HOME + 'get_box',
      type:"GET",
      cache:"false",
      data:{
        "barcode":barcode,
        "order_code" : order_code
      },
      success:function(rs){
        var rs = $.trim(rs);
        if( ! isNaN( parseInt(rs) ) ){
          $("#id_box").val(rs);
          $("#barcode-box").attr('disabled', 'disabled');
          $(".item").removeAttr('disabled');
          $("#barcode-item").focus();
          updateBoxList();
        }else{
          swal("Error!", rs, "error");
        }
      }
    });
  }
}



function confirmSaveBeforeChangeBox(){
  var count = 0;
  $(".hidden-qc").each(function(index, element){
    if( $(this).val() > 0){
      count++;
    }
  });

  if( count > 0 ){
    swal({
  		title: "บันทึกรายการก่อน ?",
  		text: "คุณจำเป็นต้องบันทึกรายการก่อนที่จะเปลี่ยนกล่องใหม่",
  		type: "warning",
  		showCancelButton: true,
  		confirmButtonColor: "#5FB404",
  		confirmButtonText: 'บันทึก',
  		cancelButtonText: 'ยกเลิก',
  		closeOnConfirm: false
  		}, function(){
  			saveQc(1);
  	});
  }else {
    changeBox();
  }
}





function changeBox(){

  $("#id_box").val('');
  $("#barcode-item").val('');
  $(".item").attr('disabled', 'disabled');
  $("#barcode-box").removeAttr('disabled');
  $("#barcode-box").val('');
  $("#barcode-box").focus();
}




function showCloseButton(){
  $("#force-bar").addClass('hide');
  $("#close-bar").removeClass('hide');
}


function showForceCloseBar(){
  $("#close-bar").addClass('hide');
  $("#force-bar").removeClass('hide');
}

function updateQty(id_qc){
  remove_qty = Math.ceil($('#input-'+id_qc).val());
  limit = parseInt($('#label-'+id_qc).text());
  limit = isNaN(limit) ? 0 : limit;

  if(remove_qty > limit){
    swal('ยอดที่เอาออกต้องไม่มากกว่ายอดตรวจนับ');
    return false;
  }

  if(limit >= remove_qty){
    load_in();
    $.ajax({
      url:'controller/qcController.php?decreaseCheckedQty',
      type:'POST',
      cache:'false',
      data:{
        'id_qc' : id_qc,
        'remove_qty' : remove_qty
      },
      success:function(rs){
        load_out();
        var rs = $.trim(rs);
        if(rs == 'success'){
          qty = limit - remove_qty;
          $('#label-'+id_qc).text(qty);
          $('#input-'+id_qc).val('');
        }
      }
    });
  }
}



function showEditOption(order_code, product_code, pdCode){
  $('#edit-title').text(pdCode);
  load_in();
  $.ajax({
    url:'controller/qcController.php?getCheckedTable',
    type:'GET',
    cache:'false',
    data:{
      'order_code' : order_code,
      'product_code' : product_code
    },
    success:function(rs){
      load_out();
      var rs = $.trim(rs);
      if(isJson(rs)){
        var source = $('#edit-template').html();
        var data = $.parseJSON(rs);
        var output = $('#edit-body');
        render(source, data, output);
        $('#edit-modal').modal('show');
      }else{
        swal('Error!',rs, 'error');
      }
    }
  });
}


$('.bc').click(function(){
  if(!$('#barcode-item').prop('disabled'))
  {
    var bc = $.trim($(this).text());
    $('#barcode-item').val(bc);
    $('#barcode-item').focus();
  }
});

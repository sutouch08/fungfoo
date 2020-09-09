var HOME = BASE_URL + 'masters/zone';


function goBack(){
  window.location.href = HOME;
}

function getSearch(){
  $('#searchForm').submit();
}


function clearFilter(){
  $.get(HOME +'/clear_filter', function(){
    goBack();
  });
}



function getEdit(code){
  window.location.href = HOME + '/edit/'+code;
}


function addNew(){
  window.location.href = HOME + '/add_new';
}


function checkAdd(){
  var code = $('#code').val();
  var name = $('#name').val();
  var warehouse = $('#warehouse').val();

  if(code.length === 0){
    set_error($('#code'), $('#code-error'), 'Required');
    return false;
  }else{
    clear_error($('#code'), $('#code-error'));
  }

  if(name.length === 0){
    set_error($('#name'), $('#name-error'), 'Required');
    return false;
  }else{
    clear_error($('#name'), $('#name-error'));
  }

  if(warehouse == ""){
    set_error($('#warehouse'), $('#warehouse-error'), 'Please Choose');
    return false;
  }else{
    clear_error($('#warehouse'), $('#warehouse-error'));
  }

  //--- check duplicate code
  $.ajax({
    url:HOME + '/is_exists_code/' + code,
    type:'GET',
    cache:false,
    success:function(rs){
      if(rs != 'ok'){
        set_error($('#code'), $('#code-error'), rs);
        return false;
      }else{
        clear_error($('#code'), $('#code-error'));
        $.ajax({
          url:HOME + '/is_exists_name/'+ name,
          type:'GET',
          cache:false,
          success:function(rs){
            if(rs != 'ok'){
              set_error($('#name'), $('#name-error'), rs);
              return false;
            }else{
              clear_error($('#name'), $('#name-error'));
              $('#addForm').submit();
            }
          }
        });
      }
    }
  });
}



function checkUpdate(){
  var code = $('#code').val();
  var old_code = $('#old_code').val();
  var name = $('#name').val();
  var old_name = $('#old_name').val();
  var warehouse = $('#warehouse').val();

  if(code.length === 0){
    set_error($('#code'), $('#code-error'), 'Required');
    return false;
  }else{
    clear_error($('#code'), $('#code-error'));
  }

  if(name.length === 0){
    set_error($('#name'), $('#name-error'), 'Required');
    return false;
  }else{
    clear_error($('#name'), $('#name-error'));
  }

  if(warehouse == ""){
    set_error($('#warehouse'), $('#warehouse-error'), 'Please Choose');
    return false;
  }else{
    clear_error($('#warehouse'), $('#warehouse-error'));
  }

  //--- check duplicate code
  $.ajax({
    url:HOME + '/is_exists_code/' + code +'/'+old_code,
    type:'GET',
    cache:false,
    success:function(rs){
      if(rs != 'ok'){
        set_error($('#code'), $('#code-error'), rs);
        return false;
      }else{
        clear_error($('#code'), $('#code-error'));
        $.ajax({
          url:HOME + '/is_exists_name/'+ name +'/' + name,
          type:'GET',
          cache:false,
          success:function(rs){
            if(rs != 'ok'){
              set_error($('#name'), $('#name-error'), rs);
              return false;
            }else{
              clear_error($('#name'), $('#name-error'));
              $('#addForm').submit();
            }
          }
        });
      }
    }
  });
}



$('#search-box').autocomplete({
  source:BASE_URL + 'auto_complete/get_customer_code_and_name',
  autoFocus:true,
  close:function(){
    let arr = $(this).val().split(' | ');
    if(arr.length == 2){
      let code = arr[0];
      let name = arr[1];
      $(this).val(name);
      $('#customer_code').val(code);
    }else{
      $(this).val('');
      $('#customer_code').val('');
    }
  }
});


$('#search-box').keyup(function(e){
  if(e.keyCode == 13){
    addCustomer();
  }
});


function addCustomer(){
  let code = $('#zone_code').val();
  let customer_code = $('#customer_code').val();
  let customer_name = $('#search-box').val();
  if(code === undefined){
    swal('ไม่พบรหัสโซน');
    return false;
  }

  if(customer_code == '' || customer_name.length == 0){
    swal('ชื่อลูกค้าไม่ถูกต้อง');
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + '/add_customer',
    type:'POST',
    cache:false,
    data:{
      'zone_code' : code,
      'customer_code' : customer_code
    },
    success:function(rs){
      load_out();
      if(rs === 'success'){
        swal({
          title:'Success',
          text:'เพิ่มลูกค้าเรียบร้อยแล้ว',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          window.location.reload();
        }, 1200);
      }else{
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        });
      }
    }
  });
}



function getDelete(code){
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ ' + code + ' หรือไม่ ?',
    type:'warning',
    showCancelButton: true,
		confirmButtonColor: '#FA5858',
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
  },function(){
    $.ajax({
      url: HOME + '/delete/' + code,
      type:'GET',
      cache:false,
      success:function(rs){
        if(rs === 'success'){
          swal({
            title:'Deleted',
            text:'ลบ '+code+' เรียบร้อยแล้ว',
            type:'success',
            timer:1000
          });
          $('#row-'+code).remove();
          reIndex();
        }else{
          swal({
            title:'Error!',
            text:rs,
            type:'error'
          });
        }
      }
    })

  })
}



function deleteCustomer(id,code){
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ ' + code + ' หรือไม่ ?',
    type:'warning',
    showCancelButton: true,
		confirmButtonColor: '#FA5858',
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
  },function(){
    $.ajax({
      url: HOME + '/delete_customer/' + id,
      type:'GET',
      cache:false,
      success:function(rs){
        if(rs === 'success'){
          swal({
            title:'Deleted',
            text:'ลบ '+code+' เรียบร้อยแล้ว',
            type:'success',
            timer:1000
          });
          $('#row-'+id).remove();
          reIndex();
          $('#search-box').focus();
        }else{
          swal({
            title:'Error!',
            text:rs,
            type:'error'
          });
        }
      }
    })

  })
}




function syncData(){
  load_in();
  $.get(HOME +'/syncData', function(){
    load_out();
    swal({
      title:'Completed',
      type:'success',
      timer:1000
    });
    setTimeout(function(){
      goBack();
    }, 1500);
  });
}

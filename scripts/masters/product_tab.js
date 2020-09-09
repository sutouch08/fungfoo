var HOME = BASE_URL + 'masters/product_tab/';

function goBack(){
  window.location.href = HOME;
}

function addNew(){
  window.location.href = HOME + 'add_new';
}



function getEdit(id){
  window.location.href = HOME + 'edit/'+id;
}

function saveAdd(){
  addForm.submit();
}


function clearFilter(){
  $.get(HOME + 'clear_filter', function(){
    goBack();
  });
}


function getDelete(id, name){
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ ' + name + ' หรือไม่ ?',
    type:'warning',
    showCancelButton: true,
		confirmButtonColor: '#FA5858',
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
  },function(){
    $.ajax({
      url: HOME + 'delete/' + id,
      type:'GET',
      cache:false,
      success:function(rs){
        if(rs === 'success'){
          swal({
            title:'Deleted',
            text:'ลบรายการเรียบร้อยแล้ว',
            type:'success',
            timer:1000
          });

          $('#row-'+id).remove();
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



function getSearch(){
  $('#searchForm').submit();
}


function doExport(code){
  load_in();
  $.ajax({
    url:BASE_URL + 'masters/products/export_products/'+code,
    type:'POST',
    cache:false,
    success:function(rs){
      load_out();
      if(rs === 'success'){
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });
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

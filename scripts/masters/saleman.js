var HOME = BASE_URL + 'masters/saleman/';

function goBack(){
  window.location.href = HOME;
}



function addNew(){
  window.location.href = HOME + 'add_new';
}

function goEdit(code){
  window.location.href = HOME + 'edit/'+code;
}


function getDelete(code, name){
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
      url: HOME + 'delete/'+code,
      type:'POST',
      cache:false,
      success:function(rs){
        if(rs == 'success'){
          swal({
            title:'Deleted',
            text:'ลบ '+name+' เรียบร้อยแล้ว',
            type:'success',
            timer:1000
          });

          setTimeout(function(){
            window.location.reload();
          }, 1500);
        }else{
          swal({
            title:'Error!',
            text:rs,
            type:'error'
          });
        }
      }
    });
  })
}


function getSearch(){
  $('#searchForm').submit();
}


$('.search-box').keyup(function(e){
  if(e.keyCode == 13){
    getSearch();
  }
});


function clearFilter(){
  $.get(HOME + 'clear_filter', function(){
    goBack();
  });
}

function goBack(){
  window.location.href = HOME;
}


function syncData(){
  load_in();
  $.ajax({
    url:HOME + 'syncData',
    type:'POST',
    cache:false,
    success:function(rs){
      load_out();
      if(rs == 'success'){
        swal({
          title:'Completed',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          goBack();
        }, 1500);
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

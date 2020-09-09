<div class="modal fade" id="upload-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
 <div class="modal-dialog" style="width:500px;">
   <div class="modal-content">
       <div class="modal-header">
       <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
       <h4 class="modal-title">นำเข้าไฟล์ Excel</h4>
      </div>
      <div class="modal-body">
        <form id="upload-form" name="upload-form" method="post" enctype="multipart/form-data">
        <div class="row">
          <div class="col-sm-9">
            <button type="button" class="btn btn-sm btn-primary btn-block" id="show-file-name" onclick="getFile()">กรุณาเลือกไฟล์ Excel</button>
          </div>

          <div class="col-sm-3">
            <button type="button" class="btn btn-sm btn-info" onclick="uploadfile()"><i class="fa fa-cloud-upload"></i> นำเข้า</button>
          </div>
        </div>
        <input type="file" class="hide" name="uploadFile" id="uploadFile" accept=".xlsx" />
        <input type="hidden" name="555" />
        </form>
       </div>
      <div class="modal-footer">

      </div>
   </div>
 </div>
</div>

<script>

function getUploadFile(){
  $('#upload-modal').modal('show');
}



function getFile(){
  $('#uploadFile').click();
}








$("#uploadFile").change(function(){
	if($(this).val() != '')
	{
		var file 		= this.files[0];
		var name		= file.name;
		var type 		= file.type;
		var size		= file.size;

		if( size > 5000000 )
		{
			swal("ขนาดไฟล์ใหญ่เกินไป", "ไฟล์แนบต้องมีขนาดไม่เกิน 5 MB", "error");
			$(this).val('');
			return false;
		}
		//readURL(this);
    $('#show-file-name').text(name);
	}
});



	function uploadfile()
	{
    $('#upload-modal').modal('hide');

		var file	= $("#uploadFile")[0].files[0];
		var fd = new FormData();
		fd.append('uploadFile', $('input[type=file]')[0].files[0]);
		if( file !== '')
		{
			load_in();
			$.ajax({
				url:BASE_URL + 'orders/import_order', //"controller/importController.php?importOrderFromWeb",
				type:"POST",
        cache:"false",
        data: fd,
        processData:false,
        contentType: false,
				success: function(rs){
					load_out();
					var rs = $.trim(rs);
          if(rs === 'success'){
            swal({
              title: 'นำเข้าเรียบร้อยแล้ว',
              text : rs,
              type: 'success',
              html:true,
              timer:1000
            });

            setTimeout(function(){
              window.location.reload();
            }, 1200);
          }else{
            swal({
              title:'Error!!',
              text:rs,
              type:'error'
            });
          }
				}
			});
		}
	}
</script>

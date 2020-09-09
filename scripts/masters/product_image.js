// JavaScript Document

$(document).ready(function(e) {
   setColorbox();
});


//----------------  Dropzone --------------------//
Dropzone.autoDiscover = false;
var myDropzone = new Dropzone("#imageForm", {
	url: BASE_URL + 'masters/product_images/upload_images/' + $('#style').val(),
	paramName: "file", // The name that will be used to transfer the file
	maxFilesize: 2, // MB
	uploadMultiple: true,
	maxFiles: 10,
	acceptedFiles: "image/*",
	parallelUploads: 10,
	autoProcessQueue: false,
	addRemoveLinks: true
});

myDropzone.on('complete', function(){
	clearUploadBox();
	//loadImageTable();
  window.location.reload();
});

function doUpload()
{
	myDropzone.processQueue();
}

function clearUploadBox()
{
	$("#uploadBox").modal('hide');
	myDropzone.removeAllFiles();
}

function showUploadBox()
{
	$("#uploadBox").modal('show');
}




function removeImage(style, id_img)
{
  swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการลบรูปภาพ หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#FA5858",
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
      $.ajax({
    		url: BASE_URL + 'masters/product_images/remove_image',
    		type:"POST",
        cache:"false",
        data:{
          "style" : style,
          "id_image" : id_img
        },
    		success: function(rs){
    			var rs = $.trim(rs);
    			if( rs == 'success' )
    			{
            swal({
              title:'Deleted',
              text:'ลบรูปภาพเรียบร้อยแล้ว',
              type:'success',
              timer:1000
            });

    				$("#div-image-"+id_img).remove();
    			}
    			else
    			{
    				swal("ข้อผิดพลาด", "ลบรูปภาพไม่สำเร็จ", "error");
    			}
    		}
    	});
	});
}



function showNewCover(id){
	$(".btn-cover").removeClass('btn-success');
	$("#btn-cover-"+id).addClass('btn-success');
}



function setAsCover(id_pd, id_img)
{
	$.ajax({
		url: BASE_URL + 'masters/product_images/set_cover_image',
		type:"POST",
    cache:"false",
    data:{
      "style" :  $('#style').val(),
      "id_image" : id_img
    },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'success' )
			{
				$(".btn-cover").removeClass('btn-success');
				$("#btn-cover-"+id_img).addClass('btn-success');
			}
		}
	});
}




function setColorbox()
{
	var colorbox_params = {
				rel: 'colorbox',
				reposition: true,
				scalePhotos: true,
				scrolling: false,
				previous: '<i class="fa fa-arrow-left"></i>',
				next: '<i class="fa fa-arrow-right"></i>',
				close: 'X',
				current: '{current} of {total}',
				maxWidth: '800px',
				maxHeight: '800px',
				opacity:0.5,
				speed: 500,
				onComplete: function(){
					$.colorbox.resize();
				}
		}

	$('[data-rel="colorbox"]').colorbox(colorbox_params);
}

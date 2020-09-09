function goBack(){
  window.location.href = BASE_URL+'users/permission';
}

function getEdit(id){
  window.location.href = BASE_URL + 'users/permission/edit_permission/'+id;
}


function clearFilter(){
  var url = BASE_URL+'users/permission/clear_filter';
  var page = BASE_URL+'users/permission';
  $.get(url, function(rs){
    window.location.href = page;
  });
}




function groupViewCheck(el, id)
{
	if(el.is(":checked")){
		$(".view-"+id).each(function(index, element) {
			$(this).prop("checked",true);
		});
	}else{
		$(".view-"+id).each(function(index, element) {
			$(this).prop("checked",false);
		});
	}
}

function groupAddCheck(el, id)
{
	if(el.is(":checked")){
		$(".add-"+id).each(function(index, element) {
			$(this).prop("checked",true);
		});
	}else{
		$(".add-"+id).each(function(index, element) {
			$(this).prop("checked",false);
		});
	}
}

function groupEditCheck(el, id)
{
	if(el.is(":checked")){
		$(".edit-"+id).each(function(index, element) {
			$(this).prop("checked",true);
		});
	}else{
		$(".edit-"+id).each(function(index, element) {
			$(this).prop("checked",false);
		});
	}
}

function groupDeleteCheck(el, id)
{
	if(el.is(":checked")){
		$(".delete-"+id).each(function(index, element) {
			$(this).prop("checked",true);
		});
	}else{
		$(".delete-"+id).each(function(index, element) {
			$(this).prop("checked",false);
		});
	}
}

function groupApproveCheck(el, id)
{
	if(el.is(":checked")){
		$(".approve-"+id).each(function(index, element) {
			$(this).prop("checked",true);
		});
	}else{
		$(".approve-"+id).each(function(index, element) {
			$(this).prop("checked",false);
		});
	}
}


function groupAllCheck(el, id)
{
  var view = $("#view-group-"+id);
  var add = $("#add-group-"+id);
  var edit = $("#edit-group-"+id);
  var del  = $("#delete-group-"+id);
  var ap = $('#approve-group-'+id);

	if(el.is(":checked")){
		view.prop("checked", true);
		groupViewCheck(view, id);
		add.prop("checked", true);
		groupAddCheck(add, id);
		edit.prop("checked", true);
		groupEditCheck(edit, id);
		del.prop("checked", true);
		groupDeleteCheck(del, id);
    ap.prop("checked", true);
    groupApproveCheck(ap, id);

	}else{
    view.prop("checked", false);
		groupViewCheck(view, id);
		add.prop("checked", false);
		groupAddCheck(add, id);
		edit.prop("checked", false);
		groupEditCheck(edit, id);
		del.prop("checked", false);
		groupDeleteCheck(del, id);
    ap.prop("checked", false);
    groupApproveCheck(ap, id);

	}
}


function allCheck(el, id_tab){
	if(el.is(":checked")){
		$("."+id_tab).each(function(index, element) {
            $(this).prop("checked", true);
        });
	}else{
		$("."+id_tab).each(function(index, element) {
            $(this).prop("checked", false);
        });
	}
}



function savePermission(){
  $('#permissionForm').submit();
}

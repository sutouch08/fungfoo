
<div class="row">
  <div class="col-sm-2 padding-5 first">
    <label><?php label('style'); ?></label>
    <input type="text" class="form-control input-sm text-center" name="pdCode" id="pd-code" value="" autofocus>
  </div>
  <div class="col-sm-1 padding-5">
    <label class="display-block not-show">search</label>
    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="getProductGrid()">ดึงรายการ</button>
  </div>


  <div class="col-sm-1 col-sm-offset-6 padding-5">
    <?php if(!empty($doc->po_code)) : ?>
    <label class="display-block not-show">getPo</label>
    <button type="button" class="btn btn-xs btn-info btn-block" onclick="getData()"><?php label('get_po'); ?></button>
    <?php endif; ?>
  </div>
  <div class="col-sm-2 padding-5 last">
    <label class="display-block not-show">delete</label>
    <button type="button" class="btn btn-xs btn-danger btn-block" onclick="clearAll()"><?php label('delete_all'); ?></button>
  </div>
</div>
<hr class="margin-top-15">


<form id="orderForm">
<div class="modal fade" id="orderGrid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" id="modal">
		<div class="modal-content">
  			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="modalTitle">title</h4>
        <center><span style="color: red;">ใน ( ) = ยอดคงเหลือทั้งหมด   ไม่มีวงเล็บ = สั่งได้ทันที</span></center>
			 </div>
			 <div class="modal-body" id="modalBody"></div>
			 <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
				<button type="button" class="btn btn-primary" onClick="insert_item()" >เพิ่มในรายการ</button>
			 </div>
		</div>
	</div>
</div>
</form>


<div class="modal fade" id="poGrid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:800px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <center style="margin-bottom:10px;"><h4 class="modal-title" id="po-title">title</h4></center>
      </div>
      <div class="modal-body">
        <table class="table table-striped table-bordered">
          <thead>
            <th class="width-10 text-center"><?php label('Num'); ?></th>
            <th class="width-20 text-center"><?php label('item_code'); ?></th>
            <th class="text-center"><?php label('item_name'); ?></th>
            <th class="width-10 text-center"><?php label('price'); ?></th>
            <th class="width-10 text-center"><?php label('po_backlogs'); ?></th>
            <th class="width-10 text-center"><?php label('qty'); ?></th>
          </thead>
          <tbody id="po-body">

          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="btn_close" data-dismiss="modal">ปิด</button>
        <button type="button" class="btn btn-primary" onclick="insertPoItems()">เพิ่มในรายการ</button>
       </div>
    </div>
  </div>
</div>



<script id="row-template" type="text/x-handlebarsTemplate">
{{#each this}}
<tr>
  <td class="text-center middle no">{{no}}</td>
  <td class="middle">{{pdCode}}</td>
  <td class="middle">{{pdName}}</td>
  <td class="middle text-center">
    <input type="number" class="form-control input-sm text-center receive-box" id="receive-{{id_pa}}" value="{{qty}}" />
    <span class="hide" id="label-{{id_pa}}">{{qty}}</span>
    <input type="hidden" id="productId-{{id_pa}}" value="{{id_pd}}" />
  </td>
  <td class="middle text-center">
    <button type="button" class="btn btn-sm btn-danger" id="btn-remove-{{id_pa}}" onclick="deleteRow({{id_pa}})"><i class="fa fa-trash"></i></button>
  </td>
</tr>
{{/each}}
</script>


<script id="po-template" type="text/x-handlebarsTemplate">
{{#each this}}
<tr class="item-row">
  <td class="text-center middle no">{{no}}</td>
  <td class="middle">{{pdCode}}</td>
  <td class="middle">{{pdName}}</td>
  <td class="middle text-center">{{price}}</td>
  <td class="middle text-center">{{backlogs}}</td>
  <td class="middle text-center">
    <input type="number" class="form-control input-sm text-center receive_qty" id="{{pdCode}}" value="" />
  </td>
</tr>
{{/each}}
</script>

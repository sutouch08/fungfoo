<?php $sumStock = 0; ?>
<?php $sumCount = 0; ?>
<?php $sumDiff = 0; ?>

<div class="row">
  <div class="col-sm-2 col-sm-offset-3 text-center">
    <label class="display-block">ในโซน</label>
    <span><h4 class="title" id="total-zone">Loading...</h4></span>
  </div>
  <div class="col-sm-2 text-center">
    <label class="display-block">ตรวจนับ</label>
    <span><h4 class="title" id="total-checked">Loading...</h4></span>
  </div>
  <div class="col-sm-2 text-center">
    <label class="display-block">ยอดต่าง</label>
    <span><h4 class="title" id="total-diff">Loading...</h4></span>
  </div>
  <div class="col-sm-3 text-right top-col">
    <button type="button" class="btn btn-sm btn-info" onclick="getBoxList()"><i class="fa fa-file-text"></i> พิมพ์ใบปะหน้ากล่อง</button>
  </div>
</div>
<hr/>
<div class="row"></div>
<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped border-1">
			<thead>
				<tr>
					<th class="width-5 middle text-center">No.</th>
					<th class="width-15 middle">บาร์โค้ด</th>
					<th class="width-40 middle">รหัสสินค้า</th>
					<th class="width-10 middle text-right">ยอดในโซน</th>
					<th class="width-10 middle text-right">ยอดตรวจนับ</th>
					<th class="width-10 middle text-right">ยอดต่าง</th>
					<th class="width-10 middle text-right"></th>
				</tr>
			</thead>
			<tbody id="detail-table">
<?php if(!empty($details)) : ?>
  <?php $no = 1; ?>
	<?php foreach($details as $rs) : ?>
		<?php $diff = $rs->stock_qty - $rs->qty; ?>
			 	<tr id="row-<?php echo $rs->barcode; ?>">
				<td class="middle text-center no">
					<?php echo $no; ?>
				</td>
				<td class="middle">
					<input type="hidden" id="barcode_<?php echo $rs->barcode; ?>" value="<?php echo $rs->barcode; ?>">
					<span class="barcode"><?php echo $rs->barcode; ?></span>
				</td>
				<td class="middle"><?php echo $rs->product_code; ?></td>
				<td class="middle text-right">
					<span class="stock-qty" id="stock_qty_<?php echo $rs->barcode; ?>"><?php echo $rs->stock_qty; ?></span>
				</td>
				<td class="middle text-right">
					<span class="checked-qty checked-<?php echo $rs->product_code; ?>" id="check_qty_<?php echo $rs->barcode; ?>"><?php echo $rs->qty; ?></span>
				</td>
				<td class="middle text-right">
					<span class="diff-qty diff-<?php echo $rs->product_code; ?>" id="diff_qty_<?php echo $rs->barcode; ?>">
						<?php echo $diff; ?>
					</span>
				</td>
				<td class="middle text-right">
					<?php $hide = $rs->qty > 0 ? '' : 'hide'; ?>
					<button type="button" class="btn btn-minier btn-info <?php echo $hide; ?>" id="btn-<?php echo $rs->barcode; ?>" onclick="showDetail('<?php echo $rs->product_code; ?>')">
						<i class="fa fa-eye"></i>
					</button>
				</td>
			 	</tr>
		<?php $no++; ?>
		<?php $sumStock += $rs->stock_qty; ?>
		<?php $sumCount += $rs->qty; ?>
		<?php $sumDiff  += $diff; ?>
	<?php endforeach; ?>
<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
<input type="hidden" id="sumStock" value="<?php echo $sumStock; ?>">
<input type="hidden" id="sumCount" value="<?php echo $sumCount; ?>">
<input type="hidden" id="sumDiff" value="<?php echo $sumDiff; ?>">


<div class="modal fade" id="checked-detail-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width:400px;">
		<div class="modal-content">
  			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			 </div>
			 <div class="modal-body" id="modal_body">

       </div>
			 <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
			 </div>
		</div>
	</div>
</div>


<script id="box-detail-template" type="text/handlebarsTemplate">
<div class="row">
  <div class="col-sm-12">
    <table class="table table-bordered">
      <tr>
        <td colspan="3" class="text-center">{{pdCode}}</td>
      </tr>
      <tr>
        <td class="width-50 text-center">กล่อง</td>
        <td class="width-30 text-center">จำนวน</td>
        <td class="width-20 text-right"></td>
      </tr>
      {{#each rows}}
        {{#if pdCode}}
        <tr id="row-{{id_box}}-{{pdCode}}">
          <td class="middle text-center">{{ box }}</td>
          <td class="middle text-center">{{ qty }}</td>
          <td class="text-right">
     <?php if(($this->pm->can_add OR $this->pm->can_edit) && $doc->valid == 0 && $doc->status == 0) : ?>
            <button type="button" class="btn btn-xs btn-danger" onclick="removeCheckedItem('{{id_box}}', '{{pdCode}}', {{qty}},'{{ box }}')">
              <i class="fa fa-trash"></i>
            </button>
     <?php endif; ?>
          </td>
        </tr>
        {{else}}
         <tr><td colspan="3" class="text-center">ไม่มีข้อมูล</td></tr>
         {{/if}}
      {{/each}}
    </table>
  </div>
</div>
</script>

<div class="modal fade" id="box-list-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width:500px;">
		<div class="modal-content">
  			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			 </div>
			 <div class="modal-body" id="box-list-body">

       </div>
			 <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
			 </div>
		</div>
	</div>
</div>


<script id="box-list-template" type="text-handlebarsTemplate">
<div class="row">
  <div class="col-sm-12">
    <table class="table" style="margin-bottom:0px;">

      <tbody>
    {{#each this}}
      {{#if nodata}}
      <tr>
        <td colspan="3" class="text-center">ไม่พบรายการ</td>
      </tr>
      {{else}}
      <tr>
        <td class="middle text-center">{{ box_no }}</td>
        <td class="middle text-center">{{ barcode }}</td>
        <td class="middle text-right">
          <button type="button" class="btn btn-sm btn-info" onclick="printConsignBox({{ id_box }})">
            <i class="fa fa-print"></i> พิมพ์
          </button>
        </td>
      </tr>
      {{/if}}
    {{/each}}
      </tbody>
    </table>
  </div>
</div>
</script>

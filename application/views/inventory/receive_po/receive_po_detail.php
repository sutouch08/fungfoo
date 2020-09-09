<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    	<h3 class="title" ><?php echo $this->title; ?></h3>
	</div>
    <div class="col-sm-6">
    <p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> <?php label('back'); ?></button>
			<?php if($this->pm->can_delete && $doc->status == 1) : ?>
				<button type="button" class="btn btn-sm btn-danger" onclick="unSave()"><i class="fa fa-refresh"></i> <?php label('unsave'); ?></button>
			<?php endif; ?>
      <button type="button" class="btn btn-sm btn-info" onclick="printReceived()"><i class="fa fa-print"></i> <?php label('print'); ?></button>
    </p>
  </div>
</div>
<hr />

<div class="row">
  <div class="col-sm-1 col-1-harf padding-5 first">
  	<label><?php label('doc_num'); ?></label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
  </div>
	<div class="col-sm-1 padding-5">
    <label><?php label('date'); ?></label>
    <input type="text" class="form-control input-sm text-center" name="date_add" id="dateAdd" value="<?php echo thai_date($doc->date_add); ?>" disabled />
  </div>
  <div class="col-sm-1 col-1-harf padding-5">
    <label><?php label('vender_code'); ?></label>
    <input type="text" class="form-control input-sm" value="<?php echo $doc->vender_code; ?>" disabled />
  </div>
  <div class="col-sm-5 padding-5">
  	<label><?php label('vender_name'); ?></label>
    <input type="text" class="form-control input-sm" value="<?php echo $doc->vender_name; ?>" disabled />
  </div>

	<div class="col-sm-1 col-1-harf padding-5">
    <label><?php label('po'); ?></label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->po_code; ?>" disabled />
  </div>

  <div class="col-sm-1 col-1-harf padding-5 last">
  	<label><?php label('inv'); ?></label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->invoice_code; ?>" disabled/>
  </div>
  <div class="col-sm-2 padding-5 first">
    <label><?php label('zone_code'); ?></label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->zone_code; ?>" disabled />
  </div>
  <div class="col-sm-4 padding-5">
  	<label><?php label('zone_name'); ?></label>
    <input type="text" class="form-control input-sm" value="<?php echo $doc->zone_name; ?>" disabled/>
  </div>
  <div class="col-sm-6 padding-5 last">
		<label><?php label('remark'); ?></label>
		<input type="text" class="form-control input-sm" value="<?php echo $doc->remark; ?>" disabled />
	</div>
  <input type="hidden" name="code" id="code" value="<?php echo $doc->code; ?>" />
</div>

<?php
if($doc->status == 2)
{
  $this->load->view('cancle_watermark');
}
?>
<hr class="margin-top-15 margin-bottom-15"/>
<div class="row">
	<div class="col-sm-12">
    <table class="table table-striped border-1">
      <thead>
      	<tr class="font-size-12">
        	<th class="width-5 text-center"><?php label('Num'); ?>	</th>
          <th class="width-15 text-center"><?php label('barcode'); ?></th>
          <th class="width-20 text-center"><?php label('item_code'); ?></th>
          <th class=""><?php label('item_name'); ?></th>
					<th class="width-10 text-right"><?php label('price'); ?></th>
          <th class="width-10 text-right"><?php label('qty'); ?></th>
					<th class="width-15 text-right"><?php label('amount'); ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($details)) : ?>
          <?php $no =  1; ?>
          <?php $total_qty = 0; ?>
					<?php $total_amount = 0; ?>
          <?php foreach($details as $rs) : ?>
            <tr class="font-size-12">
              <td class="middle text-center"><?php echo $no; ?></td>
              <td class="middle"><?php echo $rs->barcode; ?></td>
              <td class="middle"><?php echo $rs->product_code; ?></td>
              <td class="middle"><?php echo $rs->product_name; ?></td>
							<td class="middle text-right"><?php echo number($rs->price, 2); ?></td>
              <td class="middle text-right"><?php echo number($rs->qty); ?></td>
							<td class="middle text-right"><?php echo number($rs->amount, 2); ?></td>
            </tr>
            <?php $no++; ?>
            <?php $total_qty += $rs->qty; ?>
						<?php $total_amount += $rs->amount; ?>
          <?php endforeach; ?>
          <tr>
            <td colspan="5" class="text-right"><strong><?php label('total'); ?></strong></td>
            <td class="text-right"><strong><?php echo number($total_qty); ?></strong></td>
						<td class="text-right"><strong><?php echo number($total_amount, 2); ?></strong></td>
          </tr>
        <?php endif; ?>
			  </tbody>
      </table>
    </div>
</div>

<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po.js"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po_add.js"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po_edit.js"></script>

<?php $this->load->view('include/footer'); ?>

<?php $this->load->view('include/header'); ?>

<div class="row">
	<div class="col-sm-6">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6">
    	<p class="pull-right top-p">
       <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> <?php label('back'); ?></button>
			 <?php if($po->status == 3 && ($this->pm->can_add OR $this->pm->can_edit)) : ?>
				 <button type="button" class="btn btn-sm btn-primary" onclick="unClosePO()"><i class="fa fa-unlock"></i> <?php label('un_close_po'); ?></button>
			 <?php endif; ?>
       <button type="button" class="btn btn-sm btn-info" onclick="printPO()"><i class="fa fa-print"></i> <?php label('print'); ?></button>
      </p>
    </div>
</div><!-- End Row -->
<hr class=""/>
<div class="row">
  <div class="col-sm-1 col-1-harf col-xs-6 padding-5 first">
    <label><?php label('doc_num'); ?></label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $po->code; ?>" disabled>
  </div>
  <div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label><?php label('date'); ?></label>
    <input type="text" class="form-control input-sm text-center edit" name="date_add" id="date_add" value="<?php echo date('d-m-Y'); ?>" disabled>
  </div>
  <div class="col-sm-2 col-xs-6 padding-5">
    <label><?php label('vender_code'); ?></label>
    <input type="text" class="form-control input-sm text-center edit" name="vender_code" id="vender_code" value="<?php echo $po->vender_code; ?>" disabled>
  </div>
  <div class="col-sm-5 col-5-harf col-xs-6 padding-5">
    <label><?php label('vender_name'); ?></label>
    <input type="text" class="form-control input-sm edit" name="vender_name" id="vender_name" value="<?php echo $po->vender_name; ?>" disabled>
  </div>
  <div class="col-sm-1 col-1-harf col-xs-6 padding-5 last">
    <label><?php label('require_date'); ?></label>
    <input type="text" class="form-control input-sm text-center edit" name="require_date" id="require_date" value="<?php echo thai_date($po->require_date); ?>" disabled>
  </div>
  <div class="col-sm-12 col-xs-12 padding-5 first last">
    <label><?php label('remark'); ?></label>
    <input type="text" class="form-control input-sm edit" name="remark" id="remark" value="<?php echo $po->remark; ?>" disabled>
  </div>

</div>

<input type="hidden" name="code" id="code" value="<?php echo $po->code; ?>">

<hr class="margin-top-15">

<div class="row">
  <div class="col-sm-12">
    <table class="table table-striped table-bordered border-1">
      <thead>
        <tr>
          <th class="width-5 text-center"><?php label('num'); ?></th>
          <th class="width-20"><?php label('item_code'); ?></th>
          <th class=""><?php label('item_name'); ?></th>
          <th class="width-10 text-center"><?php label('price'); ?></th>
          <th class="width-10 text-center"><?php label('qty'); ?></th>
          <th class="width-20 text-center"><?php label('amount'); ?></th>
					<th class="width-10 text-center"><?php label('received'); ?></th>
        </tr>
      </thead>
      <tbody>
      <?php if(!empty($details)) : ?>
        <?php $no = 1; ?>
        <?php $total_qty = 0; ?>
        <?php $total_amount = 0; ?>
				<?php $total_received = 0; ?>
        <?php foreach($details as $rs) : ?>
        <tr>
          <td class="middle text-center"><?php echo $no; ?></td>
          <td class="middle"><?php echo $rs->product_code; ?></td>
          <td class="middle"><?php echo $rs->product_name; ?></td>
          <td class="middle text-right"><?php echo number($rs->price, 2); ?></td>
          <td class="middle text-right"><?php echo number($rs->qty, 2); ?></td>
          <td class="middle text-right" ><?php echo number($rs->total_amount, 2); ?></td>
					<td class="middle text-right"><?php echo number($rs->received); ?></td>
        </tr>
          <?php $no++; ?>
          <?php $total_qty += $rs->qty; ?>
          <?php $total_amount += $rs->total_amount; ?>
					<?php $total_received += $rs->received; ?>
        <?php endforeach; ?>
        <tr class="bold">
          <td colspan="4" class="text-right"><?php label('total'); ?></td>
          <td class="text-right"><?php echo number($total_qty); ?></td>
          <td class="text-right"><?php echo number($total_amount, 2); ?></td>
					<td class="middle text-right"><?php echo number($total_received); ?></td>
        </tr>
      <?php else : ?>
        <tr>
          <td colspan="7" class="text-center">---- <?php label('no_content'); ?> ----</td>
        </tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="<?php echo base_url(); ?>scripts/purchase/po.js"></script>
<script src="<?php echo base_url(); ?>scripts/purchase/po_add.js"></script>
<?php $this->load->view('include/footer'); ?>

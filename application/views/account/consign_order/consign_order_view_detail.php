<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-4 col-xs-4">
    <h4 class="title">
      <?php echo $this->title; ?>
    </h4>
    </div>
    <div class="col-sm-8 col-xs-8">
    	<p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		<?php if($doc->status == 1 && $this->pm->can_delete) : ?>
				<button type="button" class="btn btn-sm btn-danger" onclick="unSaveConsign()">ยกเลิกการบันทึก</button>
		<?php endif; ?>
				<button type="button" class="btn btn-sm btn-info hidden-xs" onclick="printConsignOrder()"><i class="fa fa-print"></i> พิมพ์</button>
      </p>
    </div>
</div><!-- End Row -->
<hr class=""/>
<?php if($doc->status == 2) : ?>
<?php 	$this->load->view('cancle_watermark'); ?>
<?php endif; ?>
<form id="addForm" method="post" action="<?php echo $this->home; ?>/update">
<div class="row">
  <div class="col-sm-1 col-1-harf col-xs-6 padding-5 first">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm" value="<?php echo $doc->code; ?>" disabled />
  </div>

  <div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center edit" name="date_add" id="date" value="<?php echo thai_date($doc->date_add); ?>" readonly disabled />
  </div>

  <div class="col-sm-4 col-4-harf col-xs-12 padding-5">
    <label>ลูกค้า[ในระบบ]</label>
    <input type="text" class="form-control input-sm" name="customer" id="customer" value="<?php echo $doc->customer_name; ?>" disabled />
  </div>

	<div class="col-sm-4 col-4-harf col-xs-12 padding-5 last">
    <label>โซน[ฝากขาย]</label>
		<input type="text" class="form-control input-sm" name="zone" id="zone" value="<?php echo $doc->zone_name; ?>" disabled />
  </div>


  <div class="col-sm-1 col-1-harf col-xs-6 padding-5 first">
    <label>อ้างอิง</label>
    <input type="text" class="form-control input-sm text-center" name="ref_code" id="ref_code" value="<?php echo $doc->ref_code; ?>" disabled>
  </div>
	<div class="col-sm-10 col-10-harf col-xs-12 padding-5 last">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm" name="remark" id="remark" value="<?php echo $doc->remark; ?>" disabled>
  </div>

</div>
<hr class="margin-top-15">
<input type="hidden" name="consign_code" id="consign_code" value="<?php echo $doc->code; ?>">
<input type="hidden" name="customer_code" id="customer_code" value="<?php echo $doc->customer_code; ?>">
<input type="hidden" name="zone_code" id="zone_code" value="<?php echo $doc->zone_code; ?>" >
</form>

<?php $this->load->view('account/consign_order/consign_order_detail'); ?>


<script src="<?php echo base_url(); ?>scripts/account/consign_order/consign_order.js"></script>
<script src="<?php echo base_url(); ?>scripts/account/consign_order/consign_order_add.js"></script>
<script src="<?php echo base_url(); ?>scripts/account/consign_order/consign_order_control.js"></script>

<?php $this->load->view('include/footer'); ?>

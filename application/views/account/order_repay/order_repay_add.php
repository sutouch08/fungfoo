<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 col-xs-6">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6 col-xs-6">
    	<p class="pull-right top-p">
        <button type="button" class="btn btn-xs btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
      </p>
    </div>
</div><!-- End Row -->
<hr class=""/>
<form id="addForm" method="post" action="<?php echo $this->home; ?>/add">
<div class="row">
  <div class="col-sm-1 col-1-harf col-xs-6 padding-5 first">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $new_code; ?>" disabled />
  </div>

  <div class="col-sm-1 col-xs-6 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center" name="date_add" id="date" value="<?php echo date('d-m-Y'); ?>" readonly required />
  </div>

	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label>ชำระโดย</label>
    <select class="form-control input-sm" name="pay_type" id="pay_type" required>
			<option value="">โปรดเลือก</option>
			<?php echo select_payment_type(); ?>
		</select>
  </div>

  <div class="col-sm-3 col-xs-12 padding-5">
    <label>ลูกค้า[ในระบบ]</label>
    <input type="text" class="form-control input-sm" name="customer" id="customer" value="" required />
  </div>

  <div class="col-sm-4 col-xs-12 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm" name="remark" id="remark" value="">
  </div>
  <div class="col-sm-1 col-xs-12 padding-5 last">
    <label class="display-block not-show">Submit</label>
    <button type="button" class="btn btn-xs btn-success btn-block" onclick="add()"><i class="fa fa-plus"></i> เพิ่ม</button>
  </div>
</div>
<hr class="margin-top-15">
<input type="hidden" name="customerCode" id="customerCode" value="" />
</form>

<script src="<?php echo base_url(); ?>scripts/account/order_repay/order_repay.js"></script>
<script src="<?php echo base_url(); ?>scripts/account/order_repay/order_repay_add.js"></script>


<?php $this->load->view('include/footer'); ?>

<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6">
    	<p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
      </p>
    </div>
</div><!-- End Row -->
<hr class=""/>
<form id="addForm" method="post" action="<?php echo $this->home; ?>/add">
<div class="row">
  <div class="col-sm-2 hidden-xs padding-5 first">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" value="" disabled />
  </div>

  <div class="col-sm-1 col-1-harf col-xs-12 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center" name="date_add" id="date_add" value="<?php echo date('d-m-Y'); ?>" required readonly />
  </div>

  <div class="col-sm-3 col-xs-12 padding-5">
    <label>ลูกค้า[ในระบบ]</label>
    <input type="text" class="form-control input-sm" name="customer" id="customer" value="" required />
  </div>

	<div class="col-sm-3 col-xs-12 padding-5">
    <label>ผู้ติดต่อ</label>
		<input type="text" class="form-control input-sm" name="contact" value="" />
  </div>

  <div class="col-sm-1 col-1-harf col-xs-12 padding-5">
    <label>เงื่อนไข</label>
		<select class="form-control input-sm" name="is_term" id="is_term">
			<option value="0">เงินสด</option>
      <option value="1">เครดิต</option>
    </select>
  </div>

	<div class="col-sm-1 col-xs-12 padding-5 last">
    <label>เครดิต(วัน)</label>
		<input type="number" class="form-control input-sm text-center" name="credit_term" id="credit_term" value="0" readonly/>
  </div>

  <div class="col-sm-10 col-xs-12 padding-5 first">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm" name="remark" id="remark" value="">
  </div>

  <div class="col-sm-2 padding-5 col-xs-12 last">
    <label class="display-block not-show">Submit</label>
    <button type="submit" class="btn btn-xs btn-success btn-block"><i class="fa fa-plus"></i> เพิ่ม</button>
  </div>
</div>
<hr class="margin-top-15">
<input type="hidden" name="customerCode" id="customerCode" value="" />
</form>

<script src="<?php echo base_url(); ?>scripts/quotation/quotation.js"></script>
<script src="<?php echo base_url(); ?>scripts/quotation/quotation_add.js"></script>

<?php $this->load->view('include/footer'); ?>

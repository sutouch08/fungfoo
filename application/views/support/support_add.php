<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    <h3 class="title">
      <i class="fa fa-users"></i> <?php echo $this->title; ?>
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
  <div class="col-sm-1 col-1-harf padding-5 first">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm" value="" disabled />
  </div>

  <div class="col-sm-1 col-1-harf padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center" name="date" id="date" value="<?php echo date('d-m-Y'); ?>" required />
  </div>

  <div class="col-sm-4 padding-5">
    <label>ผู้เบิก[พนักงาน]</label>
    <input type="text" class="form-control input-sm" name="customer" id="customer" value="" required />
  </div>

	<div class="col-sm-2 padding-5">
    <label>งบประมาณคงเหลือ</label>
    <input type="text" class="form-control input-sm text-center" name="budgetAmount" id="budgetAmount" value="" disabled />
  </div>

	<div class="col-sm-3 padding-5 last">
    <label>ผู้รับ[ลูกค้า]</label>
    <input type="text" class="form-control input-sm" name="empName" id="empName" value="" required />
  </div>

  <div class="col-sm-11 padding-5 first">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm" name="remark" id="remark" value="">
  </div>
  <div class="col-sm-1 padding-5 last">
    <label class="display-block not-show">Submit</label>
    <button type="button" class="btn btn-xs btn-success btn-block" onclick="add()"><i class="fa fa-plus"></i> เพิ่ม</button>
  </div>
</div>
<hr class="margin-top-15">
<input type="hidden" name="customerCode" id="customerCode" value="" />
</form>

<script src="<?php echo base_url(); ?>scripts/support/support.js"></script>
<script src="<?php echo base_url(); ?>scripts/support/support_add.js"></script>

<?php $this->load->view('include/footer'); ?>

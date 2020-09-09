<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    	<h3 class="title" >
        <?php echo $this->title; ?>
      </h3>
	</div>
    <div class="col-sm-6">
      <p class="pull-right top-p">
				<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
      </p>
    </div>
</div>
<hr />

<form id="addForm" action="<?php echo $this->home.'/add'; ?>" method="post">
<div class="row">
    <div class="col-sm-1 col-1-harf padding-5 first">
    	<label>เลขที่เอกสาร</label>
        <input type="text" class="form-control input-sm text-center" value="" disabled />
    </div>
		<div class="col-sm-1 col-1-harf padding-5">
    	<label>วันที่</label>
      <input type="text" class="form-control input-sm text-center" name="date_add" id="dateAdd" value="<?php echo date('d-m-Y'); ?>" readonly />
    </div>
		<div class="col-sm-4 padding-5">
			<label>ลูกค้า</label>
			<input type="text" class="form-control input-sm edit" name="customer" id="customer" value=""  required/>
		</div>
		<div class="col-sm-5 padding-5 last">
			<label>โซน[ฝากขาย]</label>
			<input type="text" class="form-control input-sm edit" name="zone" id="zone" value="" placeholder="กำหนดโซนที่จะกระทบยอด" required />
		</div>
		<div class="col-sm-10 col-10-harf padding-5 first">
    	<label>หมายเหตุ</label>
        <input type="text" class="form-control input-sm" name="remark" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" />
    </div>
		<div class="col-sm-1 col-1-harf padding-5 last">
			<label class="display-block not-show">add</label>
			<button type="button" class="btn btn-xs btn-success btn-block" onclick="add()"><i class="fa fa-plus"></i> เพิ่ม</button>
		</div>
</div>
<input type="hidden" name="zone_code" id="zone_code">
<input type="hidden" name="customer_code" id="customer_code">
</form>

<script src="<?php echo base_url(); ?>scripts/inventory/consign_check/consign_check.js"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/consign_check/consign_check_add.js"></script>
<?php $this->load->view('include/footer'); ?>

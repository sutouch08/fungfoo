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

<div class="row">
    <div class="col-sm-1 col-1-harf padding-5 first">
    	<label>เลขที่เอกสาร</label>
        <input type="text" class="form-control input-sm text-center" value="<?php echo $code; ?>" disabled />
    </div>
		<div class="col-sm-1 padding-5">
    	<label>วันที่</label>
      <input type="text" class="form-control input-sm text-center" id="date_add" value="<?php echo date('d-m-Y'); ?>" readonly />
    </div>
		<div class="col-sm-2 padding-5">
			<label>อ้างถึง</label>
			<input type="text" class="form-control input-sm" id="reference" value="" />
		</div>
		<div class="col-sm-6 padding-5">
    	<label>หมายเหตุ</label>
        <input type="text" class="form-control input-sm" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" />
    </div>
		<div class="col-sm-1 col-1-harf padding-5 last">
			<label class="display-block not-show">add</label>
			<button type="button" class="btn btn-xs btn-success btn-block" onclick="add()"><i class="fa fa-plus"></i> เพิ่ม</button>
		</div>
</div>
<script src="<?php echo base_url(); ?>scripts/inventory/adjust/adjust.js"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/adjust/adjust_add.js"></script>
<?php $this->load->view('include/footer'); ?>

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
        <input type="text" class="form-control input-sm text-center" disabled />
    </div>
		<div class="col-sm-1 col-1-harf padding-5">
    	<label>วันที่</label>
      <input type="text" class="form-control input-sm text-center" name="date_add" id="dateAdd" value="<?php echo date('d-m-Y'); ?>" readonly />
    </div>
		<div class="col-sm-2 padding-5">
			<label>เลขที่บิล[SAP]</label>
			<input type="text" class="form-control input-sm text-center" name="invoice" id="invoice" value="" />
		</div>
		<div class="col-sm-4 padding-5">
			<label>ลูกค้า</label>
			<input type="text" class="form-control input-sm text-center" id="customer" value="" />
		</div>
		<div class="col-sm-3 padding-5 last">
			<label>โซน[รับคืน]</label>
			<input type="text" class="form-control input-sm" name="zone" id="zone" value="" />
		</div>

		<!-- <div class="col-sm-3 padding-5 last">
			<label>คลัง[รับคืน]</label>
			<input type="text" class="form-control input-sm" name="warehouse" id="warehouse" value="" />
		</div> -->



    <div class="col-sm-11 padding-5 first">
    	<label>หมายเหตุ</label>
        <input type="text" class="form-control input-sm" name="remark" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" />
    </div>
		<div class="col-sm-1 padding-5 last">
			<label class="display-block not-show">save</label>
			<?php 	if($this->pm->can_add) : ?>
			<button type="button" class="btn btn-xs btn-success btn-block" onclick="addNew()"><i class="fa fa-plus"></i> เพิ่ม</button>
			<?php	endif; ?>
		</div>
</div>
<input type="hidden" name="customer_code" id="customer_code" value="" />
<input type="hidden" name="warehouse_code" id="warehouse_code" value="" />
<input type="hidden" name="zone_code" id="zone_code" value="" />
</form>
<hr class="margin-top-15"/>

<script src="<?php echo base_url(); ?>scripts/inventory/return_order/return_order.js"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_order/return_order_add.js"></script>
<?php $this->load->view('include/footer'); ?>

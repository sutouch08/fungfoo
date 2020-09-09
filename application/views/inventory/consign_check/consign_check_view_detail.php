<?php $this->load->view('include/header'); ?>
<style>
	#detail-table > tr:first-child {
		color:blue;
	}
</style>
<div class="row">
	<div class="col-sm-6">
    	<h3 class="title" >
        <?php echo $this->title; ?>
      </h3>
	</div>
    <div class="col-sm-6">
      <p class="pull-right top-p">
				<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
			<?php if(($this->pm->can_add OR $this->pm->can_edit) && $doc->status == 0 && $doc->valid == 0) : ?>
	      <button type="button" class="btn btn-sm btn-primary" onclick="reloadStock()">
	        <i class="fa fa-refresh"></i> โหลดยอดตั้งต้นใหม่
	      </button>
	       <!--- consign_check_detail.js --->
	      <button type="button" class="btn btn-sm btn-success" onclick="closeCheck()">
	        <i class="fa fa-bolt"></i> บันทึกการตรวจนับ
	      </button>
			<?php endif; ?>
			<?php if($this->pm->can_edit && $doc->status != 2 && $doc->valid == 0) : ?>
				<!--- consign_check_detail.js --->
	      <button type="button" class="btn btn-sm btn-danger" onclick="openCheck()">
	        <i class="fa fa-bolt"></i> ยกเลิกการบันทึก
	      </button>
	    <?php endif; ?>
			<?php if($this->pm->can_delete && $doc->status == 0 && $doc->valid == 0) : ?>
				<!--- consign_check_detail.js --->
        <button type="button" class="btn btn-sm btn-danger" onclick="clearDetails()">
          <i class="fa fa-trash"></i> ยกเลิกการตรวจนับ
        </button>
			<?php endif; ?>
      </p>
    </div>
</div>
<hr />

<form id="addForm" action="<?php echo $this->home.'/add'; ?>" method="post">
<div class="row">
    <div class="col-sm-1 col-1-harf padding-5 first">
    	<label>เลขที่เอกสาร</label>
        <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
    </div>
		<div class="col-sm-1 col-1-harf padding-5">
    	<label>วันที่</label>
      <input type="text" class="form-control input-sm text-center" name="date_add" id="dateAdd" value="<?php echo thai_date($doc->date_add); ?>" readonly  disabled>
    </div>
		<div class="col-sm-4 padding-5">
			<label>ลูกค้า</label>
			<input type="text" class="form-control input-sm edit" name="customer" id="customer" value="<?php echo $doc->customer_name; ?>"  required disabled/>
		</div>
		<div class="col-sm-5 padding-5 last">
			<label>โซน[ฝากขาย]</label>
			<input type="text" class="form-control input-sm edit" name="zone" id="zone" value="<?php echo $doc->zone_name; ?>" disabled required />
		</div>
		<div class="col-sm-12 padding-5 first last">
    	<label>หมายเหตุ</label>
        <input type="text" class="form-control input-sm" name="remark" id="remark" value="<?php echo $doc->remark; ?>" disabled>
    </div>
</div>
<input type="hidden" name="zone_code" id="zone_code" value="<?php echo $doc->zone_code; ?>">
<input type="hidden" name="customer_code" id="customer_code" value="<?php echo $doc->customer_code; ?>">
<input type="hidden" name="check_code" id="check_code" value="<?php echo $doc->code; ?>">
<input type="hidden" name="id_box" id="id_box">
</form>
<hr class="margin-top-15"/>
<?php if($doc->status != 2) : ?>
<?php 	$this->load->view('inventory/consign_check/consign_check_edit_detail'); ?>
<?php else : ?>
	<?php $this->load->view('cancle_watermark'); ?>
<?php endif; ?>

<script src="<?php echo base_url(); ?>scripts/inventory/consign_check/consign_check.js"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/consign_check/consign_check_add.js"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/consign_check/consign_check_control.js"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/consign_check/consign_check_detail.js"></script>
<?php $this->load->view('include/footer'); ?>

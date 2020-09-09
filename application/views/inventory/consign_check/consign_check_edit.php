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
	    <?php else : ?>
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
		<div class="col-sm-10 col-10-harf padding-5 first">
    	<label>หมายเหตุ</label>
        <input type="text" class="form-control input-sm" name="remark" id="remark" value="<?php echo $doc->remark; ?>" disabled>
    </div>
		<div class="col-sm-1 col-1-harf padding-5 last">
			<label class="display-block not-show">add</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="getEdit()"><i class="fa fa-pencil"></i> แก้ไข</button>
			<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="update()"><i class="fa fa-save"></i> บันทึก</button>
		</div>
</div>
<input type="hidden" name="zone_code" id="zone_code" value="<?php echo $doc->zone_code; ?>">
<input type="hidden" name="customer_code" id="customer_code" value="<?php echo $doc->customer_code; ?>">
<input type="hidden" name="check_code" id="check_code" value="<?php echo $doc->code; ?>">
<input type="hidden" name="id_box" id="id_box">
</form>
<hr class="margin-top-15"/>

<div class="row">
	<div class="col-sm-2 padding-5 first">
		<label>บาร์โค้ดกล่อง</label>
		<input type="text" class="form-control input-sm text-center box" id="box-code" placeholder="ยิงบาร์โค้ดกล่อง" autofocus>
	</div>
	<div class="col-sm-1 padding-5">
		<label>จำนวน</label>
		<input type="number" class="form-control input-sm text-center item" id="qty-box" value="1" disabled>
	</div>
	<div class="col-sm-2 padding-5">
		<label>บาร์โค้ดสินค้า</label>
		<input type="text" class="form-control input-sm text-center item" id="barcode" placeholder="ยิงบาร์โค้ดสินค้า" disabled>
	</div>
	<div class="col-sm-1 col-1-harf padding-5">
		<label class="display-block not-show">changebox</label>
		<button type="button" class="btn btn-xs btn-info btn-block item" id="btn-change-box" onclick="changeBox()" disabled><i class="fa fa-refresh"></i> เปลี่ยนกล่อง</button>
	</div>
	<div class="col-sm-3 col-3-harf">
		<h4 class="pull-right" style="margin-top:15px;" id="box-label">จำนวนในกล่อง</h4>
	</div>
	<div class="col-sm-2 padding-5 last">
		<div class="title middle text-center" style="height:55px; background-color:black; color:white; padding:10px; margin-top:0px;">
			<h4 class="inline text-center" id="box-qty">0</h4>
		</div>
	</div>
</div>
<hr>
<?php $this->load->view('inventory/consign_check/consign_check_edit_detail'); ?>


<script src="<?php echo base_url(); ?>scripts/inventory/consign_check/consign_check.js"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/consign_check/consign_check_add.js"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/consign_check/consign_check_control.js"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/consign_check/consign_check_detail.js"></script>
<?php $this->load->view('include/footer'); ?>

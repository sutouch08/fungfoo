<div class="row">
	<div class="col-sm-1 col-1-harf padding-5 first">
    	<label>เลขที่เอกสาร</label>
        <input type="text" class="form-control input-sm text-center" value="<?php echo $order->code; ?>" disabled />
    </div>
    <div class="col-sm-1 padding-5">
    	<label>วันที่</label>
			<input type="text" class="form-control input-sm text-center edit" name="date" id="date" value="<?php echo thai_date($order->date_add); ?>" disabled />
    </div>
    <div class="col-sm-4 col-4-harf padding-5">
    	<label>ลูกค้า[ในระบบ]</label>
			<input type="text" class="form-control input-sm edit" id="customer" name="customer" value="<?php echo $order->customer_name; ?>" required disabled />
    </div>
		<div class="col-sm-4 padding-5">
	    <label>โซน[ฝากขาย]</label>
			<input type="text" class="form-control input-sm edit" name="zone" id="zone" value="<?php echo $order->zone_name; ?>" required disabled/>
	  </div>

	  <div class="col-sm-1 padding-5 last">
	    <label>GP[%]</label>
			<input type="text" class="form-control input-sm text-center edit" name="gp" id="gp" value="<?php echo $order->gp; ?>" disabled />
	  </div>

		<div class="col-sm-10 padding-5 first">
		 	<label>หมายเหตุ</label>
		  <input type="text" class="form-control input-sm edit" name="remark" id="remark" value="<?php echo $order->remark; ?>" disabled />
		</div>
		<?php if($this->pm->can_add OR $this->pm->can_edit): ?>
		<div class="col-sm-2 padding-5 last">
			<label class="display-block not-show">แก้ไข</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="getEdit()"><i class="fa fa-pencil"></i> แก้ไข</i></button>
			<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="validUpdate()"><i class="fa fa-save"></i> บันทึก</i></button>
		</div>
		<?php endif; ?>

    <input type="hidden" name="order_code" id="order_code" value="<?php echo $order->code; ?>" />
    <input type="hidden" name="customerCode" id="customerCode" value="<?php echo $order->customer_code; ?>" />
		<input type="hidden" name="zone_code" id="zone_code" value="<?php echo $order->zone_code; ?>" />
</div>
<hr class="margin-bottom-15"/>

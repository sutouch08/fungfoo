<div class="row">
	<div class="col-sm-1 col-1-harf col-xs-6 padding-5 first">
    	<label>เลขที่เอกสาร</label>
        <input type="text" class="form-control input-sm text-center" value="<?php echo $order->code; ?>" disabled />
    </div>
    <div class="col-sm-1 col-xs-6 padding-5">
    	<label>วันที่</label>
			<input type="text" class="form-control input-sm text-center edit" name="date" id="date" value="<?php echo thai_date($order->date_add); ?>" disabled readonly />
    </div>
    <div class="col-sm-4 col-4-harf col-xs-12 padding-5">
    	<label>ลูกค้า[ในระบบ]</label>
			<input type="text" class="form-control input-sm edit" id="customer" name="customer" value="<?php echo $order->customer_name; ?>" required disabled />
    </div>
    <div class="col-sm-2 col-xs-12 padding-5">
    	<label>ลูกค้า[ออนไลน์]</label>
      <input type="text" class="form-control input-sm edit" id="customer_ref" name="customer_ref" value="<?php echo $order->customer_ref; ?>" disabled />
    </div>
    <div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    	<label>ช่องทางขาย</label>
			<select class="form-control input-sm edit" name="channels" id="channels" required disabled>
				<option value="">เลือกรายการ</option>
				<?php echo select_channels($order->channels_code); ?>
			</select>

    </div>
    <div class="col-sm-1 col-1-harf col-xs-6 padding-5 last">
    	<label>การชำระเงิน</label>
			<select class="form-control input-sm edit" name="payment" id="payment" required disabled>
				<option value="">เลือกรายการ</option>
				<?php echo select_payment_method($order->payment_code); ?>
			</select>
    </div>
		<div class="col-sm-1 col-1-harf col-xs-6 padding-5 first">
			<label>อ้างอิง</label>
		  <input type="text" class="form-control input-sm text-center edit" name="reference" id="reference" value="<?php echo $order->reference; ?>" disabled />
		</div>
		<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
			<label>เลขที่จัดส่ง</label>
		  <input type="text" class="form-control input-sm text-center edit" name="shipping_code" id="shipping_code" value="<?php echo $order->shipping_code; ?>" disabled />
		</div>
		<div class="col-sm-2 col-xs-12 padding-5">
			<label>การจัดส่ง</label>
	    <select class="form-control input-sm edit" name="sender_id" id="sender_id" disabled>
	      <option value="">กรุณาเลือก</option>
	      <?php echo select_sender_list($order->sender_id); ?>
	    </select>
	  </div>
		<?php if($order->state < 4) : ?>
		<div class="col-sm-6 col-xs-12 padding-5">
		 	<label>หมายเหตุ</label>
		  <input type="text" class="form-control input-sm edit" name="remark" id="remark" value="<?php echo $order->remark; ?>" disabled />
		</div>
		<?php else : ?>
			<div class="col-sm-7 col-xs-12 padding-5 last">
			 	<label>หมายเหตุ</label>
			  <input type="text" class="form-control input-sm edit" name="remark" id="remark" value="<?php echo $order->remark; ?>" disabled />
			</div>
		<?php endif; ?>
		<?php if(($this->pm->can_add OR $this->pm->can_edit) && $order->state < 4): ?>
		<div class="col-sm-1 col-xs-12 padding-5 last">
			<label class="display-block not-show">แก้ไข</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="getEdit()"><i class="fa fa-pencil"></i> แก้ไข</i></button>
			<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="validUpdate()"><i class="fa fa-save"></i> บันทึก</i></button>
		</div>
		<?php endif; ?>
    <input type="hidden" name="order_code" id="order_code" value="<?php echo $order->code; ?>" />
    <input type="hidden" name="customerCode" id="customerCode" value="<?php echo $order->customer_code; ?>" />
</div>
<hr class="margin-bottom-15"/>

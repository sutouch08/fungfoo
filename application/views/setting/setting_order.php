<?php
//---- อนุญาติให้แก้ไขราคาในออเดอร์หรือไม่
$btn_price_yes = $ALLOW_EDIT_PRICE == 1 ? 'btn-success' : '';
$btn_price_no = $ALLOW_EDIT_PRICE == 0 ? 'btn-danger' : '';

//--- อนุญาติให้แก้ไขส่วนลดในออเดอร์หรือไม่
$btn_disc_yes = $ALLOW_EDIT_DISCOUNT == 1 ? 'btn-success' : '';
$btn_disc_no  = $ALLOW_EDIT_DISCOUNT == 0 ? 'btn-danger' : '';

//--- ไม่อนุญาติให้ขายสินค้ากับลูกค้าที่มียอดค้างชำระเกินกำหนด
$btn_strict_yes = $STRICT_OVER_DUE == 1 ? 'btn-success' : '';
$btn_strict_no  = $STRICT_OVER_DUE == 0 ? 'btn-danger' : '';

$btn_credit_yes = $CONTROL_CREDIT == 1 ? 'btn-success' : '';
$btn_credit_no  = $CONTROL_CREDIT == 0 ? 'btn-danger' : '';
?>
<div class="tab-pane fade" id="order">
<form id="orderForm" method="post" action="<?php echo $this->home; ?>/update_config">
	<div class="row">
		<div class="col-sm-3"><span class="form-control left-label">อายุของออเดอร์ ( วัน )</span></div>
    <div class="col-sm-9">
      <input type="text" class="form-control input-sm input-small text-center" name="ORDER_EXPIRATION" required value="<?php echo $ORDER_EXPIRATION; ?>" />
      <span class="help-block">กำหนดวันหมดอายุของออเดอร์ หากออเดอร์อยู่ในสถานะ รอการชำระเงิน, รอจัดสินค้า หรือ ไม่บันทึก เกินกว่าจำนวนวันที่กำหนด</span>
    </div>
    <div class="divider-hidden"></div>

		<div class="col-sm-3"><span class="form-control left-label">การจำกัดการแสดงผลสต็อก</span></div>
		<div class="col-sm-9">
			<input type="text" class="form-control input-sm input-small text-center" name="STOCK_FILTER" required value="<?php echo $STOCK_FILTER; ?>" />
			<span class="help-block">กำหนดจำนวนสินค้าคงเหลือสูงสุดที่จะแสดงใหเห็น ถ้าไม่ต้องการใช้กำหนดเป็น 0 </span>
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-3"><span class="form-control left-label">รหัสลูกค้าเริ่มต้น</span></div>
		<div class="col-sm-9">
			<input type="text" class="form-control input-sm input-small text-center" name="DEFAULT_CUSTOMER" required value="<?php echo $DEFAULT_CUSTOMER; ?>" />
			<span class="help-block">ลูกค้าเริ่มต้นหากไม่มีการกำหนดรหัสลูกค้า</span>
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-3"><span class="form-control left-label">ควบคุมเครดิต</span></div>
		<div class="col-sm-9">
			<div class="btn-group">
				<button type="button" class="btn btn-sm <?php echo $btn_credit_yes; ?>" style="width:50%;" id="btn-credit-yes" onClick="toggleControlCredit(1)">คุม</button>
				<button type="button" class="btn btn-sm <?php echo $btn_credit_no; ?>" style="width:50%;" id="btn-credit-no" onClick="toggleControlCredit(0)">ไม่คุม</button>
			</div>
			<input type="hidden" name="CONTROL_CREDIT" id="control-credit" value="<?php echo $CONTROL_CREDIT; ?>" />
			<span class="help-block">ใช้การควบคุมเครดิตหรือไม่ หากควบคุมจะไม่สามารถเปิดออเดอร์ได้ถ้าเครดิตคงเหลือไม่เพียงพอ</span>
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-3"><span class="form-control left-label">วันเพิ่มในการคุมเครดิต</span></div>
		<div class="col-sm-9">
			<input type="number" class="form-control input-sm input-small text-center" name="OVER_DUE_DATE" required value="<?php echo $OVER_DUE_DATE; ?>" />
			<span class="help-block">จำนวนวันเพิ่มจากวันครบกำหนดชำระ เช่น เครดติ 30 วัน เพิ่มอีก 30 วัน</span>
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-3"><span class="form-control left-label">มียอดค้างชำระเกินกำหนด</span></div>
    <div class="col-sm-9">
			<div class="btn-group">
				<button type="button" class="btn btn-sm <?php echo $btn_strict_yes; ?>" style="width:50%;" id="btn-strict-yes" onClick="toggleStrictDue(1)">ไม่ขาย</button>
				<button type="button" class="btn btn-sm <?php echo $btn_strict_no; ?>" style="width:50%;" id="btn-strict-no" onClick="toggleStrictDue(0)">ขาย</button>
			</div>
      <span class="help-block">ไม่อนุญาติให้ขายสินค้าให้ลูกค้าที่มียอดค้างชำระเกินวันที่กำหนดในการคุมเครดิต</span>
			<input type="hidden" name="STRICT_OVER_DUE" id="strict-over-due" value="<?php echo $STRICT_OVER_DUE; ?>" />
    </div>
    <div class="divider-hidden"></div>

		<div class="col-sm-3"><span class="form-control left-label">การแก้ไขส่วนลดในออเดอร์</span></div>
		<div class="col-sm-9">
			<div class="btn-group input-small">
				<button type="button" class="btn btn-sm <?php echo $btn_disc_yes; ?>" style="width:50%;" id="btn-disc-yes" onClick="toggleEditDiscount(1)">เปิด</button>
				<button type="button" class="btn btn-sm <?php echo $btn_disc_no; ?>" style="width:50%;" id="btn-disc-no" onClick="toggleEditDiscount(0)">ปิด</button>
			</div>
			<span class="help-block">กรณีปิดจะไม่สามารแก้ไขส่วนลดในออเดอร์ได้ ส่วนลดจะถูกคำนวณโดยระบบเท่านั้น</span>
			<input type="hidden" name="ALLOW_EDIT_DISCOUNT" id="allow-edit-discount" value="<?php echo $ALLOW_EDIT_DISCOUNT; ?>" />
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-3"><span class="form-control left-label">การแก้ไขราคาในออเดอร์</span></div>
		<div class="col-sm-9">
			<div class="btn-group input-small">
				<button type="button" class="btn btn-sm <?php echo $btn_price_yes; ?>" style="width:50%;" id="btn-price-yes" onClick="toggleEditPrice(1)">เปิด</button>
				<button type="button" class="btn btn-sm <?php echo $btn_price_no; ?>" style="width:50%;" id="btn-price-no" onClick="toggleEditPrice(0)">ปิด</button>
			</div>
			<span class="help-block">กรณีปิดจะไม่สามารแก้ไขราคาขายสินค้าในออเดอร์ได้ จะใช้ราคาขายในระบบเท่านั้น</span>
			<input type="hidden" name="ALLOW_EDIT_PRICE" id="allow-edit-price" value="<?php echo $ALLOW_EDIT_PRICE; ?>" />
		</div>
		<div class="divider-hidden"></div>
<!--
		<div class="col-sm-3"><span class="form-control left-label">รหัสนำหน้าเลขที่จัดส่ง</span></div>
    <div class="col-sm-9">
      <input type="text" class="form-control input-sm input-small text-center" name="PREFIX_SHIPPING_NUMBER" value="<?php echo $PREFIX_SHIPPING_NUMBER; ?>" />
      <span class="help-block">รหัสนำหน้าเลขที่จัดส่ง โดยใช้เลขที่ออเดอร์ของ Warrix12 แล้วเติมรหัสนี้นำหน้าและบันทึกเป็นเลขที่จัดส่งทันที ใช้ในการ import ออเดอร์จากเว็บไซต์</span>
    </div>

		<div class="col-sm-3"><span class="form-control left-label">น้ำหนักเหมารวม(กรัม)</span></div>
    <div class="col-sm-9">
      <input type="text" class="form-control input-sm input-small text-center" name="DHL_DEFAULT_WEIGHT" value="<?php echo $DHL_DEFAULT_WEIGHT; ?>" />
      <span class="help-block">น้ำหนักเหมารวมในการจัดส่ง 1 แพ็คเกจ(กรัม)</span>
    </div>
-->

    <div class="col-sm-9 col-sm-offset-3">
			<button type="button" class="btn btn-sm btn-success" onClick="updateConfig('orderForm')"><i class="fa fa-save"></i> บันทึก</button>
		</div>
		<div class="divider-hidden"></div>
  </div>
</form>
</div><!--- Tab-pane --->

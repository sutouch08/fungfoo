<?php
$btn_qc_yes = $USE_QC == 1 ? 'btn-success' : '';
$btn_qc_no = $USE_QC == 0 ? 'btn-danger' : '';
$auz_no = $ALLOW_UNDER_ZERO == 0 ? 'btn-success' : '';
$auz_yes = $ALLOW_UNDER_ZERO == 1 ? 'btn-danger' : '';
?>
<div class="tab-pane fade" id="inventory">
	<form id="inventoryForm" method="post" action="<?php echo $this->home; ?>/update_config">
  	<div class="row">
    	<div class="col-sm-3">
        <span class="form-control left-label">รับสินค้าเกินไปสั่งซื้อ(%)</span>
      </div>
      <div class="col-sm-9">
        <input type="text" class="form-control input-sm input-small" name="RECEIVE_OVER_PO"  value="<?php echo $RECEIVE_OVER_PO; ?>" />
      </div>
      <div class="divider-hidden"></div>
<!--
      <div class="col-sm-3">
        <span class="form-control left-label">รหัสคลังสินค้าระหว่างทำ</span>
      </div>
      <div class="col-sm-9">
        <input type="text" class="form-control input-sm input-small" name="TRANSFORM_WAREHOUSE" value="<?php echo $TRANSFORM_WAREHOUSE; ?>" />
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-3">
        <span class="form-control left-label">รหัสคลังยืมสินค้า</span>
      </div>
      <div class="col-sm-9">
        <input type="text" class="form-control input-sm input-small" name="LEND_WAREHOUSE" value="<?php echo $LEND_WAREHOUSE; ?>" />
      </div>
    -->
      <div class="divider-hidden"></div>
			<div class="col-sm-3"><span class="form-control left-label">ใช้งานระบบ QC</span></div>
			<div class="col-sm-9">
				<div class="btn-group input-medium">
					<button type="button" class="btn btn-sm <?php echo $btn_qc_yes; ?>" style="width:50%;" id="btn-qc-yes" onClick="toggleQC(1)">ใช้</button>
					<button type="button" class="btn btn-sm <?php echo $btn_qc_no; ?>" style="width:50%;" id="btn-qc-no" onClick="toggleQC(0)">ไม่ใช้</button>
				</div>
				<span class="help-block">เปิด/ปิด ระบบ QC</span>
				<input type="hidden" name="USE_QC" id="use_qc" value="<?php echo $USE_QC; ?>" />
			</div>
			<div class="divider-hidden"></div>

      <div class="col-sm-3">
        <span class="form-control left-label">สต็อกติดลบได้</span>
      </div>
      <div class="col-sm-9">
				<div class="btn-group input-medium">
        	<button type="button" class="btn btn-sm <?php echo $auz_no; ?>" style="width:50%;" id="btn-auz-no" onClick="toggleAuz(0)">ไม่ได้</button>
          <button type="button" class="btn btn-sm <?php echo $auz_yes; ?>" style="width:50%;" id="btn-auz-yes" onClick="toggleAuz(1)">ได้</button>
        </div>
        <span class="help-block">อนุญาติให้สต็อกติดลบได้</span>
        <input type="hidden" name="ALLOW_UNDER_ZERO" id="allow-under-zero" value="<?php echo $ALLOW_UNDER_ZERO; ?>" />
      </div>
      <div class="divider-hidden"></div>


      <div class="col-sm-9 col-sm-offset-3">
        <button type="button" class="btn btn-sm btn-success input-small" onClick="updateConfig('inventoryForm')">
          <i class="fa fa-save"></i> บันทึก
        </button>
      </div>
      <div class="divider-hidden"></div>

  	</div><!--/ row -->
  </form>
</div>

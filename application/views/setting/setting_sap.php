
<div class="tab-pane fade" id="SAP">
	<form id="sapForm" method="post" action="<?php echo $this->home; ?>/update_config">
  	<div class="row">
    	<div class="col-sm-4">
        <span class="form-control left-label">Currency (สกุลเงิน)</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-small" name="CURRENCY"  value="<?php echo $CURRENCY; ?>" />
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-4">
        <span class="form-control left-label">Item group code (รหัสกลุ่มสินค้า)</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-small" name="ITEM_GROUP_CODE" value="<?php echo $ITEM_GROUP_CODE; ?>" />
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-4">
        <span class="form-control left-label">Purchase VAT code (รหัสภาษีซื้อ)</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-small" name="PURCHASE_VAT_CODE" value="<?php echo $PURCHASE_VAT_CODE; ?>" />
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-4">
        <span class="form-control left-label">Purchase VAT rate (อัตราภาษีซื้อ)</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-small" name="PURCHASE_VAT_RATE" value="<?php echo $PURCHASE_VAT_RATE; ?>" />
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">Sell VAT code (รหัสภาษีขาย)</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-small" name="SALE_VAT_CODE" value="<?php echo $SALE_VAT_CODE; ?>" />
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-4">
        <span class="form-control left-label">Sell VAT rate (อัตราภาษีขาย)</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-small" name="SALE_VAT_RATE" value="<?php echo $SALE_VAT_RATE; ?>" />
      </div>
      <div class="divider-hidden"></div>



      <div class="col-sm-8 col-sm-offset-4">
        <button type="button" class="btn btn-sm btn-success input-small" onClick="updateConfig('sapForm')">
          <i class="fa fa-save"></i> บันทึก
        </button>
      </div>
      <div class="divider-hidden"></div>

  	</div><!--/ row -->
  </form>
</div>

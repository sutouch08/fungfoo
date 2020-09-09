<div class="tab-pane fade" id="bookcode">
	<form id="bookcodeForm" method="post" action="<?php echo $this->home; ?>/update_config">
    <div class="row">
    	<div class="col-sm-3"><span class="form-control left-label">ขายสินค้า</span></div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-small bookcode text-center" name="BOOK_CODE_ORDER" value="<?php echo $BOOK_CODE_ORDER; ?>" />
        <span class="help-block">กำหนดรหัสเล่มเอกสาร "ใบสั่งขาย"</span>
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-3"><span class="form-control left-label">เบิกอภินันท์</span></div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-small bookcode text-center" name="BOOK_CODE_SUPPORT" value="<?php echo $BOOK_CODE_SUPPORT; ?>" />
        <span class="help-block">กำหนดรหัสเล่มเอกสาร "ใบสั่งขาย"</span>
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-3"><span class="form-control left-label">เบิกสปอนเซอร์</span></div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-small bookcode text-center" name="BOOK_CODE_SPONSOR" value="<?php echo $BOOK_CODE_SPONSOR; ?>" />
        <span class="help-block">กำหนดรหัสเล่มเอกสาร "ใบสั่งขาย"</span>
      </div>
      <div class="divider-hidden"></div>


      <div class="col-sm-3"><span class="form-control left-label">รับสินค้าจากการซื้อ</span></div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-small bookcode text-center" name="BOOK_CODE_BI" value="<?php echo $BOOK_CODE_RECEIVE_PO; ?>" />
        <span class="help-block">กำหนดรหัสเล่มเอกสาร "ใบรับสินค้า"</span>
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-3"><span class="form-control left-label">โอนสินค้าระหว่างคลัง</span></div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-small bookcode text-center" name="BOOK_CODE_TRANSFER" value="<?php echo $BOOK_CODE_TRANSFER; ?>" />
        <span class="help-block">กำหนดรหัสเล่มเอกสาร "ใบโอนสินค้าคลัง"</span>
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-3"><span class="form-control left-label">ยืมสินค้า</span></div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-small bookcode text-center" name="BOOK_CODE_LEND" value="<?php echo $BOOK_CODE_LEND; ?>" />
        <span class="help-block">กำหนดรหัสเล่มเอกสาร "ใบโอนสินค้า"</span>
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-3"><span class="form-control left-label">ฝากขาย(ใบกำกับ)</span></div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-small bookcode text-center" name="BOOK_CODE_CONSIGN_TR" value="<?php echo $BOOK_CODE_CONSIGN_TR; ?>" />
        <span class="help-block">กำหนดรหัสเล่มเอกสาร "ใบโอนสินค้า"</span>
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-3"><span class="form-control left-label">ฝากขาย(โอนคลัง)</span></div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-small bookcode text-center" name="BOOK_CODE_CONSIGN_SO" value="<?php echo $BOOK_CODE_CONSIGN_SO; ?>" />
        <span class="help-block">กำหนดรหัสเล่มเอกสาร "ใบโอนสินค้า"</span>
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-3"><span class="form-control left-label">ตัดยอดฝากขาย</span></div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-small bookcode text-center" name="BOOK_CODE_CONSIGN_SOLD" value="<?php echo $BOOK_CODE_CONSIGN_SOLD; ?>" />
        <span class="help-block">กำหนดรหัสเล่มเอกสาร "ใบสั่งขาย"</span>
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-3"><span class="form-control left-label">เบิกแปรสภาพ(เพื่อขาย)</span></div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-small bookcode text-center" name="BOOK_CODE_TRANSFORM" value="<?php echo $BOOK_CODE_TRANSFORM; ?>" />
        <span class="help-block">กำหนดรหัสเล่มเอกสาร "ใบเบิกแปรสภาพ"</span>
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-3"><span class="form-control left-label">เบิกแปรสภาพ(เพื่อสต็อก)</span></div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-small bookcode text-center" name="BOOK_CODE_TRANSFORM_STOCK" value="<?php echo $BOOK_CODE_TRANSFORM_STOCK; ?>" />
        <span class="help-block">กำหนดรหัสเล่มเอกสาร "ใบเบิกแปรสภาพ"</span>
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-3"><span class="form-control left-label">รับสินค้าจากการแปรสภาพ</span></div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-small bookcode text-center" name="BOOK_CODE_RECEIVE_TRANSFORM" value="<?php echo $BOOK_CODE_RECEIVE_TRANSFORM; ?>" />
        <span class="help-block">กำหนดรหัสเล่มเอกสาร "ใบรับสินค้าจากการแปรสภาพ"</span>
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-3"><span class="form-control left-label">คืนสินค้า(ลดหนี้ขาย)</span></div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-small bookcode text-center" name="BOOK_CODE_RETURN_ORDER" value="<?php echo $BOOK_CODE_RETURN_ORDER; ?>" />
        <span class="help-block">กำหนดรหัสเล่มเอกสาร "ใบลดหนี้"</span>
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-3"><span class="form-control left-label">คืนสินค้า(จากการยืม)</span></div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-small bookcode text-center" name="BOOK_CODE_RETURN_LEND" value="<?php echo $BOOK_CODE_RETURN_LEND; ?>" />
        <span class="help-block">กำหนดรหัสเล่มเอกสาร "ใบลดหนี้"</span>
      </div>
      <div class="divider-hidden"></div>


      <div class="col-sm-9 col-sm-offset-3">
      	<button type="button" class="btn btn-sm btn-success input-small" onClick="updateConfig('bookcodeForm')"><i class="fa fa-save"></i> บันทึก</button>
      </div>
      <div class="divider-hidden"></div>

    </div><!--/ row -->
  </form>
</div>

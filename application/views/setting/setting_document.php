<div class="tab-pane fade" id="document">
	<form id="documentForm" method="post" action="<?php echo $this->home; ?>/update_config">
    <div class="row">
    	<div class="col-sm-3">
				<span class="form-control left-label">ขายสินค้า</span>
			</div>
      <div class="col-sm-2">
				<input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_ORDER" required value="<?php echo $PREFIX_ORDER; ?>" /></div>
      <div class="col-sm-1 col-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center digit" required name="RUN_DIGIT_ORDER" value="<?php echo $RUN_DIGIT_ORDER; ?>" /></div>
      <div class="divider-hidden"></div>
			<!--
      <div class="col-sm-3"><span class="form-control left-label">ฝากขาย[โอนคลัง]</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_CONSIG_TR" required value="<?php echo $PREFIX_CONSIGN_TR; ?>" /></div>
      <div class="col-sm-1 col-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center digit" name="RUN_DIGIT_CONSIGN_TR" required value="<?php echo $RUN_DIGIT_CONSIGN_TR; ?>" /></div>
      <div class="divider-hidden"></div>
		-->
      <div class="col-sm-3"><span class="form-control left-label">ฝากขาย[ใบกำกับ]</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_CONSIGN_SO" required value="<?php echo $PREFIX_CONSIGN_SO; ?>" /></div>
      <div class="col-sm-1 col-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center digit" name="RUN_DIGIT_CONSIGN_SO" required value="<?php echo $RUN_DIGIT_CONSIGN_SO; ?>" /></div>
      <div class="divider-hidden"></div>

      <div class="col-sm-3"><span class="form-control left-label">ตัดยอดฝากขาย</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_CONSIGN_SOLD" required value="<?php echo $PREFIX_CONSIGN_SOLD; ?>" /></div>
      <div class="col-sm-1 col-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center digit" name="RUN_DIGIT_CONSIGN_SOLD" required value="<?php echo $RUN_DIGIT_CONSIGN_SOLD; ?>" /></div>
      <div class="divider-hidden"></div>

      <div class="col-sm-3"><span class="form-control left-label">รับสินคาเข้าจากการซื้อ</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_RECEIVE_PO" required value="<?php echo $PREFIX_RECEIVE_PO; ?>" /></div>
      <div class="col-sm-1 col-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center digit" name="RUN_DIGIT_RECEIVE_PO" required value="<?php echo $RUN_DIGIT_RECEIVE_PO; ?>" /></div>
      <div class="divider-hidden"></div>

<!--
      <div class="col-sm-3"><span class="form-control left-label">รับสินค้าเข้าจากการแปรสภาพ</span></div>
      <div class="col-sm-2">
      	<input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_RECEIVE_TRANSFORM" required value="<?php echo $PREFIX_RECEIVE_TRANSFORM; ?>" />
      </div>
      <div class="col-sm-1 col-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
      <div class="col-sm-2">
      	<input type="text" class="form-control input-sm input-small text-center digit" name="RUN_DIGIT_RECEIVE_TRANSFORM" required value="<?php echo $RUN_DIGIT_RECEIVE_TRANSFORM; ?>" />
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-3"><span class="form-control left-label">เบิกแปรสภาพ(เพื่อขาย)</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center" name="PREFIX_TRANSFORM" required value="<?php echo $PREFIX_TRANSFORM; ?>" /></div>
      <div class="col-sm-1 col-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center" name="RUN_DIGIT_TRANSFORM" required value="<?php echo $RUN_DIGIT_TRANSFORM; ?>" /></div>
      <div class="divider-hidden"></div>

			<div class="col-sm-3"><span class="form-control left-label">เบิกแปรสภาพ(เพื่อสต็อก)</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center" name="PREFIX_TRANSFORM_STOCK" required value="<?php echo $PREFIX_TRANSFORM_STOCK; ?>" /></div>
      <div class="col-sm-1 col-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center" name="RUN_DIGIT_TRANSFORM_STOCK" required value="<?php echo $RUN_DIGIT_TRANSFORM_STOCK; ?>" /></div>
      <div class="divider-hidden"></div>

      <div class="col-sm-3"><span class="form-control left-label">ยืมสินค้า</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_LEND" required value="<?php echo $PREFIX_LEND; ?>" /></div>
      <div class="col-sm-1 col-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center digit" name="RUN_DIGIT_LEND" required value="<?php echo $RUN_DIGIT_LEND; ?>" /></div>
      <div class="divider-hidden"></div>

      <div class="col-sm-3"><span class="form-control left-label">เบิกสปอนเซอร์</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_SPONSOR" required value="<?php echo $PREFIX_SPONSOR; ?>" /></div>
      <div class="col-sm-1 col-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center digit" name="RUN_DIGIT_SPONSOR" required value="<?php echo $RUN_DIGIT_SPONSOR; ?>" /></div>
      <div class="divider-hidden"></div>

      <div class="col-sm-3"><span class="form-control left-label">เบิกอภินันท์</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_SUPPORT" required value="<?php echo $PREFIX_SUPPORT; ?>" /></div>
      <div class="col-sm-1 col-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center digit" name="RUN_DIGIT_SUPPORT" required value="<?php echo $RUN_DIGIT_SUPPORT; ?>" /></div>
      <div class="divider-hidden"></div>
-->
      <div class="col-sm-3"><span class="form-control left-label">คืนสินค้าจากการขาย</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_RETURN_ORDER" required value="<?php echo $PREFIX_RETURN_ORDER; ?>" /></div>
      <div class="col-sm-1 col-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center digit" name="RUN_DIGIT_RETURN_ORDER" required value="<?php echo $RUN_DIGIT_RETURN_ORDER; ?>" /></div>
      <div class="divider-hidden"></div>
<!--
      <div class="col-sm-3"><span class="form-control left-label">คืนสินค้าจากการยืม</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_RETURN_LEND" required value="<?php echo $PREFIX_RETURN_LEND; ?>" /></div>
      <div class="col-sm-1 col-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center digit" name="RUN_DIGIT_RETURN_LEND" required value="<?php echo $RUN_DIGIT_RETURN_LEND; ?>" /></div>
      <div class="divider-hidden"></div>
-->
      <div class="col-sm-3"><span class="form-control left-label">กระทบยอด</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_CONSIGN_CHECK" required value="<?php echo $PREFIX_CONSIGN_CHECK; ?>" /></div>
      <div class="col-sm-1 col-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center digit" name="RUN_DIGIT_CONSIGN_CHECK" required value="<?php echo $RUN_DIGIT_CONSIGN_CHECK; ?>" /></div>
      <div class="divider-hidden"></div>

      <div class="col-sm-3"><span class="form-control left-label">โอนสินค้าระหว่างคลัง</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_TRANSFER" required value="<?php echo $PREFIX_TRANSFER; ?>" /></div>
      <div class="col-sm-1 col-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center digit" name="RUN_DIGIT_TRANSFER" required value="<?php echo $RUN_DIGIT_TRANSFER; ?>" /></div>
      <div class="divider-hidden"></div>

      <div class="col-sm-3"><span class="form-control left-label">ย้ายพื้นที่จัดเก็บ</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_MOVE" required value="<?php echo $PREFIX_MOVE; ?>" /></div>
      <div class="col-sm-1 col-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center digit" name="RUN_DIGIT_MOVE" required value="<?php echo $RUN_DIGIT_MOVE; ?>" /></div>
      <div class="divider-hidden"></div>


			<div class="col-sm-3"><span class="form-control left-label">ปรับยอดสต็อก</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_ADJUST" required value="<?php echo $PREFIX_ADJUST; ?>" /></div>
      <div class="col-sm-1 col-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center digit" name="RUN_DIGIT_ADJUST" required value="<?php echo $RUN_DIGIT_ADJUST; ?>" /></div>
      <div class="divider-hidden"></div>


      <div class="col-sm-3"><span class="form-control left-label">นโยบายส่วนลด</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_POLICY" required value="<?php echo $PREFIX_POLICY; ?>" /></div>
      <div class="col-sm-1 col-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center digit" name="RUN_DIGIT_POLICY" required value="<?php echo $RUN_DIGIT_POLICY; ?>" /></div>
      <div class="divider-hidden"></div>

      <div class="col-sm-3"><span class="form-control left-label">เงื่อนไขส่วนลด</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_RULE" required value="<?php echo $PREFIX_RULE; ?>" /></div>
      <div class="col-sm-1 col-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center digit" name="RUN_DIGIT_RULE" required value="<?php echo $RUN_DIGIT_RULE; ?>" /></div>
      <div class="divider-hidden"></div>
			<div class="divider-hidden"></div>
			<div class="divider-hidden"></div>

      <div class="col-sm-4 col-sm-offset-3">
      	<button type="button" class="btn btn-sm btn-success input-small text-center" onClick="checkDocumentSetting()"><i class="fa fa-save"></i> บันทึก</button>
      </div>
      <div class="divider-hidden"></div>

    </div><!--/ row -->
  </form>
</div>

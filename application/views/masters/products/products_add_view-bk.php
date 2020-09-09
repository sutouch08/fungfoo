<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-sm-6">
		<p class="pull-right">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="title-block"/>
<form class="form-horizontal" id="addForm" method="post" action="<?php echo $this->home."/add"; ?>">
	<div class="tab-pane fade <?php echo $tab1; ?>" id="tab1">
	<form id="productForm">
		<div class="row">
				<div class="col-sm-3"><span class="form-control label-left">รหัสสินค้า</span></div>
					<div class="col-sm-9">
						<input type="text" class="form-control input-sm input-large inline" name="pCode" id="pCode" value="<?php echo $rs['product_code']; ?>" placeholder="กำหนดรหัสของรุ่นสินค้า" autofocus  />
							<span id="pCode-error" class="label-left red" style="margin-left:15px; display:none;">รหัสสินค้าซ้ำ</span>
					</div>
					<div class="divider-hidden" style="margin-top:5px; margin-bottom:5px;"></div>

					<div class="col-sm-3"><span class="form-control label-left">ชื่อสินค้า</span></div>
					<div class="col-sm-9">
							<input type="text" class="form-control input-sm input-large inline" name="pName" id="pName" value="<?php echo $rs['product_name']; ?>" placeholder="กำหนดชื่อของสินค้า"  />
							<span id="pName-error" class="label-left red" style="margin-left:15px; display:none;">จำเป็นต้องกำหนดช่องนี้</span>
					</div>
					<div class="divider-hidden" style="margin-top:5px; margin-bottom:5px;"></div>


					<div class="col-sm-3"><span class="form-control label-left">กลุ่มสินค้า</span></div>
					<div class="col-sm-9">
							<select class="form-control input-sm input-large" name="pGroup" id="pGroup">
							<?php echo selectProductGroup($rs['id_product_group']); ?>
							</select>
					</div>
					<div class="divider-hidden" style="margin-top:5px; margin-bottom:5px;"></div>


					<div class="col-sm-3"><span class="form-control label-left">หมวดหมู่หลัก</span></div>
					<div class="col-sm-9">
						<select class="form-control input-sm input-large" name="dCategory" id="dCategory">
								<?php echo categoryList($rs['default_category_id']); ?>
							</select>
					</div>
					<div class="divider-hidden" style="margin-top:5px; margin-bottom:5px;"></div>

					<div class="col-sm-3"><span class="form-control label-left">หมวดหมู่ย่อย</span></div>
					<div class="col-sm-9"><?php echo categoryTree($id_pd);  ?></div>
					<div class="divider-hidden" style="margin-top:5px; margin-bottom:5px;"></div>

					<div class="col-sm-3"><span class="form-control label-left">ทุน</span></div>
					<div class="col-sm-9">
					<input type="text" class="form-control input-sm input-mini inline" name="cost" id="cost" value="<?php echo $rs['product_cost']; ?>"  />
					<span id="cost-error" class="label-left red" style="margin-left:15px; display:none;">ตัวเลขไม่ถูกต้อง</span>
					</div>
					<div class="divider-hidden" style="margin-top:5px; margin-bottom:5px;"></div>

					<div class="col-sm-3"><span class="form-control label-left">ราคาขาย</span></div>
					<div class="col-sm-9">
					<input type="text" class="form-control input-sm input-mini inline" name="price" id="price" value="<?php echo $rs['product_price']; ?>" />
					<span id="price-error" class="label-left red" style="margin-left:15px; display:none;">ตัวเลขไม่ถูกต้อง</span>
					</div>
					<div class="divider-hidden" style="margin-top:5px; margin-bottom:5px;"></div>

					<div class="col-sm-3"><span class="form-control label-left">ส่วนลด</span></div>
					<div class="col-sm-9">
					<input type="text" class="form-control input-sm input-mini" style="position:relative; float:left; margin-right:5px;" name="discount" id="discount" value="<?php echo $rs['discount']; ?>" />
					<select class="form-control input-sm input-mini inline" name="discount_type" id="dType">
							<option value="percentage" <?php echo isSelected('percentage', $rs['discount_type']); ?>>เปอร์เซ็นต์</option>
							<option value="amount" <?php echo isSelected('amount', $rs['discount_type']); ?>>จำนวนเงิน</option>
					 </select>
					 <span id="discount-error" class="label-left red" style="margin-left:15px; display:none;">ตัวเลขไม่ถูกต้อง</span>
					</div>

					<div class="divider-hidden" style="margin-top:5px; margin-bottom:5px;"></div>

					<div class="col-sm-3"><span class="form-control label-left">น้ำหนัก</span></div>
					<div class="col-sm-9">
						<input type="text" class="form-control input-sm input-mini inline ops" name="weight" id="weight" value="<?php echo $rs['weight']; ?>" />
							<span class="label-left inline" style="margin-left:15px;">กิโลกรัม</span>
					</div>
					<div class="divider-hidden" style="margin-top:5px; margin-bottom:5px;"></div>

					<div class="col-sm-3"><span class="form-control label-left">ความกว้าง</span></div>
					<div class="col-sm-9">
						<input type="text" class="form-control input-sm input-mini inline ops" name="width" id="width" value="<?php echo $rs['width']; ?>" />
							<span class="label-left inline" style="margin-left:15px;">เซ็นติเมตร</span>
					</div>
					<div class="divider-hidden" style="margin-top:5px; margin-bottom:5px;"></div>

					<div class="col-sm-3"><span class="form-control label-left">ยาว</span></div>
					<div class="col-sm-9">
						<input type="text" class="form-control input-sm input-mini inline ops" name="length" id="length" value="<?php echo $rs['length']; ?>" />
							<span class="label-left inline" style="margin-left:15px;">เซ็นติเมตร</span>
					</div>
					<div class="divider-hidden" style="margin-top:5px; margin-bottom:5px;"></div>

					<div class="col-sm-3"><span class="form-control label-left">สูง</span></div>
					<div class="col-sm-9">
						<input type="text" class="form-control input-sm input-mini inline ops" name="height" id="height" value="<?php echo $rs['height']; ?>" />
							<span class="label-left inline" style="margin-left:15px;">เซ็นติเมตร</span>
					</div>
					<div class="divider-hidden" style="margin-top:5px; margin-bottom:5px;"></div>

					<div class="col-sm-3"><span class="form-control label-left">สินค้าเสมือน</span></div>
					<div class="col-sm-9">
						<div class="btn-group input-small">
								<button type="button" class="btn btn-sm <?php echo $vs; ?>" id="btn-vs" onClick="toggleVisual(1)" style="width:50%;">ใช่</button>
									<button type="button" class="btn btn-sm <?php echo $nvs; ?>" id="btn-nvs" onClick="toggleVisual(0)" style="width:50%;">ไม่ใช่</button>
							</div>
					</div>
					<div class="divider-hidden" style="margin-top:5px; margin-bottom:5px;"></div>

					<div class="col-sm-3"><span class="form-control label-left">เปิดใช้งาน</span></div>
					<div class="col-sm-9">
						<div class="btn-group input-small">
								<button type="button" class="btn btn-sm <?php echo $ac; ?>" id="btn-ac" onClick="toggleActived(1)" style="width:50%;">ใช่</button>
									<button type="button" class="btn btn-sm <?php echo $dac; ?>" id="btn-dac" onClick="toggleActived(0)" style="width:50%;">ไม่ใช่</button>
							</div>

					</div>
					<div class="divider-hidden" style="margin-top:5px; margin-bottom:5px;"></div>

					<div class="col-sm-3"><span class="form-control label-left">แสดงในหน้าลูกค้า</span></div>
					<div class="col-sm-9">
						<div class="btn-group input-small">
								<button type="button" class="btn btn-sm <?php echo $is; ?>" id="btn-is" onClick="toggleInShop(1)" style="width:50%;">ใช่</button>
									<button type="button" class="btn btn-sm <?php echo $nis; ?>" id="btn-nis" onClick="toggleInShop(0)" style="width:50%;">ไม่ใช่</button>
							</div>

					</div>
					<div class="divider-hidden" style="margin-top:5px; margin-bottom:5px;"></div>

					<div class="col-sm-3"><span class="form-control label-left">คำอธิบายสินค้า</span></div>
					<div class="col-sm-9">
						<textarea class="form-control input-xlarge" name="description" rows="4" placeholder="กำหนดคำอธิบายสินค้า ( สำหรับลูกค้า )"><?php echo productDescription($id_pd); ?></textarea>
					</div>
					<input type="hidden" name="id_product" id="id_product" value="<?php echo $id_product; ?>" /><!-- ถ้าเพิ่มใหม่ยังไม่บันทึก id_product = '' --->
					<input type="hidden" name="isVisual" id="isVisual" value="<?php echo $rs['is_visual']; ?>" />
					<input type="hidden" name="active" id="active" value="<?php echo $rs['active']; ?>" />
					<input type="hidden" name="inShop" id="inShop" value="<?php echo $rs['show_in_shop']; ?>" />
					<input type="hidden" id="isDuplicated" value="0" />
					<div class="divider-hidden" style="margin-top:25px; margin-bottom:25px;"></div>

					<div class="col-sm-3"></div>
					<div class="col-sm-9">
					<?php if( $id_pd != 0 ) : ?>
							<button type="button" class="btn btn-success input-xlarge" onClick="saveProduct(<?php echo $id_pd; ?>)" ><i class="fa fa-save"></i> บันทึก</button>
	<?php endif; ?>
					</div>
					<div class="divider-hidden" style="margin-top:5px; margin-bottom:5px;"></div>

	</div>
	</form>
	</div><!--/ tab-pane #tab1 -->
</form>

<script src="<?php echo base_url(); ?>scripts/masters/product_brand.js"></script>
<?php $this->load->view('include/footer'); ?>

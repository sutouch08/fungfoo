<?php $id = $rule->id; ?>

<div class="modal fade" id="productGrid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" id="modal" style="min-width:250px;">
		<div class="modal-content">
  			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="modalTitle">title</h4>
			 </div>
			 <div class="modal-body" id="modalBody"></div>
			 <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
				<button type="button" class="btn btn-primary" onClick="addToList()" >เพิ่มรายการ</button>
			 </div>
		</div>
	</div>
</div>


<div class="modal fade" id="items-list-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:600px;">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">สินค้าที่กำหนด</h4>
      </div>
      <div class="modal-body" id="items-list-body">
        <ul style="list-style-type:none;" id="items-list">
<?php
      $qr  = "SELECT ps.code FROM discount_rule_product AS sr ";
      $qr .= "LEFT JOIN products AS ps ON sr.product_code = ps.code ";
      $qr .= "WHERE sr.id_rule = ".$id;
?>
<?php $qs = $this->db->query($qr); ?>
<?php if($qs->num_rows() > 0) : ?>
<?php   foreach($qs->result() as $rs) : ?>
          <li style="min-height:15px; padding:5px;" id="item-id-<?php echo $rs->code; ?>">
            <a href="#" class="paddint-5" onclick="removeItemId('<?php echo $rs->code; ?>')">
              <i class="fa fa-times red"></i>
            </a>
            <span style="margin-left:10px;"><?php echo $rs->code ?></span>
          </li>
          <input type="hidden" name="itemId[<?php echo $rs->code; ?>]" id="itemId-<?php echo $rs->code; ?>" class="itemId" value="<?php echo $rs->code; ?>" />
<?php endforeach; ?>
<?php endif; ?>
        </ul>
      </div>
      <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="style-list-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:600px;">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">รุ่นสินค้าที่กำหนด</h4>
      </div>
      <div class="modal-body" id="style-list-body">
        <ul style="list-style-type:none;" id="style-list">
<?php
      $qr  = "SELECT ps.code FROM discount_rule_product_style AS sr ";
      $qr .= "LEFT JOIN product_style AS ps ON sr.style_code = ps.code ";
      $qr .= "WHERE sr.id_rule = ".$id;
?>
<?php $qs = $this->db->query($qr); ?>
<?php if($qs->num_rows() > 0) : ?>
<?php   foreach($qs->result() as $rs) : ?>
          <li style="min-height:15px; padding:5px;" id="style-id-<?php echo $rs->code; ?>">
            <a href="#" class="paddint-5" onclick="removeStyleId('<?php echo $rs->code; ?>')">
              <i class="fa fa-times red"></i>
            </a>
            <span style="margin-left:10px;"><?php echo $rs->code ?></span>
          </li>
          <input type="hidden" name="styleId[<?php echo $rs->code; ?>]" id="styleId-<?php echo $rs->code; ?>" class="styleId" value="<?php echo $rs->code; ?>" />
<?php endforeach; ?>
<?php endif; ?>
        </ul>
      </div>
      <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
      </div>
    </div>
  </div>
</div>




<div class="modal fade" id="pd-group-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:400px;">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">เลือกกลุ่มสินค้า</h4>
      </div>
      <div class="modal-body" id="pd-group-body">
        <div class="row">
          <div class="col-sm-12">
    <?php
    $qs = $this->db->query("SELECT * FROM product_group"); ?>
    <?php if($qs->num_rows() > 0) : ?>
      <?php $sd = $this->discount_rule_model->getRuleProductGroup($id); ?>
      <?php foreach($qs->result() as $rs) : ?>
        <?php $se = isset($sd[$rs->code]) ? 'checked' : ''; ?>
              <label class="display-block">
                <input type="checkbox" class="chk-pd-group" name="chk-pd-group-<?php echo $rs->code; ?>" id="chk-pd-group-<?php echo $rs->code; ?>" value="<?php echo $rs->code; ?>" <?php echo $se; ?> />
                <?php echo $rs->name; ?>
              </label>
      <?php endforeach; ?>
    <?php endif;?>
          </div>
        </div>
      </div>
      <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
      </div>
    </div>
  </div>
</div>



<div class="modal fade" id="pd-sub-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:400px;">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">เลือกกลุ่มสินค้า</h4>
      </div>
      <div class="modal-body" id="pd-sub-body">
        <div class="row">
          <div class="col-sm-12">
    <?php
    $qs = $this->db->query("SELECT * FROM product_sub_group"); ?>
    <?php if($qs->num_rows() > 0) : ?>
      <?php $sd = $this->discount_rule_model->getRuleProductSubGroup($id); ?>
      <?php foreach($qs->result() as $rs) : ?>
        <?php $se = isset($sd[$rs->code]) ? 'checked' : ''; ?>
              <label class="display-block">
                <input type="checkbox" class="chk-pd-sub" name="chk-pd-sub-<?php echo $rs->code; ?>" id="chk-pd-sub-<?php echo $rs->code; ?>" value="<?php echo $rs->code; ?>" <?php echo $se; ?> />
                <?php echo $rs->name; ?>
              </label>
      <?php endforeach; ?>
    <?php endif;?>
          </div>
        </div>
      </div>
      <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
      </div>
    </div>
  </div>
</div>



<div class="modal fade" id="pd-cat-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:400px;">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">เลือกหมวดหมู่สินค้า</h4>
      </div>
      <div class="modal-body" id="pd-cat-body">
        <div class="row">
          <div class="col-sm-12">
    <?php
    $qs = $this->db->query("SELECT * FROM product_category"); ?>
    <?php if($qs->num_rows() > 0) : ?>
      <?php $sd = $this->discount_rule_model->getRuleProductCategory($id); ?>
      <?php foreach($qs->result() as $rs) : ?>
        <?php $se = isset($sd[$rs->code]) ? 'checked' : ''; ?>
              <label class="display-block">
                <input type="checkbox" class="chk-pd-cat" name="chk-pd-cat-<?php echo $rs->code; ?>" id="chk-pd-cat-<?php echo $rs->code; ?>" value="<?php echo $rs->code; ?>" <?php echo $se; ?> />
                <?php echo $rs->name; ?>
              </label>
      <?php endforeach; ?>
    <?php endif;?>
          </div>
        </div>
      </div>
      <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
      </div>
    </div>
  </div>
</div>



<div class="modal fade" id="pd-type-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:400px;">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">เลือกชนิดสินค้า</h4>
      </div>
      <div class="modal-body" id="pd-type-body">
        <div class="row">
          <div class="col-sm-12">
    <?php
    $qs = $this->db->query("SELECT * FROM product_type"); ?>
    <?php if($qs->num_rows() > 0) : ?>
      <?php $sd = $this->discount_rule_model->getRuleProductType($id); ?>
      <?php foreach($qs->result() as $rs) : ?>
        <?php $se = isset($sd[$rs->code]) ? 'checked' : ''; ?>
              <label class="display-block">
                <input type="checkbox" class="chk-pd-type" name="chk-pd-type-<?php echo $rs->code; ?>" id="chk-pd-type-<?php echo $rs->code; ?>" value="<?php echo $rs->code; ?>" <?php echo $se; ?> />
                <?php echo $rs->name; ?>
              </label>
      <?php endforeach; ?>
    <?php endif;?>
          </div>
        </div>
      </div>
      <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="pd-kind-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:400px;">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">เลือกประเภทสินค้า</h4>
      </div>
      <div class="modal-body" id="pd-kind-body">
        <div class="row">
          <div class="col-sm-12">
    <?php
    $qs = $this->db->query("SELECT * FROM product_kind"); ?>
    <?php if($qs->num_rows() > 0) : ?>
      <?php $sd = $this->discount_rule_model->getRuleProductKind($id); ?>
      <?php foreach($qs->result() as $rs) : ?>
        <?php $se = isset($sd[$rs->code]) ? 'checked' : ''; ?>
              <label class="display-block">
                <input type="checkbox" class="chk-pd-kind" name="chk-pd-kind-<?php echo $rs->code; ?>" id="chk-pd-kind-<?php echo $rs->code; ?>" value="<?php echo $rs->code; ?>" <?php echo $se; ?> />
                <?php echo $rs->name; ?>
              </label>
      <?php endforeach; ?>
    <?php endif;?>
          </div>
        </div>
      </div>
      <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
      </div>
    </div>
  </div>
</div>



<div class="modal fade" id="pd-brand-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:400px;">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">เลือกยี่ห้อสินค้า</h4>
      </div>
      <div class="modal-body" id="pd-brand-body">
        <div class="row">
          <div class="col-sm-12">
    <?php
    $qs = $this->db->query("SELECT * FROM product_brand"); ?>
    <?php if($qs->num_rows() > 0) : ?>
      <?php $sd = $this->discount_rule_model->getRuleProductBrand($id); ?>
      <?php foreach($qs->result() as $rs) : ?>
        <?php $se = isset($sd[$rs->code]) ? 'checked' : ''; ?>
              <label class="display-block">
                <input type="checkbox" class="chk-pd-brand" name="chk-pd-brand-<?php echo $rs->code; ?>" id="chk-pd-brand-<?php echo $rs->code; ?>" value="<?php echo $rs->code; ?>" <?php echo $se; ?> />
                <?php echo $rs->name; ?>
              </label>
      <?php endforeach; ?>
    <?php endif;?>
          </div>
        </div>
      </div>
      <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
      </div>
    </div>
  </div>
</div>



<div class="modal fade" id="pd-year-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:400px;">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">เลือกปีสินค้า</h4>
      </div>
      <div class="modal-body" id="pd-year-body">
        <div class="row">
          <div class="col-sm-12">
    <?php
    $qs = $this->db->query("SELECT DISTINCT year FROM products"); ?>
    <?php if($qs->num_rows() > 0) : ?>
      <?php $sd = $this->discount_rule_model->getRuleProductYear($id); ?>
      <?php foreach($qs->result() as $rs) : ?>
        <?php $se = isset($sd[$rs->year]) ? 'checked' : ''; ?>
              <label class="display-block">
                <input type="checkbox" class="chk-pd-year" name="chk-pd-year-<?php echo $rs->year; ?>" id="chk-pd-year-<?php echo $rs->year; ?>" value="<?php echo $rs->year; ?>" <?php echo $se; ?> />
                <?php echo (($rs->year == '0000' OR $rs->year == '') ? 'ไม่ระบุ' :$rs->year); ?>
              </label>
      <?php endforeach; ?>
    <?php endif;?>
          </div>
        </div>
      </div>
      <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
      </div>
    </div>
  </div>
</div>

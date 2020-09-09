<?php $id = $rule->id; ?>
<div class="modal fade" id="cust-name-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:600px;">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">รายชื่อลูกค้า</h4>
      </div>
      <div class="modal-body" id="cust-name-body">
        <ul style="list-style-type:none;" id="cust-list">
<?php
      $qr  = "SELECT
                cs.code, cs.name
              FROM
                discount_rule_customer AS cr
              LEFT JOIN
                customers AS cs ON cr.customer_code = cs.code
              WHERE
              cr.id_rule = $id";
?>
<?php $qs = $this->db->query($qr); ?>
<?php if($qs->num_rows() > 0) : ?>
<?php   foreach($qs->result() as $rs) : ?>
          <li style="min-height:15px; padding:5px;" id="cust-id-<?php echo $rs->code; ?>">
            <a href="#" class="paddint-5" onclick="removeCustId('<?php echo $rs->code; ?>')">
              <i class="fa fa-times red"></i>
            </a>
            <span style="margin-left:10px;"><?php echo $rs->code.' : '.$rs->name; ?></span>
          </li>
          <input type="hidden" name="custId[<?php echo $rs->code; ?>]" id="custId-<?php echo $rs->code; ?>" class="custId" value="<?php echo $rs->code; ?>" />
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




<div class="modal fade" id="cust-group-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:400px;">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">เลือกกลุ่มลูกค้า</h4>
      </div>
      <div class="modal-body" id="cust-group-body">
        <div class="row">
          <div class="col-sm-12">
    <?php $qs = $this->db->query("SELECT * FROM customer_group"); ?>
    <?php if($qs->num_rows() > 0) : ?>
      <?php $group_rule = $this->discount_rule_model->getRuleCustomerGroup($id); ?>
      <?php foreach($qs->result() as $rs) : ?>
        <?php $se = isset($group_rule[$rs->code]) ? 'checked' : ''; ?>
              <label class="display-block">
                <input type="checkbox" class="chk-group" name="chk-group-<?php echo $rs->code; ?>" id="chk-group-<?php echo $rs->code; ?>" value="<?php echo $rs->code; ?>" <?php echo $se; ?> />
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


<div class="modal fade" id="cust-type-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:400px;">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">ชนิดลูกค้า</h4>
      </div>
      <div class="modal-body" id="cust-type-body">
        <div class="row">
          <div class="col-sm-12">
    <?php
    $qs = $this->db->query("SELECT * FROM customer_type"); ?>
    <?php if($qs->num_rows() > 0) : ?>
      <?php $group_rule = $this->discount_rule_model->getRuleCustomerType($id); ?>
      <?php foreach($qs->result() as $rs) : ?>
        <?php $se = isset($group_rule[$rs->code]) ? 'checked' : ''; ?>
              <label class="display-block">
                <input type="checkbox" class="chk-type" name="chk-type-<?php echo $rs->code; ?>" id="chk-type-<?php echo $rs->code; ?>" value="<?php echo $rs->code; ?>" <?php echo $se; ?> />
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




<div class="modal fade" id="cust-kind-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:400px;">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">ประเภทลูกค้า</h4>
      </div>
      <div class="modal-body" id="cust-kind-body">
        <div class="row">
          <div class="col-sm-12">
    <?php
    $qs = $this->db->query("SELECT * FROM customer_kind"); ?>
    <?php if($qs->num_rows() > 0) : ?>
      <?php $group_rule = $this->discount_rule_model->getRuleCustomerKind($id); ?>
      <?php foreach($qs->result() as $rs) : ?>
        <?php $se = isset($group_rule[$rs->code]) ? 'checked' : ''; ?>
              <label class="display-block">
                <input type="checkbox" class="chk-kind" name="chk-kind-<?php echo $rs->code; ?>" id="chk-kind-<?php echo $rs->code; ?>" value="<?php echo $rs->code; ?>" <?php echo $se; ?> />
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




<div class="modal fade" id="cust-area-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:400px;">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">เขตลูกค้า</h4>
      </div>
      <div class="modal-body" id="cust-area-body">
        <div class="row">
          <div class="col-sm-12">
    <?php
    $qs = $this->db->query("SELECT * FROM customer_area"); ?>
    <?php if($qs->num_rows() > 0) : ?>
      <?php $group_rule = $this->discount_rule_model->getRuleCustomerArea($id); ?>
      <?php foreach($qs->result() as $rs) : ?>
        <?php $se = isset($group_rule[$rs->code]) ? 'checked' : ''; ?>
              <label class="display-block">
                <input type="checkbox" class="chk-area" name="chk-area-<?php echo $rs->code; ?>" id="chk-area-<?php echo $rs->code; ?>" value="<?php echo $rs->code; ?>" <?php echo $se; ?> />
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




<div class="modal fade" id="cust-class-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:300px;">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">เกรดลูกค้า</h4>
      </div>
      <div class="modal-body" id="cust-class-body">
        <div class="row">
          <div class="col-sm-12">
    <?php
    $qs = $this->db->query("SELECT * FROM customer_class"); ?>
    <?php if($qs->num_rows() > 0) : ?>
      <?php $group_rule = $this->discount_rule_model->getRuleCustomerClass($id); ?>
      <?php foreach($qs->result() as $rs) : ?>
        <?php $se = isset($group_rule[$rs->code]) ? 'checked' : ''; ?>
              <label class="display-block">
                <input type="checkbox" class="chk-class" name="chk-class-<?php echo $rs->code; ?>" id="chk-class-<?php echo $rs->code; ?>" value="<?php echo $rs->code; ?>" <?php echo $se; ?> />
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

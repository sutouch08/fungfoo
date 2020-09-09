<?php $this->load->view('include/header'); ?>
<?php $ac = $rule->active == 1 ? 'btn-success' : ''; ?>
<?php $dc = $rule->active == 0 ? 'btn-danger' : ''; ?>

<div class="row top-row">
  <div class="col-sm-6 top-col">
    <h4 class="title"></i><?php echo $this->title; ?></h4>
  </div>
  <div class="col-sm-6">
    <p class="pull-right top-p">
      <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
    </p>
  </div>
</div>
<hr/>

<div class="row">
  <div class="col-sm-2 padding-5 first">
    <label>เลขที่</label>
    <input type="text" class="form-control input-sm text-center" id="txt-policy" value="<?php echo $rule->code; ?>" disabled />
  </div>
  <div class="col-sm-8 padding-5">
    <label>ชื่อเงื่อนไข</label>
    <input type="text" class="form-control input-sm" maxlength="150" id="txt-rule-name" value="<?php echo $rule->name; ?>" disabled />
  </div>
  <div class="col-sm-1 padding-5">
    <label class="display-block not-show">ใช้งาน</label>
    <div class="btn-group width-100">
      <button type="button" class="btn btn-sm <?php echo $ac; ?> width-50" id="btn-active-rule" onclick="activeRule()" disabled>
        <i class="fa fa-check"></i>
      </button>
      <button type="button" class="btn btn-sm <?php echo $dc; ?> width-50" id="btn-dis-rule" onclick="disActiveRule()" disabled>
        <i class="fa fa-times"></i>
      </button>
    </div>
  </div>
  <?php if($this->pm->can_add) : ?>
  <div class="col-sm-1 padding-5 last">
    <label class="display-block not-show">add</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="getEdit()">แก้ไข</button>
    <button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="updateRule()">บันทึก</button>
  </div>
  <?php endif; ?>
</div>
<input type="hidden" id="id_rule" value="<?php echo $rule->id; ?>" />
<input type="hidden" id="isActive" value="<?php echo $rule->active; ?>" />

<hr/>

<div class="row">
<div class="col-sm-2 padding-right-0" style="padding-top:15px;">
<ul id="myTab1" class="setting-tabs width-100" style="margin-left:0px;">
        <li class="li-block active"><a href="#discount" data-toggle="tab">ส่วนลด</a></li>
        <li class="li-block"><a href="#customer" data-toggle="tab">ลูกค้า</a></li>
        <li class="li-block"><a href="#product" data-toggle="tab">สินค้า</a></li>
        <li class="li-block"><a href="#channels" data-toggle="tab">ช่องทางขาย</a></li>
        <li class="li-block"><a href="#payment" data-toggle="tab">ช่องทางการชำระเงิน</a></li>
</ul>
</div>
<div class="col-sm-10" style="padding-top:15px; border-left:solid 1px #ccc; min-height:600px; max-height:1000px;">
<div class="tab-content">
  <?php
    $this->load->view('discount/rule/discount_rule');
    $this->load->view('discount/rule/customer_rule');
    $this->load->view('discount/rule/product_rule');
    $this->load->view('discount/rule/channels_rule');
    $this->load->view('discount/rule/payment_rule');

   ?>

</div>
</div><!--/ col-sm-9  -->
</div><!--/ row  -->

<script src="<?php echo base_url(); ?>scripts/discount/rule/rule.js"></script>
<script src="<?php echo base_url(); ?>scripts/discount/rule/rule_add.js"></script>
<script src="<?php echo base_url(); ?>scripts/discount/rule/rule_detail.js"></script>
<script src="<?php echo base_url(); ?>scripts/discount/rule/channels_tab.js"></script>
<script src="<?php echo base_url(); ?>scripts/discount/rule/payment_tab.js"></script>
<script src="<?php echo base_url(); ?>scripts/discount/rule/customer_tab.js"></script>
<script src="<?php echo base_url(); ?>scripts/discount/rule/product_tab.js"></script>
<script src="<?php echo base_url(); ?>scripts/discount/rule/discount_tab.js"></script>

<?php $this->load->view('include/footer'); ?>

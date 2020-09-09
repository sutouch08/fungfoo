<?php $this->load->view('include/header'); ?>
<div class="row top-row">
	<div class="col-sm-6">
    <h3 class="title"><?php echo $this->title; ?></h3>
    </div>
    <div class="col-sm-6">
    	<p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
      <?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
        <button type="button" class="btn btn-sm btn-primary" onclick="getActiveRuleList()">เพิ่มเงื่อนไข</button>
      <?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr class="title-block"/>

<?php $this->load->view('discount/policy/policy_edit_header'); ?>
<?php $this->load->view('discount/policy/policy_rule_list_view'); ?>

<div class="modal fade" id="rule-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:800px;">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">เลือกกฎส่วนลด</h4>
      </div>
      <div class="modal-body" id="rule-body">
        <div class="row">
          <div class="scrollbar-inner">
            <div class="col-sm-12" style="width:800px; max-height:400px;" id="result">
            </div>
          </div>

        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-primary" id="btn-add-rule" onclick="addRule()" disabled><i class="fa fa-plus"></i> เพิ่มในนโยบาย</button>
        <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">ปิด</button>
      </div>
    </div>
  </div>
</div>

<script id="rule-template" type="text/x-handlebarsTemplate">
<table class="table table-striped tablesorter margin-bottom-0" id="myTable">
  <thead>
    <tr>
      <th class="width-5">เลือก</th>
      <th class="width-25">รหัส</th>
      <th class="width-50">ชื่อกฏ</th>
      <th class="width-20">แก้ไขล่าสุด</th>
    </tr>
  </thead>
  <tbody>
{{#each this}}
  {{#if nodata}}
    <tr>
      <td colspan="4" class="text-center">ไม่พบรายการ</td>
    </tr>
  {{else}}
    <tr class="font-size-12">
      <td class="text-center">
        <input type="checkbox" class="ace chk-rule" name="ruleId[{{id_rule}}]" id="ruleId_{{id_rule}}" value="{{id_rule}}" onchange="toggleButton()" />
				<span class="lbl"></span>
      </td>
      <td>
        <label for="ruleId_{{id_rule}}" class="padding-5">{{ruleCode}}</label>
      </td>
      <td>
        <label for="ruleId_{{id_rule}}" class="padding-5">{{ruleName}}</label>
      </td>
      <td>
        <label for="ruleId_{{id_rule}}" class="padding-5">{{date_upd}}</label>
      </td>
    </tr>
  {{/if}}
{{/each}}
  </tbody>
</table>
</script>

<script src="<?php echo base_url(); ?>scripts/discount/policy/policy.js"></script>
<script src="<?php echo base_url(); ?>scripts/discount/policy/policy_list.js"></script>
<script src="<?php echo base_url(); ?>scripts/discount/policy/policy_add.js"></script>

<?php $this->load->view('include/footer'); ?>

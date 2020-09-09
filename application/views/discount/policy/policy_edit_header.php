<?php
  $ac = $policy->active == 1 ? 'btn-success' : '';
  $dc = $policy->active == 0 ? 'btn-danger' : '';
  ?>
<form id="editForm" method="post" action="<?php echo $this->home; ?>/update_policy">
<div class="row">
  <div class="col-sm-1 col-1-harf padding-5 first">
    <label>เลขที่นโยบาย</label>
    <input type="text" class="form-control input-sm" value="<?php echo $policy->code; ?>" disabled />
  </div>

  <div class="col-sm-4 col-4-harf padding-5">
    <label>ชื่อนโยบาย</label>
    <input type="text" class="form-control input-sm header-box" name="policy_name" id="policy_name" value="<?php echo $policy->name; ?>" disabled required />
  </div>

  <div class="col-sm-1 col-1-harf padding-5">
    <label>เริ่มต้น</label>
		  <input type="text" class="form-control input-sm text-center header-box" name="start_date" id="fromDate" value="<?php echo thai_date($policy->start_date); ?>" disabled required />
  </div>
  <div class="col-sm-1 col-1-harf padding-5">
    <label>สิ้นสุด</label>
    <input type="text" class="form-control input-sm text-center header-box" name="end_date" id="toDate" value="<?php echo thai_date($policy->end_date); ?>" disabled required />
  </div>
  <?php if($this->pm->can_edit) : ?>
    <div class="col-sm-2 padding-5">
      <label class="display-block not-show">active</label>
      <div class="btn-group width-100">
        <button type="button" class="btn btn-sm <?php echo $ac; ?> width-50 header-box" id="btn-active" onclick="setActive(1)" disabled>Active</button>
        <button type="button" class="btn btn-sm <?php echo $dc; ?> width-50 header-box" id="btn-disactive" onclick="setActive(0)" disabled>Disactive</button>
      </div>
    </div>
	<div class="col-sm-1 padding-5 last">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="getEdit()">แก้ไข</button>
    <button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="update()">บันทึก</button>
  </div>
  <?php endif; ?>
</div>
<input type="hidden" name="active" id="isActive" value="<?php echo $policy->active; ?>" />
<input type="hidden" name="id" id="id_policy" value="<?php echo $policy->id; ?>" />
<input type="hidden" name="policy_code" value="<?php echo $policy->code; ?>" />
<hr class="margin-top-15">
</form>

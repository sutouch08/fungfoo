<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    	<h3 class="title" >
        <?php echo $this->title; ?>
      </h3>
	</div>
    <div class="col-sm-6">
      	<p class="pull-right top-p">
			    <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
        </p>
    </div>
</div>
<hr />

<div class="row">
    <div class="col-sm-1 col-1-harf padding-5 first">
    	<label>เลขที่เอกสาร</label>
        <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
    </div>
		<div class="col-sm-1 padding-5">
    	<label>วันที่</label>
      <input type="text" class="form-control input-sm text-center edit" id="date_add" value="<?php echo thai_date($doc->date_add) ?>" readonly disabled/>
    </div>
		<div class="col-sm-2 padding-5">
			<label>อ้างถึง</label>
			<input type="text" class="form-control input-sm edit" id="reference" value="<?php echo $doc->reference; ?>" disabled />
		</div>
		<div class="col-sm-7 col-7-harf padding-5 last">
    	<label>หมายเหตุ</label>
        <input type="text" class="form-control input-sm" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" value="<?php echo $doc->remark; ?>" disabled/>
    </div>
    <input type="hidden" id="code" value="<?php echo $doc->code; ?>" />
</div>

<?php if($doc->status == 0) : ?>
<hr class="margin-top-15 margin-bottom-15"/>
<div class="row">
  <div class="col-sm-3 padding-5 first">
    <label>โซน</label>
    <input type="text" class="form-control input-sm text-center" id="zone" value="" autofocus />
  </div>
  <div class="col-sm-1 col-1-harf pddint-5">
    <label class="display-block not-show">change</label>
    <button type="button" class="btn btn-xs btn-yellow btn-block hide" id="btn-change-zone" onclick="change_zone()">เปลี่ยนโซน</button>
    <button type="button" class="btn btn-xs btn-info btn-block" id="btn-set-zone" onclick="set_zone()">ตกลง</button>
  </div>
  <div class="col-sm-3 padding-5">
    <label>รหัสสินค้า</label>
    <input type="text" class="form-control input-sm text-center" id="pd-code" value="" disabled />
  </div>
  <div class="col-sm-1 padding-5">
    <label>เพิ่ม</label>
    <input type="number" class="form-control input-sm text-center" id="qty-up" value="" disabled />
  </div>
  <div class="col-sm-1 padding-5">
    <label>ลด</label>
    <input type="number" class="form-control input-sm text-center" id="qty-down" value="" disabled />
  </div>
  <div class="col-sm-1 col-1-harf padding-5">
    <label class="display-block not-show">OK</label>
    <button type="button" class="btn btn-xs btn-primary btn-block" id="btn-add" onclick="add_detail()" disabled>เพิ่มรายการ</button>
  </div>
</div>
<?php endif; ?>
<hr class="margin-top-15 margin-bottom-15"/>
<div class="row">
  <div class="col-sm-12 first last">
    <p class="pull-right top-p">
      <span style="margin-right:30px;"><i class="fa fa-check green"></i> = ปรับยอดแล้ว</span>
      <span><i class="fa fa-times red"></i> = ยังไม่ปรับยอด</span>
    </p>
  </div>
  <div class="col-sm-12">
    <table class="table table-striped border-1">
      <thead>
        <tr>
          <th class="width-5 text-center">ลำดับ</th>
          <th class="width-20">รหัสสินค้า</th>
          <th class="">สินค้า</th>
          <th class="width-20 text-center">โซน</th>
          <th class="width-10 text-center">เพิ่ม</th>
          <th class="width-10 text-center">ลด</th>
          <th class="width-5 text-center">สถานะ</th>
          <th class="width-5 text-right"></th>
        </tr>
      </thead>
      <tbody id="detail-table">
<?php if(!empty($details)) : ?>
<?php   $no = 1;    ?>
<?php   foreach($details as $rs) : ?>
      <tr class="font-size-12 rox" id="row-<?php echo $rs->id; ?>">
        <td class="middle text-center no">
          <?php echo $no; ?>
        </td>
        <td class="middle">
          <?php echo $rs->product_code; ?>
        </td>
        <td class="middle">
          <?php echo $rs->product_name; ?>
        </td>
        <td class="middle text-center">
          <?php echo $rs->zone_name; ?>
        </td>
        <td class="middle text-center" id="qty-up-<?php echo $rs->id; ?>">
          <?php echo $rs->qty > 0 ? $rs->qty : 0 ; ?>
        </td>
        <td class="middle text-center" id="qty-down-<?php echo $rs->id; ?>">
          <?php echo $rs->qty < 0 ? ($rs->qty * -1) : 0 ; ?>
        </td>
        <td class="middle text-center">
          <?php echo is_active($rs->valid); ?>
        </td>
        <td class="middle text-right">
        <?php if(($this->pm->can_add OR $this->pm->can_edit) && $doc->status == 0) : ?>
          <button type="button" class="btn btn-xs btn-danger" onclick="deleteDetail(<?php echo $rs->id; ?>, '<?php echo $rs->product_code; ?>')">
            <i class="fa fa-trash"></i>
          </button>
        <?php endif; ?>
        </td>
      </tr>
<?php     $no++; ?>
<?php   endforeach; ?>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>


<script id="detail-template" type="text/x-handlebars-template">
<tr class="font-size-12 rox" id="row-{{id}}">
  <td class="middle text-center no">{{no}}</td>
  <td class="middle">{{ pdCode }}</td>
  <td class="middle">{{ pdName }}</td>
  <td class="middle text-center">{{ zoneName }}</td>
  <td class="middle text-center" id="qty-up-{{id}}">{{ up }}</td>
  <td class="middle text-center" id="qty-down-{{id}}">{{ down }}</td>
  <td class="middle text-center">
    {{#if valid}}
    <i class="fa fa-times red"></i>
    {{else}}
    <i class="fa fa-check green"></i>
    {{/if}}
  </td>
  <td class="middle text-right">
  <?php if(($this->pm->can_add OR $this->pm->can_edit) && $doc->status == 0) : ?>
    <button type="button" class="btn btn-xs btn-danger" onclick="deleteDetail({{ id }}, '{{ pdCode }}')">
      <i class="fa fa-trash"></i>
    </button>
  <?php endif; ?>
  </td>
</tr>
</script>


<script src="<?php echo base_url(); ?>scripts/inventory/adjust/adjust.js"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/adjust/adjust_add.js"></script>
<?php $this->load->view('include/footer'); ?>

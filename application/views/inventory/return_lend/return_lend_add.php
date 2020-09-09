<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    	<h3 class="title" >
        <?php echo $this->title; ?>
      </h3>
	</div>
    <div class="col-sm-6">
      <p class="pull-right top-p">
				<button type="button" class="btn btn-sm btn-warning" onclick="leave()"><i class="fa fa-arrow-left"></i> กลับ</button>
				<?php if($this->pm->can_add) : ?>
				<button type="button" class="btn btn-sm btn-success" onclick="save()"><i class="fa fa-save"></i> บันทึก</button>
				<?php	endif; ?>
      </p>
    </div>
</div>
<hr />

<form id="addForm" action="<?php echo $this->home.'/add'; ?>" method="post">
<div class="row">
    <div class="col-sm-1 col-1-harf padding-5 first">
    	<label>เลขที่เอกสาร</label>
        <input type="text" class="form-control input-sm text-center" value="<?php echo $new_code; ?>" disabled />
    </div>
		<div class="col-sm-1 col-1-harf padding-5">
    	<label>วันที่</label>
      <input type="text" class="form-control input-sm text-center" name="date_add" id="dateAdd" value="<?php echo date('d-m-Y'); ?>" readonly />
    </div>
		<div class="col-sm-3 padding-5">
			<label>ผู้ยืม</label>
			<input type="text" class="form-control input-sm edit" name="customer" id="customer" value="" placeholder="ชื่อผู้ยืม(พนักงาน)" required/>
		</div>
		<div class="col-sm-6 padding-5 last">
    	<label>หมายเหตุ</label>
        <input type="text" class="form-control input-sm" name="remark" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" />
    </div>

		<div class="divider-hidden"></div>

		<div class="col-sm-1 col-1-harf padding-5 first">
			<label>ใบยืมสินค้า</label>
			<input type="text" class="form-control input-sm text-center" name="lend_code" id="lend_code" value="" placeholder="ระบุเลขที่ใบยืมสินค้า" required>
		</div>
		<div class="col-sm-1 padding-5">
			<label class="display-block not-show">doc</label>
			<button type="button" class="btn btn-xs btn-success btn-block" id="btn-set-code" onclick="load_lend_details()">ดึงข้อมูล</button>
			<button type="button" class="btn btn-xs btn-primary btn-block hide" id="btn-change-code" onclick="change_lend_code()">เปลี่ยน</button>
		</div>
		<div class="col-sm-3 padding-5">
			<label>โซน[รับคืน]</label>
			<input type="text" class="form-control input-sm edit" name="zone" id="zone" value="" placeholder="กำหนดโซนที่จะรับสินค้าเข้า" required />
		</div>
		<div class="col-sm-1 padding-5">
			<label class="display-block not-show">chang</label>
			<button type="button" class="btn btn-xs btn-primary btn-block hide" id="btn-change-zone" onclick="changeZone()">เปลี่ยนโซน</button>
			<button type="button" class="btn btn-xs btn-success btn-block" id="btn-set-zone" onclick="setZone()">ตกลง</button>
		</div>

		<div class="col-sm-1 padding-5">
			<label>จำนวน</label>
			<input type="number" class="form-control input-sm text-center" id="qty" value="1">
		</div>

		<div class="col-sm-3 padding-5">
			<label>บาร์โค้ดสินค้า</label>
			<input type="text" class="form-control input-sm text-center" id="barcode" placeholder="ยิงบาร์โค้ดสินค้า">
		</div>
		<div class="col-sm-1 padding-5">
			<label class="display-block not-show">barcode</label>
			<button type="button" class="btn btn-xs btn-success btn-block" onclick="doReceive()">ตกลง</button>
		</div>

</div>
<hr class="margin-top-15"/>
<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped border-1">
			<thead>
				<tr>
					<th class="width-5 middle text-center">No.</th>
					<th class="width-15 middle">บาร์โค้ด</th>
					<th class="width-40 middle">สินค้า</th>
					<th class="width-10 middle text-right">ยืม</th>
					<th class="width-10 middle text-right">คืนแล้ว</th>
					<th class="width-10 middle text-right">ค้าง</th>
					<th class="width-10 middle text-right">ครั้งนี้</th>
				</tr>
			</thead>
			<tbody id="result">

			</tbody>
		</table>
	</div>
</div>
<input type="hidden" name="customer_code" id="customer_code" value="">
<input type="hidden" name="zone_code" id="zone_code" value="">
<input type="hidden" name="lendCode" id="lendCode" value="">
</form>

<script id="template" type="text/x-handlebarsTemplate">
{{#each details}}
	{{#if nodata}}
		<tr>
			<td colspan="7" class="middle text-center">ไม่พบข้อมูล</td>
		</tr>
	{{else}}
		{{#if @last}}
			<tr class="font-size-14">
				<td colspan="3" class="middle text-right">รวม</td>
				<td class="middle text-right">{{totalLend}}</td>
				<td class="middle text-right">{{totalReceived}}</td>
				<td class="middle text-right">{{totalBacklogs}}</td>
				<td class="middle text-right" id="totalQty">0</td>
			</tr>
		{{else}}
			<tr>
				<td class="middle text-center no">{{no}}</td>
				<td class="middle">
					<span class="barcode" onClick="addToBarcode('{{barcode}}')">{{barcode}}</span>
					<input type="hidden" id="barcode_{{barcode}}" value="{{itemCode}}">
				</td>
				<td class="middle">{{itemCode}}</td>
				<td class="middle text-right">{{lendQty}}</td>
				<td class="middle text-right">{{received}}</td>
				<td class="middle text-right">
					{{backlogs}}
					<input type="hidden" id="backlogs_{{itemCode}}" value="{{backlogs}}">
				</td>
				<td class="middle text-right">
				{{#if backlogs}}
					<input type="number" class="form-control input-sm text-right qty" name="qty[{{itemCode}}]" id="qty_{{itemCode}}" value="">
				{{/if}}
				</td>
			</tr>
		{{/if}}
	{{/if}}
{{/each}}
</script>



<script src="<?php echo base_url(); ?>scripts/inventory/return_lend/return_lend.js"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_lend/return_lend_add.js"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_lend/return_lend_control.js"></script>
<?php $this->load->view('include/footer'); ?>

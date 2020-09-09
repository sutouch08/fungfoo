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
				<?php if($doc->status == 1 && $this->pm->can_delete) : ?>
					<button type="button" class="btn btn-sm btn-danger" onclick="unsave()">ยกเลิกการบันทึก</button>
				<?php endif; ?>
				<?php if($doc->status == 1) : ?>
				<button type="button" class="btn btn-sm btn-info" onclick="doExport()"><i class="fa fa-send"></i> ส่งข้อมูลไป SAP </button>
				<?php endif; ?>
				<button type="button" class="btn btn-sm btn-success" onclick="printReturn()"><i class="fa fa-print"></i> พิมพ์</button>
      </p>
    </div>
</div>
<hr />

<div class="row">
    <div class="col-sm-1 col-1-harf padding-5 first">
    	<label>เลขที่เอกสาร</label>
        <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
    </div>
		<div class="col-sm-1 col-1-harf padding-5">
    	<label>วันที่</label>
      <input type="text" class="form-control input-sm text-center" value="<?php echo thai_date($doc->date_add); ?>" readonly disabled />
    </div>
		<div class="col-sm-4 padding-5">
			<label>ผู้ยืม</label>
			<input type="text" class="form-control input-sm" value="<?php echo $doc->customer_name; ?>" disabled/>
		</div>

		<div class="col-sm-3 col-3-harf padding-5">
			<label>ผู้รับคืน</label>
			<input type="text" class="form-control input-sm"  value="<?php echo $doc->user_name; ?>" disabled/>
		</div>
		<div class="col-sm-1 col-1-harf padding-5 last">
			<label>ใบยืมสินค้า</label>
			<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->lend_code; ?>" disabled />
		</div>

		<div class="divider-hidden"></div>

		<div class="col-sm-3 padding-5 first">
			<label>จากโซน[ยืม]</label>
			<input type="text" class="form-control input-sm" value="<?php echo $doc->from_zone_name; ?>" disabled />
		</div>
		<div class="col-sm-3 padding-5">
			<label>เข้าโซน[รับคืน]</label>
			<input type="text" class="form-control input-sm" value="<?php echo $doc->to_zone_name; ?>" disabled />
		</div>
		<div class="col-sm-6 padding-5 last">
    	<label>หมายเหตุ</label>
        <input type="text" class="form-control input-sm" value="<?php echo $doc->remark; ?>" disabled/>
    </div>
</div>
<?php
if($doc->status == 2)
{
  $this->load->view('cancle_watermark');
}
?>
<hr class="margin-top-15"/>
<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped border-1">
			<thead>
				<tr>
					<th class="width-5 middle text-center">No.</th>
					<th class="width-15 middle">รหัส</th>
					<th class="width-35 middle">สินค้า</th>
					<th class="width-10 middle text-right">ยืม</th>
					<th class="width-15 middle text-right">คืนแล้ว(รวมครั้งนี้)</th>
					<th class="width-10 middle text-right">ครั้งนี้</th>
					<th class="width-10 middle text-right">คงเหลือ</th>
				</tr>
			</thead>
			<tbody id="result">
<?php if(!empty($details)) : ?>
	<?php $no = 1; ?>
	<?php $total_lend = 0; ?>
	<?php $total_receive = 0; ?>
	<?php $total_qty = 0; ?>
	<?php $total_backlogs = 0; ?>
	<?php foreach($details as $rs) : ?>
		<?php $backlogs = $rs->qty - $rs->receive; ?>
		<?php $backlogs = $backlogs < 0 ? 0 : $backlogs; ?>
				<tr>
					<td class="middle text-center no"><?php echo $no; ?></td>
					<td class="middle"><?php echo $rs->product_code; ?></td>
					<td class="middle"><?php echo $rs->product_name; ?></td>
					<td class="middle text-right"><?php echo ac_format($rs->qty); ?></td>
					<td class="middle text-right"><?php echo ac_format($rs->receive); ?></td>
					<td class="middle text-right"><?php echo ac_format($rs->return_qty); ?></td>
					<td class="middle text-right"><?php echo ac_format($backlogs); ?></td>
				</tr>
	<?php
				$no++;
				$total_lend += $rs->qty;
				$total_receive += $rs->receive;
				$total_qty += $rs->return_qty;
				$total_backlogs += $backlogs;
	?>
	<?php endforeach; ?>
			  <tr>
			  	<td colspan="3" class="text-right">รวม</td>
					<td class="middle text-right"><?php echo number($total_lend); ?></td>
					<td class="middle text-right"><?php echo number($total_receive); ?></td>
					<td class="middle text-right"><?php echo number($total_qty); ?></td>
					<td class="middle text-right"><?php echo number($total_backlogs); ?></td>
			  </tr>
<?php else : ?>
				<tr>
					<td colspan="7" class="text-center">--- ไม่พบรายการ ---</td>
				</tr>
<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
<input type="hidden" name="customer_code" id="customer_code" value="<?php echo $doc->customer_code; ?>">
<input type="hidden" name="zone_code" id="zone_code" value="<?php echo $doc->to_zone; ?>">
<input type="hidden" name="lend_code" id="return_code" value="<?php echo $doc->code; ?>">

<script src="<?php echo base_url(); ?>scripts/inventory/return_lend/return_lend.js"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_lend/return_lend_add.js"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_lend/return_lend_control.js"></script>
<?php $this->load->view('include/footer'); ?>

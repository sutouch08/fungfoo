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
  <?php if($doc->is_approve == 1) : ?>
				<button type="button" class="btn btn-sm btn-info" onclick="doExport()"><i class="fa fa-send"></i> ส่งข้อมูลไป SAP</button>
	<?php endif; ?>
	<?php if($doc->status == 1 && $this->pm->can_edit) : ?>
				<button type="button" class="btn btn-sm btn-danger" onclick="unsave()">ยกเลิกการบันทึก</button>
	<?php endif; ?>
	<?php if($doc->status == 1 && $doc->is_approve == 0 && $this->pm->can_approve) : ?>
				<button type="button" class="btn btn-sm btn-primary" onclick="approve()"><i class="fa fa-check"></i> อนุมัติ</button>
	<?php endif; ?>
				<button type="button" class="btn btn-sm btn-info" onclick="printReturn()"><i class="fa fa-print"></i> พิมพ์</button>
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
      <input type="text" class="form-control input-sm text-center edit" name="date_add" id="dateAdd" value="<?php echo thai_date($doc->date_add, FALSE); ?>" readonly disabled/>
    </div>
		<div class="col-sm-2 padding-5">
			<label>เลขที่บิล[SAP]</label>
			<input type="text" class="form-control input-sm text-center edit" name="invoice" id="invoice" value="<?php echo $doc->invoice; ?>" disabled />
		</div>
		<div class="col-sm-4 padding-5">
			<label>ลูกค้า</label>
			<input type="text" class="form-control input-sm edit" name="customer" id="customer" value="<?php echo $doc->customer_name; ?>" disabled/>
		</div>
		<div class="col-sm-3 padding-5 last">
			<label>คลัง[รับคืน]</label>
			<input type="text" class="form-control input-sm edit" name="warehouse" id="warehouse" value="<?php echo $doc->warehouse_name; ?>" disabled />
		</div>
		<div class="col-sm-3 padding-5 first">
			<label>โซน[รับคืน]</label>
			<input type="text" class="form-control input-sm edit" name="zone" id="zone" value="<?php echo $doc->zone_name; ?>" disabled />
		</div>
    <div class="col-sm-9 padding-5">
    	<label>หมายเหตุ</label>
        <input type="text" class="form-control input-sm edit" name="remark" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" value="<?php echo $doc->remark; ?>" disabled />
    </div>
</div>

<input type="hidden" id="return_code" value="<?php echo $doc->code; ?>" />
<input type="hidden" id="customer_code" value="<?php echo $doc->customer_code; ?>" />
<input type="hidden" name="warehouse_code" id="warehouse_code" value="<?php echo $doc->warehouse_code; ?>"/>
<input type="hidden" name="zone_code" id="zone_code" value="<?php echo $doc->zone_code; ?>" />

<hr class="margin-top-15 margin-bottom-15"/>
<?php
if($doc->status == 2)
{
  $this->load->view('cancle_watermark');
}
?>
<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped border-1">
			<thead>
				<tr>
					<th class="width-5 text-center">ลำดับ</th>
					<th class="width-15">บาร์โค้ด</th>
					<th class="">สินค้า</th>
					<th class="width-15 text-center">เลขที่บิล</th>
					<th class="width-10 text-right">ราคา</th>
					<th class="width-10 text-right">ส่วนลด</th>
					<th class="width-10 text-right">จำนวน</th>
					<th class="width-10 text-right">มูลค่า</th>
				</tr>
			</thead>
			<tbody id="detail-table">
<?php if(!empty($details)) : ?>
<?php  $no = 1; ?>
<?php  $total_qty = 0; ?>
<?php  $total_amount = 0; ?>
<?php  foreach($details as $rs) : ?>
				<tr>
					<td class="middle text-center no"><?php echo $no; ?></td>
					<td class="middle"><?php echo $rs->barcode; ?></td>
					<td class="middle"><?php echo $rs->product_code .' : '.$rs->product_name; ?></td>
					<td class="middle text-center"><?php echo $rs->invoice_code; ?></td>
					<td class="middle text-right"><?php echo number($rs->price, 2); ?></td>
					<td class="middle text-right"><?php echo $rs->discount_percent; ?> %</td>
					<td class="middle text-right"><?php echo round($rs->qty,2); ?></td>
					<td class="middle text-right"><?php echo number($rs->amount,2); ?></td>
				</tr>
<?php
				$no++;
				$total_qty += $rs->qty;
				$total_amount += ($rs->qty * $rs->price);
?>
<?php  endforeach; ?>
				<tr>
					<td colspan="6" class="middle text-right">รวม</td>
					<td class="middle text-right" id="total-qty"><?php echo number($total_qty); ?></td>
					<td class="middle text-right" id="total-amount"><?php echo number($total_amount, 2); ?></td>
				</tr>
<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>


<script src="<?php echo base_url(); ?>scripts/inventory/return_order/return_order.js"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_order/return_order_add.js"></script>
<?php $this->load->view('include/footer'); ?>

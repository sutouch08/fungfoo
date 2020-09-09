<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6">
    	<p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
      </p>
    </div>
</div><!-- End Row -->
<hr class=""/>

<div class="row">
  <div class="col-sm-2 hidden-xs padding-5 first">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $data->code; ?>" disabled />
  </div>

  <div class="col-sm-1 col-1-harf col-xs-12 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center edit" name="date_add" id="date_add" value="<?php echo thai_date($data->date_add); ?>" required readonly disabled />
  </div>

  <div class="col-sm-3 col-xs-12 padding-5">
    <label>ลูกค้า[ในระบบ]</label>
    <input type="text" class="form-control input-sm edit" name="customer" id="customer" value="<?php echo $data->customer_name; ?>" required disabled/>
  </div>

	<div class="col-sm-3 col-xs-12 padding-5">
    <label>ผู้ติดต่อ</label>
		<input type="text" class="form-control input-sm edit" name="contact" id="contact" value="<?php echo $data->contact; ?>" disabled/>
  </div>

  <div class="col-sm-1 col-1-harf col-xs-12 padding-5">
    <label>เงื่อนไข</label>
		<select class="form-control input-sm edit" name="is_term" id="is_term" disabled>
			<option value="0" <?php echo is_selected($data->is_term, 0); ?>>เงินสด</option>
      <option value="1" <?php echo is_selected($data->is_term, 1); ?>>เครดิต</option>
    </select>
  </div>

	<div class="col-sm-1 col-xs-12 padding-5 last">
    <label>เครดิต(วัน)</label>
		<input type="number" class="form-control input-sm text-center edit"
		name="credit_term" id="credit_term"
		value="<?php echo $data->credit_term; ?>" <?php echo ($data->is_term == 0 ? 'readonly' : ''); ?>
		disabled/>
  </div>

  <div class="col-sm-10 col-xs-12 padding-5 first">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm edit" name="remark" id="remark" value="<?php echo $data->remark; ?>" disabled>
  </div>

  <div class="col-sm-2 padding-5 col-xs-12 last">
    <label class="display-block not-show">Submit</label>
		<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
    <button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="get_edit()"><i class="fa fa-pencil"></i> แก้ไข</button>
		<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="update()"><i class="fa fa-save"></i> บันทึก</button>
		<?php endif; ?>
  </div>
</div>
<input type="hidden" name="customerCode" id="customerCode" value="<?php echo $data->customer_code; ?>" />
<input type="hidden" name="code" id="code" value="<?php echo $data->code; ?>" />


<hr class="margin-top-15">

<?php $this->load->view('quotation/quotation_control'); ?>
<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped border-1">
			<thead>
				<tr>
					<th class="width-5 middle text-center">ลำดับ</th>
					<th class="width-15 middle">รหัสสินค้า</th>
					<th class="width-25 middle">ชื่อสินค้า</th>
					<th class="width-10 middle text-right">ราคา</th>
					<th class="width-10 middle text-right">จำนวน</th>
					<th class="width-15 middle">ส่วนลด</th>
					<th class="width-15 middle text-right">มูลค่า</th>
					<th class=""></th>
				</tr>
			</thead>
			<tbody>
		<?php if(!empty($details)) : ?>
		<?php  $no = 1; ?>
		<?php 	foreach($details as $rs) : ?>
			<tr>
				<td class="middle text-center"><?php echo $no; ?></td>
				<td class="middle"><?php echo $rs->product_code; ?></td>
				<td class="middle"><?php echo $rs->product_name; ?></td>
				<td class="middle text-right"><?php echo number($rs->price,2); ?></td>
				<td class="middle text-right"><?php echo number($rs->qty); ?></td>
				<td class="middle text-center"><?php echo discountLabel($rs->discount1, $rs->discount2, $rs->discount3); ?></td>
				<td class="middle text-right"><?php echo number($rs->total_amount, 2); ?></td>
				<td class="middle text-right">
					<button class="btn btn-minier btn-danger" onclick="reomveRow(<?php echo $rs->id; ?>)"><i class="fa fa-trash"></i></button>
				</td>
			</tr>
		<?php   $no++; ?>
		<?php 	endforeach; ?>
		<?php else : ?>
			<tr>
				<td colspan="8" class="middle text-center">---- ไม่พบรายการ ----</td>
			</tr>
		<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>



<form id="orderForm">
<div class="modal fade" id="orderGrid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" id="modal" style="min-width:250px;">
		<div class="modal-content">
  			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="modalTitle">title</h4>
        <div class="margin-top-10 text-center">
          <label>ส่วนลด</label>
          <input type="text" class="form-control input-sm input-medium text-center inline" id="discountLabel" value="0"/>
        </div>
			 </div>
			 <div class="modal-body" id="modalBody"></div>
			 <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
				<button type="button" class="btn btn-primary" onClick="insert_item()" >เพิ่มในรายการ</button>
			 </div>
		</div>
	</div>
</div>
</form>


<script src="<?php echo base_url(); ?>scripts/quotation/quotation.js"></script>
<script src="<?php echo base_url(); ?>scripts/quotation/quotation_add.js"></script>
<script src="<?php echo base_url(); ?>scripts/orders/product_tab_menu.js"></script>


<?php $this->load->view('include/footer'); ?>

<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-12">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
</div><!-- End Row -->
<hr class=""/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-sm-1 col-1-harf padding-5 first">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
  </div>

  <div class="col-sm-1 col-1-harf padding-5">
    <label>ลูกค้า</label>
    <input type="text" class="form-control input-sm search" name="customer" value="<?php echo $customer; ?>" />
  </div>

	<div class="col-sm-1 col-1-harf padding-5">
    <label>พนักงาน</label>
    <input type="text" class="form-control input-sm search" name="user" value="<?php echo $user; ?>" />
  </div>

	<div class="col-sm-2 padding-5">
    <label>เลขที่บัญชี</label>
		<select class="form-control input-sm" name="account" onchange="getSearch()">
      <option value="">ทั้งหมด</option>
      <?php echo select_bank_account($account); ?>
    </select>
  </div>
	<div class="col-sm-2 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
    </div>

  </div>

  <div class="col-sm-1 col-1-harf padding-5">
    <label>สถานะ</label>
		<select class="form-control input-sm" name="valid" onchange="getSearch()">
      <option value="0" <?php echo is_selected($valid, 0); ?>>รอตรวจสอบ</option>
      <option value="1" <?php echo is_selected($valid, 1); ?>>ยืนยันแล้ว</option>
    </select>
  </div>

  <div class="col-sm-1 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-sm-1 padding-5 last">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
<hr class="margin-top-15">
</form>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped table-hover border-1">
			<thead>
				<tr>
					<th class="width-5 middle text-center">ลำดับ</th>
					<th class="width-10 middle">เลขที่เอกสาร</th>
          <th class="width-10 middle">ช่องทาง</th>
					<th class="width-20 middle">ลูกค้า</th>
          <th class="width-15 middle hidden-md">พนักงาน</th>
					<th class="width-10 middle text-right">ยอดเงิน</th>
					<th class="width-15 middle text-center">เลขที่บัญชี</th>
					<th class="width-10 middle"></th>
				</tr>
			</thead>
			<tbody>
        <?php if(!empty($orders)) : ?>
          <?php $no = $this->uri->segment(4) + 1; ?>
          <?php foreach($orders as $rs) : ?>
            <?php $customer_name = (!empty($rs->customer_ref)) ? $rs->customer_ref : $rs->customer_name; ?>
            <tr id="row-<?php echo $rs->id; ?>" sytle="font-size:12px;">
              <td class="middle text-center"><?php echo $no; ?></td>
              <td class="middle" style="font-size:12px;"><?php echo $rs->order_code; ?></td>
              <td class="middle" style="font-size:12px;"><?php echo $rs->channels; ?></td>
              <td class="middle" style="font-size:12px;"><?php echo $customer_name; ?></td>
              <td class="middle hidden-md" style="font-size:12px;"><?php echo $rs->user; ?></td>
              <td class="middle text-right" style="font-size:12px;">
								<?php if($rs->is_deposit == 1) : ?>
									[มัดจำ] &nbsp;&nbsp;
								<?php endif; ?>
								<?php echo number($rs->pay_amount,2); ?>
							</td>
              <td class="middle text-center" style="font-size:12px;"><?php echo $rs->acc_no; ?></td>
              <td class="middle text-right">
                <button type="button" class="btn btn-xs btn-info" onClick="viewDetail(<?php echo $rs->id; ?>)"><i class="fa fa-eye"></i></button>
          <?php if($this->pm->can_delete && $rs->state < 8 && $rs->valid == 0) : ?>
                <button type="button" class="btn btn-xs btn-danger" onClick="removePayment(<?php echo $rs->id; ?>, '<?php echo $rs->order_code; ?>')"><i class="fa fa-trash"></i></button>
          <?php endif; ?>
              </td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<div class='modal fade' id='confirmModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
    <div class='modal-dialog' style="width:350px;">
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'><i class="fa fa-times"></i></button>
            </div>
            <div class='modal-body' id="detailBody">

            </div>
            <div class='modal-footer'>
            </div>
        </div>
    </div>
</div>

<div class='modal fade' id='imageModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
    <div class='modal-dialog' style="width:500px;">
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'><i class="fa fa-times"></i></button>
            </div>
            <div class='modal-body' id="imageBody">

            </div>
            <div class='modal-footer'>
            </div>
        </div>
    </div>
</div>

<script id="detailTemplate" type="text/x-handlebars-template">
<div class="row">
	<div class="col-sm-12 text-center">ข้อมูลการชำระเงิน</div>
</div>
<hr/>
<div class="row">
	<div class="col-sm-4 label-left">ยอดที่ต้องชำระ :</div><div class="col-sm-8">{{ orderAmount }}</div>
	<div class="col-sm-4 label-left">ยอดโอนชำระ : </div><div class="col-sm-8"><span style="font-weight:bold; color:#E9573F;">฿ {{ payAmount }}</span></div>
	<div class="col-sm-4 label-left">วันที่โอน : </div><div class="col-sm-8">{{ payDate }}</div>
	<div class="col-sm-4 label-left">ธนาคาร : </div><div class="col-sm-8">{{ bankName }}</div>
	<div class="col-sm-4 label-left">สาขา : </div><div class="col-sm-8">{{ branch }}</div>
	<div class="col-sm-4 label-left">เลขที่บัญชี : </div><div class="col-sm-8"><span style="font-weight:bold; color:#E9573F;">{{ accNo }}</span></div>
	<div class="col-sm-4 label-left">ชื่อบัญชี : </div><div class="col-sm-8">{{ accName }}</div>
	{{#if imageUrl}}
		<div class="col-sm-12 top-row top-col text-center">
			<a href="javascript:void(0)" onClick="viewImage('{{ imageUrl }}')">
				รูปสลิปแนบ	<i class="fa fa-paperclip fa-rotate-90"></i>
			</a>
		</div>
	{{else}}
		<div class="col-sm-12 top-row top-col text-center">---  ไม่พบไฟล์แนบ  ---</div>
	{{/if}}
	{{#if valid}}
  <?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
	<div class="col-sm-12 top-col">
		<button type="button" class="btn btn-sm btn-warning btn-block" onClick="confirmPayment({{ id }})">
			<i class="fa fa-check-circle"></i> ยืนยันการชำระเงิน
		</button>
	</div>
  <?php endif; ?>
  {{else}}
  <?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
	<div class="col-sm-12 top-col">
		<button type="button" class="btn btn-sm btn-danger btn-block" onClick="unConfirmPayment({{ id }})">
			<i class="fa fa-check-circle"></i> ยกเลิกการยืนยัน
		</button>
	</div>
  <?php endif; ?>
	{{/if}}
</div>
</script>

<script id="orderTableTemplate" type="text/x-handlebars-template">
{{#each this}}
<tr id="{{ id }}" class="font-size-12">
<td class="text-center">{{ no }}</td>
<td> {{ reference }}</td>
<td align="center"> {{ channels }}</td>
<td>{{ customer }}</td>
<td>{{ employee }}</td>
<td align="center">{{ orderAmount }}</td>
<td align="center">{{ payAmount }}</td>
<td align="center">{{ accNo }}</td>
<td align="right">
	<button type="button" class="btn btn-xs btn-warning" onClick="viewDetail({{ id }})"><i class="fa fa-eye"></i></button>
	<button type="button" class="btn btn-xs btn-danger" onClick="removePayment({{ id }}, '{{ reference }}')"><i class="fa fa-trash"></i></button>
 </td>
</tr>
{{/each}}
</script>

<script src="<?php echo base_url(); ?>scripts/orders/payment/payment.js"></script>
<script src="<?php echo base_url(); ?>scripts/orders/payment/payment_list.js"></script>

<?php $this->load->view('include/footer'); ?>

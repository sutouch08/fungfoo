<?php $this->load->view('include/header'); ?>
<?php if($document->status == 0) : ?>
<div class="row">
	<div class="col-sm-6">
    	<h3 class="title" ><?php echo $this->title; ?></h3>
	</div>
    <div class="col-sm-6">
    <p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="leave()"><i class="fa fa-arrow-left"></i> กลับ</button>
    <?php if($this->pm->can_add) : ?>
			<button type="button" class="btn btn-sm btn-success" onclick="checkLimit()"><i class="fa fa-save"></i> บันทึก</button>
    <?php	endif; ?>
    </p>
    </div>
</div>
<hr />

<div class="row">
  <div class="col-sm-1 col-1-harf padding-5 first">
  	<label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $document->code; ?>" disabled />
  </div>
	<div class="col-sm-1 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center header-box" name="date_add" id="dateAdd" value="<?php echo thai_date($document->date_add); ?>" disabled />
  </div>
	<div class="col-sm-8 col-8-harf padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm header-box" name="remark" id="remark" value="<?php echo $document->remark; ?>" disabled />
	</div>
	<div class="col-sm-1 padding-5 last">
<?php if($this->pm->can_edit && $document->status == 0) : ?>
		<label class="display-block not-show">edit</label>
		<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="editHeader()">
			<i class="fa fa-pencil"></i> แก้ไข
		</button>
		<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="updateHeader()">
			<i class="fa fa-save"></i> อัพเดต
		</button>
<?php endif; ?>
	</div>

</div>
<hr class="margin-top-10 margin-bottom-10"/>
<form id="receiveForm" method="post" action="<?php echo $this->home; ?>/save">
<div class="row">
	<div class="col-sm-2 padding-5 first">
    	<label>ใบเบิกสินค้า</label>
        <input type="text" class="form-control input-sm text-center" name="order_code" id="order_code" placeholder="ค้นหาใบสั่งซื้อ" />
        <span class="help-block red" id="po-error"></span>
    </div>
		<div class="col-sm-1 padding-5">
			<label class="display-block not-show">clear</label>
			<button type="button" class="btn btn-xs btn-info btn-block hide" id="btn-change-po" onclick="changePo()">เปลี่ยน</button>
			<button type="button" class="btn btn-xs btn-primary btn-block" id="btn-get-po" onclick="getData()">ยืนยัน</button>
		</div>
    <div class="col-sm-2 padding-5">
    	<label>ใบส่งสินค้า</label>
        <input type="text" class="form-control input-sm text-center" name="invoice" id="invoice" placeholder="อ้างอิงใบส่งสินค้า" />
        <span class="help-block red" id="invoice-error"></span>
    </div>
    <div class="col-sm-3 padding-5">
    	<label>ชื่อโซน</label>
        <input type="text" class="form-control input-sm text-center zone" name="zoneName" id="zoneName" placeholder="ค้นหาชื่อโซน"  />
        <span class="help-block red" id="zone-error"></span>
    </div>

</div>
<hr class="margin-top-15"/>
<div class="row">
	<div class="col-sm-1">
    	<label>จำนวน</label>
        <input type="text" class="form-control input-sm text-center" id="qty" value="1.00" />
    </div>
    <div class="col-sm-3 ">
    	<label>บาร์โค้ดสินค้า</label>
        <input type="text" class="form-control input-sm text-center" id="barcode" placeholder="ยิงบาร์โค้ดเพื่อรับสินค้า" autocomplete="off"  />
    </div>
    <div class="col-sm-1">
    	<label class="display-block not-show">ok</label>
        <button type="button" class="btn btn-xs btn-primary" onclick="checkBarcode()"><i class="fa fa-check"></i> ตกลง</button>
    </div>
    <input type="hidden" name="zone_code" id="zone_code" />
    <input type="hidden" name="receive_code" id="receive_code" value="<?php echo $document->code; ?>" />
    <input type="hidden" name="approver" id="approver" value="" />

</div>
<hr class="margin-top-15 margin-bottom-15"/>


<div class="row">
	<div class="col-sm-12">
    	<table class="table table-striped table-bordered">
        	<thead>
            	<tr class="font-size-12">
                	<th class="width-5 text-center">ลำดับ	</th>
                    <th class="width-15 text-center">บาร์โค้ด</th>
                    <th class="width-15 text-center">รหัสสินค้า</th>
                    <th class="width-35">ชื่อสินค้า</th>
                    <th class="width-10 text-center">สั่งซื้อ</th>
                    <th class="width-10 text-center">ค้างรับ</th>
                    <th class="width-10 text-center">จำนวน</th>
                </tr>
            </thead>
            <tbody id="receiveTable">

			      </tbody>
        </table>
    </div>
</div>
</form>

<div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
	<div class="modal-dialog input-xlarge">
    <div class="modal-content">
      <div class="modal-header">
      	<button type='button' class='close' data-dismiss='modal' aria-hidden='true'> &times; </button>
		    <h4 class='modal-title-site text-center' > ผู้มีอำนาจอนุมัติรับสินค้าเกิน </h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-12">
          	<input type="password" class="form-control input-sm text-center" id="sKey" />
            <span class="help-block red text-center" id="approvError">&nbsp;</span>
          </div>
          <div class="col-sm-12">
            <button type="button" class="btn btn-sm btn-primary btn-block" onclick="doApprove()">อนุมัติ</button>
          </div>
        </div>
    	 </div>
      </div>
    </div>
</div>
<script src="<?php echo base_url(); ?>scripts/validate_credentials.js"></script>
<script id="template" type="text/x-handlebarsTemplate">
{{#each this}}
	{{#if @last}}
        <tr>
            <td colspan="4" class="middle text-right"><strong>รวม</strong></td>
            <td class="middle text-center">{{qty}}</td>
            <td class="middle text-center">{{backlog}}</td>
            <td class="middle text-center"><span id="total-receive">0</span></td>
        </tr>
    {{else}}
        <tr class="font-size-12">
            <td class="middle text-center">{{ no }}</td>
            <td class="middle barcode" id="barcode_{{pdCode}}">{{barcode}}</td>
            <td class="middle">{{pdCode}}</td>
            <td class="middle">{{pdName}}</td>
            <td class="middle text-center" id="qty_{{pdCode}}">
      				{{qty}}
      				<input type="hidden" id="limit_{{pdCode}}" value="{{limit}}"/>
      				{{#if barcode}}
      				<input type="hidden" id="{{barcode}}" value="{{pdCode}}" />
      				{{/if}}
			      </td>
            <td class="middle text-center">
						{{backlog}}
						<input type="hidden" id="backlog_{{pdCode}}" value="{{backlog}}" />
						<input type="hidden" id="price_{{pdCode}}" value="{{price}}" />
						</td>
            <td class="middle text-center">
                <input type="text" class="form-control input-sm text-center receive-box pdCode" name="receive[{{pdCode}}]" id="receive_{{pdCode}}" />
            </td>
        </tr>
    {{/if}}
{{/each}}
</script>

<?php else : ?>
  <?php redirect($this->home.'/view_detail/'.$document->code); ?>
<?php endif; ?>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_transform/receive_transform.js"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_transform/receive_transform_add.js"></script>

<?php $this->load->view('include/footer'); ?>

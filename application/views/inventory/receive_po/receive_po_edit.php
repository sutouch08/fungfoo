<?php $this->load->view('include/header'); ?>
<?php if($doc->status == 0) : ?>
<div class="row">
	<div class="col-sm-6">
    	<h3 class="title" ><?php echo $this->title; ?></h3>
	</div>
    <div class="col-sm-6">
    <p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> <?php label('back'); ?></button>
    <?php if($this->pm->can_add) : ?>
			<button type="button" class="btn btn-sm btn-success" onclick="save()"><i class="fa fa-save"></i> <?php label('save'); ?></button>
    <?php	endif; ?>
    </p>
    </div>
</div>
<hr />

<div class="row">
  <div class="col-sm-1 col-1-harf padding-5 first">
  	<label><?php label('doc_num'); ?></label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
  </div>
	<div class="col-sm-1 col-1-harf padding-5">
		<label><?php label('date'); ?></label>
		<input type="text" class="form-control input-sm text-center edit" id="dateAdd" value="<?php echo thai_date($doc->date_add); ?>" readonly disabled/>
	</div>

	<div class="col-sm-1 col-1-harf padding-5">
			<label><?php label('vender_code'); ?></label>
				<input type="text" class="form-control input-sm text-center edit" id="venderCode" value="<?php echo $doc->vender_code; ?>" placeholder="ค้นหารหัสผู้ขาย" disabled/>
	 </div>

	 <div class="col-sm-4 col-4-harf padding-5">
			<label><?php label('vender_name'); ?></label>
				<input type="text" class="form-control input-sm edit" id="venderName" value="<?php echo $doc->vender_name; ?>" placeholder="ค้นหาชื่อผู้ขาย" disabled/>
	 </div>
	<div class="col-sm-1 col-1-harf padding-5">
			<label><?php label('po'); ?></label>
			<input type="text" class="form-control input-sm text-center edit" id="poCode" value="<?php echo $doc->po_code; ?>" placeholder="ค้นหาใบสั่งซื้อ" disabled/>
	</div>
	<div class="col-sm-1 col-1-harf padding-5 last">
		<label><?php label('inv'); ?></label>
		<input type="text" class="form-control input-sm text-center edit" id="invoice" value="<?php echo $doc->invoice_code; ?>" placeholder="อ้างอิงใบส่งสินค้า" disabled/>
	</div>
	<div class="col-sm-2 padding-5 first">
		<label><?php label('zone_code'); ?></label>
		<input type="text" class="form-control input-sm text-center edit" id="zoneCode" value="<?php echo $doc->zone_code; ?>" placeholder="ค้นหารหัสโซน" disabled />
	</div>
	<div class="col-sm-3 padding-5">
		<label><?php label('zone_name'); ?></label>
		<input type="text" class="form-control input-sm text-center edit" id="zoneName" value="<?php echo $doc->zone_name; ?>" placeholder="ค้นหาชื่อโซน" disabled />
	</div>
	<div class="col-sm-6 padding-5">
		<label><?php label('remark'); ?></label>
		<input type="text" class="form-control input-sm edit" id="remark" value="<?php echo $doc->remark; ?>" placeholder="ระบุหมายเตุ(ถ้ามี)" disabled/>
	</div>
	<div class="col-sm-1 padding-5 last">
<?php if($this->pm->can_edit && $doc->status == 0) : ?>
		<label class="display-block not-show">edit</label>
		<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="editHeader()">
			<i class="fa fa-pencil"></i> <?php label('edit'); ?>
		</button>
		<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="updateHeader()">
			<i class="fa fa-save"></i> <?php label('update'); ?>
		</button>
<?php endif; ?>
	</div>
	<input type="hidden" name="code" id="code" value="<?php echo $doc->code; ?>" />
	<input type="hidden" name="approver" id="approver" value="" />
</div>

<hr class="margin-top-15"/>
<?php $this->load->view('inventory/receive_po/receive_po_control'); ?>

<div class="row">
	<div class="col-sm-12">
    	<table class="table table-striped table-bordered">
        	<thead>
            	<tr class="font-size-12">
              	<th class="width-5 text-center"><?php label('Num'); ?>	</th>
                <th class="width-15 text-center"><?php label('item_code'); ?></th>
                <th class="width-35"><?php label('item_name'); ?></th>
								<th class="width-10 text-right"><?php label('price'); ?></th>
                <th class="width-10 text-right"><?php label('qty'); ?></th>
								<th class="width-15 text-right"><?php label('amount'); ?></th>
								<th class="width-5 text-right"></th>
              </tr>
            </thead>
            <tbody id="receiveTable">
						<?php if(!empty($details)) : ?>
							<?php $no = 1; ?>
							<?php $total_qty = 0; ?>
							<?php $total_amount = 0; ?>
							<?php foreach($details as $rs) : ?>
								<tr>
									<td class="middle text-center no"><?php echo $no; ?></td>
									<td class="moddle"><?php echo $rs->product_code; ?></td>
									<td class="middle"><?php echo $rs->product_name; ?></td>
									<td class="middle text-right"><?php echo number($rs->price,2); ?></td>
									<td class="middle text-right"><?php echo number($rs->qty); ?></td>
									<td class="middle text-right"><?php echo number($rs->amount, 2); ?></td>
									<td class="middle text-center">
										<?php if($rs->status === 'N') : ?>
											<button type="button" class="btn btn-minier btn-danger" onclick="removeRow(<?php echo $rs->id; ?>, '<?php echo $rs->product_code; ?>')">
												<i class="fa fa-trash"></i>
											</button>
										<?php endif; ?>
									</td>
								</tr>
								<?php $no++; ?>
								<?php $total_qty += $rs->qty; ?>
								<?php $total_amount += $rs->amount; ?>
							<?php endforeach; ?>
							<tr>
								<td colspan="4" class="middle text-right"><strong><?php label('total'); ?></strong></td>
								<td class="middle text-right"><strong><?php echo number($total_qty); ?></strong></td>
								<td class="middle text-right"><strong><?php echo number($total_amount, 2); ?></strong></td>
								<td></td>
							</tr>
						<?php else : ?>
							<tr id="pre_label">
								<td align='center' colspan='7'><h4>-----  <?php label('no_content'); ?>  -----</h4></td>
							</tr>
						<?php endif; ?>
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
		    <h4 class='modal-title-site text-center' > <?php label('approver'); ?> </h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-12">
          	<input type="password" class="form-control input-sm text-center" id="sKey" />
            <span class="help-block red text-center" id="approvError">&nbsp;</span>
          </div>
          <div class="col-sm-12">
            <button type="button" class="btn btn-sm btn-primary btn-block" onclick="doApprove()"><?php label('approve'); ?></button>
          </div>
        </div>
    	 </div>
      </div>
    </div>
</div>
<script src="<?php echo base_url(); ?>scripts/validate_credentials.js"></script>
<?php else : ?>
  <?php redirect($this->home.'/view_detail/'.$doc->code); ?>
<?php endif; ?>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po.js"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po_add.js"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po_edit.js"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po_control.js"></script>

<?php $this->load->view('include/footer'); ?>

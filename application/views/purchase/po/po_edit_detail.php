<?php $this->load->view('include/header'); ?>

<div class="row">
	<div class="col-sm-6">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6">
    	<p class="pull-right top-p">
       <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> <?php label('back'); ?></button>
			 <?php if(($po->status == 1 OR $po->status == 2) && ($this->pm->can_add OR $this->pm->can_edit)) : ?>
				 <button type="button" class="btn btn-sm btn-danger" onclick="closePO()"><i class="fa fa-lock"></i> <?php label('close_po'); ?></button>
			 <?php endif; ?>
			 <?php if(($this->pm->can_add OR $this->pm->can_edit) && $po->status == 0) : ?>
				 <button type="button" class="btn btn-sm btn-success" onclick="save()"><i class="fa fa-save"></i> <?php label('save'); ?></button>
			 <?php endif; ?>
			 <?php if(($this->pm->can_add OR $this->pm->can_edit) && $po->status == 1) : ?>
				 <button type="button" class="btn btn-sm btn-warning" onclick="unsave()"><i class="fa fa-refresh"></i> <?php label('unsave'); ?></button>
			 <?php endif; ?>
			 <?php if($po->status > 0 && $po->status < 4) : ?>
				 <button type="button" class="btn btn-sm btn-info" onclick="printPO()"><i class="fa fa-print"></i> <?php label('print'); ?></button>
			 <?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr class=""/>
<form class="form-horizontal" id="editForm" method="post" action="<?php echo $this->home.'/update'; ?>">
  <div class="row">
    <div class="col-sm-1 col-1-harf col-xs-6 padding-5 first">
      <label><?php label('doc_num'); ?></label>
      <input type="text" class="form-control input-sm text-center" value="<?php echo $po->code; ?>" disabled>
    </div>
    <div class="col-sm-1 col-xs-6 padding-5">
      <label><?php label('date'); ?></label>
      <input type="text" class="form-control input-sm text-center edit" name="date_add" id="date_add" value="<?php echo date('d-m-Y'); ?>" disabled readonly required>
    </div>
    <div class="col-sm-1 col-1-harf col-xs-6 padding-5">
      <label><?php label('vender_code'); ?></label>
      <input type="text" class="form-control input-sm text-center edit" name="vender_code" id="vender_code" value="<?php echo $po->vender_code; ?>" disabled required>
    </div>
    <div class="col-sm-3 col-xs-6 padding-5">
      <label><?php label('vender_name'); ?></label>
      <input type="text" class="form-control input-sm edit" name="vender_name" id="vender_name" value="<?php echo $po->vender_name; ?>" disabled required>
    </div>
    <div class="col-sm-1 col-xs-6 padding-5">
      <label><?php label('require_date'); ?></label>
      <input type="text" class="form-control input-sm text-center edit" name="require_date" id="require_date" value="<?php echo thai_date($po->require_date); ?>" disabled readonly required>
    </div>

		<?php if($po->status == 0 OR $po->status == 1) : ?>
    <div class="col-sm-3 col-xs-12 padding-5">
      <label><?php label('remark'); ?></label>
      <input type="text" class="form-control input-sm edit" name="remark" id="remark" value="<?php echo $po->remark; ?>" disabled>
    </div>
    <div class="col-sm-1 col-xs-4 padding-5 last">
      <label class="display-block not-show">add</label>
      <button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="getEdit()">
				<i class="fa fa-pencil"></i> <?php label('edit'); ?>
			</button>
			<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="update()">
				<i class="fa fa-save"></i> <?php label('update'); ?>
			</button>
    </div>
		<?php else : ?>
			<div class="col-sm-4 col-xs-12 padding-5 last">
	      <label><?php label('remark'); ?></label>
	      <input type="text" class="form-control input-sm edit" name="remark" id="remark" value="<?php echo $po->remark; ?>" disabled>
	    </div>
		<?php endif; ?>
  </div>

	<input type="hidden" name="code" id="code" value="<?php echo $po->code; ?>">
</form>
<hr class="margin-top-15">

<?php if($po->status < 2) : ?>
<?php $this->load->view('purchase/po/po_control'); ?>
<?php endif; ?>

<?php $this->load->view('purchase/po/po_detail'); ?>


<form id="orderForm">
<div class="modal fade" id="orderGrid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" id="modal" style="min-width:250px;">
		<div class="modal-content">
  			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="modalTitle">title</h4>
			 </div>
			 <div class="modal-body" id="modalBody"></div>
			 <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
				<button type="button" class="btn btn-primary" onClick="addToPo()" >เพิ่มในรายการ</button>
			 </div>
		</div>
	</div>
</div>
</form>

<script src="<?php echo base_url(); ?>scripts/purchase/po.js"></script>
<script src="<?php echo base_url(); ?>scripts/purchase/po_add.js"></script>
<script src="<?php echo base_url(); ?>scripts/purchase/po_control.js"></script>
<?php $this->load->view('include/footer'); ?>

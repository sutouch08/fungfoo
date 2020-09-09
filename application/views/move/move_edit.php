<?php $this->load->view('include/header'); ?>
<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
<div class="row">
	<div class="col-sm-3">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-9">
    	<p class="pull-right top-p">
				<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		    <?php if($doc->status == 1 && $this->pm->can_edit) : ?>
					<button type="button" class="btn btn-sm btn-danger" onclick="unSave()"><i class="fa fa-exclamation-triangle"></i> ยกเลิกการบันทึก</button>
		    <?php endif; ?>
		    <?php if($doc->status == 1 && $this->pm->can_add OR $this->pm->can_edit) : ?>

		      <?php //if($doc->status == 0 && $barcode === TRUE) : ?>
		        <!--<button type="button" class="btn btn-sm btn-primary" onclick="goUseKeyboard()">คีย์มือ</button> -->
		      <?php //endif; ?>

		      <?php //if($doc->status == 0 && $barcode === FALSE) : ?>
		        <!--<button type="button" class="btn btn-sm btn-primary" onclick="goUseBarcode()">ใช้บาร์โค้ด</button>-->
		      <?php //endif; ?>
					<?php if($doc->status == 0 && ($this->pm->can_add OR $this->pm->can_edit)) : ?>
		      <button type="button" class="btn btn-sm btn-success" onclick="save()"><i class="fa fa-save"></i> บันทึก</button>
					<?php endif; ?>
		    <?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr/>
<?php
	$this->load->view('move/move_edit_header');
	if($doc->status == 0)
	{
		$this->load->view('move/move_control');
	}

	if($barcode === TRUE)
	{
		$this->load->view('move/move_detail_barcode');
	}
	else
	{
		$this->load->view('move/move_detail');
	}
?>

<?php else : ?>
<?php $this->load->view('deny_page'); ?>
<?php endif; ?>
<script src="<?php echo base_url(); ?>scripts/move/move.js"></script>
<script src="<?php echo base_url(); ?>scripts/move/move_add.js"></script>
<script src="<?php echo base_url(); ?>scripts/move/move_control.js"></script>
<script src="<?php echo base_url(); ?>scripts/move/move_detail.js"></script>
<script src="<?php echo base_url(); ?>scripts/beep.js"></script>

<?php $this->load->view('include/footer'); ?>

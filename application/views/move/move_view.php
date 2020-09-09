<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-3">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-9">
    	<p class="pull-right top-p">
				<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		    <?php if($doc->status == 1) : ?>
				<?php if($this->pm->can_edit) : ?>
					<button type="button" class="btn btn-sm btn-danger" onclick="unSave()"><i class="fa fa-exclamation-triangle"></i> ยกเลิกการบันทึก</button>
				<?php endif; ?>
		    <?php endif; ?>
				<button type="button" class="btn btn-sm btn-primary" onclick="printMove()"><i class="fa fa-print"></i> พิมพ์</button>
      </p>
    </div>
</div><!-- End Row -->
<input type="hidden" id="move_code" name="move_code" value="<?php echo $doc->code; ?>" />
<hr/>
<?php
	$this->load->view('move/move_view_header');
	$this->load->view('move/move_view_detail');
?>

<script src="<?php echo base_url(); ?>scripts/move/move.js"></script>
<script src="<?php echo base_url(); ?>scripts/move/move_add.js"></script>
<script src="<?php echo base_url(); ?>scripts/move/move_control.js"></script>
<script src="<?php echo base_url(); ?>scripts/move/move_detail.js"></script>

<?php $this->load->view('include/footer'); ?>

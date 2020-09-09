<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-3">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-9">
    	<p class="pull-right" style="margin-bottom:1px;">
				<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>

				<button type="button" class="btn btn-sm btn-default" onclick="printOrderSheet()"><i class="fa fa-print"></i> พิมพ์</button>
				<?php if($order->state < 4 && $this->pm->can_delete && $order->never_expire == 0) : ?>
				<button type="button" class="btn btn-sm btn-primary" onclick="setNotExpire(1)">ยกเว้นการหมดอายุ</button>
				<?php endif; ?>
				<?php if($order->state < 4 && $this->pm->can_delete && $order->never_expire == 1) : ?>
					<button type="button" class="btn btn-sm btn-info" onclick="setNotExpire(0)">ไม่ยกเว้นการหมดอายุ</button>
				<?php endif; ?>
				<?php if($this->pm->can_delete && $order->is_expired == 1) : ?>
					<button type="button" class="btn btn-sm btn-warning" onclick="unExpired()">ทำให้ไม่หมดอายุ</button>
				<?php endif; ?>
				<?php if($order->state < 4 && ($this->pm->can_add OR $this->pm->can_edit)) : ?>
				<button type="button" class="btn btn-sm btn-yellow" onclick="editDetail()"><i class="fa fa-pencil"></i> แก้ไขรายการ</button>
					<?php if($order->status == 0) : ?>
						<button type="button" class="btn btn-sm btn-success" onclick="saveOrder()"><i class="fa fa-save"></i> บันทึก</button>
					<?php endif; ?>
				<?php endif; ?>
				<?php if($order->state == 1 && $order->status == 1 && $order->is_expired == 0 && $this->pm->can_approve) : ?>
						<button type="button" class="btn btn-sm btn-success" onclick="approve()"><i class="fa fa-check"></i> อนุมัติ</button>
				<?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr/>
<input type="hidden" id="order_code" value="<?php echo $order->code; ?>" />
<?php $this->load->view('support/support_edit_header'); ?>
<?php $this->load->view('orders/order_state'); ?>
<?php $this->load->view('support/support_discount_bar'); ?>
<?php $this->load->view('support/support_detail'); ?>
<script src="<?php echo base_url(); ?>scripts/support/support.js"></script>
<script src="<?php echo base_url(); ?>scripts/support/support_add.js"></script>
<script src="<?php echo base_url(); ?>scripts/print/print_order.js"></script>

<?php $this->load->view('include/footer'); ?>

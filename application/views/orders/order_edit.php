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
				<?php if($order->is_term == 0 && ($this->pm->can_add OR $this->pm->can_edit) && $order->status == 1 && $order->is_paid == 0) : ?>
				<button type="button" class="btn btn-sm btn-info" onclick="payOrder()"><i class="fa fa-credit-card"></i> แจ้งชำระเงิน</button>
				<?php endif; ?>
				<?php if(($this->pm->can_add OR $this->pm->can_edit) && $order->status == 1) : ?>
				<button type="button" class="btn btn-sm btn-grey" onClick="inputDeliveryNo()"><i class="fa fa-truck"></i> บันทึกการจัดส่ง</button>
				<?php endif; ?>
				<?php if($order->is_term == 0) : ?>
				<button type="button" class="btn btn-sm btn-purple" onclick="getSummary()"><i class="fa fa-bolt"></i> สรุปข้อมูล</button>
				<?php endif; ?>
				<button type="button" class="btn btn-sm btn-default" onclick="printOrderSheet()"><i class="fa fa-print"></i> พิมพ์</button>
				<!--
				<?php if($this->pm->can_delete && $order->never_expire == 0) : ?>
				<button type="button" class="btn btn-sm btn-primary" onclick="setNotExpire(1)">ยกเว้นการหมดอายุ</button>
				<?php endif; ?>
				<?php if($this->pm->can_delete && $order->never_expire == 1) : ?>
					<button type="button" class="btn btn-sm btn-info" onclick="setNotExpire(0)">ไม่ยกเว้นการหมดอายุ</button>
				<?php endif; ?>
			-->
				<?php if($this->pm->can_delete && $order->is_expired == 1) : ?>
								<button type="button" class="btn btn-sm btn-warning" onclick="unExpired()">ทำให้ไม่หมดอายุ</button>
				<?php endif; ?>
				<?php if($order->state < 4 && ($this->pm->can_add OR $this->pm->can_edit) && $order->is_paid == 0) : ?>
				<button type="button" class="btn btn-sm btn-yellow" onclick="editDetail()"><i class="fa fa-pencil"></i> แก้ไขรายการ</button>
				<?php endif; ?>
				<?php if($order->status == 0) : ?>
					<button type="button" class="btn btn-sm btn-success" onclick="saveOrder()"><i class="fa fa-save"></i> บันทึก</button>
				<?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr/>
<input type="hidden" id="order_code" value="<?php echo $order->code; ?>" />
<?php $this->load->view('orders/order_edit_header'); ?>
<?php $this->load->view('orders/order_panel'); ?>
<?php $this->load->view('orders/order_discount_bar'); ?>
<?php $this->load->view('orders/order_detail'); ?>
<?php $this->load->view('orders/order_online_modal'); ?>
<script src="<?php echo base_url(); ?>assets/js/clipboard.min.js"></script>
<script src="<?php echo base_url(); ?>scripts/orders/orders.js"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_add.js"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_online.js"></script>
<script src="<?php echo base_url(); ?>scripts/print/print_order.js"></script>
<script src="<?php echo base_url(); ?>scripts/print/print_address.js"></script>


<?php $this->load->view('include/footer'); ?>

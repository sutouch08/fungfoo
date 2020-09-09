<?php
if(!$order->is_expired ) :
?>
<hr/>
<div class="row">
	<div class="col-sm-12 margin-top-5 margin-bottom-5">
		<?php if($allowEditPrice) : ?>
      <button type="button" class="btn btn-sm btn-default" id="btn-edit-price" onClick="showPriceBox()">แก้ไขราคา</button>
      <button type="button" class="btn btn-sm btn-primary hide" id="btn-update-price" onClick="getApprove('price')">บันทึกราคา</button>
		<?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?php $this->load->view('validate_credentials'); ?>

<script src="<?php echo base_url(); ?>scripts/orders/order_discount.js"></script>

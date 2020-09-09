
<div class="modal fade" id="payment-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:400px;">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">ชนิดลูกค้า</h4>
      </div>
      <div class="modal-body" id="payment-body">
        <div class="row">
          <div class="col-sm-12">
    <?php
    $qs = $this->db->query("SELECT * FROM payment_method"); ?>
    <?php if($qs->num_rows() > 0) : ?>
      <?php $pm = $this->discount_rule_model->getRulePayment($rule->id); ?>
      <?php foreach($qs->result() as $rs) : ?>
        <?php $se = isset($pm[$rs->code]) ? 'checked' : ''; ?>
              <label class="display-block">
                <input type="checkbox" class="chk-payment" name="chk-payment-<?php echo $rs->code; ?>" id="chk-payment-<?php echo $rs->code; ?>" value="<?php echo $rs->code; ?>" <?php echo $se; ?> />
                <?php echo $rs->name; ?>
              </label>
      <?php endforeach; ?>
    <?php endif;?>
          </div>
        </div>
      </div>
      <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-sm-1 col-1-harf padding-5 first">
    <label><?php label('doc_num'); ?></label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
  </div>

  <div class="col-sm-1 col-1-harf padding-5">
    <label><?php label('date'); ?></label>
    <input type="text" class="form-control input-sm text-center edit" name="date" id="date" value="<?php echo thai_date($doc->date_add); ?>" readonly required disabled />
  </div>

  <div class="col-sm-4 col-4-harf padding-5">
    <label><?php label('from_wh'); ?></label>
    <input type="text" class="form-control input-sm edit" name="from_warehouse" id="from_warehouse" value="<?php echo $doc->from_warehouse_name; ?>" required disabled/>
  </div>

	<div class="col-sm-4 col-4-harf padding-5 last">
    <label><?php label('to_wh'); ?></label>
		<input type="text" class="form-control input-sm edit" name="to_warehouse" id="to_warehouse" value="<?php echo $doc->to_warehouse_name; ?>" required disabled/>
  </div>
  <div class="col-sm-12 padding-5 first last">
    <label><?php label('remark'); ?></label>
    <input type="text" class="form-control input-sm edit" name="remark" id="remark" value="<?php echo $doc->remark; ?>" disabled>
  </div>
</div>
<input type="hidden" id="transfer_code" value="<?php echo $doc->code; ?>" />
<input type="hidden" id="from_warehouse_code" value="<?php echo $doc->from_warehouse; ?>" />
<input type="hidden" id="to_warehouse_code" value="<?php echo $doc->to_warehouse; ?>" />
<hr class="margin-top-15 margin-bottom-15"/>

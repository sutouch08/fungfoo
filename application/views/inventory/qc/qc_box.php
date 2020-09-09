
<!-- แสดงผลกล่อง  -->
<div class="row">
  <div class="col-sm-12" id="box-row">
  <?php if(!empty($box_list)) : ?>
  <?php   foreach($box_list as $rs) : ?>
        <button type="button" class="btn btn-sm btn-default" id="btn-box-<?php echo $rs->id; ?>" onclick="printBox(<?php echo $rs->id; ?>)">
          <i class="fa fa-print"></i>&nbsp;กล่องที่ <?php echo $rs->box_no; ?>&nbsp; : &nbsp;
          <span id="<?php echo $rs->id; ?>"><?php echo number($rs->qty); ?></span>&nbsp; Pcs.
        </button>
  <?php   endforeach; ?>
  <?php else : ?>
    <span id="no-box-label">ยังไม่มีการตรวจสินค้า</span>
  <?php endif; ?>
  </div>
</div>

<hr/>

<script id="box-template" type="text/x-handlebars-template">
  {{#each this}}
<button type="button" class="btn btn-sm {{ class }}" id="btn-box-{{id_box}}" onclick="printBox({{id_box}})">
  <i class="fa fa-print"></i> &nbsp; กล่องที่ {{ no }}&nbsp; : &nbsp;
  <span id="{{id_box}}">{{qty}}</span>&nbsp; Pcs.
</button>
{{/each}}
</script>
<!-- แสดงผลกล่อง  -->

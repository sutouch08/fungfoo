<div class="row">
  <div class="col-sm-12">
    <table class="table table-striped border-1 qc-table">
      <thead>
        <tr class="font-size-12">
          <th class="width-15 text-center">บาร์โค้ด</th>
          <th class="width-50">สินค้า</th>
          <th class="width-8 text-center">จำนวนที่สั่ง</th>
          <th class="width-8 text-center">จำนวนที่จัด</th>
          <th class="width-8 text-center">ตรวจแล้ว</th>
          <th class="text-right">จากโซน</th>
        </tr>
      </thead>
      <tbody id="incomplete-table">
<?php   $show_close = !empty($uncomplete_details) ? 'hide' : ''; ?>
<?php   $show_force = !empty($uncomplete_details) ? '' : 'hide'; ?>
<?php  if(!empty($uncomplete_details)) : ?>
<?php   foreach($uncomplete_details as $rs) : ?>
      <tr class="font-size-12 incomplete" id="row-<?php echo $rs->id; ?>">
        <td class="middle text-center td bc"><?php echo $rs->barcode; ?></td>
        <td class="middle td"><?php echo $rs->product_code.' : '.$rs->product_name; ?></td>
        <td class="middle text-center td"><?php echo number($rs->order_qty); ?></td>
        <td class="middle text-center td" id="prepared-<?php echo $rs->id; ?>"> <?php echo number($rs->prepared); ?></td>
        <td class="middle text-center td" id="qc-<?php echo $rs->id; ?>"><?php echo number($rs->qc); ?></td>
        <td class="middle text-right td">
          <button
            type="button"
            class="btn btn-default btn-xs btn-pop"
            data-container="body"
            data-toggle="popover"
            data-placement="left"
            data-trigger="focus"
            data-content="<?php echo $rs->from_zone; ?>"
            data-original-title=""
            title="">
            ที่เก็บ
          </button>
          <input type="hidden" class="hidden-qc" id="<?php echo $rs->id; ?>" value="0"/>
          <input type="hidden" id="id-<?php echo $rs->id; ?>" value="<?php echo $rs->id; ?>" />
        </td>
      </tr>

<?php   endforeach; ?>

<?php else : ?>
      <tr><td colspan="6" class="text-center"><h4>ไม่พบรายการ</td></tr>
<?php endif; ?>
        <tr>
          <td colspan="6" class="text-center">
            <div id="force-bar" class="<?php echo $show_force; ?>">
              <button type="button" class="btn btn-sm btn-danger not-show close-order" id="btn-force-close" onclick="forceClose()">
                บังคับจบ
              </button>
              <label style="margin-left:25px;">
                <input type="checkbox" class="close-order ace" style="margin-right:10px;" id="chk-force-close"  />
                <span class="lbl">  สินค้าไม่ครบ</span>
              </label>
            </div>
            <div class="<?php echo $show_close; ?>" id="close-bar">
              <button type="button" class="btn btn-sm btn-success close-order" id="btn-close" onclick="closeOrder()">
                ตรวจเสร็จแล้ว
              </button>
            </div>
          </td>
        </tr>

      </tbody>
    </table>
  </div>
</div>

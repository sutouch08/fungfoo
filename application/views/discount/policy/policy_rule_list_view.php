
<div class="row">
  <div class="col-sm-12">
    <table class="table table-striped table-bordered">
      <thead>
        <tr style="background-image:none;">
          <th rowspan="2" class="width-5 middle text-center">ลำดับ</th>
          <th rowspan="2" class="width-10 middle text-center">เลขที่</th>
          <th rowspan="2" class="width-22 middle text-center">ชื่อกฏ</th>
          <th rowspan="2" class="width-10 middle text-center">ส่วนลด</th>
          <th colspan="6" class="middle text-center">เงื่อนไข</th>
        </tr>
        <tr class="font-size-10" style="background-image:none;">
          <th class="width-8 text-center">ลูกค้า</th>
          <th class="width-8 text-center">สิ้นค้า</th>
          <th class="width-8 text-center">ช่องทาง</th>
          <th class="width-8 text-center">การชำระเงิน</th>
          <th class="width-8 text-center">ขั้นต่ำ</th>
          <th class="width-10 text-center"></th>
        </tr>
      </thead>
      <tbody>
<?php if(!empty($rules)) : ?>
  <?php $no = 1; ?>
  <?php foreach ($rules as $rs) : ?>
        <tr class="font-size-12" id="row_<?php echo $rs->id; ?>">
          <td class="middle text-center"><?php echo $no; ?></td>
          <td class="middle text-center"><?php echo $rs->code; ?></td>
          <td class="middle"><?php echo $rs->name; ?></td>
          <td class="middle text-center"><?php echo showItemDiscountLabel($rs->item_price, $rs->item_disc, $rs->item_disc_unit); ?></td>
          <td class="middle text-center"><?php echo ($rs->all_customer == 1 ? 'ทั้งหมด' : 'กำหนดค่า'); ?></td>
          <td class="middle text-center"><?php echo ($rs->all_product == 1 ? 'ทั้งหมด' : 'กำหนดค่า'); ?></td>
          <td class="middle text-center"><?php echo ($rs->all_channels == 1 ? 'ทั้งหมด' : 'กำหนดค่า'); ?></td>
          <td class="middle text-center"><?php echo ($rs->all_payment == 1 ? 'ทั้งหมด' : 'กำหนดค่า'); ?></td>
          <td class="middle text-center"><?php echo ($rs->qty > 0 ? $rs->qty.' pcs' : ($rs->amount > 0 ? $rs->amount.' '.getConfig('CURRENTCY') : 'No')); ?></td>
          <td class="middle text-right">
            <button type="button" class="btn btn-xs btn-info" onclick="viewRuleDetail('<?php echo $rs->id; ?>')"><i class="fa fa-eye"></i></button>
            <?php if($this->pm->can_edit) : ?>
            <button type="button" class="btn btn-xs btn-danger" onclick="unlinkRule(<?php echo $rs->id; ?>, '<?php echo $rs->code; ?>')"><i class="fa fa-trash"></i></button>
            <?php endif; ?>
          </td>
        </tr>
  <?php   $no++; ?>
  <?php endforeach; ?>

<?php else : ?>
      <tr>
        <td colspan="10" class="text-center">
          <h4>ไม่พบรายการ</h4>
        </td>
      </tr>

<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

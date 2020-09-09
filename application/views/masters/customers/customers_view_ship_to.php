<div class='row'>
  <div class="col-sm-12">
  <div class="table-responsive">
    <table class='table table-bordered' style="margin-bottom:0px;">
      <thead>
        <tr>
          <td colspan="6" align="center">ที่อยู่สำหรับจัดส่ง

          </td>
        </tr>
        <tr style="font-size:12px;">
          <td align="center" width="10%">ชื่อเรียก</td>
          <td width="12%">ผู้รับ</td>
          <td width="35%">ที่อยู่</td>
          <td width="15%">อีเมล์</td>
          <td width="15%">โทรศัพท์</td>
        </tr>
      </thead>
      <tbody id="adrs">
<?php if(!empty($addr)) : ?>
<?php 	foreach($addr as $rs) : ?>
  <?php $default = $rs->is_default == 1 ? 'color:green;' : ''; ?>
  <?php  $tumbon = !empty($rs->sub_district) ? ' ต.'.$rs->sub_district : ''; ?>
  <?php  $aumphor = !empty($rs->district) ? ' อ.'.$rs->district : ''; ?>
  <?php  $province = !empty($rs->province) ? ' จ.'.$rs->province : ''; ?>
  <?php  $postcode = !empty($rs->postcode) ? ' '.$rs->postcode : ''; ?>
        <tr style="font-size:12px; <?php echo $default; ?>" id="<?php echo $rs->id; ?>">
          <td align="center"><?php echo $rs->alias; ?></td>
          <td><?php echo $rs->name; ?></td>
          <td><?php echo $rs->address . $tumbon . $aumphor . $province . $postcode; ?></td>
          <td><?php echo $rs->email; ?></td>
          <td><?php echo $rs->phone; ?></td>
<?php 	endforeach; ?>
<?php else : ?>
        <tr><td colspan="5" align="center">ไม่พบที่อยู่</td></tr>
<?php endif; ?>
      </tbody>
    </table>
  </div>
  </div>
</div><!-- /row-->

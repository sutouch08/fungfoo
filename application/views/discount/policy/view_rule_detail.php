<?php
echo $this->printer->doc_header();
$currency = getConfig('CURRENTCY');
?>
<?php if(!$id_rule) : ?>
<?php    $sc .= "ERROR"; ?>
<?php else : ?>
<div class="container">
<div class="row">
  <div class="col-sm-12">
    <table class="table table-striped table-bordered">
      <tr class="">
        <td class="width-15 middle text-right"><strong>รหัสกฎ</strong></td>
        <td class="width-20 middle"><?php echo $rule->code; ?></td>
        <td class="width-15 middle text-right"><strong>ชื่อกฏ</strong></td>
        <td class="width-50 middle" ><?php echo $rule->name; ?></td>
      </tr>
      <tr>
        <td class="middle text-right"><strong>รหัสนโยบาย</strong></td>
        <td class="middle"><?php echo (empty($policy->code) ? '':$policy->code); ?></td>
        <td class="middle text-right"><strong>ชื่อนโยบาย</strong></td>
        <td class="middle" ><?php echo (empty($policy->name) ? '' : $policy->name); ?></td>
      </tr>
      <tr class="">
        <td class="middle text-right"><strong>วันที่เพิ่ม</strong></td>
        <td class="middle"><?php echo thai_date($rule->date_add); ?></td>
        <td class="middle text-right"><strong>พนักงาน</strong></td>
        <td class="middle" ><?php echo $this->user_model->get_name($rule->user); ?></td>
      </tr>
      <tr>
        <td class="middle text-right"><strong>วันที่ปรับปรุง</strong></td>
        <td class="middle"><?php echo thai_date($rule->date_upd); ?></td>
        <td class="middle text-right"><strong>พนักงาน</strong></td>
        <td class="middle" ><?php echo $this->user_model->get_name($rule->update_user); ?></td>
      </tr>
      <tr class="">
        <td class="middle text-right"><strong>ส่วนลด</strong></td>
        <td class="middle">
          <?php echo $rule->item_disc; ?>
          <?php echo ($rule->item_disc_unit == 'amount' ? $currency : '%'); ?>
        </td>
        <td class="middle text-right"><strong>กำหนดราคา</strong></td>
        <td class="middle"><?php echo ($rule->item_price > 0 ? number($rule->item_price, 2).' '.$currency : 'No'); ?></td>
      </tr>
      <tr>
        <td class="middle text-right"><strong>จำนวนขั้นต่ำ</strong></td>
        <td class="middle"><?php echo ($rule->qty > 0 ? number($rule->qty) : 'No'); ?></td>
        <td class="middle text-right"><strong>มูลค่าขั้นต่ำ</strong></td>
        <td class="middle"><?php echo ($rule->amount > 0 ? number($rule->amount, 2).' '.$currency : 'No'); ?></td>
      </tr>
      <tr>
        <td class="middle text-right"><strong>รวมยอดได้</strong></td>
        <td class="middle"><?php echo $rule->canGroup == 1 ? 'Yes' : 'No'; ?></td>
      </tr>

      <tr>
        <td colspan="4" class="text-center"><strong>ลูกค้า</strong></td>
      </tr>
      <?php if($rule->all_customer == 1) : ?>
      <tr class="">
        <td class="middle text-right"><strong>ลูกค้า</strong></td>
        <td colspan="3"><?php echo 'ทั้งหมด'; ?></td>
      </tr>
      <?php endif; ?>
      <!------------ รายชื่อลูกค้าแบบกำหนดรายบุคคล --------->
      <?php if($rule->all_customer == 0) : ?>
      <?php   $qs = $this->discount_rule_model->getCustomerRuleList($id_rule); ?>
      <?php   if($qs->num_rows() > 0) : ?>
        <tr class="">
          <td class="middle text-right"><strong>รายชื่อลูกค้า</strong></td>
          <td class="middle" colspan="3">
          <?php $i = 1; ?>
        <?php   foreach($qs->result() as $rs) : ?>
          <?php echo $i == 1 ? $rs->code.' : '.$rs->name : ', '.$rs->code.' : '.$rs->name; ?>
          <?php $i++; ?>
        <?php endforeach; ?>
          </td>
        </tr>
        <?php endif; ?>
      <!----------- จบรายชื่อลูกค้า  ------------>
      <!---------- กลุ่มลูกค้า ----------->
      <?php   $qs = $this->discount_rule_model->getCustomerGroupRule($id_rule); ?>
      <?php   if($qs->num_rows() > 0) : ?>
        <tr class="">
          <td class="middle text-right"><strong>กลุ่มลูกค้า</strong></td>
          <td class="middle" colspan="3">
          <?php $i = 1; ?>
        <?php   foreach($qs->result() as $rs) : ?>
          <?php echo $i == 1 ? $rs->name : ', '.$rs->name; ?>
          <?php $i++; ?>
        <?php endforeach; ?>
          </td>
        </tr>
        <?php endif; ?>

      <!---------- จบกลุ่มลูกค้า ----------->
      <!---------- ชนิดลูกค้า ----------->
      <?php   $qs = $this->discount_rule_model->getCustomerTypeRule($id_rule); ?>
      <?php   if($qs->num_rows() > 0) : ?>
        <tr class="">
          <td class="middle text-right"><strong>ชนิดลูกค้า</strong></td>
          <td class="middle" colspan="3">
          <?php $i = 1; ?>
        <?php   foreach($qs->result() as $rs) : ?>
          <?php echo $i == 1 ? $rs->name : ', '.$rs->name; ?>
          <?php $i++; ?>
        <?php endforeach; ?>
          </td>
        </tr>
        <?php endif; ?>
      <!---------- จบชนิดลูกค้า ----------->
      <!---------- ประเภทลูกค้า ----------->
      <?php   $qs = $this->discount_rule_model->getCustomerKindRule($id_rule); ?>
      <?php   if($qs->num_rows() > 0) : ?>
        <tr class="">
          <td class="middle text-right"><strong>ประเภทลูกค้า</strong></td>
          <td class="middle" colspan="3">
          <?php $i = 1; ?>
        <?php   foreach($qs->result() as $rs) : ?>
          <?php echo $i == 1 ? $rs->name : ', '.$rs->name; ?>
          <?php $i++; ?>
        <?php endforeach; ?>
          </td>
        </tr>
        <?php endif; ?>
      <!---------- จบประเภทลูกค้า ----------->
      <!---------- เขตลูกค้า ----------->
      <?php   $qs = $this->discount_rule_model->getCustomerAreaRule($id_rule); ?>
      <?php   if($qs->num_rows() > 0) : ?>
        <tr class="">
          <td class="middle text-right"><strong>เขตลูกค้า</strong></td>
          <td class="middle" colspan="3">
          <?php $i = 1; ?>
        <?php   foreach($qs->result() as $rs) : ?>
          <?php echo $i == 1 ? $rs->name : ', '.$rs->name; ?>
          <?php $i++; ?>
        <?php endforeach; ?>
          </td>
        </tr>
        <?php endif; ?>
      <!---------- จบเชตลูกค้า ----------->
      <!---------- เกรดลูกค้า ----------->
      <?php   $qs = $this->discount_rule_model->getCustomerClassRule($id_rule); ?>
      <?php   if($qs->num_rows() > 0) : ?>
        <tr class="">
          <td class="middle text-right"><strong>เกรดลูกค้า</strong></td>
          <td class="middle" colspan="3">
          <?php $i = 1; ?>
        <?php   foreach($qs->result() as $rs) : ?>
          <?php echo $i == 1 ? $rs->name : ', '.$rs->name; ?>
          <?php $i++; ?>
        <?php endforeach; ?>
          </td>
        </tr>
        <?php endif; ?>
      <!---------- จบเกรดลูกค้า ----------->
      <?php endif; ?>
      <tr>
        <td colspan="4" class="text-center"><strong>สินค้า</strong></td>
      </tr>
      <?php if($rule->all_product == 1) : ?>
      <tr class="">
        <td class="middle text-right"><strong>สิ้นค้าทั้งหมด</strong></td>
        <td colspan="3"><?php echo 'Yes'; ?></td>
      </tr>
      <?php endif; ?>

      <!------------ ถ้าไม่ได้เลือกสินค้าทั้งหมด แต่เลือกเป็นรุ่น --------->
      <?php if($rule->all_product == 0) : ?>

        <?php   $qs = $this->discount_rule_model->getProductItemRule($id_rule); ?>
        <?php   if($qs->num_rows() > 0) : ?>
          <tr class="">
            <td class="middle text-right"><strong>สินค้า</strong></td>
            <td class="middle" colspan="3" style="white-space:normal;">
            <?php $i = 1; ?>
          <?php   foreach($qs->result() as $rs) : ?>
            <?php echo $i == 1 ? $rs->code : ', '.$rs->code; ?>
            <?php $i++; ?>
          <?php endforeach; ?>
            </td>
          </tr>
          <?php endif; ?>
        <!----------- จบรายการสินค้า  ------------>

      <?php   $qs = $this->discount_rule_model->getProductStyleRule($id_rule); ?>
      <?php   if($qs->num_rows() > 0) : ?>
        <tr class="">
          <td class="middle text-right"><strong>รุ่นสินค้า</strong></td>
          <td class="middle" colspan="3">
          <?php $i = 1; ?>
        <?php   foreach($qs->result() as $rs) : ?>
          <?php echo $i == 1 ? $rs->code : ', '.$rs->code; ?>
          <?php $i++; ?>
        <?php endforeach; ?>
          </td>
        </tr>
        <?php endif; ?>
      <!----------- จบรุ่นสินค้า  ------------>

      <!---------- กลุ่มสินค้า ----------->
      <?php   $qs = $this->discount_rule_model->getProductGroupRule($id_rule); ?>
      <?php   if($qs->num_rows() > 0) : ?>
        <tr class="">
          <td class="middle text-right"><strong>กลุ่มสินค้า</strong></td>
          <td class="middle" colspan="3">
          <?php $i = 1; ?>
        <?php   foreach($qs->result() as $rs) : ?>
          <?php echo $i == 1 ? $rs->name : ', '.$rs->name; ?>
          <?php $i++; ?>
        <?php endforeach; ?>
          </td>
        </tr>
        <?php endif; ?>
      <!---------- จบกลุ่มสินค้า ----------->
      <!---------- กลุ่มย่อยสินค้า ----------->
      <?php   $qs = $this->discount_rule_model->getProductSubGroupRule($id_rule); ?>
      <?php   if($qs->num_rows() > 0) : ?>
        <tr class="">
          <td class="middle text-right"><strong>กลุ่มย่อยสินค้า</strong></td>
          <td class="middle" colspan="3">
          <?php $i = 1; ?>
        <?php   foreach($qs->result() as $rs) : ?>
          <?php echo $i == 1 ? $rs->name : ', '.$rs->name; ?>
          <?php $i++; ?>
        <?php endforeach; ?>
          </td>
        </tr>
        <?php endif; ?>
      <!---------- จบกลุ่มย่อยสินค้า ----------->
      <!---------- ชนิดสินค้า ----------->
      <?php   $qs = $this->discount_rule_model->getProductTypeRule($id_rule); ?>
      <?php   if($qs->num_rows() > 0) : ?>
        <tr class="">
          <td class="middle text-right"><strong>ชนิดสินค้า</strong></td>
          <td class="middle" colspan="3">
          <?php $i = 1; ?>
        <?php   foreach($qs->result() as $rs) : ?>
          <?php echo $i == 1 ? $rs->name : ', '.$rs->name; ?>
          <?php $i++; ?>
        <?php endforeach; ?>
          </td>
        </tr>
        <?php endif; ?>
      <!---------- จบชนิดสินค้า ----------->
      <!---------- ประเภทสินค้า ----------->
      <?php   $qs = $this->discount_rule_model->getProductKindRule($id_rule); ?>
      <?php   if($qs->num_rows() > 0) : ?>
        <tr class="">
          <td class="middle text-right"><strong>ประเภทสินค้า</strong></td>
          <td class="middle" colspan="3">
          <?php $i = 1; ?>
        <?php   foreach($qs->result() as $rs) : ?>
          <?php echo $i == 1 ? $rs->name : ', '.$rs->name; ?>
          <?php $i++; ?>
        <?php endforeach; ?>
          </td>
        </tr>
        <?php endif; ?>
      <!---------- จบประเภทสินค้า ----------->
      <!---------- หมวดหมู่สินค้า ----------->
      <?php   $qs = $this->discount_rule_model->getProductCategoryRule($id_rule); ?>
      <?php   if($qs->num_rows() > 0) : ?>
        <tr class="">
          <td class="middle text-right"><strong>หมวดหมู่สินค้า</strong></td>
          <td class="middle" colspan="3">
          <?php $i = 1; ?>
        <?php   foreach($qs->result() as $rs) : ?>
          <?php echo $i == 1 ? $rs->name : ', '.$rs->name; ?>
          <?php $i++; ?>
        <?php endforeach; ?>
          </td>
        </tr>
        <?php endif; ?>
      <!---------- จบหมวดหมู่สินค้า ----------->
      <!---------- ยี่ห้อสินค้า ----------->
      <?php   $qs = $this->discount_rule_model->getProductBrandRule($id_rule); ?>
      <?php   if($qs->num_rows() > 0) : ?>
        <tr class="">
          <td class="middle text-right"><strong>ยี่ห้อสินค้า</strong></td>
          <td class="middle" colspan="3">
          <?php $i = 1; ?>
        <?php   foreach($qs->result() as $rs) : ?>
          <?php echo $i == 1 ? $rs->name : ', '.$rs->name; ?>
          <?php $i++; ?>
        <?php endforeach; ?>
          </td>
        </tr>
        <?php endif; ?>
      <!---------- จบยี่ห้อสินค้า ----------->
      <!---------- ปีสินค้า ----------->
      <?php   $qs = $this->discount_rule_model->getProductYearRule($id_rule); ?>
      <?php   if($qs->num_rows() > 0) : ?>
        <tr class="">
          <td class="middle text-right"><strong>ปีสินค้า</strong></td>
          <td class="middle" colspan="3">
          <?php $i = 1; ?>
        <?php   foreach($qs->result() as $rs) : ?>
          <?php echo $i == 1 ? $rs->year : ', '.$rs->year; ?>
          <?php $i++; ?>
        <?php endforeach; ?>
          </td>
        </tr>
        <?php endif; ?>
      <!---------- จบปีสินค้า ----------->

    <?php endif; ?>

    <tr>
      <td colspan="4" class="text-center"><strong>ช่องทางการขายและการชำระเงิน</strong></td>
    </tr>
    <tr class="">
      <td class="middle text-right"><strong>ช่องทางขาย</strong></td>
      <td colspan="3">
        <?php if($rule->all_channels == 1) : ?>
            ทั้งหมด
        <?php else : ?>
          <?php $qs = $this->discount_rule_model->getChannelsRule($id_rule); ?>
          <?php if($qs->num_rows() > 0) : ?>
            <?php $i = 1; ?>
            <?php foreach($qs->result() as $rs) : ?>
              <?php echo $i == 1 ? $rs->name : ', '.$rs->name; ?>
            <?php endforeach; ?>
          <?php endif; ?>

        <?php endif; ?>
      </td>
    </tr>
    <tr class="">
      <td class="middle text-right"><strong>การชำระเงิน</strong></td>
      <td colspan="3">
        <?php if($rule->all_payment == 1) : ?>
            ทั้งหมด
        <?php else : ?>
          <?php $qs = $this->discount_rule_model->getPaymentRule($id_rule); ?>
          <?php if($qs->num_rows() > 0) : ?>
            <?php $i = 1; ?>
            <?php foreach($qs->result() as $rs) : ?>
              <?php echo $i == 1 ? $rs->name : ', '.$rs->name; ?>
            <?php endforeach; ?>
          <?php endif; ?>

        <?php endif; ?>
      </td>
    </tr>

    </table>
  </div>
</div>
</div>
<?php endif; ?>
<?php
echo $this->printer->doc_footer();
 ?>

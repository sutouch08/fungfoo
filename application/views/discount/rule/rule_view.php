<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-sm-6 top-col">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
  <div class="col-sm-6">
    <p class="pull-right top-p">
<?php if( $this->pm->can_add ) : ?>
      <button type="button" class="btn btn-sm btn-success" onclick="goAdd()"><i class="fa fa-plus"></i> เพิ่มใหม่</button>
<?php endif; ?>
    </p>
  </div>
</div>
<hr/>

<form id="searchForm" method="post" >
<div class="row">
  <div class="col-sm-2 padding-5 first">
    <label>เลขที่</label>
    <input type="text" class="form-control input-sm text-center search-box" name="rule_code" id="rule_code" value="<?php echo $code; ?>" autofocus />
  </div>
  <div class="col-sm-4 padding-5">
    <label>ชื่อเงื่อนไข</label>
    <input type="text" class="form-control input-sm text-center search-box" name="rule_name" id="rule_name" value="<?php echo $name; ?>" />
  </div>

  <div class="col-sm-2 padding-5">
    <label>รหัส/ชื่อ นโยบาย</label>
    <input type="text" class="form-control input-sm text-center search-box" name="policy" id="policy" value="<?php echo $policy; ?>" />
  </div>
  <div class="col-sm-1 padding-5">
    <label>ส่วนลด</label>
    <input type="number" class="form-control input-sm text-center search-box" name="rule_disc" id="rule_disc" value="<?php echo $discount; ?>">
  </div>

  <div class="col-sm-1 padding-5">
    <label>สถานะ</label>
    <select class="form-control input-sm" name="active" id="active" onchange="getSearch()">
      <option value="2" <?php echo is_selected(2, $active); ?>>ทั้งหมด</option>
      <option value="1" <?php echo is_selected(1, $active); ?>>ใช้งาน</option>
      <option value="0" <?php echo is_selected(0, $active); ?>>ไม่ใช้งาน</option>
    </select>
  </div>


  <div class="col-sm-1 padding-5">
    <label class="display-block not-show">search</label>
    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="getSearch()"><i class="fa fa-search"></i> ค้นหา</button>
  </div>
  <div class="col-sm-1 padding-5 last">
    <label class="display-block not-show">reset</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
</form>

<hr class="margin-top-10 margin-bottom-10"/>

 <div class="row">
   <div class="col-sm-12">
     <table class="table table-striped border-1">
       <thead>
         <tr>
           <th class="width-5 text-center">ลำดับ</th>
           <th class="width-15 text-center">เลขที่เงื่อนไข</th>
           <th class="width-35">เงื่อนไข</th>
           <th class="width-15 text-center">เลขที่นโยบาย</th>
           <th class="width-10 text-center">ส่วนลด</th>
           <th class="width-5 text-center">สถานะ</th>
           <th class=""></th>
         </tr>
       </thead>
       <tbody>
<?php if(!empty($rules)) : ?>
  <?php $no = 1; ?>
  <?php foreach($rules as $rs) : ?>
        <tr class="font-size-12" id="row-<?php echo $rs->id; ?>">
          <td class="middle text-center no"><?php echo number($no); ?></td>
          <td class="middle text-center"><?php echo $rs->code; ?></td>
          <td class="middle"><?php echo $rs->name; ?></td>
          <td class="middle text-center"><?php echo $this->discount_policy_model->get_code($rs->id_policy); ?></td>
          <td class="middle text-center"><?php echo showItemDiscountLabel($rs->item_price, $rs->item_disc, $rs->item_disc_unit); ?></td>
          <td class="middle text-center"><?php echo is_active($rs->active); ?></td>
          <td class="middle text-right">
            <button type="button" class="btn btn-xs btn-info" onclick="viewDetail('<?php echo $rs->id; ?>')"><i class="fa fa-eye"></i></button>
      <?php if($this->pm->can_edit) : ?>
            <button type="button" class="btn btn-xs btn-warning" onclick="goEdit('<?php echo $rs->id; ?>')"><i class="fa fa-pencil"></i></button>
      <?php endif; ?>
      <?php if($this->pm->can_delete) : ?>
            <button type="button" class="btn btn-xs btn-danger" onclick="getDelete('<?php echo $rs->id; ?>', '<?php echo $rs->code; ?>')"><i class="fa fa-trash"></i></button>
      <?php endif; ?>

          </td>
        </tr>
    <?php $no++; ?>
  <?php endforeach; ?>

<?php else : ?>
        <tr>
          <td colspan="7" class="text-center">
            <h4>ไม่พบรายการ</h4>
          </td>
        </tr>
<?php endif; ?>
       </tbody>
     </table>
   </div>
 </div>

<script src="<?php echo base_url(); ?>scripts/discount/rule/rule.js"></script>
<script src="<?php echo base_url(); ?>scripts/discount/rule/rule_list.js"></script>
<?php $this->load->view('include/footer'); ?>

<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6">
    	<p class="pull-right top-p">

      </p>
    </div>
</div><!-- End Row -->
<hr class=""/>

<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-sm-2 padding-5 first">
    <label><?php label('item_code'); ?></label>
    <input type="text" class="form-control input-sm search" name="pd_code"  value="<?php echo $pd_code; ?>" />
  </div>

  <div class="col-sm-2 padding-5">
    <label><?php label('zone'); ?></label>
    <input type="text" class="form-control input-sm search" name="zone_code" value="<?php echo $zone_code; ?>" />
  </div>

  <div class="col-sm-1 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> <?php label('search'); ?></button>
  </div>
	<div class="col-sm-1 padding-5 last">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> <?php label('reset'); ?></button>
  </div>
</div>
<hr class="margin-top-15">
</form>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
  <div class="col-sm-12">
    <table class="table table-striped table-bordered">
      <tr>
        <th class="width-5 text-center">ลำดับ</th>
        <th class="width-20 text-center">สินค้า</th>
    		<th class="width-40">โซน</th>
        <th class="width-8 text-center">คงเหลือ</th>
        <th class="width-15 text-center">ปรับปรุง</th>
      </tr>
      <tbody>
    <?php if( !empty($data)) : ?>
    <?php $no = $this->uri->segment(4) + 1; ?>
    <?php foreach($data as $rs) : ?>
      <tr class="font-size-12">
        <td class="text-center"><?php echo $no; ?></td>
        <td><?php echo $rs->product_code; ?></td>
    		<td class=""><?php echo $rs->name; ?></td>
        <td class="text-center"> <?php echo number($rs->qty); ?></td>
    		<td class="text-center"><?php echo thai_date($rs->date_upd, TRUE); ?></td>
      </tr>
    <?php  $no++; ?>
    <?php endforeach; ?>
    <?php else : ?>
      <tr>
        <td colspan="5" class="text-center">--- ไม่พบข้อมูล ---</td>
      </tr>
    <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<script src="<?php echo base_url(); ?>scripts/inventory/stock/stock.js"></script>

<?php $this->load->view('include/footer'); ?>

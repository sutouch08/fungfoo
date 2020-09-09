<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6">
    	<p class="pull-right top-p">
      <?php if($this->pm->can_add) : ?>
        <button type="button" class="btn btn-sm btn-success" onclick="goAdd()"><i class="fa fa-plus"></i> <?php label('add_new'); ?></button>
      <?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr class=""/>

<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-sm-2 padding-5 first">
    <label><?php label('doc_num'); ?></label>
    <input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
  </div>

  <div class="col-sm-2 padding-5">
    <label><?php label('po'); ?></label>
    <input type="text" class="form-control input-sm search" name="po" value="<?php echo $po; ?>" />
  </div>

	<div class="col-sm-2 padding-5">
    <label><?php label('inv'); ?></label>
    <input type="text" class="form-control input-sm search" name="invoice" value="<?php echo $invoice; ?>" />
  </div>

	<div class="col-sm-2 padding-5">
    <label><?php label('vender'); ?></label>
		<input type="text" class="form-control input-sm search" name="venderx" value="<?php echo $vender; ?>" />
  </div>

	<div class="col-sm-2 padding-5">
    <label><?php label('date'); ?></label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
    </div>

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
    <p class="pull-right">
      <?php label('status'); ?> : <?php label('empty'); ?> = <?php label('normal'); ?>, &nbsp;
      <span class="red">CN</span> = <?php label('cancle'); ?>, &nbsp;
      <span class="blue">NC</span> = <?php label('not_save'); ?>
    </p>
  </div>
	<div class="col-sm-12">
		<table class="table table-striped table-hover border-1">
			<thead>
				<tr>
					<th class="width-5 middle text-center"><?php label('Num'); ?></th>
					<th class="width-10 middle text-center"><?php label('date'); ?></th>
					<th class="width-10 middle"><?php label('doc_num'); ?></th>
					<th class="width-10 middle"><?php label('inv'); ?></th>
					<th class="width-10 middle"><?php label('po'); ?></th>
					<th class="width-20 middle"><?php label('vender'); ?></th>
					<th class="width-10 middle text-center"><?php label('qty'); ?></th>
					<th class="width-10 middle text-center"><?php label('status'); ?></th>
          <th></th>
				</tr>
			</thead>
			<tbody>
        <?php if(!empty($document)) : ?>
          <?php $no = $this->uri->segment(4) + 1; ?>
          <?php foreach($document as $rs) : ?>
            <tr id="row-<?php echo $rs->code; ?>" style="font-size:12px;">
              <td class="middle text-center"><?php echo $no; ?></td>
              <td class="middle text-center"><?php echo thai_date($rs->date_add, FALSE, '/'); ?></td>
              <td class="middle"><?php echo $rs->code; ?></td>
              <td class="middle"><?php echo $rs->invoice_code; ?></td>
              <td class="middle"><?php echo $rs->po_code; ?></td>
              <td class="middle"><?php echo $rs->vender_name; ?></td>
              <td class="middle text-center"><?php echo $rs->qty; ?></td>
              <td class="middle text-center">
                <?php if($rs->status == 0 ) : ?>
                  <span class="blue"><strong>NC</strong></span>
                <?php endif; ?>
                <?php if($rs->status == 2) : ?>
                <span class="red"><strong>CN</strong></span>
                <?php endif; ?>
              </td>
              <td class="middle text-right">
                <button type="button" class="btn btn-mini btn-info" onclick="viewDetail('<?php echo $rs->code; ?>')"><i class="fa fa-eye"></i></button>
                <?php if(($this->pm->can_edit OR $this->pm->can_add) && $rs->status == 0) : ?>
                  <button type="button" class="btn btn-mini btn-warning" onclick="goEdit('<?php echo $rs->code; ?>')"><i class="fa fa-pencil"></i></button>
                <?php endif; ?>
                <?php if($this->pm->can_delete && $rs->status != 2) : ?>
                  <button type="button" class="btn btn-mini btn-danger" onclick="goDelete('<?php echo $rs->code; ?>')"><i class="fa fa-trash"></i></button>
                <?php endif; ?>
              </td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po.js"></script>

<?php $this->load->view('include/footer'); ?>

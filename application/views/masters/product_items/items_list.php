<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    <h3 class="title"><?php echo $this->title; ?></h3>
    </div>
    <div class="col-sm-6">
    	<p class="pull-right top-p">
      <?php if($this->pm->can_add) : ?>
        <button type="button" class="btn btn-sm btn-success" onclick="addNew()"><i class="fa fa-plus"></i> <?php label('add_new'); ?></button>
      <?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr class=""/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-sm-2 padding-5 first">
    <label><?php label('code'); ?></label>
    <input type="text" class="width-100" name="code" id="code" value="<?php echo $code; ?>" />
  </div>

  <div class="col-sm-2 padding-5">
    <label><?php label('name'); ?></label>
    <input type="text" class="width-100" name="name" id="name" value="<?php echo $name; ?>" />
  </div>

	<div class="col-sm-2 padding-5">
    <label><?php label('group'); ?></label>
    <select class="form-control" name="group" onchange="getSearch()">
			<option value=""><?php label('all'); ?></option>
			<?php echo select_product_group($group); ?>
		</select>
  </div>

	<div class="col-sm-2 padding-5">
    <label><?php label('sub_group'); ?></label>
		<select class="form-control" name="sub_group" onchange="getSearch()">
			<option value=""><?php label('all'); ?></option>
			<?php echo select_product_sub_group($sub_group); ?>
		</select>
  </div>

	<div class="col-sm-2 padding-5">
    <label><?php label('category'); ?></label>
		<select class="form-control" name="category" onchange="getSearch()">
			<option value=""><?php label('all'); ?></option>
			<?php echo select_product_category($category); ?>
		</select>
  </div>

	<div class="col-sm-2 padding-5 last">
    <label><?php label('kind'); ?></label>
		<select class="form-control" name="kind" onchange="getSearch()">
			<option value=""><?php label('all'); ?></option>
			<?php echo select_product_kind($kind); ?>
		</select>
  </div>

	<div class="col-sm-2 padding-5 first">
    <label><?php label('type'); ?></label>
		<select class="form-control" name="type" onchange="getSearch()">
			<option value=""><?php label('all'); ?></option>
			<?php echo select_product_type($type); ?>
		</select>
  </div>

	<div class="col-sm-2 padding-5">
    <label><?php label('brand'); ?></label>
		<select class="form-control" name="brand" onchange="getSearch()">
			<option value=""><?php label('all'); ?></option>
			<?php echo select_product_brand($brand); ?>
		</select>
  </div>

	<div class="col-sm-2 padding-5">
    <label><?php label('year'); ?></label>
		<select class="form-control" name="year" onchange="getSearch()">
			<option value=""><?php label('all'); ?></option>
			<?php echo select_years($year); ?>
		</select>
  </div>

  <div class="col-sm-2 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-sm btn-primary btn-block"><i class="fa fa-search"></i> <?php label('search'); ?></button>
  </div>
	<div class="col-sm-2 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-sm btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
<hr class="margin-top-15">
</form>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th class="width-5 middle text-center"><?php label('Num'); ?></th>
					<th class="width-20 middle"><?php label('code'); ?></th>
					<th class="width-10 middle"><?php label('price'); ?></th>
					<th class="width-10 middle"><?php label('group'); ?></th>
					<th class="width-10 middle"><?php label('category'); ?></th>
					<th class="width-10 middle"><?php label('kind'); ?></th>
					<th class="width-10 middle"><?php label('type'); ?></th>
					<th class="width-5 middle text-center"><?php label('year'); ?></th>
					<th class="width-5 middle text-center"><?php label('sell'); ?></th>
					<th class="width-5 middle text-center"><?php label('active'); ?></th>
					<th class="width-5 middle text-center">API</th>
					<th class=""></th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(4) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr id="row-<?php echo $rs->code; ?>">
						<td class="middle text-center"><?php echo $no; ?></td>
						<td class="middle"><?php echo $rs->code; ?></td>
						<td class="middle"><?php echo number($rs->price, 2); ?></td>
						<td class="middle"><?php echo $rs->group; ?></td>
						<td class="middle"><?php echo $rs->category; ?></td>
						<td class="middle"><?php echo $rs->kind; ?></td>
						<td class="middle"><?php echo $rs->type; ?></td>
						<td class="middle text-center"><?php echo $rs->year; ?></td>
						<td class="middle text-center"><?php echo is_active($rs->sell); ?></td>
						<td class="middle text-center"><?php echo is_active($rs->active); ?></td>
						<td class="middle text-center"><?php echo is_active($rs->api); ?></td>
						<td class="middle text-right">
							<?php if($this->pm->can_add) : ?>
								<button type="button" class="btn btn-mini btn-primary" onclick="duplicate('<?php echo $rs->code; ?>')">
									<i class="fa fa-copy"></i>
								</button>
							<?php endif; ?>
							<?php if($this->pm->can_edit) : ?>
								<button type="button" class="btn btn-mini btn-warning" onclick="getEdit('<?php echo $rs->code; ?>')">
									<i class="fa fa-pencil"></i>
								</button>
							<?php endif; ?>
							<?php if($this->pm->can_delete) : ?>
								<button type="button" class="btn btn-mini btn-danger" onclick="getDelete('<?php echo $rs->code; ?>', '<?php echo $rs->name; ?>')">
									<i class="fa fa-trash"></i>
								</button>
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

<script src="<?php echo base_url(); ?>scripts/masters/items.js"></script>

<?php $this->load->view('include/footer'); ?>

<?php $this->load->view('include/header'); ?>
<div class="col-sm-8 ">
<div class="row">
	<div class="col-sm-6">
    <h3 class="title">
      <i class="fa fa-lock"></i> <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6">
			<p class="pull-right">
				<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i>  กลับ</button>
			<?php if($this->permission === TRUE) : ?>
						<button type="button" class="btn btn-sm btn-success" onclick="savePermission()"><i class="fa fa-save"></i>  บันทึก</button>
			<?php endif; ?>
			</p>
		</div>
</div><!-- End Row -->
<hr class="title-block"/>
<div class="row">
	<div class="col-sm-12">
		<form id="permissionForm" method="post" action="<?php echo $this->home; ?>/save_profile_permission">
			<input type="hidden" name="id_profile" value="<?php echo $data->id; ?>" />
		<table class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th class="width-30"></th>
					<th class="width-5 text-center"><?php label('view'); ?></th>
					<th class="width-5 text-center"><?php label('add'); ?></th>
					<th class="width-5 text-center"><?php label('edit'); ?></th>
					<th class="width-5 text-center"><?php label('delete'); ?></th>
					<th class="width-5 text-center"><?php label('approve'); ?></th>
					<th class="width-5 text-center"><?php label('all'); ?></th>

				</tr>
			</thead>
			<tbody>
<?php if(!empty($menus)) : ?>
	<?php foreach($menus as $groups) : ?>
	<?php 	$g_code = $groups['group_code']; ?>
				<tr class="font-size-14" style="background-color:#428bca73;">
					<td class="middle"><?php echo $groups['group_name']; ?></td>
					<td class="middle text-center">
						<input id="view-group-<?php echo $g_code; ?>" type="checkbox" class="ace" onchange="groupViewCheck($(this), '<?php echo $g_code; ?>')" />
						<span class="lbl"></span>
					</td>
					<td class="middle text-center">
						<input id="add-group-<?php echo $g_code; ?>" type="checkbox" class="ace" onchange="groupAddCheck($(this), '<?php echo $g_code; ?>' )">
						<span class="lbl"></span>
					</td>
					<td class="middle text-center">
						<input id="edit-group-<?php echo $g_code; ?>" type="checkbox" class="ace" onchange="groupEditCheck($(this), '<?php echo $g_code; ?>' )">
						<span class="lbl"></span>
					</td>
					<td class="middle text-center">
						<input id="delete-group-<?php echo $g_code; ?>" type="checkbox" class="ace" onchange="groupDeleteCheck($(this), '<?php echo $g_code; ?>' )">
						<span class="lbl"></span>
					</td>
					<td class="middle text-center">
						<input id="approve-group-<?php echo $g_code; ?>" type="checkbox" class="ace" onchange="groupApproveCheck($(this), '<?php echo $g_code; ?>' )">
						<span class="lbl"></span>
					</td>
					<td class="middle text-center">
						<input id="all-group-<?php echo $g_code; ?>" type="checkbox" class="ace" onchange="groupAllCheck($(this), '<?php echo $g_code; ?>' )">
						<span class="lbl"></span>
					</td>

				</tr>

				<?php if(!empty($groups['menu'])) : ?>
					<?php foreach($groups['menu'] as $menu) : ?>
						<?php $code = $menu['menu_code']; ?>
						<?php $pm = $menu['permission']; ?>
						<tr>
							<td class="middle" style="padding-left:20px;"> -
								<?php echo $menu['menu_name']; ?>
								<input type="hidden" name="menu[<?php echo $code; ?>]" value="<?php echo $code; ?>"  />
							</td>
							<td class="middle text-center">
								<input id="view-<?php echo $code; ?>" name="view[<?php echo $code; ?>]" type="checkbox" class="ace view-<?php echo $g_code.' '.$code; ?>" <?php echo is_checked($pm->can_view, 1); ?> value="1">
								<span class="lbl"></span>
							</td>
							<td class="middle text-center">
								<input id="add-<?php echo $code; ?>" name="add[<?php echo $code; ?>]" type="checkbox" class="ace add-<?php echo $g_code.' '.$code; ?>" <?php echo is_checked($pm->can_add, 1); ?> value="1">
								<span class="lbl"></span>
							</td>
							<td class="middle text-center">
								<input id="edit-<?php echo $code; ?>" name="edit[<?php echo $code; ?>]" type="checkbox" class="ace edit-<?php echo $g_code.' '.$code; ?>" <?php echo is_checked($pm->can_edit, 1); ?> value="1">
								<span class="lbl"></span>
							</td>
							<td class="middle text-center">
								<input id="delete-<?php echo $code; ?>" name="delete[<?php echo $code; ?>]" type="checkbox" class="ace delete-<?php echo $g_code.' '.$code; ?>" <?php echo is_checked($pm->can_delete, 1); ?> value="1">
								<span class="lbl"></span>
							</td>
							<td class="middle text-center">
								<input id="approve-<?php echo $code; ?>" name="approve[<?php echo $code; ?>]" type="checkbox" class="ace approve-<?php echo $g_code.' '.$code; ?>" <?php echo is_checked($pm->can_approve, 1); ?> value="1">
								<span class="lbl"></span>
							</td>
							<td class="middle text-center">
								<input id="all-<?php echo $code; ?>" type="checkbox" class="ace all all-<?php echo $g_code; ?>" onchange="allCheck($(this), '<?php echo $code; ?>')">
								<span class="lbl"></span>
							</td>

						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			<?php endforeach; ?>
<?php endif; ?>
			</tbody>
		</table>
		<p class="pull-right">
			<button type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i> บันทึก</button>
		</p>
	</form>
	</div>
</div>
</div>

<script src="<?php echo base_url(); ?>scripts/users/permission.js"></script>

<?php $this->load->view('include/footer'); ?>

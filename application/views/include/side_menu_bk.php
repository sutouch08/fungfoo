<?php
$menu_sub_group_code = isset($this->menu_sub_group_code) ? $this->menu_sub_group_code : NULL;
$menu = $this->menu_code;
$menu_group = $this->menu_group_code;
?>
<!--   Side menu Start --->
<ul class="nav nav-list">
	<li class="<?php echo isActiveOpenMenu($menu_group, 'IC'); ?>">
		<a href="#" class="dropdown-toggle">
			<i class="menu-icon fa fa-home"></i>
			<span class="menu-text"><?php label('IC'); ?></span>
			<b class="arrow fa fa-angle-down"></b>
		</a>
		<ul class="submenu">
			<li class="<?php echo isActiveOpenMenu($menu_sub_group_code, 'RECEIVE'); ?>">
				<a href="#" class="dropdown-toggle">
					<i class="menu-icon fa fa-caret-right"></i> <?php label('RECEIVE'); ?> <b class="arrow fa fa-angle-down"></b>
				</a>
				<ul class="submenu">
					<?php echo side_menu($menu, 'ICPURC',  'inventory/receive_po', label_value('ICPURC')); ?>
					<?php echo side_menu($menu, 'ICPDRC',  'inventory/receive_product', label_value('ICPDRC')); ?>
					<?php echo side_menu($menu, 'ICTRRC',  'inventory/receive_transform', label_value('ICTRRC')); ?>
				</ul>
			</li>
			<li class="<?php echo isActiveOpenMenu($menu_sub_group_code, 'RETURN'); ?>">
				<a href="#" class="dropdown-toggle">
					<i class="menu-icon fa fa-caret-right"></i> <?php label('RETURN'); ?> <b class="arrow fa fa-angle-down"></b>
				</a>
				<ul class="submenu">
					<?php echo side_menu($menu, 'ICRTOR',  'inventory/return_order', label_value('ICRTOR')); ?>
					<?php echo side_menu($menu, 'ICRTLD',  'inventory/return_lend', label_value('ICRTLD')); ?>
				</ul>
			</li>
			<li class="<?php echo isActiveOpenMenu($menu_sub_group_code, 'REQUEST'); ?>">
				<a href="#" class="dropdown-toggle">
					<i class="menu-icon fa fa-caret-right"></i> <?php label('REQUEST'); ?> <b class="arrow fa fa-angle-down"></b>
				</a>
				<ul class="submenu">
				<?php echo side_menu($menu, 'ICTRFM',  'inventory/transform', label_value('ICTRFM')); ?>
				<?php echo side_menu($menu, 'ICTRFS',  'inventory/transform_stock', label_value('ICTRFS')); ?>
				<?php echo side_menu($menu, 'ICSUPP',  'inventory/support', label_value('ICSUPP')); ?>
				<?php echo side_menu($menu, 'ICLEND',  'inventory/lend', label_value('ICLEND')); ?>
				</ul>
			</li>
			<li class="<?php echo isActiveOpenMenu($menu_sub_group_code, 'PICKPACK'); ?>">
				<a href="#" class="dropdown-toggle">
					<i class="menu-icon fa fa-caret-right"></i> <?php label('PICKPACK'); ?>
					<b class="arrow fa fa-angle-down"></b>
				</a>
				<ul class="submenu">
					<?php echo side_menu($menu, 'ICODPR',  'inventory/prepare', label_value('ICODPR')); ?>
					<?php
								if(getConfig('USE_QC') == 1)
								{
									echo side_menu($menu, 'ICODQC',  'inventory/qc', label_value('ICODQC'));
								}
					?>
					<?php echo side_menu($menu, 'ICODDO',  'inventory/delivery_order', label_value('ICODDO')); ?>
					<?php echo side_menu($menu, 'ICODIV',  'inventory/invoice', label_value('ICODIV')); ?>
				</ul>
			</li>

			<li class="<?php echo isActiveOpenMenu($menu_sub_group_code, 'TRANSFER'); ?>">
				<a href="#" class="dropdown-toggle">
					<i class="menu-icon fa fa-caret-right"></i> <?php label('TRANSFER'); ?>
					<b class="arrow fa fa-angle-down"></b>
				</a>
				<ul class="submenu">
					<?php echo side_menu($menu, 'ICTRWH',  'inventory/transfer', label_value('ICTRWH')); ?>
					<?php echo side_menu($menu, 'ICTRZN',  'inventory/move', label_value('ICTRMV')); ?>
				</ul>
			</li>

			<li class="<?php echo isActiveOpenMenu($menu_sub_group_code, 'CHECK'); ?>">
				<a href="#" class="dropdown-toggle">
					<i class="menu-icon fa fa-caret-right"></i> <?php label('CHECK'); ?>
					<b class="arrow fa fa-angle-down"></b>
				</a>
				<ul class="submenu">
					<?php echo side_menu($menu, 'ICCKST',  'inventory/stock', label_value('ICCKST')); ?>
					<?php echo side_menu($menu, 'ICCKBF',  'inventory/buffer', label_value('ICCKBF')); ?>
					<?php echo side_menu($menu, 'ICCKCN',  'inventory/cancle', label_value('ICCKCN')); ?>
					<?php echo side_menu($menu, 'ICCKMV',  'inventory/movement', label_value('ICCKMV')); ?>
				</ul>
			</li>
			<?php echo side_menu($menu, 'ICCSRC',  'inventory/consign_check', label_value('ICCSRC')); ?>
		</ul>
	</li>


	<li class="<?php echo isActiveOpenMenu($menu_group, 'SO'); ?>">
		<a href="#" class="dropdown-toggle">
			<i class="menu-icon fa fa-shopping-basket"></i>
			<span class="menu-text"><?php label('SO'); ?></span>
			<b class="arrow fa fa-angle-down"></b>
		</a>
		<ul class="submenu">
			<?php echo side_menu($menu, 'SOODSO',  'orders/orders', label_value('SOODSO')); ?>
			<?php echo side_menu($menu, 'SOODSP',  'orders/sponsor', label_value('SOODSP')); ?>
			<?php echo side_menu($menu, 'SOCCSO',  'orders/consign_so', label_value('SOCCSO')); ?>
			<?php echo side_menu($menu, 'SOCCTR',  'orders/consign_tr', label_value('SOCCTR')); ?>
		</ul>
	</li>

	<li class="<?php echo isActiveOpenMenu($menu_group, 'AC'); ?>">
		<a href="#" class="dropdown-toggle">
			<i class="menu-icon fa fa-calculator"></i>
			<span class="menu-text"><?php label('AC'); ?></span>
			<b class="arrow fa fa-angle-down"></b>
		</a>
		<ul class="submenu">
			<?php echo side_menu($menu, 'ACPMCF',  'orders/order_payment', label_value('ACPMCF')); ?>
			<?php echo side_menu($menu, 'ACCSOD',  'account/consign_order', label_value('ACCSOD')); ?>
			<?php echo side_menu($menu, 'ACODRP',  'account/order_repay', label_value('ACODRP')); ?>
			<?php echo side_menu($menu, 'ACPMRC',  'account/payment_receive', label_value('ACPMRC')); ?>
			<?php echo side_menu($menu, 'ACODCR',  'account/order_credit', label_value('ACODCR')); ?>
		</ul>
	</li>

	<li class="<?php echo isActiveOpenMenu($menu_group, 'PO'); ?>">
		<a href="#" class="dropdown-toggle">
			<i class="menu-icon fa fa-shopping-bag"></i>
			<span class="menu-text"><?php label('PO'); ?></span>
			<b class="arrow fa fa-angle-down"></b>
		</a>
		<ul class="submenu">
			<?php echo side_menu($menu, 'POPURC',  'purchase/po', label_value('POPURC')); ?>
		</ul>
	</li>


	<li class="<?php echo isActiveOpenMenu($menu_group, 'SC'); ?>">
		<a href="#" class="dropdown-toggle">
			<i class="menu-icon fa fa-cogs"></i>
			<span class="menu-text"><?php label('SC'); ?></span>
			<b class="arrow fa fa-angle-down"></b>
		</a>
		<ul class="submenu">
			<?php echo side_menu($menu, 'SCCONF', 'setting/configs', label_value('SCCONF'));  ?>
			<?php echo side_menu($menu, 'SCPERM', 'users/permission', label_value('SCPERM')); ?>
			<?php echo side_menu($menu, 'SCPOLI', 'discount/discount_policy', label_value('SCPOLI')); ?>
			<?php echo side_menu($menu, 'SCRULE', 'discount/discount_rule', label_value('SCRULE')); ?>
		</ul>
	</li>

	<li class="<?php echo isActiveOpenMenu($menu_group, 'DB'); ?>">
		<a href="#" class="dropdown-toggle">
			<i class="menu-icon fa fa-database"></i>
			<span class="menu-text"><?php label('DB'); ?></span>
			<b class="arrow fa fa-angle-down"></b>
		</a>
		<ul class="submenu">
			<li class="<?php echo isActiveOpenMenu($menu_sub_group_code, 'PRODUCT'); ?>">
				<a href="#" class="dropdown-toggle"><i class="menu-icon fa fa-caret-right"></i> <?php label('PRODUCT'); ?> <b class="arrow fa fa-angle-down"></b></a>
				<ul class="submenu">
					<?php echo side_menu($menu, 'DBPROD', 'masters/products', label_value('DBPROD')); ?>
					<?php echo side_menu($menu, 'DBITEM', 'masters/items',label_value('DBITEM')); ?>
					<?php echo side_menu($menu, 'DBPDGP', 'masters/product_group', label_value('DBPDGP')); ?>
					<?php echo side_menu($menu, 'DBPDSG', 'masters/product_sub_group', label_value('DBPDSG')); ?>
					<?php echo side_menu($menu, 'DBPDCR', 'masters/product_category', label_value('DBPDCR')); ?>
					<?php echo side_menu($menu, 'DBPDKN', 'masters/product_kind', label_value('DBPDKN')); ?>
					<?php echo side_menu($menu, 'DBPDTY', 'masters/product_type', label_value('DBPDTY')); ?>
					<?php echo side_menu($menu, 'DBPTAB', 'masters/product_tab', label_value('DBPTAB')); ?>
					<?php echo side_menu($menu, 'DBPDCL', 'masters/product_color', label_value('DBPDCL')); ?>
					<?php echo side_menu($menu, 'DBPDSI', 'masters/product_size', label_value('DBPDSI')); ?>
					<?php echo side_menu($menu, 'DBPDBR', 'masters/product_brand', label_value('DBPDBR')); ?>
				</ul>
			</li>
			<li class="<?php echo isActiveOpenMenu($menu_sub_group_code, 'CUSTOMER'); ?>">
				<a href="#" class="dropdown-toggle"><i class="menu-icon fa fa-caret-right"></i> <?php label('CUSTOMER'); ?> <b class="arrow fa fa-angle-down"></b></a>
				<b class="arrow"></b>
				<ul class="submenu">
					<?php echo side_menu($menu, 'DBCUST', 'masters/customers', label_value('DBCUST')); ?>
					<?php echo side_menu($menu, 'DBCARE', 'masters/customer_area', label_value('DBCARE')); ?>
					<?php echo side_menu($menu, 'DBCLAS', 'masters/customer_class', label_value('DBCLAS')); ?>
					<?php echo side_menu($menu, 'DBCGRP', 'masters/customer_group', label_value('DBCGRP')); ?>
					<?php echo side_menu($menu, 'DBCKIN', 'masters/customer_kind', label_value('DBCKIN')); ?>
					<?php echo side_menu($menu, 'DBCTYP', 'masters/customer_type', label_value('DBCTYP')); ?>
				</ul>
			</li>

			<li class="<?php echo isActiveOpenMenu($menu_sub_group_code, 'WAREHOUSE'); ?>">
				<a href="#" class="dropdown-toggle"><i class="menu-icon fa fa-caret-right"></i> <?php label('WAREHOUSE'); ?> <b class="arrow fa fa-angle-down"></b></a>
				<b class="arrow"></b>
				<ul class="submenu">
					<?php echo side_menu($menu, 'DBWRHS', 'masters/warehouse', label_value('DBWRHS')); ?>
					<?php echo side_menu($menu, 'DBZONE', 'masters/zone', label_value('DBZONE')); ?>
				</ul>
			</li>
			<li class="<?php echo isActiveOpenMenu($menu_sub_group_code, 'TRANSPORT'); ?>">
				<a href="#" class="dropdown-toggle"><i class="menu-icon fa fa-caret-right"></i> ฐานข้อมูลขนส่ง <b class="arrow fa fa-angle-down"></b></a>
				<b class="arrow"></b>
				<ul class="submenu">
					<?php echo side_menu($menu, 'DBSEND', 'masters/sender','เพิ่ม/แก้ไข ขนส่ง'); ?>
					<?php echo side_menu($menu, 'DBTRSP', 'masters/transport','เชื่อมโยงขนส่ง'); ?>
				</ul>
			</li>
			<?php echo side_menu($menu, 'DBEMPL', 'masters/employee', label_value('DBEMPL')); ?>
			<?php echo side_menu($menu, 'DBVEND', 'masters/vender', label_value('DBVEND')); ?>
			<?php echo side_menu($menu, 'DBCHAN', 'masters/channels', label_value('DBCHAN')); ?>
			<?php echo side_menu($menu, 'DBPAYM', 'masters/payment_methods', label_value('DBPAYM')); ?>
			<?php echo side_menu($menu, 'DBSALE', 'masters/saleman', label_value('DBSALE')); ?>
			<?php echo side_menu($menu, 'DBUSER', 'users/users', label_value('DBUSER'));  ?>
			<?php echo side_menu($menu, 'DBPROF', 'users/profiles', label_value('DBPROF')); ?>
		</ul>
	</li>

	<li class="<?php echo isActiveOpenMenu($menu_group, 'RE'); ?>">
		<a href="#" class="dropdown-toggle">
			<i class="menu-icon fa fa-bar-chart"></i>
			<span class="menu-text"><?php label('report'); ?></span>
			<b class="arrow fa fa-angle-down"></b>
		</a>
		<ul class="submenu">
			<li class="<?php echo isActiveOpenMenu($menu_sub_group_code, 'REINVT'); ?>">
				<a href="#" class="dropdown-toggle"><i class="menu-icon fa fa-caret-right"></i> <?php label('REINVT'); ?> <b class="arrow fa fa-angle-down"></b></a>
				<ul class="submenu">
					<?php echo side_menu($menu, 'RICSTB', 'report/inventory/stock_balance', label_value('RICSTB')); ?>
				</ul>
			</li>

		</ul>
	</li>
</ul><!-- /.nav-list -->

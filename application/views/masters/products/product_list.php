<?php
	$sCode	= isset( $_POST['sCode'] ) ? trim( $_POST['sCode'] ) : ( getCookie('sProductCode') ? getCookie('sProductCode') : '' );
	$sName	= isset( $_POST['sName'] ) ? trim( $_POST['sName'] ) : ( getCookie('sProductName') ? getCookie('sProductName') : '' );
	$sGroup	= isset( $_POST['sGroup'] ) ? $_POST['sGroup'] : ( getCookie('sProductGroup') ? getCookie('sProductGroup') : '' );
	$sCategory	= isset( $_POST['sCategory'] ) ? $_POST['sCategory'] : ( getCookie('sProductCategory') ? getCookie('sProductCategory') : '' );
	$sYear	= isset( $_POST['sYear'] ) ? $_POST['sYear'] : ( getCookie('sProductYear') ? getCookie('sProductYear') : '' );
?>
<form id="searchForm" method="post">
	<div class="row">
    	<div class="col-sm-2">
        	<label>รหัสสินค้า</label>
            <input type="text" class="form-control input-sm text-center search-box" name="sCode" id="sCode" value="<?php echo $sCode; ?>" />
        </div>

        <div class="col-sm-2">
        	<label>ชื่อสินค้า</label>
            <input type="text" class="form-control input-sm text-center search-box" name="sName" id="sName" value="<?php echo $sName; ?>" />
        </div>

        <div class="col-sm-2">
        	<label>กลุ่มสินค้า</label>
            <select class="form-control input-sm select-box" name="sGroup" id="sGroup">
            	<?php echo selectProductGroup($sGroup); ?>
            </select>
        </div>

        <div class="col-sm-2">
        	<label>หมวดหมู่สินค้า</label>
            <select class="form-control input-sm select-box" name="sCategory" id="sCategory">
            	<?php echo selectCategory($sCategory); ?>
            </select>
        </div>

        <div class="col-sm-2">
        	<label>ปีสินค้า</label>
            <select class="form-control input-sm select-box" name="sYear" id="sYear">
            	<option value="">ทั้งหมด</option>
            	<?php echo selectYears($sYear); ?>
            </select>
        </div>


        <div class="col-sm-1">
        	<label class="display-block not-show">search</label>
            <button type="button" class="btn btn-sm btn-primary btn-block" onClick="getSearch()"><i class="fa fa-search"></i> ค้นหา</button>
        </div>
        <div class="col-sm-1">
        	<label class="display-block not-show">reset</label>
            <button type="button" class="btn btn-sm btn-warning btn-block" onClick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
        </div>

    </div>
</form>
<hr class="margin-top-15" />
<?php
	$where = "WHERE p.is_deleted = 0 ";
	if( $sCode != '' )
	{
		createCookie('sProductCode', $sCode);
		$where .= "AND p.code LIKE '%".$sCode."%' ";
	}

	if( $sName != '' )
	{
		createCookie('sProductName', $sName);
		$where .= "AND p.name LIKE '%".$sName."%' ";
	}

	if( $sGroup != '' )
	{
		createCookie('sProductGroup', $sGroup);
		$where .= "AND p.id_group = '".$sGroup."' ";
	}

	if( $sCategory != '' )
	{
		createCookie('sProductCategory', $sCategory);
		$where .= "AND p.id_category = ".$sCategory." ";
	}

	if( $sYear != "" )
	{
		createCookie('sProductYear', $sYear);
		$where .= "AND p.year = '".$sYear."' ";
	}

	$where .= "GROUP BY p.id_style ";
	$where .= "ORDER BY s.code ASC";


	$paginator 	= new paginator();
	$get_rows	= get_rows();
	//$paginator->Per_Page("tbl_product AS p JOIN tbl_style AS s ON p.id_style = s.id", $where, $get_rows);
	$paginator->Per_Page("tbl_product_style AS s JOIN tbl_product AS p ON p.id_style = s.id", $where, $get_rows);
	$paginator->display($get_rows, 'index.php?content=product');

	$qr = "SELECT p.*, s.code AS pdCode FROM tbl_product_style AS s ";
	$qr .= "JOIN tbl_product AS p ON p.id_style = s.id ";

	$qs = dbQuery($qr . $where . " LIMIT ".$paginator->Page_Start.", ".$paginator->Per_Page);

?>
<div class="row">
	<div class="col-sm-12">
    	<table class="table table-striped border-1">
        	<thead>
            	<tr>
                	<th class="width-5 text-center">ลำดับ</th>
									<th class="width-8 text-center">Image</th>
                    <th class="width-15">รหัสสินค้า</th>
                    <th class="width-35">ชื่อสินค้า</th>
                    <th class="width-10">หมวดหมู่</th>
                    <th class="width-10">กลุ่มสินค้า</th>
                    <th class="width-10">ราคา</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
<?php if( dbNumRows($qs) > 0 ) : ?>
<?php	$no = row_no();					?>
<?php	$pg = new product_group();		?>
<?php	$cg = new category();			?>
<?php $image = new image(); ?>
<?php 	while( $rs = dbFetchObject($qs) ) : 	?>
				<tr class="font-size-12">
                	<td class="text-center"><?php echo $no; ?></td>
									<td class="text-center"><img src="<?php echo $image->getProductImage($rs->id, 1 ); ?>" width="60px" /></td>
                    <td><?php echo $rs->pdCode; ?></td>
                    <td><?php echo $rs->name; ?></td>
                    <td><?php echo $cg->getCategoryName($rs->id_category); ?></td>
                    <td><?php echo $pg->getProductGroupName($rs->id_group); ?></td>
                    <td><?php echo number_format($rs->price, 2); ?></td>
                    <td align="right">
                    <?php if( $edit ) : ?>
                    	<button type="button" class="btn btn-xs btn-warning" onClick="goEdit('<?php echo $rs->id_style; ?>')"><i class="fa fa-pencil"></i></button>
                    <?php endif; ?>
                    <?php if( $delete ) : ?>
                    	<button type="button" class="btn btn-xs btn-danger" onClick="goDelete('<?php echo $rs->id; ?>', '<?php echo $rs->pdCode; ?>')"><i class="fa fa-trash"></i></button>
                    <?php endif; ?>
                    </td>
                </tr>
<?php		$no++;		?>
<?php	endwhile;			?>
<?php else : ?>
				<tr>
                	<td colspan="8" align="center"><h4>ไม่พบรายการ</h4></td>
                </tr>
<?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<script src="script/product/product_list.js"></script>

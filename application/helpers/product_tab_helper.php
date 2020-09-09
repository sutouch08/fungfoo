<?php

function productTabMenu($mode = 'order')
{
	$CI =& get_instance();
	$ajax = $mode == 'order' ? 'getOrderTabs' : ($mode == 'sale' ? 'getSaleOrderTabs' : 'getViewTabs');
	$sc = '';
	$qr = "SELECT * FROM product_tab WHERE id_parent = 0";
	$qs = $CI->db->query($qr);

	foreach( $qs->result() as $rs)
	{
		if( hasChild($rs->id) === TRUE)
		{
			//$sc .= '<li class="dropdown" onmouseover="expandTab((this))" onmouseout="collapseTab((this))">';
			$sc .= '<li class="dropdown pointer">';
			//$sc .= '<a id="ul-'.$rs->id.'" class="dropdown-toggle" role="tab" data-toggle="tab" href="#cat-'.$rs->id.'" onClick="'.$ajax.'(\''.$rs->id.'\')" >';
			$sc .= '<a id="ul-'.$rs->id.'" class="dropdown-toggle" role="tab" data-toggle="dropdown" aria-expanded="false">';
			$sc .=  $rs->name.'<span class="caret"></span></a>';
			//$sc .=  $rs->name.'</a>';
			$sc .= 	'<ul class="dropdown-menu" role="menu" aria-labelledby="ul-'.$rs->id.'">';
			$sc .= 	getSubTab($rs->id, $ajax);
			$sc .=  '</ul>';
			$sc .= '</li>';
		}
		else
		{
			$sc .= '<li class="menu"><a href="#cat-'.$rs->id.'" role="tab" data-toggle="tab" onClick="'.$ajax.'(\''.$rs->id.'\')">'.$rs->name.'</a></li>';
		}
	}
	return $sc;

}

//-- this function to view category product in order page
function getSubTab($parent, $ajax)
{
	$ci =& get_instance();
	$sc = '';
	$qs = $ci->db->query("SELECT * FROM product_tab WHERE id_parent = ".$parent." ORDER BY name ASC");

	if( $qs->num_rows() > 0 )
	{
		foreach( $qs->result() as $rs )
		{
			if( hasChild($rs->id) === TRUE ) //----- ถ้ามี sub category
			{
				$sc .= '<li class="dropup dropdown-hover" onClick="'.$ajax.'(\''.$rs->id.'\')">';
				$sc .= '<a id="ul-'.$rs->id.'" class="clearfix" href="#cat-'.$rs->id.'" role="tab" data-toggle="tab">';
				$sc .=  '<span class="pull-left">'.$rs->name.'</span><i class="ace-icon fa fa-caret-left pull-right"></i></a>';
				$sc .= 	'<ul class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="ul-'.$rs->id.'">';
				$sc .= 	getSubTab($rs->id, $ajax);
				$sc .=  '</ul>';
				$sc .= '</li>';
			}
			else
			{
				$sc .= '<li class="menu"><a href="#cat-'.$rs->id.'" role="menu" data-toggle="tab" onClick="'.$ajax.'(\''.$rs->id.'\')">'.$rs->name.'</a></li>';
			}

		}
	}
	return $sc;
}


//-- this function to view category product in order page
function getSubCategoryTab($parent, $ajax)
{
	$ci =& get_instance();
	$sc = '';
	$qs = $ci->db->query("SELECT * FROM product_tab WHERE id_parent = ".$parent);

	if( $qs->num_rows() > 0 )
	{
		foreach( $qs->result() as $rs )
		{
			if( hasChild($rs->id) === TRUE ) //----- ถ้ามี sub category
			{
				$sc .= '<li class="dropdown-hover" onmouseover="expandTab((this))" onmouseout="collapseTab((this))">';
				$sc .= '<a id="ul-'.$rs->id.'" class="dropdown-toggle" href="#cat-'.$rs->id.'" data-toggle="tab" onClick="'.$ajax.'(\''.$rs->id.'\')">';
				$sc .=  $rs->name.'</a>';
				$sc .= 	'<ul class="dropdown-menu" role="menu" aria-labelledby="ul-'.$rs->id.'">';
				$sc .= 	getSubTab($rs->id, $ajax);
				$sc .=  '</ul>';
				$sc .= '</li>';
			}
			else
			{
				$sc .= '<li class="menu"><a href="#cat-'.$rs->id.'" role="tab" data-toggle="tab" onClick="'.$ajax.'(\''.$rs->id.'\')">'.$rs->name.'</a></li>';
			}

		}
	}
	return $sc;
}



function getProductTabs()
{
	$ci =& get_instance();
	$sc = '';
	$qs = $ci->db->query("SELECT * FROM product_tab WHERE id != 0");
	foreach($qs->result() as $rs)
	{
		$sc .= '<div class="tab-pane" id="cat-'.$rs->id.'"></div>';
	}

	return $sc;
}




function selectLevel($level = '' )
{
	$sc = '<option value="">ทั้งหมด</option>';
	for( $i = 1; $i <= 5; $i++ )
	{
		$sc .= '<option value="'.$i.'" ' . isSelected($i, $level) . '>' . $i . '</option>';
	}
	return $sc;
}


function parentIn($txt)
{
	$ci =& get_instance();
	$ci->load->model('masters/product_tab_model');
	$sc = -1;
	$qs = $ci->product_tab_model->getSearchResult($txt);
	$i = 1;
	if( $qs->num_rows() > 0 )
	{
		$sc = '';
		foreach( $qs->result() as $rs )
		{
			$sc .= $i == 1 ? $rs->id : ", ".$rs->id;
		}
	}

	return $sc;
}


function getTabsTree($id = 0, $script = TRUE)
{
	$uid = uniqid();
	$cs =& get_instance();
	$sc	= '<ul class="tree">';
	$sc .= '<li>';
	$sc .= '<i class="fa fa-minus-square-o" id="catbox-0" onClick="toggleTree(0)"></i>';
	$sc .= '<label class="padding-10">';
	$sc .= '<input type="radio" class="ace" name="tabs" value="0" '. is_checked($id, 0) .' />';
	$sc .= '<span class="lbl">  TOP LEVEL</span>';
	$sc .= '</label>';
	$sc .= '<ul id="catchild-0">';
	$qs  = $cs->db->query("SELECT * FROM product_tab WHERE id_parent = 0");
	$i = 1;
	foreach( $qs->result() as $rs )
	{
		$sc .= '<li class="'. ($i == 1 ? '' : 'margin-top-15').'">';
		$i++;

		//----- Next Level
		if( hasChild($rs->id) === TRUE )
		{
			$sc .= '<i class="fa fa-plus-square-o" id="catbox-'.$rs->id.'" onClick="toggleTree('.$rs->id.')"></i>';
			$sc .= '<label class="padding-10">';
			$sc .= '<input type="radio" class="ace" name="tabs" value="'.$rs->id.'" '. is_checked($id, $rs->id) .' />';
			$sc .= '<span class="lbl">  '.$rs->name.'</span>';
			$sc .= '</label>';
			$sc .= '<ul id="catchild-'.$rs->id.'" class="hide">';
			$sc .= getChild($rs->id, $id) ;
			$sc .= '</ul>';
		}
		else
		{
			$sc .= '<label class="padding-10">';
			$sc .= '<input type="radio" class="ace" name="tabs" value="'.$rs->id.'" '. is_checked($id, $rs->id) .' />';
			$sc .= '<span class="lbl">'.$rs->name.'</span>';
			$sc .= '</label>';
		}//---- has sub cate
		$sc .= '</li>';
	}
	$sc 	.= '</ul></li>';
	$sc	.= '</ul>';
	if( $script === TRUE)
	{
		$sc .= '<script>';
		$sc .= 'function toggleTree(id){';
		$sc .= 'var ul 	= $("#catchild-"+id);';
		$sc .= 'var rs 	= ul.hasClass("hide");';
		$sc .= 'if( rs == true){';
		$sc .= 'ul.removeClass("hide");';
		$sc .= '$("#catbox-"+id).removeClass("fa-plus-square-o");';
		$sc .= '$("#catbox-"+id).addClass("fa-minus-square-o");';
		$sc .= '}else	{';
		$sc .= 'ul.addClass("hide");';
		$sc .= '$("#catbox-"+id).removeClass("fa-minus-square-o");';
		$sc .= '$("#catbox-"+id).addClass("fa-plus-square-o");';
		$sc .= '} ';
		$sc .= '}';
		$sc .= '</script>';
	}
	return $sc;
}




function getEditTabsTree($id, $script = TRUE)
{
	$cs =& get_instance();
	$cs->load->model('masters/product_tab_model');
	$id_parent = $cs->product_tab_model->getParentId($id);
	$parent = $cs->product_tab_model->getAllParent($id);
	$sc	 = '<ul class="tree">';
	$sc .= '<li>';
	$sc .= '<i class="fa fa-minus-square-o" id="edit-catbox-0" onClick="toggleEditTree(0)"></i>';
	$sc .= '<label class="padding-10">';
	$sc .= '<input type="radio" class="ace" name="tabs" value="0" '. is_checked($id_parent, 0) .' />';
	$sc .= '<span class="lbl">TOP LEVEL</span>';
	$sc .= '</label>';
	$sc .= '<ul id="edit-catchild-0">';
	$qs  = $cs->db->query("SELECT * FROM product_tab WHERE id_parent = 0");
	$i   = 1;
	foreach( $qs->result() as $rs )
	{
		if( $rs->id != $id )
		{
			$sc .= '<li class="'. ($i == 1 ? '' : 'margin-top-15').'">';
			$i++;
			$ex = isset( $parent[$rs->id] ) ? '' : 'hide';
			$ep = isset( $parent[$rs->id] ) ? 'minus' : 'plus';
			//----- Next Level
			if( hasEditChild($rs->id, $id) === TRUE )
			{
				$sc .= '<i class="fa fa-'.$ep.'-square-o" id="edit-catbox-'.$rs->id.'" onClick="toggleEditTree('.$rs->id.')"></i>';
				$sc .= '<label class="padding-10">';
				$sc .= '<input type="radio" class="ace" name="tabs" value="'.$rs->id.'" '. is_checked($id_parent, $rs->id) .' />';
				$sc .= '<span class="lbl">'.$rs->name.'</label>';
				$sc .= '<ul id="edit-catchild-'.$rs->id.'" class="'.$ex.'">';
				$sc .= getEditChild($rs->id, $parent, $id_parent, $id) ;
				$sc .= '</ul>';
			}
			else
			{
				$sc .= '<label class="padding-10">';
				$sc .= '<input type="radio" class="ace" name="tabs" value="'.$rs->id.'" '. is_checked($id_parent, $rs->id) .' />';
				$sc .= '<span class="lbl">'.$rs->name.'</span>';
				$sc .= '</label>';
			}//---- has sub cate
			$sc .= '</li>';
		}
	}
	$sc 	.= '</ul></li>';
	$sc	.= '</ul>';
	if( $script === TRUE)
	{
		$sc .= '<script>';
		$sc .= 'function toggleEditTree(id){';
		$sc .= 'var ul 	= $("#edit-catchild-"+id);';
		$sc .= 'var rs 	= ul.hasClass("hide");';
		$sc .= 'if( rs == true){';
		$sc .= 'ul.removeClass("hide");';
		$sc .= '$("#edit-catbox-"+id).removeClass("fa-plus-square-o");';
		$sc .= '$("#edit-catbox-"+id).addClass("fa-minus-square-o");';
		$sc .= '}else	{';
		$sc .= 'ul.addClass("hide");';
		$sc .= '$("#edit-catbox-"+id).removeClass("fa-minus-square-o");';
		$sc .= '$("#edit-catbox-"+id).addClass("fa-plus-square-o");';
		$sc .= '} ';
		$sc .= '}';
		$sc .= '</script>';
	}
	return $sc;
}



function hasChild($id)
{
	$sc = FALSE;
	$ci =& get_instance();
	$qs = $ci->db->query("SELECT id FROM product_tab WHERE id_parent = ".$id);
	if( $qs->num_rows() > 0 )
	{
		$sc = TRUE;
	}
	return $sc;
}



function hasEditChild($id, $id_tab)
{
	$sc = FALSE;
	$ci =& get_instance();
	$qs = $ci->db->query("SELECT id FROM product_tab WHERE id_parent = ".$id." AND id != ".$id_tab);
	if( $qs->num_rows() > 0 )
	{
		$sc = TRUE;
	}
	return $sc;
}



function getChild($id_parent, $id )
{
	$sc = '';
	$ci =& get_instance();
	$qs = $ci->db->query("SELECT * FROM product_tab WHERE id_parent = ".$id_parent);
	if( $qs->num_rows() > 0 )
	{

		foreach( $qs->result() as $rs)
		{
				$sc .= '<li>';
				//----- Next Level
			if( hasChild($rs->id) === TRUE )
			{
				$sc .= '<i class="fa fa-plus-square-o" id="catbox-'.$rs->id.'" onClick="toggleTree('.$rs->id.')"></i>';
				$sc .= '<label class="padding-10">';
				$sc .= '<input type="radio" class="ace" name="tabs" value="'.$rs->id.'" '. is_checked($id, $rs->id) .' />';
				$sc .= '<span class="lbl">' .$rs->name. '</span>';
				$sc .= '</label>';
				$sc .= '<ul id="catchild-'.$rs->id.'" class="hide">';
				$sc .= getChild($rs->id, $id) ;
				$sc .= '</ul>';
			}
			else
			{
				$sc .= '<label class="padding-10">';
				$sc .= '<input type="radio" class="ace" name="tabs" value="'.$rs->id.'" '. is_checked($id, $rs->id) .' />';
				$sc .= '<span class="lbl">'.$rs->name.'</span>';
				$sc .= '</label>';
			}//---- has sub cate
			$sc .= '</li>';
		}
	}
	return $sc;
}



function getEditChild($id_parent, $parent, $id, $id_tab )
{
	$sc = '';
	$ci =& get_instance();
	$qs = $ci->db->query("SELECT * FROM product_tab WHERE id_parent = ".$id_parent);
	if( $qs->num_rows() > 0 )
	{

		foreach( $qs->result() as $rs)
		{
			if( $rs->id != $id_tab )
			{
				$ex = isset( $parent[$rs->id] ) ? '' : 'hide';
				$ep = isset( $parent[$rs->id] ) ? 'minus' : 'plus';
				$sc .= '<li>';
				//----- Next Level
				if( hasEditChild($rs->id, $id_tab) === TRUE )
				{

					$sc .= '<i class="fa fa-'.$ep.'-square-o" id="edit-catbox-'.$rs->id.'" onClick="toggleEditTree('.$rs->id.')"></i>';
					$sc .= '<label class="padding-10">';
					$sc .= '<input type="radio" class=" ace margin-right-10" name="tabs" value="'.$rs->id.'" '. is_checked($id, $rs->id) .' />';
					$sc .= '<span class="lbl">'.$rs->name. '</span>';
					$sc .= '</label>';
					$sc .= '<ul id="edit-catchild-'.$rs->id.'" class="'.$ex.'">';
					$sc .= getEditChild($rs->id, $parent, $id, $id_tab) ;
					$sc .= '</ul>';
				}
				else //-- if hasChild
				{
					$sc .= '<label class="padding-10">';
					$sc .= '<input type="radio" class="ace" name="tabs" value="'.$rs->id.'" '. is_checked($id, $rs->id) .' />';
					$sc .= '<span class="lbl">'.$rs->name. '</span>';
					$sc .= '</label>';
				}//---- if has Child
				$sc .= '</li>';
			}
		}//--- end while
	}//-- endif
	return $sc;
}



function productTabsTree($style_code = '', $script = TRUE)
{
	$cs =& get_instance();
	$cs->load->model('masters/product_tab_model');
	//----- รายการที่ติ๊ก
	$se = $cs->product_tab_model->getStyleTabsId($style_code);

	//------- รายการที่ต้อง expan
	$parent = $cs->product_tab_model->getParentTabsId($style_code);

	$sc	= '<ul class="tree">';
	$sc 	.= '<li>';
	$sc 	.= '<i class="fa fa-minus-square-o" id="catbox-0" onClick="toggleTree(0)"></i>';
	$sc 	.= '<label class="padding-10"> TOP LEVEL </label>';
	$sc 	.= '<ul id="catchild-0">';
	$qs = $cs->db->query("SELECT * FROM product_tab WHERE id_parent = 0");
	$i = 1;
	if($qs->num_rows() > 0)
	{
		foreach( $qs->result() as $rs )
		{
			$is_checked = isset( $se[$rs->id] ) ? 'checked' : '';
			$sc .= '<li class="'. ($i == 1 ? '' : 'margin-top-15').'">';
			$i++;

			$ex = isset( $parent[$rs->id] ) ? '' : 'hide';
			$ep = isset( $parent[$rs->id] ) ? 'minus' : 'plus';
			//----- Next Level
			if( hasChild($rs->id) === TRUE )
			{
				$sc .= '<i class="fa fa-'.$ep.'-square-o" id="catbox-'.$rs->id.'" onClick="toggleTree('.$rs->id.')"></i>';
				$sc .= '<label class="padding-10">
									<input type="checkbox" class="ace margin-right-10" name="tabs[]" value="'.$rs->id.'" '. $is_checked .' />
									<span class="lbl">'.$rs->name.'</span></label>';
				$sc .= '<ul id="catchild-'.$rs->id.'" class="'.$ex.'">';
				$sc .= productTabChild($rs->id, $style_code, $parent, $se) ;
				$sc .= '</ul>';
			}
			else
			{
				$sc .= '<label class="padding-10">
									<input type="checkbox" class="ace margin-right-10" name="tabs[]" value="'.$rs->id.'" '. $is_checked .' />
									<span class="lbl">'.$rs->name.'</span></label>';
			}//---- has sub cate
			$sc .= '</li>';
		}
	}

	$sc 	.= '</ul></li>';
	$sc	.= '</ul>';
	if( $script === TRUE)
	{
		$sc .= '<script>';
		$sc .= 'function toggleTree(id){';
		$sc .= 'var ul 	= $("#catchild-"+id);';
		$sc .= 'var rs 	= ul.hasClass("hide");';
		$sc .= 'if( rs == true){';
		$sc .= 'ul.removeClass("hide");';
		$sc .= '$("#catbox-"+id).removeClass("fa-plus-square-o");';
		$sc .= '$("#catbox-"+id).addClass("fa-minus-square-o");';
		$sc .= '}else	{';
		$sc .= 'ul.addClass("hide");';
		$sc .= '$("#catbox-"+id).removeClass("fa-minus-square-o");';
		$sc .= '$("#catbox-"+id).addClass("fa-plus-square-o");';
		$sc .= '} ';
		$sc .= '}';
		$sc .= '</script>';
	}
	return $sc;
}


function productTabChild($id_parent, $style_code, $parent, $se )
{
	$sc = '';
	$ci =& get_instance();
	$qs = $ci->db->query("SELECT * FROM product_tab WHERE id_parent = ".$id_parent);
	if( $qs->num_rows() > 0 )
	{

		foreach( $qs->result() as $rs)
		{
				$sc .= '<li>';
				//----- Next Level
				$ex = isset( $parent[$rs->id] ) ? '' : 'hide';
				$ep = isset( $parent[$rs->id] ) ? 'minus' : 'plus';
				$is_checked = isset( $se[$rs->id] ) ? 'checked' : '';
			if( hasChild($rs->id) === TRUE )
			{
				$sc .= '<i class="fa fa-'.$ep.'-square-o" id="catbox-'.$rs->id.'" onClick="toggleTree('.$rs->id.')"></i>';
				$sc .= '<label class="padding-10">
									<input type="checkbox" class="ace" name="tabs[]" value="'.$rs->id.'" '. $is_checked .' />
									<span class="lbl margin-right-10">' .$rs->name. '</span></label>';
				$sc .= '<ul id="catchild-'.$rs->id.'" class="'.$ex.'">';
				$sc .= productTabChild($rs->id, $style_code, $parent, $se) ;
				$sc .= '</ul>';
			}
			else
			{
				$sc .= '<label class="padding-10">
				<input type="checkbox" class="ace margin-right-10" name="tabs[]" value="'.$rs->id.'" '. $is_checked .' />
				<span class="lbl">' .$rs->name. '</span>
				</label>';
			}//---- has sub cate
			$sc .= '</li>';
		}
	}
	return $sc;
}



function isInTab($style_code, $id_tab)
{
	$sc = FALSE;
	$ci =& get_instance();
	$qs = $ci->db->query("SELECT * FROM product_tab_style WHERE style_code = ".$style_code." AND id_tab = ".$id_tab);
	if( $qs->num_rows() > 0 )
	{
		$sc = TRUE;
	}
	return $sc;
}


?>

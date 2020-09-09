<?php
function getTransformProducts($transform_product, $state = 1, $is_expired = 0)
{
	$sc = '';

	if(!empty($transform_product))
	{
		foreach($transform_product as $rs)
		{
			$sc .= '<div class="display-block">';
			$sc .= $rs->product_code.' : '.$rs->order_qty;

			//---	ถ้ายังไม่ได้รับสินค้า สามารถลบได้
			if( $is_expired == 0 && $rs->receive_qty == 0 && $state < 3)
			{
				$sc .= '<span class="red pointer" onclick="removeTransformProduct('.$rs->id_order_detail.', \''.$rs->product_code.'\')">  <i class="fa fa-times">';
				$sc .= '</i></span>';
			}

			if( $is_expired == 0 && $rs->receive_qty > 0 && $state < 3)
			{
				$sc .= '<span class="red pointer" onClick="editTransformProduct('.$rs->id_order_detail.', \''.$rs->product_code.'\', '.$rs->receive_qty.', '.$rs->sold_qty.')"> <i class="fa fa-pencil">';
				$sc .= '</i></span>';
			}

			$sc .= '</div>';
		}
	}

	return $sc;
}

 ?>

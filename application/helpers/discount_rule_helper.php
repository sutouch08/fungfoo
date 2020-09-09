<?php

function discount_rule_in($txt)
{
  $sc = "0";
  $CI =& get_instance();
  $CI->load->model('discount/discount_rule_model');
  $rs = $CI->discount_rule_model->search($txt);

  if(!empty($rs))
  {
    foreach($rs as $cs)
    {
      $sc .= ", ".$cs->id;
    }
  }

  return $sc;
}


function showItemDiscountLabel($item_price, $item_disc, $unit)
{
	$disc = 0.00;
	//---	ถ้าเป็นการกำหนดราคาขาย
	if($item_price > 0)
	{
		$disc = 'Price '.$item_price;
	}
	else
	{
		$symbal = $unit == 'percent' ? '%' : '';
		$disc = $item_disc.' '.$symbal;
	}

	return $disc;
}
?>

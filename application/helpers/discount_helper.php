<?php

//--- convert discount text to array
function parse_discount_text($discText, $price)
{
	$disc = array(
		'discount1' => 0,
		'discount2' => 0,
		'discount3' => 0,
		'discount_amount' => 0
	);

	if(!empty($discText))
	{
		$step = explode('+', $discText);

		$i = 1;
		foreach($step as $discLabel)
		{
			if($i < 4)
			{
				$key = 'discount'.$i;
				$arr = explode('%', $discLabel);
				$arr[0] = floatval($arr[0]);
				$discount = count($arr) == 1 ? $arr[0] : ($arr[0] * 0.01) * $price; //--- ส่วนลดต่อชิ้น
				$disc[$key] = count($arr) == 1 ? $arr[0] : $arr[0].'%'; //--- discount label
				$disc['discount_amount'] += $discount;
				$price -= $discount;
			}

			$i++;
		}
	}

	return $disc;
}


//--- แสดงป้ายส่วนลด
function discountLabel($disc = 0, $disc2 = 0, $disc3 = 0)
{
	$label = '';
	$label = $disc == 0 ? 0 : getDiscLabel($disc);
	$label .= $disc2 == 0 ? '' : '+'.getDiscLabel($disc2);
	$label .= $disc3 == 0 ? '' : '+'.getDiscLabel($disc3);
	return $label;
}


function getDiscLabel($disc)
{
	$arr = explode('%', $disc);
	if( count($arr) > 1)
	{
		return trim($arr[0]).'%';
	}
	return $arr[0];
}


function discountAmountToPercent($amount, $qty, $price)
{
	return (($amount/$qty)*100)/$price;
}

?>

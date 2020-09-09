<?php
function setToken($token)
{
	$CI =& get_instance();
	$cookie = array(
		'name' => 'file_download_token',
		'value' => $token,
		'expire' => 3600,
		'path' => '/'
	);

	return $CI->input->set_cookie($cookie);
}


//---	ตัดข้อความแล้วเติม ... ข้างหลัง
function limitText($str, $length)
{
	$txt = '...';
	if( strlen($str) >= $length)
	{
		return mb_substr($str, 0, $length).$txt;
	}
	else
	{
		return $str;
	}
}




function is_selected($val, $select)
{
  return $val == $select ? 'selected' : '';
}




function is_checked($val1, $val2)
{
  return $val1 == $val2 ? 'checked' : '';
}



function is_active($val)
{
  return $val == 1 ? '<i class="fa fa-check green"></i>' : '<i class="fa fa-times red"></i>';
}




function get_filter($postName, $cookieName, $defaultValue = "")
{
  $CI =& get_instance();
  $sc = '';

  if($CI->input->post($postName) !== NULL)
  {
    $sc = $CI->input->post($postName);
    $CI->input->set_cookie(array('name' => $cookieName, 'value' => $sc, 'expire' => 3600 , 'path' => '/'));
  }
  else if($CI->input->cookie($cookieName) !== NULL)
  {
    $sc = $CI->input->cookie($cookieName);
  }
  else
  {
    $sc = $defaultValue;
  }

	return $sc;
}




function clear_filter($cookies)
{
  if(is_array($cookies))
  {
    foreach($cookies as $cookie)
    {
      delete_cookie($cookie);
    }
  }
  else
  {
    delete_cookie($cookies);
  }
}




function set_rows($value = 20)
{
  $value = $value > 300 ? 300 : $value;

  $arr = array(
    'name' => 'rows',
    'value' => $value,
    'expire' => 259200,
    'path' => '/'
  );
  $CI =& get_instance();
  return $CI->input->set_cookie($arr);
}





function get_rows()
{
  $CI =& get_instance();
  return $CI->input->cookie('rows') === NULL ? 20 : $CI->input->cookie('rows');
}




function number($val, $digit = 0)
{
  return number_format($val, $digit);
}




function ac_format($val, $digit = 0)
{
	return $val == 0 ? '-' : number_format($val, $digit);
}


function getConfig($code)
{
  $CI =& get_instance();
  $rs = $CI->db->select('value')->where('code', $code)->get('config');
  if($rs->num_rows() == 1)
  {
    return $rs->row()->value;
  }

	return NULL;
}



function get_vat_amount($amount)
{
	$vat = getConfig('SALE_VAT_RATE');
	if($vat != 0)
	{
		$re_vat = $vat * 0.01;
		return $amount * $re_vat;
	}

	return $amount;
}



function remove_vat($amount)
{
	$vat = getConfig('SALE_VAT_RATE'); //-- 7
	if( $vat != 0 )
	{
		$re_vat	= ($vat + 100) / 100;
		return $amount/$re_vat;
	}

	return $amount;
}






function add_vat($amount)
{
	$vat = getConfig('SALE_VAT_RATE');
	if( $vat != 0 )
	{
		$re_vat = $vat * 0.01;
		return ($amount * $re_vat) + $amount;
	}

	return $amount;
}



function set_error($message)
{
  $CI =& get_instance();
  $CI->session->set_flashdata('error', $message);
}


function set_message($message)
{
  $CI =& get_instance();
  $CI->session->set_flashdata('success', $message);
}



function label($content)
{
	$ci =& get_instance();
	echo $ci->lang->line($content);
}

function label_value($content)
{
	$ci =& get_instance();
	return $ci->lang->line($content);
}

//--- return null if blank value
function get_null($value)
{
	return ($value === '0' OR $value === 0 OR $value === '') ? NULL : $value;
}


//--- return TRUE if value ==  1 else return FALSE;
function is_true($value)
{
	if($value == 1 OR $value === TRUE)
	{
		return TRUE;
	}

	return FALSE;
}


function get_auz()
{
	$auz = getConfig('ALLOW_UNDER_ZERO') == 1 ? TRUE : FALSE;
	return $auz;
}

function pagination_config( $base_url, $total_rows = 0, $perpage = 20, $segment = 3)
{
    $rows = get_rows();
    $input_rows  = '<p class="pull-right pagination hidden-xs">';
    $input_rows .= label_value('all').' '.$total_rows.' '.label_value('rows').' | '.label_value('show');
    $input_rows .= '<input type="number" name="set_rows" id="set_rows" class="input-mini text-center margin-left-15 margin-right-10" value="'.$rows.'" />';
    $input_rows .= label_value('perpage').' ';
    $input_rows .= '<buton class="btn btn-success btn-xs" type="button" onClick="set_rows()">'.label_value('show').'</button>';
    $input_rows .= '</p>';

		$config['full_tag_open'] 		= '<nav><ul class="pagination hidden-print">';
		$config['full_tag_close'] 		= '</ul>'.$input_rows.'</nav><hr class="hidden-print">';
		$config['first_link'] 				= 'First';
		$config['first_tag_open'] 		= '<li>';
		$config['first_tag_close'] 		= '</li>';
		$config['next_link'] 				= 'Next';
		$config['next_tag_open'] 		= '<li>';
		$config['next_tag_close'] 	= '</li>';
		$config['prev_link'] 			= 'prev';
		$config['prev_tag_open'] 	= '<li>';
		$config['prev_tag_close'] 	= '</li>';
		$config['last_link'] 				= 'Last';
		$config['last_tag_open'] 		= '<li>';
		$config['last_tag_close'] 		= '</li>';
		$config['cur_tag_open'] 		= '<li class="active"><a href="#">';
		$config['cur_tag_close'] 		= '</a></li>';
		$config['num_tag_open'] 		= '<li>';
		$config['num_tag_close'] 		= '</li>';
		$config['uri_segment'] 		= $segment;
		$config['per_page']			= $perpage;
		$config['total_rows']			= $total_rows != false ? $total_rows : 0 ;
		$config['base_url']				= $base_url;
		return $config;
}



 ?>

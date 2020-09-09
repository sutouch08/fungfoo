<?php
function zone_in($txt)
{
  $sc = array('0');
  $CI =& get_instance();
  $CI->load->model('inventory/zone_model');
  $zone = $CI->zone_model->search($txt);
  if(!empty($zone))
  {
    foreach($zone as $rs)
    {
      $sc[] = $rs->code;
    }
  }

  return $sc;
}


 ?>

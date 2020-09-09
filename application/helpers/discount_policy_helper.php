<?php

function discount_policy_in($txt)
{
  $sc = "0";
  $CI =& get_instance();
  $CI->load->model('discount/discount_policy_model');
  $rs = $CI->discount_policy_model->search($txt);

  if(!empty($rs))
  {
    foreach($rs as $cs)
    {
      $sc .= ", ".$cs->id;
    }
  }

  return $sc;
}

?>

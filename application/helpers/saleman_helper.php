<?php

function select_saleman($code = "")
{
  $CI =& get_instance();
  $CI->load->model('masters/saleman_model');
  $result = $CI->saleman_model->get_data();
  $ds = '';
  if(!empty($result))
  {
    foreach($result as $rs)
    {
      $ds .= '<option value="'.$rs->code.'" '.is_selected($rs->code, $code).'>'.$rs->name.'</option>';
    }
  }

  return $ds;
}
 ?>

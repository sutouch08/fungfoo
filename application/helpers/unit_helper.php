<?php
function select_unit($code = '')
{
  $sc = '';
  $CI =& get_instance();
  $CI->load->model('masters/unit_model');
  $options = $CI->unit_model->get_data(); //--- OUOM

  if(!empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected($code, $rs->code).'>'.$rs->code.' | '.$rs->name.'</option>';
    }
  }

  return $sc;
}


 ?>

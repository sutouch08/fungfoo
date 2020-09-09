<?php
function select_payment_method($code = '')
{
  $sc = '';
  $CI =& get_instance();
  $CI->load->model('masters/payment_methods_model');
  $payments = $CI->payment_methods_model->get_data();
  if(!empty($payments))
  {
    foreach($payments as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected($rs->code, $code).'>'.$rs->name.'</option>';
    }
  }

  return $sc;
}



function select_payment_role($id='')
{
  $sc = "";
  $CI =& get_instance();
  $CI->load->model('masters/payment_methods_model');
  $payments = $CI->payment_methods_model->get_role_list();
  if(!empty($payments))
  {
    foreach($payments as $rs)
    {
      $sc .= '<option value="'.$rs->id.'" '.is_selected($rs->id, $id).'>'.$rs->name.'</option>';
    }
  }

  return $sc;
}

 ?>

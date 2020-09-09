<?php
function select_payment_type($code = NULL)
{
  $ci =& get_instance();
  $ci->load->model('account/order_repay_model');
  $list = $ci->order_repay_model->get_pay_type_list();
  $sc = "";
  if(!empty($list))
  {
    foreach($list as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected($code, $rs->code).'>'.$rs->name.'</option>';
    }
  }

  return $sc;
}

 ?>

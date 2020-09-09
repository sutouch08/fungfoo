<?php
function select_product_kind($code = '')
{
  $CI =& get_instance();
  $CI->load->model('masters/product_kind_model');
  $result = $CI->product_kind_model->get_data();
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

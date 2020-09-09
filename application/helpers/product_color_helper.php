<?php
function select_color_group($code = NULL)
{
  $CI =& get_instance();
  $CI->load->model('masters/product_color_model');
  $result = $CI->product_color_model->get_color_group_list();
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

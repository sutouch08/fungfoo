<?php
function select_warehouse_role($se = NULL)
{
  $sc = '';
  $CI =& get_instance();
  $CI->load->model('masters/warehouse_model');
  $options = $CI->warehouse_model->get_all_role();

  if(!empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= '<option value="'.$rs->id.'" '.is_selected($se, $rs->id).'>'.$rs->name.'</option>';
    }
  }

  return $sc;
}


function select_warehouse($se = NULL)
{
  $sc = '';
  $CI =& get_instance();
  $CI->load->model('masters/warehouse_model');
  $options = $CI->warehouse_model->get_warehouses();

  if(!empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected($se, $rs->code).'>'.$rs->name.'</option>';
    }
  }

  return $sc;
}


//--- เอาเฉพาะคลังซื้อขาย
function select_sell_warehouse($se = NULL)
{
  $sc = '';
  $CI =& get_instance();
  $CI->load->model('masters/warehouse_model');
  $options = $CI->warehouse_model->get_sell_warehouse_list();

  if(!empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected($se, $rs->code).'>'.$rs->name.'</option>';
    }
  }

  return $sc;
}

 ?>

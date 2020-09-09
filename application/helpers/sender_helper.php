<?php
function sender_in($txt)
{
  $sc = "9999999";
  $CI =& get_instance();
  $CI->load->model('masters/sender_model');
  $ds = $CI->sender_model->search($txt);

  if(!empty($ds))
  {
    foreach($ds as $rs)
    {
      $sc .= ", {$rs->id}";
    }
  }

  return $sc;
}



function select_sender_list($id=NULL)
{
  $sc = '';
  $ci =& get_instance();
  $ci->load->model('masters/sender_model');
  $list = $ci->sender_model->get_sender_list();
  if(!empty($list))
  {
    foreach($list as $rs)
    {
      $sc .= '<option value="'.$rs->id.'" '.is_selected($id, $rs->id).'>'.$rs->name.'</option>';
    }
  }

  return $sc;
}


 ?>

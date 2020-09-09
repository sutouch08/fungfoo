<?php
function select_profile($id = '')
{
  $sc = '';
  $CI =& get_instance();
  $CI->load->model('users/profile_model');
  $profile = $CI->profile_model->get_profiles();

  if(!empty($profile))
  {
    foreach($profile as $rs)
    {
      $sc .= '<option value="'.$rs->id.'" '.is_selected($id, $rs->id).'>'.$rs->name.'</option>';
    }
  }

  return $sc;

}


 ?>

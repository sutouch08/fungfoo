<?php
function _check_login()
{
  $CI =& get_instance();
  $uid = get_cookie('uid');
  if($uid === NULL OR $CI->user_model->verify_uid($uid) === FALSE)
  {
    redirect(base_url().'users/authentication');
  }
}


function get_permission($menu, $uid = NULL, $id_profile = NULL)
{
  $CI =& get_instance();

  $uid = $uid === NULL ? get_cookie('uid') : $uid;
  $id_profile = $id_profile === NULL ? get_cookie('id_profile') : $id_profile;

  $pm = $CI->user_model->get_permission($menu, $uid, $id_profile);
  if(empty($pm))
  {
    $pm = new stdClass();
    $pm->can_view = 0;
    $pm->can_add = 0;
    $pm->can_edit = 0;
    $pm->can_delete = 0;
    $pm->can_approve = 0;
  }

  return $pm;

}


function _can_view_page($can_view)
{
  if( ! $can_view)
  {
    $CI =& get_instance();
    $CI->load->view('deny_page');
    //redirect('deny_page');
  }
}


function profile_name_in($text)
{
  if($text !== '')
  {
    $CI =& get_instance();
    $CI->db->select('id');
  }
}




function user_in($txt)
{
  $sc = array('0');
  $CI =& get_instance();
  $CI->load->model('users/user_model');
  $users = $CI->user_model->search($txt);

  if(!empty($users))
  {
    foreach($users as $rs)
    {
      $sc[] = $rs->uname;
    }
  }

  return $sc;
}


function get_display_name($uname)
{
  $CI =& get_instance();
  $name = $CI->user_model->get_name($uname);
  return $name;
}



 ?>

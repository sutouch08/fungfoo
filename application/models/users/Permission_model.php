<?php
class Permission_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db->insert('permission', $ds);
    }
  }


  public function get_permission($menu, $id_profile)
  {
    $this->db->where('menu', $menu)->where('id_profile', $id_profile);
    $rs = $this->db->get('permission');
    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }
    else
    {
      $ds = new stdClass();
      $ds->can_view = 0;
      $ds->can_add = 0;
      $ds->can_edit = 0;
      $ds->can_delete = 0;
      $ds->can_approve = 0;

      return $ds;
    }
  }



  public function drop_profile_permission($id)
  {
    $this->db->where('id_profile', $id);
    return $this->db->delete('permission');
  }

}

 ?>

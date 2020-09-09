<?php
class Profile_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }



  public function add($name)
  {
    return $this->db->insert('profile', array('name' => $name));
  }




  public function update($id, $name)
  {
    return $this->db->where('id', $id)->update('profile', array('name' => $name));
  }



  public function delete($id)
  {
    return $this->db->where('id', $id)->delete('profile');
  }




  public function is_extsts($name, $id = '')
  {
    if($id !== '')
    {
      $this->db->where('id !=', $id);
    }

    $rs = $this->db->where('name', $name)->get('profile');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }





  public function count_members($id)
  {
    $this->db->select('id');
    $this->db->where('id_profile', $id);
    $rs = $this->db->get('user');
    return $rs->num_rows();
  }





  public function get_profile($id)
  {
    $rs = $this->db->where('id', $id)->get('profile');
    return $rs->row();
  }




  public function get_profiles($name = '', $perpage = 0, $offset = 0)
  {
    if($name != '')
    {
      $this->db->like('name', $name);
    }

    if($perpage > 0)
    {
      $offset = $offset === NULL ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }

    $rs = $this->db->get('profile');
    return $rs->result();
  }



  public function count_rows($name = '')
  {
    $this->db->select('id');
    if($name !== '')
    {
      $this->db->like('name', $name);
    }

    $rs = $this->db->get('profile');

    return $rs->num_rows();
  }

} //--- End class


 ?>

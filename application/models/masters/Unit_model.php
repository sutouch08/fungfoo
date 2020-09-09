<?php
class Unit_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get('unit');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get_data()
  {
    $rs = $this->db->get('unit');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }

} //--- end class

 ?>

<?php
class Bank_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get_active_bank()
  {
    $rs = $this->db->where('active', 1)->get('bank_account');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return array();
  }



  public function get_data()
  {
    $rs = $this->db->get('bank_account');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return array();
  }


  public function get_account_detail($id)
  {
    $rs = $this->db->where('id', $id)->get('bank_account');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


} //---- End class


 ?>

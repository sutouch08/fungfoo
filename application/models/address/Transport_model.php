<?php
class Transport_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function count_sender($code)
  {
    $sd = 0;
    $rs = $this->db->where('customer_code', $code)->get('address_transport');
    if($rs->num_rows() == 1)
    {
      $sd += 1;
      $sc = $rs->row()->second_sender === NULL ? 0 : 1;
      $td = $rs->row()->third_sender === NULL ? 0 : 1;
      $sc = $sd + $sc + $td;
    }

    return $sd;
  }


  public function get_sender($id)
  {
    $rs = $this->db->where('id', $id)->get('address_sender');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get_senders($code)
  {
    $rs = $this->db->where('customer_code', $code)->get('address_transport');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get_name($id)
  {
    $rs = $this->db->where('id', $id)->get('address_sender');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }


  public function get_id($code)
  {
    $rs = $this->db->select('main_sender')->where('customer_code', $code)->get('address_transport');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->main_sender;
    }

    return FALSE;
  }

}
 ?>

<?php
class Lend_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get('orders');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }

  public function add_detail(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('order_lend_detail', $ds);
    }

    return FALSE;
  }


  public function get_backlogs_list($code)
  {
    $rs = $this->db->where('order_code', $code)->get('order_lend_detail');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function is_received($code)
  {
    $rs = $this->db
    ->select('id')
    ->where('order_code', $code)
    ->where('receive >', 0 )
    ->get('order_lend_detail');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }

  public function drop_backlogs_list($code)
  {
    return $this->db->where('order_code', $code)->delete('order_lend_detail');
  }

} //--- End class


 ?>

<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Order_state_model extends CI_Model
{

  public function __construct()
  {
    parent::__construct();
  }



  public function add_state(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('order_state_change', $ds);
    }

    return FALSE;
  }



  public function get_order_state($code)
  {
    $rs = $this->db->where('order_code', $code)->get('order_state_change');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return array();
  }


}//--- end class
?>

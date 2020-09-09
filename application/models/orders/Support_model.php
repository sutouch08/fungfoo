<?php
class Support_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get_budget($code)
  {
    $this->ms
    ->select('Balance, DNotesBal, OrdersBal, CreditLine')
    ->where('CardType', 'C')
    ->where('CardCode', $code);
    $rs = $this->ms->get('OCRD');
    if($rs->num_rows() === 1)
    {
      $amount = $rs->row()->CreditLine - ($rs->row()->Balance + $rs->row()->DNotesBal + $rs->row()->OrdersBal);
      return $amount;
    }

    return 0;
  }



  public function get_budget_used($code)
  {
    $rs = $this->db
    ->select_sum('total_amount')
    ->from('order_details')
    ->join('orders', 'orders.code = order_details.order_code', 'left')
    ->where('orders.role', 'U')
    ->where('orders.customer_code', $code)
    ->where('order_details.is_complete', 0)
    ->where('orders.is_expired', 0)
    ->get();

    return is_null($rs->row()->total_amount) ? 0 : $rs->row()->total_amount;
  }



} //--- end class

 ?>

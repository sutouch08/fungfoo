<?php
class Sales_report_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get_order_sold_by_date_upd(array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db->select('sold.date_add,sold.date_upd, sold.reference, ch.name AS channels, pm.name AS payment');
      $this->db->select('cus.name AS customer_name, sold.customer_ref');
      $this->db->select('sold.product_code, sold.product_name');
      $this->db->select('sold.price, sold.sell, sold.qty, sold.discount_label, sold.discount_amount, sold.total_amount');
      $this->db->select('credit.paid, credit.balance');
      $this->db->from('order_sold AS sold');
      $this->db->join('channels AS ch', 'sold.channels_code = ch.code', 'left');
      $this->db->join('payment_method AS pm', 'sold.payment_code = pm.code', 'left');
      $this->db->join('customers AS cus', 'sold.customer_code = cus.code', 'left');
      $this->db->join('order_credit AS credit', 'sold.reference = credit.order_code', 'left');
      $this->db->where('sold.role', 'S');
      $this->db->where('sold.date_upd >=', $ds['fromDate']);
      $this->db->where('sold.date_upd <=', $ds['toDate']);

      if(empty($ds['allCustomer']) && !empty($ds['cusFrom']) && !empty($ds['cusTo']) )
      {
        $this->db->where('sold.customer_code >=', $ds['cusFrom']);
        $this->db->where('sold.customer_code <=', $ds['cusTo']);
      }

      if(empty($ds['allProduct']) && !empty($ds['pdFrom']) && !empty($ds['pdTo']))
      {
        $this->db->where('sold.product_code >=', $ds['pdFrom']);
        $this->db->where('sold.product_code <=', $ds['pdTo']);
      }

      $this->db->order_by('sold.customer_code', 'ASC');
      $this->db->order_by('sold.reference', 'ASC');

      $rs = $this->db->get();
      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }

      return FALSE;
    }

    return FALSE;
  }


  public function get_order_sold_by_customer_and_payment(array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db->select('sold.date_add,sold.date_upd, sold.reference, ch.name AS channels, pm.name AS payment');
      $this->db->select('cus.name AS customer_name, sold.customer_ref');
      $this->db->select_sum('sold.total_amount', 'total_amount');
      $this->db->select('credit.paid, credit.balance');
      $this->db->from('order_sold AS sold');
      $this->db->join('channels AS ch', 'sold.channels_code = ch.code', 'left');
      $this->db->join('payment_method AS pm', 'sold.payment_code = pm.code', 'left');
      $this->db->join('customers AS cus', 'sold.customer_code = cus.code', 'left');
      $this->db->join('order_credit AS credit', 'sold.reference = credit.order_code', 'left');
      $this->db->where('sold.role', 'S');
      $this->db->where('sold.date_upd >=', $ds['fromDate']);
      $this->db->where('sold.date_upd <=', $ds['toDate']);

      if(empty($ds['allCustomer']) && !empty($ds['cusFrom']) && !empty($ds['cusTo']) )
      {
        $this->db->where('sold.customer_code >=', $ds['cusFrom']);
        $this->db->where('sold.customer_code <=', $ds['cusTo']);
      }

      $this->db->group_by('sold.reference');

      $this->db->order_by('sold.customer_code', 'ASC');
      $this->db->order_by('sold.reference', 'ASC');

      $rs = $this->db->get();
      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }

      return FALSE;
    }

    return FALSE;
  }


} //--- end class
?>

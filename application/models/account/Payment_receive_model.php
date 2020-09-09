<?php
class Payment_receive_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function add(array $ds = array())
  {
    return $this->db->insert('payment_receive', $ds);
  }



  public function count_rows(array $ds = array())
  {
    $this->db
    ->from('payment_receive AS pm')
    ->join('customers AS cs', 'pm.customer_code = cs.code', 'left')
    ->join('payment_type AS pt', 'pm.payment_type = pt.code', 'left');

    if(!empty($ds['code']))
    {
      $this->db->like('pm.reference', $ds['code']);
    }

    if(!empty($ds['customer']))
    {
      $this->db->group_start();
      $this->db->like('cs.code', $ds['customer']);
      $this->db->or_like('cs.name', $ds['customer']);
      $this->db->group_end();
    }

    if(!empty($ds['pay_type']))
    {
      $this->db->where('pm.payment_type', $ds['pay_type']);
    }


    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('pay_date >=', from_date($ds['from_date']));
      $this->db->where('pay_date <=', to_date($ds['to_date']));
    }

    return $this->db->count_all_results();
  }


  public function get_list(array $ds = array(), $perpage = NULL, $offset = NULL)
  {
    $this->db
    ->select('pm.*')
    ->select('pt.name AS pay_type')
    ->select('cs.name AS customer_name')
    ->from('payment_receive AS pm')
    ->join('customers AS cs', 'pm.customer_code = cs.code', 'left')
    ->join('payment_type AS pt', 'pm.payment_type = pt.code', 'left');

    if(!empty($ds['code']))
    {
      $this->db->like('pm.reference', $ds['code']);
    }

    if(!empty($ds['customer']))
    {
      $this->db->group_start();
      $this->db->like('cs.code', $ds['customer']);
      $this->db->or_like('cs.name', $ds['customer']);
      $this->db->group_end();
    }

    if(!empty($ds['pay_type']))
    {
      $this->db->where('pm.payment_type', $ds['pay_type']);
    }


    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('pay_date >=', from_date($ds['from_date']));
      $this->db->where('pay_date <=', to_date($ds['to_date']));
    }

    $this->db->order_by('pay_date', 'DESC');

    if(!empty($perpage))
    {
      $offset = empty($offset) ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }


    return FALSE;
  }

} //--- end class
 ?>

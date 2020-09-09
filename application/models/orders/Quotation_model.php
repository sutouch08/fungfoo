<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Quotation_model extends CI_Model
{

  public function __construct()
  {
    parent::__construct();
  }

  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get('order_quotation');
    if(!empty($rs))
    {
      return $rs->row();
    }

    return FALSE;
  }



  public function get_detail($code, $pd_code)
  {
    $rs = $this->db
    ->where('quotation_code', $code)
    ->where('product_code', $pd_code)
    ->get('order_quotation_detail');

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return FALSE;
  }



  public function get_details($code)
  {
    $rs = $this->db->where('quotation_code', $code)->get('order_quotation_detail');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('order_quotation', $ds);
    }

    return FALSE;
  }


  public function add_detail(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('order_quotation_detail', $ds);
    }

    return FALSE;
  }



  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('code', $code)->update('order_quotation', $ds);
    }

    return FALSE;
  }



  public function update_detail($id, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('id', $id)->update('order_quotation_detail', $ds);
    }

    return FALSE;
  }


  public function count_rows($ds = array())
  {
    $this->db
    ->from('order_quotation')
    ->join('customers', 'customers.code = order_quotation.customer_code', 'left')
    ->join('user', 'user.uname = order_quotation.user', 'left');

    if(!empty($ds['code']))
    {
      $this->db->like('order_quotation.code', $ds['code']);
    }

    if(!empty($ds['customer_code']))
    {
      $this->db->group_start();
      $this->db->like('customers.code', $ds['customer_code']);
      $this->db->or_like('customers.name', $ds['customer_code']);
      $this->db->group_end();
    }

    if(!empty($ds['contact']))
    {
      $this->db->like('order_quotation.contact', $ds['contact']);
    }

    if(!empty($ds['user']))
    {
      $this->db->group_start();
      $this->db->like('user.uname', $ds['user']);
      $this->db->or_like('user.name', $ds['user']);
      $this->db->group_end();
    }

    if(!empty($ds['reference']))
    {
      $this->db->like('order_quotation.reference', $ds['reference']);
    }

    if(!empty($ds['from_date']) && ! empty($ds['to_date']))
    {
      $this->db->where('order_quotation.date_add >=', from_date($ds['from_date']));
      $this->db->where('order_quotation.date_add <=', to_date($ds['to_date']));
    }

    return $this->db->count_all_results();
  }



  public function get_list($ds = array(), $perpage = 50, $offset =  0)
  {
    $this->db
    ->select('order_quotation.*, customers.name AS customer_name')
    ->from('order_quotation')
    ->join('customers', 'customers.code = order_quotation.customer_code', 'left')
    ->join('user', 'user.uname = order_quotation.user', 'left');

    if(!empty($ds['code']))
    {
      $this->db->like('order_quotation.code', $ds['code']);
    }

    if(!empty($ds['customer_code']))
    {
      $this->db->group_start();
      $this->db->like('customers.code', $ds['customer_code']);
      $this->db->or_like('customers.name', $ds['customer_code']);
      $this->db->group_end();
    }

    if(!empty($ds['contact']))
    {
      $this->db->like('order_quotation.contact', $ds['contact']);
    }

    if(!empty($ds['user']))
    {
      $this->db->group_start();
      $this->db->like('user.uname', $ds['user']);
      $this->db->or_like('user.name', $ds['user']);
      $this->db->group_end();
    }

    if(!empty($ds['reference']))
    {
      $this->db->like('order_quotation.reference', $ds['reference']);
    }

    if(!empty($ds['from_date']) && ! empty($ds['to_date']))
    {
      $this->db->where('order_quotation.date_add >=', from_date($ds['from_date']));
      $this->db->where('order_quotation.date_add <=', to_date($ds['to_date']));
    }

    $this->db->order_by('order_quotation.code', 'DESC');
    $this->db->limit($perpage, $offset);

    $rs = $this->db->get();

    return $rs->result();
  }



  public function get_sum_total_amount($code)
  {
    $rs = $this->db
    ->select_sum('total_amount')
    ->where('quotation_code', $code)
    ->get('order_quotation_detail');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->total_amount === NULL ? 0 : $rs->row()->total_amount;
    }

    return 0;
  }


  public function get_max_code($pre)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $pre, 'after')
    ->order_by('code', 'DESC')
    ->get('order_quotation');

    return $rs->row()->code;
  }




} //--- End class


 ?>

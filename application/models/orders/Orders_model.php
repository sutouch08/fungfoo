<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Orders_model extends CI_Model
{

  public function __construct()
  {
    parent::__construct();
  }



  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('orders', $ds);
    }

    return FALSE;
  }



  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('code', $code)->update('orders', $ds);
    }

    return FALSE;
  }


  public function update_order_total_amount($code, $amount)
  {
    return $this->db->set('total_amount', $amount)->where('code', $code)->update('orders');
  }


  public function recal_order_balance($code)
  {
    return $this->db->set('balance', 'total_amount - deposit', FALSE)->where('code', $code)->update('orders');
  }

  public function update_deposit($code, $amount)
  {
    $rs = $this->db->set('deposit', "deposit + {$amount}", FALSE)->where('code', $code)->update('orders');
    if($rs)
    {
      return $this->recal_order_balance($code);
    }

    return FALSE;
  }

  public function get_order_deposit($code)
  {
    $rs = $this->db->select('deposit')->where('code', $code)->get('orders');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->deposit;
    }

    return 0;
  }


  public function get_order_balance($code)
  {
    $rs = $this->db->select('balance')->where('code', $code)->get('orders');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->balance;
    }

    return 0;
  }





  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get('orders');
    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



  public function add_detail(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('order_details', $ds);
    }

    return FALSE;
  }




  public function update_detail($id, array $ds = array())
  {
    return $this->db->where('id', $id)->update('order_details', $ds);
  }




  public function remove_detail($id)
  {
    return $this->db->where('id', $id)->delete('order_details');
  }




  public function is_exists_detail($order_code, $item_code)
  {
    $rs = $this->db->select('id')
    ->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->get('order_details');
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }




  public function get_order_detail($order_code, $item_code)
  {
    $rs = $this->db
    ->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->get('order_details');
    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



  public function get_detail($id)
  {
    $rs = $this->db->where('id', $id)->get('order_details');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }




  public function get_order_details($code)
  {
    $rs = $this->db->where('order_code', $code)->get('order_details');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function get_unvalid_details($code)
  {
    $rs = $this->db
    ->where('order_code', $code)
    ->where('valid', 0)
    ->where('is_count', 1)
    ->get('order_details');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function get_valid_details($code)
  {
    $qr  = "SELECT * FROM order_details
            WHERE order_code = '{$code}'
            AND (valid = 1 OR is_count = 0)";
    $rs = $this->db->query($qr);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function get_state($code)
  {
    $rs = $this->db->select('state')->where('code', $code)->get('orders');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->state;
    }

    return FALSE;
  }



  public function get_order_code_by_reference($reference)
  {
    $rs = $this->db->select('code')->where('reference', $reference)->get('orders');
    if($rs->num_rows() == 1)
    {
      return $rs->row()->code;
    }

    return FALSE;
  }



  public function valid_detail($id)
  {
    return $this->db->set('valid', 1)->where('id', $id)->update('order_details');
  }



  public function valid_all_details($code)
  {
    return $this->db->set('valid', 1)->where('order_code', $code)->update('order_details');
  }


  public function unvalid_detail($id)
  {
    return $this->db->set('valid', 0)->where('id', $id)->update('order_details');
  }


  public function change_state($code, $state)
  {
    $arr = array(
      'state' => $state,
      'update_user' => get_cookie('uname')
    );

    return $this->db->where('code', $code)->update('orders', $arr);
  }




  public function update_shipping_code($code, $ship_code)
  {
    return $this->db->set('shipping_code', $ship_code)->where('code', $code)->update('orders');
  }


  public function update_shipping_fee($code, $amount)
  {
    return $this->db->set('shipping_fee', $amount, FALSE)->where('code', $code)->update('orders');
  }


  public function update_service_fee($code, $amount)
  {
    return $this->db->set('service_fee', $amount, FALSE)->where('code', $code)->update('orders');
  }



  public function set_never_expire($code, $option)
  {
    return $this->db->set('never_expire', $option)->where('code', $code)->update('orders');
  }


  public function un_expired($code)
  {
    $this->db->trans_start();
    $this->db->set('is_expired', 0)->where('order_code', $code)->update('order_details');
    $this->db->set('is_expired', 0)->where('code', $code)->update('orders');
    $this->db->trans_complete();

    if($this->db->trans_status() === FALSE)
    {
      return FALSE;
    }

    return TRUE;
  }


  public function set_completed($code)
  {
    return $this->db->set('is_complete', 1)->where('order_code', $code)->update('order_details');
  }



  public function un_complete($code)
  {
    return $this->db->set('is_complete', 0)->where('order_code', $code)->update('order_details');
  }


  public function paid($code, $paid)
  {
    $paid = $paid === TRUE ? 1 : 0;
    return $this->db->set('is_paid', $paid)->where('code', $code)->update('orders');
  }



  public function count_rows(array $ds = array(), $role = 'S')
  {
    $this->db->where('role', $role);

    //---- เลขที่เอกสาร
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    //--- รหัส/ชื่อ ลูกค้า
    if( ! empty($ds['customer']))
    {
      $customers = customer_in($ds['customer']);
      $this->db->where_in('customer_code', $customers);
    }

    //---- user name / display name
    if( ! empty($ds['user']))
    {
      $users = user_in($ds['user']);
      $this->db->where_in('user', $users);
    }

    //---- เลขที่อ้างอิงออเดอร์ภายนอก
    if( ! empty($ds['reference']))
    {
      $this->db->like('reference', $ds['reference']);
    }

    //---เลขที่จัดส่ง
    if( ! empty($ds['ship_code']))
    {
      $this->db->like('shipping_code', $ds['ship_code']);
    }

    //--- ช่องทางการขาย
    if( ! empty($ds['channels']))
    {
      $this->db->where('channels_code', $ds['channels']);
    }

    //--- ช่องทางการชำระเงิน
    if( ! empty($ds['payment']))
    {
      $this->db->where('payment_code', $ds['payment']);
    }


    if( ! empty($ds['zone_code']))
    {
      $zone_in = get_zone_in($ds['zone_code']);
      $this->db->where_in('zone_code', $zone_in);
    }

    if( !empty($ds['user_ref']))
    {
      $this->db->like('user_ref', $ds['user_ref']);
    }


    if(! empty($ds['is_paid']))
    {
      if($ds['is_paid'] !== 'all')
      {
        $is_paid = $ds['is_paid'] == 'paid' ? 1 : 0;
        $this->db->where('is_paid', $is_paid);
      }
    }


    if( ! empty($ds['from_date']) && ! empty($ds['to_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    return $this->db->count_all_results('orders');
  }





  public function get_data(array $ds = array(), $perpage = '', $offset = '', $role = 'S')
  {
      $this->db->where('role', $role);

      //---- เลขที่เอกสาร
      if( ! empty($ds['code']))
      {
        $this->db->like('code', $ds['code']);
      }

      //--- รหัส/ชื่อ ลูกค้า
      if( ! empty($ds['customer']))
      {
        $customers = customer_in($ds['customer']);
        $this->db->group_start();
        $this->db->where_in('customer_code', $customers);
        $this->db->or_like('customer_ref', $ds['customer']);
        $this->db->group_end();
      }

      //---- user name / display name
      if( ! empty($ds['user']))
      {
        $users = user_in($ds['user']);
        $this->db->where_in('user', $users);
      }

      //---- เลขที่อ้างอิงออเดอร์ภายนอก
      if( ! empty($ds['reference']))
      {
        $this->db->like('reference', $ds['reference']);
      }

      //---เลขที่จัดส่ง
      if( ! empty($ds['ship_code']))
      {
        $this->db->like('shipping_code', $ds['ship_code']);
      }

      //--- ช่องทางการขาย
      if( ! empty($ds['channels']))
      {
        $this->db->where('channels_code', $ds['channels']);
      }

      //--- ช่องทางการชำระเงิน
      if( ! empty($ds['payment']))
      {
        $this->db->where('payment_code', $ds['payment']);
      }


      if( ! empty($ds['zone_code']))
      {
        $zone_in = get_zone_in($ds['zone_code']);
        $this->db->where_in('zone_code', $zone_in);
      }

      if( !empty($ds['user_ref']))
      {
        $this->db->like('user_ref', $ds['user_ref']);
      }

      if(! empty($ds['is_paid']))
      {
        if($ds['is_paid'] !== 'all')
        {
          $is_paid = $ds['is_paid'] == 'paid' ? 1 : 0;
          $this->db->where('is_paid', $is_paid);
        }
      }

      if( ! empty($ds['from_date']) && ! empty($ds['to_date']))
      {
        $this->db->where('date_add >=', from_date($ds['from_date']));
        $this->db->where('date_add <=', to_date($ds['to_date']));
      }

      $order_by = empty($ds['order_by']) ? "orders.code" : "orders.{$ds['order_by']}";
      $sort_by = empty($ds['sort_by']) ? "ASC" : $ds['sort_by'];
      $this->db->order_by($order_by, $sort_by);

      if($perpage != '')
      {
        $offset = $offset === NULL ? 0 : $offset;
        $this->db->limit($perpage, $offset);
      }

      $rs = $this->db->get('orders');
      //echo $this->db->get_compiled_select('orders');
      return $rs->result();
  }





  public function get_max_code($code)
  {
    $qr = "SELECT MAX(code) AS code FROM orders WHERE code LIKE '".$code."%' ORDER BY code DESC";
    $rs = $this->db->query($qr);
    return $rs->row()->code;
  }




  public function get_order_total_amount($code)
  {
    $this->db->select_sum('total_amount', 'amount');
    $this->db->where('order_code', $code);
    $rs = $this->db->get('order_details');
    return $rs->row()->amount;
  }


  public function get_bill_total_amount($code)
  {
    $rs = $this->db
    ->select_sum('total_amount', 'amount')
    ->where('reference', $code)
    ->get('order_sold');

    return $rs->row()->amount;
  }



  public function get_order_total_qty($code)
  {
    $this->db->select_sum('qty', 'qty');
    $this->db->where('order_code', $code);
    $rs = $this->db->get('order_details');
    return $rs->row()->qty;
  }


  //--- ใช้คำนวนยอดเครดิตคงเหลือ
  public function get_sum_not_complete_amount($customer_code)
  {
    $rs = $this->db
    ->select_sum('order_details.total_amount', 'amount')
    ->from('order_details')
    ->join('orders', 'orders.code = order_details.order_code', 'left')
    ->where_in('orders.role', array('S', 'C', 'N'))
    ->where('orders.customer_code', $customer_code)
    ->where('order_details.is_complete', 0)
    ->where('orders.is_expired', 0)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row()->amount;
    }

    return 0.00;
  }



  public function get_bill_discount($code)
  {
    $rs = $this->db->select('bDiscAmount')
    ->where('code', $code)
    ->get('orders');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->bDiscAmount;
    }

    return 0;
  }


  public function get_sum_style_qty($order_code, $style_code)
  {
    $rs = $this->db->select_sum('qty')
    ->where('order_code', $order_code)
    ->where('style_code', $style_code)
    ->get('order_detils');

    return $rs->row()->qty;
  }




  public function get_reserv_stock($item_code)
  {
    $rs = $this->db->select_sum('qty')
    ->where('product_code', $item_code)
    ->where('is_complete', 0)
    ->where('is_expired', 0)
    ->where('is_count', 1)
    ->get('order_details');

    if($rs->num_rows() == 1)
    {
      return $rs->row()->qty;
    }

    return 0;
  }



  public function get_reserv_stock_by_style($style_code)
  {
    $rs = $this->db->select_sum('qty')
    ->where('style_code', $style_code)
    ->where('is_complete', 0)
    ->where('is_expired', 0)
    ->where('is_count', 1)
    ->get('order_details');
    if($rs->num_rows() == 1)
    {
      return $rs->row()->qty;
    }

    return 0;
  }


  public function set_status($code, $status)
  {
    return $this->db->set('status', $status)->where('code', $code)->update('orders');
  }


  public function clear_order_detail($code)
  {
    return $this->db->where('order_code', $code)->delete('order_details');
  }


  public function set_address_id($code, $id_address)
  {
    return $this->db->set('address_id', $id_address)->where('code', $code)->update('orders');
  }


} //--- End class


 ?>

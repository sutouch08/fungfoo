<?php
class Buffer_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get_data(array $ds = array(), $perpage = NULL, $offset = NULL)
  {
    $this->db
    ->select('buffer.*')
    ->select('zone.name AS zone_name')
    ->select('order_state.name AS state_name')
    ->from('buffer')
    ->join('zone', 'buffer.zone_code = zone.code', 'left')
    ->join('orders', 'buffer.order_code = orders.code', 'left')
    ->join('order_state', 'orders.state = order_state.state');

    if(!empty($ds['order_code']))
    {
      $this->db->like('buffer.order_code',$ds['order_code']);
    }

    if(!empty($ds['pd_code']))
    {
      $this->db->like('buffer.product_code', $ds['pd_code']);
    }

    if(!empty($ds['zone_code']))
    {
      $this->db->group_start();
      $this->db->like('buffer.zone_code', $ds['zone_code']);
      $this->db->or_like('zone.name', $ds['zone_code']);
      $this->db->group_end();
    }

    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('buffer.date_upd >=', from_date($ds['from_date']));
      $this->db->where('buffer.date_upd <=', to_date($ds['to_date']));
    }

    if($perpage > 0)
    {
      $offset = $offset === NULL ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function count_rows(array $ds = array(), $perpage = NULL, $offset = NULL)
  {
    $this->db
    ->from('buffer')
    ->join('zone', 'buffer.zone_code = zone.code', 'left')
    ->join('orders', 'buffer.order_code = orders.code', 'left')
    ->join('order_state', 'orders.state = order_state.state');

    if(!empty($ds['order_code']))
    {
      $this->db->like('buffer.order_code',$ds['order_code']);
    }

    if(!empty($ds['pd_code']))
    {
      $this->db->like('buffer.product_code', $ds['pd_code']);
    }

    if(!empty($ds['zone_code']))
    {
      $this->db->group_start();
      $this->db->like('buffer.zone_code', $ds['zone_code']);
      $this->db->or_like('zone.name', $ds['zone_code']);
      $this->db->group_end();
    }

    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('buffer.date_upd >=', from_date($ds['from_date']));
      $this->db->where('buffer.date_upd <=', to_date($ds['to_date']));
    }

    return $this->db->count_all_results();
  }


  public function get_sum_buffer_product($order_code, $product_code)
  {
    $rs = $this->db->select_sum('qty')
    ->where('order_code', $order_code)
    ->where('product_code', $product_code)
    ->get('buffer');

    return intval($rs->row()->qty);
  }


  public function get_details($order_code, $product_code)
  {
    $rs = $this->db
    ->where('order_code', $order_code)
    ->where('product_code', $product_code)
    ->get('buffer');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function get_all_details($order_code)
  {
    $rs = $this->db->where('order_code', $order_code)->get('buffer');
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
      return $this->db->insert('buffer', $ds);
    }

    return FALSE;
  }


  public function update($order_code, $product_code, $zone_code, $qty)
  {
    $qr = "UPDATE buffer SET qty = (qty + {$qty}) ";
    $qr .= "WHERE order_code = '{$order_code}' AND product_code = '{$product_code}' ";
    $qr .= "AND zone_code = '{$zone_code}'";

    return $this->db->query($qr);
  }


  public function delete($id)
  {
    return $this->db->where('id', $id)->delete('buffer');
  }



  public function delete_all($code)
  {
    return $this->db->where('order_code', $code)->delete('buffer');
  }


  public function is_exists($order_code, $product_code, $zone_code)
  {
    $rs = $this->db->select('id')
    ->where('order_code', $order_code)
    ->where('product_code', $product_code)
    ->where('zone_code', $zone_code)
    ->get('buffer');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function remove_zero_buffer($code)
  {
    return $this->db->where('order_code', $code)->where('qty', 0)->delete('buffer');
  }
}
 ?>

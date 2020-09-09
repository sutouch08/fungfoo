<?php
class Transform_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function add($order_code)
  {
    return $this->db->insert('order_transform', array('order_code' => $order_code));
  }


  public function get_transform_product($id_order_detail)
  {
    $rs = $this->db->where('id_order_detail', $id_order_detail)->get('order_transform_detail');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function get_transform_product_by_code($order_code, $product_code)
  {
    $rs = $this->db
    ->where('order_code', $order_code)
    ->where('product_code', $product_code)
    ->get('order_transform_detail');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }




  public function update_receive_qty($id, $qty)
  {
    return $this->db->set("receive_qty", "receive_qty + {$qty}", FALSE)->where('id', $id)->update('order_transform_detail');
  }



  public function reset_sold_qty($order_code)
  {
    return  $this->db->set('sold_qty', 0)->where('order_code', $order_code)->update('order_transform_detail');
  }



  public function hasTransformProduct($id_order_detail)
  {
    $rs = $this->db->where('id_order_detail', $id_order_detail)->get('order_transform_detail');
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;

  }


  public function get_sum_transform_product_qty($id_order_detail)
  {
    $rs = $this->db
    ->select_sum('order_qty', 'qty')
    ->where('id_order_detail', $id_order_detail)
    ->get('order_transform_detail');

    return intval($rs->row()->qty);
  }



  public function is_received($order_code)
  {
    $rs = $this->db
    ->where('receive_qty >', 0)
    ->where('order_code', $order_code)
    ->limit(1)
    ->get('order_transform_detail');
    if($rs->num_rows() === 1)
    {
      return TRUE;
    }

    return FALSE;
  }

  public function is_exists($id_order_detail, $product_code)
  {
    $rs = $this->db->select('id')
    ->where('id_order_detail', $id_order_detail)
    ->where('product_code', $product_code)
    ->get('order_transform_detail');
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function is_complete($order_code)
  {
    $rs = $this->db
    ->where('order_code', $order_code)
    ->where('valid', 0)
    ->count_all_results('order_transform_detail');
    if($rs === 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function update(array $ds = array())
  {
    if(!empty($ds))
    {
      if($this->is_exists($ds['id_order_detail'], $ds['product_code']))
      {
        return $this->update_detail($ds['id_order_detail'], $ds['product_code'], $ds['order_qty']);
      }
      else
      {
        return $this->add_detail($ds);
      }
    }

    return FALSE;
  }



  public function update_sold_qty($id, $qty)
  {
    $rs = $this->db
    ->set("sold_qty", "sold_qty + {$qty}", FALSE)
    ->set('valid', 1)
    ->where('id', $id)
    ->update('order_transform_detail');

    return $rs;
  }



  public function valid_detail($id)
  {
    return $this->db->set('valid', 1)->where('id', $id)->update('order_transform_detail');
  }




  public function add_detail(array $ds = array())
  {
    return $this->db->insert('order_transform_detail', $ds);
  }


  public function update_detail($id_order_detail, $product_code, $order_qty)
  {
    $rs = $this->db
    ->set("order_qty", "order_qty + {$order_qty}", FALSE)
    ->where('id_order_detail', $id_order_detail)
    ->where('product_code', $product_code)
    ->update('order_transform_detail');

    return $rs;
  }


  public function remove_transform_product($id_order_detail, $product_code)
  {
    return $this->db
    ->where('id_order_detail', $id_order_detail)
    ->where('product_code', $product_code)
    ->delete('order_transform_detail');
  }


  public function remove_transform_detail($id_order_detail)
  {
    return $this->db->where('id_order_detail', $id_order_detail)->delete('order_transform_detail');
  }



  public function clear_transform_detail($code)
  {
    return $this->db->where('order_code', $code)->delete('order_transform_detail');
  }


  public function close_transform($code)
  {
    return $this->db->set('is_closed', 1)->where('order_code', $code)->update('order_transform');
  }


} //--- end class

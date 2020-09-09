<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class stock_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get_data(array $ds=array(), $perpage = NULL, $offset = NULL)
  {
    if(!empty($ds))
    {
      if(!empty($ds['pd_code']) OR !empty($ds['zone_code']))
      {
        $this->db
        ->select('st.*, zone.name')
        ->from('stock AS st')
        ->join('zone', 'st.zone_code = zone.code', 'left');

        if(!empty($ds['pd_code']))
        {
          $this->db->like('st.product_code', $ds['pd_code']);
        }

        if(!empty($ds['zone_code']))
        {
          $this->db->group_start();
          $this->db->like('zone.code', $ds['zone_code']);
          $this->db->or_like('zone.name', $ds['zone_code']);
          $this->db->group_end();
        }

        if($perpage > 0)
        {
          $offset = $offset === NULL ? 0 : $offset;
          $this->db->limit($perpage, $offset);
        }

        $rs = $this->db->get();

        return $rs->result();
      }
    }

    return FALSE;
  }



  public function count_rows(array $ds=array())
  {
    if(!empty($ds))
    {
      if(!empty($ds['pd_code']) OR !empty($ds['zone_code']))
      {
        $this->db
        ->from('stock AS st')
        ->join('zone', 'st.zone_code = zone.code', 'left');

        if(!empty($ds['pd_code']))
        {
          $this->db->like('st.product_code', $ds['pd_code']);
        }

        if(!empty($ds['zone_code']))
        {
          $this->db->group_start();
          $this->db->like('zone.code', $ds['zone_code']);
          $this->db->or_like('zone.name', $ds['zone_code']);
          $this->db->group_end();
        }

        return $this->db->count_all_results();
      }
    }

    return 0;
  }




  public function update_stock_zone($zone_code, $product_code, $qty)
  {
    if(!empty($zone_code) && !empty($product_code) && $qty != 0)
    {
      $id = $this->get_id($zone_code, $product_code);
      if($id === FALSE)
      {
        $arr = array(
          'product_code' => $product_code,
          'zone_code' => $zone_code,
          'qty' => $qty
        );

        return $this->add($arr);
      }
      else
      {
        return $this->update($id, $qty);
      }
    }

    return FALSE;
  }


  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('stock', $ds);
    }

    return FALSE;
  }


  public function update($id, $qty)
  {
    $rs = $this->db->set("qty", "qty + {$qty}", FALSE)->where('id', $id)->update('stock');
    if($rs)
    {
      $this->remove_zero_stock();
    }

    return $rs;
  }


  public function remove_zero_stock()
  {
    return $this->db->where('qty =',0, FALSE)->delete('stock');
  }



  public function get_id($zone_code, $product_code)
  {
    $rs = $this->db
    ->select('id')
    ->where('product_code', $product_code)
    ->where('zone_code', $zone_code)
    ->get('stock');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->id;
    }

    return FALSE;
  }

  public function get_style_sell_stock($style_code)
  {
    $this->db
    ->select_sum('qty', 'qty')
    ->from('stock')
    ->join('products', 'stock.product_code = products.code', 'left')
    ->join('zone', 'stock.zone_code = zone.code', 'left')
    ->join('warehouse', 'zone.warehouse_code = warehouse.code', 'left')
    ->where('warehouse.sell', 1)
    ->where('products.style_code', $style_code);
    $rs = $this->db->get();
    if($rs->num_rows() === 1)
    {
      return $rs->row()->qty === NULL ? 0 : $rs->row()->qty;
    }

    return 0;
  }




  public function get_stock_zone($zone_code, $pd_code)
  {
    $rs = $this->db
    ->select('qty')
    ->where('product_code', $pd_code)
    ->where('zone_code', $zone_code)
    ->get('stock');

    if($rs->num_rows() == 1)
    {
      return $rs->row()->qty === NULL ? 0 : $rs->row()->qty;
    }

    return 0;
  }


  //---- ยอดรวมสินค้าในคลังที่สั่งได้ ยอดในโซน
  public function get_sell_stock($item)
  {
    $rs = $this->db
    ->select_sum('qty', 'qty')
    ->from('stock')
    ->join('zone', 'zone.code = stock.zone_code', 'left')
    ->join('warehouse', 'warehouse.code = zone.warehouse_code', 'left')
    ->where('stock.product_code', $item)
    ->where('warehouse.sell', 1)
    ->get();

    return $rs->row()->qty === NULL ? 0 : $rs->row()->qty;
  }


  //--- ยอดรวมสินค้าทั้งหมดทุกคลัง (รวมฝากขาย)
  public function get_stock($item)
  {
    $rs = $this->db->select_sum('qty', 'qty')->where('product_code', $item)->get('stock');
    return $rs->row()->qty === NULL ? 0 : $rs->row()->qty;
  }


  //---- ยอดสินค้าคงเหลือในแต่ละโซน
  public function get_stock_in_zone($item)
  {
    $rs = $this->db
    ->select('zone_code AS code, qty AS qty')
    ->from('stock')
    ->join('zone', 'zone.code = stock.zone_code', 'left')
    ->join('warehouse', 'warehouse.code = zone.warehouse_code', 'left')
    ->where('warehouse.sell', 1)
    ->where('product_code', $item)
    ->get();

    $result = array();

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $stock)
      {
        $ds = new stdClass();
        $ds->code = $stock->code;
        $ds->name = $stock->code;
        $ds->qty  = $stock->qty;
        $result[] = $ds;
      }
    }

    return $result;
  }


  //---- สินค้าทั้งหมดที่อยู่ในโซน (ใช้โอนสินค้าระหว่างคลัง)
  public function get_all_stock_in_zone($zone_code)
  {
    $rs = $this->db
    ->select('product_code, qty')
    ->where('zone_code', $zone_code)
    ->get('stock');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function is_enough($zone_code, $product_code, $qty)
  {
    $rs = $this->db
    ->select('qty')
    ->where('product_code', $product_code)
    ->where('zone_code', $zone_code)
    ->where('qty >=', $qty, FALSE)
    ->get('stock');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }

}//--- end class

<?php
class Movement_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get_data(array $ds = array(), $perpage = NULL, $offset = NULL)
  {
    if(!empty($ds))
    {
      $this->db
      ->select('mv.*')
      ->select('zn.name AS zone_name')
      ->from('stock_movement AS mv')
      ->join('zone AS zn', 'mv.zone_code = zn.code', 'left')
      ->join('warehouse AS wh', 'mv.warehouse_code = wh.code', 'left');

      if(!empty($ds['reference']))
      {
        $this->db->like('mv.reference', $ds['reference']);
      }

      if(!empty($ds['product_code']))
      {
        $this->db->like('mv.product_code', $ds['product_code']);
      }

      if(!empty($ds['zone_code']))
      {
        $this->db->group_start();
        $this->db->like('zn.code', $ds['zone_code']);
        $this->db->or_like('zn.name', $ds['zone_code']);
        $this->db->group_end();
      }

      if(!empty($ds['warehouse_code']))
      {
        $this->db->group_start();
        $this->db->like('wh.code', $ds['warehouse_code']);
        $this->db->or_like('wh.name', $ds['warehouse_code']);
        $this->db->group_end();
      }

      if(!empty($ds['from_date']) && !empty($ds['to_date']))
      {
        $this->db->where('mv.date_add >=', from_date($ds['from_date']));
        $this->db->where('mv.date_add <=', to_date($ds['to_date']));
      }

      $this->db->order_by('mv.date_add', 'DESC');

      if(!empty($perpage))
      {
        $offset = $offset === NULL ? 0 : $offset;
        $this->db->limit($perpage, $offset);
      }

      $rs = $this->db->get();

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }
    }

    return FALSE;
  }



  public function count_rows(array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db
      ->from('stock_movement AS mv')
      ->join('zone AS zn', 'mv.zone_code = zn.code', 'left')
      ->join('warehouse AS wh', 'mv.warehouse_code = wh.code', 'left');

      if(!empty($ds['reference']))
      {
        $this->db->like('mv.reference', $ds['reference']);
      }

      if(!empty($ds['product_code']))
      {
        $this->db->like('mv.product_code', $ds['product_code']);
      }

      if(!empty($ds['zone_code']))
      {
        $this->db->group_start();
        $this->db->like('zn.code', $ds['zone_code']);
        $this->db->or_like('zn.name', $ds['zone_code']);
        $this->db->group_end();
      }

      if(!empty($ds['warehouse_code']))
      {
        $this->db->group_start();
        $this->db->like('wh.code', $ds['warehouse_code']);
        $this->db->or_like('wh.name', $ds['warehouse_code']);
        $this->db->group_end();
      }

      if(!empty($ds['from_date']) && !empty($ds['to_date']))
      {
        $this->db->where('mv.date_add >=', from_date($ds['from_date']));
        $this->db->where('mv.date_add <=', to_date($ds['to_date']));
      }

      return $this->db->count_all_results();
    }

    return 0;
  }



  public function get_sum_movement(array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db
      ->select_sum('mv.move_in')
      ->select_sum('mv.move_out')
      ->from('stock_movement AS mv')
      ->join('zone AS zn', 'mv.zone_code = zn.code', 'left')
      ->join('warehouse AS wh', 'mv.warehouse_code = wh.code', 'left');

      if(!empty($ds['reference']))
      {
        $this->db->like('mv.reference', $ds['reference']);
      }

      if(!empty($ds['product_code']))
      {
        $this->db->like('mv.product_code', $ds['product_code']);
      }

      if(!empty($ds['zone_code']))
      {
        $this->db->group_start();
        $this->db->like('zn.code', $ds['zone_code']);
        $this->db->or_like('zn.name', $ds['zone_code']);
        $this->db->group_end();
      }

      if(!empty($ds['warehouse_code']))
      {
        $this->db->group_start();
        $this->db->like('wh.code', $ds['warehouse_code']);
        $this->db->or_like('wh.name', $ds['warehouse_code']);
        $this->db->group_end();
      }

      if(!empty($ds['from_date']) && !empty($ds['to_date']))
      {
        $this->db->where('mv.date_add >=', from_date($ds['from_date']));
        $this->db->where('mv.date_add <=', to_date($ds['to_date']));
      }

      $rs = $this->db->get();

      if($rs->num_rows() === 1)
      {
        return $rs->row();
      }
    }

    return FALSE;
  }




  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('stock_movement', $ds);
    }

    return FALSE;
  }



  public function move_in($reference, $product_code, $warehouse_code, $zone_code, $qty, $date_add)
  {
    $id = $this->get_id($reference, $product_code, $zone_code, 'move_in');
    if($id !== FALSE)
    {
      $rs = $this->db->set("move_in", "move_in + {$qty}", FALSE)->where('id', $id)->update('stock_movement');
    }
    else
    {
      $arr = array(
        'reference' => $reference,
        'warehouse_code' => $warehouse_code,
        'zone_code' => $zone_code,
        'product_code' => $product_code,
        'move_in' => $qty,
        'date_add' => $date_add
      );

      $rs = $this->db->insert('stock_movement', $arr);
    }

    $this->drop_zero_movement();

    return $rs;
  }


  public function move_out($reference, $product_code, $warehouse_code, $zone_code, $qty, $date_add)
  {
    $id = $this->get_id($reference, $product_code, $zone_code, 'move_out');
    if($id !== FALSE)
    {
      $rs = $this->db->set("move_out", "move_out + {$qty}", FALSE)->where('id', $id)->update('stock_movement');
    }
    else
    {
      $arr = array(
        'reference' => $reference,
        'warehouse_code' => $warehouse_code,
        'zone_code' => $zone_code,
        'product_code' => $product_code,
        'move_out' => $qty,
        'date_add' => $date_add
      );

      $rs = $this->db->insert('stock_movement', $arr);
    }

    $this->drop_zero_movement();

    return $rs;
  }



  private function get_id($reference, $product_code, $zone_code, $move_type = 'move_in')
  {
    $this->db
    ->select('id')
    ->where('reference', $reference)
    ->where('product_code', $product_code)
    ->where('zone_code', $zone_code);

    if($move_type == 'move_in')
    {
      $this->db->where('move_out', 0)->where('move_in !=', 0);
    }
    else if($move_type == 'move_out')
    {
      $this->db->where('move_in', 0)->where('move_out !=', 0);
    }

    $rs = $this->db->get('stock_movement');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->id;
    }

    return FALSE;
  }



  public function drop_movement($code)
  {
    return $this->db->where('reference', $code)->delete('stock_movement');
  }


  public function drop_move_in($reference, $product_code, $zone_code)
  {
    $this->db
    ->where('reference', $reference)
    ->where('product_code', $product_code)
    ->where('zone_code', $zone_code)
    ->where('move_in >', 0)
    ->where('move_out', 0);
    return $this->db->delete('stock_movement');
  }


  public function drop_move_out($reference, $product_code, $zone_code)
  {
    $this->db
    ->where('reference', $reference)
    ->where('product_code', $product_code)
    ->where('zone_code', $zone_code)
    ->where('move_out >', 0)
    ->where('move_in', 0);
    return $this->db->delete('stock_movement');
  }

  private function drop_zero_movement()
  {
    return $this->db->where('move_in', 0, FALSE)->where('move_out', 0, FALSE)->delete('stock_movement');
  }


} //--- end class

?>

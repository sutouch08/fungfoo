<?php
class Products_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }



  public function count_rows(array $ds = array())
  {

    $this->db
    ->from('products')
    ->join('product_color', 'products.color_code = product_color.code', 'left')
    ->join('product_size', 'products.size_code = product_size.code', 'left');

    if(!empty($ds))
    {
      if(!empty($ds['code']))
      {
        $this->db->like('products.code', $ds['code']);
      }

      if(!empty($ds['name']))
      {
        $this->db->like('products.name', $ds['name']);
      }

      if(!empty($ds['barcode']))
      {
        $this->db->like('products.barcode', $ds['barcode']);
      }

      if(!empty($ds['color']))
      {
        if($ds['color'] === 'NULL')
        {
          $this->db->where('products.color_code IS NULL', NULL, FALSE);
        }
        else
        {
          $this->db->group_start();
          $this->db->like('product_color.code', $ds['color'])->or_like('product_color.name', $ds['color']);
          $this->db->group_end();
        }
      }

      if(!empty($ds['size']))
      {
        if($ds['size'] === 'NULL')
        {
          $this->db->where('products.size_code IS NULL', NULL, FALSE);
        }
        else
        {
          $this->db->group_start();
          $this->db->like('product_size.code', $ds['size'])->or_like('product_size.name', $ds['size']);
          $this->db->group_end();
        }
      }

      if(!empty($ds['group']))
      {
        $this->db->where('group_code', $ds['group']);
      }

      if(!empty($ds['sub_group']))
      {
        $this->db->where('sub_group_code', $ds['sub_group']);
      }

      if(!empty($ds['category']))
      {
        $this->db->where('category_code', $ds['category']);
      }

      if(!empty($ds['kind']))
      {
        $this->db->where('kind_code', $ds['kind']);
      }

      if(!empty($ds['type']))
      {
        $this->db->where('type_code', $ds['type']);
      }

      if(!empty($ds['brand']))
      {
        $this->db->where('brand_code', $ds['brand']);
      }

      if(!empty($ds['year']))
      {
        $this->db->where('year', $ds['year']);
      }
    }

    return $this->db->count_all_results();

  }



  public function get_list(array $ds = array(), $perpage = '', $offset = '')
  {
    $this->db
    ->select('products.*')
    ->from('products')
    ->join('product_color', 'products.color_code = product_color.code', 'left')
    ->join('product_size', 'products.size_code = product_size.code', 'left');

    if(!empty($ds))
    {
      if(!empty($ds['code']))
      {
        $this->db->like('products.code', $ds['code']);
      }

      if(!empty($ds['name']))
      {
        $this->db->like('products.name', $ds['name']);
      }

      if(!empty($ds['barcode']))
      {
        $this->db->like('products.barcode', $ds['barcode']);
      }

      if(!empty($ds['color']))
      {
        if($ds['color'] === 'NULL')
        {
          $this->db->where('products.color_code IS NULL', NULL, FALSE);
        }
        else
        {
          $this->db->group_start();
          $this->db->like('product_color.code', $ds['color'])->or_like('product_color.name', $ds['color']);
          $this->db->group_end();
        }
      }

      if(!empty($ds['size']))
      {
        if($ds['size'] === 'NULL')
        {
          $this->db->where('products.size_code IS NULL', NULL, FALSE);
        }
        else
        {
          $this->db->group_start();
          $this->db->like('product_size.code', $ds['size'])->or_like('product_size.name', $ds['size']);
          $this->db->group_end();
        }
      }

      if(!empty($ds['group']))
      {
        $this->db->where('group_code', $ds['group']);
      }

      if(!empty($ds['sub_group']))
      {
        $this->db->where('sub_group_code', $ds['sub_group']);
      }

      if(!empty($ds['category']))
      {
        $this->db->where('category_code', $ds['category']);
      }

      if(!empty($ds['kind']))
      {
        $this->db->where('kind_code', $ds['kind']);
      }

      if(!empty($ds['type']))
      {
        $this->db->where('type_code', $ds['type']);
      }

      if(!empty($ds['brand']))
      {
        $this->db->where('brand_code', $ds['brand']);
      }

      if(!empty($ds['year']))
      {
        $this->db->where('year', $ds['year']);
      }
    }

    $this->db->order_by('style_code', 'ASC');
    $this->db->order_by('color_code', 'ASC');
    $this->db->order_by('product_size.position', 'ASC');


    if($perpage != '')
    {
      $offset = $offset === NULL ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }
    //echo $this->db->get_compiled_select();
    $rs = $this->db->get();

    return $rs->result();
  }


  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return  $this->db->replace('products', $ds);
    }

    return FALSE;
  }



  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db->where('code', $code);
      return $this->db->update('products', $ds);
    }

    return FALSE;
  }





  public function get_status($field, $item)
  {
    $rs = $this->db->select($field)->where('code', $item)->get('products');
    if($rs->num_rows() == 1)
    {
      return $rs->row()->$field;
    }

    return 0;
  }



  public function get_barcode($code)
  {
    $rs = $this->db->where('code', $code)->get('products');
    if($rs->num_rows() === 1)
    {
      return empty($rs->row()->barcode) ? $rs->row()->code : $rs->row()->barcode;
    }

    return NULL;
  }



  public function get_product_by_barcode($barcode)
  {
    $rs = $this->db->where('barcode', $barcode)->or_where('code', $barcode)->get('products');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }




  public function set_status($field, $item, $val)
  {
    return $this->db->set($field, $val)->where('code', $item)->update('products');
  }







  public function delete_item($code)
  {
    return $this->db->where('code', $code)->delete('products');
  }


  // public function count_rows(array $ds = array())
  // {
  //   if(!empty($ds))
  //   {
  //     $this->db->select('code');
  //
  //     foreach($ds as $field => $val)
  //     {
  //       if($val != '')
  //       {
  //         $this->db->like($field, $val);
  //       }
  //     }
  //     $rs = $this->db->get('products');
  //
  //     return $rs->num_rows();
  //   }
  //
  //   return 0;
  // }




  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get('products');
    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



  public function get_name($code)
  {
    $rs = $this->db->select('name')->where('code', $code)->get('products');
    if($rs->num_rows() == 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }



  public function get_style_code($code)
  {
    $rs = $this->db->select('style_code')->where('code', $code)->get('products');
    if($rs->num_rows() == 1)
    {
      return $rs->row()->style_code;
    }

    return NULL;
  }



  public function get_data($ds, $perpage = '', $offset = '')
  {
    if(!empty($ds))
    {
      foreach($ds as $field => $val)
      {
        if($val != '')
        {
          $this->db->like($field, $val);
        }
      }

      if($perpage != '')
      {
        $offset = $offset === NULL ? 0 : $offset;
        $this->db->limit($perpage, $offset);
      }

      $rs = $this->db->get('products');

      return $rs->result();
    }

    return FALSE;
  }



  public function get_style_items($code)
  {
    $qr = "SELECT p.* FROM products AS p
          LEFT JOIN product_color AS c ON p.color_code = c.code
          LEFT JOIN product_size AS s ON p.size_code = s.code
          WHERE style_code = '$code'
          ORDER BY c.code ASC, s.position ASC";

    $rs = $this->db->query($qr);
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function get_items_by_color($style, $color)
  {
    $rs = $this->db->where('style_code', $style)->where('color_code', $color)->get('products');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return array();
  }




  public function get_item_by_color_and_size($style, $color, $size)
  {
    $rs = $this->db
    ->where('style_code', $style)
    ->where('color_code', $color)
    ->where('size_code', $size)
    ->limit(1)
    ->get('products');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return array();
  }




  public function countAttribute($style_code)
	{
		$color = $this->db->where('style_code', $style_code)->where('color_code is NOT NULL')->where('color_code !=', '')->group_by('style_code')->get('products');
		$size  = $this->db->where('style_code', $style_code)->where('size_code is NOT NULL')->where('size_code !=', '')->group_by('style_code')->get('products');
		return $color->num_rows() + $size->num_rows();
	}


  public function get_unbarcode_items($style)
  {
    $this->db->select('code');
    $this->db->where('style_code', $style);
    $this->db->where('barcode IS NULL', NULL, FALSE);
    $rs = $this->db->get('products');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return array();
  }



  public function update_barcode($code, $barcode)
  {
    $this->db->set('barcode', $barcode);
    return $this->db->where('code', $code)->update('products');
  }




  public function is_exists($code, $old_code = '')
  {
    if($old_code != '')
    {
      $this->db->where('code !=', $old_code);
    }

    $rs = $this->db->where('code', $code)->get('products');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function is_exists_name($name, $old_name = '')
  {
    if($old_name != '')
    {
      $this->db->where('name !=', $old_name);
    }

    $rs = $this->db->where('name', $name)->get('products');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }




  public function is_exists_style($style)
  {
    $rs = $this->db->select('code')->where('style_code', $style)->get('products');
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }




  public function is_disactive_all($style_code)
  {
    $this->db->select('code')->where('style_code', $style_code)->where('active', 1);
    $rs = $this->db->get('products');
    if($rs->num_rows() > 0)
    {
      return FALSE;
    }

    return TRUE;
  }




  public function count_color($style_code)
  {
    $this->db->select('color_code')
    ->where('style_code', $style_code)
    ->where('color_code is NOT NULL')
    ->where('color_code != ', '')
    ->group_by('color_code');
    $rs = $this->db->get('products');

    return $rs->num_rows();
  }



  public function count_size($style_code)
  {
    $this->db->select('size_code')
    ->where('style_code', $style_code)
    ->where('size_code is NOT NULL')
    ->where('size_code != ', '')
    ->group_by('size_code');
    $rs = $this->db->get('products');

    return $rs->num_rows();
  }



  public function get_all_colors($style_code)
  {
    $qr = "SELECT c.code, c.name FROM products AS p
          LEFT JOIN product_color AS c ON p.color_code = c.code
          WHERE p.style_code = '".$style_code."'
          GROUP BY p.color_code
          ORDER BY p.color_code ASC";
    $rs = $this->db->query($qr);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function get_all_sizes($style_code)
  {
    $qr = "SELECT s.code, s.name FROM products AS p
           LEFT JOIN product_size AS s ON p.size_code = s.code
           WHERE p.style_code = '".$style_code."'
           GROUP BY p.size_code
           ORDER BY s.position ASC";
    $rs = $this->db->query($qr);
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function get_unit_code($code)
  {
    $rs = $this->db
    ->select('unit_code')
    ->where('code', $code)
    ->get('products');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->unit_code;
    }

    return NULL;
  }



  public function has_transection($code)
  {
    $od = $this->db->select('product_code')->where('product_code', $code)->count_all_results('order_details');
    $oc = $this->db->select('product_code')->where('product_code', $code)->count_all_results('order_transform_detail');
    $rc = $this->db->select('product_code')->where('product_code', $code)->count_all_results('receive_product_detail');
    $rt = $this->db->select('product_code')->where('product_code', $code)->count_all_results('receive_transform_detail');
    $tf = $this->db->select('product_code')->where('product_code', $code)->count_all_results('transfer_detail');
    $cn = $this->db->select('product_code')->where('product_code', $code)->count_all_results('return_order_detail');

    $all = $od+$oc+$rc+$rt+$tf+$cn;
    if($all > 0)
    {
      return TRUE;
    }

    return FALSE;
  }

}
?>

<?php
class Product_color_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return  $this->db->insert('product_color', $ds);
    }

    return FALSE;
  }



  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db->where('code', $code);
      return $this->db->update('product_color', $ds);
    }

    return FALSE;
  }


  public function delete($code)
  {
    return $this->db->where('code', $code)->delete('product_color');
  }


  public function count_rows(array $ds = array())
  {
    $this->db
    ->from('product_color AS co')
    ->join('product_color_group AS cg', 'co.group_code = cg.code', 'left');

    if($ds['status'] !== 'all')
    {
      $this->db->where('co.active', $ds['status']);
    }

    if(!empty($ds['code']))
    {
      $this->db->like('co.code', $ds['code']);
    }

    if(!empty($ds['name']))
    {
      $this->db->like('co.name', $ds['name']);
    }

    if(!empty($ds['group_code']))
    {
      $this->db->where('co.group_code', $ds['group_code']);
    }

    if(!empty($ds['gen_code']))
    {
      $this->db->like('co.gen_code', $ds['gen_code']);
    }

    return $this->db->count_all_results();
  }




  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get('product_color');
    return $rs->row();
  }



  public function get_name($code)
  {
    if($code === NULL OR $code === '')
    {
      return $code;
    }

    $rs = $this->db->select('name')->where('code', $code)->get('product_color');
    return $rs->row()->name;
  }




  public function get_data(array $ds = array(), $perpage = NULL, $offset = NULL)
  {
    $this->db
    ->select('co.*, cg.name AS group_name')
    ->from('product_color AS co')
    ->join('product_color_group AS cg', 'co.group_code = cg.code', 'left');

    if($ds['status'] !== 'all')
    {
      $this->db->where('co.active', $ds['status']);
    }

    if(!empty($ds['code']))
    {
      $this->db->like('co.code', $ds['code']);
    }

    if(!empty($ds['name']))
    {
      $this->db->like('co.name', $ds['name']);
    }

    if(!empty($ds['group_code']))
    {
      $this->db->where('co.group_code', $ds['group_code']);
    }

    if(!empty($ds['gen_code']))
    {
      $this->db->like('co.gen_code', $ds['gen_code']);
    }

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

    return NULL;
  }


  public function get_all_color()
  {
    $rs = $this->db
    ->order_by('code', 'ASC')
    ->get('product_color');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }




  public function is_exists($code, $old_code = '')
  {
    if($old_code != '')
    {
      $this->db->where('code !=', $old_code);
    }

    $rs = $this->db->where('code', $code)->get('product_color');

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

    $rs = $this->db->where('name', $name)->get('product_color');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function set_active($code, $active)
  {
    return $this->db->set('active', $active)->where('code', $code)->update('product_color');
  }

  public function count_members($code)
  {
    $this->db->select('active')->where('color_code', $code);
    $rs = $this->db->get('products');
    return $rs->num_rows();
  }


  public function get_color_group_list()
  {
    $rs = $this->db->get('product_color_group');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

}
?>

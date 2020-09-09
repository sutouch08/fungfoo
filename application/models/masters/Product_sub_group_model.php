<?php
class Product_sub_group_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return  $this->db->insert('product_sub_group', $ds);
    }

    return FALSE;
  }



  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db->where('code', $code);
      return $this->db->update('product_sub_group', $ds);
    }

    return FALSE;
  }


  public function delete($code)
  {
    return $this->db->where('code', $code)->delete('product_sub_group');
  }


  public function count_rows(array $ds = array())
  {
    if(! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if(! empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }

    return $this->db->count_all_results('product_sub_group');
  }




  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get('product_sub_group');
    return $rs->row();
  }



  public function get_name($code)
  {
    if($code === NULL OR $code === '')
    {
      return $code;
    }

    $rs = $this->db->select('name')->where('code', $code)->get('product_sub_group');
    return $rs->row()->name;
  }




  public function get_data(array $ds = array(), $perpage = '', $offset = '')
  {
    if(! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if(! empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }

    if($perpage != '')
    {
      $offset = $offset === NULL ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }

    $rs = $this->db->get('product_sub_group');

    return $rs->result();
  }




  public function is_exists($code, $old_code = '')
  {
    if($old_code != '')
    {
      $this->db->where('code !=', $old_code);
    }

    $rs = $this->db->where('code', $code)->get('product_sub_group');

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

    $rs = $this->db->where('name', $name)->get('product_sub_group');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function count_members($code)
  {
    $this->db->select('active')->where('sub_group_code', $code);
    $rs = $this->db->get('products');
    return $rs->num_rows();
  }


}
?>

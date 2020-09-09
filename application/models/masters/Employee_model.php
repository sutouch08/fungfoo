<?php
class Employee_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get('employee');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function is_exists($code, $old_code = NULL)
  {
    $this->db->where('code', $code);
    if(!empty($old_code))
    {
      $this->db->where('code !=', $old_code);
    }

    $rs = $this->db->get('employee');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function is_exists_name($name, $old_name = NULL)
  {
    $this->db->where('name', $name);
    if(!empty($old_name))
    {
      $this->db->where('name !=', $old_name);
    }

    $rs = $this->db->get('employee');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('employee', $ds);
    }

    return FALSE;
  }


  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('code', $code)->update('employee', $ds);
    }

    return FALSE;
  }



  public function delete($code)
  {
    return $this->db->where('code', $code)->delete('employee');
  }



  public function count_rows(array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db->where('code !=', '');

      if(!empty($ds['code']))
      {
        $this->db->like('code', $ds['code']);
      }

      if(!empty($ds['name']))
      {
        $this->db->like('name', $ds['name']);
      }

      if($ds['active'] != 2)
      {
        $this->db->where('active', $ds['active']);
      }

      return $this->db->count_all_results('employee');
    }

    return FALSE;
  }


  public function get_list(array $ds = array(), $limit, $offset)
  {
    if(!empty($ds))
    {
      $this->db->where('code !=', '');

      if(!empty($ds['code']))
      {
        $this->db->like('code', $ds['code']);
      }

      if(!empty($ds['name']))
      {
        $this->db->like('name', $ds['name']);
      }

      if($ds['active'] != 2)
      {
        $this->db->where('active', $ds['active']);
      }

      if(!empty($limit))
      {
        $offset = empty($offset) ? 0 : $offset;
        $this->db->limit($limit, $offset);
      }

      $rs = $this->db->get('employee');

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }

    }

    return FALSE;
  }

}
?>

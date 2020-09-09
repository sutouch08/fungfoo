<?php
class Sender_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('address_sender', $ds);
    }

    return FALSE;
  }


  public function update($id, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('id', $id)->update('address_sender', $ds);
    }

    return FALSE;
  }



  public function delete($id)
  {
    return $this->db->where('id', $id)->delete('address_sender');
  }


  public function get($id)
  {
    $rs = $this->db->where('id', $id)->get('address_sender');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }





  public function get_sender($id)
  {
    $rs = $this->db->where('id', $id)->get('address_sender');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get_name($id)
  {
    $rs = $this->db->where('id', $id)->get('address_sender');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }



  public function is_exists($name, $id = NULL)
  {
    if(! empty($id))
    {
      $rs = $this->db->where('name', $name)->where('id !=',$id)->get('address_sender');
    }
    else
    {
      $rs = $this->db->where('name', $name)->get('address_sender');
    }

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function count_rows(array $ds = array())
  {
    if(!empty($ds))
    {
      if(!empty($ds['name']))
      {
        $this->db->like('name', $ds['name']);
      }

      if(!empty($ds['addr']))
      {
        $this->db->group_start();
        $this->db->like('address1', $ds['addr'])->or_like('address2', $ds['addr']);
        $this->db->group_end();
      }

      if(!empty($ds['phone']))
      {
        $this->db->like('phone', $ds['phone']);
      }

      if($ds['type'] != 'all')
      {
        $this->db->where('type', $ds['type']);
      }

      if($ds['in_list'] != 'all')
      {
        $this->db->where('show_in_list', $ds['in_list']);
      }

      return $this->db->count_all_results('address_sender');
    }

    return 0;
  }


  public function get_list(array $ds = array(), $perpage, $offset)
  {
    if(!empty($ds))
    {
      if(!empty($ds['name']))
      {
        $this->db->like('name', $ds['name']);
      }

      if(!empty($ds['addr']))
      {
        $this->db->group_start();
        $this->db->like('address1', $ds['addr'])->or_like('address2', $ds['addr']);
        $this->db->group_end();
      }

      if(!empty($ds['phone']))
      {
        $this->db->like('phone', $ds['phone']);
      }

      if($ds['type'] != 'all')
      {
        $this->db->where('type', $ds['type']);
      }

      if($ds['in_list'] != 'all')
      {
        $this->db->where('show_in_list', $ds['in_list']);
      }

      if(!empty($offset))
      {
        $offset = $offset === NULL ? 0 : $offset;
        $this->db->limit($perpage, $offset);
      }

      $rs = $this->db->get('address_sender');

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }

    }

    return FALSE;
  }


  //---- เอาเฉพาะรายการที่จะแสดงหน้าออเดอร์
  public function get_sender_list()
  {
    $rs = $this->db->where('show_in_list', 1)->get('address_sender');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }

  public function search($txt)
  {
    $qr = "SELECT id FROM address_sender WHERE name LIKE '%".$txt."%'";
    $rs = $this->db->query($qr);
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }
    else
    {
      return array();
    }

  }

}
 ?>

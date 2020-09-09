<?php
class Transport_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('address_transport', $ds);
    }

    return FALSE;
  }


  public function update($id, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('id', $id)->update('address_transport', $ds);
    }

    return FALSE;
  }



  public function delete($id)
  {
    return $this->db->where('id', $id)->delete('address_transport');
  }


  public function get($id)
  {
    $rs = $this->db->where('id', $id)->get('address_transport');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



  public function get_name($id)
  {
    $rs = $this->db->where('id', $id)->get('address_transport');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }



  public function is_exists($customer_code, $id = NULL)
  {
    if(! empty($id))
    {
      $rs = $this->db->where('customer_code', $customer_code)->where('id !=',$id)->get('address_transport');
    }
    else
    {
      $rs = $this->db->where('customer_code', $customer_code)->get('address_transport');
    }

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function count_rows(array $ds = array())
  {
    $qr  = "SELECT COUNT(id) AS row FROM address_transport AS t ";
    $qr .= "LEFT JOIN customers AS c ON t.customer_code = c.code ";
    $qr .= "WHERE t.id != 0 ";

    if(!empty($ds['name']))
    {
      $qr .= "AND (t.customer_code LIKE '%{$ds['name']}%' OR c.name LIKE '%{$ds['name']}%') ";
    }

    if(!empty($ds['sender']))
    {
      $sender = sender_in($ds['sender']);
      $qr .= "AND (t.main_sender IN({$sender}) OR t.second_sender IN({$sender}) OR t.third_sender IN({$sender})) ";
    }

    $rs = $this->db->query($qr);

    return $rs->row()->row;
  }


  public function get_list(array $ds = array(), $perpage, $offset)
  {
    $qr  = "SELECT c.name AS customer_name, t.id, t.main_sender, t.second_sender, t.third_sender ";
    $qr .= "FROM address_transport AS t ";
    $qr .= "LEFT JOIN customers AS c ON t.customer_code = c.code ";
    $qr .= "WHERE t.id != 0 ";

    if(!empty($ds['name']))
    {
      $qr .= "AND (t.customer_code LIKE '%{$ds['name']}%' OR c.name LIKE '%{$ds['name']}%') ";
    }

    if(!empty($ds['sender']))
    {
      $sender = sender_in($ds['sender']);
      $qr .= "AND (t.main_sender IN({$sender}) OR t.second_sender IN({$sender}) OR t.third_sender IN({$sender})) ";
    }

    if(empty($offset))
    {
      $offset = 0;
    }

    $qr .= "LIMIT {$perpage} OFFSET {$offset}";

    $rs = $this->db->query($qr);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }


    return FALSE;
  }


}
 ?>

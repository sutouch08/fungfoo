<?php
class User_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }



  public function new_user(array $data = array())
  {
    if(!empty($data))
    {
      return $this->db->insert('user', $data);
    }

    return FALSE;
  }




  public function update_user($id, array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db->where('id', $id);
      return $this->db->update('user', $ds);
    }

    return FALSE;
  }



  public function delete_user($id)
  {
    return $this->db->where('id', $id)->delete('user');
  }



  public function get_user($id)
  {
    $rs = $this->db->where('id', $id)->get('user');
    return $rs->row();
  }



  public function get_name($uname)
  {
    $rs = $this->db->where('uname', $uname)->get('user');
    if($rs->num_rows() == 1)
    {
      return $rs->row()->name;
    }

    return "";
  }


  public function get_users($uname = '', $dname = '', $profile = '', $perpage = 50, $offset = 0)
  {
    $offset = $offset === NULL ? 0 : $offset;
    $qr = "SELECT u.id AS id, u.uname AS uname, u.name AS dname, p.name AS pname, u.date_add, u.active ";
    $qr .= "FROM user AS u ";
    $qr .= "LEFT JOIN profile AS p ON u.id_profile = p.id ";
    $qr .= "WHERE u.id != 0 ";

    if($uname !== '')
    {
      $qr .= "AND u.uname LIKE '%".$uname."%' ";
    }

    if($dname !== '')
    {
      $qr .= "AND u.name LIKE '%".$dname."%' ";
    }

    if($profile !== '')
    {
      $qr .= "AND p.name LIKE '%".$profile."%' ";
    }

    $qr .= "ORDER BY u.name ASC ";
    $qr .= "LIMIT ".$perpage;
    $qr .= " OFFSET ".$offset;

    $rs = $this->db->query($qr);

    return $rs->result();
  }





  public function count_rows($uname = '', $dname = '', $profile = '')
  {
    $qr = "SELECT u.id ";
    $qr .= "FROM user AS u ";
    $qr .= "LEFT JOIN profile AS p ON u.id_profile = p.id ";
    $qr .= "WHERE u.id != 0 ";

    if($uname !== '')
    {
      $qr .= "AND u.uname LIKE '%".$uname."%' ";
    }

    if($dname !== '')
    {
      $qr .= "AND u.name LIKE '%".$dname."%' ";
    }

    if($profile !== '')
    {
      $qr .= "AND p.name LIKE '%".$profile."%' ";
    }

    $rs = $this->db->query($qr);

    return $rs->num_rows();
  }






  public function get_permission($menu, $uid, $id_profile)
  {
    if(!empty($menu))
    {
      $rs = $this->db->where('code', $menu)->get('menu');
      if($rs->num_rows() === 1)
      {
        if($rs->row()->valid == 1)
        {
          $user_pm = $this->get_user_permission($menu, $uid);
          if($user_pm !== FALSE)
          {
            return $user_pm;
          }

          return $this->get_profile_permission($menu, $id_profile);
        }
        else
        {
          $ds = new stdClass();
          $ds->can_view = 1;
          $ds->can_add = 1;
          $ds->can_edit = 1;
          $ds->can_delete = 1;
          $ds->can_approve = 1;
          return $ds;
        }
      }

    }

    return FALSE;
  }


  private function get_user_permission($menu, $uid)
  {
    $rs = $this->db->where('menu', $menu)->where('uid', $uid)->get('permission');
    return $rs->num_rows() == 1 ? $rs->row() : FALSE;
  }


  private function get_profile_permission($menu, $id_profile)
  {
    $rs = $this->db->where('menu', $menu)->where('id_profile', $id_profile)->get('permission');
    return $rs->num_rows() == 1 ? $rs->row() : FALSE;
  }



  //--- activate suspended user by id
  public function active_user($id)
  {
    $this->db->set('active', 1)->where('id', $id);

    if($this->db->update('user'))
    {
      return TRUE;
    }

    return $this->db->error();
  }




//---- Suspend activeted user by id
  public function disactive_user($id)
  {
    $this->db->set('active', 0)->where('id', $id);
    if($this->db->update('user'))
    {
      return TRUE;
    }

    return $this->db->error();
  }





  public function is_exists_uname($uname, $id)
  {
    if($id !== '')
    {
      $this->db->where('id !=', $id);
    }

    $rs = $this->db->where('uname', $uname)->get('user');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function is_exists_display_name($dname, $id)
  {
    if($id !== '')
    {
      $this->db->where('id !=', $id);
    }

    $rs = $this->db->where('name', $dname)->get('user');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }




  public function get_user_credentials($uname)
  {
    $this->db->where('uname', $uname);
    $rs = $this->db->get('user');
    return $rs->row();
  }





  public function change_password($id, $pwd)
  {
    $this->db->set('pwd', $pwd);
    $this->db->where('id', $id);
    return $this->db->update('user');
  }



  public function verify_uid($uid)
  {
    $this->db->select('uid');
    $this->db->where('uid', $uid);
    $this->db->where('active', 1);
    $rs = $this->db->get('user');

    return $rs->num_rows() === 1 ? TRUE : FALSE;
  }




  public function get_user_credentials_by_skey($skey)
  {
    if(!empty($skey))
    {
      $rs = $this->db->where('skey', $skey)->get('user');
      if($rs->num_rows() === 1)
      {
        return $rs->row();
      }
    }

    return FALSE;
  }


  public function search($txt)
  {
    $qr = "SELECT uname FROM user WHERE uname LIKE '%".$txt."%' OR name LIKE '%".$txt."%'";
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


} //---- End class

 ?>

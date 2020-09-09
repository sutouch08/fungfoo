<?php
class Product_image_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('images', $ds);
    }
  }




  public function update_product_image(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->replace('image_product', $ds);
    }

    return NULL;
  }


  public function get_style_images($style)
  {
    $rs = $this->db->where('style', $style)->get('images');
    return $rs->result();
  }




  public function get_id_image($code)
  {
    $rs = $this->db->where('code', $code)->get('image_product');
    if($rs->num_rows() > 0)
    {
      return $rs->row()->id_image;
    }

    return 0;
  }



  public function get_new_id()
  {
    $rs = $this->db->select_max('id')->get('images');
    if($rs->num_rows() == 1)
    {
      return $rs->row()->id + 1;
    }

    return 1;
  }






  public function get_style_code($id)
  {
    $rs = $this->db->where('id', $id)->get('images');
    if($rs->num_rows() > 0)
    {
      return $rs->row()->style;
    }

    return FALSE;

  }






  public function has_cover($style)
  {
    $rs = $this->db->where('style', $style)->where('cover', 1)->get('images');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }







  public function unset_cover($style)
  {
    return $this->db->set('cover', 0)->where('style', $style)->update('images');
  }






  public function set_cover($id)
  {
    return $this->db->set('cover', 1)->where('id', $id)->update('images');
  }



  public function get_cover($code)
  {
    $rs = $this->db->where('style', $code)->where('cover', 1)->get('images');
    if($rs->num_rows() > 0)
    {
      return $rs->row()->id;
    }

    return 0;
  }




  public function update_product_imag(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->replace('image_product', $ds);
    }

    return FALSE;
  }


  public function delete_product_image($id)
  {
    $this->db->trans_start();
    $this->db->where('id', $id)->delete('images');
    $this->db->where('id_image', $id)->delete('image_product');
    $this->db->trans_complete();

    if($this->db->trans_status() === FALSE)
    {
      return FALSE;
    }

    return TRUE;
  }



}
?>

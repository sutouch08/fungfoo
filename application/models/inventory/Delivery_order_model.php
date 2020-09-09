<?php
class Delivery_order_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }



  public function get_sold_details($reference)
  {
    $rs = $this->db->where('reference', $reference)->get('order_sold');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }




  public function count_rows(array $ds = array(), $state = 7)
  {
    $this->db
    ->from('orders')
    ->join('channels', 'channels.code = orders.channels_code','left')
    ->join('payment_method', 'payment_method.code = orders.payment_code', 'left')
    ->join('customers', 'customers.code = orders.customer_code', 'left')
    ->where('orders.state', $state);

    if(!empty($ds['code']))
    {
      $this->db->like('orders.code', $ds['code']);
    }

    if(!empty($ds['customer']))
    {
      $this->db->group_start();
      $this->db->like('customers.name', $ds['customer']);
      $this->db->or_like('orders.customer_ref', $ds['customer']);
      $this->db->group_end();
    }

    //---- user name / display name
    if(!empty($ds['user']))
    {
      $users = user_in($ds['user']);
      $this->db->where_in('user', $users);
    }


    if(!empty($ds['role']))
    {
      $this->db->where('orders.role', $ds['role']);
    }


    if(!empty($ds['channels']))
    {
      $this->db->where('orders.channels_code', $ds['channels']);
    }


    if(!empty($ds['payment']))
    {
      $this->db->where('orders.payment_code', $ds['payment']);
    }


    if($ds['from_date'] != '' && $ds['to_date'] != '')
    {
      $this->db->where('orders.date_add >=', from_date($ds['from_date']));
      $this->db->where('orders.date_add <=', to_date($ds['to_date']));
    }

    return $this->db->count_all_results();
  }



  public function get_data(array $ds = array(), $perpage = '', $offset = '', $state = 7)
  {
    //$total_query = "(SELECT SUM(total_amount) FROM order_details WHERE order_code = orders.code) AS total_amount";
    $this->db->select('orders.*')
    ->select('channels.name AS channels_name')
    ->select('payment_method.name AS payment_name, payment_method.role AS payment_role')
    ->select('customers.name AS customer_name')
    ->from('orders')
    ->join('channels', 'channels.code = orders.channels_code','left')
    ->join('payment_method', 'payment_method.code = orders.payment_code', 'left')
    ->join('customers', 'customers.code = orders.customer_code', 'left')
    ->where('orders.state', $state);

    if(!empty($ds['code']))
    {
      $this->db->like('orders.code', $ds['code']);
    }

    if(!empty($ds['customer']))
    {
      $this->db->group_start();
      $this->db->like('customers.name', $ds['customer']);
      $this->db->or_like('orders.customer_ref', $ds['customer']);
      $this->db->group_end();
    }

    //---- user name / display name
    if(!empty($ds['user']))
    {
      $users = user_in($ds['user']);
      $this->db->where_in('orders.user', $users);
    }


    if(!empty($ds['role']))
    {
      $this->db->where('orders.role', $ds['role']);
    }

    if(!empty($ds['channels']))
    {
      $this->db->where('orders.channels_code', $ds['channels']);
    }

    if(!empty($ds['payment']))
    {
      $this->db->where('orders.payment_code', $ds['payment']);
    }

    if($ds['from_date'] != '' && $ds['to_date'] != '')
    {
      $this->db->where('orders.date_add >=', from_date($ds['from_date']));
      $this->db->where('orders.date_add <=', to_date($ds['to_date']));
    }

    if($perpage != '')
    {
      $offset = $offset === NULL ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }

    $rs = $this->db->get();

    return $rs->result();
  }




    //------------------ สำหรับแสดงยอดที่มีการบันทึกขายไปแล้ว -----------//
    //--- รายการสั้งซื้อ รายการจัดสินค้า รายการตรวจสินค้า
    //--- เปรียบเทียบยอดที่มีการสั่งซื้อ และมีการตรวจสอนค้า
    //--- เพื่อให้ได้ยอดที่ต้องเปิดบิล บันทึกขายจริงๆ
    //--- ผลลัพธ์จะได้ยอดสั่งซื้อเป็นหลัก หากไม่มียอดตรวจ จะได้ยอดตรวจ เป็น NULL
    //--- กรณีสินค้าเป็นสินค้าที่ไม่นับสต็อกจะบันทึกตามยอดที่สั่งมา
    public function get_billed_detail($code, $use_qc = TRUE)
    {
      $qr = "SELECT o.product_code, o.product_name, o.qty AS order_qty, o.is_count, ";
      $qr .= "o.price, o.discount1, o.discount2, o.discount3, ";
      $qr .= "(o.discount_amount / o.qty) AS discount_amount, ";
      $qr .= "(o.total_amount/o.qty) AS final_price, ";
      $qr .= "(SELECT SUM(qty) FROM prepare WHERE order_code = '{$code}' AND product_code = o.product_code) AS prepared ";
      if($use_qc)
      {
        $qr .= ",(SELECT SUM(qty) FROM qc WHERE order_code = '{$code}' AND product_code = o.product_code) AS qc ";
      }

      $qr .= "FROM order_details AS o ";
      $qr .= "WHERE o.order_code = '{$code}' GROUP BY o.product_code";

      $rs = $this->db->query($qr);
      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }

      return FALSE;
    }



    //------------- สำหรับใช้ในการบันทึกขาย ---------//
    //--- รายการสั้งซื้อ รายการจัดสินค้า รายการตรวจสินค้า
    //--- เปรียบเทียบยอดที่มีการสั่งซื้อ และมีการตรวจสอนค้า
    //--- เพื่อให้ได้ยอดที่ต้องเปิดบิล บันทึกขายจริงๆ
    //--- ผลลัพธ์จะไม่ได้ยอดที่มีการสั่งซื้อแต่ไม่มียอดตรวจ หรือ มียอดตรวจแต่ไม่มียอดสั่งซื้อ (กรณีมีการแก้ไขออเดอร์)
    public function get_bill_detail($code, $use_qc = TRUE)
    {
      $qr = "SELECT o.id, o.style_code, o.product_code, o.product_name, o.qty AS order_qty, ";
      $qr .= "o.cost, o.price, o.discount1, o.discount2, o.discount3, ";
      $qr .= "o.id_rule, ru.id_policy, o.is_count, ";
      $qr .= "(o.discount_amount / o.qty) AS discount_amount, ";
      $qr .= "(o.total_amount/o.qty) AS final_price, ";
      $qr .= "(SELECT SUM(qty) FROM buffer WHERE order_code = '{$code}' AND product_code = o.product_code) AS prepared ";
      if($use_qc)
      {
        $qr .= ",(SELECT SUM(qty) FROM qc WHERE order_code = '{$code}' AND product_code = o.product_code) AS qc ";
      }

      $qr .= "FROM order_details AS o ";
      $qr .= "LEFT JOIN discount_rule AS ru ON ru.id = o.id_rule ";
      $qr .= "WHERE o.order_code = '{$code}' GROUP BY o.product_code ";
      if($use_qc)
      {
        $qr .= "HAVING qc IS NOT NULL";
      }

      $rs = $this->db->query($qr);
      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }

      return FALSE;
    }


    public function get_non_count_bill_detail($code)
    {
      $qr  = "SELECT o.product_code, o.product_name, o.style_code, o.qty, ";
      $qr .= "o.cost, o.price, o.discount1, o.discount2, o.discount3, ";
      $qr .= "o.id_rule, ru.id_policy, o.is_count, ";
      $qr .= "(o.discount_amount / o.qty) AS discount_amount, ";
      $qr .= "(o.total_amount/o.qty) AS final_price ";
      $qr .= "FROM order_details AS o ";
      $qr .= "LEFT JOIN discount_rule AS ru ON ru.id = o.id_rule ";
      $qr .= "WHERE o.order_code = '{$code}' ";
      $qr .= "AND o.is_count = 0 ";

      $rs = $this->db->query($qr);
      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }

      return FALSE;
    }


    public function sold(array $ds = array())
    {
      if(!empty($ds))
      {
        return $this->db->insert('order_sold', $ds);
      }

      return FALSE;
    }


}

 ?>

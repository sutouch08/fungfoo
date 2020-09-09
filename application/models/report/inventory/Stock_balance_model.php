<?php
class Stock_balance_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get_current_stock_balance($allProduct, $pdFrom, $pdTo, $allWhouse, $warehouse)
  {
    $this->db
    ->select('products.barcode, products.code, products.name, products.cost')
    ->select_sum('stock.qty')
    ->from('stock')
    ->join('products', 'stock.product_code = products.code', 'left')
    ->join('product_size', 'product_size.code = products.size_code', 'left');

    //--- if specify warehouse
    if(empty($allWhouse))
    {
      $this->db->join('zone', 'stock.zone_code = zone.code', 'left');
      $this->db->where_in('zone.warehouse_code', $warehouse);
    }

    //--- if specify product
    if(empty($allProduct))
    {
      $this->db->where('products.style_code >=', $pdFrom);
      $this->db->where('products.style_code <=', $pdTo);
    }

    $this->db->group_by('stock.product_code');
    $this->db->order_by('products.style_code', 'ASC');
    $this->db->order_by('products.color_code', 'ASC');
    $this->db->order_by('product_size.position', 'ASC');

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function get_stock_balance_prev_date($allProduct, $pdFrom, $pdTo, $allWhouse, $warehouse, $date)
  {
    $date = to_date($date);

    $qr  = "SELECT pd.barcode, pd.code, pd.name, pd.cost, (SUM(s.move_in) - SUM(s.move_out)) AS qty ";
    $qr .= "FROM stock_movement AS s ";
    $qr .= "JOIN products AS pd ON s.product_code = pd.code ";
    $qr .= "JOIN product_size AS ps ON pd.size_code = ps.code ";
    if($allWhouse == 0)
    {
      $qr .= "JOIN zone AS z ON s.zone_code = z.code ";
    }

    $qr .= "WHERE s.date_add <= '{$date}' ";

    if($allProduct == 0)
    {
      $qr .= "AND pd.style_code >= '{$pdFrom}' ";
      $qr .= "AND pd.style_code <= '{$pdTo}' ";
    }


    if($allWhouse == 0)
    {
      $wh_list = "";
      $i = 1;
      foreach($warehouse as $wh)
      {
        $wh_list .= $i === 1 ? "'{$wh}'" : ", '{$wh}'";
        $i++;
      }

      $qr .= "AND z.warehouse_code IN({$wh_list}) ";
    }

    $qr .= "GROUP BY pd.code ";
    $qr .= "ORDER BY pd.style_code ASC, ";
    $qr .= "pd.color_code ASC, ";
    $qr .= "ps.position ASC";

    $rs = $this->db->query($qr);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }
}

 ?>

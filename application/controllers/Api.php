<?php
class Api extends CI_Controller
{
  private $web_url = 'http://34.97.150.198/rest/V1/';
  private $userData = array('username' => 'user', 'password' => 'W@rr1X$p0rt');
  private $token_url = "http://34.97.150.198/rest/V1/integration/admin/token";
  private $token;
  public function __construct()
  {
    parent::__construct();
    $this->load->model('stock/stock_model');
    $this->load->model('orders/orders_model');
  }

  private function get_token()
  {
    $ch = curl_init($this->token_url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->userData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-Lenght: " . strlen(json_encode($this->userData))));

    $this->token = trim(curl_exec($ch), '""');
  }


  public function update_receive_stock($code)
  {
    $this->load->model('inventory/receive_po_model');
    $details = $this->receive_po_model->get_details($code);
    if(!empty($details))
    {
      $this->get_token();
      if(!empty($this->token))
      {
        if(!empty($details))
        {
          foreach($details as $rs)
          {
            $this->update_web_stock($rs->product_code, $qty);
          }
        }
      }
    }
  }


  
  public function get_available_stock($item)
  {
    $sell_stock = $this->stock_model->get_sell_stock($item);
    $reserv_stock = $this->orders_model->get_reserv_stock($item);
    $availableStock = $sell_stock - $reserv_stock;
    return $availableStock < 0 ? 0 : $availableStock;
  }


  public function update_api_stock($item)
  {
    $available_stock = $this->get_available_stock($item);
    $this->update_web_stock($item, $available_stock);
  }


  public function update_web_stock($item, $qty)
  {
    $token = $this->token;
    $url = $this->web_url."products/{$item}/stockItems/1";
    $setHeaders = array("Content-Type:application/json","Authorization:Bearer {$token}");
    $apiUrl = str_replace(" ","%20",$url);
    $method = 'PUT';
    $data = ["stockItem" => ["qty" => $qty]];

    $data_string = json_encode($data);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $setHeaders);
    $result = curl_exec($ch);
  }

}

 ?>

<?php
class Test_api extends CI_Controller
{
  private $web_url = 'http://34.97.150.198/rest/V1/';
  private $userData = array('username' => 'user', 'password' => 'W@rr1X$p0rt');
  private $token_url = "http://34.97.150.198/rest/V1/integration/admin/token";
  public function __construct()
  {
    parent::__construct();
  }

  public function index()
  {
    $this->load->library('api');
    $item = 'WA-19FT53M-DD-M';
    $qty = 59;
    $this->api->send_stock($item, $qty);
  }

  private function get_token()
  {
    $ch = curl_init($this->token_url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->userData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-Lenght: " . strlen(json_encode($this->userData))));

    return curl_exec($ch);
  }


  public function update_web_stock($item, $qty)
  {
    $token = trim($this->get_token(), '""');
    $url = $this->web_url."products/{$item}/stockItems/1";
    $setHeaders = array("Content-Type:application/json","Authorization:Bearer {$token}");
    $apiUrl = str_replace(" ","%20",$url);
    $method = 'PUT';
    $data = ["stockItem" => ["qty" => $qty]];

    $data_string = json_encode($data);
     // echo $token.'<br/>';
     // echo $url .'<br/>';
     // echo '<pre>' ; print_r($setHeaders) .'</pre>';
     // echo $data_string;
     // return;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $setHeaders);
    $result = curl_exec($ch);

    echo $result;
    // $response = json_decode( curl_exec($ch), TRUE);
    // curl_close($ch);
  }

  public function update_stock($item, $qty)
  {
    $this->load->library('api');
    $rs = $this->api->update_web_stock($item, $qty);
    echo $rs;
  }


  public function test()
  {
    $data = ["stockItems" => [ "qty" => 20  ] ];
    echo json_encode($data);
  }
}

 ?>

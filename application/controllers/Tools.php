<?php
class Tools extends CI_Controller
{
  public $ms;

  public function __construct()
  {
    parent::__construct();
  }

  public function set_rows()
  {
    if($this->input->post('set_rows') && $this->input->post('set_rows') > 0)
    {
      $rows = intval($this->input->post('set_rows'));
      $cookie = array(
        'name' => 'rows',
        'value' => $rows > 300 ? 300 : $rows,
        'expire' => 2592000, //--- 30 days
        'path' => '/'
      );

      $this->input->set_cookie($cookie);
    }

    echo 'done';
  }


  public function change_language($lang)
  {
    $cookie = array(
      'name' => 'display_lang',
      'value' => $lang,
      'expire' => 259200000, //--- 30 days
      'path' => '/'
    );

    $this->input->set_cookie($cookie);
  }
}

 ?>

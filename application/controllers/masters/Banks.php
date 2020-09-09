<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Banks extends PS_Controller
{
  public $menu_code = 'DBCHAN';
	public $menu_group_code = 'DB';
	public $title = 'Sale Channels';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/channels';
    $this->load->model('masters/channels_model');
  }
}
?>

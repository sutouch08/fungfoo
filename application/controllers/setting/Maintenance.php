<?php
class Maintenance extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    //--- get permission for user
    $this->pm = get_permission('SCSYSC', get_cookie('uid'), get_cookie('id_profile'));
    $this->load->model('setting/config_model');
  }


  public function index()
  {
    if(getConfig('CLOSE_SYSTEM') == 0)
    {
      redirect(base_url());
    }

    $this->load->view('maintenance');
  }

  public function open_system()
  {
    if($this->pm->can_add OR $this->pm->can_edit OR $this->pm->can_delete)
    {
      $rs = $this->config_model->update('CLOSE_SYSTEM', 0);
      echo $rs === TRUE ? 'success' : 'fail';
    }
  }


  public function check_open_system()
  {
    $rs = $this->config_model->get('CLOSE_SYSTEM');
    echo $rs == 1 ? 'close' : 'open';
  }


}


 ?>

<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Configs extends PS_Controller
{
  public $menu_code = 'SCCONF';
	public $menu_group_code = 'SC';
  public $menu_sub_group_code = 'CONFIG';
	public $title = 'การกำหนดค่า';
  public $error = '';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'setting/configs';
    $this->load->model('setting/config_model');
  }



  public function index()
  {
    $groups = $this->config_model->get_group();
    $ps = get_permission('SCSYSC');
    $cando = ($ps->can_add + $ps->can_edit + $ps->can_delete) > 0 ? TRUE : FALSE;
    $ds = array();
    foreach($groups as $rs)
    {
       $group = $this->config_model->get_config_by_group($rs->code);
       if(!empty($group))
       {
         foreach($group as $rd)
         {
           $ds[$rd->code] = $this->config_model->get($rd->code);
         }
       }
    }

    $ds['cando'] = $cando;

    $this->load->view('setting/configs', $ds);
  }



  public function update_config()
  {
    $sc = TRUE;
    if($this->input->post())
    {
      $this->error = "Cannot update : ";
      $configs = $this->input->post();
      foreach($configs as $name => $value)
      {
        if(! $this->config_model->update($name, $value))
        {
          $sc = FALSE;
          $this->error .= "{$name}, ";
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Form content not found";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }

} //-- end class
?>

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profiles extends PS_Controller{
	public $menu_code = 'DBPROF'; //--- Add/Edit Profile
	public $menu_group_code = 'DB'; //--- System security
	public $title = 'Profiles';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'users/profiles';
    $this->load->model('users/profile_model');
  }




  public function index()
  {
		$profileName = get_filter('profileName', 'profileName', '');

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 4; //-- url segment
		$rows = $this->profile_model->count_rows($profileName);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$result = $this->profile_model->get_profiles($profileName, $perpage, $this->uri->segment($segment));
    $data = array();

    if(!empty($result))
    {
      foreach($result as $rs)
      {
        $row = new stdClass();
        $row->id = $rs->id;
        $row->name = $rs->name;
        $row->member = $this->profile_model->count_members($rs->id);
        $data[] = $row;
      }
    }

    $ds = array(
      'profileName' => $profileName,
			'data' => $data
    );

		$this->pagination->initialize($init);

    $this->load->view('users/profile_view', $ds);
  }




  public function add_profile()
  {
    $data['pname'] = $this->session->flashdata('profileName');
    $this->title = 'New profile';
    $this->load->view('users/profile_add_view', $data);
  }




  public function edit_profile($id)
  {
    $data['data'] = $this->profile_model->get_profile($id);
    $this->title = 'Edit Profile';
    $this->load->view('users/profile_edit_view', $data);
  }




  public function update_profile()
  {
    if($this->input->post('profile_id'))
    {
      $id = $this->input->post('profile_id');
      $name = $this->input->post('profileName');

      if($this->profile_model->is_extsts($name, $id) === FALSE)
      {
        if($this->profile_model->update($id, $name))
        {
          set_message('Profile updated');
        }
        else
        {
          set_error('Update profile not successfull');
        }
      }
      else
      {
        set_error("Profile '".$name."' already exists please choose another");
      }
    }
    else
    {
      set_error('Not found : profile_id');
    }

    redirect($this->home.'/edit_profile/'.$id);
  }





  public function new_profile()
  {
    if($this->input->post('profileName'))
    {
      $name = $this->input->post('profileName');

      if($this->profile_model->is_extsts($name) === FALSE)
      {
        if($this->profile_model->add($name))
        {
          set_message('Profile created successfully');
        }
      }
      else
      {
        set_error('Profile name already exists, please choose another');
        $this->session->set_flashdata('profileName', $name); //--- ไว้แสดงผลหน้าต่อไป
      }
    }
    else
    {
      set_error('Invalid profile name');
    }

    redirect($this->home.'/add_profile');
  }





  public function delete_profile($id)
  {
    if($this->profile_model->delete($id))
    {
      set_message("Profile has been deleted");
    }
    else
    {
      set_error("Failed to delete profile");
    }

    redirect($this->home);
  }




  public function clear_filter()
	{
		clear_filter('profileName');
		echo 'done';
	}
}
?>

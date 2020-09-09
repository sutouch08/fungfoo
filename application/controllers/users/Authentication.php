<?php
class Authentication extends CI_Controller
{

  public function __construct()
	{
		parent::__construct();
		//$this->home = base_url()."authentication";
	}


	public function index()
	{
		$this->load->view("login");
	}



	public function validate_credentials()
	{
    $sc = TRUE;
    $user_name = $this->input->post('user_name');
    $pwd = $this->input->post('password');
    $remember = $this->input->post('remember');
		$rs = $this->user_model->get_user_credentials($user_name);

    if(! empty($rs))
    {
      if($rs->active == 0 )
      {
        $sc = FALSE;
        $message = 'Your account has been suspended';
        $this->session->set_flashdata('error_message', $message);
      }
      else if(password_verify($pwd, $rs->pwd))
      {
        $ds = array(
          'uid' => $rs->uid,
          'uname' => $rs->uname,
          'displayName' => $rs->name,
          'id_profile' => $rs->id_profile
        );

        $this->create_user_data($ds, $remember);
      }
      else
      {
        $sc = FALSE;
        $message = 'Username or password is incorrect';
        $this->session->set_flashdata('error_message', $message);
      }
    }
    else
    {
      $sc = FALSE;
      $message = 'Username or password is incorrect';
      $this->session->set_flashdata('error_message', $message);
    }

    if($sc === TRUE)
    {
      redirect('welcome');
    }
    else
    {
      redirect('users/authentication');
    }
	}



  public function create_user_data(array $ds = array(), $remember )
  {
    if(!empty($ds))
    {
      $times = $remember == 1 ? intval(60*60*24*30) : intval(60*60*12);

      foreach($ds as $key => $val)
      {
        $cookie = array(
          'name' => $key,
          'value' => $val,
          'expire' => $times,
          'path' => '/'
        );

        $this->input->set_cookie($cookie);
      }
    }
  }




	public function logout()
	{
		delete_cookie('uid');
    delete_cookie('displayName');
    delete_cookie('id_profile');
    redirect('users/authentication');
	}


} //--- end class


 ?>

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends Main_Controller
{
	public function index()
	{
		$this->check_login();

		redirect('user/account');
	}

	public function register()
	{
		$this->load->library('form_validation');
		$this->load->helper(array('form', 'url'));
		$this->load->model('User_model');

		$data = array(
			'mainview' => 'register',
			'title' => 'Register',
		);

		$rules = array(
			array(
				'field' => 'password',
				'label' => 'Password',
				'rules' => 'required|min_length[8]|max_length[64]'
			),
			array(
				'field' => 're-password',
				'label' => 'Confirm Password',
				'rules' => 'required|matches[password]'
			),
			array(
				'field' => 'email',
				'label' => 'Email',
				'rules' => 'required|max_length[64]|valid_email|is_unique[user.email]'
			),
			array(
				'field' => 'first_name',
				'label' => 'First Name',
				'rules' => 'required|min_length[3]|max_length[64]'
			),
			array(
				'field' => 'last_name',
				'label' => 'Last Name',
				'rules' => 'required|min_length[3]|max_length[64]'
			)
		);
		$this->form_validation->set_rules($rules);
		if ($this->form_validation->run() == true) {
			$data = array(
				'email' => $this->input->post('email'),
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'password' => $this->input->post('password'),
				'type' => 2
			);

			if ($userID = $this->User_model->insert($data)) {
				$this->User_model->set($userID);
				$this->_set_session_data();
				redirect();
			}
		}

		$this->load_view($data);
	}

	public function login()
	{
		$this->load->helper('url');
		$this->load->library('form_validation');
		$this->load->helper(array('form', 'url'));
		$this->load->model('User_model');

		$data = array(
			'mainview' => 'login',
			'title' => 'Login',
		);

		$rules = array(
			array(
				'field' => 'email',
				'label' => 'Email',
				'rules' => 'required'
			),
			array(
				'field' => 'password',
				'label' => 'Password',
				'rules' => 'required'
			),
		);
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() == true) {
			if ($this->User_model->login($this->input->post('email'), $this->input->post('password'), 2)) {
				$this->_set_session_data();
				redirect();
			}
		}
		$this->load_view($data);
	}

	public function logout()
	{
		$this->load->helper('url');
		$this->session->sess_destroy();
		redirect();
	}

	public function account()
	{
		$data = array(
			'mainview' => 'account',
			'title' => 'Account',
		);

		$this->check_login();

		$this->load->library('form_validation');
		$this->load->model('User_model');
		$userID = $this->session->userdata('userID');

		$this->User_model->set($userID);

		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$update = array();
			$fields = array(
				'email' => array(
					'field' => 'email',
					'label' => 'Email',
					'rules' => 'required|valid_email|max_length[75]|is_unique[user.email]',
				),
				'first_name' => array(
					'field' => 'first_name',
					'label' => 'First Name',
					'rules' => 'required|max_length[45]',
				),
				'last_name' => array(
					'field' => 'last_name',
					'label' => 'Last Name',
					'rules' => 'required|max_length[45]',
				),
			);

			foreach ($fields as $f_name => $field) {
				if ($this->input->post($f_name) != $this->User_model->{$field['field']}) {
					$this->form_validation->set_rules(array($field));
					$update[$field['field']] = $this->input->post($f_name);
				}
			}

			if (!$update) {
				$data['alert_message'] = 'Enter data you want to update.';
				$data['alert_class'] = 'alert-error';
			} else {
				$this->form_validation->set_rules(
					'password',
					'Password',
					'required|checkpassword'
				);
			}

			if ($this->form_validation->run() == true) {
				if ($this->User_model->update($update, $userID)) {
					$data['alert_message'] = 'Your data has been updated successfully.';
					$data['alert_class'] = 'alert-success';
					$this->User_model->set($userID);
					$this->_set_session_data();
				} else {
					$data['alert_message'] = 'Something went wrong.';
					$data['alert_class'] = 'alert-error';
				}
			}
		}

		$this->User_model->set($userID);

		$data['userID'] = $this->User_model->user_id;
		$data['userFirstName'] = $this->User_model->first_name;
		$data['userLastName'] = $this->User_model->last_name;
		$data['userEmail'] = $this->User_model->email;

		$this->load_view($data);
	}

	public function password()
	{
		$this->check_login();

		$this->load->model('User_model');
		$this->load->library('form_validation');

		$data = array(
			'mainview' => 'password',
			'title' => 'Password',
		);

		$rules = array(
			array(
				'field' => 'password',
				'label' => 'Current Password',
				'rules' => 'required|checkpassword'
			),
			array(
				'field' => 'new-password',
				'label' => 'New Password',
				'rules' => 'required|min_length[8]|max_length[64]'
			),
			array(
				'field' => 're-password',
				'label' => 'Re-enter New Password',
				'rules' => 'required|matches[new-password]'
			)
		);

		$this->form_validation->set_rules($rules);

		if($this->form_validation->run() == true) {
			$update = array('password' => $this->input->post('new-password'));
			if($this->User_model->update($update, $this->session->userdata('userID'))) {
				$data['alert_message'] = 'The password is updated.';
				$data['alert_class'] = 'alert-success';
 			}
		}

		$this->load_view($data);
	}

	private function _set_session_data()
	{
		$session = array(
			'userID' => $this->User_model->user_id,
			'userFirstName' => $this->User_model->first_name,
			'userLastName' => $this->User_model->last_name,
			'userFullName' =>  $this->User_model->first_name.' '.$this->User_model->last_name,
			'userLoggedIn' => true
		);

		$this->session->set_userdata($session);
	}
}

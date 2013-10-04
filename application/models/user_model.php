<?php

class User_model extends MY_Model
{
	public $userID;
	public $userEmail;
	public $userFirstName;
	public $userLastName;
	public $userType;

	public function __construct()
	{
		parent::__construct();
		parent::initialize('user', 'userID');
	}
	
	/**
	 *	User Authentication
	 *
	 *	@param string The email of user
	 *	@param string The password of user
	 *	@return bool
	 */
	public function login($email, $password, $userType = null)
	{
		$this->load->database();

		$this->db->from('user')
			->where('userEmail', $email)
			->where('userPassword', $password);
		if($userType == 2)
			$this->db->where('userType', $userType);
		$query = $this->db->get();

		if ($query->num_rows() != 1) {
			return false;
		}

		$row = $query->row();
		$this->userID = $row->userID;
		$this->userEmail = $row->userEmail;
		$this->userFirstName = $row->userFirstName;
		$this->userLastName = $row->userLastName;
		$this->userType = $row->userType;

		return true;
	}

	/**
	 *	Checking user is logged
	 *
	 */
	public function admin_logged()
	{
		$this->load->library('session');
		$this->load->helper('url');
		$check = $this->session->userdata('logged_in');

		if (!$check) {
			redirect('/admin/login', 'location');
		}
	}

	/**
	 *	Checking user's password is correct
	 *
	 *	@param string The password of user.
	 *	@param string The userID of user.
	 *	@return bool
	 */
	public function checkpassword($password, $id)
	{
		$this->load->database();
		$this->db->from('user');

		$data = array('userID' => $id, 'userPassword' => $password);

		$query = $this->db->where($data)->get();

		if ($query->num_rows() != 1) {
			return false;
		}

		return true;
	}

	public function insert($data)
	{
		$this->load->database();
		if($this->db->insert('user', $data)) {
			$this->userID = $this->db->insert_id();
			return true;
		} else {
			return false;
		}
	}
}

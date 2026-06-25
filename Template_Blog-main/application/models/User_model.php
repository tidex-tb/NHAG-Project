<?php
require_once(APPPATH . 'models/Base_Model.php');

class User_model extends Base_Model
{
	protected $table = 'users';

	public function __construct()
	{
		parent::__construct();
	}

	public function register($data)
	{
		return $this->db->insert('users', $data) ? $this->db->insert_id() : false;
	}

	public function authenticate($username, $password)
	{
		$user = $this->db
			->group_start()
			->where('username', $username)
			->or_where('email', $username)
			->group_end()
			->where('is_active', 1)
			->get('users')
			->row_array();

		if ($user && password_verify($password, $user['password'])) {
			return $user;
		}

		return false;
	}

	public function has_permission($user_id, $permission_name)
	{
		$user = $this->db->select('role_id')->get_where('users', array('id' => $user_id))->row_array();

		if (!$user) return false;

		// Super Admin has all permissions
		if ((int) $user['role_id'] === 1) {
			return true;
		}

		$result = $this->db
			->select('p.id')
			->from('permissions p')
			->join('role_permissions rp', 'p.id = rp.permission_id')
			->where('rp.role_id', $user['role_id'])
			->where('p.name', $permission_name)
			->count_all_results();

		return $result > 0;
	}

	public function count_users()
	{
		return $this->db->count_all_results('users');
	}

	public function check_username_exists($username)
	{
		return $this->db
			->where('username', $username)
			->count_all_results('users') == 0;
	}

	public function check_email_exists($email)
	{
		return $this->db
			->where('email', $email)
			->count_all_results('users') == 0;
	}
}


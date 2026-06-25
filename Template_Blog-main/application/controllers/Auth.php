<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Auth Controller - Authentication and Authorization
 */
class Auth extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('user_model');
		$this->load->model('member_model');
		$this->load->model('audit_model');
	}

	/**
	 * Login page
	 */
	public function login()
	{
		if ($this->session->userdata('logged_in')) {
			redirect('dashboard');
		}

		if ($this->input->post()) {
			$username = $this->input->post('username');
			$password = $this->input->post('password');

			$user = $this->user_model->authenticate($username, $password);

			if ($user) {
				$this->session->sess_regenerate(TRUE);

				// Create session
				$session_data = array(
					'user_id' => $user['id'],
					'username' => $user['username'],
					'name' => $user['name'],
					'email' => $user['email'],
					'role_id' => $user['role_id'],
					'logged_in' => true
				);

				$this->session->set_userdata($session_data);

				// Log login activity
				$this->audit_model->log_login($user['id']);

				$this->session->set_flashdata('success', 'Welcome back, ' . $user['name'] . '!');
				redirect('dashboard');
			} else {
				$this->session->set_flashdata('error', 'Invalid username or password');
			}
		}

		$this->load->view('auth/login');
	}

	/**
	 * Register page
	 */
	public function register()
	{
		if ($this->session->userdata('logged_in')) {
			redirect('dashboard');
		}

		if ($this->input->post()) {
			$this->load->library('form_validation');

			$this->form_validation->set_rules('name', 'Name', 'required');
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]');
			$this->form_validation->set_rules('username', 'Username', 'required|is_unique[users.username]');
			$this->form_validation->set_rules('national_id', 'National ID', 'required|is_unique[members.national_id]');
			$this->form_validation->set_rules('phone_number', 'Phone Number', 'required');
			$this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
			$this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]');

			if ($this->form_validation->run() == FALSE) {
				$data['errors'] = $this->form_validation->error_array();
				$this->load->view('auth/register', $data);
			} else {
				$role_id = $this->user_model->count_users() === 0 ? 1 : 4;

				$user_data = array(
					'name'     => $this->input->post('name'),
					'email'    => $this->input->post('email'),
					'username' => $this->input->post('username'),
					'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
					'role_id'  => $role_id,
					'is_active' => 1
				);

				$user_id = $this->user_model->register($user_data);
				if ($user_id) {
					if ((int) $role_id === 4) {
						$name_parts = preg_split('/\s+/', trim($this->input->post('name')), 2);
						$member_data = array(
							'user_id' => $user_id,
							'first_name' => $name_parts[0],
							'last_name' => $name_parts[1] ?? $name_parts[0],
							'national_id' => $this->input->post('national_id'),
							'phone_number' => $this->input->post('phone_number'),
							'email' => $this->input->post('email')
						);
						$this->member_model->create_member($member_data);
					}
					$this->session->set_flashdata('success', 'Registration successful! Please login.');
					redirect('auth/login');
				} else {
					$this->session->set_flashdata('error', 'Registration failed. Please try again.');
				}
			}
		}

		$this->load->view('auth/register');
	}

	/**
	 * Logout
	 */
	public function logout()
	{
		$user_id = $this->session->userdata('user_id');

		if ($user_id) {
			$this->audit_model->log_logout($user_id);
		}

		$this->session->sess_destroy();
		redirect('auth/login');
	}

	/**
	 * Require login
	 */
	public function require_login()
	{
		if (!$this->session->userdata('logged_in')) {
			redirect('auth/login');
		}
	}

	/**
	 * Require permission
	 */
	public function require_permission($permission_name)
	{
		$user_id = $this->session->userdata('user_id');

		if (!$user_id || !$this->user_model->has_permission($user_id, $permission_name)) {
			show_403();
		}
	}
}


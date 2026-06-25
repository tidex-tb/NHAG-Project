<?php

class Users extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->model('user_model');
		$this->load->library('form_validation');
	}

	public function register()
	{
		$data['title'] = 'Register';

		$this->form_validation->set_rules(
			'name',
			'Name',
			'required'
		);

		$this->form_validation->set_rules(
			'username',
			'Username',
			'required|min_length[4]|callback_check_username_exists'
		);

		$this->form_validation->set_rules(
			'email',
			'Email',
			'required|valid_email|callback_check_email_exists'
		);

		$this->form_validation->set_rules(
			'password',
			'Password',
			'required|min_length[6]'
		);

		$this->form_validation->set_rules(
			'password2',
			'Confirm Password',
			'required|matches[password]'
		);

		if ($this->form_validation->run() === FALSE) {

			$this->load->view('layouts/header');
			$this->load->view('users/register', $data);
		} else {

			$user_data = [
				'name'      => $this->input->post('name'),
				'username'  => $this->input->post('username'),
				'email'     => $this->input->post('email'),
				'zipcode'   => $this->input->post('zipcode'),
				'password'  => password_hash(
					$this->input->post('password'),
					PASSWORD_DEFAULT
				)
			];

			$this->user_model->register($user_data);

			$this->session->set_flashdata(
				'success',
				'Registration successful.'
			);

			redirect('auth/login');
		}
	}

	public function check_username_exists($username)
	{
		if ($this->user_model->check_username_exists($username)) {
			return true;
		}

		$this->form_validation->set_message(
			'check_username_exists',
			'Username already exists.'
		);

		return false;
	}

	public function check_email_exists($email)
	{
		if ($this->user_model->check_email_exists($email)) {
			return true;
		}

		$this->form_validation->set_message(
			'check_email_exists',
			'Email already exists.'
		);

		return false;
	}
}

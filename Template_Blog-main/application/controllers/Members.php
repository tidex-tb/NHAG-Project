<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Members Controller - Member management
 */
class Members extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		// Require login
		if (!$this->session->userdata('logged_in')) {
			redirect('auth/login');
		}

		$this->load->model('member_model');
		$this->load->model('member_profile_model');
		$this->load->model('user_model');
		$this->load->library('form_validation');
	}

	/**
	 * List members
	 */
	public function index()
	{
		// Check permission
		if (!$this->user_model->has_permission($this->session->userdata('user_id'), 'view_members')) {
			show_403();
		}

		$page = $this->input->get('page') ?? 0;
		$limit = 20;

		$data = array(
			'page_title' => 'Members',
			'page_heading' => 'Member Management',
			'breadcrumbs' => array(
				'Dashboard' => 'dashboard',
				'Members' => 'members'
			)
		);

		// Get members with pagination
		$data['members'] = $this->member_model->get_active_members($limit, $page * $limit);
		$data['total_members'] = $this->member_model->get_count();

		// Pagination
		$this->load->library('pagination');
		$config = array(
			'base_url' => base_url('members?page='),
			'total_rows' => $data['total_members'],
			'per_page' => $limit,
			'use_page_numbers' => true
		);
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();


		$this->load->view('members/index', $data);
	}

	/**
	 * View member
	 */
	public function view($member_id)
	{
		if (!$this->_can_access_member($member_id)) {
			show_403();
		}

		$data = array(
			'page_title' => 'Member Profile',
			'breadcrumbs' => array(
				'Dashboard' => 'dashboard',
				'Members' => 'members',
				'Profile' => ''
			)
		);

		$member = $this->member_model->get_member_with_profile($member_id);

		if (!$member) {
			show_404();
		}

		$data['member'] = $member;
		$data['contributions'] = $this->member_model->get_member_contributions($member_id);

		$this->load->model('ledger_model');
		$data['ledger'] = $this->ledger_model->get_member_ledger($member_id);


		$this->_render('members/view', $data, 'members');
	}

	/**
	 * Create new member
	 */
	public function create()
	{
		// Check permission
		if (!$this->user_model->has_permission($this->session->userdata('user_id'), 'create_member')) {
			show_403();
		}

		if ($this->input->post()) {
			$this->form_validation->set_rules('first_name', 'First Name', 'required');
			$this->form_validation->set_rules('last_name', 'Last Name', 'required');
			$this->form_validation->set_rules('national_id', 'National ID', 'required|is_unique[members.national_id]');
			$this->form_validation->set_rules('email', 'Email', 'valid_email');
			$this->form_validation->set_rules('phone_number', 'Phone Number', 'required');

			if ($this->form_validation->run() == FALSE) {
				$data['errors'] = $this->form_validation->error_array();
			} else {
				$member_data = array(
					'first_name' => $this->input->post('first_name'),
					'last_name' => $this->input->post('last_name'),
					'gender' => $this->input->post('gender'),
					'date_of_birth' => $this->input->post('date_of_birth'),
					'national_id' => $this->input->post('national_id'),
					'phone_number' => $this->input->post('phone_number'),
					'email' => $this->input->post('email'),
					'physical_address' => $this->input->post('physical_address'),
					'next_of_kin_name' => $this->input->post('next_of_kin_name'),
					'next_of_kin_phone' => $this->input->post('next_of_kin_phone'),
					'next_of_kin_relationship' => $this->input->post('next_of_kin_relationship'),
					'employment_status' => $this->input->post('employment_status'),
					'employer_name' => $this->input->post('employer_name'),
					'monthly_income' => $this->input->post('monthly_income')
				);

				$member_id = $this->member_model->create_member($member_data);

				if ($member_id) {
					// Send notification
					$this->load->model('notification_model');
					$this->notification_model->notify_member(
						$member_id,
						'Registration Successful',
						'Welcome to NHAG Savings Platform. Your account has been created successfully.',
						'Registration'
					);

					$this->session->set_flashdata('success', 'Member created successfully');
					redirect('members/view/' . $member_id);
				} else {
					$this->session->set_flashdata('error', 'Error creating member');
				}
			}
		}

		$data = array(
			'page_title' => 'Create Member',
			'page_heading' => 'Add New Member',
			'breadcrumbs' => array(
				'Dashboard' => 'dashboard',
				'Members' => 'members',
				'Create' => ''
			)
		);


		$this->load->view('members/create', $data);
	}

	/**
	 * Edit member
	 */
	public function edit($member_id)
	{
		// Check permission
		if (!$this->user_model->has_permission($this->session->userdata('user_id'), 'edit_member')) {
			show_403();
		}

		$member = $this->member_model->get_by_id($member_id);

		if (!$member) {
			show_404();
		}

		if ($this->input->post()) {
			$this->form_validation->set_rules('first_name', 'First Name', 'required');
			$this->form_validation->set_rules('last_name', 'Last Name', 'required');

			if ($this->form_validation->run() == FALSE) {
				$data['errors'] = $this->form_validation->error_array();
			} else {
				$update_data = array(
					'first_name' => $this->input->post('first_name'),
					'last_name' => $this->input->post('last_name'),
					'gender' => $this->input->post('gender'),
					'phone_number' => $this->input->post('phone_number'),
					'email' => $this->input->post('email'),
					'physical_address' => $this->input->post('physical_address'),
					'next_of_kin_name' => $this->input->post('next_of_kin_name'),
					'next_of_kin_phone' => $this->input->post('next_of_kin_phone'),
					'next_of_kin_relationship' => $this->input->post('next_of_kin_relationship'),
					'employment_status' => $this->input->post('employment_status'),
					'employer_name' => $this->input->post('employer_name'),
					'monthly_income' => $this->input->post('monthly_income')
				);

				$result = $this->member_model->update_record($member_id, $update_data);

				if ($result) {
					$this->session->set_flashdata('success', 'Member updated successfully');
					redirect('members/view/' . $member_id);
				} else {
					$this->session->set_flashdata('error', 'Error updating member');
				}
			}
		}

		$data = array(
			'page_title' => 'Edit Member',
			'page_heading' => 'Edit Member Information',
			'breadcrumbs' => array(
				'Dashboard' => 'dashboard',
				'Members' => 'members',
				'Edit' => ''
			),
			'member' => $member
		);


		$this->_render('members/edit', $data, 'members');
	}

	/**
	 * Suspend member
	 */
	public function suspend($member_id)
	{
		// Check permission
		if (!$this->user_model->has_permission($this->session->userdata('user_id'), 'suspend_member')) {
			show_403();
		}

		$member = $this->member_model->get_by_id($member_id);

		if (!$member) {
			show_404();
		}

		$reason = $this->input->post('reason');
		$result = $this->member_model->suspend_member($member_id, $reason);

		if ($result) {
			$this->session->set_flashdata('success', 'Member suspended successfully');
		} else {
			$this->session->set_flashdata('error', 'Error suspending member');
		}

		redirect('members/view/' . $member_id);
	}

	/**
	 * Activate member
	 */
	public function activate($member_id)
	{
		// Check permission
		if (!$this->user_model->has_permission($this->session->userdata('user_id'), 'activate_member')) {
			show_403();
		}

		$member = $this->member_model->get_by_id($member_id);

		if (!$member) {
			show_404();
		}

		$reason = $this->input->post('reason');
		$result = $this->member_model->activate_member($member_id, $reason);

		if ($result) {
			$this->session->set_flashdata('success', 'Member activated successfully');
		} else {
			$this->session->set_flashdata('error', 'Error activating member');
		}

		redirect('members/view/' . $member_id);
	}

	/**
	 * Search members (AJAX)
	 */
	public function search()
	{
		if (!$this->user_model->has_permission($this->session->userdata('user_id'), 'view_members')) {
			show_403();
		}

		$this->output->set_content_type('application/json');

		$search_term = $this->input->get('q');
		$members = $this->member_model->search_members($search_term);

		echo json_encode(array(
			'success' => true,
			'members' => $members
		));
	}

	private function _can_access_member($member_id)
	{
		if ($this->user_model->has_permission($this->session->userdata('user_id'), 'view_members')) {
			return true;
		}

		$this->db->where('id', $member_id);
		$this->db->where('user_id', $this->session->userdata('user_id'));

		return $this->db->count_all_results('members') > 0;
	}
	private function _render($view, $data = array(), $current_page = 'members')
	{
		$this->load->view('layouts/header', array(
			'show_sidebar' => true,
			'page_title' => $data['page_title'] ?? 'Members',
			'current_page' => $current_page,
			'is_admin' => in_array((int) $this->session->userdata('role_id'), array(1, 2))
		));
		$this->load->view($view, $data);
		$this->load->view('layouts/footer');
	}
}


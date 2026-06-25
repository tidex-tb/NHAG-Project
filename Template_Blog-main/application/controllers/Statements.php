<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Statements Controller - Member statements
 */
class Statements extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		if (!$this->session->userdata('logged_in')) {
			redirect('auth/login');
		}

		$this->load->model('member_model');
		$this->load->model('ledger_model');
		$this->load->model('user_model');
	}

	/**
	 * List statements
	 */
	public function index()
	{
		if (!$this->user_model->has_permission($this->session->userdata('user_id'), 'view_statement')) {
			show_403();
		}

		$is_staff = in_array((int) $this->session->userdata('role_id'), array(1, 2, 3));
		$member = null;

		if (!$is_staff) {
			$member = $this->db->get_where('members', array('user_id' => $this->session->userdata('user_id')))->row_array();

			if (!$member) {
				show_404();
			}
		}

		$this->db->select('s.*, m.member_number, m.first_name, m.last_name');
		$this->db->from('statements s');
		$this->db->join('members m', 'm.id = s.member_id', 'left');

		if (!$is_staff) {
			$this->db->where('m.user_id', $this->session->userdata('user_id'));
		}

		$this->db->order_by('s.period_start', 'DESC');
		$statements = $this->db->get()->result_array();

		$data = array(
			'page_title' => 'My Statements',
			'page_heading' => 'Savings Statements',
			'member' => $member,
			'is_staff' => $is_staff,
			'statements' => $statements,
			'breadcrumbs' => array(
				'Dashboard' => 'dashboard',
				'Statements' => 'statements'
			)
		);


		$this->load->view('statements/index', $data);
	}

	/**
	 * View statement
	 */
	public function view($statement_id)
	{
		$this->db->where('id', $statement_id);
		$statement = $this->db->get('statements')->row_array();

		if (!$statement) {
			show_404();
		}

		$member = $this->member_model->get_by_id($statement['member_id']);

		if (!$this->_can_access_member($member['id'])) {
			show_403();
		}

		$ledger = $this->ledger_model->get_by_date_range($member['id'], $statement['period_start'], $statement['period_end']);

		$data = array(
			'page_title' => 'Statement',
			'member' => $member,
			'statement' => $statement,
			'ledger' => $ledger,
			'breadcrumbs' => array(
				'Dashboard' => 'dashboard',
				'Statements' => 'statements',
				'View' => ''
			)
		);


		$this->_render('statements/view', $data);
	}

	/**
	 * Generate custom statement
	 */
	public function generate()
	{
		if (!$this->user_model->has_permission($this->session->userdata('user_id'), 'download_statement')) {
			show_403();
		}

		$this->db->where('user_id', $this->session->userdata('user_id'));
		$member = $this->db->get('members')->row_array();

		if (!$member) {
			show_404();
		}

		if ($this->input->post()) {
			$this->load->library('form_validation');
			$this->form_validation->set_rules('start_date', 'Start Date', 'required');
			$this->form_validation->set_rules('end_date', 'End Date', 'required');

			$start_date = $this->input->post('start_date');
			$end_date = $this->input->post('end_date');

			if ($this->form_validation->run() == FALSE || !$this->_valid_date_range($start_date, $end_date)) {
				$data['errors'] = $this->form_validation->error_array();
				if (!$this->_valid_date_range($start_date, $end_date)) {
					$data['errors'][] = 'Please enter a valid date range.';
				}
			} else {
				$statement_id = $this->_create_statement($member['id'], $start_date, $end_date, 'Custom');

				if ($statement_id) {
					$this->session->set_flashdata('success', 'Statement generated successfully');
					redirect('statements/view/' . $statement_id);
				}
			}
		}

		$data = array(
			'page_title' => 'Generate Statement',
			'page_heading' => 'Generate Custom Statement',
			'member' => $member,
			'breadcrumbs' => array(
				'Dashboard' => 'dashboard',
				'Statements' => 'statements',
				'Generate' => ''
			)
		);


		$this->_render('statements/generate', $data);
	}

	/**
	 * Download PDF
	 */
	public function download_pdf($statement_id)
	{
		$this->db->where('id', $statement_id);
		$statement = $this->db->get('statements')->row_array();

		if (!$statement) {
			show_404();
		}

		// Check permission
		if (!$this->_can_access_member($statement['member_id'])) {
			show_403();
		}

		// Generate PDF
		$this->_generate_pdf($statement);
	}

	/**
	 * Create statement
	 */
	private function _create_statement($member_id, $start_date, $end_date, $type = 'Custom')
	{
		$this->load->model('system_setting_model');
		$prefix = $this->system_setting_model->get_setting('statement_number_prefix', 'STMT');

		$statement_number = $prefix . date('Y') . str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT);

		// Get ledger summary
		$summary = $this->ledger_model->get_summary($member_id, $start_date, $end_date);

		$statement_data = array(
			'statement_number' => $statement_number,
			'member_id' => $member_id,
			'statement_type' => $type,
			'period_start' => $start_date,
			'period_end' => $end_date,
			'opening_balance' => 0,
			'total_contributions' => $summary['total_credits'] ?? 0,
			'total_adjustments' => 0,
			'closing_balance' => $summary['total_credits'] ?? 0,
			'generated_by' => $this->session->userdata('user_id')
		);

		return $this->db->insert('statements', $statement_data) ? $this->db->insert_id() : false;
	}

	/**
	 * Generate PDF
	 */
	private function _generate_pdf($statement)
	{
		$member = $this->member_model->get_by_id($statement['member_id']);
		$ledger = $this->ledger_model->get_by_date_range($member['id'], $statement['period_start'], $statement['period_end']);

		$html = $this->load->view('statements/print_template', array(
			'statement' => $statement,
			'member' => $member,
			'ledger' => $ledger
		), true);

		$this->load->helper('download');
		force_download('statement_' . $statement['statement_number'] . '.html', $html);
	}

	private function _valid_date_range($start_date, $end_date)
	{
		$start = DateTime::createFromFormat('Y-m-d', $start_date);
		$end = DateTime::createFromFormat('Y-m-d', $end_date);

		return $start && $end && $start->format('Y-m-d') === $start_date && $end->format('Y-m-d') === $end_date && $start <= $end;
	}

	private function _can_access_member($member_id)
	{
		if (in_array((int) $this->session->userdata('role_id'), array(1, 2, 3))) {
			return true;
		}

		$this->db->where('id', $member_id);
		$this->db->where('user_id', $this->session->userdata('user_id'));

		return $this->db->count_all_results('members') > 0;
	}

	private function _render($view, $data = array())
	{
		$this->load->view('layouts/header', array(
			'show_sidebar' => true,
			'page_title' => $data['page_title'] ?? 'Statements',
			'current_page' => 'statements',
			'is_admin' => in_array((int) $this->session->userdata('role_id'), array(1, 2))
		));
		$this->load->view($view, $data);
		$this->load->view('layouts/footer');
	}
}

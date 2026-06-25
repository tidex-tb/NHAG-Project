<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Dashboard Controller - Role-based dashboards
 */
class Dashboard extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		if (!$this->session->userdata('logged_in')) {
			redirect('auth/login');
		}

		$this->load->model('member_model');
		$this->load->model('contribution_model');
		$this->load->model('ledger_model');
		$this->load->model('notification_model');
		$this->load->model('data_entry_model');
		$this->load->model('member_request_model');
	}

	/**
	 * Main dashboard - routes to role-specific dashboard
	 */
	public function index()
	{
		$role_id = $this->session->userdata('role_id');

		switch ($role_id) {
			case 1: // Super Admin
			case 2: // Admin
				$this->admin_dashboard();
				break;
			case 3: // Finance Officer
				$this->finance_dashboard();
				break;
			case 4: // Member
				$this->member_dashboard();
				break;
			default:
				$this->session->sess_destroy();
				redirect('auth/login');
		}
	}

	/**
	 * Admin Dashboard
	 */
	private function admin_dashboard()
	{
		$data = array(
			'page_title' => 'Admin Dashboard',
			'page_heading' => 'Administration Dashboard',
			'breadcrumbs' => array(
				'Dashboard' => 'dashboard'
			)
		);

		// Get statistics
		$data['member_stats'] = $this->member_model->get_member_statistics();
		$data['contribution_stats'] = $this->contribution_model->get_contribution_statistics();
		$data['total_members'] = $data['member_stats']['total_members'];
		$data['active_members'] = $data['member_stats']['active_members'];
		$data['data_entry_totals'] = $this->data_entry_model->get_totals();

		// Get recent data
		$data['recent_members'] = $this->member_model->get_members_for_dashboard(5);
		$data['recent_contributions'] = $this->contribution_model->get_recent_contributions(10);

		// Get total savings
		$ledger_stats = $this->ledger_model->get_total_savings();
		$data['total_savings'] = $ledger_stats['total_savings'] ?? 0;

		// Get pending contributions count
		$data['pending_count'] = $this->contribution_model->get_count(array('status' => 'Pending'));
		$data['pending_contributions'] = $data['pending_count'];

		// $this->load->view('layouts/header', $data);
		$this->load->view('dashboards/admin_dashboard', $data);
		// $this->load->view('layouts/footer');
	}

	/**
	 * Finance Officer Dashboard
	 */
	private function finance_dashboard()
	{
		$data = array(
			'page_title' => 'Finance Dashboard',
			'page_heading' => 'Finance Officer Dashboard',
			'breadcrumbs' => array(
				'Dashboard' => 'dashboard'
			)
		);

		// Get contribution statistics
		$data['contribution_stats'] = $this->contribution_model->get_contribution_statistics();
		$data['contribution_stats']['pending'] = (int) ($data['contribution_stats']['Pending']['count'] ?? 0);
		$data['contribution_stats']['rejected'] = (int) ($data['contribution_stats']['Rejected']['count'] ?? 0);
		$data['contribution_stats']['approved'] = (int) ($data['contribution_stats']['Approved']['count'] ?? 0);

		$this->db->where('status', 'Approved');
		$this->db->where('approval_date >=', date('Y-m-01 00:00:00'));
		$this->db->where('approval_date <=', date('Y-m-t 23:59:59'));
		$this->db->where('deleted_at IS NULL', null, false);
		$data['contribution_stats']['approved_month'] = $this->db->count_all_results('contributions');
		$data['data_entry_totals'] = $this->data_entry_model->get_totals();

		// Get pending contributions
		$data['pending_contributions'] = $this->contribution_model->get_pending_contributions(10);

		// Get recent approvals
		$this->db->select('c.*, m.first_name, m.last_name');
		$this->db->from('contributions c');
		$this->db->join('members m', 'c.member_id = m.id');
		$this->db->where('c.status', 'Approved');
		$this->db->order_by('c.approval_date', 'DESC');
		$this->db->limit(10);
		$data['recent_approvals'] = $this->db->get()->result_array();

		// Get total savings
		$ledger_stats = $this->ledger_model->get_total_savings();
		$data['total_savings'] = $ledger_stats['total_savings'] ?? 0;


		$this->load->view('dashboards/finance_dashboard', $data);
	}

	/**
	 * Member Dashboard
	 */
	private function member_dashboard()
	{
		$user_id = $this->session->userdata('user_id');

		$data = array(
			'page_title' => 'My Dashboard',
			'page_heading' => 'Dashboard',
			'breadcrumbs' => array(
				'Dashboard' => 'dashboard'
			)
		);

		// Get member info
		$this->db->where('user_id', $user_id);
		$member = $this->db->get('members')->row_array();

		if (!$member) {
			$data['member'] = null;
			$data['total_savings'] = 0;
			$data['pending_count'] = 0;
			$data['monthly_approved'] = 0;
			$data['recent_contributions'] = array();
			$data['chart_labels'] = array();
			$data['chart_data'] = array();
			for ($i = 5; $i >= 0; $i--) {
				$data['chart_labels'][] = date('M Y', strtotime("-{$i} months"));
				$data['chart_data'][] = 0;
			}
			$data['recent_transactions'] = array();
			$data['unread_notifications'] = $this->notification_model->get_user_notifications($user_id, true);
			$data['profile_missing'] = true;
			$data['pending_member_request'] = $this->member_request_model->get_pending_for_user($user_id);
			$this->load->view('dashboards/member_dashboard', $data);
			return;
		}

		$data['member'] = $member;

		// Get member statistics
		$data['total_contributions'] = $this->contribution_model->get_count(array('member_id' => $member['id']));
		$data['approved_contributions'] = $this->contribution_model->get_count(array('member_id' => $member['id'], 'status' => 'Approved'));
		$data['current_balance'] = $member['current_balance'];
		$data['total_savings'] = $member['current_balance'];
		$data['pending_count'] = $this->contribution_model->get_count(array('member_id' => $member['id'], 'status' => 'Pending'));
		$data['recent_contributions'] = $this->contribution_model->get_member_contributions($member['id']);
		$data['recent_contributions'] = array_slice($data['recent_contributions'], 0, 10);

		$this->db->select_sum('amount');
		$this->db->where('member_id', $member['id']);
		$this->db->where('status', 'Approved');
		$this->db->where('approval_date >=', date('Y-m-01 00:00:00'));
		$this->db->where('approval_date <=', date('Y-m-t 23:59:59'));
		$this->db->where('deleted_at IS NULL', null, false);
		$monthly = $this->db->get('contributions')->row_array();
		$data['monthly_approved'] = (float) ($monthly['amount'] ?? 0);

		$data['chart_labels'] = array();
		$data['chart_data'] = array();
		for ($i = 5; $i >= 0; $i--) {
			$month_start = date('Y-m-01', strtotime("-{$i} months"));
			$month_end = date('Y-m-t', strtotime("-{$i} months"));
			$data['chart_labels'][] = date('M Y', strtotime($month_start));

			$this->db->select_sum('credit_amount');
			$this->db->where('member_id', $member['id']);
			$this->db->where('transaction_date >=', $month_start);
			$this->db->where('transaction_date <=', $month_end);
			$monthly_credit = $this->db->get('savings_ledger')->row_array();
			$data['chart_data'][] = (float) ($monthly_credit['credit_amount'] ?? 0);
		}

		// Get recent transactions
		$data['recent_transactions'] = $this->ledger_model->get_member_ledger($member['id'], 5);

		// Get unread notifications
		$data['unread_notifications'] = $this->notification_model->get_user_notifications($user_id, true);


		$this->load->view('dashboards/member_dashboard', $data);
	}

	/**
	 * Get notifications (AJAX)
	 */
	public function get_notifications()
	{
		$this->output->set_content_type('application/json');

		$user_id = $this->session->userdata('user_id');
		$notifications = $this->notification_model->get_user_notifications($user_id, true);

		echo json_encode(array(
			'success' => true,
			'count' => count($notifications),
			'notifications' => $notifications
		));
	}

	public function notifications()
	{
		$user_id = $this->session->userdata('user_id');
		$data = array(
			'page_title' => 'Notifications',
			'notifications' => $this->notification_model->get_user_notifications($user_id)
		);

		$this->load->view('layouts/header', array(
			'show_sidebar' => true,
			'page_title' => 'Notifications',
			'current_page' => 'notifications',
			'is_admin' => in_array((int) $this->session->userdata('role_id'), array(1, 2))
		));
		$this->load->view('dashboards/notifications', $data);
		$this->load->view('layouts/footer');
	}

	/**
	 * Mark notification as read (AJAX)
	 */
	public function mark_notification_read()
	{
		$this->output->set_content_type('application/json');

		$notification_id = $this->input->post('notification_id');
		$this->db->where('id', $notification_id);
		$this->db->where('user_id', $this->session->userdata('user_id'));
		$notification = $this->db->get('notifications')->row_array();

		$result = $notification ? $this->notification_model->mark_as_read($notification_id) : false;

		echo json_encode(array(
			'success' => $result,
			'message' => $result ? 'Notification marked as read' : 'Error marking notification'
		));
	}
}





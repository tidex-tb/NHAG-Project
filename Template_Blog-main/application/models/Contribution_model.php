<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'models/Base_Model.php');

class Contribution_model extends Base_Model
{

	protected $table = 'contributions';

	/**
	 * Submit contribution
	 */
	public function submit_contribution($data)
	{
		$data['reference_number'] = $this->_generate_reference_number();
		$data['status'] = 'Pending';
		$data['transaction_date'] = date('Y-m-d');

		return $this->insert_record($data);
	}

	/**
	 * Get member contributions
	 */
	public function get_member_contributions($member_id, $status = null)
	{
		$this->db->select('*, status as display_status');
		$this->db->where('member_id', $member_id);
		$this->db->where('deleted_at IS NULL', null, false);

		if ($status) {
			$this->db->where('status', $status);
		}

		$this->db->order_by('transaction_date', 'DESC');
		return $this->db->get($this->table)->result_array();
	}

	/**
	 * Get pending contributions
	 */
	public function get_pending_contributions($limit = null, $offset = 0)
	{
		$this->db->select('c.*, m.first_name, m.last_name, m.member_number, CONCAT(m.first_name, " ", m.last_name) as member_name');
		$this->db->from($this->table . ' c');
		$this->db->join('members m', 'c.member_id = m.id', 'left');
		$this->db->where('c.status', 'Pending');
		$this->db->where('c.deleted_at IS NULL', null, false);
		$this->db->order_by('c.transaction_date', 'DESC');

		if ($limit) {
			$this->db->limit($limit, $offset);
		}

		return $this->db->get()->result_array();
	}

	/**
	 * Approve contribution
	 */
	public function approve_contribution($contribution_id, $notes = '')
	{
		$contribution = $this->get_by_id($contribution_id);

		if (!$contribution || $contribution['status'] != 'Pending') {
			return false;
		}

		$data = array(
			'status' => 'Approved',
			'approved_by' => $this->session->userdata('user_id'),
			'approval_date' => date('Y-m-d H:i:s'),
			'received_date' => date('Y-m-d'),
			'notes' => $notes
		);

		$result = $this->update_record($contribution_id, $data);

		if ($result) {
			// Update member balance
			$this->load->model('member_model');
			$this->member_model->update_balance($contribution['member_id'], $contribution['amount'], 'add');

			// Create ledger entry
			$this->_create_ledger_entry($contribution_id, $contribution, 'Contribution');

			// Log status change
			$this->_log_contribution_status($contribution_id, 'Pending', 'Approved');

			// Send notification
			$this->_send_notification(
				$contribution['member_id'],
				'Contribution Approved',
				'Your contribution of N$' . $contribution['amount'] . ' has been approved.'
			);
		}

		return $result;
	}

	/**
	 * Reject contribution
	 */
	public function reject_contribution($contribution_id, $rejection_reason)
	{
		$contribution = $this->get_by_id($contribution_id);

		if (!$contribution || $contribution['status'] != 'Pending') {
			return false;
		}

		$data = array(
			'status' => 'Rejected',
			'rejection_reason' => $rejection_reason,
			'updated_by' => $this->session->userdata('user_id')
		);

		$result = $this->update_record($contribution_id, $data);

		if ($result) {
			$this->_log_contribution_status($contribution_id, 'Pending', 'Rejected');

			// Send notification
			$this->_send_notification(
				$contribution['member_id'],
				'Contribution Rejected',
				'Your contribution has been rejected. Reason: ' . $rejection_reason
			);
		}

		return $result;
	}

	/**
	 * Get contribution statistics
	 */
	public function get_contribution_statistics()
	{
		$stats = array();

		$this->db->select('status, COUNT(id) as count, SUM(amount) as total');
		$this->db->where('deleted_at IS NULL', null, false);
		$this->db->group_by('status');
		$result = $this->db->get($this->table)->result_array();

		foreach ($result as $row) {
			$stats[$row['status']] = array(
				'count' => $row['count'],
				'total' => $row['total']
			);
		}

		return $stats;
	}

	/**
	 * Get contributions for dashboard
	 */
	public function get_recent_contributions($limit = 10)
	{
		$this->db->select('c.*, m.first_name, m.last_name, m.member_number, CONCAT(m.first_name, " ", m.last_name) as member_name');
		$this->db->from('contributions c');
		$this->db->join('members m', 'c.member_id = m.id');
		$this->db->where('c.deleted_at IS NULL', null, false);
		$this->db->order_by('c.transaction_date', 'DESC');
		$this->db->limit($limit);

		return $this->db->get()->result_array();
	}

	/**
	 * Create ledger entry for contribution
	 */
	private function _create_ledger_entry($contribution_id, $contribution, $type)
	{
		$this->load->model('ledger_model');

		// Get current member balance
		$this->load->model('member_model');
		$member = $this->member_model->get_by_id($contribution['member_id']);
		$new_balance = $member['current_balance'];

		$ledger_data = array(
			'member_id' => $contribution['member_id'],
			'transaction_type' => $type,
			'reference_number' => $contribution['reference_number'],
			'debit_amount' => 0,
			'credit_amount' => $contribution['amount'],
			'balance_after' => $new_balance,
			'description' => 'Contribution: ' . $contribution['reference_number'],
			'transaction_date' => date('Y-m-d')
		);

		$this->ledger_model->insert_record($ledger_data);
	}

	/**
	 * Log contribution status change
	 */
	private function _log_contribution_status($contribution_id, $old_status, $new_status)
	{
		$data = array(
			'contribution_id' => $contribution_id,
			'old_status' => $old_status,
			'new_status' => $new_status,
			'changed_by' => $this->session->userdata('user_id'),
			'changed_at' => date('Y-m-d H:i:s')
		);

		$this->db->insert('contribution_status_history', $data);
	}

	/**
	 * Send notification
	 */
	private function _send_notification($member_id, $title, $message)
	{
		$this->load->model('notification_model');

		// Get member's user_id
		$this->load->model('member_model');
		$member = $this->member_model->get_by_id($member_id);

		if ($member && $member['user_id']) {
			$notification_data = array(
				'user_id' => $member['user_id'],
				'title' => $title,
				'message' => $message,
				'notification_type' => 'Contribution',
				'related_entity_type' => 'contribution',
				'related_entity_id' => $member_id
			);

			$this->notification_model->insert_record($notification_data);
		}
	}

	/**
	 * Generate unique reference number
	 */
	private function _generate_reference_number()
	{
		$this->load->model('system_setting_model');
		$prefix = $this->system_setting_model->get_setting('contribution_ref_prefix', 'CONT');

		// Get next sequence number
		$this->db->select_max('id');
		$query = $this->db->get($this->table);
		$row = $query->row_array();
		$next_num = ($row['id'] ?? 0) + 1;

		return $prefix . date('Y') . str_pad($next_num, 6, '0', STR_PAD_LEFT);
	}

	/**
	 * Upload contribution document
	 */
	public function upload_document($contribution_id, $file_info)
	{
		$data = array(
			'contribution_id' => $contribution_id,
			'document_type' => 'Proof of Payment',
			'file_name' => $file_info['file_name'],
			'file_path' => $file_info['file_path'],
			'file_size' => $file_info['file_size'],
			'file_type' => $file_info['file_type'],
			'uploaded_by' => $this->session->userdata('user_id')
		);

		return $this->db->insert('contribution_documents', $data);
	}
}

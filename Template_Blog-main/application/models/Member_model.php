<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'models/Base_Model.php');

class Member_model extends Base_Model
{

	protected $table = 'members';

	/**
	 * Create new member
	 */
	public function create_member($member_data, $profile_data = null)
	{
		// Generate unique member number
		$member_data['member_number'] = $this->_generate_member_number();
		$member_data['registration_date'] = date('Y-m-d');
		$member_data['status'] = 'Active';

		// Insert member
		$member_id = $this->insert_record($member_data);

		if ($member_id && $profile_data) {
			$profile_data['member_id'] = $member_id;
			$this->load->model('member_profile_model');
			$this->member_profile_model->insert_record($profile_data);
		}

		return $member_id;
	}

	/**
	 * Get member with profile
	 */
	public function get_member_with_profile($member_id)
	{
		$member = $this->get_by_id($member_id);

		if ($member) {
			$this->load->model('member_profile_model');
			$member['profile'] = $this->member_profile_model->get_one_by(array('member_id' => $member_id));
		}

		return $member;
	}

	/**
	 * Get active members
	 */
	public function get_active_members($limit = null, $offset = 0)
	{
		$this->db->select('*, CONCAT(first_name, " ", last_name) as name, registration_date as created_at');
		$this->db->where('status', 'Active');
		$this->db->where('deleted_at IS NULL', null, false);
		$this->db->order_by('registration_date', 'DESC');

		if ($limit) {
			$this->db->limit($limit, $offset);
		}

		return $this->db->get($this->table)->result_array();
	}

	/**
	 * Get member statistics
	 */
	public function get_member_statistics()
	{
		return array(
			'total_members' => $this->get_count(),
			'active_members' => $this->get_count(array('status' => 'Active')),
			'suspended_members' => $this->get_count(array('status' => 'Suspended')),
			'inactive_members' => $this->get_count(array('status' => 'Inactive'))
		);
	}

	/**
	 * Get members for dashboard (with recent activity)
	 */
	public function get_members_for_dashboard($limit = 10)
	{
		$this->db->select('m.*, CONCAT(m.first_name, " ", m.last_name) as name, m.registration_date as created_at, COUNT(c.id) as total_contributions, SUM(c.amount) as total_savings');
		$this->db->from('members m');
		$this->db->join('contributions c', 'm.id = c.member_id AND c.status = "Approved"', 'left');
		$this->db->where('m.deleted_at IS NULL', null, false);
		$this->db->group_by('m.id');
		$this->db->order_by('m.registration_date', 'DESC');
		$this->db->limit($limit);

		return $this->db->get()->result_array();
	}

	/**
	 * Search members
	 */
	public function search_members($search_term)
	{
		$this->db->like('first_name', $search_term);
		$this->db->or_like('last_name', $search_term);
		$this->db->or_like('member_number', $search_term);
		$this->db->or_like('national_id', $search_term);
		$this->db->or_like('email', $search_term);
		$this->db->or_like('phone_number', $search_term);
		$this->db->where('deleted_at IS NULL', null, false);

		return $this->db->get($this->table)->result_array();
	}

	/**
	 * Suspend member
	 */
	public function suspend_member($member_id, $reason = '')
	{
		$old_status = $this->get_by_id($member_id)['status'];

		$result = $this->update_record($member_id, array('status' => 'Suspended'));

		if ($result) {
			// Log status change
			$this->_log_status_change($member_id, $old_status, 'Suspended', $reason);
		}

		return $result;
	}

	/**
	 * Activate member
	 */
	public function activate_member($member_id, $reason = '')
	{
		$old_status = $this->get_by_id($member_id)['status'];

		$result = $this->update_record($member_id, array('status' => 'Active'));

		if ($result) {
			$this->_log_status_change($member_id, $old_status, 'Active', $reason);
		}

		return $result;
	}

	/**
	 * Log member status change
	 */
	private function _log_status_change($member_id, $old_status, $new_status, $reason = '')
	{
		$data = array(
			'member_id' => $member_id,
			'old_status' => $old_status,
			'new_status' => $new_status,
			'reason' => $reason,
			'changed_by' => $this->session->userdata('user_id'),
			'changed_at' => date('Y-m-d H:i:s')
		);

		$this->db->insert('member_status_history', $data);
	}

	/**
	 * Generate unique member number
	 */
	private function _generate_member_number()
	{
		$this->load->model('system_setting_model');
		$prefix = $this->system_setting_model->get_setting('member_number_prefix', 'MEM');

		// Get next sequence number
		$this->db->select_max('id');
		$query = $this->db->get($this->table);
		$row = $query->row_array();
		$next_num = ($row['id'] ?? 0) + 1;

		return $prefix . date('Y') . str_pad($next_num, 5, '0', STR_PAD_LEFT);
	}

	/**
	 * Update member balance
	 */
	public function update_balance($member_id, $amount, $operation = 'add', $set_balance = null)
	{
		$member = $this->get_by_id($member_id);

		if (!$member) {
			return false;
		}

		$new_balance = $member['current_balance'];
		$total_savings = $member['total_savings'];

		if ($operation == 'set') {
			$new_balance = $set_balance;
		} elseif ($operation == 'add') {
			$new_balance += $amount;
			$total_savings += $amount;
		} elseif ($operation == 'subtract') {
			$new_balance -= $amount;
		}

		return $this->update_record($member_id, array(
			'current_balance' => $new_balance,
			'total_savings' => $total_savings
		));
	}

	/**
	 * Get member contribution history
	 */
	public function get_member_contributions($member_id)
	{
		$this->load->model('contribution_model');
		return $this->contribution_model->get_member_contributions($member_id);
	}

	/**
	 * Get member ledger
	 */
	public function get_member_ledger($member_id)
	{
		$this->load->model('ledger_model');
		return $this->ledger_model->get_member_ledger($member_id);
	}
}

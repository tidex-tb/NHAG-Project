<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Audit_model extends Base_Model
{

	protected $table = 'audit_logs';

	/**
	 * Get audit logs
	 */
	public function get_audit_logs($limit = 100, $offset = 0)
	{
		$this->db->order_by('logged_at', 'DESC');
		$this->db->limit($limit, $offset);

		return $this->db->get($this->table)->result_array();
	}

	/**
	 * Get audit logs for entity
	 */
	public function get_entity_logs($entity_type, $entity_id)
	{
		$this->db->where('entity_type', $entity_type);
		$this->db->where('entity_id', $entity_id);
		$this->db->order_by('logged_at', 'DESC');

		return $this->db->get($this->table)->result_array();
	}

	/**
	 * Get audit logs by user
	 */
	public function get_user_logs($user_id)
	{
		$this->db->where('user_id', $user_id);
		$this->db->order_by('logged_at', 'DESC');

		return $this->db->get($this->table)->result_array();
	}

	/**
	 * Get logs by action
	 */
	public function get_by_action($action)
	{
		$this->db->where('action', $action);
		$this->db->order_by('logged_at', 'DESC');

		return $this->db->get($this->table)->result_array();
	}

	/**
	 * Get logs by date range
	 */
	public function get_by_date_range($start_date, $end_date)
	{
		$this->db->where('DATE(logged_at) >=', $start_date);
		$this->db->where('DATE(logged_at) <=', $end_date);
		$this->db->order_by('logged_at', 'DESC');

		return $this->db->get($this->table)->result_array();
	}

	/**
	 * Log login activity
	 */
	public function log_login($user_id)
	{
		$data = array(
			'user_id' => $this->_valid_user_id($user_id),
			'action' => 'LOGIN',
			'entity_type' => 'User',
			'description' => 'User login',
			'ip_address' => $this->input->ip_address(),
			'user_agent' => $this->input->user_agent(),
			'logged_at' => date('Y-m-d H:i:s')
		);

		return $this->db->insert($this->table, $data);
	}

	/**
	 * Log logout activity
	 */
	public function log_logout($user_id)
	{
		$data = array(
			'user_id' => $this->_valid_user_id($user_id),
			'action' => 'LOGOUT',
			'entity_type' => 'User',
			'description' => 'User logout',
			'ip_address' => $this->input->ip_address(),
			'user_agent' => $this->input->user_agent(),
			'logged_at' => date('Y-m-d H:i:s')
		);

		return $this->db->insert($this->table, $data);
	}

	private function _valid_user_id($user_id)
	{
		if (!$user_id) {
			return null;
		}

		return $this->db->where('id', $user_id)->count_all_results('users') > 0 ? $user_id : null;
	}

	/**
	 * Cleanup old logs
	 */
	public function cleanup_old_logs($days = 365)
	{
		$cutoff_date = date('Y-m-d H:i:s', strtotime("-$days days"));

		$this->db->where('logged_at <', $cutoff_date);
		return $this->db->delete($this->table);
	}
}


<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'models/Base_Model.php');

class Ledger_model extends Base_Model
{

	protected $table = 'savings_ledger';

	/**
	 * Get member ledger
	 */
	public function get_member_ledger($member_id, $limit = null, $offset = 0)
	{
		$this->db->where('member_id', $member_id);
		$this->db->order_by('transaction_date', 'DESC');

		if ($limit) {
			$this->db->limit($limit, $offset);
		}

		return $this->db->get($this->table)->result_array();
	}

	/**
	 * Get member balance
	 */
	public function get_member_balance($member_id)
	{
		$this->db->select_max('balance_after');
		$this->db->where('member_id', $member_id);
		$query = $this->db->get($this->table);

		if ($query->num_rows() > 0) {
			$row = $query->row_array();
			return $row['balance_after'] ?? 0;
		}

		return 0;
	}

	/**
	 * Get ledger entries by date range
	 */
	public function get_by_date_range($member_id, $start_date, $end_date)
	{
		$this->db->where('member_id', $member_id);
		$this->db->where('transaction_date >=', $start_date);
		$this->db->where('transaction_date <=', $end_date);
		$this->db->order_by('transaction_date', 'DESC');

		return $this->db->get($this->table)->result_array();
	}

	/**
	 * Get ledger entries by type
	 */
	public function get_by_type($member_id, $type)
	{
		$this->db->where('member_id', $member_id);
		$this->db->where('transaction_type', $type);
		$this->db->order_by('transaction_date', 'DESC');

		return $this->db->get($this->table)->result_array();
	}

	/**
	 * Get summary for date range
	 */
	public function get_summary($member_id, $start_date, $end_date)
	{
		$this->db->select('
            SUM(credit_amount) as total_credits,
            SUM(debit_amount) as total_debits,
            COUNT(id) as transaction_count
        ');
		$this->db->where('member_id', $member_id);
		$this->db->where('transaction_date >=', $start_date);
		$this->db->where('transaction_date <=', $end_date);

		return $this->db->get($this->table)->row_array();
	}

	/**
	 * Record adjustment
	 */
	public function record_adjustment($member_id, $amount, $description, $operation = 'credit')
	{
		$this->load->model('member_model');
		$member = $this->member_model->get_by_id($member_id);

		$new_balance = $member['current_balance'];

		$data = array(
			'member_id' => $member_id,
			'transaction_type' => 'Adjustment',
			'description' => $description,
			'transaction_date' => date('Y-m-d')
		);

		if ($operation == 'credit') {
			$data['credit_amount'] = $amount;
			$data['debit_amount'] = 0;
			$new_balance += $amount;
		} else {
			$data['debit_amount'] = $amount;
			$data['credit_amount'] = 0;
			$new_balance -= $amount;
		}

		$data['balance_after'] = $new_balance;

		$result = $this->insert_record($data);

		if ($result) {
			$this->member_model->update_balance($member_id, 0, 'set', $new_balance);
		}

		return $result;
	}

	/**
	 * Get total savings for period
	 */
	public function get_total_savings($start_date = null, $end_date = null)
	{
		$this->db->select('SUM(credit_amount) as total_savings');

		if ($start_date && $end_date) {
			$this->db->where('transaction_date >=', $start_date);
			$this->db->where('transaction_date <=', $end_date);
		}

		return $this->db->get($this->table)->row_array();
	}
}

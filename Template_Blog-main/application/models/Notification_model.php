<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'models/Base_Model.php');

class Notification_model extends Base_Model
{

	protected $table = 'notifications';

	/**
	 * Get user notifications
	 */
	public function get_user_notifications($user_id, $unread_only = false)
	{
		$this->db->where('user_id', $user_id);

		if ($unread_only) {
			$this->db->where('is_read', 0);
		}

		$this->db->order_by('created_at', 'DESC');
		return $this->db->get($this->table)->result_array();
	}

	/**
	 * Get unread count
	 */
	public function get_unread_count($user_id)
	{
		return $this->get_count(array('user_id' => $user_id, 'is_read' => 0));
	}

	/**
	 * Mark as read
	 */
	public function mark_as_read($notification_id)
	{
		$data = array(
			'is_read' => 1,
			'read_at' => date('Y-m-d H:i:s')
		);

		$this->db->where('id', $notification_id);
		return $this->db->update($this->table, $data);
	}

	/**
	 * Mark all as read for user
	 */
	public function mark_all_as_read($user_id)
	{
		$data = array(
			'is_read' => 1,
			'read_at' => date('Y-m-d H:i:s')
		);

		$this->db->where('user_id', $user_id);
		return $this->db->update($this->table, $data);
	}

	/**
	 * Send notification to member via user
	 */
	public function notify_member($member_id, $title, $message, $type = 'System')
	{
		$this->load->model('member_model');
		$member = $this->member_model->get_by_id($member_id);

		if (!$member || !$member['user_id']) {
			return false;
		}

		$data = array(
			'user_id' => $member['user_id'],
			'title' => $title,
			'message' => $message,
			'notification_type' => $type,
			'related_entity_type' => 'member',
			'related_entity_id' => $member_id,
			'channel' => 'In-System'
		);

		return $this->insert_record($data);
	}

	/**
	 * Send email notification (optional)
	 */
	public function send_email($user_id, $subject, $message)
	{
		$this->load->model('user_model');
		$user = $this->user_model->get_by_id($user_id);

		if ($user && $user['email']) {
			$this->load->library('email');

			$this->email->from('notifications@nhag.org.na', 'NHAG Savings Platform');
			$this->email->to($user['email']);
			$this->email->subject($subject);
			$this->email->message($message);

			return $this->email->send();
		}

		return false;
	}

	/**
	 * Delete old notifications (cleanup)
	 */
	public function cleanup_old_notifications($days = 90)
	{
		$cutoff_date = date('Y-m-d H:i:s', strtotime("-$days days"));

		$this->db->where('created_at <', $cutoff_date);
		$this->db->where('is_read', 1);

		return $this->db->delete($this->table);
	}
}

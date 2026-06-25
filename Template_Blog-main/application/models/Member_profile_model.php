<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'models/Base_Model.php');

class Member_profile_model extends Base_Model
{

	protected $table = 'member_profiles';

	/**
	 * Get member profile
	 */
	public function get_member_profile($member_id)
	{
		return $this->get_one_by(array('member_id' => $member_id));
	}

	/**
	 * Create or update profile
	 */
	public function save_profile($member_id, $data)
	{
		$data['member_id'] = $member_id;

		$existing = $this->get_member_profile($member_id);

		if ($existing) {
			$this->db->where('member_id', $member_id);
			return $this->db->update($this->table, $data);
		} else {
			return $this->insert_record($data);
		}
	}
}

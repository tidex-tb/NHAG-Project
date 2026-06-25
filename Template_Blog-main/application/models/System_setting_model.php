<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'models/Base_Model.php');

class System_setting_model extends Base_Model
{

	protected $table = 'system_settings';

	/**
	 * Get setting by key
	 */
	public function get_setting($key, $default = null)
	{
		$setting = $this->get_one_by(array('setting_key' => $key));

		if ($setting) {
			return $this->_parse_value($setting['setting_value'], $setting['data_type']);
		}

		return $default;
	}

	/**
	 * Update setting
	 */
	public function update_setting($key, $value, $data_type = 'string')
	{
		$existing = $this->get_one_by(array('setting_key' => $key));

		$data = array(
			'setting_value' => $value,
			'data_type' => $data_type,
			'updated_at' => date('Y-m-d H:i:s')
		);

		if ($existing) {
			$this->db->where('setting_key', $key);
			return $this->db->update($this->table, $data);
		} else {
			$data['setting_key'] = $key;
			return $this->db->insert($this->table, $data);
		}
	}

	/**
	 * Get all settings
	 */
	public function get_all_settings()
	{
		$settings = $this->get_all();

		$result = array();
		foreach ($settings as $setting) {
			$result[$setting['setting_key']] = $this->_parse_value(
				$setting['setting_value'],
				$setting['data_type']
			);
		}

		return $result;
	}

	/**
	 * Parse value based on data type
	 */
	private function _parse_value($value, $data_type)
	{
		switch ($data_type) {
			case 'integer':
				return (int)$value;
			case 'boolean':
				return (bool)$value || $value === '1';
			case 'json':
				return json_decode($value, true);
			case 'string':
			default:
				return $value;
		}
	}
}

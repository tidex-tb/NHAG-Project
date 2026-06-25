<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'models/Base_Model.php');

class Permission_model extends Base_Model
{

	protected $table = 'permissions';

	/**
	 * Get permissions by module
	 */
	public function get_by_module($module)
	{
		$this->db->where('module', $module);
		return $this->db->get($this->table)->result_array();
	}

	/**
	 * Get permissions by action
	 */
	public function get_by_action($action)
	{
		$this->db->where('action', $action);
		return $this->db->get($this->table)->result_array();
	}

	/**
	 * Get all permissions grouped by module
	 */
	public function get_grouped_by_module()
	{
		$this->db->order_by('module, name');
		$permissions = $this->db->get($this->table)->result_array();

		$grouped = array();
		foreach ($permissions as $perm) {
			$module = $perm['module'];
			if (!isset($grouped[$module])) {
				$grouped[$module] = array();
			}
			$grouped[$module][] = $perm;
		}

		return $grouped;
	}
}

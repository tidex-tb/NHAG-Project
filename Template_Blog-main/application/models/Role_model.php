<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'models/Base_Model.php');

class Role_model extends Base_Model
{

	protected $table = 'roles';

	/**
	 * Get all roles with their permissions
	 */
	public function get_roles_with_permissions()
	{
		$roles = $this->get_all('id');

		foreach ($roles as &$role) {
			$this->db->select('p.*');
			$this->db->from('permissions p');
			$this->db->join('role_permissions rp', 'p.id = rp.permission_id');
			$this->db->where('rp.role_id', $role['id']);
			$this->db->order_by('p.module, p.name');
			$role['permissions'] = $this->db->get()->result_array();
		}

		return $roles;
	}

	/**
	 * Get role with permissions by ID
	 */
	public function get_role_with_permissions($role_id)
	{
		$role = $this->get_by_id($role_id);

		if ($role) {
			$this->db->select('p.*');
			$this->db->from('permissions p');
			$this->db->join('role_permissions rp', 'p.id = rp.permission_id');
			$this->db->where('rp.role_id', $role_id);
			$role['permissions'] = $this->db->get()->result_array();
		}

		return $role;
	}

	/**
	 * Assign permission to role
	 */
	public function assign_permission($role_id, $permission_id)
	{
		$data = array(
			'role_id' => $role_id,
			'permission_id' => $permission_id
		);
		return $this->db->insert('role_permissions', $data);
	}

	/**
	 * Remove permission from role
	 */
	public function remove_permission($role_id, $permission_id)
	{
		$this->db->where(array('role_id' => $role_id, 'permission_id' => $permission_id));
		return $this->db->delete('role_permissions');
	}
}

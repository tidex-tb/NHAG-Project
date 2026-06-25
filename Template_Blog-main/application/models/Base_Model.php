<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Base_Model - Extended CI_Model with common functionality
 * Provides common database operations and audit logging
 */
class Base_Model extends CI_Model
{

    protected $table = '';
    protected $primary_key = 'id';

    /**
     * Get all records
     */
    public function get_all($order_by = 'id', $limit = null, $offset = 0)
    {
        $this->db->order_by($order_by);

        if ($limit) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get($this->table)->result_array();
    }

    /**
     * Get single record by ID
     */
    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, array($this->primary_key => $id))->row_array();
    }

    /**
     * Get records by condition
     */
    public function get_by($condition = array(), $order_by = 'id')
    {
        $this->db->where($condition);
        $this->db->order_by($order_by);
        return $this->db->get($this->table)->result_array();
    }

    /**
     * Get single record by condition
     */
    public function get_one_by($condition = array())
    {
        return $this->db->get_where($this->table, $condition)->row_array();
    }

    /**
     * Insert new record with audit trail
     */
    public function insert_record($data)
    {
        if ($this->db->field_exists('created_by', $this->table)) {
            $data['created_by'] = $this->_current_user_id();
        }
        $data['created_at'] = date('Y-m-d H:i:s');

        $result = $this->db->insert($this->table, $data);

        if ($result) {
            $insert_id = $this->db->insert_id();
            $this->_log_audit('INSERT', $this->table, $insert_id, null, $data);
            return $insert_id;
        }

        return false;
    }

    /**
     * Update record with audit trail
     */
    public function update_record($id, $data)
    {
        $old_data = $this->get_by_id($id);

        if ($this->db->field_exists('updated_by', $this->table)) {
            $data['updated_by'] = $this->_current_user_id();
        }
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->where($this->primary_key, $id);
        $result = $this->db->update($this->table, $data);

        if ($result) {
            $this->_log_audit('UPDATE', $this->table, $id, $old_data, $data);
            return true;
        }

        return false;
    }

    /**
     * Delete record with audit trail (soft delete)
     */
    public function delete_record($id)
    {
        $old_data = $this->get_by_id($id);

        $data = array(
            'deleted_at' => date('Y-m-d H:i:s'),
            'updated_by' => $this->_current_user_id()
        );

        $this->db->where($this->primary_key, $id);
        $result = $this->db->update($this->table, $data);

        if ($result) {
            $this->_log_audit('DELETE', $this->table, $id, $old_data, $data);
            return true;
        }

        return false;
    }

    /**
     * Get total count
     */
    public function get_count($condition = array())
    {
        if (!empty($condition)) {
            $this->db->where($condition);
        }
        return $this->db->count_all_results($this->table);
    }

    /**
     * Log audit trail
     */
    protected function _log_audit($action, $entity_type, $entity_id, $old_values, $new_values)
    {
        $this->load->model('audit_model');

        $audit_data = array(
            'user_id' => $this->_current_user_id(),
            'action' => $action,
            'entity_type' => $entity_type,
            'entity_id' => $entity_id,
            'old_values' => json_encode($old_values),
            'new_values' => json_encode($new_values),
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),
            'logged_at' => date('Y-m-d H:i:s')
        );

        $this->db->insert('audit_logs', $audit_data);
    }

    protected function _current_user_id()
    {
        $user_id = $this->session->userdata('user_id');

        if (!$user_id) {
            return null;
        }

        return $this->db->where('id', $user_id)->count_all_results('users') > 0 ? $user_id : null;
    }

    /**
     * Exclude soft deleted records
     */
    public function exclude_deleted()
    {
        $this->db->where('deleted_at IS NULL');
        return $this;
    }
}


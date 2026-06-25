<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'models/Base_Model.php');

class Member_request_model extends Base_Model
{
    protected $table = 'member_profile_requests';

    public function get_pending_for_user($user_id)
    {
        return $this->db
            ->where('user_id', $user_id)
            ->where('status', 'Pending')
            ->order_by('created_at', 'DESC')
            ->get($this->table)
            ->row_array();
    }

    public function get_requests($status = null)
    {
        $this->db->select('mpr.*, u.username, u.name as user_name');
        $this->db->from($this->table . ' mpr');
        $this->db->join('users u', 'u.id = mpr.user_id', 'left');
        if ($status) {
            $this->db->where('mpr.status', $status);
        }
        $this->db->order_by('mpr.created_at', 'DESC');
        return $this->db->get()->result_array();
    }

    public function get_request($id)
    {
        $this->db->select('mpr.*, u.username, u.name as user_name');
        $this->db->from($this->table . ' mpr');
        $this->db->join('users u', 'u.id = mpr.user_id', 'left');
        $this->db->where('mpr.id', $id);
        return $this->db->get()->row_array();
    }

    public function create_request($data)
    {
        return $this->insert_record($data);
    }

    public function approve_request($id, $admin_notes = '')
    {
        $request = $this->get_by_id($id);
        if (!$request || $request['status'] !== 'Pending') {
            return false;
        }

        if ($this->db->where('user_id', $request['user_id'])->count_all_results('members') > 0) {
            return false;
        }

        if ($this->db->where('national_id', $request['national_id'])->count_all_results('members') > 0) {
            return false;
        }

        $this->load->model('member_model');
        $member_id = $this->member_model->create_member(array(
            'user_id' => $request['user_id'],
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'gender' => $request['gender'],
            'date_of_birth' => $request['date_of_birth'],
            'national_id' => $request['national_id'],
            'phone_number' => $request['phone_number'],
            'email' => $request['email'],
            'physical_address' => $request['physical_address']
        ));

        if (!$member_id) {
            return false;
        }

        $this->db->where('id', $id);
        return $this->db->update($this->table, array(
            'status' => 'Approved',
            'member_id' => $member_id,
            'admin_notes' => $admin_notes,
            'reviewed_by' => $this->session->userdata('user_id'),
            'reviewed_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        )) ? $member_id : false;
    }

    public function reject_request($id, $admin_notes = '')
    {
        $request = $this->get_by_id($id);
        if (!$request || $request['status'] !== 'Pending') {
            return false;
        }

        $this->db->where('id', $id);
        return $this->db->update($this->table, array(
            'status' => 'Rejected',
            'admin_notes' => $admin_notes,
            'reviewed_by' => $this->session->userdata('user_id'),
            'reviewed_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ));
    }
}

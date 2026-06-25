<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Member_requests extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }

        $this->load->model('member_request_model');
        $this->load->model('notification_model');
        $this->load->library('form_validation');
    }

    public function request()
    {
        $user_id = $this->session->userdata('user_id');

        if ($this->db->where('user_id', $user_id)->count_all_results('members') > 0) {
            $this->session->set_flashdata('success', 'Your account is already linked to a member profile.');
            redirect('dashboard');
        }

        $pending = $this->member_request_model->get_pending_for_user($user_id);

        if ($this->input->post() && !$pending) {
            $this->_set_request_rules();

            if ($this->form_validation->run() === TRUE) {
                $data = array(
                    'user_id' => $user_id,
                    'first_name' => $this->input->post('first_name'),
                    'last_name' => $this->input->post('last_name'),
                    'gender' => $this->input->post('gender'),
                    'date_of_birth' => $this->input->post('date_of_birth') ?: null,
                    'national_id' => $this->input->post('national_id'),
                    'phone_number' => $this->input->post('phone_number'),
                    'email' => $this->input->post('email'),
                    'physical_address' => $this->input->post('physical_address'),
                    'status' => 'Pending'
                );

                $request_id = $this->member_request_model->create_request($data);
                if ($request_id) {
                    $this->_notify_admins($request_id, $data);
                    $this->session->set_flashdata('success', 'Your member profile request has been submitted for administrator approval.');
                    redirect('dashboard');
                }

                $this->session->set_flashdata('error', 'Unable to submit your request. Please try again.');
            }
        }

        $this->load->view('member_requests/request', array(
            'page_title' => 'Request Member Profile',
            'pending_request' => $pending,
            'user' => $this->db->get_where('users', array('id' => $user_id))->row_array()
        ));
    }

    public function admin_index()
    {
        $this->_require_admin();
        $this->_render_admin('member_requests/admin_index', array(
            'page_title' => 'Member Profile Requests',
            'requests' => $this->member_request_model->get_requests()
        ), 'admin_member_requests');
    }

    public function view($id)
    {
        $this->_require_admin();
        $request = $this->member_request_model->get_request($id);
        if (!$request) {
            show_404();
        }

        $this->_render_admin('member_requests/review', array(
            'page_title' => 'Review Member Request',
            'request' => $request
        ), 'admin_member_requests');
    }

    public function approve($id)
    {
        $this->_require_admin();
        $request = $this->member_request_model->get_by_id($id);
        if (!$request) {
            show_404();
        }

        $member_id = $this->member_request_model->approve_request($id, $this->input->post('admin_notes'));
        if ($member_id) {
            $this->_notify_user($request['user_id'], 'Member Profile Approved', 'Your member profile request has been approved. You can now submit contributions and view your member records.', 'Approval', 'member', $member_id);
            $this->session->set_flashdata('success', 'Member request approved and profile created.');
            redirect('members/view/' . $member_id);
        }

        $this->session->set_flashdata('error', 'Unable to approve this request. Check whether this user or national ID is already linked to a member.');
        redirect('admin/member-requests/view/' . $id);
    }

    public function reject($id)
    {
        $this->_require_admin();
        $request = $this->member_request_model->get_by_id($id);
        if (!$request) {
            show_404();
        }

        $notes = $this->input->post('admin_notes');
        if ($this->member_request_model->reject_request($id, $notes)) {
            $message = 'Your member profile request was rejected.';
            if ($notes) {
                $message .= ' Reason: ' . $notes;
            }
            $this->_notify_user($request['user_id'], 'Member Profile Request Rejected', $message, 'Rejection', 'member_profile_request', $id);
            $this->session->set_flashdata('success', 'Member request rejected.');
            redirect('admin/member-requests');
        }

        $this->session->set_flashdata('error', 'Unable to reject this request.');
        redirect('admin/member-requests/view/' . $id);
    }

    private function _set_request_rules()
    {
        $this->form_validation->set_rules('first_name', 'First Name', 'required');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required');
        $this->form_validation->set_rules('national_id', 'National ID', 'required');
        $this->form_validation->set_rules('phone_number', 'Phone Number', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
    }

    private function _notify_admins($request_id, $data)
    {
        $admins = $this->db->where_in('role_id', array(1, 2))->where('is_active', 1)->get('users')->result_array();
        foreach ($admins as $admin) {
            $this->_notify_user(
                $admin['id'],
                'New Member Profile Request',
                $data['first_name'] . ' ' . $data['last_name'] . ' requested member profile approval.',
                'System',
                'member_profile_request',
                $request_id
            );
        }
    }

    private function _notify_user($user_id, $title, $message, $type, $entity_type, $entity_id)
    {
        return $this->notification_model->insert_record(array(
            'user_id' => $user_id,
            'title' => $title,
            'message' => $message,
            'notification_type' => $type,
            'related_entity_type' => $entity_type,
            'related_entity_id' => $entity_id,
            'channel' => 'In-System'
        ));
    }

    private function _require_admin()
    {
        if (!in_array((int) $this->session->userdata('role_id'), array(1, 2))) {
            show_403();
        }
    }

    private function _render_admin($view, $data, $current_page)
    {
        $this->load->view('layouts/header', array(
            'show_sidebar' => true,
            'page_title' => $data['page_title'] ?? 'Member Requests',
            'current_page' => $current_page,
            'is_admin' => true
        ));
        $this->load->view($view, $data);
        $this->load->view('layouts/footer');
    }
}

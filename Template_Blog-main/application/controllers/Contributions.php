<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Contributions Controller - Contribution management
 */
class Contributions extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        // Require login
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }

        $this->load->model('contribution_model');
        $this->load->model('member_model');
        $this->load->model('user_model');
        $this->load->library('form_validation');
    }

    /**
     * List contributions
     */
    public function index()
    {
        // Check permission
        if (!$this->user_model->has_permission($this->session->userdata('user_id'), 'view_contributions')) {
            show_403();
        }

        $page = $this->input->get('page') ?? 0;
        $limit = 20;
        $status = $this->input->get('status');

        $data = array(
            'page_title' => 'Contributions',
            'page_heading' => 'Contribution Management',
            'breadcrumbs' => array(
                'Dashboard' => 'dashboard',
                'Contributions' => 'contributions'
            )
        );

        // Get contributions
        $this->db->select('c.*, m.member_number, m.first_name, m.last_name, CONCAT(m.first_name, " ", m.last_name) as member_name');
        $this->db->from('contributions c');
        $this->db->join('members m', 'c.member_id = m.id');

        if ($status) {
            $this->db->where('c.status', $status);
        }

        $this->db->where('c.deleted_at IS NULL', null, false);
        $this->db->order_by('c.transaction_date', 'DESC');

        $data['total'] = $this->db->count_all_results('', false);

        $this->db->limit($limit, $page * $limit);
        $data['contributions'] = $this->db->get()->result_array();

        // Pagination
        $this->load->library('pagination');
        $config = array(
            'base_url' => base_url('contributions?page='),
            'total_rows' => $data['total'],
            'per_page' => $limit,
            'use_page_numbers' => true
        );
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();


        $this->load->view('contributions/index', $data);
    }

    /**
     * View contribution
     */
    public function view($contribution_id)
    {
        if (!$this->_can_access_contribution($contribution_id)) {
            show_403();
        }

        $this->db->select('c.*, m.member_number, m.first_name, m.last_name, CONCAT(m.first_name, " ", m.last_name) as member_name');
        $this->db->from('contributions c');
        $this->db->join('members m', 'c.member_id = m.id');
        $this->db->where('c.id', $contribution_id);
        $contribution = $this->db->get()->row_array();

        if (!$contribution) {
            show_404();
        }

        // Get documents
        $this->db->where('contribution_id', $contribution_id);
        $documents = $this->db->get('contribution_documents')->result_array();

        $data = array(
            'page_title' => 'Contribution Details',
            'breadcrumbs' => array(
                'Dashboard' => 'dashboard',
                'Contributions' => 'contributions',
                'Details' => ''
            ),
            'contribution' => $contribution,
            'documents' => $documents
        );


        $this->_render('contributions/view', $data, 'contributions');
    }

    /**
     * Submit contribution (Member)
     */
    public function submit()
    {
        // Check permission
        if (!$this->user_model->has_permission($this->session->userdata('user_id'), 'submit_contribution')) {
            show_403();
        }

        // Get member for logged-in user
        $this->db->where('user_id', $this->session->userdata('user_id'));
        $member = $this->db->get('members')->row_array();

        if (!$member) {
            $this->session->set_flashdata('error', 'Member profile not found');
            redirect('dashboard');
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('amount', 'Amount', 'required|numeric|greater_than[0]');
            $this->form_validation->set_rules('payment_method', 'Payment Method', 'required');

            if ($this->form_validation->run() == FALSE) {
                $data['errors'] = $this->form_validation->error_array();
            } else {
                $contribution_data = array(
                    'member_id' => $member['id'],
                    'amount' => $this->input->post('amount'),
                    'payment_method' => $this->input->post('payment_method'),
                    'notes' => $this->input->post('notes')
                );

                $contribution_id = $this->contribution_model->submit_contribution($contribution_data);

                if ($contribution_id) {
                    // Handle file upload
                    if ($_FILES['proof_of_payment']['name']) {
                        $this->_handle_file_upload($contribution_id);
                    }

                    // Send notification
                    $this->load->model('notification_model');
                    $this->notification_model->notify_member(
                        $member['id'],
                        'Contribution Submitted',
                        'Your contribution of N$' . $contribution_data['amount'] . ' has been submitted for approval.',
                        'Contribution'
                    );

                    $this->session->set_flashdata('success', 'Contribution submitted successfully');
                    redirect('contributions/view/' . $contribution_id);
                } else {
                    $this->session->set_flashdata('error', 'Error submitting contribution');
                }
            }
        }

        $data = array(
            'page_title' => 'Submit Contribution',
            'page_heading' => 'Submit Contribution',
            'breadcrumbs' => array(
                'Dashboard' => 'dashboard',
                'Contributions' => 'contributions',
                'Submit' => ''
            ),
            'member' => $member
        );


        $this->load->view('contributions/submit', $data);
    }

    /**
     * Approve contribution
     */
    public function approve($contribution_id)
    {
        // Check permission
        if (!$this->user_model->has_permission($this->session->userdata('user_id'), 'approve_contribution')) {
            show_403();
        }

        $contribution = $this->contribution_model->get_by_id($contribution_id);

        if (!$contribution) {
            show_404();
        }

        if ($this->input->post()) {
            $notes = $this->input->post('approval_notes');
            $result = $this->contribution_model->approve_contribution($contribution_id, $notes);

            if ($result) {
                $this->session->set_flashdata('success', 'Contribution approved');
            } else {
                $this->session->set_flashdata('error', 'Error approving contribution');
            }
        }

        redirect('contributions/view/' . $contribution_id);
    }

    /**
     * Reject contribution
     */
    public function reject($contribution_id)
    {
        // Check permission
        if (!$this->user_model->has_permission($this->session->userdata('user_id'), 'reject_contribution')) {
            show_403();
        }

        $contribution = $this->contribution_model->get_by_id($contribution_id);

        if (!$contribution) {
            show_404();
        }

        if ($this->input->post()) {
            $reason = $this->input->post('rejection_reason');
            $result = $this->contribution_model->reject_contribution($contribution_id, $reason);

            if ($result) {
                $this->session->set_flashdata('success', 'Contribution rejected');
            } else {
                $this->session->set_flashdata('error', 'Error rejecting contribution');
            }
        }

        redirect('contributions/view/' . $contribution_id);
    }

    /**
     * Handle file upload
     */
    private function _handle_file_upload($contribution_id)
    {
        $upload_config = array(
            'upload_path' => './uploads/contributions/',
            'allowed_types' => 'gif|jpg|png|pdf',
            'max_size' => 5120, // 5MB
            'file_name' => 'contribution_' . $contribution_id . '_' . time()
        );

        $this->load->library('upload', $upload_config);

        if ($this->upload->do_upload('proof_of_payment')) {
            $file_data = $this->upload->data();

            $file_info = array(
                'file_name' => $file_data['file_name'],
                'file_path' => 'uploads/contributions/' . $file_data['file_name'],
                'file_size' => $file_data['file_size'],
                'file_type' => $file_data['file_type']
            );

            $this->contribution_model->upload_document($contribution_id, $file_info);
        } else {
            $this->session->set_flashdata('warning', 'File upload failed: ' . $this->upload->display_errors());
        }
    }

    /**
     * Download document
     */
    public function download_document($document_id)
    {
        $this->db->select('cd.*, c.member_id');
        $this->db->from('contribution_documents cd');
        $this->db->join('contributions c', 'cd.contribution_id = c.id');
        $this->db->where('cd.id', $document_id);
        $document = $this->db->get()->row_array();

        if (!$document) {
            show_404();
        }

        if (!$this->_can_access_member($document['member_id'])) {
            show_403();
        }

        $file_path = FCPATH . $document['file_path'];

        if (file_exists($file_path)) {
            $this->load->helper('download');
            force_download($file_path, NULL);
        } else {
            show_404();
        }
    }

    /**
     * Get pending contributions (AJAX)
     */
    public function get_pending()
    {
        if (!$this->user_model->has_permission($this->session->userdata('user_id'), 'approve_contribution')) {
            show_403();
        }

        $this->output->set_content_type('application/json');

        $pending = $this->contribution_model->get_pending_contributions(10);

        echo json_encode(array(
            'success' => true,
            'count' => count($pending),
            'contributions' => $pending
        ));
    }

    private function _can_access_contribution($contribution_id)
    {
        $this->db->select('member_id');
        $contribution = $this->db->get_where('contributions', array('id' => $contribution_id))->row_array();

        if (!$contribution) {
            return true;
        }

        return $this->_can_access_member($contribution['member_id']);
    }

    private function _can_access_member($member_id)
    {
        if ($this->user_model->has_permission($this->session->userdata('user_id'), 'view_contributions')) {
            return true;
        }

        $this->db->where('id', $member_id);
        $this->db->where('user_id', $this->session->userdata('user_id'));

        return $this->db->count_all_results('members') > 0;
    }
    private function _render($view, $data = array(), $current_page = 'contributions')
    {
        $this->load->view('layouts/header', array(
            'show_sidebar' => true,
            'page_title' => $data['page_title'] ?? 'Contributions',
            'current_page' => $current_page,
            'is_admin' => in_array((int) $this->session->userdata('role_id'), array(1, 2))
        ));
        $this->load->view($view, $data);
        $this->load->view('layouts/footer');
    }
}

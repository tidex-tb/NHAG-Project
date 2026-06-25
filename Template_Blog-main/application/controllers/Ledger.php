<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Ledger Controller - Savings ledger management
 */
class Ledger extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }

        $this->load->model('ledger_model');
        $this->load->model('member_model');
        $this->load->model('user_model');
    }

    /**
     * View ledger (Admin only)
     */
    public function index()
    {
        if (!$this->user_model->has_permission($this->session->userdata('user_id'), 'view_ledger')) {
            show_403();
        }

        $page = $this->input->get('page') ?? 0;
        $limit = 50;

        $data = array(
            'page_title' => 'Savings Ledger',
            'page_heading' => 'Savings Ledger',
            'breadcrumbs' => array(
                'Dashboard' => 'dashboard',
                'Ledger' => 'ledger'
            )
        );

        // Get all ledger entries
        $this->db->select('l.*, m.member_number, m.first_name, m.last_name');
        $this->db->from('savings_ledger l');
        $this->db->join('members m', 'l.member_id = m.id');
        $this->db->order_by('l.transaction_date', 'DESC');

        $data['total'] = $this->db->count_all_results('', false);

        $this->db->limit($limit, $page * $limit);
        $data['entries'] = $this->db->get()->result_array();

        // Pagination
        $this->load->library('pagination');
        $config = array(
            'base_url' => base_url('ledger?page='),
            'total_rows' => $data['total'],
            'per_page' => $limit,
            'use_page_numbers' => true
        );
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();


        $this->_render('ledger/index', $data);
    }

    /**
     * View member ledger
     */
    public function member($member_id)
    {
        $member = $this->member_model->get_by_id($member_id);

        if (!$member) {
            show_404();
        }

        // Check if user owns this member profile or is admin
        $user_id = $this->session->userdata('user_id');
        $this->db->where('user_id', $user_id);
        $user_member = $this->db->get('members')->row_array();

        if (!in_array($this->session->userdata('role_id'), array(1, 2, 3)) && (!$user_member || $user_member['id'] != $member_id)) {
            show_403();
        }

        $page = $this->input->get('page') ?? 0;
        $limit = 20;

        $data = array(
            'page_title' => 'Member Ledger',
            'page_heading' => 'Savings Ledger - ' . $member['first_name'] . ' ' . $member['last_name'],
            'member' => $member,
            'breadcrumbs' => array(
                'Dashboard' => 'dashboard',
                'Members' => 'members',
                'Ledger' => ''
            )
        );

        // Get ledger entries
        $data['entries'] = $this->ledger_model->get_member_ledger($member_id, $limit, $page * $limit);
        $data['total'] = count($this->ledger_model->get_member_ledger($member_id));

        // Get summary
        $data['balance'] = $this->ledger_model->get_member_balance($member_id);

        // Pagination
        $this->load->library('pagination');
        $config = array(
            'base_url' => base_url('ledger/member/' . $member_id . '?page='),
            'total_rows' => $data['total'],
            'per_page' => $limit,
            'use_page_numbers' => true
        );
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();


        $this->_render('ledger/member', $data);
    }

    /**
     * Get ledger summary
     */
    public function summary()
    {
        $this->output->set_content_type('application/json');

        $member_id = $this->input->get('member_id');

        if (!$member_id) {
            echo json_encode(array('success' => false, 'message' => 'Invalid member'));
            return;
        }

        if (!$this->_can_access_member($member_id)) {
            show_403();
        }

        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        if (!$start_date || !$end_date) {
            $start_date = date('Y-m-01');
            $end_date = date('Y-m-t');
        }

        $summary = $this->ledger_model->get_summary($member_id, $start_date, $end_date);
        $balance = $this->ledger_model->get_member_balance($member_id);

        echo json_encode(array(
            'success' => true,
            'summary' => $summary,
            'balance' => $balance
        ));
    }

    /**
     * Record adjustment (Admin only)
     */
    public function record_adjustment()
    {
        if (!$this->user_model->has_permission($this->session->userdata('user_id'), 'manage_ledger')) {
            show_403();
        }

        $this->load->library('form_validation');

        if ($this->input->post()) {
            $this->form_validation->set_rules('member_id', 'Member', 'required|numeric');
            $this->form_validation->set_rules('amount', 'Amount', 'required|numeric|greater_than[0]');
            $this->form_validation->set_rules('description', 'Description', 'required');
            $this->form_validation->set_rules('operation', 'Operation', 'required|in_list[credit,debit]');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', $this->form_validation->error_string());
            } else {
                $member_id = $this->input->post('member_id');
                $amount = $this->input->post('amount');
                $description = $this->input->post('description');
                $operation = $this->input->post('operation');

                $result = $this->ledger_model->record_adjustment($member_id, $amount, $description, $operation);

                if ($result) {
                    $this->session->set_flashdata('success', 'Adjustment recorded successfully');
                } else {
                    $this->session->set_flashdata('error', 'Error recording adjustment');
                }
            }

            redirect('ledger/member/' . $this->input->post('member_id'));
        }

        show_404();
    }

    /**
     * Export ledger (AJAX)
     */
    public function export()
    {
        $member_id = $this->input->get('member_id');
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        if (!$this->_can_access_member($member_id)) {
            show_403();
        }

        $entries = $this->ledger_model->get_by_date_range($member_id, $start_date, $end_date);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="ledger_' . date('Y-m-d_His') . '.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, array('Date', 'Type', 'Reference', 'Debit', 'Credit', 'Balance'));

        foreach ($entries as $entry) {
            fputcsv($output, array(
                $entry['transaction_date'],
                $entry['transaction_type'],
                $entry['reference_number'],
                $entry['debit_amount'],
                $entry['credit_amount'],
                $entry['balance_after']
            ));
        }

        fclose($output);
    }

    private function _can_access_member($member_id)
    {
        if ($this->user_model->has_permission($this->session->userdata('user_id'), 'view_ledger')) {
            return true;
        }

        $this->db->where('id', $member_id);
        $this->db->where('user_id', $this->session->userdata('user_id'));

        return $this->db->count_all_results('members') > 0;
    }

    private function _render($view, $data = array())
    {
        $this->load->view('layouts/header', array(
            'show_sidebar' => true,
            'page_title' => $data['page_title'] ?? 'Ledger',
            'current_page' => 'ledger',
            'is_admin' => in_array((int) $this->session->userdata('role_id'), array(1, 2))
        ));
        $this->load->view($view, $data);
        $this->load->view('layouts/footer');
    }
}

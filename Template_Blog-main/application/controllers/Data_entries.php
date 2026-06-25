<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Data_entries extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }

        if (!in_array((int) $this->session->userdata('role_id'), array(1, 2, 3))) {
            show_403();
        }

        $this->load->model('data_entry_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data = array(
            'page_title' => 'Group Data Entries',
            'page_heading' => 'Regional Group Data Entries',
            'entries' => $this->data_entry_model->get_entries(),
            'totals' => $this->data_entry_model->get_totals()
        );

        $this->load->view('data_entries/index', $data);
    }

    public function create()
    {
        if ($this->input->post()) {
            $this->_set_validation_rules();

            if ($this->form_validation->run() === TRUE) {
                $entry_id = $this->data_entry_model->create_entry($this->_entry_data_from_post());

                if ($entry_id) {
                    $this->session->set_flashdata('success', 'Data entry saved successfully.');
                    redirect('data-entries/view/' . $entry_id);
                }

                $this->session->set_flashdata('error', 'Unable to save data entry.');
            }
        }

        $data = array(
            'page_title' => 'Create Data Entry',
            'page_heading' => 'Create Regional Group Data Entry'
        );

        $this->load->view('data_entries/create', $data);
    }

    public function view($entry_id)
    {
        $entry = $this->data_entry_model->get_by_id($entry_id);

        if (!$entry) {
            show_404();
        }

        $data = array(
            'page_title' => 'Data Entry Details',
            'page_heading' => 'Regional Group Data Entry',
            'entry' => $entry
        );

        $this->load->view('data_entries/view', $data);
    }

    private function _set_validation_rules()
    {
        $this->form_validation->set_rules('region_name', 'Region', 'required');
        $this->form_validation->set_rules('area_type', 'Area Type', 'required');
        $this->form_validation->set_rules('area_name', 'Area Name', 'required');
        $this->form_validation->set_rules('capture_year', 'Capture Year', 'required|integer');
        $this->form_validation->set_rules('group_number', 'Group Number', 'required');
        $this->form_validation->set_rules('number_of_members', 'Number of Members', 'required|integer|greater_than_equal_to[0]');
        $this->form_validation->set_rules('number_of_males', 'Number of Males', 'required|integer|greater_than_equal_to[0]');
        $this->form_validation->set_rules('number_of_females', 'Number of Females', 'required|integer|greater_than_equal_to[0]');
    }

    private function _entry_data_from_post()
    {
        $money_fields = array(
            'savings_brought_forward', 'six_month_savings', 'total_land', 'total_member_withdrawals',
            'total_loans_issued', 'total_loans_repaid', 'total_interest_earned', 'total_advance_payments',
            'total_short', 'total_cash', 'total_bank_amount', 'total_bank_charges', 'total_business',
            'total_daily_needs', 'total_ppe', 'total_buy_a_brick'
        );

        $data = array(
            'region_name' => $this->input->post('region_name'),
            'municipal_area_name' => $this->input->post('municipal_area_name'),
            'area_type' => $this->input->post('area_type'),
            'area_name' => $this->input->post('area_name'),
            'area_count' => (int) $this->input->post('area_count'),
            'entry_notes' => $this->input->post('entry_notes'),
            'capture_year' => (int) $this->input->post('capture_year'),
            'group_number' => $this->input->post('group_number'),
            'group_name' => $this->input->post('group_name'),
            'number_of_members' => (int) $this->input->post('number_of_members'),
            'number_of_males' => (int) $this->input->post('number_of_males'),
            'number_of_females' => (int) $this->input->post('number_of_females')
        );

        foreach ($money_fields as $field) {
            $data[$field] = (float) ($this->input->post($field) ?: 0);
        }

        $data['total_expenditure'] =
            $data['total_cash'] + $data['total_bank_charges'] + $data['total_business'] +
            $data['total_daily_needs'] + $data['total_ppe'] + $data['total_buy_a_brick'];

        return $data;
    }
}


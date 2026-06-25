<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Reports Controller - Reporting and analytics
 */
class Reports extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }

        $this->load->model('member_model');
        $this->load->model('contribution_model');
        $this->load->model('ledger_model');
        $this->load->model('user_model');
    }

    /**
     * Reports index
     */
    public function index()
    {
        if (!$this->user_model->has_permission($this->session->userdata('user_id'), 'view_reports')) {
            show_403();
        }

        $data = array(
            'page_title' => 'Reports',
            'page_heading' => 'Reports & Analytics',
            'breadcrumbs' => array(
                'Dashboard' => 'dashboard',
                'Reports' => 'reports'
            )
        );


        $this->_render('reports/index', $data);
    }

    /**
     * Savings report
     */
    public function savings_report()
    {
        if (!$this->user_model->has_permission($this->session->userdata('user_id'), 'generate_reports')) {
            show_403();
        }

        $period = $this->input->get('period') ?? 'monthly';
        $date = $this->input->get('date') ?? date('Y-m-d');

        $data = array(
            'page_title' => 'Savings Report',
            'page_heading' => 'Savings Report',
            'breadcrumbs' => array(
                'Dashboard' => 'dashboard',
                'Reports' => 'reports',
                'Savings' => ''
            )
        );

        // Get date range
        $date_range = $this->_get_date_range($period, $date);

        // Get savings data
        $this->db->select('DATE(transaction_date) as date, SUM(credit_amount) as total');
        $this->db->from('savings_ledger');
        $this->db->where('transaction_date >=', $date_range['start']);
        $this->db->where('transaction_date <=', $date_range['end']);
        $this->db->group_by('DATE(transaction_date)');
        $this->db->order_by('date', 'ASC');
        $data['chart_data'] = $this->db->get()->result_array();

        // Get summary
        $summary = $this->ledger_model->get_total_savings($date_range['start'], $date_range['end']);
        $data['total_savings'] = $summary['total_savings'] ?? 0;


        $this->_render('reports/savings_report', $data);
    }

    /**
     * Contributions report
     */
    public function contributions_report()
    {
        if (!$this->user_model->has_permission($this->session->userdata('user_id'), 'generate_reports')) {
            show_403();
        }

        $data = array(
            'page_title' => 'Contributions Report',
            'page_heading' => 'Contributions Report',
            'breadcrumbs' => array(
                'Dashboard' => 'dashboard',
                'Reports' => 'reports',
                'Contributions' => ''
            )
        );

        $data['stats'] = $this->contribution_model->get_contribution_statistics();


        $this->_render('reports/contributions_report', $data);
    }

    /**
     * Members report
     */
    public function members_report()
    {
        if (!$this->user_model->has_permission($this->session->userdata('user_id'), 'generate_reports')) {
            show_403();
        }

        $data = array(
            'page_title' => 'Members Report',
            'page_heading' => 'Members Report',
            'breadcrumbs' => array(
                'Dashboard' => 'dashboard',
                'Reports' => 'reports',
                'Members' => ''
            )
        );

        $data['stats'] = $this->member_model->get_member_statistics();

        // Get members by employment status
        $this->db->select('employment_status, COUNT(*) as count');
        $this->db->from('members');
        $this->db->where('deleted_at IS NULL', null, false);
        $this->db->group_by('employment_status');
        $data['employment_data'] = $this->db->get()->result_array();


        $this->_render('reports/members_report', $data);
    }

    /**
     * Export report
     */
    public function export($type = 'csv')
    {
        if (!$this->user_model->has_permission($this->session->userdata('user_id'), 'export_reports')) {
            show_403();
        }

        $report_type = $this->input->get('report_type') ?? 'members';

        if ($type == 'csv') {
            $this->_export_csv($report_type);
        } elseif ($type == 'excel') {
            $this->_export_excel($report_type);
        } elseif ($type == 'pdf') {
            $this->_export_pdf($report_type);
        } else {
            show_404();
        }
    }

    /**
     * Export as CSV
     */
    private function _export_csv($report_type)
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="report_' . date('Y-m-d_His') . '.csv"');

        $output = fopen('php://output', 'w');

        if ($report_type == 'members') {
            fputcsv($output, array('Member Number', 'Name', 'Email', 'Phone', 'Status', 'Registration Date', 'Total Savings'));

            $members = $this->member_model->get_all();
            foreach ($members as $member) {
                fputcsv($output, array(
                    $member['member_number'],
                    $member['first_name'] . ' ' . $member['last_name'],
                    $member['email'],
                    $member['phone_number'],
                    $member['status'],
                    $member['registration_date'],
                    $member['current_balance']
                ));
            }
        } elseif ($report_type == 'contributions') {
            fputcsv($output, array('Reference', 'Member', 'Amount', 'Date', 'Status'));

            $this->db->where('deleted_at IS NULL', null, false);
            $this->db->order_by('transaction_date', 'DESC');
            $contributions = $this->db->get('contributions')->result_array();

            foreach ($contributions as $contrib) {
                $member = $this->member_model->get_by_id($contrib['member_id']);
                fputcsv($output, array(
                    $contrib['reference_number'],
                    $member['first_name'] . ' ' . $member['last_name'],
                    $contrib['amount'],
                    $contrib['transaction_date'],
                    $contrib['status']
                ));
            }
        }

        fclose($output);
    }

    /**
     * Export as Excel
     */
    private function _export_excel($report_type)
    {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="report_' . date('Y-m-d_His') . '.xls"');
        $this->_write_tabular_export($report_type, "\t");
    }

    /**
     * Export as PDF
     */
    private function _export_pdf($report_type)
    {
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: attachment; filename="report_' . date('Y-m-d_His') . '.html"');

        echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Report</title>';
        echo '<style>body{font-family:Arial,sans-serif;margin:32px}table{border-collapse:collapse;width:100%}th,td{border:1px solid #ddd;padding:8px}th{background:#f1f3f5}</style>';
        echo '</head><body><h1>' . html_escape(ucfirst($report_type)) . ' Report</h1><table>';
        $this->_write_html_export_rows($report_type);
        echo '</table></body></html>';
    }

    private function _write_tabular_export($report_type, $delimiter = ",")
    {
        $output = fopen('php://output', 'w');
        $rows = $this->_get_export_rows($report_type);

        foreach ($rows as $row) {
            fputcsv($output, $row, $delimiter);
        }

        fclose($output);
    }

    private function _write_html_export_rows($report_type)
    {
        $rows = $this->_get_export_rows($report_type);

        foreach ($rows as $index => $row) {
            echo '<tr>';
            foreach ($row as $cell) {
                echo $index === 0 ? '<th>' . html_escape($cell) . '</th>' : '<td>' . html_escape($cell) . '</td>';
            }
            echo '</tr>';
        }
    }

    private function _get_export_rows($report_type)
    {
        $rows = array();

        if ($report_type == 'contributions') {
            $rows[] = array('Reference', 'Member', 'Amount', 'Date', 'Status');
            $this->db->select('c.*, m.first_name, m.last_name');
            $this->db->from('contributions c');
            $this->db->join('members m', 'm.id = c.member_id', 'left');
            $this->db->where('c.deleted_at IS NULL', null, false);
            $this->db->order_by('c.transaction_date', 'DESC');
            foreach ($this->db->get()->result_array() as $contrib) {
                $rows[] = array(
                    $contrib['reference_number'],
                    trim($contrib['first_name'] . ' ' . $contrib['last_name']),
                    $contrib['amount'],
                    $contrib['transaction_date'],
                    $contrib['status']
                );
            }
            return $rows;
        }

        $rows[] = array('Member Number', 'Name', 'Email', 'Phone', 'Status', 'Registration Date', 'Total Savings');
        foreach ($this->member_model->get_all() as $member) {
            $rows[] = array(
                $member['member_number'],
                $member['first_name'] . ' ' . $member['last_name'],
                $member['email'],
                $member['phone_number'],
                $member['status'],
                $member['registration_date'],
                $member['current_balance']
            );
        }

        return $rows;
    }

    /**
     * Get date range for period
     */
    private function _get_date_range($period, $date)
    {
        $date_obj = new DateTime($date);

        switch ($period) {
            case 'daily':
                return array(
                    'start' => $date,
                    'end' => $date
                );
            case 'weekly':
                $date_obj->modify('monday this week');
                $start = $date_obj->format('Y-m-d');
                $date_obj->modify('+6 days');
                $end = $date_obj->format('Y-m-d');
                return array('start' => $start, 'end' => $end);
            case 'monthly':
                $date_obj->modify('first day of this month');
                $start = $date_obj->format('Y-m-d');
                $date_obj->modify('last day of this month');
                $end = $date_obj->format('Y-m-d');
                return array('start' => $start, 'end' => $end);
            case 'annual':
                $year = $date_obj->format('Y');
                return array('start' => $year . '-01-01', 'end' => $year . '-12-31');
            default:
                return array('start' => $date, 'end' => $date);
        }
    }

    private function _render($view, $data = array())
    {
        $this->load->view('layouts/header', array(
            'show_sidebar' => true,
            'page_title' => $data['page_title'] ?? 'Reports',
            'current_page' => 'reports',
            'is_admin' => in_array((int) $this->session->userdata('role_id'), array(1, 2))
        ));
        $this->load->view($view, $data);
        $this->load->view('layouts/footer');
    }
}

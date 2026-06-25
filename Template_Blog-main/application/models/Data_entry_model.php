<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'models/Base_Model.php');

class Data_entry_model extends Base_Model
{
    protected $table = 'group_data_entries';

    public function get_entries($limit = 50, $offset = 0)
    {
        $this->db->order_by('capture_year', 'DESC');
        $this->db->order_by('region_name', 'ASC');
        $this->db->limit($limit, $offset);

        return $this->db->get($this->table)->result_array();
    }

    public function create_entry($data)
    {
        return $this->insert_record($data);
    }

    public function get_totals()
    {
        $this->db->select('
            COUNT(id) as entry_count,
            SUM(area_count) as total_areas,
            SUM(number_of_members) as total_members,
            SUM(number_of_males) as total_males,
            SUM(number_of_females) as total_females,
            SUM(savings_brought_forward) as savings_brought_forward,
            SUM(six_month_savings) as six_month_savings,
            SUM(total_loans_issued) as total_loans_issued,
            SUM(total_loans_repaid) as total_loans_repaid,
            SUM(total_expenditure) as total_expenditure
        ');

        return $this->db->get($this->table)->row_array();
    }
}

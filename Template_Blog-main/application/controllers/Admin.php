<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Admin Controller - System administration
 */
class Admin extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }

        $this->load->model('user_model');
        $this->load->model('role_model');
        $this->load->model('permission_model');
        $this->load->model('audit_model');
        $this->load->model('system_setting_model');
        $this->load->library('form_validation');

        // Check admin permission
        if (!in_array((int) $this->session->userdata('role_id'), array(1, 2))) {
            show_403();
        }
    }

    /**
     * Admin dashboard
     */
    public function index()
    {
        $data = array(
            'page_title' => 'Administration',
            'page_heading' => 'System Administration',
            'breadcrumbs' => array('Administration' => 'admin')
        );


        $this->_render('admin/index', $data, 'admin');
    }

    /**
     * List users
     */
    public function users()
    {
        $page = $this->input->get('page') ?? 0;
        $limit = 20;

        $data = array(
            'page_title' => 'User Management',
            'page_heading' => 'Manage Users',
            'breadcrumbs' => array(
                'Administration' => 'admin',
                'Users' => 'admin/users'
            )
        );

        $data['users'] = $this->user_model->get_all('created_at DESC', $limit, $page * $limit);
        $data['total'] = $this->user_model->get_count();

        // Pagination
        $this->load->library('pagination');
        $config = array(
            'base_url' => base_url('admin/users?page='),
            'total_rows' => $data['total'],
            'per_page' => $limit,
            'use_page_numbers' => true
        );
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();


        $this->_render('admin/users', $data, 'admin_users');
    }

    /**
     * Create user
     */
    public function create_user()
    {
        if ($this->input->post()) {
            $this->form_validation->set_rules('name', 'Name', 'required');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]');
            $this->form_validation->set_rules('username', 'Username', 'required|is_unique[users.username]');
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
            $this->form_validation->set_rules('role_id', 'Role', 'required|numeric');

            if ($this->form_validation->run() == FALSE) {
                $data['errors'] = $this->form_validation->error_array();
            } else {
                $user_data = array(
                    'name' => $this->input->post('name'),
                    'email' => $this->input->post('email'),
                    'username' => $this->input->post('username'),
                    'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                    'phone_number' => $this->input->post('phone_number'),
                    'role_id' => $this->input->post('role_id'),
                    'is_active' => 1
                );

                $user_id = $this->db->insert('users', $user_data) ? $this->db->insert_id() : false;

                if ($user_id) {
                    $this->session->set_flashdata('success', 'User created successfully');
                    redirect('admin/users');
                }
            }
        }

        $data = array(
            'page_title' => 'Create User',
            'page_heading' => 'Add New User',
            'roles' => $this->role_model->get_all(),
            'breadcrumbs' => array(
                'Administration' => 'admin',
                'Users' => 'admin/users',
                'Create' => ''
            )
        );


        $this->_render('admin/create_user', $data, 'admin_users');
    }

    /**
     * Edit user
     */
    public function edit_user($user_id)
    {
        $user = $this->user_model->get_by_id($user_id);

        if (!$user) {
            show_404();
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('name', 'Name', 'required');
            $this->form_validation->set_rules('role_id', 'Role', 'required');

            if ($this->form_validation->run() == FALSE) {
                $data['errors'] = $this->form_validation->error_array();
            } else {
                $update_data = array(
                    'name' => $this->input->post('name'),
                    'phone_number' => $this->input->post('phone_number'),
                    'role_id' => $this->input->post('role_id')
                );

                if ($this->input->post('password')) {
                    $update_data['password'] = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
                }

                $result = $this->user_model->update_record($user_id, $update_data);

                if ($result) {
                    $this->session->set_flashdata('success', 'User updated successfully');
                    redirect('admin/users');
                }
            }
        }

        $data = array(
            'page_title' => 'Edit User',
            'page_heading' => 'Edit User',
            'user' => $user,
            'roles' => $this->role_model->get_all(),
            'breadcrumbs' => array(
                'Administration' => 'admin',
                'Users' => 'admin/users',
                'Edit' => ''
            )
        );


        $this->_render('admin/edit_user', $data, 'admin_users');
    }

    /**
     * Role management
     */
    public function roles()
    {
        $this->_require_super_admin();

        $data = array(
            'page_title' => 'Roles',
            'page_heading' => 'Role Management',
            'roles' => $this->role_model->get_roles_with_permissions(),
            'breadcrumbs' => array(
                'Administration' => 'admin',
                'Roles' => 'admin/roles'
            )
        );


        $this->_render('admin/roles', $data, 'admin_roles');
    }

    /**
     * Permission management
     */
    public function permissions()
    {
        $this->_require_super_admin();

        $data = array(
            'page_title' => 'Permissions',
            'page_heading' => 'Permission Management',
            'permissions' => $this->permission_model->get_grouped_by_module(),
            'breadcrumbs' => array(
                'Administration' => 'admin',
                'Permissions' => 'admin/permissions'
            )
        );


        $this->_render('admin/permissions', $data, 'admin_permissions');
    }

    /**
     * Audit logs
     */
    public function audit_logs()
    {
        $this->_require_super_admin();

        $page = $this->input->get('page') ?? 0;
        $limit = 50;

        $data = array(
            'page_title' => 'Audit Logs',
            'page_heading' => 'Audit Logs',
            'breadcrumbs' => array(
                'Administration' => 'admin',
                'Audit Logs' => 'admin/audit-logs'
            )
        );

        $data['logs'] = $this->audit_model->get_audit_logs($limit, $page * $limit);
        $data['total'] = $this->db->count_all('audit_logs');

        // Pagination
        $this->load->library('pagination');
        $config = array(
            'base_url' => base_url('admin/audit-logs?page='),
            'total_rows' => $data['total'],
            'per_page' => $limit,
            'use_page_numbers' => true
        );
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();


        $this->_render('admin/audit_logs', $data, 'admin_audit');
    }

    /**
     * System settings
     */
    public function settings()
    {
        $this->_require_super_admin();

        if ($this->input->post()) {
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'setting_') === 0) {
                    $setting_key = str_replace('setting_', '', $key);
                    $this->system_setting_model->update_setting($setting_key, $value);
                }
            }

            $this->session->set_flashdata('success', 'Settings updated successfully');
            redirect('admin/settings');
        }

        $data = array(
            'page_title' => 'Settings',
            'page_heading' => 'System Settings',
            'settings' => $this->system_setting_model->get_all_settings(),
            'breadcrumbs' => array(
                'Administration' => 'admin',
                'Settings' => 'admin/settings'
            )
        );


        $this->_render('admin/settings', $data, 'admin_settings');
    }

    private function _render($view, $data = array(), $current_page = 'admin')
    {
        $data['current_page'] = $current_page;
        $this->load->view('layouts/header', array(
            'show_sidebar' => true,
            'page_title' => $data['page_title'] ?? 'Administration',
            'current_page' => $current_page,
            'is_admin' => in_array((int) $this->session->userdata('role_id'), array(1, 2))
        ));
        $this->load->view($view, $data);
        $this->load->view('layouts/footer');
    }
    private function _require_super_admin()
    {
        if ((int) $this->session->userdata('role_id') !== 1) {
            show_403();
        }
    }
}

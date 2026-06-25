<?php
class Categories  extends CI_Controller
{
    public function index()
    {
        $data['title'] = 'Categories';
        // load view for categories page
        $data['categories'] =  $this->category_model->get_categories();
        $this->load->view('templates/header');
        $this->load->view('categories/index', $data);
        $this->load->view('templates/footer');
    }
    public function  get_categories()
    {
        $this->db->order_by('name');
        $query = $this->db->get('categories');
        return $query->result_array();
    }

    public function create()
    { //check Login
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
        $data['title'] = 'Create Category';

        $this->form_validation->set_rules('name', 'Name', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('categories/create', $data);
            $this->load->view('templates/footer', $data);
        } else {
            $this->category_model->create_category();
            $this->session->set_flashdata('category_create', 'Category has been created');
            redirect('categories');
        }
    }
    public function posts($id)
    {
        $data['title'] = $this->category_model->get_category($id)->name;
        $data['posts'] = $this->post_model->get_posts_by_category($id);

        // $category = $this->category_model->get_category($id)->name;

        // $data['cat_title'] = "Posts in category: ". $category ;
        $this->load->view('templates/header', $data);
        $this->load->view('posts/index', $data);
        $this->load->view('templates/footer', $data);
    }
    
}

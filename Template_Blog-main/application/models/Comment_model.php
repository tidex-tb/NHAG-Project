<?php
    class Comment_model extends CI_Model{
        public function __construct(){
            $this->load->database();
        }
    
        public function create_comment($post_id, $name, $email, $body){
            $data = array(
                'post_id' => $post_id, 
                'name' => $name,
                'email' => $email,
                'body' => $body
            );
    
            return $this->db->insert('comments', $data);
        }
    }
    
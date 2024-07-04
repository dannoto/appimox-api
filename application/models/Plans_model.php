<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Plans_model extends CI_Model
{

    /**
     * CONSTRUCTOR | LOAD DB
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_plans()
    {
     
        $this->db->where('is_deleted', 0);
        return $this->db->get('user_plans')->result();
    }
    public function get_terms()
    {
     
        return $this->db->get('terms')->row();
    }

    public function get_categorias() {
        $this->db->where('is_deleted', 0);
        return $this->db->get('support_categories')->result();
    }

}
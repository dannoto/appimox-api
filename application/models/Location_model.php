<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Location_model extends CI_Model
{

    /**
     * CONSTRUCTOR | LOAD DB
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
   
  

    public function get_locations() {
        $this->db->where('is_deleted', 0);
       return $this->db->get('propertys_location')->result();
      
    }
   
}

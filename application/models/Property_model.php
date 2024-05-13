<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Property_model extends CI_Model
{

    /**
     * CONSTRUCTOR | LOAD DB
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_property($property_id)
    {
        $this->db->where('id', $property_id);
        $this->db->where('is_deleted', 0);
        return $this->db->get('propertys')->row();
    }
    public function get_property_by_location_id($location_id)
    {
        $this->db->where('id', $location_id);
        $this->db->where('is_deleted', 0);
        $data =  $this->db->get('propertys_location')->row();
        return $data['property_id'];
    }
}

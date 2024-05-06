<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Broker_model extends CI_Model
{

    /**
     * CONSTRUCTOR | LOAD DB
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
   
    public function get_broker_propertys($user_id)
    {
        $this->db->where('property_user_id', $user_id);
        $this->db->where('is_deleted', 0);
        $this->db->order_by('id', 'desc');
        return $this->db->get('propertys')->result();
    }

    public function get_broker_property($property_id)
    {
        $this->db->where('id', $property_id);
        // $this->db->where('is_deleted', 0);
   
        return $this->db->get('propertys')->row();
    }

    public function search_broker_propertys($user_id, $query)
    {
        $this->db->where('property_user_id', $user_id);
        $this->db->like('property_title', $query);
        $this->db->order_by('id', 'desc');
        $this->db->where('is_deleted', 0);
        return $this->db->get('propertys')->result();
    }

    public function add_broker_property($property_data)
    {
        $data = $this->db->insert('propertys', $property_data);
        return $this->db->insert_id();
    }

    public function add_broker_property_location($data) {
        $data =  $this->db->insert('propertys_location', $data);
        return $this->db->insert_id();

    }

    public function get_broker_property_location($property_id) {
        $this->db->where('property_id', $property_id);
        $this->db->where('is_deleted', 0);
        return $this->db->get('propertys_location')->row();

    }

    public function delete_broker_property_location($property_location_id) {

        $this->db->where('id', $property_location_id);

        $data = array(
            'is_deleted' => 1
        );

        return $this->db->update('propertys_location', $data);
    }
 
    public function update_broker_property($property_id, $update_data)
    {
        $this->db->where('id', $property_id);
        return $this->db->update('propertys', $update_data);
      
    }

    public function add_broker_property_images($data) {
        return $this->db->insert('propertys_images', $data);
    }

    public function get_broker_property_images($property_id) {
      
        $this->db->where('property_id', $property_id);
        $this->db->where('is_deleted', 0);
        return $this->db->get('propertys_images')->result();
    }

    public function check_edit_property_data($property_user_id, $property_id) {

        $this->db->where('property_user_id', $property_user_id);
        $this->db->where('id', $property_id);
        $this->db->where('is_deleted', 0);

        return $this->db->get('propertys')->row();
    }

    public function delete_broker_property($property_user_id, $property_id) {

        $this->db->where('id', $property_id);
        $this->db->where('property_user_id', $property_user_id);

        $data = array(
            'is_deleted' => 1
        );

        return $this->db->update('propertys', $data);

    }
}

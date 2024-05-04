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
    /**
     * send email with recovery link
     * CONSTRUCTOR | LOAD DB
     */
    public function get_broker_propertys($user_id)
    {

        $this->db->where('property_user_id', $user_id);
        $this->db->where('is_deleted', 0);
        $this->db->order_by('id', 'desc');
        return $this->db->get('propertys')->result();
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

    public function update_broker_property($property_id, $update_data)
    {
        $this->db->where('id', $property_id);

        return $this->db->update('propertys', $update_data);
      
    }
}

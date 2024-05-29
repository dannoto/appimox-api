<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Partner_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function add_partner($data)
    {
        $this->db->insert('user_partners', $data);
        return $this->db->insert_id();
    }

    public function get_partner_associated($partner_id) {
        
        $this->db->where('partner_id', $partner_id);
        $this->db->where('is_deleted', 0);
        return $this->db->get('user_partners_propertys')->result();
    }

    
    public function get_partner($partner_id)
    {
        $this->db->where('id', $partner_id);
        return $this->db->get('user_partners')->row();
    }


    public function update_partner($partner_id, $data)
    {
        $this->db->where('id', $partner_id);
        return $this->db->update('user_partners', $data);
    }

    public function add_partner_property($data)
    {
        return $this->db->insert('user_partners_propertys', $data);
    }

    public function add_partner_action($data)
    {
        $this->db->insert('user_partners_actions', $data);
        return $this->db->insert_id();
    }
    
}

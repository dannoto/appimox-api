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

    public function get_partners_property($partner_id) {
        $this->db->where('partner_id', $partner_id);
        $this->db->where('is_deleted', 0);
        return $this->db->get('user_partners_propertys')->result();

    }

    public function check_exist_partner($property_id, $user_id) {
        // Passo 1: Buscar todos os registros em user_partners_propertys com o partner_property_id
        $this->db->select('partner_id');
        $this->db->where('partner_property_id', $property_id);
        $query = $this->db->get('user_partners_propertys');
        $partner_ids = array();
        
        foreach ($query->result() as $row) {
            $partner_ids[] = $row->partner_id;
        }
        
        if (empty($partner_ids)) {
            return null; // Nenhum partner_id encontrado
        }
        
        // Passo 2: Filtrar os partner_ids em user_partners onde partner_status = 2 e is_deleted = 0
        $this->db->select('id');
        $this->db->where_in('id', $partner_ids);
        $this->db->where('partner_status', 2);
        $this->db->where('is_deleted', 0);
        $query = $this->db->get('user_partners');
        $valid_partner_ids = array();
        
        foreach ($query->result() as $row) {
            $valid_partner_ids[] = $row->id;
        }
        
        if (empty($valid_partner_ids)) {
            return null; // Nenhum partner_id válido encontrado
        }
        
        // Passo 3: Verificar se o user_id existe em partner_property_owner ou partner_property_broker
        $this->db->group_start();
        $this->db->where_in('id', $valid_partner_ids);
        $this->db->where('partner_property_owner', $user_id);
        $this->db->or_where('partner_property_broker', $user_id);
        $this->db->group_end();
        
        return $this->db->get('user_partners')->row();
    }
    
    // public function check_exist_partner($property_id, $user_id) {

    //     $this->db->where('id', $user_id);
    //     $this->db->where('partner_property_owner', $user_id);
    //     $this->db->or_where('partner_property_broker', $user_id);

    //     $this->db->where('is_deleted', 0);
    //     $this->db->where('partner_status', 2);

    //     return $this->db->get('user_partners')->row();
    // }

    public function get_partner_associated($partner_id)
    {

        $this->db->where('partner_id', $partner_id);
        $this->db->where('is_deleted', 0);
        return $this->db->get('user_partners_propertys')->result();
    }

    public function get_partners_by_broker($broker_id) {

        $this->db->where('partner_property_broker', $broker_id);
        $this->db->where('is_deleted', 0);
        $this->db->where('partner_status', 2);
        return $this->db->get('user_partners')->result();

    }

    public function get_partners_by_property($property_id) {
        // get brokers tha have active partner on the property
        $this->db->select('up.partner_property_broker');
        $this->db->from('user_partners_propertys upp');
        $this->db->join('user_partners up', 'upp.partner_id = up.id', 'inner');
        $this->db->where('upp.partner_property_id', $property_id);
        $this->db->where('upp.is_deleted', 0);
        $this->db->where('up.partner_status', 2);
        $this->db->where('up.is_deleted', 0);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $result = $query->result();
            // Process the result as needed
            return $result;
            // foreach ($result as $row) {
            //     echo 'Broker ID: ' . $row['partner_property_broker'] . '<br>';
            // }
        } else {

            return false;
            echo 'No active partnerships found for the given property.';
        }
        

    }

    public function check_able_to_rating($partner_id)
    {
        $this->db->where('id', $partner_id);
        // $this->db->where('partner_expiration !=', null);
        return $this->db->get('user_partners')->row();
    }

    public function check_partner_action_restart_pending($partner_id) {
        $this->db->where('partner_id', $partner_id);
        // $this->db->where('partner_action_type', 3);
        $this->db->where('partner_status', 0);

        return $this->db->get('user_partners_actions')->row();
    }

    public function get_property($property_id)
    {
        $this->db->where('id', $property_id);
        $this->db->where('is_deleted', 0);
        return $this->db->get('propertys')->row();
    }


    public function get_partner_action($action_id)
    {
        $this->db->where('id', $action_id);
        return $this->db->get('user_partners_actions')->row();
    }


    public function get_partner_actions($partner_id)
    {

        $this->db->where('partner_id', $partner_id);
        $this->db->where('is_deleted', 0);
        return $this->db->get('user_partners_actions')->result();
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

    public function update_partner_action($action_id, $data)
    {
        $this->db->where('id', $action_id);
        return $this->db->update('user_partners_actions', $data);
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


    public function get_partners_by_user($user_id)
    {
        $this->db->select('*');
        $this->db->from('user_partners');
        $this->db->where('partner_publish', 1);
        $this->db->where('is_deleted', 0);
        $this->db->group_start();
        $this->db->where('partner_property_owner', $user_id);
        $this->db->or_where('partner_property_broker', $user_id);
        $this->db->group_end();
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();

        return $query->result_array();
    }
}

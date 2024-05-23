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

    public function get_property_filter($property_id, $f_data)
    {
        $this->db->where('id', $property_id);
        $this->db->where('is_deleted', 0);

        if (strlen($f_data['filter_type']) > 0) {
            $this->db->where('property_type', $f_data['filter_type']);
        }

        if (strlen($f_data['filter_type_offer']) > 0) {
            $this->db->where('property_type_offer', $f_data['filter_type_offer']);
        }

        if (strlen($f_data['filter_room']) > 0) {
            $this->db->where('property_room', $f_data['filter_room']);
        }

        if (strlen($f_data['filter_bathroom']) > 0) {
            $this->db->where('property_bathroom', $f_data['filter_bathroom']);
        }

        if (strlen($f_data['filter_places']) > 0) {
            $this->db->where('property_places', $f_data['filter_places']);
        }

        // Filtrando Preço

        if (strlen($f_data['filter_price_min']) > 0 && strlen($f_data['filter_price_max']) == 0) {
            // Somente preço mínimo foi definido.
            $this->db->where('property_price >=', $f_data['filter_price_min']);
        } else if (strlen($f_data['filter_price_min']) == 0 && strlen($f_data['filter_price_max']) > 0) {
            // Somente preço máximo foi definido.
            $this->db->where('property_price <=', $f_data['filter_price_max']);
        } else if (strlen($f_data['filter_price_min']) > 0 && strlen($f_data['filter_price_max']) > 0) {
            // Preço mínimo e preço máximo definidos.
            $this->db->where('property_price >=', $f_data['filter_price_min']);
            $this->db->where('property_price <=', $f_data['filter_price_max']);
        }
        // Filtrando Preço

        if (strlen($f_data['filter_function']) > 0) {
            $this->db->where('property_function', $f_data['filter_function']);
        }

        if (strlen($f_data['filter_disponibility']) > 0) {
            $this->db->where('property_disponibility', $f_data['filter_disponibility']);
        }

        return $this->db->get('propertys')->row();
    }

    public function get_property_by_location_id($location_id)
    {
        $this->db->where('id', $location_id);
        $this->db->where('is_deleted', 0);
        $data =  $this->db->get('propertys_location')->row();
        return $data->property_id;
    }

    // =======

    public function get_broker_by_location_id($location_id)
    {
        $this->db->where('id', $location_id);
        $this->db->where('is_deleted', 0);
        $data =  $this->db->get('propertys_location')->row();
        return $data->property_broker;
    }

    public function get_broker($broker_id)
    {
        $this->db->where('id', $broker_id);
        $this->db->where('user_type', 'broker');
        $this->db->where('user_verified_creci', 1);
        $this->db->where('user_verified_preferences', 1);
        $this->db->where('user_status', 0);

        return $this->db->get('users')->row();

        // $this->db->select('users.*, user_preferences.*');
        // $this->db->from('users');
        // $this->db->where('users.id', $broker_id);
        // $this->db->where('users.user_type', 'broker');
        // $this->db->where('users.user_verified_creci', 1);
        // $this->db->where('users.user_verified_preferences', 1);
        // $this->db->where('users.user_status', 0);
        // $this->db->join('user_preferences', 'users.id = user_preferences.user_id', 'left');

        // $query = $this->db->get();
        // $result = $query->result();

        // // Agrupar os resultados por ID de usuário
        // $grouped_result = [];
        // foreach ($result as $row) {
        //     $user_id = $row->id;
        //     if (!isset($grouped_result[$user_id])) {
        //         $grouped_result[$user_id] = (object)[
        //             'user_data' => [],
        //             'user_preferences' => []
        //         ];
        //     }
        //     $grouped_result[$user_id]->user_data = $row;
        //     unset($row->id, $row->user_id); // Remover redundâncias
        //     $grouped_result[$user_id]->user_preferences[] = $row;
        // }

        // return $grouped_result;
    }

    // filtra se os corretores encontrados possuem a prefeences
    public function filter_broker_by_preferences($broker_id, $f_data)
    {
        $this->db->where('id', $broker_id);
        $this->db->where('user_type', 'broker');
        $this->db->where('user_verified_creci', 1);
        $this->db->where('user_verified_preferences', 1);
        $this->db->where('user_status', 0);

        return $this->db->get('users')->row();
    }

    // busca propriedades associadas.
    public function get_property_by_associate_broker_id($location_id, $broker_id)
    {
        $this->db->where('id', $location_id);
        $this->db->where('property_broker', $broker_id);
        $this->db->where('is_deleted', 0);
        $data =  $this->db->get('propertys_location')->row();

        if ($data) {
            return $data->property_id;
        } else {
            return false;
        }
    }



    //  suggest imoveis
    public function suggest_property_by_estado($property_estado)
    {

        $this->db->where('property_estado', $property_estado);
        $this->db->where('is_deleted', 0);
        $this->db->order_by('id', 'rand');
        $this->db->limit(20);
        return $this->db->get('propertys')->result();
    }

    public function suggest_property_by_cidade($property_cidade)
    {
        $this->db->where('property_cidade', $property_cidade);
        $this->db->where('is_deleted', 0);
        $this->db->order_by('id', 'rand');
        $this->db->limit(20);
        return $this->db->get('propertys')->result();
    }



    public function get_cidade_label($cidade_id) {
        $this->db->where('id', $cidade_id);
        $data =  $this->db->get('db_cidades')->row();
        return $data->nome;
    }

    public function get_estado_label($estado_id) {
        $this->db->where('id', $estado_id);
        $data =  $this->db->get('db_estados')->row();
        return $data->uf;
    }
}

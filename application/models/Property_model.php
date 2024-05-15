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
            $this->db->where('property_room >=', $f_data['filter_room']);
        }

        if (strlen($f_data['filter_bathroom']) > 0) {
            $this->db->where('property_bathroom >=', $f_data['filter_bathroom']);
        }

        if (strlen($f_data['filter_places']) > 0) {
            $this->db->where('property_places >=', $f_data['filter_places']);
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
}

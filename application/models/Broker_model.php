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

    public function get_broker_propertys($user_id, $limit = null)
    {
        $this->db->where('property_user_id', $user_id);
        $this->db->where('is_deleted', 0);
        $this->db->order_by('id', 'desc');

        if ($limit != null) {
            $this->db->limit($limit);
        }

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

    public function add_broker_property_location($data)
    {
        $data =  $this->db->insert('propertys_location', $data);
        return $this->db->insert_id();
    }
    public function update_broker_property_location($property_location_id, $data)
    {
        $this->db->where('id', $property_location_id);
        return $this->db->update('propertys_location', $data);
    }

    public function get_broker_property_location($property_id)
    {
        $this->db->where('property_id', $property_id);
        $this->db->where('is_deleted', 0);
        return $this->db->get('propertys_location')->row();
    }

    public function delete_broker_property_location($property_location_id)
    {

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

    public function delete_property_image($property_id, $property_user_id, $imagem_url) {
        
        $this->db->where('property_id', $property_id);
        $this->db->where('property_user_id', $property_user_id);
        $this->db->where('property_image', $imagem_url);

        $data = array(
            'is_deleted' => 1
        );

        return $this->db->update('propertys_images', $data);
    }

    public function add_broker_property_images($data)
    {
        return $this->db->insert('propertys_images', $data);
    }

    public function get_broker_property_images($property_id)
    {

        $this->db->where('property_id', $property_id);
        $this->db->where('is_deleted', 0);
        return $this->db->get('propertys_images')->result();
    }

    public function check_edit_property_data($property_user_id, $property_id)
    {

        $this->db->where('property_user_id', $property_user_id);
        $this->db->where('id', $property_id);
        $this->db->where('is_deleted', 0);

        return $this->db->get('propertys')->row();
    }

    public function delete_broker_property($property_user_id, $property_id)
    {

        $this->db->where('id', $property_id);
        $this->db->where('property_user_id', $property_user_id);

        $data = array(
            'is_deleted' => 1
        );

        return $this->db->update('propertys', $data);
    }

    public function search_broker_propertys_home($user_id, $filter)
    {

        $this->db->order_by('id', 'desc');

        $this->db->where('property_user_id', $user_id);
        $this->db->where('property_type_offer', $filter);
        $this->db->where('is_deleted', 0);

        return $this->db->get('propertys')->result();
    }

    public function check_creci_pb($creci, $cpf)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://www.crecipb.conselho.net.br/form_pesquisa_cadastro_geral_site.php',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('inscricao' => $creci, 'cpf' => $cpf),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $data = json_decode($response);
        return $data;
    }

    public function check_creci_pe($creci, $cpf)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://www.crecipe.conselho.net.br/form_pesquisa_cadastro_geral_site.php',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('inscricao' => $creci, 'cpf' => $cpf),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $data = json_decode($response);
        return $data;
    }

    public function check_creci_rn($creci, $cpf)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://www.crecirn.conselho.net.br/form_pesquisa_cadastro_geral_site.php',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('inscricao' => $creci, 'cpf' => $cpf),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $data = json_decode($response);
        return $data;
    }



     //  suggest imoveis
     public function suggest_broker_by_estado($broker_state)
     {

         $this->db->where('user_state', $broker_state);
         $this->db->where('user_type', 'broker');
         $this->db->where('user_status', 0);
         $this->db->order_by('id', 'rand');
         $this->db->limit(20);
         return $this->db->get('users')->result();
     }
 
     public function suggest_broker_by_cidade($broker_city)
     {
        $this->db->where('user_city', $broker_city);
        $this->db->where('user_type', 'broker');
        $this->db->where('user_status', 0);
        $this->db->order_by('id', 'rand');
        $this->db->limit(20);
        return $this->db->get('users')->result();
     }


     public function default_suggest_broker()
     {
        $this->db->where('user_type', 'broker');
        $this->db->where('user_status', 0);
        $this->db->order_by('id', 'rand');
        $this->db->limit(20);
        return $this->db->get('users')->result();
     }
}

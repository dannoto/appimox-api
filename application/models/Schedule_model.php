<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Schedule_model extends CI_Model
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
    public function check_schedule($client_id, $broker_id, $property_id, $schedule_date)
    {

        $this->db->where('schedule_client', $client_id);
        $this->db->where('schedule_broker', $broker_id);
        // $this->db->where('schedule_property', $property_id);
        $this->db->where('schedule_date', $schedule_date);

        $this->db->where('schedule_status', 0);
        $this->db->where('is_deleted', 0);

        return $this->db->get('user_schedules')->row();
    }

    // Verifica se a existe um agendamento em aberto entre corretor, cliente e imovel
    public function check_schedule_duplicated($client_id, $broker_id, $property_id)
    {

        $this->db->where('schedule_client', $client_id);
        $this->db->where('schedule_broker', $broker_id);
        $this->db->where('schedule_property', $property_id);

        $this->db->where('schedule_status', 0);
        $this->db->where('is_deleted', 0);

        return $this->db->get('user_schedules')->row();
    }

    public function add_schedule($data)
    {
        return $this->db->insert('user_schedules', $data);
    }

    public function delete_broker_schedule($schedule_id) {
        $this->db->where('id',  $schedule_id);
        
        $data = array(
            'schedule_broker_delete' => 1,
        );

        return $this->db->update('user_schedules', $data);
    }

    public function delete_client_schedule($schedule_id) {
        $this->db->where('id',  $schedule_id);
        
        $data = array(
            'schedule_client_delete' => 1,
        );
        
        return $this->db->update('user_schedules', $data);
    }

    public function add_schedule_action($schedule_data)
    {

        // 0 Agendamento foi Criado pelo Cliente
        // 1 Cancelado foi pelo cliente
        // 2 Cancelado foi pelo corretor
        // 3 Agendamento foi Finalizado
        // 4 Agendamento foi Avaliado pelo Cliente
        // 5 Horário do Agendamento Alterado pelo Corretor
        // 6 Horário do Agendamento Alterado pelo Cliene


        return $this->db->insert('user_schedules_action', $schedule_data);
    }

    // public function search_broker_schedules()
    // {
    //     return $this->db->insert('user_schedules', $data);
    // }

    public function update_broker_schedule($schedule_id, $schedule_data)
    {

        $this->db->where('id', $schedule_id);
        return $this->db->update('user_schedules', $schedule_data);
    }


    public function get_broker_schedules($broker_id, $user_type = null)
    {
        $this->db->where('schedule_broker', $broker_id);
        $this->db->where('is_deleted', 0);

        if ($user_type != null) {

            if ($user_type == "broker") {
                $this->db->where('schedule_broker_delete', 0);

            } else if ($user_type == "client") {
                $this->db->where('schedule_client_delete', 0);

            }

        }
        
        $this->db->order_by('id', 'desc');

        return $this->db->get('user_schedules')->result();
    }

    public function search_broker_schedules($broker_id, $query = null)
    {
        // Adiciona condição para verificar se o agendamento é do corretor especificado
        $this->db->where('user_schedules.schedule_broker', $broker_id);
        $this->db->where('user_schedules.is_deleted', 0);
    
        // Se uma consulta de pesquisa for fornecida, adiciona uma junção com a tabela 'users' onde está o cliente
        if (!empty($query)) {
            $this->db->join('users', 'user_schedules.schedule_client = users.id');
            $this->db->like('users.user_name', $query);
        }
    
        $this->db->order_by('user_schedules.id', 'desc');
    
        return $this->db->get('user_schedules')->result();
    }


    public function filter_broker_schedules($broker_id, $schedule_status)
    {
        // Adiciona condição para verificar se o agendamento é do corretor especificado
        $this->db->where('user_schedules.schedule_broker', $broker_id);
        $this->db->where('user_schedules.schedule_status', $schedule_status);
        $this->db->where('user_schedules.is_deleted', 0);
        $this->db->order_by('user_schedules.id', 'desc');
    
        return $this->db->get('user_schedules')->result();
    }
    
    public function get_broker_schedules_filter($broker_id, $schedule_status)
    {
        $this->db->where('schedule_broker', $broker_id);
        $this->db->where('schedule_status', $schedule_status);

        $this->db->where('is_deleted', 0);
        return $this->db->get('user_schedules')->result();
    }

    // ---------------------------------------------

  
    public function get_client_schedules_filter($client_id, $schedule_status)
    {
        $this->db->where('schedule_client', $client_id);
        $this->db->where('schedule_status', $schedule_status);

        $this->db->where('is_deleted', 0);
        return $this->db->get('user_schedules')->result();
    }

    public function get_schedule($schedule_id)
    {
        $this->db->where('id', $schedule_id);
        return $this->db->get('user_schedules')->row();
    }

    // ================ SCHEDULE CLIENTE PART ===================

    public function get_client_schedules($client_id)
    {
        $this->db->where('schedule_client', $client_id);
        $this->db->where('is_deleted', 0);
        $this->db->order_by('id', 'desc');

        return $this->db->get('user_schedules')->result();
    }

    public function search_client_schedules($client_id, $query = null)
    {
        // Adiciona condição para verificar se o agendamento é do corretor especificado
        $this->db->where('user_schedules.schedule_client', $client_id);
        $this->db->where('user_schedules.is_deleted', 0);
    
        // Se uma consulta de pesquisa for fornecida, adiciona uma junção com a tabela 'users' onde está o cliente
        if (!empty($query)) {
            $this->db->join('users', 'user_schedules.schedule_broker = users.id');
            $this->db->like('users.user_name', $query);
        }
    
        $this->db->order_by('user_schedules.id', 'desc');
    
        return $this->db->get('user_schedules')->result();
    }

    public function update_client_schedule($schedule_id, $schedule_data)
    {

        $this->db->where('id', $schedule_id);
        return $this->db->update('user_schedules', $schedule_data);
    }


    public function filter_client_schedules($client_id, $schedule_status)
    {
        // Adiciona condição para verificar se o agendamento é do corretor especificado
        $this->db->where('user_schedules.schedule_client', $client_id);
        $this->db->where('user_schedules.schedule_status', $schedule_status);
        $this->db->where('user_schedules.is_deleted', 0);
        $this->db->order_by('user_schedules.id', 'desc');
    
        return $this->db->get('user_schedules')->result();
    }


    // =====

    public function get_restrict_schedule($user_id) {
        $this->db->where('user_id', $user_id);
        $this->db->where('is_deleted', 0);

        return $this->db->get('user_schedules_restrict')->result();
    }

    public function check_restrict_schedule($user_id, $schedule_data) {

        $this->db->where('user_id', $user_id);
        $this->db->where('schedule_data', $schedule_data);
        $this->db->where('is_deleted', 0);

        return $this->db->get('user_schedules_restrict')->result();
    }

    public function add_restrict_schedule($data) {

        return $this->db->insert('user_schedules_restrict', $data);
    }

    public function delete_restrict_schedule($schedule_id) {

        $this->db->where('id', $schedule_id);
        $this->db->where('id', 'desc');

        $data = array(
            'is_deleted' => 1
        );

        return $this->db->update('user_schedules_restrict', $data);
    }
}

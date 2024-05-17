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
    public function check_schedule($client_id, $broker_id, $property_id, $schedule_date, $schedule_time)
    {

        $this->db->where('schedule_client', $client_id);
        $this->db->where('schedule_broker', $broker_id);
        $this->db->where('schedule_property', $property_id);
        $this->db->where('schedule_date', $schedule_date);
        $this->db->where('schedule_time', $schedule_time);

        $this->db->where('schedule_status', 0);
        $this->db->where('is_deleted', 0);

        return $this->db->get('user_schedules')->row();
    }

    public function add_schedule($data)
    {
        return $this->db->insert('user_schedules', $data);
    }

    public function add_schedule_action($schedule_data)
    {
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


    public function get_broker_schedules($broker_id)
    {
        $this->db->where('schedule_broker', $broker_id);
        $this->db->where('is_deleted', 0);
        $this->db->order_by('id', 'desc');

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

    public function update_client_schedule($schedule_id, $schedule_data)
    {

        $this->db->where('id', $schedule_id);
        return $this->db->update('user_schedules', $schedule_data);
    }

    // public function search_client_schedules()
    // {
    // }

    public function get_client_schedules($client_id)
    {
        $this->db->where('schedule_client', $client_id);
        $this->db->where('is_deleted', 0);
        $this->db->order_by('id', 'desc');

        return $this->db->get('user_schedules')->result();
    }

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
}

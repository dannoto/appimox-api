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

    public function add_schedule($data) {
        return $this->db->insert('user_schedules', $data); 
    }
}

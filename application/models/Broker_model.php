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
    public function get_broker_propertys($user_id) {
	
		$this->db->where('property_user_id', $user_id);
        $this->db->where('is_deleted', 0);
		return $this->db->get('propertys')->result();
		
	}
}

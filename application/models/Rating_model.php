<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Rating_model extends CI_Model
{

    /**
     * CONSTRUCTOR | LOAD DB
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function update_user_rating($user_id, $rating_note)
    {

        $this->db->where('id', $user_id);

        $data = array(
            'user_rating' => $rating_note
        );

        return $this->db->update('users', $data);
    }

    public function check_rating($rating_schedule_id,  $rating_property_id,  $rating_owner_id, $rating_rated_id)
    {

        $this->db->where('rating_schedule_id', $rating_schedule_id);
        $this->db->where('rating_property_id', $rating_property_id);
        $this->db->where('rating_owner_id', $rating_owner_id);
        $this->db->where('rating_rated_id', $rating_rated_id);
        $this->db->where('is_deleted', 0);

        return $this->db->get('user_ratings')->row();
    }


    public function add_rating($data)
    {
        $this->db->insert('user_ratings', $data);
        return $this->db->insert_id();
    }

    public function get_broker_ratings($user_id)
    {

        $this->db->where('rating_rated_id', $user_id);
        $this->db->where('is_deleted', 0);
        $this->db->order_by('id', 'desc');

        return $this->db->get('user_ratings')->result();
    }
}

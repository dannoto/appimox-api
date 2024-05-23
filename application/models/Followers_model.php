<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Followers_model extends CI_Model
{

    /**
     * CONSTRUCTOR | LOAD DB
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function check_follower($f_following, $f_followed) {

        $this->db->where('f_following', $f_following);
        $this->db->where('f_followed', $f_followed);
        $this->db->where('is_deleted', 0);

       return $this->db->get('users_followers')->row();

    }
   
    public function to_follow($data) {
        return $this->db->insert('users_followers', $data);
    }

    public function to_unfollow($f_following, $f_followed) {

        $this->db->where('f_following', $f_following);
        $this->db->where('f_followed', $f_followed);
        $this->db->where('is_deleted', 0);

        $data = array(
            'is_deleted' => 1
        );

        return $this->db->update('users_followers', $data);
    }

    public function get_followers($user_id) {

        $this->db->where('f_followed', $user_id);
        $this->db->where('is_deleted', 0);
        return $this->db->get('users_followers')->result();
    }

    public function search_followers($user_id) {

        $this->db->where('users_followers.f_followed', $user_id);
        $this->db->where('users_followers.is_deleted', 0);
    
        // Se uma consulta de pesquisa for fornecida, adiciona uma junção com a tabela 'users' onde está o cliente
        if (!empty($query)) {
            $this->db->join('users', 'users_followers.f_following = users.id');
            $this->db->like('users.user_name', $query);
        }
    
        $this->db->order_by('users_followers.id', 'desc');
    
        return $this->db->get('users_followers')->result();
    }

    public function get_following($user_id, $limit = null) {

        $this->db->where('f_following', $user_id);
        $this->db->where('is_deleted', 0);

        if ($limit != null) {
            $this->db->limit($limit);
        }

        return $this->db->get('users_followers')->result();
    }

    public function search_following($user_id, $query) {

        $this->db->where('users_followers.f_following', $user_id);
        $this->db->where('users_followers.is_deleted', 0);
    
        // Se uma consulta de pesquisa for fornecida, adiciona uma junção com a tabela 'users' onde está o cliente
        if (!empty($query)) {
            $this->db->join('users', 'users_followers.f_followed = users.id');
            $this->db->like('users.user_name', $query);
        }
    
        $this->db->order_by('users_followers.id', 'desc');
    
        return $this->db->get('users_followers')->result();
    }
   
}

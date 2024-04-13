<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class User_model extends CI_Model {

	
	public function __construct() {
		
		parent::__construct();
		$this->load->database();
		
	}
	
	public function add_user($user_name, $user_email, $user_password, $user_auth_type) {
		
		$data = array(
			'user_name'   => $user_name,
			'user_email'      => $user_email,
			'user_password'   => $this->hash_password($user_password),
			'user_register' => date('Y-m-j H:i:s'),
			'user_auth_type' => $user_auth_type

		);
		
		$this->db->insert('users', $data);
		return $this->db->insert_id(); 
		
	}
	
	public function auth($user_email, $user_password) {
		
		$this->db->select('user_password');
		$this->db->from('users');
		$this->db->where('user_email', $user_email);
		$hash = $this->db->get()->row('user_password');
		
		return $this->verify_password_hash($user_password, $hash);
		
	}
	
	public function get_user_id_from_email($user_email) {
		
		$this->db->select('id');
		$this->db->from('users');
		$this->db->where('user_email', $user_email);

		return $this->db->get()->row('id');
		
	}
	
	public function get_user($user_id) {
		
		$this->db->from('users');
		$this->db->where('id', $user_id);
		return $this->db->get()->row();
		
	}
	
	private function hash_password($password) {
		
		return password_hash($password, PASSWORD_BCRYPT);
		
	}
	
	private function verify_password_hash($password, $hash) {
		
		return password_verify($password, $hash);
		
	}
	
	public function get_db_preferences() {

		$this->db->where('is_deleted', 0);
		return $this->db->get('db_preferences')->result();

	}
}

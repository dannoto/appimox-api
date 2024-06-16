<?php
defined('BASEPATH') or exit('No direct script access allowed');


class User_model extends CI_Model
{


	public function __construct()
	{

		parent::__construct();
		$this->load->database();
	}


	public function add_user($user_name, $user_email, $user_password, $user_auth_type)
	{

		$data = array(
			'user_name'   => $user_name,
			'user_email'      => $user_email,
			'user_password'   => $this->hash_password($user_password),
			'user_register' => date('Y-m-j H:i:s'),
			'user_auth_type' => $user_auth_type,
			'user_image' => 'public/images/users/default.png',
			'user_verified_creci' => 0,
			'user_verified_email' => 0,
			'user_verified_preferences' => 0,
			'user_rating' => 0,
			'user_status' => 0,

		);

		$this->db->insert('users', $data);
		return $this->db->insert_id();
	}



	public function update_user_type($user_id, $user_type)
	{
		$this->db->where('id', $user_id);

		$data = array(
			'user_type' => $user_type
		);
		
		return $this->db->update('users', $data);
	}
	public function update_user($user_id, $data)
	{
		$this->db->where('id', $user_id);
		return $this->db->update('users', $data);
	}

	public function auth($user_email, $user_password)
	{

		$this->db->select('user_password');
		$this->db->from('users');
		$this->db->where('user_email', $user_email);
		$hash = $this->db->get()->row('user_password');

		return $this->verify_password_hash($user_password, $hash);
	}

	public function get_user_id_from_email($user_email)
	{

		$this->db->select('id');
		$this->db->from('users');
		$this->db->where('user_email', $user_email);

		return $this->db->get()->row('id');
	}

	// Init

	public function check_init_preferences($user_id)
	{
		$this->db->from('users');
		$this->db->where('id', $user_id);
		return $this->db->get()->row();
	}

	public function check_init_creci($user_id)
	{
		$this->db->from('users');
		$this->db->where('id', $user_id);
		return $this->db->get()->row();
	}

	public function check_creci_is_unique($user_creci, $user_cpf) {
		$this->db->where('user_creci', $user_creci);
		$this->db->where('user_cpf', $user_cpf);
		$this->db->where('user_verified_creci', 1);

		return $this->db->get('users')->row();
	}

	// Init

	public function get_user($user_id)
	{

		$this->db->from('users');
		$this->db->where('id', $user_id);
		return $this->db->get()->row();
	}

	private function hash_password($password)
	{

		return password_hash($password, PASSWORD_BCRYPT);
	}

	private function verify_password_hash($password, $hash)
	{

		return password_verify($password, $hash);
	}

	// ====================================

	// Preferences

	public function get_user_preferences($user_id) {

		$this->db->where('user_id', $user_id);
		return $this->db->get('user_preferences')->result();

	}

	public function get_db_preferences()
	{

		$this->db->where('is_deleted', 0);
		return $this->db->get('db_preferences')->result();
	}

	public function reset_user_preferences($user_id)
	{
		$this->db->where('user_id', $user_id);
		return $this->db->delete('user_preferences');
	}

	public function add_user_preferences($preference_id, $user_id, $user_type)
	{

		$data  = array(
			'user_id' => $user_id,
			'user_type' => $user_type,
			'preference_id' => $preference_id
		);

		return $this->db->insert('user_preferences', $data);
	}

	public function reset_user_preferences_items($user_id)
	{

		$this->db->where('user_id', $user_id);

		$data  = array(
			'pontualidade' => 0,
			'flexibilidade' => 0,
			'honestidade' => 0,
			'conhecimento' => 0,
			'comunicacao' => 0,
			'experiencia' => 0,
			'profissionalismo' => 0,
			'cordialidade' => 0,
			'atendimento' => 0,
		);

		return $this->db->update('user_preferences', $data);
	}

	public function add_user_preferences_item($p, $user_id)
	{

		$this->db->where('user_id', $user_id);

		$data  = array(
			'' . $p . '' => 1,
		);

		return $this->db->update('user_preferences', $data);
	}

	// Preferences

	// ====================================

	// Property

	public function get_broker_propertys($broker_id)
	{
	}

	public function add_property($property_id, $property_data)
	{
	}

	public function update_property($property_id, $property_data)
	{
	}

	public function delete_property($property_id)
	{
	}
	// Property


	// favorits

	public function get_favorits($user_id)
	{
		// Seleciona todas as propriedades favoritas do usuário com base no ID do usuário
		$this->db->select('*');
		$this->db->from('user_favorits');
		$this->db->join('propertys', 'user_favorits.favorit_property_id = propertys.id');
		$this->db->where('user_favorits.favorit_user_id', $user_id);
		$this->db->where('user_favorits.is_deleted', 0);
		$this->db->order_by('user_favorits.id' , 'desc');

		// Executa a consulta e retorna os resultados
		return $this->db->get()->result();
	}

	public function search_get_favorits($user_id, $query)
	{
		// Seleciona todas as propriedades favoritas do usuário com base no ID do usuário
		$this->db->select('*');
		$this->db->from('user_favorits');
		$this->db->join('propertys', 'user_favorits.favorit_property_id = propertys.id');
		$this->db->where('user_favorits.favorit_user_id', $user_id);
		$this->db->where('user_favorits.is_deleted', 0);
		$this->db->like('propertys.property_title', $query);
		// Executa a consulta e retorna os resultados
		return $this->db->get()->result();
	}



	public function check_favorit($user_id, $property_id)
	{

		$this->db->where('favorit_user_id', $user_id);
		$this->db->where('favorit_property_id', $property_id);


		return $this->db->get('user_favorits')->row();
	}

	public function add_favorit($user_id, $property_id)
	{

		$data = array(
			'favorit_user_id' => $user_id,
			'favorit_property_id' => $property_id,
			'favorit_data' => date('Y-m-d H:i:s'),
			'is_deleted' => 0
		);
		return $this->db->insert('user_favorits', $data);
	}

	public function delete_favorit($user_id, $property_id)
	{

		$this->db->where('favorit_user_id', $user_id);
		$this->db->where('favorit_property_id', $property_id);

		// $data = array(
		// 	'is_deleted' => 1
		// );

		return $this->db->delete('user_favorits');
	}
	// favorits




	// perfil
	public function update_user_profile($user_id, $data)
	{
		$this->db->where('id', $user_id);
		return $this->db->update('users', $data);
	}

	public function check_email($user_email, $user_id)
	{
		$this->db->where('user_email', $user_email);
		$this->db->where('id !=', $user_id);
		return $this->db->get('users')->row();
	}
	// perfil


	// cidades

	public function get_cidades_by_estado($uf) {
		$this->db->where('uf', $uf);
		return $this->db->get('db_cidades')->result();
	}

	public function get_estados() {
		$this->db->where('available', 1);
		return $this->db->get('db_estados')->result();
	}


	public function get_estados_client() {
		// $this->db->where('available', 1);
		return $this->db->get('db_estados')->result();
	}
	// cidades
}



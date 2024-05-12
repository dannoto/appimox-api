<?php
defined('BASEPATH') or exit('No direct script access allowed');

require(APPPATH . '/libraries/REST_Controller.php');

use Restserver\Libraries\REST_Controller;

class User extends REST_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->library('Authorization_Token');
		$this->load->model('user_model');
		$this->load->model('email_model');
	}

	public function register_post()
	{

		// set validation rules
		$this->form_validation->set_rules('user_name', 'Nome do Usuário', 'trim|required|min_length[6]');
		$this->form_validation->set_rules('user_email', 'Email', 'trim|required|valid_email|is_unique[users.user_email]', array('is_unique' => 'Este e-mail já existe. Escolha outro.'));

		$this->form_validation->set_rules('user_password', 'Senha', 'trim|required|min_length[6]');
		$this->form_validation->set_rules('user_password_confirm', 'Confirmação de Senha', 'trim|required|min_length[6]|matches[user_password]');
		$this->form_validation->set_rules('user_auth_type', 'Tipo de Cadastro', 'trim|required');

		// $this->form_validation->set_rules('username', 'Username', 'trim|required|alpha_numeric|min_length[4]|is_unique[users.username]', array('is_unique' => 'This username already exists. Please choose another one.'));
		// $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[users.email]');
		// $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]');
		// $this->form_validation->set_rules('password_confirm', 'Confirm Password', 'trim|required|min_length[6]|matches[password]');

		if ($this->form_validation->run() === false) {


			$final['status'] = false;
			$final['message'] = validation_errors();
			$final['note'] = 'Erro no formulário.';

			$this->response($final, REST_Controller::HTTP_OK);
		} else {

			// set variables from the form
			$user_name = $this->input->post('user_name');
			$user_email    = $this->input->post('user_email');
			$user_password = $this->input->post('user_password');
			$user_auth_type = $this->input->post('user_auth_type');

			if ($res = $this->user_model->add_user($user_name, $user_email, $user_password, $user_auth_type)) {

				// user creation ok
				$token_data['uid'] = $res;
				$token_data['user_name'] = $user_name;
				$tokenData = $this->authorization_token->generateToken($token_data);
				$final = array();
				$final['access_token'] = $tokenData;
				$final['status'] = true;
				$final['uid'] = $res;
				$final['message'] = 'Conta criada com sucesso!';
				$final['note'] = 'Sua conta foi criada com sucesso. Verifique seu e-mail para confirmar sua conta.';

				$this->response($final, REST_Controller::HTTP_OK);
			} else {

				$final['status'] = false;
				$final['message'] = 'Houve um problema ao criar sua conta. Tente novamente.';
				$final['note'] = 'Houve um problema em add_user().';

				// user creation failed, this should never happen
				$this->response($final, REST_Controller::HTTP_OK);
			}
		}
	}

	public function login_post()
	{

		// set validation rules
		$this->form_validation->set_rules('user_email', 'E-mail', 'trim|required|valid_email');
		$this->form_validation->set_rules('user_password', 'Senha', 'trim|required');

		if ($this->form_validation->run() == false) {

			$final['status'] = false;
			$final['message'] = validation_errors();
			$final['note'] = 'Erro no formulário.';

			$this->response($final, REST_Controller::HTTP_OK);
		} else {

			// set variables from the form
			$user_email = $this->input->post('user_email');
			$user_password = $this->input->post('user_password');

			if ($this->user_model->auth($user_email, $user_password)) {

				$user_id = $this->user_model->get_user_id_from_email($user_email);
				$user    = $this->user_model->get_user($user_id);

				// set session user datas
				$_SESSION['user_id']      = (int)$user->id;
				$_SESSION['user_email']     = (string)$user->user_email;
				$_SESSION['logged_in']    = (bool)true;
				// $_SESSION['is_confirmed'] = (bool)$user->is_confirmed;
				// $_SESSION['is_admin']     = (bool)$user->is_admin;

				// user login ok
				$token_data['uid'] = $user_id;
				$token_data['user_email'] = $user->user_email;
				$tokenData = $this->authorization_token->generateToken($token_data);
				$final = array();
				$final['uid'] = $user_id;
				$final['access_token'] = $tokenData;
				$final['user_type'] = $user->user_type;
				$final['status'] = true;
				$final['message'] = 'Logado com sucesso!';
				$final['note'] = 'Você está logado.';

				$this->response($final, REST_Controller::HTTP_OK);
			} else {

				$final['status'] = false;
				$final['message'] = 'E-mail ou senha estão incorretos.';
				$final['note'] = 'E-mail ou senha estão incorretos.';
				// login failed
				$this->response($final, REST_Controller::HTTP_OK);
			}
		}
	}

	public function logout_post()
	{

		if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {

			// remove session datas
			foreach ($_SESSION as $key => $value) {
				unset($_SESSION[$key]);
			}

			// user logout ok
			$final['status'] = true;
			$final['message'] = 'Deslogado com sucesso!.';
			$final['note'] = 'Sessão foi resetada.';

			$this->response($final, REST_Controller::HTTP_OK);
		} else {

			// there user was not logged in, we cannot logged him out,
			// redirect him to site root
			// redirect('/');
			$final['status'] = false;
			$final['message'] = 'Não existe sessão ativa.';
			$final['note'] = 'Usuário provavelmente já está deslogado.';

			$this->response($final, REST_Controller::HTTP_OK);
		}
	}

	public function recovery_post()
	{
		$this->form_validation->set_rules('user_email', 'E-mail', 'trim|required|valid_email');

		if ($this->form_validation->run() == false) {

			$final['status'] = false;
			$final['message'] = validation_errors();
			$final['note'] = 'Erro no formulário.';

			$this->response($final, REST_Controller::HTTP_OK);
		} else {

			// set variables from the form
			$user_email = $this->input->post('user_email');

			if ($res = $this->user_model->get_user_id_from_email($user_email)) {


				if ($this->email_model->send_user_recovery($user_email)) {

					$final['status'] = true;
					$final['message'] = 'Se existir o e-mail, você receberá um link para alterar sua senha.';
					$final['note'] = 'O e-mail foi encontrado em get_user_id_from_email()';

					$this->response($final, REST_Controller::HTTP_OK);
				} else {

					$final['status'] = false;
					$final['message'] = 'Houve um erro ao enviar email. Tente novamente.';
					$final['note'] = 'Erro em send_user_recovery()';

					$this->response($final, REST_Controller::HTTP_OK);
				}
			} else {

				$final['status'] = true;
				$final['message'] = 'Se existir o e-mail, você receberá um link para alterar sua senha.';
				$final['note'] = 'O e-mail não foi encontrado em get_user_id_from_email()';
				// login failed
				$this->response($final, REST_Controller::HTTP_OK);
			}
		}
	}

	public function check_preferences_init_post()
	{
		$this->form_validation->set_rules('user_id', 'User ID', 'trim|required');

		if ($this->form_validation->run() == false) {

			$final['status'] = false;
			$final['message'] = validation_errors();
			$final['note'] = 'Erro no formulário.';

			$this->response($final, REST_Controller::HTTP_OK);
		} else {

			$headers = $this->input->request_headers();

			if (isset($headers['Authorization'])) {

				$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);

				if ($decodedToken['status']) {

					$user_id = $this->input->post('user_id');
					$check_init_preferences =  $this->user_model->check_init_preferences($user_id);

					if ($check_init_preferences) {

						$final['status'] = true;
						$final['message'] = 'Preferencias encontradas com sucesso.';
						$final['response'] = $check_init_preferences->user_verified_preferences;
						$final['note'] = 'Dados   encontrados get_check_init_preferences()';

						$this->response($final, REST_Controller::HTTP_OK);
					} else {

						$final['status'] = false;
						$final['message'] = 'Nenhuma preferência encontrada.';
						$final['note'] = 'Erro em get_check_init_preferences()';

						$this->response($final, REST_Controller::HTTP_OK);
					}
				} else {

					$final['status'] = false;
					$final['message'] = 'Sua sessão expirou.';
					$final['note'] = 'Erro em $decodedToken["status"]';
					$this->response($decodedToken);
				}
			} else {

				$final['status'] = false;
				$final['message'] = 'Falha na autenticação.';
				$final['note'] = 'Erro em validateToken()';

				$this->response($final, REST_Controller::HTTP_OK);
			}
		}
	}

	public function check_creci_init_post()
	{
		$this->form_validation->set_rules('user_id', 'User ID', 'trim|required');

		if ($this->form_validation->run() == false) {

			$final['status'] = false;
			$final['message'] = validation_errors();
			$final['note'] = 'Erro no formulário.';

			$this->response($final, REST_Controller::HTTP_OK);
		} else {

			$headers = $this->input->request_headers();

			if (isset($headers['Authorization'])) {

				$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);

				if ($decodedToken['status']) {

					$user_id = $this->input->post('user_id');
					$check_init_creci =  $this->user_model->check_init_creci($user_id);

					if ($check_init_creci) {

						$final['status'] = true;
						$final['message'] = 'Creci encontradas com sucesso.';
						$final['response'] = $check_init_creci->user_verified_creci;
						$final['note'] = 'Dados   encontrados get_check_init_creci()';

						$this->response($final, REST_Controller::HTTP_OK);
					} else {

						$final['status'] = false;
						$final['message'] = 'Nenhuma preferência encontrada.';
						$final['note'] = 'Erro em get_check_init_creci()';

						$this->response($final, REST_Controller::HTTP_OK);
					}
				} else {

					$final['status'] = false;
					$final['message'] = 'Sua sessão expirou.';
					$final['note'] = 'Erro em $decodedToken["status"]';
					$this->response($decodedToken);
				}
			} else {

				$final['status'] = false;
				$final['message'] = 'Falha na autenticação.';
				$final['note'] = 'Erro em validateToken()';

				$this->response($final, REST_Controller::HTTP_OK);
			}
		}
	}

	public function preferences_get()
	{

		$headers = $this->input->request_headers();

		if (isset($headers['Authorization'])) {

			$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);


			if ($decodedToken['status']) {

				$db_preferences =  $this->user_model->get_db_preferences();

				if ($db_preferences) {

					$final['status'] = true;
					$final['message'] = 'Preferencias encontradas com sucesso.';
					$final['response'] = $db_preferences;
					$final['note'] = 'Dados   encontrados get_db_preferences()';

					$this->response($final, REST_Controller::HTTP_OK);
				} else {

					$final['status'] = false;
					$final['message'] = 'Nenhuma preferência encontrada.';
					$final['note'] = 'Erro em get_db_preferences()';

					$this->response($final, REST_Controller::HTTP_OK);
				}
			} else {

				$final['status'] = false;
				$final['message'] = 'Sua sessão expirou.';
				$final['note'] = 'Erro em $decodedToken["status"]';

				$this->response($decodedToken);
			}
		} else {

			$final['status'] = false;
			$final['message'] = 'Falha na autenticação.';
			$final['note'] = 'Erro em validateToken()';

			$this->response($final, REST_Controller::HTTP_OK);
		}
	}

	public function preferences_post()
	{
		$this->form_validation->set_rules('user_id', 'User ID', 'trim|required|alpha_numeric');
		$this->form_validation->set_rules('user_type', 'User Type', 'trim|required|alpha_numeric');

		if ($this->form_validation->run() == false) {

			$final['status'] = false;
			$final['message'] = validation_errors();
			$final['note'] = 'Erro no formulário.';

			$this->response($final, REST_Controller::HTTP_OK);
		} else {

			$user_id = $this->input->post('user_id');
			$user_type = $this->input->post('user_type');

			$preferences_data = $this->input->post('preferences_data');
			$preferences_data = trim($preferences_data, "[]");
			$preferences_data = stripslashes($preferences_data);
			$preferences_data = str_replace('"', "", $preferences_data);
			$preferences_array = explode(",", $preferences_data);


			if (count($preferences_array) < 5) {

				$final['status'] = false;
				$final['message'] = 'Selecione pelo menos 5 características.';
				$final['note'] = 'count($preferences_array) < 5';

				$this->response($final, REST_Controller::HTTP_OK);
			} else if (count($preferences_array) > 5) {

				$final['status'] = false;
				$final['message'] = 'Selecione no máximo 5 características.';
				$final['note'] = '$preferences_count > 5';

				$this->response($final, REST_Controller::HTTP_OK);
			} else {

				if ($this->user_model->get_user($user_id)) {

					$this->user_model->reset_user_preferences($user_id);

					foreach ($preferences_array as $p) {
						$this->user_model->add_user_preferences($p, $user_id, $user_type);
					}

					$dados =  array(
						'user_verified_preferences' => 1
					);

					$this->user_model->update_user($user_id, $dados);

					$final['status'] = true;
					$final['message'] = 'Preferencias adicionadas com sucesso.';
					$final['note'] = 'Sucessoadd_user_preferences()';

					$this->response($final, REST_Controller::HTTP_OK);
				} else {

					$final['status'] = false;
					$final['message'] = 'ID do usuário inválido.';
					$final['note'] = 'Erro no get_user($user_id)';

					$this->response($final, REST_Controller::HTTP_OK);
				}
			}
		}
	}

	public function check_session_post()
	{

		$headers = $this->input->request_headers();

		if (isset($headers['Authorization'])) {

			$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);

			if ($decodedToken['status']) {

				$final['status'] = true;
				$final['message'] = 'Sessão ativa.';
				$final['response'] = $decodedToken['status'];
				$final['note'] = 'Sessão ativa.';

				$this->response($final, REST_Controller::HTTP_OK);
			} else {

				$final['status'] = false;
				$final['message'] = 'Sua sessão expirou.';
				$final['note'] = 'Erro em $decodedToken["status"]';
				$this->response($decodedToken);
			}
		} else {

			$final['status'] = false;
			$final['message'] = 'Falha na autenticação.';
			$final['note'] = 'Erro em validateToken()';

			$this->response($final, REST_Controller::HTTP_OK);
		}
	}


	// add favorits


	public function check_favorit_post()
	{

		$this->form_validation->set_rules('user_id', 'User ID', 'trim|required');
		$this->form_validation->set_rules('property_id', 'Property ID', 'trim|required');

		if ($this->form_validation->run() == false) {

			$final['status'] = false;
			$final['message'] = validation_errors();
			$final['note'] = 'Erro no formulário.';

			$this->response($final, REST_Controller::HTTP_OK);
		} else {

			$headers = $this->input->request_headers();

			if (isset($headers['Authorization'])) {

				$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);

				if ($decodedToken['status']) {

					$user_id = $this->input->post('user_id');
					$property_id = $this->input->post('property_id');

					$check_data =  $this->user_model->check_favorit($user_id, $property_id);

					if ($check_data) {

						$final['status'] = true;
						$final['response'] = $check_data;
						$final['message'] = 'check Favorito encontrado com sucesso.';
						$final['note'] = 'Dados   encontrados get_favorits()';

						$this->response($final, REST_Controller::HTTP_OK);
					} else {

						$final['status'] = false;
						$final['message'] = 'Erro ao encontrar check favorito.';
						$final['note'] = 'Erro em get_favorits()';

						$this->response($final, REST_Controller::HTTP_OK);
					}
				} else {

					$final['status'] = false;
					$final['message'] = 'Sua sessão expirou.';
					$final['note'] = 'Erro em $decodedToken["status"]';
					$this->response($decodedToken);
				}
			} else {

				$final['status'] = false;
				$final['message'] = 'Falha na autenticação.';
				$final['note'] = 'Erro em validateToken()';

				$this->response($final, REST_Controller::HTTP_OK);
			}
		}
	}

	public function add_favorit_post()
	{

		$this->form_validation->set_rules('user_id', 'User ID', 'trim|required');
		$this->form_validation->set_rules('property_id', 'Property ID', 'trim|required');

		if ($this->form_validation->run() == false) {

			$final['status'] = false;
			$final['message'] = validation_errors();
			$final['note'] = 'Erro no formulário.';

			$this->response($final, REST_Controller::HTTP_OK);
		} else {

			$headers = $this->input->request_headers();

			if (isset($headers['Authorization'])) {

				$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);

				if ($decodedToken['status']) {

					$user_id = $this->input->post('user_id');
					$property_id = $this->input->post('property_id');

					$add_favorit =  $this->user_model->add_favorit($user_id, $property_id);

					if ($add_favorit) {

						$final['status'] = true;
						$final['message'] = 'Favorito adicionado com sucesso.';
						$final['note'] = 'Dados   encontrados add_favorit()';

						$this->response($final, REST_Controller::HTTP_OK);
					} else {

						$final['status'] = false;
						$final['message'] = 'Erro ao adicionar favorito.';

						$final['note'] = 'Erro em add_favorit()';

						$this->response($final, REST_Controller::HTTP_OK);
					}
				} else {

					$final['status'] = false;
					$final['message'] = 'Sua sessão expirou.';
					$final['note'] = 'Erro em $decodedToken["status"]';
					$this->response($decodedToken);
				}
			} else {

				$final['status'] = false;
				$final['message'] = 'Falha na autenticação.';
				$final['note'] = 'Erro em validateToken()';

				$this->response($final, REST_Controller::HTTP_OK);
			}
		}
	}

	public function get_favorits_post()
	{

		$this->form_validation->set_rules('user_id', 'User ID', 'trim|required');

		if ($this->form_validation->run() == false) {

			$final['status'] = false;
			$final['message'] = validation_errors();
			$final['note'] = 'Erro no formulário.';

			$this->response($final, REST_Controller::HTTP_OK);
		} else {

			$headers = $this->input->request_headers();

			if (isset($headers['Authorization'])) {

				$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);

				if ($decodedToken['status']) {

					$user_id = $this->input->post('user_id');

					$favorits_data =  $this->user_model->get_favorits($user_id);

					if ($favorits_data) {

						$final['status'] = true;
						$final['response'] = $favorits_data;
						$final['message'] = 'Favorito encontrado com sucesso.';
						$final['note'] = 'Dados   encontrados get_favorits()';

						$this->response($final, REST_Controller::HTTP_OK);
					} else {

						$final['status'] = false;
						$final['message'] = 'Erro ao encontrar favorito.';
						$final['note'] = 'Erro em get_favorits()';

						$this->response($final, REST_Controller::HTTP_OK);
					}
				} else {

					$final['status'] = false;
					$final['message'] = 'Sua sessão expirou.';
					$final['note'] = 'Erro em $decodedToken["status"]';
					$this->response($decodedToken);
				}
			} else {

				$final['status'] = false;
				$final['message'] = 'Falha na autenticação.';
				$final['note'] = 'Erro em validateToken()';

				$this->response($final, REST_Controller::HTTP_OK);
			}
		}
	}

	public function delete_favorit_post()
	{

		$this->form_validation->set_rules('user_id', 'User ID', 'trim|required');
		$this->form_validation->set_rules('property_id', 'Property ID', 'trim|required');

		if ($this->form_validation->run() == false) {

			$final['status'] = false;
			$final['message'] = validation_errors();
			$final['note'] = 'Erro no formulário.';

			$this->response($final, REST_Controller::HTTP_OK);
		} else {

			$headers = $this->input->request_headers();

			if (isset($headers['Authorization'])) {

				$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);

				if ($decodedToken['status']) {

					$user_id = $this->input->post('user_id');
					$property_id = $this->input->post('property_id');

					$delete_favorit =  $this->user_model->delete_favorit($user_id, $property_id);

					if ($delete_favorit) {

						$final['status'] = true;
						$final['message'] = 'Favorito deleteado com sucesso.';
						$final['note'] = 'Dados   encontrados delete_favorit()';

						$this->response($final, REST_Controller::HTTP_OK);
					} else {

						$final['status'] = false;
						$final['message'] = 'Erro ao deletar favorito.';

						$final['note'] = 'Erro em delete_favorit()';

						$this->response($final, REST_Controller::HTTP_OK);
					}
				} else {

					$final['status'] = false;
					$final['message'] = 'Sua sessão expirou.';
					$final['note'] = 'Erro em $decodedToken["status"]';
					$this->response($decodedToken);
				}
			} else {

				$final['status'] = false;
				$final['message'] = 'Falha na autenticação.';
				$final['note'] = 'Erro em validateToken()';

				$this->response($final, REST_Controller::HTTP_OK);
			}
		}
	}

	// add favorits


	// profile

	public function get_user_profile_data_post()
	{

		$this->form_validation->set_rules('user_id', 'User ID', 'trim|required');


		if ($this->form_validation->run() == false) {

			$final['status'] = false;
			$final['message'] = validation_errors();
			$final['note'] = 'Erro no formulário.';

			$this->response($final, REST_Controller::HTTP_OK);
		} else {

			$headers = $this->input->request_headers();

			if (isset($headers['Authorization'])) {

				$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);

				if ($decodedToken['status']) {

					$user_id = $this->input->post('user_id');

					$user_data =  $this->user_model->get_user($user_id);

					if ($user_data) {

						$final['status'] = true;
						$final['message'] = 'Encontrado com sucesso.';
						$final['response'] = $user_data;
						$final['note'] = 'Dados encontrados get_user()';
						$this->response($final, REST_Controller::HTTP_OK);
					} else {

						$final['status'] = false;
						$final['message'] = 'Ocorreu um erro ao pegar dados.';
						$final['note'] = 'Erro em get_user()';
						$this->response($final, REST_Controller::HTTP_OK);
					}
				} else {

					$final['status'] = false;
					$final['message'] = 'Sua sessão expirou.';
					$final['note'] = 'Erro em $decodedToken["status"]';
					$this->response($decodedToken);
				}
			} else {

				$final['status'] = false;
				$final['message'] = 'Falha na autenticação.';
				$final['note'] = 'Erro em validateToken()';

				$this->response($final, REST_Controller::HTTP_OK);
			}
		}
	}
	public function update_broker_profile_post()
	{

		$this->form_validation->set_rules('user_id', 'User ID', 'trim|required');
		$this->form_validation->set_rules('user_name', 'Nome', 'trim|required');
		$this->form_validation->set_rules('user_email', 'E-mail', 'trim|required');
		$this->form_validation->set_rules('user_state', 'Estado', 'trim|required');
		$this->form_validation->set_rules('user_cidade', 'Cidade', 'trim|required');
		$this->form_validation->set_rules('user_creci', 'CRECI', 'trim|required');
		$this->form_validation->set_rules('user_cpf', 'CPF', 'trim|required');

		if ($this->form_validation->run() == false) {

			$final['status'] = false;
			$final['message'] = validation_errors();
			$final['note'] = 'Erro no formulário.';

			$this->response($final, REST_Controller::HTTP_OK);
		} else {

			$headers = $this->input->request_headers();

			if (isset($headers['Authorization'])) {

				$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);

				if ($decodedToken['status']) {

					$user_id = $this->input->post('user_id');

					$data['user_name'] = $this->input->post('user_name');
					$data['user_email'] = $this->input->post('user_email');
					$data['user_state'] = $this->input->post('user_state');
					$data['user_city'] = $this->input->post('user_cidade');
					$data['user_creci'] = $this->input->post('user_creci');
					$data['user_cpf'] = $this->input->post('user_cpf');


					if (strlen($this->input->post('user_image')) > 0) {

						$path = 'public/images/users/';
						$property_main_image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->input->post('user_image')));

						$file_name = uniqid() . '.jpg';

						$data['user_image'] = $path . $file_name;

						if (file_put_contents($data['user_image'], $property_main_image)) {
						} else {
						}
					}

					$update_user =  $this->user_model->update_user_profile($user_id, $data);

					if (!$this->user_model->check_email($data['user_email'], $user_id)) {

						if ($update_user) {

							$final['status'] = true;
							$final['message'] = 'Atualizado com sucesso.';
							$final['note'] = 'Dados encontrados update_user_profile()';
							$this->response($final, REST_Controller::HTTP_OK);
						} else {

							$final['status'] = false;
							$final['message'] = 'Erro ao atualizar dados.';
							$final['note'] = 'Erro em update_user_profile()';
							$this->response($final, REST_Controller::HTTP_OK);
						}
					} else {

						$final['status'] = false;
						$final['message'] = 'Este e-mail já esta em uso.';
						$final['note'] = 'Erro em update_user_profile()';
						$this->response($final, REST_Controller::HTTP_OK);
					}
				} else {

					$final['status'] = false;
					$final['message'] = 'Sua sessão expirou.';
					$final['note'] = 'Erro em $decodedToken["status"]';
					$this->response($decodedToken);
				}
			} else {

				$final['status'] = false;
				$final['message'] = 'Falha na autenticação.';
				$final['note'] = 'Erro em validateToken()';

				$this->response($final, REST_Controller::HTTP_OK);
			}
		}
	}

	public function update_client_profile_post()
	{

		$this->form_validation->set_rules('user_id', 'User ID', 'trim|required');
		$this->form_validation->set_rules('user_name', 'Nome', 'trim|required');
		$this->form_validation->set_rules('user_email', 'E-mail', 'trim|required');
		$this->form_validation->set_rules('user_state', 'Estado', 'trim|required');
		$this->form_validation->set_rules('user_cidade', 'Cidade', 'trim|required');
		$this->form_validation->set_rules('user_creci', 'CRECI', 'trim|required');
		$this->form_validation->set_rules('user_cpf', 'CPF', 'trim|required');

		if ($this->form_validation->run() == false) {

			$final['status'] = false;
			$final['message'] = validation_errors();
			$final['note'] = 'Erro no formulário.';

			$this->response($final, REST_Controller::HTTP_OK);
		} else {

			$headers = $this->input->request_headers();

			if (isset($headers['Authorization'])) {

				$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);

				if ($decodedToken['status']) {

					$user_id = $this->input->post('user_id');

					$data['user_name'] = $this->input->post('user_name');
					$data['user_email'] = $this->input->post('user_email');
					$data['user_state'] = $this->input->post('user_state');
					$data['user_city'] = $this->input->post('user_cidade');

					// $data['user_creci'] = $this->input->post('user_creci');
					// $data['user_cpf'] = $this->input->post('user_cpf');


					// ==
					if (strlen( $this->input->post('user_image')) > 0) {
						$path = 'public/images/users/';
						$property_main_image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->input->post('user_image')));

						$file_name = uniqid() . '.jpg';

						$data['user_image'] = $path . $file_name;

						if (file_put_contents($data['user_image'], $property_main_image)) {
						} else {
						}
					}

					$update_user =  $this->user_model->update_user_profile($user_id, $data);

					if (!$this->user_model->check_email($data['user_email'], $user_id)) {

						if ($update_user) {

							$final['status'] = true;
							$final['message'] = 'Atualizado com sucesso.';
							$final['note'] = 'Dados encontrados update_user_profile()';
							$this->response($final, REST_Controller::HTTP_OK);
						} else {

							$final['status'] = false;
							$final['message'] = 'Erro ao atualizar dados.';
							$final['note'] = 'Erro em update_user_profile()';
							$this->response($final, REST_Controller::HTTP_OK);
						}
					} else {

						$final['status'] = false;
						$final['message'] = 'Este e-mail já esta em uso.';
						$final['note'] = 'Erro em update_user_profile()';
						$this->response($final, REST_Controller::HTTP_OK);
					}
				} else {

					$final['status'] = false;
					$final['message'] = 'Sua sessão expirou.';
					$final['note'] = 'Erro em $decodedToken["status"]';
					$this->response($decodedToken);
				}
			} else {

				$final['status'] = false;
				$final['message'] = 'Falha na autenticação.';
				$final['note'] = 'Erro em validateToken()';

				$this->response($final, REST_Controller::HTTP_OK);
			}
		}
	}

	// profile

}

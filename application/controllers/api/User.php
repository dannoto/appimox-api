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
		$this->load->model('broker_model');
		$this->load->model('property_model');
		$this->load->model('schedule_model');

		$this->load->model('followers_model');
		$this->load->model('plans_model');
	}

	public function inactive_post()
	{
		$this->form_validation->set_rules('user_id', 'User ID', 'trim|required');

		if ($this->form_validation->run() == false) {

			$final['status'] = false;
			$final['message'] = validation_errors();
			$final['note'] = 'Erro no formulário.';

			$this->response($final, REST_Controller::HTTP_OK);
		} else {

			// set variables from the form
			$user_id = $this->input->post('user_id');

			$data = array(
				'user_status' => 2
			);


			if ($this->user_model->update_user_profile($user_id, $data)) {

				$final['status'] = true;
				$final['message'] = 'Sua conta foi desativada com sucesso.';
				$final['note'] = 'Sua conta foi desativada com sucesso.';
				// login failed
				$this->response($final, REST_Controller::HTTP_OK);
			} else {

				$final['status'] = false;
				$final['message'] = 'Não foi possivel desativar sua conta. Tente novamente.';
				$final['note'] = 'Não foi possivel desativar sua conta. Tente novamente.';
				// login failed
				$this->response($final, REST_Controller::HTTP_OK);
			}
		}
	}

	public function register_post()
	{

		// set validation rules
		$this->form_validation->set_rules('user_name', 'Nome do Usuário', 'trim|required|min_length[6]');
		$this->form_validation->set_rules('user_email', 'Email', 'trim|required|valid_email|is_unique[users.user_email]', array('is_unique' => 'Este e-mail já existe. Escolha outro.'));

		$this->form_validation->set_rules('user_password', 'Senha', 'trim|required|min_length[6]');
		$this->form_validation->set_rules('user_password_confirm', 'Confirmação de Senha', 'trim|required|min_length[6]|matches[user_password]');
		// $this->form_validation->set_rules('user_auth_type', 'Tipo de Cadastro', 'trim|required');

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
				$final['user_name'] =  $user->user_name;

				$final['user_status'] =  $user->user_status;
				$final['user_notification_token'] =  $user->user_notification_token;

				$final['access_token'] = $tokenData;
				$final['user_type'] = $user->user_type;
				$final['user_admin'] = $user->user_admin;
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

	public function add_notification_token_post()
	{

		// set validation rules
		$this->form_validation->set_rules('user_id', 'User ID', 'trim|required');
		$this->form_validation->set_rules('notification_token', 'notification_token ', 'trim|required');

		if ($this->form_validation->run() == false) {

			$final['status'] = false;
			$final['message'] = validation_errors();
			$final['note'] = 'Erro no formulário.';

			$this->response($final, REST_Controller::HTTP_OK);
		} else {

			// set variables from the form
			$notification_token = $this->input->post('notification_token');
			$user_id = $this->input->post('user_id');

			if ($this->user_model->check_notification_token($user_id, $notification_token)) {

				if ($this->user_model->add_notification_token($user_id, $notification_token)) {

					$final['status'] = true;
					$final['message'] = 'Notification Token Já foi registrado.';
					$final['note'] = 'Notification Token Já foi registrado.';
					// login failed
					$this->response($final, REST_Controller::HTTP_OK);
				}
			} else {

				if ($this->user_model->add_notification_token($user_id, $notification_token)) {

					$final['status'] = false;
					$final['message'] = 'Registrado com sucesso.';
					$final['note'] = 'Registrado com sucesso.';
					// login failed
					$this->response($final, REST_Controller::HTTP_OK);
				} else {

					$final['status'] = false;
					$final['message'] = 'Erro ao registrar token.';
					$final['note'] = 'Erro ao registrar token.';
					// login failed
					$this->response($final, REST_Controller::HTTP_OK);
				}
			}
		}
	}


	public function set_user_type_post()
	{

		// set validation rules
		$this->form_validation->set_rules('user_type', 'User Type', 'trim|required');
		$this->form_validation->set_rules('user_id', 'User ID', 'trim|required');
		$this->form_validation->set_rules('access_token', 'Access Token', 'trim|required');

		if ($this->form_validation->run() == false) {

			$final['status'] = false;
			$final['message'] = validation_errors();
			$final['note'] = 'Erro no formulário.';

			$this->response($final, REST_Controller::HTTP_OK);
		} else {

			// set variables from the form
			$user_type = $this->input->post('user_type');
			$user_id = $this->input->post('user_id');
			$access_token = $this->input->post('access_token');


			if ($this->user_model->update_user_type($user_id, $user_type)) {

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
				$tokenData = $access_token;
				$final = array();
				$final['uid'] = $user_id;
				$final['access_token'] = $tokenData;
				$final['user_type'] = $user->user_type;
				$final['status'] = true;
				$final['message'] = 'Tipo definido com sucesso!';
				$final['note'] = 'Tipo definido com sucesso!';

				$this->response($final, REST_Controller::HTTP_OK);
			} else {

				$final['status'] = false;
				$final['message'] = 'Não foi possivel definir o tipo. Tente novamente.';
				$final['note'] = 'Não foi possivel definir o tipo. Tente novamente.';
				// login failed
				$this->response($final, REST_Controller::HTTP_OK);
			}
		}
	}

	public function check_creci_validation_post()
	{

		$this->form_validation->set_rules('user_creci', 'USER CRECI', 'trim|required');
		$this->form_validation->set_rules('user_cpf', 'USER CPF', 'required');
		$this->form_validation->set_rules('user_state', 'USER STATE', 'trim|required');
		$this->form_validation->set_rules('user_city', 'USER STATE', 'trim|required');

		$this->form_validation->set_rules('user_id', 'USER STATE', 'trim|required');

		if ($this->form_validation->run() == false) {

			$final['status'] = false;
			$final['message'] = validation_errors();
			$final['note'] = 'Erro no formulário.';

			$this->response($final, REST_Controller::HTTP_OK);
		} else {

			$user_id = $this->input->post('user_id');
			$user_creci = $this->input->post('user_creci');
			$user_cpf = $this->input->post('user_cpf');
			$user_state = $this->input->post('user_state');
			$user_city = $this->input->post('user_city');

			if ($user_state == "20") {
				$creci_data = $this->broker_model->check_creci_rn($user_creci, $user_cpf);
			} else if ($user_state == "15") {
				$creci_data = $this->broker_model->check_creci_pb($user_creci, $user_cpf);
			} else if ($user_state == "16") {

				$creci_data = $this->broker_model->check_creci_pe($user_creci, $user_cpf);
			}

			if (count($creci_data->cadastros) > 0) {

				foreach ($creci_data->cadastros as $c) {

					if ($c->tipo == 1) {

						if ($c->situacao == 1) {

							// verificando duplicidade

							if ($this->user_model->check_creci_is_unique($user_creci, $user_cpf)) {

								$final['status'] = false;
								$final['message'] = 'Esta inscrição já está sendo usada. Contate o suporte';
								$final['note'] = 'Esta inscrição já está sendo usada. Contate o suporte';

								$this->response($final, REST_Controller::HTTP_OK);
							} else {

								$user_data['user_state'] = $user_state;
								$user_data['user_city'] = $user_city;
								$user_data['user_verified_creci'] = 1;
								$user_data['user_cpf'] = $c->cpf;
								$user_data['user_creci'] = $c->creci;

								if ($this->user_model->update_user($user_id, $user_data)) {

									$final['status'] = true;
									$final['response'] = $c;
									$final['message'] = 'Validado com sucesso! Bem-vindo!';
									$final['note'] = 'Validado com sucesso! Bem-vindo!';

									$this->response($final, REST_Controller::HTTP_OK);
								} else {

									$final['status'] = false;

									$final['message'] = 'Houve um erro ao verificar! Tente novamente!';
									$final['note'] = 'Houve um erro ao verificar! Tente novamente!';

									$this->response($final, REST_Controller::HTTP_OK);
								}
							}
						} else  if ($c->situacao == 8) {

							$final['status'] = false;
							$final['response'] = $c;
							$final['message'] = 'Seu cadastro está inativo.';
							$final['note'] = 'Seu cadastro está inativo.';

							$this->response($final, REST_Controller::HTTP_OK);
						} else {

							$final['status'] = false;
							$final['response'] = $c;
							$final['message'] = 'Seu cadastro não está ativo.';
							$final['note'] = 'Seu cadastro não está ativo.';

							$this->response($final, REST_Controller::HTTP_OK);
						}
					}
				}
			} else {

				$final['status'] = false;
				$final['message'] = 'Nenhuma inscrição encontrada. Confira seus dados.';
				$final['note'] = 'Nenhuma inscrição encontrada. Confira seus dados.';

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

			if ( $this->user_model->get_user_id_from_email($user_email)) {


				if ($this->email_model->send_user_recovery($user_email)) {

					$final['status'] = true;
					$final['message'] = 'Se existirx o e-mail, você receberá um link para alterar sua senha.';
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
				$final['message'] = 'Se existiry o e-mail, você receberá um link para alterar sua senha.';
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

	public function update_client_location_post()
	{
		$this->form_validation->set_rules('user_id', 'User ID', 'trim|required');
		$this->form_validation->set_rules('user_state', 'Cidade', 'trim|required');
		$this->form_validation->set_rules('user_city', 'Estado', 'trim|required');

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
					$data['user_state'] = $this->input->post('user_state');
					$data['user_city'] = $this->input->post('user_city');

					if ($this->user_model->update_user($user_id, $data)) {

						$final['status'] = true;
						$final['message'] = 'Atualizado com sucesso.';
						$final['note'] = 'Dados atualizado update_user()';

						$this->response($final, REST_Controller::HTTP_OK);
					} else {

						$final['status'] = false;
						$final['message'] = 'Erro ao atualizar.';
						$final['note'] = 'Erro em update_user()';

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
					$broker_property_id = $this->property_model->get_property($property_id);
					$broker_property_id = $broker_property_id->property_user_id;

					$add_favorit =  $this->user_model->add_favorit($user_id, $property_id, $broker_property_id);

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


	public function search_get_favorits_post()
	{

		$this->form_validation->set_rules('user_id', 'User ID', 'trim|required');
		$this->form_validation->set_rules('query', 'User ID', 'trim|required');

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
					$query = $this->input->post('query');

					$favorits_data =  $this->user_model->search_get_favorits($user_id, $query);

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
						$final['user_id'] = $this->input->post('request_id');
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

					$user_current_data = $this->user_model->get_user($user_id);

					$data['user_name'] = $this->input->post('user_name');
					$data['user_email'] = $this->input->post('user_email');
					$data['user_state'] = $this->input->post('user_state');
					$data['user_city'] = $this->input->post('user_cidade');

					// Verificando se alterour creci ou cpf.

					if ($user_current_data->user_creci != $this->input->post('user_creci') || $user_current_data->user_cpf !=  $this->input->post('user_cpf')) {
						$data['user_creci'] = $this->input->post('user_creci');
						$data['user_cpf'] = $this->input->post('user_cpf');
					}

					if (strlen($this->input->post('user_image')) > 0) {

						$path = 'public/images/users/';
						$property_main_image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->input->post('user_image')));

						$file_name = uniqid() . '.jpg';

						$data['user_image'] = $path . $file_name;

						if (file_put_contents($data['user_image'], $property_main_image)) {
						} else {
						}
					}

					if ($user_current_data->user_creci != $this->input->post('user_creci') || $user_current_data->user_cpf !=  $this->input->post('user_cpf')) {


						if ($data['user_state'] == "RN") {

							$creci_data = $this->broker_model->check_creci_rn($data['user_creci'], $data['user_cpf']);
						} else if ($data['user_state'] == "PB") {

							$creci_data = $this->broker_model->check_creci_pb($data['user_creci'], $data['user_cpf']);
						} else if ($data['user_state'] == "PE") {

							$creci_data = $this->broker_model->check_creci_pe($data['user_creci'], $data['user_cpf']);
						}

						if (count($creci_data->cadastros) > 0) {

							foreach ($creci_data->cadastros as $c) {

								if ($c->tipo == 1) {

									if ($c->situacao == 1) {

										// verificando duplicidade

										if ($this->user_model->check_creci_is_unique($data['user_creci'], $data['user_cpf'])) {

											$final['status'] = false;
											$final['message'] = 'Esta inscrição já está sendo usada. Contate o suporte';
											$final['note'] = 'Esta inscrição já está sendo usada. Contate o suporte';

											$this->response($final, REST_Controller::HTTP_OK);
										} else {


											if (!$this->user_model->check_email($data['user_email'], $user_id)) {

												$update_user =  $this->user_model->update_user_profile($user_id, $data);
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
										}
									} else  if ($c->situacao == 8) {

										$final['status'] = false;
										$final['response'] = $c;
										$final['message'] = 'Seu cadastro está inativo.';
										$final['note'] = 'Seu cadastro está inativo.';

										$this->response($final, REST_Controller::HTTP_OK);
									} else {

										$final['status'] = false;
										$final['response'] = $c;
										$final['message'] = 'Seu cadastro não está ativo.';
										$final['note'] = 'Seu cadastro não está ativo.';

										$this->response($final, REST_Controller::HTTP_OK);
									}
								}
							}
						} else {

							$final['status'] = false;
							$final['message'] = 'Nenhuma inscrição encontrada. Confira seus dados.';
							$final['note'] = 'Nenhuma inscrição encontrada. Confira seus dados.';

							$this->response($final, REST_Controller::HTTP_OK);
						}
					} else {

						if (!$this->user_model->check_email($data['user_email'], $user_id)) {

							$update_user =  $this->user_model->update_user_profile($user_id, $data);
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
					if (strlen($this->input->post('user_image')) > 0) {
						$path = 'public/images/users/';
						$property_main_image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->input->post('user_image')));

						$file_name = uniqid() . '.jpg';

						$data['user_image'] = $path . $file_name;

						if (file_put_contents($data['user_image'], $property_main_image)) {
						} else {
						}
					}


					if (!$this->user_model->check_email($data['user_email'], $user_id)) {

						$update_user =  $this->user_model->update_user_profile($user_id, $data);

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



	// cidades

	public function get_cidades_by_estado_post()
	{

		$this->form_validation->set_rules('uf', 'User ID', 'trim|required');


		if ($this->form_validation->run() == false) {

			$final['status'] = false;
			$final['message'] = validation_errors();
			$final['note'] = 'Erro no formulário.';

			$this->response($final, REST_Controller::HTTP_OK);
		} else {

			// $headers = $this->input->request_headers();

			// if (isset($headers['Authorization'])) {

			// 	$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);

			// if ($decodedToken['status']) {

			$uf = $this->input->post('uf');

			$cidades_data = $this->user_model->get_cidades_by_estado($uf);

			if ($cidades_data) {

				$final['status'] = true;
				$final['response'] = $cidades_data;
				$final['message'] = 'Cidades encontradas com sucesso.';
				$final['note'] = 'Cidades encontradas com sucesso.';
				$this->response($final, REST_Controller::HTTP_OK);
			} else {

				$final['status'] = false;
				$final['message'] = 'Nenhuma cidade encontrada.';
				$final['note'] = 'Nenhuma cidade encontrada.';
				$this->response($final, REST_Controller::HTTP_OK);
			}
			// } else {

			// 	$final['status'] = false;
			// 	$final['message'] = 'Sua sessão expirou.';
			// 	$final['note'] = 'Erro em $decodedToken["status"]';
			// 	$this->response($decodedToken);
			// }
			// } else {

			// 	$final['status'] = false;
			// 	$final['message'] = 'Falha na autenticação.';
			// 	$final['note'] = 'Erro em validateToken()';

			// 	$this->response($final, REST_Controller::HTTP_OK);
			// }
		}
	}

	public function get_estados_post()
	{

		$estados_data = $this->user_model->get_estados();

		if ($estados_data) {

			$final['status'] = true;
			$final['response'] = $estados_data;
			$final['message'] = 'Estados encontrados com sucesso.';
			$final['note'] = 'Estados encontrados com sucesso.';

			$this->response($final, REST_Controller::HTTP_OK);
		} else {

			$final['status'] = false;
			$final['message'] = 'Nenhum estado encontrada.';
			$final['note'] = 'Nenhum estado encontrada.';

			$this->response($final, REST_Controller::HTTP_OK);
		}
	}

	private function hash_password($password)
	{

		return password_hash($password, PASSWORD_BCRYPT);
	}

	private function verify_password_hash($password, $hash)
	{

		return password_verify($password, $hash);
	}

	public function update_password_post()
	{

		$this->form_validation->set_rules('user_id', 'ID do usuario', 'trim|required');
		$this->form_validation->set_rules('current_password', 'ID do usuario', 'trim|required');
		$this->form_validation->set_rules('new_password', 'ID do usuario', 'trim|required');
		// $this->form_validation->set_rules('new_password_confirm', 'ID do usuario', 'trim|required');


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

					$user_data = $this->user_model->get_user($this->input->post('user_id'));
					$current_password = $this->input->post('current_password');
					$new_password = $this->input->post('new_password');

					// check password

					if (!$this->verify_password_hash($current_password, $user_data->user_password)) {

						$final['status'] = false;
						$final['message'] = 'Sua senha atual está incorreta.';
						$this->response($final, REST_Controller::HTTP_OK);
					} else {


						$df = array(
							'user_password' => $this->hash_password($new_password),
						);

						if ($this->user_model->update_user($user_data->id, $df)) {

							$final['status'] = true;
							$final['message'] = 'Sua senha foi alterada com sucesso.';
							$this->response($final, REST_Controller::HTTP_OK);
						} else {

							$final['status'] = false;
							$final['message'] = 'Erro temporário. Tente novamente.';
							$this->response($final, REST_Controller::HTTP_OK);
						}
					}
				} else {

					$final['status'] = false;
					$final['message'] = 'Sua sessão expirou.';
					$final['note'] = 'Erro em $decodedToken["status"]';
					$this->response($final, REST_Controller::HTTP_OK);
				}
			} else {

				$final['status'] = false;
				$final['message'] = 'Falha na autenticação.';
				$final['note'] = 'Erro em validateToken()';

				$this->response($final, REST_Controller::HTTP_OK);
			}
		}
	}

	public function get_current_plan_config_post()
	{

		$this->form_validation->set_rules('user_id', 'ID do usuario', 'trim|required');


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

					$user_data = $this->user_model->get_user($this->input->post('user_id'));

					if ($user_data) {

						$plan_data = $this->plans_model->get_plan($user_data->user_plan);

						if ($plan_data) {

							$final['status'] = true;
							$final['message'] = 'Plano encontrado sucesso';
							$final['response'] = $plan_data;
							$final['note'] = 'Dados encontrados suggest_property_by_cidade()';
							$this->response($final, REST_Controller::HTTP_OK);
						} else {

							$final['status'] = true;
							$final['message'] = 'Plano não foi encontrado';
							$final['note'] = 'Dados encontrados suggest_property_by_estado()';
							$this->response($final, REST_Controller::HTTP_OK);
						}
					} else {

						$final['status'] = false;
						$final['message'] = 'Ocorreu um erro ao identificar usuario.';
						$final['note'] = 'Erro em get_user()';
						$this->response($final, REST_Controller::HTTP_OK);
					}
				} else {

					$final['status'] = false;
					$final['message'] = 'Sua sessão expirou.';
					$final['note'] = 'Erro em $decodedToken["status"]';
					$this->response($final, REST_Controller::HTTP_OK);
				}
			} else {

				$final['status'] = false;
				$final['message'] = 'Falha na autenticação.';
				$final['note'] = 'Erro em validateToken()';

				$this->response($final, REST_Controller::HTTP_OK);
			}
		}
	}

	public function get_config_post()
	{

		$config_data = $this->user_model->get_config();

		if ($config_data) {

			$final['status'] = true;
			$final['response'] = $config_data;
			$final['message'] = 'Config encontrada com sucesso.';
			$final['note'] = 'Config encontrada com sucesso.';

			$this->response($final, REST_Controller::HTTP_OK);
		} else {

			$final['status'] = false;
			$final['message'] = 'Nenhum config encontrada.';
			$final['note'] = 'Nenhum config encontrada.';

			$this->response($final, REST_Controller::HTTP_OK);
		}
	}

	public function get_estados_client_post()
	{

		$estados_data = $this->user_model->get_estados_client();

		if ($estados_data) {

			$final['status'] = true;
			$final['response'] = $estados_data;
			$final['message'] = 'Estados encontrados com sucesso.';
			$final['note'] = 'Estados encontrados com sucesso.';
			$this->response($final, REST_Controller::HTTP_OK);
		} else {

			$final['status'] = false;
			$final['message'] = 'Nenhum estado encontrada.';
			$final['note'] = 'Nenhum estado encontrada.';
			$this->response($final, REST_Controller::HTTP_OK);
		}
	}


	// Cliente dashboard	
	public function get_suggest_client_propertys_post()
	{

		$this->form_validation->set_rules('user_id', 'ID do usuario', 'trim|required');


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

						$property_estado = $this->property_model->suggest_property_by_estado($user_data->user_state);
						$property_cidade = $this->property_model->suggest_property_by_cidade($user_data->user_city);


						$default_suggest_property = $this->property_model->default_suggest_property();

						if (count($property_cidade) > 0) {

							$final['status'] = true;
							$final['message'] = 'Sugestao por cidade encontrados com sucesso';
							$final['response'] = $property_cidade;
							$final['note'] = 'Dados encontrados suggest_property_by_cidade()';
							$this->response($final, REST_Controller::HTTP_OK);
						} else if (count($property_estado) > 0) {

							$final['status'] = true;
							$final['message'] = 'Sugestao por estado encontrados com sucesso';
							$final['response'] = $property_estado;
							$final['note'] = 'Dados encontrados suggest_property_by_estado()';
							$this->response($final, REST_Controller::HTTP_OK);
						} else {

							if (count($default_suggest_property) > 0) {

								$final['status'] = true;
								$final['message'] = 'Sugestao por cidade default encontrados com sucesso';
								$final['response'] = $default_suggest_property;
								$final['note'] = 'Dados encontrados suggest_property_by_cidade()';
								$this->response($final, REST_Controller::HTTP_OK);
							} else {

								$final['status'] = false;
								$final['message'] = 'Nenhum imovel default encontrdo.';
								$final['note'] = 'Erro em get_user()';
								$this->response($final, REST_Controller::HTTP_OK);
							}
						}
					} else {

						$final['status'] = false;
						$final['message'] = 'Ocorreu um erro ao identificar usuario.';
						$final['note'] = 'Erro em get_user()';
						$this->response($final, REST_Controller::HTTP_OK);
					}
				} else {

					$final['status'] = false;
					$final['message'] = 'Sua sessão expirou.';
					$final['note'] = 'Erro em $decodedToken["status"]';
					$this->response($final, REST_Controller::HTTP_OK);
				}
			} else {

				$final['status'] = false;
				$final['message'] = 'Falha na autenticação.';
				$final['note'] = 'Erro em validateToken()';

				$this->response($final, REST_Controller::HTTP_OK);
			}
		}
	}

	public function get_suggest_client_brokers_post()
	{

		$this->form_validation->set_rules('user_id', 'ID do usuario', 'trim|required');


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

						$broker_estado = $this->broker_model->suggest_broker_by_estado($user_data->user_state);
						$broker_cidade = $this->broker_model->suggest_broker_by_cidade($user_data->user_city);

						$default_broker_cidade = $this->broker_model->default_suggest_broker();


						if (count($broker_cidade) > 0) {

							$final['status'] = true;
							$final['message'] = 'Sugestao por cidade encontrados com sucesso';
							$final['response'] = $broker_cidade;
							$final['note'] = 'Dados encontrados suggest_property_by_cidade()';
							$this->response($final, REST_Controller::HTTP_OK);
						} else if (count($broker_estado) > 0) {

							$final['status'] = true;
							$final['message'] = 'Sugestao por estado encontrados com sucesso';
							$final['response'] = $broker_estado;
							$final['note'] = 'Dados encontrados suggest_property_by_estado()';
							$this->response($final, REST_Controller::HTTP_OK);
						} else {

							if ($default_broker_cidade) {


								$final['status'] = true;
								$final['message'] = 'Sugestao por default encontrados com sucesso';
								$final['response'] = $default_broker_cidade;
								$final['note'] = 'Dados encontrados suggest_property_by_estado()';
								$this->response($final, REST_Controller::HTTP_OK);
							} else {

								$final['status'] = false;
								$final['message'] = 'Nenhuma sugestão encontrada';
								$final['note'] = 'Nenhuma sugestão encontrada';
								$this->response($final, REST_Controller::HTTP_OK);
							}
						}
					} else {

						$final['status'] = false;
						$final['message'] = 'Ocorreu um erro ao identificar usuario.';
						$final['note'] = 'Erro em get_user()';
						$this->response($final, REST_Controller::HTTP_OK);
					}
				} else {

					$final['status'] = false;
					$final['message'] = 'Sua sessão expirou.';
					$final['note'] = 'Erro em $decodedToken["status"]';
					$this->response($final, REST_Controller::HTTP_OK);
				}
			} else {

				$final['status'] = false;
				$final['message'] = 'Falha na autenticação.';
				$final['note'] = 'Erro em validateToken()';

				$this->response($final, REST_Controller::HTTP_OK);
			}
		}
	}

	public function get_client_feed_post()
	{

		$this->form_validation->set_rules('user_id', 'ID do usuario', 'trim|required');


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

						$get_client_following = $this->followers_model->get_following($user_id, 20);


						if ($get_client_following) {

							$response = array();

							foreach ($get_client_following as $p) {

								$response_a = array();

								$response_a['broker_data'] = $this->user_model->get_user($p->f_followed);
								$response_a['broker_post'] = $this->user_model->get_broker_propertys($p->f_followed, 1);

								$response[] = $response_a;
							}

							// print_r($response); 

							$final['status'] = true;
							$final['message'] = 'Posts encontrados com sucesso.';
							$final['response'] = $response;
							$final['note'] = 'Dados encontrados suggest_property_by_cidade()';
							$this->response($final, REST_Controller::HTTP_OK);
						} else {

							$final['status'] = false;
							$final['message'] = 'Nenhuma postagem encontrada.';
							$final['note'] = 'Nenhuma postagem encontrada.';
							$this->response($final, REST_Controller::HTTP_OK);
						}
						
					} else {

						$final['status'] = false;
						$final['message'] = 'Ocorreu um erro ao identificar usuario.';
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


	// Cliente dashboard
	public function get_leads_post()
	{

		$this->form_validation->set_rules('user_id', 'ID do usuario', 'trim|required');


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

					$broker_id = $this->input->post('user_id');
					$leads_data =  $this->user_model->get_leads($broker_id);

					if ($leads_data) {

						$response = array();

						foreach ($leads_data as $d) {

							$d->lead_data = $this->user_model->get_user($d->favorit_user_id);
							$d->property_data = $this->property_model->get_property($d->favorit_property_id);

							$response[] = $d;
						}

						if ($response) {

							$final['status'] = true;
							$final['message'] = 'Leads com sucesso.';
							$final['response'] = $response;
							$final['note'] = 'Dados encontrados suggest_property_by_cidade()';
							$this->response($final, REST_Controller::HTTP_OK);
						} else {

							$final['status'] = false;
							$final['message'] = 'Nenhuma lead encontrada.';
							$final['note'] = 'Nenhuma lead encontrada.';
							$this->response($final, REST_Controller::HTTP_OK);
						}
					} else {

						$final['status'] = false;
						$final['message'] = 'Ocorreu um erro ao identificar usuario.';
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
}

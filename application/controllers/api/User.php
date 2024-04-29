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
			$user_auth_type = $this->input->post('user_type');
			$preferences_data = json_decode($this->input->post('preferences_data'));

			echo $preferences_data;
			// $preferences_count = count(explode(",", $preferences_data));
			// $preferences_data = explode(",", $preferences_data);


			// $this->response($preferences_data, REST_Controller::HTTP_OK);

			// if ($preferences_count < 5) {

			// 	$final['status'] = false;
			// 	$final['message'] = 'Selecione pelo menos 5 características.';
			// 	$final['note'] = '$preferences_count < 5';

			// 	$this->response($final, REST_Controller::HTTP_OK);
			// } else if ($preferences_count > 5) {

			// 	$final['status'] = false;
			// 	$final['message'] = 'Selecione no máximo 5 características.';
			// 	$final['note'] = '$preferences_count > 5';

			// 	$this->response($final, REST_Controller::HTTP_OK);
			// } else {

			// 	if ($this->user_model->get_user($user_id)) {



			// 		foreach ($preferences_data as $p) {

			// 			if ($this->user_model->check_user_preferences($user_id, $p)) {
			// 			} else {

			// 				$this->user_model->add_user_preferences($p, $user_id, $user_auth_type);
			// 			}
			// 		}

			// 		$final['status'] = true;
			// 		$final['message'] = 'Preferencias adicionadas com sucesso.';
			// 		$final['note'] = 'Sucessoadd_user_preferences()';

			// 	} else {

			// 		$final['status'] = false;
			// 		$final['message'] = 'ID do usuário inválido.';
			// 		$final['note'] = 'Erro no get_user($user_id)';

			// 		$this->response($final, REST_Controller::HTTP_OK);
			// 	}
			// }
		}
	}
}

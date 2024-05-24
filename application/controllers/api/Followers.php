<?php
defined('BASEPATH') or exit('No direct script access allowed');

require(APPPATH . '/libraries/REST_Controller.php');

use Restserver\Libraries\REST_Controller;

class Followers extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Authorization_Token');
        $this->load->model('user_model');
        $this->load->model('email_model');
        $this->load->model('broker_model');
        $this->load->model('location_model');
        $this->load->model('followers_model');
        $this->load->model('rating_model');
    }

    public function check_follow_post()
    {

        // set validation rules
        $this->form_validation->set_rules('f_following', 'ID do seguidor', 'trim|required');
        $this->form_validation->set_rules('f_followed', 'ID do seguido', 'trim|required');


        if ($this->form_validation->run() === false) {


            $final['status'] = false;
            $final['message'] = validation_errors();
            $final['note'] = 'Erro no formulário.';

            $this->response($final, REST_Controller::HTTP_OK);

        } else {

            // set variables from the form
            $data['f_following'] = $this->input->post('f_following');
            $data['f_followed']    = $this->input->post('f_followed');
            $data['f_date']    = date('Y-m-d H:i:s');
            $data['is_deleted']    = 0;


            if ($this->followers_model->check_follower($data['f_following'], $data['f_followed'])) {

                $final['status'] = true;
                $final['response'] = $data;
                $final['message'] = 'Voce já segue este usuario';
                $final['note'] = 'Voce já segue este usuario';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            } else {

                $final['status'] = false;
                $final['response'] = $data;

                $final['message'] = 'Voce ainda não segue.';
                $final['note'] = 'Voce ainda não segue.';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            }

           
        }
    }

    public function to_follow_post()
    {

        // set validation rules
        $this->form_validation->set_rules('f_following', 'ID do seguidor', 'trim|required');
        $this->form_validation->set_rules('f_followed', 'ID do seguido', 'trim|required');


        if ($this->form_validation->run() === false) {


            $final['status'] = false;
            $final['message'] = validation_errors();
            $final['note'] = 'Erro no formulário.';

            $this->response($final, REST_Controller::HTTP_OK);
        } else {

            // set variables from the form
            $data['f_following'] = $this->input->post('f_following');
            $data['f_followed']    = $this->input->post('f_follower');
            $data['f_date']    = date('Y-m-d H:i:s');
            $data['is_deleted']    = 0;


            if ($this->followers_model->check_follower($data['f_following'], $data['f_followed'])) {

                $final['status'] = false;
                $final['message'] = 'Voce já segue este usuario';
                $final['note'] = 'Voce já segue este usuario';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            }

            if ($this->followers_model->to_follow($data)) {

                $final['status'] = true;
                $final['message'] = 'Seguindo com sucesso';
                $final['note'] = 'Seguindo com sucesso';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            } else {

                $final['status'] = false;
                $final['message'] = 'Houve um problema ao seguir. Tente novamente.';
                $final['note'] = 'Houve um problema ao seguir. Tente novamente.';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            }
        }
    }

    public function to_unfollow_post()
    {
        // set validation rules
        $this->form_validation->set_rules('f_following', 'ID do seguidor', 'trim|required');
        $this->form_validation->set_rules('f_followed', 'ID do seguido', 'trim|required');


        if ($this->form_validation->run() === false) {


            $final['status'] = false;
            $final['message'] = validation_errors();
            $final['note'] = 'Erro no formulário.';

            $this->response($final, REST_Controller::HTTP_OK);
        } else {

            // set variables from the form
            $data['f_following'] = $this->input->post('f_following');
            $data['f_followed']    = $this->input->post('f_followed');

            if ($this->followers_model->to_unfollow($data['f_following'], $data['f_followed'])) {

                $final['status'] = true;
                $final['message'] = 'Deseguindo com sucesso';
                $final['note'] = 'Deseguindo com sucesso';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            } else {

                $final['status'] = false;
                $final['message'] = 'Houve um problema ao deseguir. Tente novamente.';
                $final['note'] = 'Houve um problema ao deseguir. Tente novamente.';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            }
        }
    }

    // Quem o cliente está seguindo
    public function get_client_following_post()
    {
        $this->form_validation->set_rules('user_id', 'ID do seguidor', 'trim|required');


        if ($this->form_validation->run() === false) {


            $final['status'] = false;
            $final['message'] = validation_errors();
            $final['note'] = 'Erro no formulário.';

            $this->response($final, REST_Controller::HTTP_OK);
        } else {

            // set variables from the form
            $data['user_id'] = $this->input->post('user_id');

            $following_data = $this->followers_model->get_following($data['user_id']);

            if ($following_data) {

                $response = array();

                foreach ($following_data as $f) {

                    $response_a =  array();

                    $response_a['f_data'] = $f;
                    $response_a['followed_data'] = $this->user_model->get_user($f->f_followed);

                    $response[] = $response_a;
                }

                $final['status'] = true;
                $final['response'] = $response;
                $final['message'] = 'Encontrado com sucesso quem voce segue';
                $final['note'] = 'Encontrado com sucesso quem voce segue';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            } else {

                $final['status'] = false;
                $final['message'] = 'Voce não está seguindo ninguém.';
                $final['note'] = 'Voce não está seguindo ninguém.';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            }
        }
    }

    public function search_client_following_post()
    {
        $this->form_validation->set_rules('user_id', 'ID do seguidor', 'trim|required');
        $this->form_validation->set_rules('query', 'Nome do Usuario', 'trim|required');


        if ($this->form_validation->run() === false) {


            $final['status'] = false;
            $final['message'] = validation_errors();
            $final['note'] = 'Erro no formulário.';

            $this->response($final, REST_Controller::HTTP_OK);
        } else {

            // set variables from the form
            $data['user_id'] = $this->input->post('user_id');
            $data['query'] = $this->input->post('query');

            $following_data = $this->followers_model->search_following($data['user_id'],  $data['query']);

            if ($following_data) {

                $response = array();

                foreach ($following_data as $f) {

                    $response_a =  array();

                    $response_a['f_data'] = $f;
                    $response_a['followed_data'] = $this->user_model->get_user($f->f_followed);

                    $response[] = $response_a;
                }

                $final['status'] = true;
                $final['response'] = $response;
                $final['message'] = 'Encontrado com sucesso quem voce segue';
                $final['note'] = 'Encontrado com sucesso quem voce segue';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            } else {

                $final['status'] = false;
                $final['message'] = 'Voce não está seguindo ninguém.';
                $final['note'] = 'Voce não está seguindo ninguém.';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            }
        }
    }

    // Quem segue voce
    public function get_broker_followers_post()
    {
        $this->form_validation->set_rules('user_id', 'ID do seguidor', 'trim|required');


        if ($this->form_validation->run() === false) {


            $final['status'] = false;
            $final['message'] = validation_errors();
            $final['note'] = 'Erro no formulário.';

            $this->response($final, REST_Controller::HTTP_OK);
        } else {

            // set variables from the form
            $data['user_id'] = $this->input->post('user_id');

            $followers_data = $this->followers_model->get_followers($data['user_id']);

            if ($followers_data) {

                $response = array();

                foreach ($followers_data as $f) {

                    $response_a =  array();

                    $response_a['f_data'] = $f;
                    $response_a['following_data'] = $this->user_model->get_user($f->f_following);

                    $response[] = $response_a;
                }

                $final['status'] = true;
                $final['response'] = $response;
                $final['message'] = 'Encontrado com sucesso quem segue voce';
                $final['note'] = 'Encontrado com sucesso quem segue voce';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            } else {

                $final['status'] = false;
                $final['message'] = 'Ninguem segue voce.';
                $final['note'] = 'Ninguem segue voce.';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            }
        }
    }

    public function search_broker_followers_post()
    {
        $this->form_validation->set_rules('user_id', 'ID do seguidor', 'trim|required');
        $this->form_validation->set_rules('query', 'Nome do Usuario', 'trim|required');


        if ($this->form_validation->run() === false) {


            $final['status'] = false;
            $final['message'] = validation_errors();
            $final['note'] = 'Erro no formulário.';

            $this->response($final, REST_Controller::HTTP_OK);
        } else {

            // set variables from the form
            $data['user_id'] = $this->input->post('user_id');
            $data['query'] = $this->input->post('query');

            $followers_data = $this->followers_model->search_followers($data['user_id'],  $data['query']);

            if ($followers_data) {

                $response = array();

                foreach ($followers_data as $f) {

                    $response_a =  array();

                    $response_a['f_data'] = $f;
                    $response_a['following_data'] = $this->user_model->get_user($f->f_following);

                    $response[] = $response_a;
                }

                $final['status'] = true;
                $final['response'] = $response;
                $final['message'] = 'Encontrado com sucesso quem segue voce';
                $final['note'] = 'Encontrado com sucesso quem segue voce';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            } else {

                $final['status'] = false;
                $final['message'] = 'Ninguem segue voce.';
                $final['note'] = 'Ninguem segue voce.';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            }
        }
    }
}

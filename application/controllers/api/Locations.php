<?php
defined('BASEPATH') or exit('No direct script access allowed');

require(APPPATH . '/libraries/REST_Controller.php');

use Restserver\Libraries\REST_Controller;

class Locations extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Authorization_Token');
        $this->load->model('user_model');
        $this->load->model('email_model');
        $this->load->model('broker_model');
        $this->load->model('location_model');
    }

    public function get_locations_post()
    {


        $headers = $this->input->request_headers();

        if (isset($headers['Authorization'])) {

            $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);

            if ($decodedToken['status']) {

                $locations =  $this->location_model->get_locations();

                if ($locations) {

                    $final['status'] = true;
                    $final['message'] = 'Locations encontradas com sucesso.';
                    $final['response'] = $locations;
                    $final['note'] = 'Dados   encontrados get_broker_propertys()';

                    $this->response($final, REST_Controller::HTTP_OK);
                } else {

                    $final['status'] = false;
                    $final['message'] = 'Nenhum Locations encontrada.';
                    $final['note'] = 'Erro em get_broker_propertys()';

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

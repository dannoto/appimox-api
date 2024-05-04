<?php
defined('BASEPATH') or exit('No direct script access allowed');

require(APPPATH . '/libraries/REST_Controller.php');

use Restserver\Libraries\REST_Controller;

class Propertys extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Authorization_Token');
        $this->load->model('user_model');
        $this->load->model('email_model');
        $this->load->model('broker_model');
    }



    public function broker_propriety_post()
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
                    $_broker_propertys =  $this->broker_model->get_broker_propertys($user_id);

                    if ($_broker_propertys) {

                        $final['status'] = true;
                        $final['message'] = 'Imóveis encontradas com sucesso.';
                        $final['response'] = $_broker_propertys;
                        $final['note'] = 'Dados   encontrados get_broker_propertys()';

                        $this->response($final, REST_Controller::HTTP_OK);
                    } else {

                        $final['status'] = false;
                        $final['message'] = 'Nenhum imoveil encontrado.';
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

    public function search_broker_propertys_post()
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

                    $_broker_propertys =  $this->broker_model->search_broker_propertys($user_id, $query);

                    if ($_broker_propertys) {

                        $final['status'] = true;
                        $final['message'] = 'Imóveis encontradas com sucesso.';
                        $final['response'] = $_broker_propertys;
                        $final['note'] = 'Dados   encontrados get_broker_propertys()';

                        $this->response($final, REST_Controller::HTTP_OK);
                    } else {

                        $final['status'] = false;
                        $final['message'] = 'Nenhum imoveil encontrado.';
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

    public function add_broker_property_post()
    {

        $this->form_validation->set_rules('property_user_id', 'User ID', 'trim|required');
        $this->form_validation->set_rules('property_title', 'Título do Imóvel', 'trim|required');
        $this->form_validation->set_rules('property_type', 'Tipo do Imóvel', 'trim|required');
        $this->form_validation->set_rules('property_type_offer', 'Tipo de Oferta do Imóvel', 'trim|required');
        $this->form_validation->set_rules('property_price', 'Preço do Imóvel', 'trim|required');
        $this->form_validation->set_rules('property_area', 'Área do Imóvel', 'trim|required');

        $this->form_validation->set_rules('property_function', 'Função do Imóvel', 'trim|required');
        $this->form_validation->set_rules('property_disponibility', 'Disponibilidade do Imóvel', 'trim|required');
        $this->form_validation->set_rules('property_exclusive', 'Exclusividade do Imóvel', 'trim|required');

        $this->form_validation->set_rules('property_condominio', 'Valor do Condominio do Imóvel', 'numeric');
        $this->form_validation->set_rules('property_iptu', 'IPTU do Imóvel', 'numeric');
        $this->form_validation->set_rules('property_room', 'Qtd de Quartos do Imóvel', 'integer');
        $this->form_validation->set_rules('property_bathroom', 'Qtd de Banheiros do Imóvel', 'integer');
        $this->form_validation->set_rules('property_places', 'Qtd de cômodos do Imóvel', 'integer');

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

                    $data['property_user_id'] = $this->input->post('property_user_id');
                    $data['property_title'] = $this->input->post('property_title');
                    $data['property_type'] = $this->input->post('property_type');
                    $data['property_type_offer'] = $this->input->post('property_type_offer');
                    $data['property_price'] = $this->input->post('property_price');
                    $data['property_area'] = $this->input->post('property_area');
                    $data['property_function'] = $this->input->post('property_function');
                    $data['property_disponibility'] = $this->input->post('property_disponibility');
                    $data['property_exclusive'] = $this->input->post('property_exclusive');
                    $data['property_address'] = $this->input->post('location_address');


                    $data['property_condominio'] = $this->input->post('property_condominio');
                    $data['property_iptu'] = $this->input->post('property_iptu');
                    $data['property_room'] = $this->input->post('property_room');
                    $data['property_bathroom'] = $this->input->post('property_bathroom');
                    $data['property_places'] = $this->input->post('property_places');
                    $data['is_deleted'] = 0;




                    // Images

                    // Main
                    $path = 'public/images/property/';
                    $property_main_image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->input->post('property_main_image')));

                    $file_name = uniqid() . '.jpg';

                    $data['property_main_image'] = $path . $file_name;

                    if (file_put_contents($data['property_main_image'], $property_main_image)) {
                        // echo 'Imagem salva com sucesso em: ' . $data['property_main_image'];
                    } else {
                        // echo 'Erro ao salvar a imagem.';
                    }
                    // Main


                    // print_r($data);

                    // $_broker_propertys =  $this->broker_model->search_broker_propertys($user_id, $query);
                    $porperty_id = $this->broker_model->add_broker_property($data);
                    if ($porperty_id) {

                        // Adding Location
                        $data_location['property_latitude'] = $this->input->post('location_latitude');
                        $data_location['property_longitude'] = $this->input->post('location_longitude');
                        $data_location['property_address'] = $this->input->post('location_address');
                        $data_location['property_id'] = $porperty_id;
                        $data_location['property_broker'] = $this->input->post('property_user_id');
                        $data_location['property_name'] = $this->input->post('property_title');
                        $data_location['is_deleted'] = 0;

                        // Adding Location

                        $location_id = $this->broker_model->add_broker_property_location($data_location);

                        if ($location_id) {

                            $update_data['property_location_id'] = $location_id;

                            $this->broker_model->update_broker_property($porperty_id, $update_data);

                            $final['status'] = true;
                            $final['property_id'] =  $porperty_id;
                            $final['location_id'] =  $location_id;
                            $final['message'] = 'Imóveil e dados adicionado com sucesso.';
                            $final['response'] = $data;
                            $final['note'] = 'add_broker_property_location() e add_broker_property()';

                            $this->response($final, REST_Controller::HTTP_OK);
                        } else {
                            $final['status'] = false;
                            $final['message'] = 'Erro ao adicionar locatoin do imovel.';
                            $final['note'] = 'Erro em add_broker_property_location()';

                            $this->response($final, REST_Controller::HTTP_OK);
                        }
                    } else {

                        $final['status'] = false;
                        $final['message'] = 'Erro ao adicionar imovel.';
                        $final['note'] = 'Erro em add_broker_property()';

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

    public function add_broker_property_others_images_post()
    {

        $this->form_validation->set_rules('property_user_id', 'User ID', 'trim|required');
        $this->form_validation->set_rules('property_id', 'Imóvel ID', 'trim|required');
        $this->form_validation->set_rules('property_location_id', 'Localização ID', 'trim|required');


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

                    $data['property_user_id'] = $this->input->post('property_user_id');
                    $data['property_id'] = $this->input->post('property_id');
                    $data['property_location_id'] = $this->input->post('property_location_id');

                    $data['is_deleted'] = 0;

                    // Main
                    $path = 'public/images/property/';
                    $property_main_image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->input->post('property_image')));

                    $file_name = uniqid() . '.jpg';

                    $data['property_image'] = $path . $file_name;

                    if (file_put_contents($data['property_image'], $property_main_image)) {
                      
                    } else {
                       
                    }
                  
                    if ( $this->broker_model->add_broker_property_images($data)) {

                        $final['status'] = true;
                        $final['message'] = 'Imagem adicionada com sucesso.';
                        $final['response'] = $data;
                        $final['note'] = 'add_broker_property_images';

                        $this->response($final, REST_Controller::HTTP_OK);
                    } else {

                        $final['status'] = false;
                        $final['message'] = 'Erro ao adicionar imagem.';
                        $final['note'] = 'Erro em add_broker_property_images()';

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

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
        $this->load->model('property_model');
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

        // location
        $this->form_validation->set_rules('property_logradouro', 'Logradouro do Imóvel', 'trim|required');
        $this->form_validation->set_rules('property_bairro', 'Bairro do Imóvel', 'trim|required');
        $this->form_validation->set_rules('property_cep', 'CEP do Imóvel', 'required');
        $this->form_validation->set_rules('property_cidade', 'Cidade do Imóvel', 'integer');
        $this->form_validation->set_rules('property_estado', 'Estado do Imóvel', 'integer');


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



                    $data['property_condominio'] = $this->input->post('property_condominio');
                    $data['property_iptu'] = $this->input->post('property_iptu');
                    $data['property_room'] = $this->input->post('property_room');

                    $data['property_bathroom'] = $this->input->post('property_bathroom');
                    $data['property_places'] = $this->input->post('property_places');


                    // localizaçao
                    $data['property_logradouro'] = $this->input->post('property_logradouro');
                    $data['property_bairro'] = $this->input->post('property_bairro');
                    $data['property_numero'] = $this->input->post('property_numero');
                    $data['property_cep'] = $this->input->post('property_cep');
                    $data['property_cidade'] = $this->input->post('property_cidade');
                    $data['property_estado'] = $this->input->post('property_estado');

                    $address_comp = $data['property_logradouro'] ."". $data['property_numero'] . ", " . $data['property_bairro'] . " | " . $data['property_cidade'] . " - " . $data['property_estado'] . ", " . $data['property_cep'];
                    $data['property_address'] = $address_comp;

                    // localização

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


    
                        $data['property_numero'] = ', nº ' . $this->input->post('property_numero');
              

                        $address_comp = $data['property_logradouro'] ."". $data['property_numero'] . ", " . $data['property_bairro'] . " | " . $data['property_cidade'] . " - " . $data['property_estado'] . ", " . $data['property_cep'];
                        // Adding Location
                        $data_location['property_latitude'] = $this->input->post('location_latitude');
                        $data_location['property_longitude'] = $this->input->post('location_longitude');
                        // $data_location['property_address'] = $this->input->post('location_address');
                        $data_location['property_address'] = $address_comp;
                        $data_location['property_id'] = $porperty_id;
                        $data_location['property_broker'] = $this->input->post('property_user_id');
                        $data_location['property_name'] = $this->input->post('property_title');
                        $data_location['property_place_id'] = $this->input->post('property_place_id');
                        $data_location['is_deleted'] = 0;

                        // Adding Location

                        $location_id = $this->broker_model->add_broker_property_location($data_location);

                        if ($location_id) {

                            $update_data['property_location_id'] = $location_id;

                            $this->broker_model->update_broker_property($porperty_id, $update_data);

                            $final['status'] = true;
                            $final['property_id'] =  $porperty_id;
                            $final['property_location_id'] =  $location_id;
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

    public function update_broker_property_post()
    {
        $this->form_validation->set_rules('property_id', 'Propriedade ID', 'trim|required');
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

        // location
        $this->form_validation->set_rules('property_logradouro', 'Logradouro do Imóvel', 'integer');
        $this->form_validation->set_rules('property_bairro', 'Bairro do Imóvel', 'integer');
        $this->form_validation->set_rules('property_cep', 'CEP do Imóvel', 'integer');
        $this->form_validation->set_rules('property_cidade', 'Cidade do Imóvel', 'integer');
        $this->form_validation->set_rules('property_estado', 'Estado do Imóvel', 'integer');
        // location


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

                    $property_id = $this->input->post('property_id');
                    $property_location_id = $this->input->post('property_location_id');
                    $data['property_user_id'] = $this->input->post('property_user_id');
                    $data['property_title'] = $this->input->post('property_title');
                    $data['property_type'] = $this->input->post('property_type');
                    $data['property_type_offer'] = $this->input->post('property_type_offer');
                    $data['property_price'] = $this->input->post('property_price');
                    $data['property_area'] = $this->input->post('property_area');
                    $data['property_function'] = $this->input->post('property_function');
                    $data['property_disponibility'] = $this->input->post('property_disponibility');
                    $data['property_exclusive'] = $this->input->post('property_exclusive');

                    $data['property_condominio'] = $this->input->post('property_condominio');
                    $data['property_iptu'] = $this->input->post('property_iptu');
                    $data['property_room'] = $this->input->post('property_room');

                    $data['property_bathroom'] = $this->input->post('property_bathroom');
                    $data['property_places'] = $this->input->post('property_places');

                    // $data['property_location_id'] = $this->input->post('property_location_id'); //*

                    // localizaçao
                    $data['property_logradouro'] = $this->input->post('property_logradouro');
                    $data['property_bairro'] = $this->input->post('property_bairro');
                    $data['property_numero'] = $this->input->post('property_numero');
                    $data['property_cep'] = $this->input->post('property_cep');
                    $data['property_cidade'] = $this->input->post('property_cidade');
                    $data['property_estado'] = $this->input->post('property_estado');
                    // localização

                    $address_comp = $data['property_logradouro'] ."". $data['property_numero'] . ", " . $data['property_bairro'] . " | " . $data['property_cidade'] . " - " . $data['property_estado'] . ", " . $data['property_cep'];
                    $data['property_address'] = $address_comp;

                    $data['is_deleted'] = 0;
                    // Images

                    // Main

                    if (strlen($this->input->post('property_main_image')) > 0) {


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
                    }

                    if ($this->broker_model->update_broker_property($property_id, $data)) {

                        // Adding Location
                        $data['property_numero'] = ', nº ' . $this->input->post('property_numero');


                        $address_comp = $data['property_logradouro'] ."". $data['property_numero'] . ", " . $data['property_bairro'] . " | " . $data['property_cidade'] . " - " . $data['property_estado'] . ", " . $data['property_cep'];


                        $data_location['property_latitude'] = $this->input->post('location_latitude');
                        $data_location['property_longitude'] = $this->input->post('location_longitude');
                        $data_location['property_address'] = $address_comp;
                        $data_location['property_id'] = $property_id;
                        $data_location['property_broker'] = $this->input->post('property_user_id');
                        $data_location['property_name'] = $this->input->post('property_title');
                        $data_location['property_place_id'] = $this->input->post('property_place_id');
                        $data_location['is_deleted'] = 0;

                        // Adding Location
                        if ($this->broker_model->update_broker_property_location($property_location_id, $data_location)) {

                            $final['status'] = true;
                            $final['property_id'] =  $property_id;
                            $final['property_location_id'] =  $property_location_id;
                            $final['message'] = 'Imóveil e dados atualizados com sucesso.';
                            $final['response'] = $data;
                            $final['note'] = 'add_broker_property_location() e add_broker_property()';

                            $this->response($final, REST_Controller::HTTP_OK);
                        } else {
                            $final['status'] = false;
                            $final['message'] = 'Erro ao atualizar location do imovel.';
                            $final['note'] = 'Erro em add_broker_property_location()';

                            $this->response($final, REST_Controller::HTTP_OK);
                        }
                    } else {

                        $final['status'] = false;
                        $final['message'] = 'Erro ao atualizar imovel.';
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

                    if ($this->broker_model->add_broker_property_images($data)) {

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

    public function get_broker_property_data_post()
    {

        $this->form_validation->set_rules('property_user_id', 'User ID', 'trim|required');
        $this->form_validation->set_rules('property_id', 'Imóvel ID', 'trim|required');


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


                    // if ($this->broker_model->check_edit_property_data($data['property_user_id'], $data['property_id'])) {

                    $final['status'] = true;
                    $final['message'] = 'Imovel recuperados com sucesso.';
                    $final['property_data'] = $this->broker_model->get_broker_property($data['property_id']);
                    $final['property_location_data'] = $this->broker_model->get_broker_property_location($data['property_id']);
                    $final['property_imagens_data'] = $this->broker_model->get_broker_property_images($data['property_id']);
                    $final['note'] = 'get_broker_property_data';

                    $this->response($final, REST_Controller::HTTP_OK);
                    // } else {

                    //     $final['status'] = false;
                    //     $final['message'] = 'Você não é proprietário deste imóvel.';
                    //     $final['note'] = 'Erro em get_broker_property_data()';

                    //     $this->response($final, REST_Controller::HTTP_OK);
                    // }
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

    public function delete_broker_property_post()
    {
        $this->form_validation->set_rules('property_user_id', 'User ID', 'trim|required');
        $this->form_validation->set_rules('property_id', 'Imóvel ID', 'trim|required');


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

                    if ($this->broker_model->delete_broker_property($data['property_user_id'], $data['property_id'])) {

                        $property_data = $this->broker_model->get_broker_property($data['property_id']);

                        $this->broker_model->delete_broker_property_location($property_data->property_location_id);

                        $final['status'] = true;
                        $final['property_id'] = $data['property_id'];
                        $final['property_location_id'] = $property_data->property_location_id;
                        $final['message'] = 'Imovel excluido com sucesso.';
                        $final['note'] = 'delete_broker_property';

                        $this->response($final, REST_Controller::HTTP_OK);
                    } else {

                        $final['status'] = false;
                        $final['message'] = 'Erro ao excluir imovel.';
                        $final['note'] = 'Erro em delete_broker_property()';

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


    // ===
    public function get_broker_property_home_post()
    {

        $this->form_validation->set_rules('property_user_id', 'User ID', 'trim|required');
        $this->form_validation->set_rules('filter', 'User ID', 'trim|required');

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

                    $property_user_id = $this->input->post('property_user_id');
                    $filter = $this->input->post('filter');

                    $_broker_propertys =  $this->broker_model->search_broker_propertys_home($property_user_id, $filter);

                    if ($_broker_propertys) {

                        $final['status'] = true;
                        $final['message'] = 'Imóveis encontradas com sucesso.';
                        $final['response'] = $_broker_propertys;
                        $final['note'] = 'Dados   encontrados search_broker_propertys_home()';

                        $this->response($final, REST_Controller::HTTP_OK);
                    } else {

                        $final['status'] = false;
                        $final['message'] = 'Nenhum imovel encontrado.';
                        $final['response'] = $_broker_propertys;

                        $final['note'] = 'Erro em search_broker_propertys_home()';

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


    // 
    // search proprtyes 

    public function get_propertys_by_range_post()
    {

        $this->form_validation->set_rules('user_id', 'User ID', 'trim|required');

        if ($this->form_validation->run() == false) {

            $final['status'] = false;
            $final['message'] = validation_errors();
            $final['note'] = 'Erro no formulárioi.';

            $this->response($final, REST_Controller::HTTP_OK);
        } else {

            $headers = $this->input->request_headers();

            if (isset($headers['Authorization'])) {

                $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);

                if ($decodedToken['status']) {

                    $markers_data = str_replace('"', '', $this->input->post('markers_data'));
                    $markers_data = str_replace(']', '', $markers_data);
                    $markers_data = str_replace('[', '', $markers_data);

                    $markers_data = explode(",", $markers_data);

                    $propertys_data = array();


                    if (count($markers_data) > 0) {

                        foreach ($markers_data as $p) {

                            $property_id =  $this->property_model->get_property_by_location_id($p);
                            $property_data = $this->property_model->get_property($property_id);
                            $propertys_data[] = $property_data;
                        }


                        $final['status'] = true;
                        $final['message'] = 'Propriedades encontrados';
                        $final['response'] =  $propertys_data;
                        $final['note'] = 'Erro em $decodedToken["status"]';
                        $this->response($final);
                    } else {

                        $final['status'] = false;
                        $final['message'] = 'Nenhuma propriedade encontrada';
                        $final['note'] = 'Erro em $decodedToken["status"]';
                        $this->response($final);
                    }
                } else {

                    $final['status'] = false;
                    $final['message'] = 'Sua sessão expiroux.';
                    $final['note'] = 'Erro em $decodedToken["status"]';
                    $this->response($decodedToken);
                }
            } else {

                $final['status'] = false;
                $final['message'] = 'Falha na autenticaçãoy.';
                $final['note'] = 'Erro em validateToken()';

                $this->response($final, REST_Controller::HTTP_OK);
            }
        }
    }

    public function get_propertys_by_range_filter_post()
    {

        $this->form_validation->set_rules('user_id', 'User ID', 'trim|required');

        if ($this->form_validation->run() == false) {

            $final['status'] = false;
            $final['message'] = validation_errors();
            $final['note'] = 'Erro no formulárioi.';

            $this->response($final, REST_Controller::HTTP_OK);
        } else {

            $headers = $this->input->request_headers();

            if (isset($headers['Authorization'])) {

                $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);

                if ($decodedToken['status']) {

                    $markers_data = str_replace('"', '', $this->input->post('markers_data'));
                    $markers_data = str_replace(']', '', $markers_data);
                    $markers_data = str_replace('[', '', $markers_data);

                    $markers_data = explode(",", $markers_data);

                    $propertys_data = array();

                    // filters
                    $f_data['filter_type'] =  htmlspecialchars($this->input->post('filter_type'));
                    $f_data['filter_type_offer'] =  htmlspecialchars($this->input->post('filter_type_offer'));
                    $f_data['filter_room'] =  htmlspecialchars($this->input->post('filter_room'));
                    $f_data['filter_bathroom'] =  htmlspecialchars($this->input->post('filter_bathroom'));
                    $f_data['filter_places'] =  htmlspecialchars($this->input->post('filter_places'));
                    $f_data['filter_price_min'] =  htmlspecialchars($this->input->post('filter_price_min'));
                    $f_data['filter_price_max'] =  htmlspecialchars($this->input->post('filter_price_max'));
                    $f_data['filter_function'] =  htmlspecialchars($this->input->post('filter_function'));
                    $f_data['filter_disponibility'] =  htmlspecialchars($this->input->post('filter_disponibility'));
                    // filters

                    if (count($markers_data) > 0) {

                        foreach ($markers_data as $p) {

                            $property_id =  $this->property_model->get_property_by_location_id($p);
                            $property_data = $this->property_model->get_property_filter($property_id, $f_data);

                            if ($property_data) {
                                $propertys_data[] = $property_data;
                            }
                        }


                        $final['status'] = true;
                        $final['message'] = 'Propriedades encontrados';
                        $final['response'] =  $propertys_data;
                        $final['note'] = 'Erro em $decodedToken["status"]';
                        $this->response($final);
                    } else {

                        $final['status'] = false;
                        $final['message'] = 'Nenhuma propriedade encontrada';
                        $final['note'] = 'Erro em $decodedToken["status"]';
                        $this->response($final);
                    }
                } else {

                    $final['status'] = false;
                    $final['message'] = 'Sua sessão expiroux.';
                    $final['note'] = 'Erro em $decodedToken["status"]';
                    $this->response($decodedToken);
                }
            } else {

                $final['status'] = false;
                $final['message'] = 'Falha na autenticaçãoy.';
                $final['note'] = 'Erro em validateToken()';

                $this->response($final, REST_Controller::HTTP_OK);
            }
        }
    }

    // ====================

    public function get_broker_by_range_post()
    {

        $this->form_validation->set_rules('user_id', 'User ID', 'trim|required');

        if ($this->form_validation->run() == false) {

            $final['status'] = false;
            $final['message'] = validation_errors();
            $final['note'] = 'Erro no formulárioi.';

            $this->response($final, REST_Controller::HTTP_OK);
        } else {

            $headers = $this->input->request_headers();

            if (isset($headers['Authorization'])) {

                $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);

                if ($decodedToken['status']) {

                    $markers_data = str_replace('"', '', $this->input->post('markers_data'));
                    $markers_data = str_replace(']', '', $markers_data);
                    $markers_data = str_replace('[', '', $markers_data);

                    $markers_data = explode(",", $markers_data);

                    $brokers_data = array();


                    if (count($markers_data) > 0) {

                        foreach ($markers_data as $p) {

                            $broker_id =  $this->property_model->get_broker_by_location_id($p);
                            $broker_data = $this->property_model->get_broker($broker_id);


                            if ($broker_id) {

                                if ($broker_data) {


                                    $id_exists = false;
                                    foreach ($brokers_data as $existing_broker) {
                                        if ($existing_broker->id == $broker_data->id) {
                                            $id_exists = true;
                                            break;
                                        }
                                    }

                                    // Se o ID não existe, adiciona o corretor a brokers_data
                                    if (!$id_exists) {
                                        $brokers_data[] = $broker_data;
                                    }
                                }
                            }



                            // $brokers_data[] = $broker_data;
                        }


                        $final['status'] = true;
                        $final['message'] = 'Propriedades encontrados';
                        $final['response'] =  $brokers_data;
                        $final['note'] = 'Erro em $decodedToken["status"]';
                        $this->response($final);
                    } else {

                        $final['status'] = false;
                        $final['message'] = 'Nenhuma propriedade encontrada';
                        $final['note'] = 'Erro em $decodedToken["status"]';
                        $this->response($final);
                    }
                } else {

                    $final['status'] = false;
                    $final['message'] = 'Sua sessão expiroux.';
                    $final['note'] = 'Erro em $decodedToken["status"]';
                    $this->response($decodedToken);
                }
            } else {

                $final['status'] = false;
                $final['message'] = 'Falha na autenticaçãoy.';
                $final['note'] = 'Erro em validateToken()';

                $this->response($final, REST_Controller::HTTP_OK);
            }
        }
    }

    public function get_broker_by_range_filter_post()
    {

        $this->form_validation->set_rules('user_id', 'User ID', 'trim|required');

        if ($this->form_validation->run() == false) {

            $final['status'] = false;
            $final['message'] = validation_errors();
            $final['note'] = 'Erro no formulárioi.';

            $this->response($final, REST_Controller::HTTP_OK);
        } else {

            $headers = $this->input->request_headers();

            if (isset($headers['Authorization'])) {

                $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);

                if ($decodedToken['status']) {

                    $markers_data = str_replace('"', '', $this->input->post('markers_data'));
                    $markers_data = str_replace(']', '', $markers_data);
                    $markers_data = str_replace('[', '', $markers_data);

                    $markers_data = explode(",", $markers_data);

                    $brokers_data = array();

                    // filters
                    $f_data['selected_preferences'] =  htmlspecialchars($this->input->post('selected_preferences'));

                    // filters

                    if (count($markers_data) > 0) {

                        // foreach ($markers_data as $p) {

                        //     $broker_id =  $this->property_model->get_broker_by_location_id($p);
                        //     $broker_data = $this->property_model->filter_broker_by_preferences($broker_id,  $f_data['selected_preferences']);


                        //     $id_exists = false;
                        //     foreach ($brokers_data as $existing_broker) {
                        //         if ($existing_broker->id == $broker_data->id) {
                        //             $id_exists = true;
                        //             break;
                        //         }
                        //     }

                        //     // Se o ID não existe, adiciona o corretor a brokers_data
                        //     if (!$id_exists) {
                        //         $brokers_data[] = $broker_data;
                        //     }
                        // }

                        foreach ($markers_data as $p) {

                            $broker_id =  $this->property_model->get_broker_by_location_id($p);
                            $broker_data = $this->property_model->get_broker($broker_id);

                            if ($broker_id) {

                                if ($broker_data) {

                                    $id_exists = false;
                                    foreach ($brokers_data as $existing_broker) {
                                        if ($existing_broker->id == $broker_data->id) {
                                            $id_exists = true;
                                            break;
                                        }
                                    }

                                    // Se o ID não existe, adiciona o corretor a brokers_data
                                    if (!$id_exists) {
                                        $brokers_data[] = $broker_data;
                                    }
                                }
                            }



                            // $brokers_data[] = $broker_data;
                        }

                        $final['status'] = true;
                        $final['message'] = 'Propriedades encontrados';
                        $final['response'] =  $brokers_data;
                        $final['como ta chegando'] =  $f_data['selected_preferences'];
                        $final['note'] = 'Erro em $decodedToken["status"]';
                        $this->response($final);
                    } else {

                        $final['status'] = false;
                        $final['message'] = 'Nenhuma propriedade encontrada';
                        $final['note'] = 'Erro em $decodedToken["status"]';
                        $this->response($final);
                    }
                } else {

                    $final['status'] = false;
                    $final['message'] = 'Sua sessão expiroux.';
                    $final['note'] = 'Erro em $decodedToken["status"]';
                    $this->response($decodedToken);
                }
            } else {

                $final['status'] = false;
                $final['message'] = 'Falha na autenticaçãoy.';
                $final['note'] = 'Erro em validateToken()';

                $this->response($final, REST_Controller::HTTP_OK);
            }
        }
    }

    // ====================

    public function get_broker_associate_properties_post()
    {

        $this->form_validation->set_rules('user_id', 'User ID', 'trim|required');
        $this->form_validation->set_rules('broker_id', 'Broker ID', 'trim|required');

        if ($this->form_validation->run() == false) {

            $final['status'] = false;
            $final['message'] = validation_errors();
            $final['note'] = 'Erro no formulárioi.';

            $this->response($final, REST_Controller::HTTP_OK);
        } else {

            $headers = $this->input->request_headers();

            if (isset($headers['Authorization'])) {

                $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);

                if ($decodedToken['status']) {

                    $markers_data = str_replace('"', '', $this->input->post('markers_data'));
                    $markers_data = str_replace(']', '', $markers_data);
                    $markers_data = str_replace('[', '', $markers_data);

                    $markers_data = explode(",", $markers_data);

                    $propertys_data = array();

                    // filters
                    $broker_id_search =  htmlspecialchars($this->input->post('broker_id'));

                    // filters

                    if (count($markers_data) > 0) {

                        foreach ($markers_data as $p) {

                            $property_id =  $this->property_model->get_property_by_associate_broker_id($p, $broker_id_search);

                            if ($property_id) {
                                $property_data = $this->property_model->get_property($property_id);

                                if ($property_data) {
                                    $propertys_data[] = $property_data;
                                }
                            }
                        }

                        $final['status'] = true;
                        $final['message'] = 'Propriedades encontrados';
                        $final['response'] =  $propertys_data;
                        $final['note'] = 'Erro em $decodedToken["status"]';
                        $this->response($final);
                    } else {

                        $final['status'] = false;
                        $final['message'] = 'Nenhuma propriedade encontrada';
                        $final['note'] = 'Erro em $decodedToken["status"]';
                        $this->response($final);
                    }
                } else {

                    $final['status'] = false;
                    $final['message'] = 'Sua sessão expiroux.';
                    $final['note'] = 'Erro em $decodedToken["status"]';
                    $this->response($decodedToken);
                }
            } else {

                $final['status'] = false;
                $final['message'] = 'Falha na autenticaçãoy.';
                $final['note'] = 'Erro em validateToken()';

                $this->response($final, REST_Controller::HTTP_OK);
            }
        }
    }
}

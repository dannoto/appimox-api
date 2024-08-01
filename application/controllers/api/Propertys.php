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
        $this->load->model('partner_model');
        $this->load->model('plans_model');
    }

    public function web_get_propretys_post()
    {
        $this->form_validation->set_rules('user_id', 'User ID', 'trim|required');

        if ($this->form_validation->run() == false) {

            $final['status'] = false;
            $final['message'] = validation_errors();
            $final['note'] = 'Erro no formulário.';

            $this->response($final, REST_Controller::HTTP_OK);
        } else {



            $web_get_propertys =  $this->property_model->web_get_propertys();


            if ($web_get_propertys) {

                $final['status'] = true;
                $final['message'] = 'Imóveis encontradas com sucesso.';
                $final['response'] = $web_get_propertys;
                $final['note'] = 'Dados   encontrados get_broker_propertys()';

                $this->response($final, REST_Controller::HTTP_OK);
            } else {

                $final['status'] = false;
                $final['message'] = 'Nenhum imoveil encontrado.';
                $final['note'] = 'Erro em get_broker_propertys()';

                $this->response($final, REST_Controller::HTTP_OK);
            }
        }
    }

    public function web_search_propretys_post()
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


                    $f_data['filter_type'] =  htmlspecialchars($this->input->post('filter_type'));
                    $f_data['filter_type_offer'] =  htmlspecialchars($this->input->post('filter_type_offer'));
                    $f_data['filter_room'] =  htmlspecialchars($this->input->post('filter_room'));
                    $f_data['filter_bathroom'] =  htmlspecialchars($this->input->post('filter_bathroom'));
                    $f_data['filter_places'] =  htmlspecialchars($this->input->post('filter_places'));
                    $f_data['filter_price_min'] =  htmlspecialchars($this->input->post('filter_price_min'));
                    $f_data['filter_price_max'] =  htmlspecialchars($this->input->post('filter_price_max'));
                    $f_data['filter_function'] =  htmlspecialchars($this->input->post('filter_function'));
                    $f_data['filter_disponibility'] =  htmlspecialchars($this->input->post('filter_disponibility'));

                    $web_get_propertys =  $this->property_model->web_search_propertys($f_data);


                    if ($web_get_propertys) {

                        $final['status'] = true;
                        $final['message'] = 'Imóveis encontradas com sucesso.';
                        $final['response'] = $web_get_propertys;
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

    public function broker_propertys_partner_post()
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

                    $_total_broker_propertys = array();
                    $_broker_propertys =  $this->broker_model->get_broker_propertys($user_id);

                    foreach ($_broker_propertys as $c) {

                        if ($this->partner_model->check_exist_partner($c->id, $user_id)) {

                            $c->check_partner = true;
                            $_total_broker_propertys[] = $c;
                        } else {

                            $c->check_partner = false;
                            $_total_broker_propertys[] = $c;
                        }
                    }

                    if ($_broker_propertys) {

                        $final['status'] = true;
                        $final['message'] = 'Imóveis encontradas com sucesso.';
                        $final['response'] = $_total_broker_propertys;
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

        $this->form_validation->set_rules('property_age', 'Idade do Imóvel', 'integer');

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


                    $data['property_age'] = $this->input->post('property_age');


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

                    if (strlen($this->input->post('property_numero')) > 0) {
                        $p_numero = ', nº ' . $this->input->post('property_numero');
                    }


                    $address_comp = $data['property_logradouro'] . "" . $p_numero . ", " . $data['property_bairro'] . " | " . $this->property_model->get_cidade_label($data['property_cidade']) . " - " . $this->property_model->get_estado_label($data['property_estado']) . ", " . $data['property_cep'];
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

                    // =========== PLAN CONTROLLER ==============
                    $user_data = $this->user_model->get_user($data['property_user_id']);
                    $plan_data = $this->plans_model->get_plan($user_data->user_plan);

                    $count_total_property_limit = count($this->broker_model->get_broker_propertys($data['property_user_id']));
                    $limit_property_by_plan = $plan_data->plan_limit_images;

                    if ($limit_property_by_plan <= $count_total_property_limit) {

                        $final['status'] = false;
                        $final['message'] = "Você só pode adicionar até " . $limit_property_by_plan . " imóveis. Faça upgrade para aumentar o limite!";
                        $final['note'] = 'Erro em add_broker_property_location()';

                        $this->response($final, REST_Controller::HTTP_OK);
                    }
                    // else {

                    //     $final['status'] = false;
                    //     $final['message'] = "SUCESSO. Você só pode adicionar até ".$limit_property_by_plan." imóveis. Até agora voce ja adicionou ".".$count_total_property_limit."." !";
                    //     $final['note'] = 'Erro em add_broker_property_location()';

                    //     $this->response($final, REST_Controller::HTTP_OK);
                    // }
                    // =========================

                    $porperty_id = $this->broker_model->add_broker_property($data);

                    if ($porperty_id) {

                        if (strlen($this->input->post('property_numero')) > 0) {
                            $data['property_numero'] = ', nº ' . $this->input->post('property_numero');
                        }

                        $address_comp = $data['property_logradouro'] . "" . $data['property_numero'] . ", " . $data['property_bairro'] . " | " . $this->property_model->get_cidade_label($data['property_cidade']) . " - " . $this->property_model->get_estado_label($data['property_estado']) . ", " . $data['property_cep'];
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
                            $final['message'] = 'Imóvel e dados adicionado com sucesso.';
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
        $this->form_validation->set_rules('property_logradouro', 'Logradouro do Imóvel', 'trim|required');
        $this->form_validation->set_rules('property_bairro', 'Bairro do Imóvel', 'trim|required');
        $this->form_validation->set_rules('property_cep', 'CEP do Imóvel', 'required');
        $this->form_validation->set_rules('property_cidade', 'Cidade do Imóvel', 'integer');
        $this->form_validation->set_rules('property_estado', 'Estado do Imóvel', 'integer');
        // location

        $this->form_validation->set_rules('property_age', 'Idade do Imóvel', 'integer');



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

                    $data['property_age'] = $this->input->post('property_age');

                    // $data['property_location_id'] = $this->input->post('property_location_id'); //*

                    // localizaçao
                    $data['property_logradouro'] = $this->input->post('property_logradouro');
                    $data['property_bairro'] = $this->input->post('property_bairro');
                    $data['property_numero'] = $this->input->post('property_numero');
                    $data['property_cep'] = $this->input->post('property_cep');
                    $data['property_cidade'] = $this->input->post('property_cidade');
                    $data['property_estado'] = $this->input->post('property_estado');
                    // localização

                    if (strlen($this->input->post('property_numero')) > 0) {
                        $p_numero = ', nº ' . $this->input->post('property_numero');
                    }

                    $address_comp = $data['property_logradouro'] . "" . $p_numero . ", " . $data['property_bairro'] . " | " . $this->property_model->get_cidade_label($data['property_cidade']) . " - " . $this->property_model->get_estado_label($data['property_estado']) . ", " . $data['property_cep'];
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
                        if (strlen($this->input->post('property_numero')) > 0) {
                            $data['property_numero'] = ', nº ' . $this->input->post('property_numero');
                        }

                        $address_comp = $data['property_logradouro'] . "" . $data['property_numero'] . ", " . $data['property_bairro'] . " | " . $this->property_model->get_cidade_label($data['property_cidade']) . " - " . $this->property_model->get_estado_label($data['property_estado']) . ", " . $data['property_cep'];


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
                            $final['message'] = 'Imóvel e dados atualizados com sucesso.';
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

    public function delete_property_image_post()
    {

        $this->form_validation->set_rules('property_user_id', 'User ID', 'trim|required');
        $this->form_validation->set_rules('property_id', 'Imóvel ID', 'trim|required');
        $this->form_validation->set_rules('imagem_url', 'Imagem URL', 'trim|required');


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
                    $data['property_user_id'] = $this->input->post('property_user_id');
                    $data['imagem_url'] = $this->input->post('imagem_url');



                    if ($this->broker_model->delete_property_image($property_id, $data['property_user_id'], $data['imagem_url'])) {

                        $final['status'] = true;
                        $final['message'] = 'Imagem excluida com sucesso.';
                        $final['response'] = $data;
                        $final['note'] = 'add_broker_property_images';

                        $this->response($final, REST_Controller::HTTP_OK);
                    } else {

                        $final['status'] = false;
                        $final['message'] = 'Erro ao excluir imagem.';
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


                    if ($markers_data) {

                        foreach ($markers_data as $p) {

                            $property_id =  $this->property_model->get_property_by_location_id($p);
                            $property_data = $this->property_model->get_property($property_id);

                            // $propertys_data[] = $property_data;

                            // =========== ÓTIMO ============ 
                            $user_id = $this->input->post('user_id');
                            $user_preferences = $this->user_model->get_user_preferences($user_id);
                            // $broker_id = $this->property_model->get_broker_by_location_id($property_data->property_user_id);
                            // $broker_data = $this->property_model->get_broker($broker_id);

                            if ($user_preferences) {

                                $broker_preferences = $this->user_model->get_user_preferences($property_data->property_user_id);

                                $match_percentage = $this->calculate_match_percentage($user_preferences, $broker_preferences);
                                $property_data->match_percentage = $match_percentage;
                                $property_data->recommended = false;

                                $propertys_data[] = $property_data;
                            }
                        }

                        // Ordenar corretores pela porcentagem de correspondência em ordem decrescente
                        usort($propertys_data, function ($a, $b) {
                            return $b->match_percentage - $a->match_percentage;
                        });

                        // Definir os três melhores corretores como recomendados
                        for ($i = 0; $i < min(3, count($propertys_data)); $i++) {
                            $propertys_data[$i]->recommended = true;
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


    public function get_property_post()

    {

        $this->form_validation->set_rules('property_id', 'User ID', 'trim|required');

        if ($this->form_validation->run() == false) {

            $final['status'] = false;
            $final['message'] = validation_errors();
            $final['note'] = 'Erro no formulárioi.';

            $this->response($final, REST_Controller::HTTP_OK);
        } else {

            $property_id = $this->input->post('property_id');
            $property_data = $this->property_model->get_property($property_id);


            if ($property_data) {

                $final['status'] = true;
                $final['message'] = 'Propriedades encontrados';
                $final['response'] =  $property_data;
                $final['note'] = 'Erro em $decodedToken["status"]';
                $this->response($final);
            } else {

                $final['status'] = false;
                $final['message'] = 'Nenhuma propriedade encontrada';
                $final['note'] = 'Erro em $decodedToken["status"]';
                $this->response($final);
            }
        }
    }



    public function get_property_new_post()
    {

        $this->form_validation->set_rules('limit', 'User ID', 'trim|required');

        if ($this->form_validation->run() == false) {

            $final['status'] = false;
            $final['message'] = validation_errors();
            $final['note'] = 'Erro no formulárioi.';

            $this->response($final, REST_Controller::HTTP_OK);
        } else {

            $limit = $this->input->post('limit');


            $propertys = $this->property_model->get_property_new($limit);

            if ($propertys) {

                $final['status'] = true;
                $final['message'] = 'Propriedades encontrados';
                $final['response'] =  $propertys;
                $final['note'] = 'Erro em $decodedToken["status"]';
                $this->response($final);
            } else {

                $final['status'] = false;

                $final['message'] = 'Nenhuma propriedade encontrada';
                $final['note'] = 'Erro em $decodedToken["status"]';
                $this->response($final);
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

                            // if ($property_data) {
                            //     $propertys_data[] = $property_data;
                            // }
                            // // =========== ÓTIMO ============ 
                            $user_id = $this->input->post('user_id');
                            $user_preferences = $this->user_model->get_user_preferences($user_id);
                            // $broker_id = $this->property_model->get_broker_by_location_id($property_data->property_user_id);
                            // $broker_data = $this->property_model->get_broker($broker_id);

                            if ($user_preferences) {


                                if ($property_data->property_user_id) {

                                    $broker_preferences = $this->user_model->get_user_preferences($property_data->property_user_id);

                                    $match_percentage = $this->calculate_match_percentage($user_preferences, $broker_preferences);
                                    $property_data->match_percentage = $match_percentage;
                                    $property_data->recommended = false;


                                    if ($property_data) {
                                        $propertys_data[] = $property_data;
                                    }
                                }
                            }
                        }

                        // Ordenar corretores pela porcentagem de correspondência em ordem decrescente
                        usort($propertys_data, function ($a, $b) {
                            return $b->match_percentage - $a->match_percentage;
                        });

                        // Definir os três melhores corretores como recomendados
                        for ($i = 0; $i < min(3, count($propertys_data)); $i++) {
                            $propertys_data[$i]->recommended = true;
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

    // public function get_broker_by_range_post()
    // {

    //     $this->form_validation->set_rules('user_id', 'User ID', 'trim|required');

    //     if ($this->form_validation->run() == false) {

    //         $final['status'] = false;
    //         $final['message'] = validation_errors();
    //         $final['note'] = 'Erro no formulárioi.';

    //         $this->response($final, REST_Controller::HTTP_OK);
    //     } else {

    //         $headers = $this->input->request_headers();

    //         if (isset($headers['Authorization'])) {

    //             $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);

    //             if ($decodedToken['status']) {

    //                 $markers_data = str_replace('"', '', $this->input->post('markers_data'));
    //                 $markers_data = str_replace(']', '', $markers_data);
    //                 $markers_data = str_replace('[', '', $markers_data);

    //                 $markers_data = explode(",", $markers_data);

    //                 $brokers_data = array();


    //                 if (count($markers_data) > 0) {

    //                     foreach ($markers_data as $p) {

    //                         $broker_id =  $this->property_model->get_broker_by_location_id($p);
    //                         $broker_data = $this->property_model->get_broker($broker_id);


    //                         if ($broker_id) {

    //                             if ($broker_data) {


    //                                 $id_exists = false;
    //                                 foreach ($brokers_data as $existing_broker) {
    //                                     if ($existing_broker->id == $broker_data->id) {
    //                                         $id_exists = true;
    //                                         break;
    //                                     }
    //                                 }

    //                                 // Se o ID não existe, adiciona o corretor a brokers_data
    //                                 if (!$id_exists) {
    //                                     $brokers_data[] = $broker_data;
    //                                 }
    //                             }
    //                         }



    //                         // $brokers_data[] = $broker_data;
    //                     }


    //                     $final['status'] = true;
    //                     $final['message'] = 'Propriedades encontrados';
    //                     $final['response'] =  $brokers_data;
    //                     $final['note'] = 'Erro em $decodedToken["status"]';
    //                     $this->response($final);

    //                 } else {

    //                     $final['status'] = false;
    //                     $final['message'] = 'Nenhuma propriedade encontrada';
    //                     $final['note'] = 'Erro em $decodedToken["status"]';
    //                     $this->response($final);
    //                 }
    //             } else {

    //                 $final['status'] = false;
    //                 $final['message'] = 'Sua sessão expiroux.';
    //                 $final['note'] = 'Erro em $decodedToken["status"]';
    //                 $this->response($decodedToken);
    //             }
    //         } else {

    //             $final['status'] = false;
    //             $final['message'] = 'Falha na autenticaçãoy.';
    //             $final['note'] = 'Erro em validateToken()';

    //             $this->response($final, REST_Controller::HTTP_OK);
    //         }
    //     }
    // }

    public function get_broker_by_range_post()
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
                    $markers_data = str_replace('"', '', $this->input->post('markers_data'));
                    $markers_data = str_replace(']', '', $markers_data);
                    $markers_data = str_replace('[', '', $markers_data);
                    $markers_data = explode(",", $markers_data);

                    $brokers_data = array();

                    if (count($markers_data) > 0) {
                        $user_id = $this->input->post('user_id');
                        $user_preferences = $this->user_model->get_user_preferences($user_id);

                        foreach ($markers_data as $p) {
                            $broker_id = $this->property_model->get_broker_by_location_id($p);
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

                                    if (!$id_exists) {
                                        // Obter preferências do corretor
                                        $broker_preferences = $this->user_model->get_user_preferences($broker_id);

                                        // Calcular a porcentagem de correspondência
                                        $match_percentage = $this->calculate_match_percentage($user_preferences, $broker_preferences);
                                        $broker_data->match_percentage = $match_percentage;
                                        $broker_data->recommended = false; // Definir como false inicialmente

                                        $brokers_data[] = $broker_data;
                                    }
                                }
                            }
                        }

                        // Ordenar corretores pela porcentagem de correspondência em ordem decrescente
                        usort($brokers_data, function ($a, $b) {
                            return $b->match_percentage - $a->match_percentage;
                        });

                        // Definir os três melhores corretores como recomendados
                        for ($i = 0; $i < min(3, count($brokers_data)); $i++) {
                            $brokers_data[$i]->recommended = true;
                        }

                        $final['status'] = true;
                        $final['message'] = 'Propriedades encontradas';
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

    // public function get_broker_by_range_filter_post()
    // {

    //     $this->form_validation->set_rules('user_id', 'User ID', 'trim|required');

    //     if ($this->form_validation->run() == false) {

    //         $final['status'] = false;
    //         $final['message'] = validation_errors();
    //         $final['note'] = 'Erro no formulário.';

    //         $this->response($final, REST_Controller::HTTP_OK);
    //     } else {
    //         $headers = $this->input->request_headers();

    //         if (isset($headers['Authorization'])) {
    //             $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);

    //             if ($decodedToken['status']) {
    //                 $markers_data = str_replace('"', '', $this->input->post('markers_data'));
    //                 $markers_data = str_replace(']', '', $markers_data);
    //                 $markers_data = str_replace('[', '', $markers_data);
    //                 $markers_data = explode(",", $markers_data);

    //                 $f_data['filter_avaliation'] =  htmlspecialchars($this->input->post('filter_avaliation'));
    //                 $f_data['property_type'] =  htmlspecialchars($this->input->post('filter_type'));
    //                 $f_data['property_type_offer'] =  htmlspecialchars($this->input->post('filter_type_offer'));
    //                 $f_data['filter_price_min'] =  htmlspecialchars($this->input->post('filter_price_min'));
    //                 $f_data['filter_price_max'] =  htmlspecialchars($this->input->post('filter_price_max'));

    //                 $brokers_data = array();

    //                 if (count($markers_data) > 0) {

    //                     $user_id = $this->input->post('user_id');
    //                     $user_preferences = $this->user_model->get_user_preferences($user_id);

    //                     foreach ($markers_data as $p) {

    //                         $broker_id = $this->property_model->get_broker_by_location_id($p);
    //                         $broker_data = $this->property_model->get_broker($broker_id);

    //                         if ($broker_id) {

    //                             if ($broker_data) {

    //                                 $id_exists = false;
    //                                 foreach ($brokers_data as $existing_broker) {
    //                                     if ($existing_broker->id == $broker_data->id) {
    //                                         $id_exists = true;
    //                                         break;
    //                                     }
    //                                 }


    //                                 if (!$id_exists) {
    //                                     // Obter preferências do corretor
    //                                     $broker_preferences = $this->user_model->get_user_preferences($broker_id);

    //                                     // Calcular a porcentagem de correspondência
    //                                     $match_percentage = $this->calculate_match_percentage($user_preferences, $broker_preferences);
    //                                     $broker_data->match_percentage = $match_percentage;
    //                                     $broker_data->recommended = false; // Definir como false inicialmente

    //                                     $broker_proprietys = array();
    //                                     $broker_proprietys_location = $this->property_model->get_property_by_associate_broker_id($p, $broker_id);

    //                                     foreach ($broker_proprietys_location as $p )
    //                                     {
    //                                         $property_data = $this->property_model->get_property($p->property_id);
    //                                         $broker_proprietys[] =  $property_data;
    //                                     } 

    //                                     if ($this->filter_broker_proprietys($broker_proprietys, $f_data)) {

    //                                         $brokers_data[] = $broker_data;
    //                                     }
    //                                 }
    //                             }
    //                         }
    //                     }

    //                     // Ordenar corretores pela porcentagem de correspondência em ordem decrescente

    //                     if (strlen($f_data['filter_avaliation']) > 0) {

    //                         usort($brokers_data, function ($a, $b) {
    //                             return $b->user_rating - $a->user_rating;
    //                         });
    //                     }


    //                     // Definir os três melhores corretores como recomendados
    //                     for ($i = 0; $i < min(3, count($brokers_data)); $i++) {

    //                         $brokers_data[$i]->recommended = true;
    //                     }

    //                     $final['status'] = true;
    //                     $final['message'] = 'Propriedades encontradas';
    //                     $final['response'] =  $brokers_data;
    //                     $final['note'] = 'Erro em $decodedToken["status"]';

    //                     $this->response($final);
    //                 } else {

    //                     $final['status'] = false;
    //                     $final['message'] = 'Nenhuma propriedade encontrada';
    //                     $final['note'] = 'Erro em $decodedToken["status"]';
    //                     $this->response($final);
    //                 }
    //             } else {

    //                 $final['status'] = false;
    //                 $final['message'] = 'Sua sessão expirou.';
    //                 $final['note'] = 'Erro em $decodedToken["status"]';
    //                 $this->response($decodedToken);
    //             }
    //         } else {
    //             $final['status'] = false;
    //             $final['message'] = 'Falha na autenticação.';
    //             $final['note'] = 'Erro em validateToken()';

    //             $this->response($final, REST_Controller::HTTP_OK);
    //         }
    //     }
    // }

    public function get_broker_by_range_filter_post()
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
                    $markers_data = str_replace(['"', '[', ']'], '', $this->input->post('markers_data'));
                    $markers_data = explode(",", $markers_data);

                    $f_data['filter_avaliation'] = htmlspecialchars($this->input->post('filter_avaliation'));
                    $f_data['property_type'] = htmlspecialchars($this->input->post('filter_type'));
                    $f_data['property_type_offer'] = htmlspecialchars($this->input->post('filter_type_offer'));
                    $f_data['filter_price_min'] = htmlspecialchars($this->input->post('filter_price_min'));
                    $f_data['filter_price_max'] = htmlspecialchars($this->input->post('filter_price_max'));

                    $brokers_data = [];

                    if (count($markers_data) > 0) {

                        $user_id = $this->input->post('user_id');
                        $user_preferences = $this->user_model->get_user_preferences($user_id);

                        foreach ($markers_data as $location_id) {

                            $broker_id = $this->property_model->get_broker_by_location_id($location_id);
                            $broker_data = $this->property_model->get_broker($broker_id);

                            if ($broker_id && $broker_data) {

                                $id_exists = false;

                                foreach ($brokers_data as $existing_broker) {
                                    if ($existing_broker->id == $broker_data->id) {
                                        $id_exists = true;
                                        break;
                                    }
                                }

                                if (!$id_exists) {
                                    // Obter preferências do corretor
                                    $broker_preferences = $this->user_model->get_user_preferences($broker_id);

                                    // Calcular a porcentagem de correspondência
                                    $match_percentage = $this->calculate_match_percentage($user_preferences, $broker_preferences);
                                    $broker_data->match_percentage = $match_percentage;
                                    $broker_data->recommended = false; // Definir como false inicialmente

                                    $broker_proprietys = [];


                                    $broker_proprietys_location = $this->property_model->get_property_by_associate_broker_id($location_id, $broker_id);

                                    // echo "==".$broker_proprietys_location."<br>\n";

                                    // foreach ($broker_proprietys_location as $property) {

                                    $property_data = $this->property_model->get_property($broker_proprietys_location);
                                    // $broker_proprietys[] = $property_data;
                                    // }

                                    // print_r($broker_proprietys_location);

                                    // foreach ($broker_proprietys as $b) {

                                    if ($this->filter_broker_proprietys($property_data, $f_data)) {
                                        $brokers_data[] = $broker_data;
                                    }
                                    // }
                                }
                            }
                        }

                        // Ordenar corretores pelo rating 1-5 
                        // if (strlen($f_data['filter_avaliation']) > 0) {

                        //     if ($f_data['filter_avaliation'] == "melhores") {

                        //         usort($brokers_data, function ($a, $b) {
                        //             return $b->user_rating - $a->user_rating;
                        //         });

                        //     } else if ($f_data['filter_avaliation'] == "piores") {


                        //         usort($brokers_data, function ($a, $b) {
                        //             return $a->user_rating - $b->user_rating;
                        //         });

                        //     } else if ($f_data['filter_avaliation'] == "aleatórios") {
                        //         //  pass
                        //     }
                        // }
                        if (strlen($f_data['filter_avaliation']) > 0) {
                            if ($f_data['filter_avaliation'] == "melhores") {
                                usort($brokers_data, function ($a, $b) {
                                    if ($a->user_rating == $b->user_rating) {
                                        return 0;
                                    }
                                    return ($a->user_rating < $b->user_rating) ? 1 : -1;
                                });
                            } else if ($f_data['filter_avaliation'] == "piores") {
                                usort($brokers_data, function ($a, $b) {
                                    if ($a->user_rating == $b->user_rating) {
                                        return 0;
                                    }
                                    return ($a->user_rating > $b->user_rating) ? 1 : -1;
                                });
                            } else if ($f_data['filter_avaliation'] == "aleatórios") {
                                // Não faz nada, mantém a ordem atual
                            }
                        }


                        // Definir os três melhores corretores como recomendados
                        for ($i = 0; $i < min(3, count($brokers_data)); $i++) {
                            $brokers_data[$i]->recommended = true;
                        }

                        $final['status'] = true;
                        $final['message'] = 'Propriedades encontradas';
                        $final['response'] = $brokers_data;
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

    private function calculate_match_percentage($user_preferences, $broker_preferences)
    {
        if (empty($user_preferences) || empty($broker_preferences)) {
            return 0;
        }

        $matches = 0;
        foreach ($user_preferences as $preference) {
            if (in_array($preference, $broker_preferences)) {
                $matches++;
            }
        }

        $total_preferences = count($user_preferences);
        return ($total_preferences > 0) ? ($matches / $total_preferences) * 100 : 0;
    }

    public function web_process_property_main_image($base_image) {

        $path = 'public/images/property/';
        $property_main_image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base_image));

        $file_name = uniqid() . '.jpg';

        $base_image = $path . $file_name;

        if (file_put_contents($base_image, $property_main_image)) {

            return $base_image;


        } else {

            return false;
        }

    }

    // private function filter_broker_proprietys($broker_proprietys, $f_data)
    // {
    //     foreach ($broker_proprietys as $b) {

    //         $passes_filter = true;

    //         // Verificar tipo de propriedade
    //         if (strlen($f_data['property_type']) > 0) {
    //             if ($b->property_type != $f_data['property_type']) {
    //                 $passes_filter = false;
    //             }
    //         }

    //         // Verificar tipo de oferta
    //         if (strlen($f_data['property_type_offer']) > 0) {
    //             if ($b->property_type_offer != $f_data['property_type_offer']) {
    //                 $passes_filter = false;
    //             }
    //         }

    //         // Verificar preço mínimo
    //         if (strlen($f_data['filter_price_min']) > 0) {
    //             if ($b->property_price <= $f_data['filter_price_min']) {
    //                 $passes_filter = false;
    //             }
    //         }

    //         // Verificar preço máximo
    //         if (strlen($f_data['filter_price_max']) > 0) {
    //             if ($b->property_price >= $f_data['filter_price_max']) {
    //                 $passes_filter = false;
    //             }
    //         }

    //         if ($passes_filter) {
    //             return true;
    //         }
    //     }

    //     return false;
    // }

    private function filter_broker_proprietys($bp, $f_data)
    {

        // foreach ($broker_proprietys as $b) {
        $passes_filter = true;

        // Verificar tipo de propriedade
        if (strlen($f_data['property_type']) > 0) {
            if ($bp->property_type != $f_data['property_type']) {
                $passes_filter = false;
            }
        }

        // Verificar tipo de oferta
        if (strlen($f_data['property_type_offer']) > 0) {
            if ($bp->property_type_offer  != $f_data['property_type_offer']) {
                $passes_filter = false;
            }
        }

        // Verificar preço mínimo
        if (strlen($f_data['filter_price_min']) > 0) {
            if ($bp->property_price  < $f_data['filter_price_min']) {
                $passes_filter = false;
            }
        }

        // Verificar preço máximo
        if (strlen($f_data['filter_price_max']) > 0) {
            if ($bp->property_price > $f_data['filter_price_max']) {
                $passes_filter = false;
            }
        }

        if ($passes_filter) {
            return true;
        }
        // }

        return false;
    }
}

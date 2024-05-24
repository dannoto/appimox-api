<?php
defined('BASEPATH') or exit('No direct script access allowed');

require(APPPATH . '/libraries/REST_Controller.php');

use Restserver\Libraries\REST_Controller;

class Chat extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Authorization_Token');
        $this->load->model('user_model');
        $this->load->model('schedule_model');
        $this->load->model('broker_model');
        $this->load->model('property_model');
        $this->load->model('chat_model');
    }

    public function add_chat_post()
    {

        $this->form_validation->set_rules('chat_user_client', 'ID do usuário', 'trim|required');
        $this->form_validation->set_rules('chat_user_broker', 'ID da Imóvel', 'trim|required');

        if ($this->form_validation->run() === false) {

            $final['status'] = false;
            $final['message'] = validation_errors();
            $final['note'] = 'Erro no formulário.';

            $this->response($final, REST_Controller::HTTP_OK);
        } else {

            $broker_data =   $this->user_model->get_user($this->input->post('chat_user_broker'));
            $client_data =   $this->user_model->get_user($this->input->post('chat_user_client'));

            if ($broker_data->user_type == "broker" && $client_data->user_type == "broker") {
                $chat_type = 2;
            } else {
                $chat_type = 1;
            }

            $chat_datax['chat_user_broker']  = $this->input->post('chat_user_broker');
            $chat_datax['chat_user_client']  = $this->input->post('chat_user_client');
            $chat_datax['chat_type']  = $chat_type;
            $chat_datax['chat_date'] = date('Y-m-d H:i:s');
            $chat_datax['is_deleted'] = 0;

            $check_chat = $this->chat_model->check_chat($chat_datax['chat_user_broker'], $chat_datax['chat_user_client']);

            if (!$check_chat) {

                $chat_id = $this->chat_model->add_chat($chat_datax);

                if ($chat_id) {


                    $response = array();

                    $response['chat_data'] = $this->chat_model->get_chat($chat_id);
                    $response['broker_data'] = $broker_data;
                    $response['client_data'] = $client_data;


                    $response['status'] = true;
                    $response['response'] = $response;
                    $response['message'] = 'Chat criado com sucesso.';
                    $response['note'] = 'Chat criado com sucesso.';

                    // user creation failed, this should never happen
                    $this->response($final, REST_Controller::HTTP_OK);
                } else {

                    $final['status'] = false;
                    $final['message'] = 'Erro ao adicionar chat.';
                    $final['note'] = 'Erro ao adicionar chat.';

                    // user creation failed, this should never happen
                    $this->response($final, REST_Controller::HTTP_OK);
                }
            } else {


                $response = array();

                $response['chat_data'] = $check_chat;
                $response['client_data'] = $client_data;
                $response['broker_data'] = $broker_data;


                $response['status'] = true;
                $response['response'] = $response;
                $response['message'] = 'Já existe o chat criado.';
                $response['note'] = 'Já existe o chat criado.';

                // user creation failed, this should never happen
                $this->response($response, REST_Controller::HTTP_OK);
            }
          
        }
    }




    public function add_chat_message_post()
    {

        $this->form_validation->set_rules('chat_id', 'ID do usuário', 'trim|required');
        $this->form_validation->set_rules('message_sender_id', 'ID da Imóvel', 'trim|required');
        $this->form_validation->set_rules('message_receiver_id', 'ID da Imóvel', 'trim|required');
        $this->form_validation->set_rules('message_content', 'ID da Imóvel', 'trim|required');
        // $this->form_validation->set_rules('message_date', 'ID da Imóvel', 'trim|required');
        // $this->form_validation->set_rules('is_deleted_sender_id', 'ID da Imóvel', 'trim|required');
        // $this->form_validation->set_rules('is_deleted_receiver_id', 'ID da Imóvel', 'trim|required');
        // $this->form_validation->set_rules('message_sender_view', 'ID da Imóvel', 'trim|required');
        // $this->form_validation->set_rules('message_receiver_view', 'ID da Imóvel', 'trim|required');

        if ($this->form_validation->run() === false) {

            $final['status'] = false;
            $final['message'] = validation_errors();
            $final['note'] = 'Erro no formulário.';

            $this->response($final, REST_Controller::HTTP_OK);
        } else {

            $chat_data['chat_id']  = $this->input->post('chat_id');
            $chat_data['message_sender_id']  = $this->input->post('message_sender_id');
            $chat_data['message_receiver_id']  = $this->input->post('message_receiver_id');
            $chat_data['message_content']  = $this->input->post('message_content');
            $chat_data['message_date']  = date('Y-m-d H:i:s');
            $chat_data['is_deleted_sender_id']  = 0;
            $chat_data['is_deleted_receiver_id']  = 0;
            $chat_data['message_sender_view']  = 0;
            $chat_data['message_receiver_view']  = 0;
            $chat_data['is_deleted']  = 0;

            $add_chat_message = $this->chat_model->add_chat_message($chat_data);

            if ($add_chat_message) {

                $final['status'] = true;
                $final['message'] = 'Enviado com sucesso.';
                $final['note'] = 'Enviado com sucesso.';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            } else {

                $final['status'] = false;
                $final['message'] = 'Erro ao enviar.';
                $final['note'] = 'Erro ao enviar.';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            }
        }
    }

    public function get_broker_chat_post()
    {

        $this->form_validation->set_rules('broker_id', 'ID do usuário', 'trim|required');

        if ($this->form_validation->run() === false) {

            $final['status'] = false;
            $final['message'] = validation_errors();
            $final['note'] = 'Erro no formulário.';

            $this->response($final, REST_Controller::HTTP_OK);
        } else {

            $broker_id = $this->input->post('broker_id');
            $broker_chats = $this->chat_model->get_broker_chats($broker_id);

            if ($broker_chats) {


                $response = array();

               
                foreach ($broker_chats as $c) {

                    $format_response = array();

                    $format_response['chat_data'] = $c;
                    $format_response['client_data'] = $this->user_model->get_user($c->chat_user_client);
                    $format_response['chat_message_data'] = $this->chat_model->get_chat_message_preview($c->id);
                    $format_response['unread_count'] = $this->chat_model->unread_count($c->id, $c->chat_user_broker);

                    $response[] = $format_response;
                }

                $final['status'] = true;
                $final['response'] = $response;
                $final['message'] = 'Encontrado com sucesso.';
                $final['note'] = 'Encontrado com sucesso.';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            } else {

                $final['status'] = false;
                $final['message'] = 'Erro ao encontrar.';
                $final['note'] = 'Erro ao encontrar.';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            }
        }
    }

    public function search_broker_chat_post()
    {

        $this->form_validation->set_rules('broker_id', 'ID do usuário', 'trim|required');
        $this->form_validation->set_rules('query', 'ID do usuário', 'trim|required');

        if ($this->form_validation->run() === false) {

            $final['status'] = false;
            $final['message'] = validation_errors();
            $final['note'] = 'Erro no formulário.';

            $this->response($final, REST_Controller::HTTP_OK);
        } else {

            $broker_id = $this->input->post('broker_id');
            $query = $this->input->post('query');

            $broker_chats = $this->chat_model->search_broker_chats($broker_id, $query);

            if ($broker_chats) {


                $response = array();


                foreach ($broker_chats as $c) {

                    $format_response = array();

                    $format_response['chat_data'] = $c;
                    $format_response['client_data'] = $this->user_model->get_user($c->chat_user_client);
                    $format_response['chat_message_data'] = $this->chat_model->get_chat_message_preview($c->id);
                    $format_response['unread_count'] = $this->chat_model->unread_count($c->id, $c->chat_user_broker);

                    $response[] = $format_response;
                }

                $final['status'] = true;
                $final['response'] = $response;
                $final['message'] = 'Encontrado com sucesso.';
                $final['note'] = 'Encontrado com sucesso.';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            } else {

                $final['status'] = false;
                $final['message'] = 'Erro ao encontrar.';
                $final['note'] = 'Erro ao encontrar.';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            }
        }
    }

    public function get_client_chat_post()
    {

        $this->form_validation->set_rules('client_id', 'ID do usuário', 'trim|required');

        if ($this->form_validation->run() === false) {

            $final['status'] = false;
            $final['message'] = validation_errors();
            $final['note'] = 'Erro no formulário.';

            $this->response($final, REST_Controller::HTTP_OK);
        } else {

            $client_id = $this->input->post('client_id');
            $client_chats = $this->chat_model->get_client_chats($client_id);

            if ($client_chats) {


                $response = array();

               
                foreach ($client_chats as $c) {


                    if ($this->user_model->get_user($c->chat_user_broker)) {

                        $format_response = array();

                        $format_response['chat_data'] = $c;
                        $format_response['broker_data'] = $this->user_model->get_user($c->chat_user_broker);
                        $format_response['chat_message_data'] = $this->chat_model->get_chat_message_preview($c->id);
                        $format_response['unread_count'] = $this->chat_model->unread_count($c->id, $c->chat_user_client);
    
                        $response[] = $format_response;
                    }
                  
                }

                $final['status'] = true;
                $final['response'] = $response;
                $final['message'] = 'Encontrado com sucesso.';
                $final['note'] = 'Encontrado com sucesso.';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            } else {

                $final['status'] = false;
                $final['message'] = 'Erro ao encontrar.';
                $final['note'] = 'Erro ao encontrar.';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            }
        }
    }

    public function search_client_chat_post()
    {

        $this->form_validation->set_rules('client_id', 'ID do usuário', 'trim|required');
        $this->form_validation->set_rules('query', 'ID do usuário', 'trim|required');

        if ($this->form_validation->run() === false) {

            $final['status'] = false;
            $final['message'] = validation_errors();
            $final['note'] = 'Erro no formulário.';

            $this->response($final, REST_Controller::HTTP_OK);
        } else {

            $client_id = $this->input->post('client_id');
            $query = $this->input->post('query');

            $client_chats = $this->chat_model->search_client_chats($client_id, $query);

            if ($client_chats) {


                $response = array();


                foreach ($client_chats as $c) {

                    $format_response = array();

                    $format_response['chat_data'] = $c;
                    $format_response['broker_data'] = $this->user_model->get_user($c->chat_user_broker);
                    $format_response['chat_message_data'] = $this->chat_model->get_chat_message_preview($c->id);
                    $format_response['unread_count'] = $this->chat_model->unread_count($c->id, $c->chat_user_client);

                    $response[] = $format_response;
                }

                $final['status'] = true;
                $final['response'] = $response;
                $final['message'] = 'Encontrado com sucesso.';
                $final['note'] = 'Encontrado com sucesso.';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            } else {

                $final['status'] = false;
                $final['message'] = 'Erro ao encontrar.';
                $final['note'] = 'Erro ao encontrar.';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            }
        }
    }



    public function get_chat_messages_post()
    {

        $this->form_validation->set_rules('chat_id', 'ID do usuário', 'trim|required');
        $this->form_validation->set_rules('user_id', 'ID do usuário', 'trim|required');


        if ($this->form_validation->run() === false) {

            $final['status'] = false;
            $final['message'] = validation_errors();
            $final['note'] = 'Erro no formulário.';

            $this->response($final, REST_Controller::HTTP_OK);

        } else {

            $chat_id  = $this->input->post('chat_id');
            $user_id  = $this->input->post('user_id');

             // update unread
            $this->chat_model->update_unread_count($chat_id, $user_id);

            $chat_message = $this->chat_model->get_chat_message($chat_id);

            if ($chat_message) {

                $final['status'] = true;
                $final['response'] = $chat_message;
                $final['message'] = 'Mensagens encontradas';
                $final['note'] = 'Mensagens encontradas';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            } else {

                $final['status'] = false;
                $final['message'] = 'Nenhuma mensagem encontrada.';
                $final['note'] = 'Nenhuma mensagem encontrada.';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            }
        }
    }
}

<?php

require APPPATH . '/libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class Partner extends REST_Controller
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
        $this->load->model('schedule_model');
        $this->load->model('partner_model');
    }

    public function add_partner_property_post()
    {

        $this->form_validation->set_rules('partner_property_owner', 'ID do Proprietário', 'trim|required');
        $this->form_validation->set_rules('partner_property_broker', 'ID do Broker', 'trim|required');
        $this->form_validation->set_rules('partner_property_id', 'ID do Imóvel', 'trim|required');


        if ($this->form_validation->run() === false) {

            $final['status'] = false;
            $final['message'] = validation_errors();
            $final['note'] = 'Erro no formulário.';

            $this->response($final, REST_Controller::HTTP_OK);
        } else {

            // 1 -> em aberto
            // 2 -> ativo
            // 3 -> finalizado  

            $data['partner_status'] = 1;
            $data['partner_publish'] = 0;
            $data['partner_type']    = 'property';
            $data['partner_property_owner'] = $this->input->post('partner_property_owner');
            $data['partner_property_broker']    = $this->input->post('partner_property_broker');
            $data['partner_date']    = date('Y-m-d H:i:s');
            $data['is_deleted']    = 0;


            // if ($this->partner_model->add_partner($data)) {

            //     $final['status'] = false;
            //     $final['message'] = 'Parceria criado com sucesso.';
            //     $final['note'] = 'Parceria criado com sucesso.';

            //     // user creation failed, this should never happen
            //     $this->response($final, REST_Controller::HTTP_OK);
            // }

            $partner_id = $this->partner_model->add_partner($data);

            if ($partner_id) {

                $property_data['partner_id'] = $partner_id;
                $property_data['partner_property_id'] = $this->input->post('partner_property_id');
                $property_data['is_deleted'] = 0;

                if ($this->partner_model->add_partner_property($property_data)) {

                    $final['status'] = true;
                    $final['response'] = $partner_id;
                    $final['message'] = 'Parceria criada com sucesso.';
                    $final['note'] = 'Parceria criada com sucesso.';

                    $this->response($final, REST_Controller::HTTP_OK);
                } else {

                    $final['status'] = false;
                    $final['message'] = 'Erro ao adicionar Imovel Associado';
                    $final['note'] = 'Erro ao adicionar Imovel Associado';

                    $this->response($final, REST_Controller::HTTP_OK);
                }
            } else {

                $final['status'] = false;
                $final['message'] = 'Erro ao adicionar Parceria';
                $final['note'] = 'Erro ao adicionar Parceria';

                $this->response($final, REST_Controller::HTTP_OK);
            }
        }
    }

    public function add_partner_portfolio_post()
    {

        $this->form_validation->set_rules('partner_property_owner', 'ID do Proprietário', 'trim|required');
        $this->form_validation->set_rules('partner_property_broker', 'ID do Broker', 'trim|required');


        if ($this->form_validation->run() === false) {

            $final['status'] = false;
            $final['message'] = validation_errors();
            $final['note'] = 'Erro no formulário.';

            $this->response($final, REST_Controller::HTTP_OK);
        } else {

            // 1 -> em aberto
            // 2 -> ativo
            // 3 -> finalizado  

            $data['partner_status'] = 1;
            $data['partner_publish'] = 0;
            $data['partner_type']    = 'portfolio';
            $data['partner_property_owner'] = $this->input->post('partner_property_owner');
            $data['partner_property_broker']    = $this->input->post('partner_property_broker');
            $data['partner_date']    = date('Y-m-d H:i:s');
            $data['is_deleted']    = 0;


            // if ($this->partner_model->add_partner($data)) {

            //     $final['status'] = false;
            //     $final['message'] = 'Parceria criado com sucesso.';
            //     $final['note'] = 'Parceria criado com sucesso.';

            //     // user creation failed, this should never happen
            //     $this->response($final, REST_Controller::HTTP_OK);
            // }

            $partner_id = $this->partner_model->add_partner($data);

            if ($partner_id) {

                $final['status'] = true;
                $final['response'] = $partner_id;
                $final['message'] = 'Parceria criada com sucesso.';
                $final['note'] = 'Parceria criada com sucesso.';

                $this->response($final, REST_Controller::HTTP_OK);
            } else {

                $final['status'] = false;
                $final['message'] = 'Erro ao adicionar Parceria';
                $final['note'] = 'Erro ao adicionar Parceria';

                $this->response($final, REST_Controller::HTTP_OK);
            }
        }
    }

    // atualiza status 
    public function update_partner_status_post()
    {

        $this->form_validation->set_rules('partner_id', 'ID da Parceria', 'trim|required');
        $this->form_validation->set_rules('partner_publish', 'status da Parceria', 'trim|required');


        if ($this->form_validation->run() === false) {

            $final['status'] = false;
            $final['message'] = validation_errors();
            $final['note'] = 'Erro no formulário.';

            $this->response($final, REST_Controller::HTTP_OK);
        } else {

            // 1 -> em aberto
            // 2 -> ativo
            // 3 -> finalizado  
            $partner_id = $this->input->post('partner_id');
            $data['partner_publish'] = $this->input->post('partner_publish');


            $partner_id = $this->partner_model->update_partner($partner_id, $data);

            if ($partner_id) {

                $final['status'] = true;
                $final['message'] = 'Parceria atualizada com sucesso.';
                $final['note'] = 'Parceria atualizada com sucesso.';

                $this->response($final, REST_Controller::HTTP_OK);
            } else {

                $final['status'] = false;
                $final['message'] = 'Erro ao atualizr Parceria';
                $final['note'] = 'Erro ao atualizr Parceria';

                $this->response($final, REST_Controller::HTTP_OK);
            }
        }
    }

    // Adiciona imovel associada para partner tipo portfolio
    public function add_partner_propertys_post()
    {
        $this->form_validation->set_rules('partner_id', 'ID da Parceria', 'trim|required');
        $this->form_validation->set_rules('partner_property_id', 'ID do Imóvel', 'trim|required');

        if ($this->form_validation->run() === false) {

            $final['status'] = false;
            $final['message'] = validation_errors();
            $final['note'] = 'Erro no formulário.';

            $this->response($final, REST_Controller::HTTP_OK);
        } else {

            // 1 -> em aberto
            // 2 -> ativo
            // 3 -> finalizado  

            $property_data['partner_id'] = $this->input->post('partner_id');
            $property_data['partner_property_id'] = $this->input->post('partner_property_id');
            $property_data['is_deleted'] = 0;

            if ($this->partner_model->add_partner_property($property_data)) {

                $final['status'] = true;
                $final['response'] = $property_data['partner_id'];
                $final['message'] = 'Imóvel criada com sucesso.';
                $final['note'] = 'Imóvel criada com sucesso.';

                $this->response($final, REST_Controller::HTTP_OK);
            } else {

                $final['status'] = false;
                $final['message'] = 'Erro ao adicionar Imovel Associado';
                $final['note'] = 'Erro ao adicionar Imovel Associado';

                $this->response($final, REST_Controller::HTTP_OK);
            }
        }
    }


    public function get_partner_post()
    {

        $this->form_validation->set_rules('partner_id', 'ID da Parceria', 'trim|required');

        if ($this->form_validation->run() === false) {

            $final['status'] = false;
            $final['message'] = validation_errors();
            $final['note'] = 'Erro no formulário.';

            $this->response($final, REST_Controller::HTTP_OK);
        } else {

            $partner_id = $this->input->post('partner_id');

            $partner_data = $this->partner_model->get_partner($partner_id);

            $propertys_data = array();

            foreach ($this->partner_model->get_partner_associated($partner_id) as $p) {

                $p_data = $this->partner_model->get_property($p->partner_property_id);
                $propertys_data[] = $p_data;
                
            }


            if ($partner_data) {

                $response = array();
                $response['partner_data'] = $partner_data;
                $response['owner_data'] = $this->user_model->get_user($partner_data->partner_property_owner);
                $response['offer_data'] = $this->user_model->get_user($partner_data->partner_property_broker);
                $response['property_data'] = $this->partner_model->get_partner_associated($partner_id);
                $response['partner_actions'] = $this->partner_model->get_partner_actions($partner_id);


                $final['status'] = true;
                $final['response'] = $response;
                $final['message'] = 'Parceria encontrada com sucesso.';
                $final['note'] = 'Parceria encontrada com sucesso.';

                $this->response($final, REST_Controller::HTTP_OK);
            } else {

                $final['status'] = false;
                $final['message'] = 'Erro ao adicionar Parceria';
                $final['note'] = 'Erro ao adicionar Parceria';

                $this->response($final, REST_Controller::HTTP_OK);
            }
        }
    }


    public function get_partners_post()
    {

        $this->form_validation->set_rules('user_id', 'ID do usuario', 'trim|required');

        if ($this->form_validation->run() === false) {

            $final['status'] = false;
            $final['message'] = validation_errors();
            $final['note'] = 'Erro no formulário.';

            $this->response($final, REST_Controller::HTTP_OK);
        } else {

            $user_id = $this->input->post('user_id');

            if ($partner_data) {


                $final['status'] = true;
                $final['response'] = $response;
                $final['message'] = 'Parceria encontrada com sucesso.';
                $final['note'] = 'Parceria encontrada com sucesso.';

                $this->response($final, REST_Controller::HTTP_OK);
            } else {

                $final['status'] = false;
                $final['message'] = 'Erro ao adicionar Parceria';
                $final['note'] = 'Erro ao adicionar Parceria';

                $this->response($final, REST_Controller::HTTP_OK);
            }
        }
    }


    // action

    public function add_partner_action_post()
    {

        $this->form_validation->set_rules('partner_id', 'partner_id', 'trim|required');
        $this->form_validation->set_rules('partner_offer', 'partner_offer', 'trim|required');
        $this->form_validation->set_rules('partner_receiver', 'partner_receiver', 'trim|required');
        $this->form_validation->set_rules('partner_porcentage', 'partner_porcentage', 'trim|required');
        $this->form_validation->set_rules('partner_duration', 'partner_duration', 'trim|required');
        $this->form_validation->set_rules('partnet_duration_type', 'partnet_duration_type', 'trim|required');
        $this->form_validation->set_rules('partner_status', 'partner_status', 'trim|required');
        // $this->form_validation->set_rules('is_deleted', 'is_deleted', 'trim|required');


        if ($this->form_validation->run() === false) {

            $final['status'] = false;
            $final['message'] = validation_errors();
            $final['note'] = 'Erro no formulário.';

            $this->response($final, REST_Controller::HTTP_OK);
        } else {

            $data['partner_id'] = $this->input->post('partner_id');
            $data['partner_offer'] = $this->input->post('partner_offer');
            $data['partner_receiver']  = $this->input->post('partner_receiver');
            $data['partner_porcentage'] = $this->input->post('partner_porcentage');
            $data['partner_duration']    = $this->input->post('partner_duration');
            $data['partnet_duration_type']    = $this->input->post('partnet_duration_type');
            $data['partner_created']   = date('Y-m-d H:i:s');
            $data['partner_status']    = $this->input->post('partner_status');
            $data['is_deleted']    = 0;

            $partner_data = $this->partner_model->add_partner_action($data);

            if ($partner_data) {

                $final['status'] = true;
                $final['response'] = $partner_data;
                $final['message'] = 'Ação criada com sucesso.';
                $final['note'] = 'Ação criada com sucesso.';

                $this->response($final, REST_Controller::HTTP_OK);
            } else {

                $final['status'] = false;
                $final['message'] = 'Erro ao adicionar ação';
                $final['note'] = 'Erro ao adicionar ação';

                $this->response($final, REST_Controller::HTTP_OK);
            }
        }
    }
}

<?php
defined('BASEPATH') or exit('No direct script access allowed');

require(APPPATH . '/libraries/REST_Controller.php');

use Restserver\Libraries\REST_Controller;

class Schedule extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Authorization_Token');
        $this->load->model('user_model');
        $this->load->model('schedule_model');
        $this->load->model('broker_model');
        $this->load->model('property_model');
    }

    public function add_schedule_post()
    {

        // set validation rules
        $this->form_validation->set_rules('client_id', 'ID do usuário', 'trim|required');
        $this->form_validation->set_rules('property_id', 'ID da Imóvel', 'trim|required');
        $this->form_validation->set_rules('broker_id', 'ID do Corretor', 'trim|required');
        $this->form_validation->set_rules('schedule_date', 'Dia do agendamento', 'trim|required');

        if ($this->form_validation->run() === false) {


            $final['status'] = false;
            $final['message'] = validation_errors();
            $final['note'] = 'Erro no formulário.';

            $this->response($final, REST_Controller::HTTP_OK);
        } else {

            // set variables from the form
            $client_id = $this->input->post('client_id');
            $property_id    = $this->input->post('property_id');
            $broker_id    = $this->input->post('broker_id');
            $schedule_date = $this->input->post('schedule_date');


            if ($client_id ==  $broker_id) {
                $final['status'] = false;
                $final['message'] = 'Você não pode agendar consigo mesmo.';
                $final['note'] = 'Você não pode agendar consigo mesmo.';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            }

            if (!$this->schedule_model->check_schedule($client_id, $broker_id, $property_id, $schedule_date)) {

                if ($this->schedule_model->check_schedule_duplicated($client_id, $broker_id, $property_id)) {

                    $final['status'] = false;
                    $final['message'] = 'Você já possui um agendamento "em aberto" neste imóvel com este corretor.';
                    $final['note'] = 'Você já possui um agendamento "em aberto" neste imóvel com este corretor.';

                    // user creation failed, this should never happen
                    $this->response($final, REST_Controller::HTTP_OK);
                }

                $data['schedule_client'] = $client_id;
                $data['schedule_broker'] = $broker_id;
                $data['schedule_property'] = $property_id;
                $data['schedule_created'] = date('Y-m-d H:i:s');
                $data['schedule_date'] = $schedule_date;
                $data['schedule_status'] = 0;
                $data['is_deleted'] = 0;


                if ($this->schedule_model->add_schedule($data)) {

                    $final['status'] = true;
                    $final['message'] = 'Agendado com sucesso.';
                    $final['note'] = 'Agendado com sucesso.';

                    // user creation failed, this should never happen
                    $this->response($final, REST_Controller::HTTP_OK);
                } else {

                    $final['status'] = false;
                    $final['message'] = 'Erro ao adicionar agendamento. Tente novamente.';
                    $final['note'] = 'Erro ao adicionar agendamento. Tente novamente.';

                    // user creation failed, this should never happen
                    $this->response($final, REST_Controller::HTTP_OK);
                }
            } else {

                $final['status'] = false;
                $final['message'] = 'Já existe agendamento nesta data/hora. Escolha outro.';
                $final['note'] = 'Já existe agendamento nesta data/hora. Escolha outro.';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            }
        }
    }


    public function get_broker_schedules_post()
    {

        // set validation rules
        $this->form_validation->set_rules('user_id', 'ID do usuário', 'trim|required');

        if ($this->form_validation->run() === false) {


            $final['status'] = false;
            $final['message'] = validation_errors();
            $final['note'] = 'Erro no formulário.';

            $this->response($final, REST_Controller::HTTP_OK);
        } else {

            // set variables from the form
            $user_id = $this->input->post('user_id');

            $schedules_data = $this->schedule_model->get_broker_schedules($user_id);

            if ($schedules_data) {


                $response = array();

                foreach ($schedules_data as $sc) {

                    $dx = array();

                    $client_data = $this->user_model->get_user($sc->schedule_client);

                    $broker_data = $this->user_model->get_user($sc->schedule_broker);
                    $poperty_data = $this->property_model->get_property($sc->schedule_property);
                    $schedules_data = $sc;

                    $dx['broker_data'] = $broker_data;
                    $dx['property_data'] = $poperty_data;
                    $dx['schedule_data'] = $schedules_data;
                    $dx['client_data'] = $client_data;


                    $response[] = $dx;
                }

                $final['status'] = true;
                $final['response'] = $response;
                $final['message'] = 'Agendamentos encontrados com sucesso';
                $final['note'] = 'Agendamentos encontrados com sucesso';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            } else {

                $final['status'] = false;
                $final['message'] = 'Nenhum agendamento encontrado.';
                $final['note'] = 'Nenhum agendamento encontrado.';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            }
        }
    }


    // Corretor atualiza a data do agendamento.
    public function update_schedule_broker_date_post()
    {

        $this->form_validation->set_rules('schedule_id', 'ID do Agendamento', 'trim|required');
        $this->form_validation->set_rules('client_id', 'ID do usuário', 'trim|required');
        $this->form_validation->set_rules('property_id', 'ID da Imóvel', 'trim|required');
        $this->form_validation->set_rules('broker_id', 'ID do Corretor', 'trim|required');
        $this->form_validation->set_rules('schedule_date', 'Dia do agendamento', 'trim|required');

        if ($this->form_validation->run() === false) {


            $final['status'] = false;
            $final['message'] = validation_errors();
            $final['note'] = 'Erro no formulário.';

            $this->response($final, REST_Controller::HTTP_OK);
        } else {

            // set variables from the form
            $schedule_id = $this->input->post('schedule_id');

            $client_id = $this->input->post('client_id');
            $property_id    = $this->input->post('property_id');
            $broker_id    = $this->input->post('broker_id');
            $schedule_date = $this->input->post('schedule_date');

            // formatando data para datetime
            $date_time = DateTime::createFromFormat('d-m-Y H:i:s', $schedule_date);
            $formatted_date_time = $date_time->format('Y-m-d H:i:s');
            $schedule_date =  $formatted_date_time;
            // formatando data para datetime

            // validando data futura
            $current_datetime = new DateTime();

            if ($formatted_date_time <= $current_datetime) {

                $final['status'] = false;
                $final['message'] = 'Escolha uma data futura.'.  $current_datetime;
                $final['note'] = 'Escolha uma data futura.';

                $this->response($final, REST_Controller::HTTP_OK);
            }

            // validando data futura

            if (!$this->schedule_model->check_schedule($client_id, $broker_id, $property_id, $schedule_date)) {


                $schedule_data['schedule_date'] = $schedule_date;


                if ($this->schedule_model->update_broker_schedule($schedule_id, $schedule_data)) {

                    // Registrando Action
                    $schedule_data_action['schedule_id'] = $schedule_id;
                    $schedule_data_action['schedule_action_id'] = 5;
                    $schedule_data_action['schedule_action_description'] = 'Horário do Agendamento Alterado pelo Corretor';
                    $schedule_data_action['schedule_action_date'] = date('Y-m-d H:i:s');
                    $schedule_data_action['is_deleted'] = 0;

                    $this->schedule_model->add_schedule_action($schedule_data_action);
                    // Registrando Action


                    $final['status'] = true;
                    $final['message'] = 'Atualizado com sucesso.';
                    $final['note'] = 'Atualizado com sucesso.';

                    // user creation failed, this should never happen
                    $this->response($final, REST_Controller::HTTP_OK);
                } else {

                    $final['status'] = false;
                    $final['message'] = 'Erro ao atualizar agendamento. Tente novamente.';
                    $final['note'] = 'Erro ao atualizar agendamento. Tente novamente.';

                    // user creation failed, this should never happen
                    $this->response($final, REST_Controller::HTTP_OK);
                }
            } else {

                $final['status'] = false;
                $final['message'] = 'Já existe agendamento nesta data/hora. Escolha outro.';
                $final['note'] = 'Já existe agendamento nesta data/hora. Escolha outro.';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            }
        }
    }
}

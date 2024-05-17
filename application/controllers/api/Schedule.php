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
        $this->form_validation->set_rules('schedule_time', 'Hora do Agendamento', 'trim|required');

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
            $schedule_time = $this->input->post('schedule_time');

            $schedule_date[0] = explode("T", $schedule_date);

            if ($client_id ==  $broker_id) {
                $final['status'] = false;
                $final['message'] = 'Você não pode agendar consigo mesmo.';
                $final['note'] = 'Você não pode agendar consigo mesmo.';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            }

            if (!$this->schedule_model->check_schedule($client_id, $broker_id, $property_id, $schedule_date, $schedule_time)) {


                $data['schedule_client'] = $client_id;
                $data['schedule_broker'] = $broker_id;
                $data['schedule_property'] = $property_id;
                $data['schedule_created'] = date('Y-m-d H:i:s');
                $data['schedule_date'] = $schedule_date;
                $data['schedule_time'] = $schedule_time;
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


                $response= array();

                foreach ($schedules_data as $sc) {

                    $dx = array();


                    $broker_data = $this->broker_model->get_user($sc->schedule_broker);
                    $poperty_data = $this->property_model->get_property($sc->schedule_property);
                    $schedules_data = $sc;

                    $dx[] = $broker_data;
                    $dx[] = $poperty_data;
                    $dx[] = $schedules_data;


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
}
